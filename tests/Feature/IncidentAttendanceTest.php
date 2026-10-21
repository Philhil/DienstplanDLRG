<?php

namespace Tests\Feature;

use App\Client_user;
use App\Incident_attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesIncidentFixtures;
use Tests\TestCase;

class IncidentAttendanceTest extends TestCase
{
    use RefreshDatabase, CreatesIncidentFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    private function report($user, $incident, $status, array $extra = [])
    {
        return $this->actingAs($user)->post('/incident/' . $incident->id . '/report', ['status' => $status] + $extra);
    }

    // ── Self report ──────────────────────────────────────────────────────────

    public function test_member_reports_on_the_way()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);

        $this->report($member, $incident, 'on_the_way')->assertRedirect();

        $attendance = Incident_attendance::first();
        $this->assertSame($member->id, $attendance->user_id);
        $this->assertSame('on_the_way', $attendance->status);
        $this->assertSame(Incident_attendance::SOURCE_SELF, $attendance->source);
        $this->assertNull($attendance->confirmed_at);
        $this->assertNull($attendance->station_id);
    }

    public function test_report_preselects_home_station_and_ignores_stations_of_other_clients()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $station = $this->makeStation($client);
        $foreignStation = $this->makeStation($other, 'Fremde Wache');
        $member = $this->makeMember($client);
        $second = $this->makeMember($client);
        Client_user::where(['client_id' => $client->id, 'user_id' => $member->id])->update(['home_station_id' => $station->id]);
        $incident = $this->makeIncident($client);

        $this->report($member, $incident, 'present');
        $this->assertSame($station->id, Incident_attendance::where('user_id', $member->id)->first()->station_id);

        $this->report($second, $incident, 'present', ['station_id' => $foreignStation->id]);
        $this->assertNull(Incident_attendance::where('user_id', $second->id)->first()->station_id);
    }

    public function test_station_is_ignored_when_client_has_no_stations()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $foreignStation = $this->makeStation($other, 'Fremde Wache');
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);

        $this->report($member, $incident, 'present', ['station_id' => $foreignStation->id]);

        $this->assertNull(Incident_attendance::first()->station_id);
    }

    public function test_second_report_changes_status_instead_of_adding_a_row()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);

        $this->report($member, $incident, 'on_the_way');
        $this->actingAs($admin)->post('/incident/' . $incident->id . '/attendance/resolve', ['action' => 'confirm']);
        $this->assertNotNull(Incident_attendance::first()->confirmed_at);

        $this->report($member, $incident, 'present');

        $this->assertSame(1, Incident_attendance::count());
        $attendance = Incident_attendance::first();
        $this->assertSame('present', $attendance->status);
        // a changed self report has to be confirmed again
        $this->assertNull($attendance->confirmed_at);
    }

    public function test_report_with_invalid_status_is_rejected()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);

        $this->report($member, $incident, 'checked_out')->assertSessionHasErrors('status');
        $this->assertSame(0, Incident_attendance::count());
    }

    public function test_member_withdraws_own_report()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $this->report($member, $incident, 'present');

        $this->actingAs($member)->delete('/incident/' . $incident->id . '/report')->assertRedirect();

        $this->assertSame(0, Incident_attendance::count());
    }

    public function test_member_cannot_withdraw_entry_recorded_by_someone_else()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $this->makeAttendance($incident, $member, ['source' => 'recorded', 'recorded_by' => $editor->id]);

        $this->actingAs($member)->delete('/incident/' . $incident->id . '/report')->assertRedirect();

        $this->assertSame(1, Incident_attendance::count());
    }

    public function test_finalized_incident_rejects_reports_and_withdrawals()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $late = $this->makeMember($client);
        $incident = $this->makeFinalizedIncident($client);
        $this->makeAttendance($incident, $member);

        $this->report($late, $incident, 'present')->assertRedirect()->assertSessionHas('errormessage');
        $this->actingAs($member)->delete('/incident/' . $incident->id . '/report')->assertSessionHas('errormessage');

        $this->assertSame(1, Incident_attendance::count());
        $this->assertSame($member->id, Incident_attendance::first()->user_id);
    }

    public function test_unapproved_and_foreign_members_cannot_report()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $unapproved = $this->makeMember($client, ['approved' => false]);
        $stranger = $this->makeMember($other);
        $incident = $this->makeIncident($client);

        $this->report($unapproved, $incident, 'present')->assertStatus(402);
        $this->report($stranger, $incident, 'present')->assertStatus(402);
        $this->assertSame(0, Incident_attendance::count());
    }

    // ── Recording others ─────────────────────────────────────────────────────

    public function test_editor_records_member_and_guest_as_confirmed()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $member = $this->makeMember($client);
        $station = $this->makeStation($client);
        $incident = $this->makeIncident($client);

        $this->actingAs($editor)->post('/incident/' . $incident->id . '/attendance', [
            'user_id' => $member->id, 'status' => 'present', 'station_id' => $station->id,
        ])->assertRedirect();
        $this->actingAs($editor)->post('/incident/' . $incident->id . '/attendance', [
            'guest_name' => '  Gast Mustermann ', 'status' => 'present',
        ])->assertRedirect();

        $memberRow = Incident_attendance::where('user_id', $member->id)->first();
        $this->assertSame(Incident_attendance::SOURCE_RECORDED, $memberRow->source);
        $this->assertSame($editor->id, $memberRow->recorded_by);
        $this->assertNotNull($memberRow->confirmed_at);
        $this->assertSame($station->id, $memberRow->station_id);

        $guestRow = Incident_attendance::whereNull('user_id')->first();
        $this->assertSame('Gast Mustermann', $guestRow->guest_name);
        $this->assertNotNull($guestRow->confirmed_at);
    }

    public function test_incident_leader_can_record_without_editor_role()
    {
        $client = $this->makeClient();
        $leader = $this->makeMember($client);
        $incident = $this->makeIncident($client, ['leader_user_id' => $leader->id]);

        $this->actingAs($leader)->post('/incident/' . $incident->id . '/attendance', [
            'guest_name' => 'Gast', 'status' => 'present',
        ])->assertRedirect();

        $this->assertSame(1, Incident_attendance::count());
    }

    public function test_recording_needs_exactly_one_of_member_and_guest()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);

        $this->actingAs($editor)->post('/incident/' . $incident->id . '/attendance', ['status' => 'present'])
            ->assertSessionHasErrors('user_id');
        $this->actingAs($editor)->post('/incident/' . $incident->id . '/attendance', [
            'user_id' => $member->id, 'guest_name' => 'Gast', 'status' => 'present',
        ])->assertSessionHasErrors('user_id');

        $this->assertSame(0, Incident_attendance::count());
    }

    public function test_recording_rejects_duplicates_and_foreign_members()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $member = $this->makeMember($client);
        $stranger = $this->makeMember($other);
        $incident = $this->makeIncident($client);
        $this->makeAttendance($incident, $member);

        $this->actingAs($editor)->post('/incident/' . $incident->id . '/attendance', ['user_id' => $member->id, 'status' => 'present'])
            ->assertSessionHas('errormessage');
        $this->actingAs($editor)->post('/incident/' . $incident->id . '/attendance', ['user_id' => $stranger->id, 'status' => 'present'])
            ->assertSessionHas('errormessage');

        $this->assertSame(1, Incident_attendance::count());
    }

    public function test_normal_member_cannot_record_others()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);

        $this->actingAs($member)->post('/incident/' . $incident->id . '/attendance', ['guest_name' => 'Gast', 'status' => 'present'])
            ->assertStatus(402);
        $this->assertSame(0, Incident_attendance::count());
    }

    public function test_leader_qualification_allows_recording_but_not_foreign_entries()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $leader = $this->makeLeaderQualified($client);
        $helper = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $foreignEntry = $this->makeAttendance($incident, $helper, ['source' => 'recorded', 'recorded_by' => $editor->id]);

        $this->actingAs($leader)->post('/incident/' . $incident->id . '/attendance', ['guest_name' => 'Von der Fuehrungskraft', 'status' => 'present'])
            ->assertRedirect();
        $own = Incident_attendance::where('guest_name', 'Von der Fuehrungskraft')->first();
        $this->assertSame($leader->id, $own->recorded_by);

        // own recorded entry: change and remove
        $this->actingAs($leader)->put('/incident/' . $incident->id . '/attendance/' . $own->id, ['status' => 'on_the_way'])->assertRedirect();
        $this->assertSame('on_the_way', $own->fresh()->status);

        // foreign entry: neither change nor remove
        $this->actingAs($leader)->put('/incident/' . $incident->id . '/attendance/' . $foreignEntry->id, ['status' => 'on_the_way'])
            ->assertStatus(402);
        $this->actingAs($leader)->delete('/incident/' . $incident->id . '/attendance/' . $foreignEntry->id)->assertStatus(402);
        $this->assertSame('present', $foreignEntry->fresh()->status);

        $this->actingAs($leader)->delete('/incident/' . $incident->id . '/attendance/' . $own->id)->assertRedirect();
        $this->assertNull(Incident_attendance::find($own->id));
        $this->assertNotNull(Incident_attendance::find($foreignEntry->id));
    }

    public function test_qualification_of_another_client_does_not_count()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $incident = $this->makeIncident($client);
        // qualified in the other client, plain member in this one
        $user = $this->makeLeaderQualified($other);
        Client_user::create(['client_id' => $client->id, 'user_id' => $user->id]);
        Client_user::where(['client_id' => $client->id, 'user_id' => $user->id])->update(['approved' => true]);

        $this->actingAs($user)->post('/incident/' . $incident->id . '/attendance', ['guest_name' => 'Gast', 'status' => 'present'])
            ->assertStatus(402);
    }

    public function test_manager_changes_and_removes_any_entry()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $helper = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $attendance = $this->makeAttendance($incident, $helper, ['status' => 'on_the_way']);

        $this->actingAs($editor)->put('/incident/' . $incident->id . '/attendance/' . $attendance->id, ['status' => 'present'])->assertRedirect();
        $this->assertSame('present', $attendance->fresh()->status);

        $this->actingAs($editor)->delete('/incident/' . $incident->id . '/attendance/' . $attendance->id)->assertRedirect();
        $this->assertSame(0, Incident_attendance::count());
    }

    public function test_attendance_of_another_incident_cannot_be_changed_through_this_incident()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $helper = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $otherIncident = $this->makeIncident($client, ['title' => 'Anderer']);
        $attendance = $this->makeAttendance($otherIncident, $helper);

        $this->actingAs($editor)->delete('/incident/' . $incident->id . '/attendance/' . $attendance->id)->assertStatus(404);
        $this->assertSame(1, Incident_attendance::count());
    }

    public function test_changes_are_locked_after_finalizing()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $helper = $this->makeMember($client);
        $incident = $this->makeFinalizedIncident($client);
        $attendance = $this->makeAttendance($incident, $helper);

        $this->actingAs($editor)->post('/incident/' . $incident->id . '/attendance', ['guest_name' => 'Spaet', 'status' => 'present'])
            ->assertSessionHas('errormessage');
        $this->actingAs($editor)->put('/incident/' . $incident->id . '/attendance/' . $attendance->id, ['status' => 'on_the_way'])
            ->assertSessionHas('errormessage');
        $this->actingAs($editor)->delete('/incident/' . $incident->id . '/attendance/' . $attendance->id)
            ->assertSessionHas('errormessage');

        $this->assertSame(1, Incident_attendance::count());
        $this->assertSame('present', $attendance->fresh()->status);
    }

    public function test_resolve_is_only_for_managers()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $this->makeAttendance($incident, $member, ['confirmed_at' => null]);

        $this->actingAs($member)->post('/incident/' . $incident->id . '/attendance/resolve', ['action' => 'confirm'])->assertStatus(402);
        $this->assertNull(Incident_attendance::first()->confirmed_at);
    }

    // ── Stations ─────────────────────────────────────────────────────────────

    public function test_station_management_is_admin_only_and_scoped_to_client()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $member = $this->makeMember($client);
        $foreign = $this->makeStation($other, 'Fremde Wache');

        $this->actingAs($member)->post('/station', ['name' => 'Wache Nord'])->assertStatus(402);
        $this->actingAs($admin)->post('/station', ['name' => 'Wache Nord'])->assertRedirect();
        $this->assertSame(1, $client->Stations()->count());

        $this->actingAs($admin)->put('/station/' . $foreign->id, ['name' => 'Uebernommen'])->assertStatus(404);
        $this->actingAs($admin)->delete('/station/' . $foreign->id)->assertStatus(404);
        $this->assertSame('Fremde Wache', $foreign->fresh()->name);
    }

    public function test_member_sets_home_station_only_from_own_client()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $member = $this->makeMember($client);
        $station = $this->makeStation($client);
        $foreign = $this->makeStation($other, 'Fremde Wache');

        $this->actingAs($member)->post('/station/home', ['station_id' => $station->id])->assertRedirect();
        $this->assertSame($station->id, $member->homeStationId($client->id));

        $this->actingAs($member)->post('/station/home', ['station_id' => $foreign->id])->assertRedirect();
        $this->assertNull($member->homeStationId($client->id));
    }
}
