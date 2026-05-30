<?php

namespace Tests\Feature;

use App\Client;
use App\Position;
use App\PositionCandidature;
use App\Qualification;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PositionSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        $this->artisan('demo:createDemoClient');
    }

    // ── Test 11: subscribe, hastoauthorize=false → direct assignment ─────────

    public function test_subscribe_without_authorization_assigns_user_directly()
    {
        $user   = User::where('name', 'User')->first();
        $client = Client::first();
        $rs     = Qualification::where(['short' => 'RS', 'client_id' => $client->id])->first();

        $service = new Service();
        $service->date           = Carbon::tomorrow()->startOfDay()->addHours(6);
        $service->hastoauthorize = false;
        $service->client_id      = $client->id;
        $service->save();

        $position = new Position();
        $position->service_id       = $service->id;
        $position->qualification_id = $rs->id;
        $position->user_id          = null;
        $position->save();

        $this->actingAs($user)->get('/position/'.$position->id.'/subscribe');

        $this->assertEquals($user->id, Position::find($position->id)->user_id);
    }

    // ── Test 12: subscribe, hastoauthorize=true → creates candidature ────────

    public function test_subscribe_with_authorization_creates_candidature()
    {
        $user            = User::where('name', 'User')->first();
        $client          = Client::first();
        $bf              = Qualification::where(['short' => 'Bf', 'client_id' => $client->id])->first();
        $serviceTomorrow = Service::whereDate('date', Carbon::tomorrow()->toDateString())->first();
        $positionBf      = Position::where(['service_id' => $serviceTomorrow->id, 'qualification_id' => $bf->id])
            ->whereNull('user_id')->first();

        $this->actingAs($user)->get('/position/'.$positionBf->id.'/subscribe');

        $this->assertDatabaseHas('positioncandidatures', [
            'user_id'     => $user->id,
            'position_id' => $positionBf->id,
        ]);
        $this->assertNull(Position::find($positionBf->id)->user_id);
    }

    // ── Test 13: admin authorizes candidature → user assigned, candidatures removed

    public function test_admin_authorizes_candidature_assigns_user_and_removes_candidatures()
    {
        $user            = User::where('name', 'User')->first();
        $admin           = User::where('name', 'Admin')->first();
        $client          = Client::first();
        $bf              = Qualification::where(['short' => 'Bf', 'client_id' => $client->id])->first();
        $serviceTomorrow = Service::whereDate('date', Carbon::tomorrow()->toDateString())->first();
        $positionBf      = Position::where(['service_id' => $serviceTomorrow->id, 'qualification_id' => $bf->id])
            ->whereNull('user_id')->first();

        $candidature = PositionCandidature::create([
            'user_id'     => $user->id,
            'position_id' => $positionBf->id,
        ]);

        $this->actingAs($admin)->get('/position/'.$candidature->id.'/authorize');

        $this->assertEquals($user->id, Position::find($positionBf->id)->user_id);
        $this->assertDatabaseMissing('positioncandidatures', ['position_id' => $positionBf->id]);
    }

    // ── Test 14: user without qualification cannot subscribe ─────────────────

    public function test_user_without_qualification_cannot_subscribe()
    {
        $user   = User::where('name', 'User')->first();
        $client = Client::first();

        $newQual = Qualification::create([
            'name'                      => 'Sanitäter',
            'short'                     => 'San',
            'isservicedefault'          => false,
            'defaultcount'              => 0,
            'defaultrequiredasposition' => false,
        ]);
        $newQual->client_id = $client->id;
        $newQual->save();

        $service = new Service();
        $service->date           = Carbon::tomorrow()->startOfDay()->addHours(8);
        $service->hastoauthorize = false;
        $service->client_id      = $client->id;
        $service->save();

        $position = new Position();
        $position->service_id       = $service->id;
        $position->qualification_id = $newQual->id;
        $position->user_id          = null;
        $position->save();

        $this->actingAs($user)->get('/position/'.$position->id.'/subscribe');

        $this->assertNull(Position::find($position->id)->user_id);
        $this->assertDatabaseMissing('positioncandidatures', [
            'user_id'     => $user->id,
            'position_id' => $position->id,
        ]);
    }
}
