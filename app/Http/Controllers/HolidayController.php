<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\Http\Requests\StoreHolidayRequest;
use App\Service;
use App\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = Holiday::where('user_id', '=', Auth::user()->id)->whereDate('to', '>=', Carbon::now())->orderBy('from', 'ASC')->get();
        return view('holiday.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $holiday = new Holiday();
        return view('holiday.create', compact('holiday'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHolidayRequest $request)
    {
        $holiday = new Holiday($request->all());
        $holiday['user_id'] = Auth::user()->id;
        $holiday->save();

        return redirect(action('HolidayController@index'));
    }

    /**
     * Store a newly created resource in relation to a Service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeService($service_id)
    {
        $service = Service::findOrFail($service_id);

        $holiday = new Holiday();
        $holiday['from'] = $service->date;
        $holiday['to'] = empty($service->dateEnd) ? $service->date->endOfDay() : $service->dateEnd;
        $holiday['user_id'] = Auth::user()->id;
        $holiday->save();

        return redirect(action('ServiceController@index'));
    }

    /**
     * Store a newly created resource in relation to a Training storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTraining($training_id)
    {
        $training = Training::findOrFail($training_id);

        $holiday = new Holiday();
        $holiday['from'] = $training->date;
        $holiday['to'] = empty($training->dateEnd) ? $training->date->endOfDay() : $training->dateEnd;
        $holiday['user_id'] = Auth::user()->id;
        $holiday->save();

        return redirect(action('TrainingController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        if($holiday->user_id != Auth::user()->id) {
            abort(402, "Nope.");
        }
        return view('holiday.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        if($holiday->user_id != Auth::user()->id) {
            abort(402, "Nope.");
        }

        $holiday->from = $request->get('from');
        $holiday->to = $request->get('to');
        $holiday->save();

        return redirect(action('HolidayController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday)
    {
        if($holiday->user_id != Auth::user()->id) {
            abort(402, "Nope.");
        }

        $holiday->delete();

        return redirect(action('HolidayController@index'));
    }
}
