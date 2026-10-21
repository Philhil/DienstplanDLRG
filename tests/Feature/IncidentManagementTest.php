<?php

namespace Tests\Feature;

use App\Incident;
use App\Incident_attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesIncidentFixtures;
use Tests\TestCase;

class IncidentManagementTest extends TestCase
{
    use RefreshDatabase, CreatesIncidentFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    private function payload(array $overrides = [])
    {
        return $overrides + [
            'type' => Incident::TYPE_INCIDENT,
            'title' => 'Person im Wasser',
            'start' => Carbon::now()->subHour()->format('d m Y H:i'),
        ];
    }

    public function test_module_disabled_returns_402()
    {
        $client = $this->makeClient('Ohne Modul', ['module_incident' => false]);
        $admin = $this->makeMember($client, ['isAdmin' => true]);

        $this->actingAs($admin)->get('/incident')->assertStatus(402);
    }

    public function test_pages_render_for_admin()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $member = $this->makeMember($client);
        $station = $this->makeStation($client);
        $open = $this->makeIncident($client, ['title' => 'Offener Einsatz', 'leader_user_id' => $member->id]);
        $this->makeAttendance($open, $member, ['station_id' => $station->id]);
        $this->makeAttendance($open, null, ['guest_name' => 'Gast Mustermann']);
        $done = $this->makeFinalizedIncident($client, ['title' => 'Alter Einsatz']);

