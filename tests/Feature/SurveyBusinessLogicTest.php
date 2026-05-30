<?php

namespace Tests\Feature;

use App\Client;
use App\Survey;
use App\Survey_user;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SurveyBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        $this->artisan('demo:createDemoClient');
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // ── Test 4: vote stores Survey_user with correct value ───────────────────

    public function test_vote_stores_survey_user_with_correct_vote()
    {
        $user   = User::where('name', 'User')->first();
        $survey = Survey::first();

        $this->actingAs($user)
            ->post('/survey/vote/'.$survey->id, ['submit' => 'accept'])
            ->assertRedirect('/survey/'.$survey->id);

        $surveyUser = Survey_user::where(['survey_id' => $survey->id, 'user_id' => $user->id])->first();
        $this->assertNotNull($surveyUser);
        $this->assertTrue((bool) $surveyUser->vote);
    }

    // ── Test 5: postpone increments rememberCount and sets rememberAt ────────

    public function test_postpone_increments_rememberCount_and_sets_rememberAt()
    {
        $user   = User::where('name', 'User')->first();
        $survey = Survey::first();

        $this->actingAs($user)
            ->get('/survey/postpone/'.$survey->id)
            ->assertRedirect();

        $surveyUser = Survey_user::where(['survey_id' => $survey->id, 'user_id' => $user->id])->first();
        $this->assertNotNull($surveyUser);
        $this->assertEquals(1, $surveyUser->rememberCount);
        $this->assertTrue($surveyUser->rememberAt->isSameDay(Carbon::tomorrow()));
    }

    // ── Test 6: fourth postpone flashes error ────────────────────────────────

    public function test_fourth_postpone_flashes_error_message()
    {
        $user   = User::where('name', 'User')->first();
        $survey = Survey::first();

        $surveyUser = new Survey_user();
        $surveyUser->survey_id     = $survey->id;
        $surveyUser->user_id       = $user->id;
        $surveyUser->rememberCount = 3;
        $surveyUser->save();

        $this->actingAs($user)
            ->get('/survey/postpone/'.$survey->id)
            ->assertRedirect('/survey/'.$survey->id)
            ->assertSessionHas('errormessage');
    }

    // ── Test 7: vote with wrong password returns validation error ────────────

    public function test_vote_with_wrong_password_returns_password_error()
    {
        $user   = User::where('name', 'User')->first();
        $survey = Survey::first();
        $survey->passwordConfirmationRequired = true;
        $survey->save();

        $this->actingAs($user)
            ->post('/survey/vote/'.$survey->id, ['submit' => 'accept', 'password' => 'wrongpassword'])
            ->assertSessionHasErrors(['password']);
    }

    // ── Test 8: module_survey disabled returns 402 ───────────────────────────

    public function test_module_survey_disabled_returns_402()
    {
        $user   = User::where('name', 'User')->first();
        $client = Client::first();
        $client->module_survey = false;
        $client->save();

        $this->actingAs($user)
            ->get('/survey')
            ->assertStatus(402);
    }
}
