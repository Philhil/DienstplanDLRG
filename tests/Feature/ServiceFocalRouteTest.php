<?php

namespace Tests\Feature;

use App\Position;
use App\Service;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ServiceFocalRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        $this->artisan('demo:createDemoClient');
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * The focal user (service_focal = true) can access the service information page.
     */
    public function test_FocalUserCanAccessServiceInformation()
    {
        $focalUser    = User::where('name', 'FocalUser')->first();
        $focalService = Position::where('service_focal', true)->firstOrFail()->service;

        $this->actingAs($focalUser)
            ->get('/service/' . $focalService->id . '/inform')
            ->assertStatus(200)
            ->assertViewIs('inform.create');
    }

    /**
     * A regular user (service_focal = false) cannot access the service information page.
     */
    public function test_RegularUserCannotAccessServiceInformation()
    {
        $user         = User::where('name', 'User')->first();
        $focalService = Position::where('service_focal', true)->firstOrFail()->service;

        $this->actingAs($user)
            ->get('/service/' . $focalService->id . '/inform')
            ->assertStatus(402);
    }

    /**
     * The focal user can see the phone number of other assigned users on the service index.
     */
    public function test_FocalUserCanSeePhoneNumbers()
    {
        $focalUser    = User::where('name', 'FocalUser')->first();
        $regularUser  = User::where('name', 'User')->first();
        $focalService = Position::where('service_focal', true)->firstOrFail()->service;

        $this->actingAs($focalUser)
            ->get('/service')
            ->assertStatus(200)
            ->assertSee($regularUser->mobilenumber);
    }

    /**
     * A regular user cannot see other users' phone numbers on the service index.
     */
    public function test_RegularUserCannotSeePhoneNumbers()
    {
        $user        = User::where('name', 'User')->first();
        $focalUser   = User::where('name', 'FocalUser')->first();

        $this->actingAs($user)
            ->get('/service')
            ->assertStatus(200)
            ->assertDontSee($focalUser->mobilenumber);
    }
}
