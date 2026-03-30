<?php

namespace Tests\Feature;

use App\Client;
use App\Holiday;
use App\Service;
use App\Training;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class HolidayTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
        $this->artisan('demo:createDemoClient');

        $this->user  = User::where('name', 'User')->first();
        $this->admin = User::where('name', 'Admin')->first();
    }

    // ── CRUD ─────────────────────────────────────────────────

    public function test_user_can_create_holiday(): void
    {
        Session::start();
        $this->actingAs($this->user)
            ->followingRedirects()
            ->post('/holiday', [
                '_token' => session('_token'),
                'from'   => Carbon::tomorrow()->format('Y-m-d'),
                'to'     => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('holidays', ['user_id' => $this->user->id]);
    }

    public function test_user_can_view_holiday_list(): void
    {
        $this->actingAs($this->user)
            ->get('/holiday')
            ->assertStatus(200)
            ->assertViewIs('holiday.index');
    }

    public function test_user_can_access_create_holiday_form(): void
    {
        $this->actingAs($this->user)
            ->get('/holiday/create')
            ->assertStatus(200)
            ->assertViewIs('holiday.create');
    }

    public function test_user_can_delete_own_holiday(): void
    {
        Session::start();
        // Create holiday first
        $this->actingAs($this->user)->post('/holiday', [
            '_token' => session('_token'),
            'from'   => Carbon::tomorrow()->format('Y-m-d'),
            'to'     => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
        ]);

        $holiday = Holiday::where('user_id', $this->user->id)->first();
        $this->assertNotNull($holiday);

        $this->actingAs($this->user)
            ->delete('/holiday/' . $holiday->id, ['_token' => session('_token')]);

        $this->assertDatabaseMissing('holidays', ['id' => $holiday->id]);
    }

    // ── Holiday → service/training blocking ──────────────────

    public function test_service_in_holiday_range_appears_in_holiday_list(): void
    {
        $this->actingAs($this->user);
        // Clear any demo holidays that might interfere
        \App\Holiday::where('user_id', $this->user->id)->delete();

        $service = Service::where('date', '>=', Carbon::tomorrow())->first();
        $serviceDate = $service->date;

        \App\Holiday::create([
            'user_id' => $this->user->id,
            'from'    => $serviceDate->copy()->subDay(),
            'to'      => $serviceDate->copy()->addDay(),
        ]);

        $this->user->refresh();
        $holidayServices = $this->user->services_inHolidayList();
        $this->assertContains($service->id, $holidayServices);
    }

    public function test_service_outside_holiday_range_not_in_holiday_list(): void
    {
        $this->actingAs($this->user);
        // Clear any demo holidays first
        \App\Holiday::where('user_id', $this->user->id)->delete();

        // Add holiday far in the future — no current services should appear
        \App\Holiday::create([
            'user_id' => $this->user->id,
            'from'    => Carbon::now()->addYears(5),
            'to'      => Carbon::now()->addYears(5)->addDays(7),
        ]);

        $this->user->refresh();
        $holidayServices = $this->user->services_inHolidayList();
        $this->assertEmpty($holidayServices);
    }

    public function test_training_in_holiday_range_appears_in_holiday_list(): void
    {
        $this->actingAs($this->user);
        $training = Training::first();
        $trainingDate = $training->date;

        \App\Holiday::create([
            'user_id' => $this->user->id,
            'from'    => $trainingDate->copy()->subDay(),
            'to'      => $trainingDate->copy()->addDay(),
        ]);

        $this->user->refresh();
        $holidayTrainings = $this->user->trainings_inHolidayList();
        $this->assertContains($training->id, $holidayTrainings);
    }

    // ── Store holiday from service/training shortcut ─────────

    public function test_storeservice_creates_holiday_from_service_date(): void
    {
        $service = Service::first();

        $this->actingAs($this->user)
            ->followingRedirects()
            ->get('/holiday/storeservice/' . $service->id)
            ->assertStatus(200)
            ->assertViewIs('service.index');

        $this->assertDatabaseHas('holidays', ['user_id' => $this->user->id]);
    }

    public function test_storetraining_creates_holiday_from_training_date(): void
    {
        $training = Training::first();

        $this->actingAs($this->user)
            ->followingRedirects()
            ->get('/holiday/storetraining/' . $training->id)
            ->assertStatus(200)
            ->assertViewIs('training.index');

        $this->assertDatabaseHas('holidays', ['user_id' => $this->user->id]);
    }
}
