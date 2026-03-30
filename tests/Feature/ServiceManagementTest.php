<?php

namespace Tests\Feature;

use App\Client;
use App\Client_user;
use App\Position;
use App\PositionCandidature;
use App\Qualification;
use App\Qualification_user;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;
    protected User $user;
    protected User $admin;
    protected Qualification $qualification;
    protected Service $service;
    protected Position $position;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        $this->artisan('demo:createDemoClient');

        $this->user  = User::where('name', 'User')->first();
        $this->admin = User::where('name', 'Admin')->first();
        $this->client = Client::first();
        $this->service = Service::first();
        $this->position = Position::first();
        $this->qualification = Qualification::first();
    }

    // ── Service CRUD (admin only) ────────────────────────────

    public function test_admin_can_view_services(): void
    {
        $this->actingAs($this->admin)
            ->get('/service')
            ->assertStatus(200)
            ->assertViewIs('service.index');
    }

    public function test_admin_can_access_create_service_form(): void
    {
        $this->actingAs($this->admin)
            ->get('/service/create')
            ->assertStatus(200)
            ->assertViewIs('service.create');
    }

    public function test_user_cannot_access_create_service_form(): void
    {
        $this->actingAs($this->user)
            ->get('/service/create')
            ->assertStatus(402);
    }

    public function test_admin_can_edit_service(): void
    {
        $this->actingAs($this->admin)
            ->get('/service/' . $this->service->id . '/edit')
            ->assertStatus(200);
    }

    public function test_user_cannot_edit_service(): void
    {
        $this->actingAs($this->user)
            ->get('/service/' . $this->service->id . '/edit')
            ->assertStatus(402);
    }

    public function test_user_cannot_delete_service(): void
    {
        Session::start();
        $this->actingAs($this->user)
            ->delete('/service/' . $this->service->id, ['_token' => session('_token')])
            ->assertStatus(402);
    }

    // ── Position subscribe/unsubscribe ───────────────────────

    public function test_user_can_subscribe_to_position(): void
    {
        Session::start();
        $response = $this->actingAs($this->user)
            ->post('/position/' . $this->position->id . '/subscribe', ['_token' => session('_token')]);

        $response->assertStatus(200);
    }

    public function test_user_can_unsubscribe_from_position(): void
    {
        Session::start();
        // Subscribe first
        $this->actingAs($this->user)
            ->post('/position/' . $this->position->id . '/subscribe', ['_token' => session('_token')]);

        // Then unsubscribe
        $response = $this->actingAs($this->user)
            ->post('/position/' . $this->position->id . '/unsubscribe', ['_token' => session('_token')]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_authorize_position(): void
    {
        Session::start();
        $this->actingAs($this->user)
            ->post('/position/' . $this->position->id . '/authorize', ['_token' => session('_token')])
            ->assertStatus(402);
    }

    public function test_admin_can_view_unauthorized_positions(): void
    {
        $this->actingAs($this->admin)
            ->get('/position/list_notAuthorized')
            ->assertStatus(200)
            ->assertViewIs('position.index_notAuthorized');
    }

    public function test_user_cannot_view_unauthorized_positions(): void
    {
        $this->actingAs($this->user)
            ->get('/position/list_notAuthorized')
            ->assertStatus(402);
    }

    // ── Service history ──────────────────────────────────────

    public function test_admin_can_access_service_history(): void
    {
        $this->actingAs($this->admin)
            ->get('/servicehistory')
            ->assertStatus(200)
            ->assertViewIs('service.history');
    }

    public function test_user_cannot_access_service_history(): void
    {
        $this->actingAs($this->user)
            ->get('/servicehistory')
            ->assertStatus(402);
    }
}
