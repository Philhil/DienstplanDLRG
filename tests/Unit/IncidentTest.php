<?php

namespace Tests\Unit;

use App\Incident;
use App\Incident_attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesIncidentFixtures;
use Tests\TestCase;

class IncidentTest extends TestCase
{
    use RefreshDatabase, CreatesIncidentFixtures;

    public function test_duration_is_start_to_end_and_null_without_end()
    {
        $client = $this->makeClient();
        $incident = $this->makeIncident($client, [
            'start' => Carbon::create(2026, 3, 10, 10, 0), 'end' => null,
        ]);

        $this->assertNull($incident->durationMinutes());
        $this->assertNull($incident->helperMinutes());

        $incident->end = Carbon::create(2026, 3, 10, 12, 45);
        $incident->save();
        $this->assertSame(165, $incident->durationMinutes());
    }

    public function test_helper_minutes_count_only_helpers_that_were_there()
    {
        $client = $this->makeClient();
        $incident = $this->makeIncident($client, [
            'start' => Carbon::create(2026, 3, 10, 10, 0), 'end' => Carbon::create(2026, 3, 10, 11, 0),
        ]);
        $this->makeAttendance($incident, $this->makeMember($client));
        $this->makeAttendance($incident, null, ['guest_name' => 'Gast']);
        $this->makeAttendance($incident, $this->makeMember($client), ['status' => Incident_attendance::STATUS_ON_THE_WAY]);

        $this->assertSame(2, $incident->presentCount());
        $this->assertSame(120, $incident->helperMinutes());
    }

    public function test_state_helpers()
    {
        $client = $this->makeClient();
        $running = $this->makeIncident($client);
        $ended = $this->makeIncident($client, ['end' => Carbon::now()]);
        $done = $this->makeFinalizedIncident($client);

        $this->assertTrue($running->isOpen());
        $this->assertTrue($running->isRunning());
        $this->assertTrue($ended->isOpen());
        $this->assertFalse($ended->isRunning());
        $this->assertTrue($done->isFinalized());
        $this->assertFalse($done->isOpen());
    }

    public function test_permissions_by_role()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $leader = $this->makeMember($client);
        $qualified = $this->makeLeaderQualified($client);
        $member = $this->makeMember($client);
        $unapproved = $this->makeMember($client, ['approved' => false]);
        $stranger = $this->makeMember($other);
        $incident = $this->makeIncident($client, ['leader_user_id' => $leader->id]);

        foreach ([$admin, $editor, $leader] as $manager) {
            $this->assertTrue($incident->canManage($manager));
            $this->assertTrue($incident->canRecord($manager));
        }
        $this->assertFalse($incident->canManage($qualified));
        $this->assertTrue($incident->canRecord($qualified));
        $this->assertFalse($incident->canManage($member));
        $this->assertFalse($incident->canRecord($member));

        $this->assertTrue(Incident::canCreate($admin, $client->id));
        $this->assertTrue(Incident::canCreate($editor, $client->id));
        $this->assertFalse(Incident::canCreate($leader, $client->id));
        $this->assertFalse(Incident::canCreate($editor, $other->id));

        $this->assertTrue($incident->canSelfReport($member));
        $this->assertFalse($incident->canSelfReport($unapproved));
        $this->assertFalse($incident->canSelfReport($stranger));
    }

    public function test_attendance_names_for_member_guest_and_deleted_user()
    {
        $client = $this->makeClient();
        $incident = $this->makeIncident($client);
        $member = $this->makeMember($client, [], ['first_name' => 'Anna', 'name' => 'Eins']);
        $memberRow = $this->makeAttendance($incident, $member);
        $guestRow = $this->makeAttendance($incident, null, ['guest_name' => 'Gast Mustermann']);

        $this->assertSame('Anna Eins', $memberRow->displayName());
        $this->assertSame('Gast Mustermann', $guestRow->displayName());
        $this->assertTrue($guestRow->isGuest());
        $this->assertFalse($memberRow->isGuest());
    }

    public function test_a_user_can_only_be_recorded_once_per_incident()
    {
        $client = $this->makeClient();
        $incident = $this->makeIncident($client);
        $member = $this->makeMember($client);
        $this->makeAttendance($incident, $member);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->makeAttendance($incident, $member);
    }
}