        $this->actingAs($admin)->get('/incident')->assertOk()->assertSee('Offener Einsatz')->assertSee('Alter Einsatz');
        $this->actingAs($admin)->get('/incident/create')->assertOk();
        $this->actingAs($admin)->get('/incident/' . $open->id)->assertOk()->assertSee('Gast Mustermann');
        $this->actingAs($admin)->get('/incident/' . $open->id . '/edit')->assertOk();
        $this->actingAs($admin)->get('/incident/' . $done->id)->assertOk();
        $this->actingAs($admin)->get('/station')->assertOk()->assertSee('Wache Mitte');
    }

    public function test_pages_render_for_normal_member()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client, ['title' => 'Offener Einsatz']);

        $this->actingAs($member)->get('/incident')->assertOk();
        $this->actingAs($member)->get('/incident/' . $incident->id)->assertOk()->assertSee('Ich bin auf Anfahrt');
    }

    public function test_normal_member_cannot_create()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);

        $this->actingAs($member)->get('/incident/create')->assertStatus(402);
        $this->actingAs($member)->post('/incident', $this->payload())->assertStatus(403);
        $this->assertSame(0, Incident::count());
    }

    public function test_editor_creates_incident_for_own_client()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $leader = $this->makeMember($client);

        $this->actingAs($editor)->post('/incident', $this->payload([
            'leader_user_id' => $leader->id,
            'end' => Carbon::now()->format('d m Y H:i'),
        ]))->assertRedirect();

        $incident = Incident::first();
        $this->assertSame($client->id, $incident->client_id);
        $this->assertSame($editor->id, $incident->created_by);
        $this->assertSame($leader->id, $incident->leader_user_id);
        $this->assertNull($incident->finalized_at);
    }

    public function test_admin_creates_incident()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);

        $this->actingAs($admin)->post('/incident', $this->payload())->assertRedirect();
        $this->assertSame(1, Incident::count());
    }

    public function test_create_validates_end_and_leader()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $foreign = $this->makeMember($other);

        $this->actingAs($editor)->post('/incident', $this->payload([
            'end' => Carbon::now()->subDays(2)->format('d m Y H:i'),
        ]))->assertSessionHasErrors('end');

        $this->actingAs($editor)->post('/incident', $this->payload([
            'leader_user_id' => $foreign->id,
        ]))->assertSessionHasErrors('leader_user_id');

        $this->assertSame(0, Incident::count());
    }

    public function test_member_of_another_client_cannot_see_incident()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $stranger = $this->makeMember($other);
        $incident = $this->makeIncident($client);

        $this->actingAs($stranger)->get('/incident/' . $incident->id)->assertStatus(402);
    }

    public function test_unapproved_member_cannot_see_incident()
    {
        $client = $this->makeClient();
        $unapproved = $this->makeMember($client, ['approved' => false]);
        $incident = $this->makeIncident($client);

        $this->actingAs($unapproved)->get('/incident/' . $incident->id)->assertStatus(402);
    }

    public function test_admins_and_incident_editors_can_edit_incidents()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $incident = $this->makeIncident($client, ['title' => 'Original']);

        foreach ([$admin, $editor] as $allowed) {
            $this->actingAs($allowed)->get('/incident/' . $incident->id . '/edit')->assertOk();
            $this->actingAs($allowed)->put('/incident/' . $incident->id, $this->payload(['title' => 'Geaendert von ' . $allowed->id]))->assertRedirect();
            $this->assertSame('Geaendert von ' . $allowed->id, $incident->fresh()->title);
        }
    }

    public function test_leader_edits_only_own_incident_and_cannot_create()
    {
        $client = $this->makeClient();
        $leader = $this->makeMember($client);
        $own = $this->makeIncident($client, ['title' => 'Eigener', 'leader_user_id' => $leader->id]);
        $foreign = $this->makeIncident($client, ['title' => 'Fremder Einsatz']);

        $this->actingAs($leader)->get('/incident/create')->assertStatus(402);
        $this->actingAs($leader)->post('/incident', $this->payload())->assertStatus(403);
        $this->actingAs($leader)->get('/incident/' . $own->id . '/edit')->assertOk();
        $this->actingAs($leader)->get('/incident/' . $foreign->id . '/edit')->assertStatus(402);

        $this->actingAs($leader)->put('/incident/' . $own->id, $this->payload(['title' => 'Neuer Titel']))->assertRedirect();
        $this->assertSame('Neuer Titel', $own->fresh()->title);
        $this->actingAs($leader)->put('/incident/' . $foreign->id, $this->payload(['title' => 'Manipuliert']))->assertStatus(402);
        $this->assertSame('Fremder Einsatz', $foreign->fresh()->title);
    }

    public function test_members_without_editor_role_cannot_create_or_edit()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $statEditor = $this->makeMember($client, ['isStatisticEditor' => true]);
        $qualified = $this->makeLeaderQualified($client);
        $incident = $this->makeIncident($client, ['title' => 'Original']);

        foreach ([$member, $statEditor, $qualified] as $denied) {
            $this->actingAs($denied)->get('/incident/create')->assertStatus(402);
            $this->actingAs($denied)->post('/incident', $this->payload())->assertStatus(403);
            $this->actingAs($denied)->get('/incident/' . $incident->id . '/edit')->assertStatus(402);
            $this->actingAs($denied)->put('/incident/' . $incident->id, $this->payload(['title' => 'Manipuliert']))->assertStatus(402);
        }

        $this->assertSame(1, Incident::count());
        $this->assertSame('Original', $incident->fresh()->title);
    }

    public function test_incident_editor_of_another_client_cannot_create_or_edit_here()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $foreignEditor = $this->makeMember($other, ['isIncidentEditor' => true]);
        $incident = $this->makeIncident($client, ['title' => 'Original']);

        $this->actingAs($foreignEditor)->get('/incident/' . $incident->id . '/edit')->assertStatus(402);
        $this->actingAs($foreignEditor)->put('/incident/' . $incident->id, $this->payload(['title' => 'Manipuliert']))->assertStatus(402);
        $this->assertSame('Original', $incident->fresh()->title);

        // the editor role of the other client does not help in this client where the user is a plain member
        $foreignEditor->currentclient_id = $client->id;
        $foreignEditor->save();
        \App\Client_user::create(['client_id' => $client->id, 'user_id' => $foreignEditor->id]);
        \App\Client_user::where(['client_id' => $client->id, 'user_id' => $foreignEditor->id])->update(['approved' => true]);
        $this->actingAs($foreignEditor)->post('/incident', $this->payload())->assertStatus(403);
        $this->actingAs($foreignEditor)->get('/incident/' . $incident->id . '/edit')->assertStatus(402);
        $this->assertSame(1, Incident::count());
    }

    public function test_finalized_incident_cannot_be_edited()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $incident = $this->makeFinalizedIncident($client, ['title' => 'Abgeschlossen']);

        $this->actingAs($admin)->put('/incident/' . $incident->id, $this->payload(['title' => 'Geaendert']))
            ->assertRedirect()->assertSessionHas('errormessage');
        $this->assertSame('Abgeschlossen', $incident->fresh()->title);
    }

    public function test_finalize_requires_end()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $incident = $this->makeIncident($client);

        $this->actingAs($admin)->post('/incident/' . $incident->id . '/finalize')
            ->assertRedirect()->assertSessionHas('errormessage');
        $this->assertNull($incident->fresh()->finalized_at);
    }

    public function test_finalize_rejects_end_before_start()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $incident = $this->makeIncident($client);

        $this->actingAs($admin)->post('/incident/' . $incident->id . '/finalize', [
            'end' => Carbon::now()->subDays(1)->format('d m Y H:i'),
        ])->assertRedirect()->assertSessionHas('errormessage');
        $this->assertNull($incident->fresh()->finalized_at);
    }

    public function test_finalize_is_blocked_by_unconfirmed_and_on_the_way_until_resolved()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $helper = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $attendance = $this->makeAttendance($incident, $helper, [
            'status' => Incident_attendance::STATUS_ON_THE_WAY,
            'confirmed_at' => null,
        ]);
        $end = Carbon::now()->format('d m Y H:i');

        $this->actingAs($admin)->post('/incident/' . $incident->id . '/finalize', ['end' => $end])
            ->assertRedirect()->assertSessionHas('errormessage');
        $this->assertNull($incident->fresh()->finalized_at);

        $this->actingAs($admin)->post('/incident/' . $incident->id . '/attendance/resolve', ['action' => 'confirm']);
        $this->actingAs($admin)->post('/incident/' . $incident->id . '/finalize', ['end' => $end])
            ->assertRedirect()->assertSessionHas('errormessage');
        $this->assertNull($incident->fresh()->finalized_at);

        $this->actingAs($admin)->post('/incident/' . $incident->id . '/attendance/resolve', ['action' => 'present']);
        $this->assertSame(Incident_attendance::STATUS_PRESENT, $attendance->fresh()->status);

        $this->actingAs($admin)->post('/incident/' . $incident->id . '/finalize', ['end' => $end])
            ->assertRedirect()->assertSessionHas('successmessage');

        $incident = $incident->fresh();
        $this->assertNotNull($incident->finalized_at);
        $this->assertSame($admin->id, $incident->finalized_by);
        $this->assertNotNull($incident->end);
    }

    public function test_normal_member_cannot_finalize()
    {
        $client = $this->makeClient();
        $member = $this->makeMember($client);
        $incident = $this->makeIncident($client, ['end' => Carbon::now()]);

        $this->actingAs($member)->post('/incident/' . $incident->id . '/finalize')->assertStatus(402);
        $this->assertNull($incident->fresh()->finalized_at);
    }

    public function test_only_admin_can_reopen()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $incident = $this->makeFinalizedIncident($client);
        $incident->finalized_by = $admin->id;
        $incident->save();

        $this->actingAs($editor)->post('/incident/' . $incident->id . '/reopen')->assertStatus(402);
        $this->assertNotNull($incident->fresh()->finalized_at);

        $this->actingAs($admin)->post('/incident/' . $incident->id . '/reopen')->assertRedirect();
        $this->assertNull($incident->fresh()->finalized_at);
        $this->assertNull($incident->fresh()->finalized_by);
    }

    public function test_only_admin_can_delete_incident_and_attendance_list()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $helper = $this->makeMember($client);
        $incident = $this->makeIncident($client);
        $this->makeAttendance($incident, $helper);

        $this->actingAs($editor)->delete('/incident/' . $incident->id)->assertStatus(402);
        $this->assertSame(1, Incident::count());

        $this->actingAs($admin)->delete('/incident/' . $incident->id)->assertRedirect();
        $this->assertSame(0, Incident::count());
        $this->assertSame(0, Incident_attendance::count());
    }

    public function test_admin_cannot_delete_incident_of_another_client()
    {
        $client = $this->makeClient();
        $other = $this->makeClient('Andere');
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $incident = $this->makeIncident($other);

        $this->actingAs($admin)->delete('/incident/' . $incident->id)->assertStatus(402);
        $this->assertSame(1, Incident::count());
    }

    public function test_pdf_for_manager_only()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $member = $this->makeMember($client);
        $station = $this->makeStation($client);
        $incident = $this->makeFinalizedIncident($client);
        $this->makeAttendance($incident, $member, ['station_id' => $station->id]);
        $this->makeAttendance($incident, null, ['guest_name' => 'Gast Mustermann']);

        $this->actingAs($member)->get('/incident/' . $incident->id . '/pdf')->assertStatus(402);

        $response = $this->actingAs($admin)->get('/incident/' . $incident->id . '/pdf');
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_module_switch_only_for_super_admin()
    {
        $client = $this->makeClient('Modul', ['module_incident' => false]);
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $super = $this->makeMember($client, [], ['role' => 'admin']);

        $this->actingAs($admin)->post('/client/module', ['client_id' => $client->id, 'module' => 'module_incident', 'state' => 'true'])
            ->assertStatus(402);
        $this->assertEquals(0, $client->fresh()->module_incident);

        $this->actingAs($super)->post('/client/module', ['client_id' => $client->id, 'module' => 'module_incident', 'state' => 'true'])
            ->assertOk();
        $this->assertEquals(1, $client->fresh()->module_incident);
    }

    public function test_incident_editor_assignment_only_by_admin()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);
        $member = $this->makeMember($client);

        $this->actingAs($member)->post('/client_user/incidenteditor', ['client_id' => $client->id, 'user_id' => $member->id, 'isIncidentEditor' => 1])
            ->assertStatus(402);
        $this->assertFalse($member->isIncidentEditorOfClient($client->id));

        $this->actingAs($admin)->post('/client_user/incidenteditor', ['client_id' => $client->id, 'user_id' => $member->id, 'isIncidentEditor' => 1])
            ->assertOk();
        $this->assertTrue($member->isIncidentEditorOfClient($client->id));
    }

    public function test_client_edit_page_offers_incident_editor_assignment_and_module_switch()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);

        $this->actingAs($admin)->get('/client/' . $client->id . '/edit')->assertOk()
            ->assertSee('client_user_incidenteditorselect')->assertSee('module_incident');

        $client->module_incident = false;
        $client->save();
        $this->actingAs($admin)->get('/client/' . $client->id . '/edit')->assertOk()
            ->assertDontSee('client_user_incidenteditorselect');
    }

    public function test_qualification_form_marks_leader_qualifications_only_with_active_module()
    {
        $client = $this->makeClient();
        $admin = $this->makeMember($client, ['isAdmin' => true]);

        $this->actingAs($admin)->get('/qualification/create')->assertOk()->assertSee('can_record_incident');

        $this->actingAs($admin)->post('/qualification', [
            'name' => 'Gruppenführer', 'short' => 'GF', 'isservicedefault' => 0, 'defaultcount' => 0,
            'defaultrequiredasposition' => 0, 'can_record_incident' => 1,
        ])->assertRedirect();
        $this->assertEquals(1, \App\Qualification::where('short', 'GF')->first()->can_record_incident);

        $client->module_incident = false;
        $client->save();
        $this->actingAs($admin)->get('/qualification/create')->assertOk()->assertDontSee('can_record_incident');
    }

    public function test_menu_shows_incident_entries_by_role_and_module()
    {
        $client = $this->makeClient();
        $editor = $this->makeMember($client, ['isIncidentEditor' => true]);
        $member = $this->makeMember($client);

        $this->actingAs($editor)->get('/incident')->assertOk()->assertSee('Einsatz anlegen');
        $this->actingAs($member)->get('/incident')->assertOk()->assertSee('Einsätze')->assertDontSee('Einsatz anlegen');
    }
}
