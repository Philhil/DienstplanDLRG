<?php

namespace Tests\Feature;

use App\Client;
use App\Client_user;
use App\Position;
use App\Training;
use App\Training_user;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class TrainingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;
    protected User $user;
    protected User $admin;
    protected Training $training;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        $this->artisan('demo:createDemoClient');

        $this->user     = User::where('name', 'User')->first();
        $this->admin    = User::where('name', 'Admin')->first();
        $this->client   = Client::first();
        $this->training = Training::first();
    }

    // ── List ─────────────────────────────────────────────────

    public function test_user_can_view_training_list(): void
    {
        $this->actingAs($this->user)
            ->get('/training')
            ->assertStatus(200)
            ->assertViewIs('training.index');
    }

    public function test_admin_can_view_training_list(): void
    {
        $this->actingAs($this->admin)
            ->get('/training')
            ->assertStatus(200)
            ->assertViewIs('training.index');
    }

    // ── Create ───────────────────────────────────────────────

    public function test_admin_can_access_create_training_form(): void
    {
        $this->actingAs($this->admin)
            ->get('/training/create')
            ->assertStatus(200);
    }

    public function test_user_cannot_access_create_training_form(): void
    {
        $this->actingAs($this->user)
            ->get('/training/create')
            ->assertStatus(402);
    }

    public function test_user_cannot_create_training(): void
    {
        // POST /training checks isAdmin||isTrainingEditor and aborts 402.
        // The StoreTrainingRequest validates first; to reach the auth check we need valid data.
        // A regular user hitting this with valid data gets 402.
        Session::start();
        $this->actingAs($this->user)
            ->post('/training', [
                '_token'  => session('_token'),
                'title'   => 'Unauthorised Training',
                'date'    => Carbon::tomorrow()->format('d m Y H:i'),
                'dateEnd' => '',
            ])
            ->assertStatus(402);
    }

    // ── Edit / Delete ─────────────────────────────────────────

    public function test_user_cannot_edit_training(): void
    {
        $this->actingAs($this->user)
            ->get('/training/' . $this->training->id . '/edit')
            ->assertStatus(402);
    }

    public function test_user_cannot_delete_training(): void
    {
        Session::start();
        $this->actingAs($this->user)
            ->delete('/training/' . $this->training->id, ['_token' => session('_token')])
            ->assertStatus(402);
    }

    public function test_admin_can_access_edit_training(): void
    {
        $this->actingAs($this->admin)
            ->get('/training/' . $this->training->id . '/edit')
            ->assertStatus(200);
    }

    // ── Training user management ─────────────────────────────

    public function test_user_cannot_remove_training_user(): void
    {
        Session::start();
        $training_user = Training_user::first();
        $this->actingAs($this->user)
            ->followingRedirects()
            ->post('/training/training_user/' . $training_user->id . '/delete', ['_token' => session('_token')])
            ->assertStatus(402);
    }

    public function test_admin_can_remove_training_user(): void
    {
        Session::start();
        $training_user = Training_user::first();
        $this->actingAs($this->admin)
            ->followingRedirects()
            ->post('/training/training_user/' . $training_user->id . '/delete', ['_token' => session('_token')])
            ->assertStatus(200);
    }

    // ── Training editor role ──────────────────────────────────

    public function test_training_editor_can_access_create_form(): void
    {
        // Promote user to training editor
        Client_user::where([
            'client_id' => $this->client->id,
            'user_id'   => $this->user->id,
        ])->update(['isTrainingEditor' => true]);

        $this->actingAs($this->user)
            ->get('/training/create')
            ->assertStatus(200);
    }
}
