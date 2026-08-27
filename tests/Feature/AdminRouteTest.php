<?php

namespace Tests\Feature;

use App\Calendar;
use App\News;
use App\Position;
use App\Qualification;
use App\Service;
use App\Survey;
use App\Training;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AdminRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    public function test_AllRoutesAsAdminAvailable()
    {
        $this->artisan('demo:createDemoClient');

        $admin         = User::where('name', '=', 'Admin')->first();
        $user          = User::where('name', '=', 'User')->first();
        $service       = Service::first();
        $position      = Position::first();
        $training      = Training::first();
        $qualification = Qualification::first();
        $calendar      = Calendar::first();
        $survey        = Survey::first();
        $news          = News::first();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // ── Qualification (all methods admin-only via constructor middleware) ─

        $this->actingAs($admin)->get('/qualification')
            ->assertStatus(200)->assertViewIs('qualification.index');

        $this->actingAs($admin)->get('/qualification/create')
            ->assertStatus(200)->assertViewIs('qualification.create');

        $this->actingAs($admin)->get('/qualification/'.$qualification->id.'/edit')
            ->assertStatus(200);

        // ── User management ──────────────────────────────────────────────────

        $this->actingAs($admin)->get('/user')
            ->assertStatus(200)->assertViewIs('user.index');

        $this->actingAs($admin)->get('/user/'.$user->id.'/edit')
            ->assertStatus(200)->assertViewIs('user.edit');

        // ── Service Information ───────────────────────────────────────────────
            
            $this->actingAs($admin)->get('/service/'.$service->id.'/inform')
            ->assertStatus(200)->assertViewIs('inform.create');
            
        // ── Service management ───────────────────────────────────────────────

        $this->actingAs($admin)->get('/service/create')
            ->assertStatus(200)->assertViewIs('service.create');

        $this->actingAs($admin)->get('/servicehistory')
            ->assertStatus(200);

        $this->actingAs($admin)->get('/service/'.$service->id.'/edit')
            ->assertStatus(200)->assertViewIs('service.edit');

        // ── Statistic ────────────────────────────────────────────────────────

        $this->actingAs($admin)->get('/statistic')
            ->assertStatus(200);

        // ── Training management (admin or training editor) ───────────────────

        $this->actingAs($admin)->get('/training/create')
            ->assertStatus(200);

        $this->actingAs($admin)->get('/training/'.$training->id.'/edit')
            ->assertStatus(200);

        // ── Calendar management (admin or training editor) ───────────────────

        $this->actingAs($admin)->get('/calendar/create')
            ->assertStatus(200)->assertViewIs('calendar.create');

        $this->actingAs($admin)->get('/calendar/'.$calendar->id.'/edit')
            ->assertStatus(200);

        // ── News management ──────────────────────────────────────────────────

        $this->actingAs($admin)->get('/news/create')
            ->assertStatus(200)->assertViewIs('news.create');

        $this->actingAs($admin)->get('/news/'.$news->id.'/edit')
            ->assertStatus(200);

        // ── Survey management (admin of client) ──────────────────────────────

        $this->actingAs($admin)->get('/survey')
            ->assertStatus(200)->assertViewIs('survey.index');

        $this->actingAs($admin)->get('/survey/create')
            ->assertStatus(200)->assertViewIs('survey.create');

        $this->actingAs($admin)->get('/survey/'.$survey->id.'/edit')
            ->assertStatus(200)->assertViewIs('survey.edit');

        // ── Position management ──────────────────────────────────────────────

        $this->actingAs($admin)->get('/position/list_notAuthorized')
            ->assertStatus(200)->assertViewIs('position.index_notAuthorized');

        $this->actingAs($admin)->get('/position/'.$position->id.'/position_user')
            ->assertStatus(200)->assertViewIs('position.position_user');
    }
}
