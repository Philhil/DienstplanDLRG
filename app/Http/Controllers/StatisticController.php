<?php

namespace App\Http\Controllers;

use App\News;
use App\Position;
use App\Service;
use App\Training_user;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
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
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $saison = Auth::user()->currentclient()->Season();
        if (!empty($saison['from']) && !empty($saison['to']))
        {
            return $this->getStatistic($saison['from'], $saison['to']);
        }
    }

    private function getStatistic($from, $to)
    {
        $overviewStats = $this->getTotalStat();
        $timespanStats = $this->statisticTimespan($from, $to);

        return view('statistic.index', compact('overviewStats', 'timespanStats'));
    }
    private function statisticTimespan($from, $to)
    {
        $stat = collect();

        $timespanInMonths = range($from->year, $to->year);
        $month = $from->month;

        foreach ($timespanInMonths as $year)
        {
            //lastyear year
            if ($year == $to->year && $month > $to->month)
            {
                break;
            }
            $timespanInMonths[$to->year] = 0;
        }
        //to year

        $stat->put('timespan', $timespanInMonths);

        //neue user pro Monat
        $users = User::selectRaw('year(created_at) year, month(created_at) month, count(*) data')
            ->groupBy('year', 'month')
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->get();

        $users_createedByMonth = array();
        foreach($users as $user) {
            $users_createedByMonth[$user['year'] . " " .  $user['month']] = $user['data'];
        }
        $stat->put('users_createedByMonth', $users_createedByMonth);

        dd($stat);
        //Anz. Dienste pro Helfer in Daterange
        //Anz. Dienste nach Helfer in Daterange
        //Anzahl User mit min 1 Dienst im Zeitraum


        return $stat;
    }

    private function getTotalStat()
    {
        $stat = collect();
        //Anz User
        $users_count = \Auth::user()->currentclient()->user()->count();
        $stat->put('users', $users_count);

        //Anz Dienste
        $users_count = \Auth::user()->currentclient()->Services()->count();
        $stat->put('services', $users_count);

        //Anz pflicht Pos
        $positions_total_required = Position::whereNull('positions.training_id')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->where('positions.requiredposition', '=', 1)->count();
        $stat->put('positions_required', $positions_total_required);

        //Anz optional pos
        $positions_total_optional = Position::whereNull('positions.training_id')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->where('positions.requiredposition', '=', 0)->count();
        $stat->put('positions_optional', $positions_total_optional);

        //Anz besetzte pflicht Pos
        $positions_total_assigned_required = Position::whereNotNull('positions.user_id')->whereNull('positions.training_id')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->where('positions.requiredposition', '=', 1)->count();
        $stat->put('positions_assigned_required', $positions_total_assigned_required);

        //Anz besetzte optional Pos
        $positions_total_assigned_optional = Position::whereNotNull('positions.user_id')->whereNull('positions.training_id')->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.client_id', '=', Auth::user()->currentclient_id)->where('positions.requiredposition', '=', 0)->count();
        $stat->put('positions_assigned_optional', $positions_total_assigned_optional);

        //Anz News
        $news_count = \Auth::user()->currentclient()->News()->count();
        $stat->put('news', $news_count);

        //Anz Fortbildungen
        $trainings_count = \Auth::user()->currentclient()->Trainings()->count();
        $stat->put('trainings', $trainings_count);

        //AVG Zusage zu Diensten
        $service_avg = \Auth::user()->currentclient()->Services()->join('positions', 'services.id', '=', 'positions.service_id')->whereNotNull('positions.user_id')
            ->select(\DB::raw('DATEDIFF(services.date, positions.updated_at) AS datediff'))->get()->avg('datediff');
        $stat->put('service_avg_inDays', $service_avg);

        return $stat;
    }
}
