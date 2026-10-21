<?php

namespace Tests\Feature;

use App\Http\Controllers\IncidentStatisticController;
use App\Incident;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesIncidentFixtures;
use Tests\TestCase;

class IncidentStatisticTest extends TestCase
{
    use RefreshDatabase, CreatesIncidentFixtures;

    private $client;
    private $users;
    private $mitte;

    /**
     * Incident A (einsatz, Tue 10.03.2026 10:00-12:30, 150 min): u1 + u2 at "Mitte", one guest without station,
     * u3 still on the way (does not count).
     * Incident B (uebung, Thu 12.03.2026 20:00-21:00, 60 min): u1 at "Mitte".
     * Incident C: finalized but in April, incident D: in March but not finalized.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->makeClient();
        $this->mitte = $this->makeStation($this->client, 'Wache Mitte');
        $this->users = [
            $this->makeMember($this->client, [], ['first_name' => 'Anna', 'name' => 'Eins']),
            $this->makeMember($this->client, [], ['first_name' => 'Bernd', 'name' => 'Zwei']),
            $this->makeMember($this->client, [], ['first_name' => 'Cora', 'name' => 'Drei']),
        ];

        $a = $this->makeFinalizedIncident($this->client, [
            'type' => Incident::TYPE_INCIDENT, 'title' => 'Einsatz A',
            'start' => Carbon::create(2026, 3, 10, 10, 0), 'end' => Carbon::create(2026, 3, 10, 12, 30),
        ]);
        $this->makeAttendance($a, $this->users[0], ['station_id' => $this->mitte->id]);
        $this->makeAttendance($a, $this->users[1], ['station_id' => $this->mitte->id]);
        $this->makeAttendance($a, null, ['guest_name' => 'Gast']);
        $this->makeAttendance($a, $this->users[2], ['status' => 'on_the_way']);

        $b = $this->makeFinalizedIncident($this->client, [
            'type' => Incident::TYPE_EXERCISE, 'title' => 'Uebung B',
            'start' => Carbon::create(2026, 3, 12, 20, 0), 'end' => Carbon::create(2026, 3, 12, 21, 0),
        ]);
        $this->makeAttendance($b, $this->users[0], ['station_id' => $this->mitte->id]);

        $c = $this->makeFinalizedIncident($this->client, [
            'title' => 'Einsatz C', 'start' => Carbon::create(2026, 4, 5, 10, 0), 'end' => Carbon::create(2026, 4, 5, 11, 0),
        ]);
        $this->makeAttendance($c, $this->users[0]);

        $d = $this->makeIncident($this->client, [
            'title' => 'Einsatz D', 'start' => Carbon::create(2026, 3, 15, 10, 0), 'end' => Carbon::create(2026, 3, 15, 11, 0),
        ]);
        $this->makeAttendance($d, $this->users[0]);
    }

    private function stats($type = null, $stationId = null)
    {
        return app(IncidentStatisticController::class)->calculate(
            $this->client->id, Carbon::create(2026, 3, 1)->startOfDay(), Carbon::create(2026, 3, 31)->endOfDay(),
            $type, $stationId, $this->client->Stations()->get()
        );
    }

    public function test_counts_duration_and_helper_hours_from_start_and_end()
    {
        $stats = $this->stats();

        $this->assertSame(2, $stats['count']);
        $this->assertSame(['einsatz' => 1, 'uebung' => 1, 'sonstiges' => 0], $stats['byType']);
        $this->assertSame(210, $stats['totalMinutes']);
        $this->assertSame(105, $stats['avgMinutes']);
        // present: A = u1, u2, guest; B = u1 (u3 is only on the way)
        $this->assertSame(4, $stats['presentTotal']);
        $this->assertEquals(2.0, $stats['avgPresent']);
        $this->assertSame(2, $stats['distinctUsers']);
        $this->assertSame(1, $stats['guestTotal']);
        // the duration is the same for every helper: 150 x 3 + 60 x 1
        $this->assertSame(510, $stats['helperMinutes']);
    }

    public function test_only_finalized_incidents_in_range_are_counted()
    {
        $titles = $this->stats()['incidents']->pluck('title')->all();

        $this->assertSame(['Einsatz A', 'Uebung B'], $titles);
    }

    public function test_distribution_by_month_weekday_daytime_and_station()
    {
        $stats = $this->stats();

        $this->assertSame(['2026-03' => 2], $stats['byMonth']);
        $this->assertSame(1, $stats['byWeekday'][2]); // Tuesday
        $this->assertSame(1, $stats['byWeekday'][4]); // Thursday
        $this->assertSame(1, $stats['byDaytime'][IncidentStatisticController::DAYTIMES[1]]);
        $this->assertSame(1, $stats['byDaytime'][IncidentStatisticController::DAYTIMES[3]]);
        $this->assertSame(['Wache Mitte' => 3, 'Ohne Wache' => 1], $stats['byStation']);
    }

    public function test_top_helpers_only_list_members_with_account()
    {
        $top = array_values($this->stats()['topHelpers']);

        $this->assertCount(2, $top);
        $this->assertSame('Anna Eins', $top[0]['name']);
        $this->assertSame(2, $top[0]['incidents']);
        $this->assertSame(210, $top[0]['minutes']);
        $this->assertSame('Bernd Zwei', $top[1]['name']);
        $this->assertSame(150, $top[1]['minutes']);
    }

    public function test_type_filter()
    {
        $stats = $this->stats(Incident::TYPE_EXERCISE);

        $this->assertSame(1, $stats['count']);
        $this->assertSame(60, $stats['totalMinutes']);
        $this->assertSame(60, $stats['helperMinutes']);
    }

    public function test_station_filter_counts_only_attendances_of_that_station()
    {
        $stats = $this->stats(null, $this->mitte->id);

        $this->assertSame(2, $stats['count']);
        $this->assertSame(3, $stats['presentTotal']);
        $this->assertSame(0, $stats['guestTotal']);
        $this->assertSame(150 * 2 + 60, $stats['helperMinutes']);
    }

    public function test_page_access()
    {
        $admin = $this->makeMember($this->client, ['isAdmin' => true]);
        $statEditor = $this->makeMember($this->client, ['isStatisticEditor' => true]);
        $member = $this->makeMember($this->client);
        $url = '/incident/statistic?from=01.03.2026&to=31.03.2026';

        $this->actingAs($member)->get($url)->assertStatus(402);
        $this->actingAs($admin)->get($url)->assertOk()->assertSee('Einsatz A')->assertSee('Uebung B')->assertDontSee('Einsatz C');
        $this->actingAs($statEditor)->get($url)->assertOk();

        $this->client->module_statistic = false;
        $this->client->save();
        $this->actingAs($statEditor)->get($url)->assertStatus(402);
        $this->actingAs($admin)->get($url)->assertOk();

        $this->client->module_incident = false;
        $this->client->save();
        $this->actingAs($admin)->get($url)->assertStatus(402);
    }

    public function test_default_period_is_the_season_of_the_client()
    {
        $admin = $this->makeMember($this->client, ['isAdmin' => true]);

        $this->actingAs($admin)->get('/incident/statistic')->assertOk();
    }

    public function test_csv_exports()
    {
        $admin = $this->makeMember($this->client, ['isAdmin' => true]);
        $query = 'from=01.03.2026&to=31.03.2026&export=';

        $incidents = $this->actingAs($admin)->get('/incident/statistic?' . $query . 'incidents');
        $incidents->assertOk();
        $this->assertStringContainsString('text/csv', $incidents->headers->get('Content-Type'));
        $content = $incidents->streamedContent();
        $this->assertStringContainsString('Einsatz A', $content);
        $this->assertStringContainsString('10.03.2026 10:00', $content);
        $this->assertStringContainsString(';150;3;1', $content);
        $this->assertStringNotContainsString('Einsatz C', $content);

        $attendances = $this->actingAs($admin)->get('/incident/statistic?' . $query . 'attendances')->streamedContent();
        $this->assertStringContainsString('Anna Eins', $attendances);
        $this->assertStringContainsString('Gast', $attendances);
        $this->assertStringContainsString('Wache Mitte', $attendances);
    }
}
