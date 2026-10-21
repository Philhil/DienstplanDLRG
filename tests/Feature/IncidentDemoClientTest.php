<?php

namespace Tests\Feature;

use App\Incident;
use App\Incident_attendance;
use App\Station;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class IncidentDemoClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    public function test_demo_client_has_incident_module_stations_and_incidents()
    {
        $this->artisan('demo:createDemoClient');

        $this->assertSame(2, Station::count());
        $this->assertSame(2, Incident::count());
        $this->assertSame(1, Incident::whereNotNull('finalized_at')->count());
        $this->assertSame(4, Incident_attendance::count());

        $admin = User::where('name', 'Admin')->first();
        $this->actingAs($admin)->get('/incident')->assertOk()->assertSee('Demo Spontanübung')->assertSee('Demo Einsatz: Person im Wasser');

        $focal = User::where('name', 'FocalUser')->first();
        $open = Incident::whereNull('finalized_at')->first();
        $this->assertTrue($open->canRecord($focal), 'the demo leader qualification may record attendance');
    }

    public function test_recreating_the_demo_client_does_not_leave_old_incidents_behind()
    {
        $this->artisan('demo:createDemoClient');

        // MySQL sets positions.user_id to null when a user is deleted (migration 2019_05_29), SQLite test databases
        // cannot alter that foreign key, so the same is done by hand to be able to regenerate the demo data there
        \App\Position::query()->update(['user_id' => null]);

        $this->artisan('demo:createDemoClient');

        $this->assertSame(2, Station::count());
        $this->assertSame(2, Incident::count());
        $this->assertSame(4, Incident_attendance::count());
    }
}
