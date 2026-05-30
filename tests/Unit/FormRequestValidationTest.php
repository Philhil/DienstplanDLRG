<?php

namespace Tests\Unit;

use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\StoreHolidayRequest;
use App\Http\Requests\StoreService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FormRequestValidationTest extends TestCase
{
    // ── Test 21: StoreHolidayRequest ─────────────────────────────────────────

    public function test_holiday_rejects_to_date_before_from_date()
    {
        $data = ['from' => '2026-06-15', 'to' => '2026-06-10'];
        $v = Validator::make($data, (new StoreHolidayRequest())->rules());

        $this->assertFalse($v->passes());
        $this->assertArrayHasKey('to', $v->errors()->toArray());
    }

    public function test_holiday_accepts_to_date_equal_to_from_date()
    {
        $data = ['from' => '2026-06-15', 'to' => '2026-06-15'];
        $v = Validator::make($data, (new StoreHolidayRequest())->rules());

        $this->assertTrue($v->passes());
    }

    public function test_holiday_rejects_missing_from_date()
    {
        $data = ['to' => '2026-06-15'];
        $v = Validator::make($data, (new StoreHolidayRequest())->rules());

        $this->assertFalse($v->passes());
        $this->assertArrayHasKey('from', $v->errors()->toArray());
    }

    // ── Test 22: StoreCalendarRequest ────────────────────────────────────────

    public function test_calendar_rejects_missing_title()
    {
        $data = ['date' => '15 06 2026 10:00'];
        $v = Validator::make($data, (new StoreCalendarRequest())->rules());

        $this->assertFalse($v->passes());
        $this->assertArrayHasKey('title', $v->errors()->toArray());
    }

    public function test_calendar_rejects_dateEnd_before_date()
    {
        $data = [
            'date'    => '15 06 2026 14:00',
            'dateEnd' => '15 06 2026 10:00',
            'title'   => 'Test Event',
        ];
        $v = Validator::make($data, (new StoreCalendarRequest())->rules());

        $this->assertFalse($v->passes());
        $this->assertArrayHasKey('dateEnd', $v->errors()->toArray());
    }

    public function test_calendar_accepts_valid_data()
    {
        $data = [
            'date'    => '15 06 2026 10:00',
            'dateEnd' => '15 06 2026 14:00',
            'title'   => 'Test Event',
        ];
        $v = Validator::make($data, (new StoreCalendarRequest())->rules());

        $this->assertTrue($v->passes());
    }

    // ── Test 23: StoreService ────────────────────────────────────────────────

    public function test_service_rejects_date_in_the_past()
    {
        $data = ['date' => Carbon::yesterday()->format('d m Y H:i')];
        $v = Validator::make($data, (new StoreService())->rules());

        $this->assertFalse($v->passes());
        $this->assertArrayHasKey('date', $v->errors()->toArray());
    }

    public function test_service_rejects_wrong_date_format()
    {
        $data = ['date' => '2026-06-15 10:00']; // ISO format, not d m Y H:i
        $v = Validator::make($data, (new StoreService())->rules());

        $this->assertFalse($v->passes());
    }

    public function test_service_accepts_future_date_with_correct_format()
    {
        $data = ['date' => Carbon::tomorrow()->format('d m Y H:i')];
        $v = Validator::make($data, (new StoreService())->rules());

        $this->assertTrue($v->passes());
    }
}
