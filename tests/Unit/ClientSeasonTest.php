<?php

namespace Tests\Unit;

use App\Client;
use Carbon\Carbon;
use Tests\TestCase;

class ClientSeasonTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_season_from_is_current_year_when_start_date_already_passed()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 15));

        $client = new Client();
        $client->seasonStart = '2000-01-01'; // Jan 1 is always past by June

        $season = $client->Season();

        $this->assertEquals(2026, $season['from']->year);
        $this->assertEquals(1,    $season['from']->month);
        $this->assertEquals(1,    $season['from']->day);
        $this->assertEquals(2027, $season['to']->year);
        $this->assertEquals(1,    $season['to']->month);
    }

    public function test_season_from_is_previous_year_when_start_date_not_yet_reached()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 15));

        $client = new Client();
        $client->seasonStart = '2000-12-01'; // Dec 1 is still in the future in June

        $season = $client->Season();

        $this->assertEquals(2025, $season['from']->year);
        $this->assertEquals(12,   $season['from']->month);
        $this->assertEquals(1,    $season['from']->day);
        $this->assertEquals(2026, $season['to']->year);
        $this->assertEquals(12,   $season['to']->month);
    }
}
