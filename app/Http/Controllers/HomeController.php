<?php

namespace App\Http\Controllers;

use App\Position;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions_user_past = Position::where('user_id', '=', Auth::user()->id)->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))->count();
        $positions_total_past = Position::where('user_id', '!=', null)->join('services', 'positions.service_id', '=', 'services.id')
                            ->where('services.date', '<', DB::raw('CURDATE()'))->count();
        $positions_free = Position::where('user_id', '=', null)->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '>=', DB::raw('CURDATE()'))->count();
        $top_users = Position::where('user_id', '!=', null)->with('user')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.date', '<', DB::raw('CURDATE()'))->selectRaw('user_id, count(*) as aggregate')->groupBy('user_id')->get();

        return view('home.index', compact('positions_user_past', 'positions_total_past', 'positions_free', 'top_users'));
    }
}
