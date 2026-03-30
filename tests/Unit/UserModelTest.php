<?php

namespace Tests\Unit;

use App\Client;
use App\Client_user;
use App\PositionCandidature;
use App\Qualification;
use App\Qualification_user;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');

        // Minimal setup: one client, one regular user, one admin
        $this->client = Client::create([
            'name'                        => 'TestGliederung',
            'seasonStart'                 => '2000-01-01',
            'isMailinglistCommunication'  => false,
            'weeklyServiceviewEmail'      => false,
            'mailinglistAddress'          => null,
            'mailSenderName'              => 'Test',
            'mailReplyAddress'            => 'test@test.de',
            'module_training'             => true,
            'module_training_credit'      => true,
            'module_statistic'            => true,
            'module_survey'               => true,
        ]);

        $this->user = User::create([
            'name'             => 'Mustermann',
            'first_name'       => 'Max',
            'email'            => 'max@test.de',
            'password'         => bcrypt('password'),
            'approved'         => 1,
            'currentclient_id' => $this->client->id,
        ]);

        $this->admin = User::create([
            'name'             => 'Admin',
            'first_name'       => 'Anna',
            'email'            => 'anna@test.de',
            'password'         => bcrypt('password'),
            'approved'         => 1,
            'currentclient_id' => $this->client->id,
        ]);

        // Note: isTrainingEditor/isAdmin/qualifications methods use Auth::user() internally.
        // Each test that calls these must call actingAs() explicitly.

        Client_user::create([
            'client_id'       => $this->client->id,
            'user_id'         => $this->user->id,
            'isAdmin'         => false,
            'isTrainingEditor' => false,
            'approved'        => true,
        ]);

        Client_user::create([
            'client_id'        => $this->client->id,
            'user_id'          => $this->admin->id,
            'isAdmin'          => 1,
            'isTrainingEditor' => 1,
            'approved'         => 1,
        ]);
    }

    // ── Role checks ─────────────────────────────────────────

    public function test_isSuperAdmin_returns_true_only_for_admin_role(): void
    {
        $this->user->role = 'user';
        $this->assertFalse($this->user->isSuperAdmin());

        $this->user->role = 'admin';
        $this->assertTrue($this->user->isSuperAdmin());
    }

    public function test_isAdminOfClient_returns_true_for_admin(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue($this->admin->isAdminOfClient($this->client->id));
    }

    public function test_isAdminOfClient_returns_false_for_regular_user(): void
    {
        $this->actingAs($this->user);
        $this->assertFalse($this->user->isAdminOfClient($this->client->id));
    }

    public function test_isTrainingEditor_returns_true_for_training_editor(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue($this->admin->isTrainingEditor());
    }

    public function test_isTrainingEditor_returns_false_for_regular_user(): void
    {
        $this->actingAs($this->user); // user has isTrainingEditor=false
        $this->assertFalse($this->user->isTrainingEditor());
    }

    public function test_isStatisticEditor_returns_true_when_flag_set(): void
    {
        $this->actingAs($this->user);

        // Set statisticeditor flag for user
        Client_user::where(['client_id' => $this->client->id, 'user_id' => $this->user->id])
            ->update(['isStatisticEditor' => true]);

        $this->assertTrue($this->user->isStatisticEditor());
    }

    public function test_isStatisticEditor_returns_false_without_flag(): void
    {
        $this->actingAs($this->user);
        $this->assertFalse($this->user->isStatisticEditor());
    }

    // ── Qualification checks ─────────────────────────────────

    public function test_hasqualification_returns_true_when_assigned(): void
    {
        $this->actingAs($this->user);

        $qualification = Qualification::create([
            'name'             => 'Rettungsschwimmer',
            'short'            => 'RS',
            'isservicedefault' => false,
            'client_id'        => $this->client->id,
        ]);

        Qualification_user::create([
            'qualification_id' => $qualification->id,
            'user_id'          => $this->user->id,
            'client_id'        => $this->client->id,
        ]);

        $this->assertTrue($this->user->hasqualification($qualification->id));
    }

    public function test_hasqualification_returns_false_when_not_assigned(): void
    {
        $this->actingAs($this->user);

        $qualification = Qualification::create([
            'name'             => 'Bootsführer',
            'short'            => 'BF',
            'isservicedefault' => false,
            'client_id'        => $this->client->id,
        ]);

        $this->assertFalse($this->user->hasqualification($qualification->id));
    }

    public function test_qualificationsNotAssignedToUser_excludes_assigned(): void
    {
        $this->actingAs($this->user); // needed because qualificationsNotAssignedToUser uses Auth::user()

        $q1 = Qualification::create(['name' => 'Q1', 'short' => 'Q1', 'isservicedefault' => false, 'client_id' => $this->client->id]);
        $q2 = Qualification::create(['name' => 'Q2', 'short' => 'Q2', 'isservicedefault' => false, 'client_id' => $this->client->id]);

        Qualification_user::create([
            'qualification_id' => $q1->id,
            'user_id'          => $this->user->id,
            'client_id'        => $this->client->id,
        ]);

        $allQuals = Qualification::where('client_id', $this->client->id)->pluck('id');
        $unassigned = $this->user->qualificationsNotAssignedToUser()->pluck('id');

        // $allQuals should have q1+q2, $unassigned should have only q2
        $this->assertCount(2, $allQuals, 'Should have 2 qualifications total');
        $this->assertTrue($unassigned->contains($q2->id), 'Q2 should be unassigned');
        $this->assertFalse($unassigned->contains($q1->id), 'Q1 should be assigned (excluded)');
    }
}
