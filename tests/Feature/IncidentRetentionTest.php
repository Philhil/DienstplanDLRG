<?php

namespace Tests\Feature;

use App\Incident;
use App\Incident_attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesIncidentFixtures;
use Tests\TestCase;

/**
 * Attendance lists are kept for queries and insurance. Deleting users, stations or clients must not
 * delete them, the ids stay set so the statistic stays complete. Only a manual delete removes them.
 */
class IncidentRetentionTest extends TestCase
{
    use RefreshDatabase, CreatesIncidentFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_deleting_a_user_keeps_the_attendance_with_the_user_id()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $helper = $this->makeMember($client, [], ['first_name' => 'Hanna', 'name' => 'Helfer']);
        $incident = $this->makeFinalizedIncident($client);
        $attendance = $this->makeAttendance($incident, $helper);
        $helperId = $helper->id;

        $helper->delete();

        $attendance = $attendance->fresh();
        $this->assertNotNull($attendance);
        $this->assertSame($helperId, $attendance->user_id);
        $this->assertSame('Gelöschter Benutzer (#' . $helperId . ')', $attendance->displayName());

        // still shown in the incident and counted in the statistic
        $this->actingAs($admin)->get('/incident/' . $incident->id)->assertOk()->assertSee('Gelöschter Benutzer (#' . $helperId . ')');
        $stats = app(\App\Http\Controllers\IncidentStatisticController::class)
            ->calculate($client->id, Carbon::now()->subYear(), Carbon::now()->addDay());
        $this->assertSame(1, $stats['presentTotal']);
        $this->assertSame(1, $stats['distinctUsers']);
    }

    public function test_deleting_the_leader_keeps_the_leader_id()
    {
        $client = $this->makeClient();
        $leader = $this->makeMember($client);
        $incident = $this->makeIncident($client, ['leader_user_id' => $leader->id, 'created_by' => $leader->id]);
        $leaderId = $leader->id;

        $leader->delete();

        $incident = $incident->fresh();
        $this->assertSame($leaderId, $incident->leader_user_id);
        $this->assertSame($leaderId, $incident->created_by);
    }

    public function test_deleting_a_station_keeps_the_station_id_in_the_attendance()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $helper = $this->makeMember($client);
        $station = $this->makeStation($client, 'Wache Alt');
        $incident = $this->makeFinalizedIncident($client);
        $attendance = $this->makeAttendance($incident, $helper, ['station_id' => $station->id]);
        $stationId = $station->id;

        $this->actingAs($admin)->delete('/station/' . $stationId)->assertRedirect();

        $this->assertNull(\App\Station::find($stationId));
        $attendance = $attendance->fresh();
        $this->assertSame($stationId, $attendance->station_id);
        $this->assertSame('Gelöschte Wache (#' . $stationId . ')', $attendance->stationName());

        $stats = app(\App\Http\Controllers\IncidentStatisticController::class)
            ->calculate($client->id, Carbon::now()->subYear(), Carbon::now()->addDay());
        $this->assertSame(['Gelöschte Wache (#' . $stationId . ')' => 1], $stats['byStation']);
    }

    public function test_deleting_the_client_does_not_delete_incidents_or_attendances()
    {
        $client = $this->makeClient();
        $incident = $this->makeFinalizedIncident($client);
        $this->makeAttendance($incident, null, ['guest_name' => 'Gast Mustermann']);
        $this->makeAttendance($incident, null, ['guest_name' => 'Zweiter Gast']);

        $client->delete();

        $this->assertSame(1, Incident::count());
        $this->assertSame(2, Incident_attendance::count());
    }

    public function test_attendances_are_only_removed_by_the_manual_incident_delete()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $helper = $this->makeMember($client);
        $incident = $this->makeFinalizedIncident($client);
        $this->makeAttendance($incident, $helper);

        $helper->delete();
        $this->assertSame(1, Incident_attendance::count());

        $this->actingAs($admin)->delete('/incident/' . $incident->id)->assertRedirect();
        $this->assertSame(0, Incident_attendance::count());
    }
}
