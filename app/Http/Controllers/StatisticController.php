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
    public function index(Request $request)
    {
        if(!Auth::user()->isAdmin() && !Auth::user()->isStatisticEditor()) {
            abort(402, "Nope.");
        }
        $from = null;
        $to = null;

        if($request->has('from') && $request->has('to')) {
            $from = Carbon::createFromFormat('d.m.Y', $request->input('from'));
            $to = Carbon::createFromFormat('d.m.Y', $request->input('to'));
        }

        if(empty($from) || empty($to)) {
            $saison = Auth::user()->currentclient()->Season();
            $from = $saison['from'];
            $to = $saison['to'];
        }

        return $this->getStatistic($from, $to);
    }

    private function getStatistic($from, $to)
    {
        $overviewStats = $this->getTotalStat();
        $timespanStats = $this->statisticTimespan($from, $to);

        $timespanlabel = '"' . implode('","', array_keys($timespanStats['timespan'])) . '"';

        return view('statistic.index', compact('overviewStats', 'timespanStats', 'timespanlabel', 'from', 'to'));
    }
    private function statisticTimespan($from, $to)
    {
        $stat = collect();

        $timespanInMonths = array();
        //in first year from 'first month'
        $month = $from->month;

        //iterate to all years + month
        foreach (range($from->year, $to->year) as $year)
        {
            while($month <= 12)
            {
                //in last year till 'last month'
                if ($year == $to->year && $month > $to->month)
                {
                    break;
                }
                $timespanInMonths[$year . " " .$month] = 0;
                $month += 1;
            }

            $month = 1;
        }

        $stat->put('timespan', $timespanInMonths);

        //Anz Dienste
        $users_count = \Auth::user()->currentclient()->Services()->whereBetween('services.date', [$from, $to])->count();
        $stat->put('services', $users_count);

        //neue user pro Monat
        $users = User::selectRaw('year(users.created_at) year, month(users.created_at) month, count(*) data')
            ->whereBetween('users.created_at', [$from, $to])
            ->join('client_user', 'users.id', '=', 'client_user.user_id')->where('client_user.client_id', '=',\Auth::user()->currentclient_id)
            ->groupBy('year', 'month')
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->get();

        $users_createedByMonth = $timespanInMonths;
        foreach($users as $user) {
            $users_createedByMonth[$user['year'] . " " .  $user['month']] = $user['data'];
        }
        $stat->put('users_createedByMonth', $users_createedByMonth);

        //Anz pflicht Pos in Daterange
        $positions_total_required = Position::whereNull('positions.training_id')
            ->join('services', 'positions.service_id', '=', 'services.id')->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->where('positions.requiredposition', '=', 1)
            ->whereBetween('services.date', [$from, $to])
            ->count();
        $stat->put('positions_required', $positions_total_required);

        //Anz optional pos in Daterange
        $positions_total_optional = Position::whereNull('positions.training_id')
            ->join('services', 'positions.service_id', '=', 'services.id')->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->where('positions.requiredposition', '=', 0)
            ->whereBetween('services.date', [$from, $to])
            ->count();
        $stat->put('positions_optional', $positions_total_optional);

        //Anz besetzte pflicht Pos
        $positions_total_assigned_required = Position::whereNotNull('positions.user_id')->whereNull('positions.training_id')
            ->join('services', 'positions.service_id', '=', 'services.id')->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->where('positions.requiredposition', '=', 1)
            ->whereBetween('services.date', [$from, $to])
            ->count();
        $stat->put('positions_assigned_required', $positions_total_assigned_required);

        //Anz besetzte optional Pos
        $positions_total_assigned_optional = Position::whereNotNull('positions.user_id')->whereNull('positions.training_id')
            ->join('services', 'positions.service_id', '=', 'services.id')->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->where('positions.requiredposition', '=', 0)
            ->whereBetween('services.date', [$from, $to])
            ->count();
        $stat->put('positions_assigned_optional', $positions_total_assigned_optional);


        //Anz. Dienste pro Helfer in Daterange (Durchscnitt)
        $users_count = \Auth::user()->currentclient()->user()->count();
        $positions_per_user_avg = round(($positions_total_assigned_optional + $positions_total_assigned_required) / $users_count, 2);
        $stat->put('positions_per_user_avg', $positions_per_user_avg);

        //Anzahl User mit min 1 Dienst im Zeitraum
        $users_min1pos = Position::whereNotNull('positions.user_id')->whereNull('positions.training_id')
            ->join('services', 'positions.service_id', '=', 'services.id')->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->whereBetween('services.date', [$from, $to])
            ->groupBy('positions.user_id')
            ->get()->count();
        //instead groupBy -> get -> count: ->distinct('positions.user_id')->count('positions.user_id');
        $stat->put('users_min1pos', $users_min1pos);

        //AVG Zusage zu Diensten
        $service_avg = \Auth::user()->currentclient()->Services()
            ->join('positions', 'services.id', '=', 'positions.service_id')->whereNotNull('positions.service_id')
            ->whereNotNull('positions.user_id')
            ->whereBetween('services.date', [$from, $to])
            ->select(\DB::raw('DATEDIFF(services.date, positions.updated_at) AS datediff'))->get()->avg('datediff');
        $stat->put('service_avg_inDays', $service_avg);

        //ehrenamtlich geleistete Stunden in Daterange (Service end - service start) * anz besetzte pos
        $service_hours = Service::where('services.client_id', '=', Auth::user()->currentclient_id)
            ->whereBetween('services.date', [$from, $to])
            ->select(\DB::raw('timestampdiff(HOUR, services.date, services.dateEnd) AS datediff'))->withCount('assignpositions')->get();
        $totalservice_hours = 0;
        foreach ($service_hours as $service_hour) {
            if(!empty($service_hour->datediff)) {
                $totalservice_hours += ($service_hour->datediff * $service_hour->assignpositions_count);
            }
        }
        $stat->put('totalservice_hours', $totalservice_hours);

        //Anz. Dienste nach Helfer in Daterange
        $countpos = \Auth::user()->currentclient()->Services()
            ->selectRaw('users.id, count(*) countpositions')
            ->join('positions', 'services.id', '=', 'positions.service_id')->whereNotNull('positions.service_id')
            ->join('users', 'positions.user_id', '=', 'users.id')
            ->join('client_user', 'client_user.user_id', '=', 'users.id')
            ->where('client_user.client_id', '=', \Auth::user()->currentclient_id)
            ->whereBetween('services.date', [$from, $to])
            ->groupBy('positions.user_id')
            ->orderBy('users.name')
            ->get();
        $users_countpos = array();
        //foreach to group by user [user:anzahl]
        foreach($countpos as $pos) {
            $users_countpos[$pos->id] = $pos->countpositions;
        }
        $stat->put('users_countpos', $users_countpos);

        //anzahl Dienste nach Qualifikation:
        //SELECT users.name, users.first_name, positions.qualification_id, qualifications.name, count(*) countPositionsWithQualifications FROM services JOIN positions on services.id = positions.service_id JOIN users on users.id = positions.user_id
        //JOIN qualifications on positions.qualification_id = qualifications.id
        //WHERE services.date > "2018-08-26 00:00:00" and services.date < "2020-12-31 00:00:00" AND services.client_id = 2 AND positions.service_id IS NOT NULL GROUP BY positions.qualification_id, positions.user_id ORDER BY users.name
        $countposByQuali = \Auth::user()->currentclient()->Services()
            ->selectRaw('users.id, positions.qualification_id as qualification_id, count(*) countPositionsWithQualifications')
            ->join('positions', 'services.id', '=', 'positions.service_id')->whereNotNull('positions.service_id')
            ->join('users', 'positions.user_id', '=', 'users.id')
            ->join('client_user', 'client_user.user_id', '=', 'users.id')
            ->where('client_user.client_id', '=', \Auth::user()->currentclient_id)
            ->whereBetween('services.date', [$from, $to])
            ->groupBy('positions.user_id')
            ->groupBy('positions.qualification_id')
            ->orderBy('users.name')
            ->get();
        $users_countposByQuali = array();

        //foreach to group by user [user:[quali1:anzahl]]
        foreach($countposByQuali as $posquali) {
            if (empty($users_countposByQuali[$posquali->id])) {
                $users_countposByQuali[$posquali->id] = array();
            }

            $users_countposByQuali[$posquali->id][$posquali->qualification_id] = $posquali->countPositionsWithQualifications;
        }

        $stat->put('users_countposByQuali', $users_countposByQuali);

        $users = \Auth::user()->currentclient()->user()
            ->select('users.id', 'users.name', 'users.first_name')
            ->get();
        $stat->put('users', $users);

        $qualifications = \Auth::user()->currentclient()->Qualifications()
            ->select('qualifications.id', 'qualifications.name', 'qualifications.short')
            ->orderBy('qualifications.short')
            ->get();
        $stat->put('qualifications', $qualifications);


        //how many position of a Qualification are requested for each month
        $couintposByQuali_inmonth = \Auth::user()->currentclient()->Services()
            ->selectRaw('year(services.date) year, month(services.date) month, positions.qualification_id as qualification_id, count(*) countPositionsWithQualifications')
            ->join('positions', 'services.id', '=', 'positions.service_id')->whereNotNull('positions.service_id')
            ->whereBetween('services.date', [$from, $to])
            ->groupBy('year', 'month', 'positions.qualification_id')
            ->orderBy('month', 'ASC')
            ->get();

        //foreach to group for each qualification by "year month" [quali[date:anzahl]]
        $quali_byMonth_pos = array();
        $quali_colors = array();
        foreach ($qualifications as $quali) {
            $quali_byMonth_pos[$quali->id] = $timespanInMonths;

            $rgbColor = array();
            foreach(array('r', 'g', 'b') as $color){
                //Generate a random number between 0 and 255.
                $rgbColor[$color] = mt_rand(0, 255);
            }
            $quali_colors[$quali->id] = implode(",", $rgbColor);
        }

        foreach($couintposByQuali_inmonth as $pos_quali_count) {
            if(array_key_exists($pos_quali_count['year'] . " " .  $pos_quali_count['month'], $quali_byMonth_pos[$pos_quali_count->qualification_id])) {
                $quali_byMonth_pos[$pos_quali_count->qualification_id][$pos_quali_count['year'] . " " .  $pos_quali_count['month']] = $pos_quali_count->countPositionsWithQualifications;
            }
        }
        $stat->put('quali_byMonth_pos', $quali_byMonth_pos);
        $stat->put('quali_colors', $quali_colors);

        //besetzt/nicht besetzt nach quali
        $positions_total_required_quali_all = Position::whereNull('positions.training_id')
            ->selectRaw('positions.requiredposition, positions.qualification_id as qualification_id, count(*) countPositionsWithQualifications')
            ->join('services', 'positions.service_id', '=', 'services.id')->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->whereBetween('services.date', [$from, $to])
            ->groupBy('positions.requiredposition', 'positions.qualification_id')
            ->get();
        //posquali_all_required[quali:countPos]
        //posquali_all_optional[quali:countPos]
        $posquali_all_required = array();
        $posquali_all_optional = array();
        foreach ($positions_total_required_quali_all as $posquali) {
            if ($posquali->requiredposition == 1) {
                $posquali_all_required[$posquali->qualification_id] = $posquali->countPositionsWithQualifications;
            }
            else {
                $posquali_all_optional[$posquali->qualification_id] = $posquali->countPositionsWithQualifications;
            }
        }
        $stat->put('posquali_all_required', $posquali_all_required);
        $stat->put('posquali_all_optional', $posquali_all_optional);

        $positions_total_required_quali_assigned = Position::whereNotNull('positions.user_id')->whereNull('positions.training_id')
            ->selectRaw('positions.requiredposition, positions.qualification_id as qualification_id, count(*) countPositionsWithQualifications')
            ->join('services', 'positions.service_id', '=', 'services.id')->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->whereBetween('services.date', [$from, $to])
            ->groupBy('positions.requiredposition', 'positions.qualification_id')
            ->get();
        //posquali_assigned_required[quali:countPos]
        //posquali_assigned_optional[quali:countPos]
        $posquali_assigned_required = array();
        $posquali_assigned_optional = array();
        foreach ($positions_total_required_quali_assigned as $posquali) {
            if ($posquali->requiredposition == 1) {
                $posquali_assigned_required[$posquali->qualification_id] = $posquali->countPositionsWithQualifications;
            }
            else {
                $posquali_assigned_optional[$posquali->qualification_id] = $posquali->countPositionsWithQualifications;
            }
        }
        $stat->put('posquali_assigned_required', $posquali_assigned_required);
        $stat->put('posquali_assigned_optional', $posquali_assigned_optional);

        $service_avg_byquali_inDays = array();

        foreach ($qualifications as $quali) {
            $service_avg_byquali_inDays[$quali->id] = \Auth::user()->currentclient()->Services()
                ->join('positions', 'services.id', '=', 'positions.service_id')->whereNotNull('positions.service_id')
                ->whereNotNull('positions.user_id')
                ->whereBetween('services.date', [$from, $to])
                ->where('positions.qualification_id', '=', $quali->id)
                ->selectRaw('DATEDIFF(services.date, positions.updated_at) AS datediff')->get()->avg('datediff');
        }
        $stat->put('service_avg_byquali_inDays', $service_avg_byquali_inDays);

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

        //Anz News
        $news_count = \Auth::user()->currentclient()->News()->count();
        $stat->put('news', $news_count);

        //Anz Fortbildungen
        $trainings_count = \Auth::user()->currentclient()->Trainings()->count();
        $stat->put('trainings', $trainings_count);

        //AVG Zusage zu Diensten
        $service_avg = \Auth::user()->currentclient()->Services()
            ->join('positions', 'services.id', '=', 'positions.service_id')->whereNotNull('positions.user_id')
            ->whereNotNull('positions.service_id')
            ->select(\DB::raw('DATEDIFF(services.date, positions.updated_at) AS datediff'))->get()->avg('datediff');
        $stat->put('service_avg_inDays', $service_avg);

        //Count Users with qualification
        $qualifications = \Auth::user()->currentclient()->Qualifications()->withCount('users')->get();
        $stat->put('qualifications_users_count', $qualifications);

        return $stat;
    }
}
