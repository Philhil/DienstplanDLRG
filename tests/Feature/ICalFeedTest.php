<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ICalFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    public function test_ical_feed_returns_calendar_for_valid_token()
    {
        $this->artisan('demo:createDemoClient');

        $user = User::whereNotNull('ical_token')->first();

        $this->get('/ical/' . $user->ical_token)
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/calendar; charset=UTF-8');
    }

    public function test_ical_feed_returns_404_for_invalid_token()
    {
        $this->get('/ical/' . Str::uuid())
            ->assertStatus(404);
    }

    public function test_ical_feed_is_accessible_without_authentication()
    {
        $this->artisan('demo:createDemoClient');

        $user = User::whereNotNull('ical_token')->first();

        // Route is outside the auth middleware group — guests must be able to reach it
        $this->assertGuest();
        $this->get('/ical/' . $user->ical_token)
            ->assertStatus(200);
    }

    public function test_ical_feed_contains_vcalendar_structure()
    {
        $this->artisan('demo:createDemoClient');

        $user = User::whereNotNull('ical_token')->first();

        $response = $this->get('/ical/' . $user->ical_token);

        $response->assertStatus(200);
        $response->assertSee('BEGIN:VCALENDAR', false);
        $response->assertSee('END:VCALENDAR', false);
    }
}
