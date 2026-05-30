<?php

namespace Tests\Feature;

use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ServiceLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        $this->artisan('demo:createDemoClient');
    }

    // ── Test 9: finalize past service sets finalized_at ──────────────────────

    public function test_finalize_past_service_sets_finalized_at()
    {
        $admin       = User::where('name', 'Admin')->first();
        $servicePast = Service::whereDate('date', Carbon::yesterday()->toDateString())->first();

        $this->actingAs($admin)
            ->get('/service/finalize/'.$servicePast->id)
            ->assertRedirect();

        $this->assertNotNull(Service::find($servicePast->id)->finalized_at);
    }

    // ── Test 10: finalize future service does not set finalized_at ───────────

    public function test_finalize_future_service_does_not_set_finalized_at()
    {
        $admin         = User::where('name', 'Admin')->first();
        $serviceFuture = Service::whereDate('date', Carbon::tomorrow()->toDateString())->first();

        $this->actingAs($admin)
            ->get('/service/finalize/'.$serviceFuture->id)
            ->assertRedirect()
            ->assertSessionHas('errormessage');

        $this->assertNull(Service::find($serviceFuture->id)->finalized_at);
    }
}
