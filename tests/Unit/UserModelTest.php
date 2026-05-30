<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // ── Test 18: isSuperAdmin ────────────────────────────────────────────────

    public function test_isSuperAdmin_returns_true_for_admin_role()
    {
        $user = new User();
        $user->role = 'admin';
        $this->assertTrue($user->isSuperAdmin());
    }

    public function test_isSuperAdmin_returns_false_for_benutzer_role()
    {
        $user = new User();
        $user->role = 'benutzer';
        $this->assertFalse($user->isSuperAdmin());
    }

    // ── Test 19: ical_token auto-generation ─────────────────────────────────

    public function test_ical_token_is_auto_generated_as_uuid_on_creation()
    {
        $user = User::create([
            'name'       => 'Test',
            'first_name' => 'Test',
            'email'      => 'test@example.com',
            'password'   => bcrypt('password'),
        ]);

        $token = $user->fresh()->ical_token;
        $this->assertNotNull($token);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $token
        );
    }

    public function test_ical_token_is_not_mass_assignable()
    {
        $user = new User(['ical_token' => 'should-be-ignored']);
        $this->assertNull($user->ical_token);
    }

    // ── Test 20: revokeIcalToken ─────────────────────────────────────────────

    public function test_revoke_ical_token_generates_a_different_valid_uuid()
    {
        $user = User::create([
            'name'       => 'Test',
            'first_name' => 'Test',
            'email'      => 'test@example.com',
            'password'   => bcrypt('password'),
        ]);

        $original = $user->fresh()->ical_token;
        $user->revokeIcalToken();
        $renewed = $user->fresh()->ical_token;

        $this->assertNotEquals($original, $renewed);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $renewed
        );
    }
}
