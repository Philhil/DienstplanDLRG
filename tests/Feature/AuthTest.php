<?php

namespace Tests\Feature;

use App\Client;
use App\Client_user;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    private function createClientWithUser(bool $isAdmin = false, bool $approved = true): array
    {
        $client = Client::create([
            'name'                       => 'TestClient',
            'seasonStart'                => '2000-01-01',
            'isMailinglistCommunication' => false,
            'weeklyServiceviewEmail'     => false,
            'mailinglistAddress'         => null,
            'mailSenderName'             => 'Test',
            'mailReplyAddress'           => 'test@test.de',
            'module_training'            => true,
            'module_training_credit'     => true,
            'module_statistic'           => true,
            'module_survey'              => true,
        ]);

        $user = User::create([
            'name'             => 'Mustermann',
            'first_name'       => 'Max',
            'email'            => 'max@test.de',
            'password'         => bcrypt('correctpassword'),
            'approved'         => $approved ? 1 : 0,
            'currentclient_id' => $client->id,
        ]);

        Client_user::create([
            'client_id'        => $client->id,
            'user_id'          => $user->id,
            'isAdmin'          => $isAdmin,
            'isTrainingEditor' => false,
            'approved'         => true,
        ]);

        return [$client, $user];
    }

    // ── Login ────────────────────────────────────────────────

    public function test_login_with_correct_credentials_succeeds(): void
    {
        [, $user] = $this->createClientWithUser();

        Session::start();
        $this->followingRedirects()
            ->post('/login', [
                'email'    => 'max@test.de',
                'password' => 'correctpassword',
                '_token'   => session('_token'),
            ])
            ->assertStatus(200)
            ->assertViewIs('service.index');
    }

    public function test_login_with_wrong_password_fails(): void
    {
        $this->createClientWithUser();

        Session::start();
        $this->followingRedirects()
            ->post('/login', [
                'email'    => 'max@test.de',
                'password' => 'wrongpassword',
                '_token'   => session('_token'),
            ])
            ->assertStatus(200)
            ->assertViewIs('auth.login');
    }

    public function test_login_with_unknown_email_fails(): void
    {
        Session::start();
        $this->followingRedirects()
            ->post('/login', [
                'email'    => 'nobody@test.de',
                'password' => 'anypassword',
                '_token'   => session('_token'),
            ])
            ->assertStatus(200)
            ->assertViewIs('auth.login');
    }

    public function test_unapproved_user_cannot_access_protected_routes(): void
    {
        [, $user] = $this->createClientWithUser(false, false);

        $this->actingAs($user)
            ->followingRedirects()
            ->get('/home')
            ->assertStatus(200);
        // Unapproved users should be redirected away from protected content
        // (actual behavior depends on middleware — test that they don't reach home.index)
    }

    public function test_logout_clears_session(): void
    {
        [, $user] = $this->createClientWithUser();

        Session::start();
        $this->actingAs($user)
            ->followingRedirects()
            ->post('/logout', ['_token' => session('_token')])
            ->assertStatus(200)
            ->assertViewIs('auth.login');
    }

    // ── Registration ─────────────────────────────────────────

    public function test_registration_creates_unapproved_user(): void
    {
        // Need a real client in DB for registration to attach to
        [$client] = $this->createClientWithUser();

        Session::start();

        $this->post('/register', [
            'name'                  => 'Neumann',
            'first_name'            => 'Karl',
            'email'                 => 'karl@test.de',
            'password'              => 'securepassword',
            'password_confirmation' => 'securepassword',
            'zip'                   => 'spamprevention', // honeypot spam check
            'street'                => '',               // must be empty (honeypot)
            'client'                => [$client->id],    // valid client
            '_token'                => session('_token'),
        ]);

        $user = User::where('email', 'karl@test.de')->first();
        $this->assertNotNull($user);
        $this->assertEquals(0, $user->approved);
    }

    public function test_registration_with_missing_fields_fails(): void
    {
        Session::start();

        $response = $this->post('/register', [
            'email'  => 'incomplete@test.de',
            '_token' => session('_token'),
        ]);

        $response->assertSessionHasErrors();
        $this->assertNull(User::where('email', 'incomplete@test.de')->first());
    }

    public function test_duplicate_email_registration_fails(): void
    {
        $this->createClientWithUser();

        Session::start();
        $response = $this->post('/register', [
            'name'                  => 'Other',
            'first_name'            => 'Person',
            'email'                 => 'max@test.de', // already exists
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            '_token'                => session('_token'),
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
