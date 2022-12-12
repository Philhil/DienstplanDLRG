<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'email', 'approved', 'role', 'password', 'mobilenumber', 'currentclient_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isSuperAdmin()
    {
        return $this->role == "admin";
    }

    public function isAdmin()
    {
        return $this->isAdminOfClient(Auth::user()->currentclient_id);
    }

    public function isAdminOfClient($clientID)
    {
        if (Client_user::where(['client_id' => $clientID, 'user_id' => Auth::user()->id, 'isAdmin' => true])->count() > 0)
        {
            return true;
        }
        return false;
    }

    public function isTrainingEditor()
    {
        if (Client_user::where(['client_id' => Auth::user()->currentclient_id, 'user_id' => Auth::user()->id, 'isTrainingEditor' => true])->count() > 0)
        {
            return true;
        }
        return false;
    }

    public function isTrainingEditorOfClient($clientID)
    {
        if (Client_user::where(['client_id' => $clientID, 'user_id' => Auth::user()->id, 'isTrainingEditor' => true])->count() > 0)
        {
            return true;
        }
        return false;
    }

    public function qualifications()
    {
        return $this->belongsToMany(Qualification::class, 'qualification_users')->where('client_id', '=', Auth::user()->currentclient_id)->orderBy('name');
    }

    public function qualificationsNotAssignedToUser()
    {
        $id = $this->id;
        return Qualification::leftJoin('qualification_users',function ($join) use ($id) {
            $join->on('qualifications.id', '=', 'qualification_users.qualification_id');
            $join->on('qualification_users.user_id', '=', DB::raw("'". $id. "'"));
        })->whereNull('qualification_users.qualification_id')->select('qualifications.*')
            ->where('qualifications.client_id', '=', Auth::user()->currentclient_id)->orderBy('qualifications.name');
    }

    public function hasqualification($qualificationid)
    {
        if($this->belongsToMany(Qualification::class, 'qualification_users')->where('qualification_id', '=', $qualificationid)->count() > 0){
            return true;
        }
        return false;
    }

    public function hascandidate($positionid)
    {
        if($this->hasMany(PositionCandidature::class)->where('positioncandidatures.position_id', '=', $positionid)->count() > 0){
            return true;
        }
        return false;
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
    
    public function authorizedpositions()
    {
        return $this->hasMany(Position::class)->join('services', 'positions.service_id', '=', 'services.id')
            ->where('services.client_id', '=', Auth::user()->currentclient_id)
            ->orderBy('services.date')->with('qualification');
    }

    public function authorizedpositions_future()
    {
        return $this->authorizedpositions()->where('services.date','>=', DB::raw('CURDATE()'));
    }

    public function client_user()
    {
        return $this->hasMany(Client_user::class);
    }

    public function clients()
    {
        return $this->hasManyThrough(
            Client::class,
            Client_user::class,
            'user_id', // Foreign key on Client_user table...
            'id', // Foreign key on clients table...
            'id', // Local key on users table...
            'client_id' // Local key on Client_user table...
        );
    }

    public function currentclient()
    {
        return $this->clients()->where('clients.id', '=', Auth::user()->currentclient_id)->first();
    }

    public function clients_candidature()
    {
        return $this->hasManyThrough(
            Client::class,
            Client_user::class,
            'user_id', // Foreign key on Client_user table...
            'id', // Foreign key on clients table...
            'id', // Local key on users table...
            'client_id' // Local key on Client_user table...
        )->where(['client_user.approved' => false]);
    }

    public function holidaysInFuture()
    {
        return $this->hasMany(Holiday::class)->where('to','>=', DB::raw('CURDATE()'));
    }

    public function services_inHolidayList()
    {
        $servicesList = [];
        foreach ($this->holidaysInFuture()->get() as $holiday)
        {
            $from = $holiday->from->startOfDay();
            $to = $holiday->to->endOfDay();
            $services = Service::where('date','>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->with(['positions.candidatures.user'=> function ($query) {
                    $query->where('id', '=', Auth::user()->id);
                }])
                ->where(function($query) use ($from, $to) {
                    $query->whereBetween('date', [$from,$to])
                        ->orWhereBetween('dateEnd', [$from,$to]);
                })
                ->pluck('id')->toArray();
            $servicesList = array_merge($servicesList, $services);
        }

        return $servicesList;
    }

    public function trainings_inHolidayList()
    {
        $trainingsList = [];
        foreach ($this->holidaysInFuture()->get() as $holiday)
        {
            $from = $holiday->from->startOfDay();
            $to = $holiday->to->endOfDay();
            $trainings = Training::where('date','>=', DB::raw('CURDATE()'))->where('client_id', '=', Auth::user()->currentclient_id)
                ->with(['positions.candidatures.user'=> function ($query) {
                    $query->where('id', '=', Auth::user()->id);
                }])
                ->where(function($query) use ($from, $to) {
                    $query->whereBetween('date', [$from,$to])
                        ->orWhereBetween('dateEnd', [$from,$to]);
                })
                ->pluck('id')->toArray();
            $trainingsList = array_merge($trainingsList, $trainings);
        }

        return $trainingsList;
    }

    public function position_inHolidayList(Position $position)
    {
        //Service
        if(empty($position->training)) {
            return $this->services_inHolidayList();
        }

        //Training
        return $this->trainings_inHolidayList();
    }

    public function services()
    {
        return $this->hasManyThrough(Service::class, Position::class, 'user_id', 'id', 'id', 'service_id');
    }

    //get all SurveyUser of a specific survey
    public function mySurveyUser($surveyId)
    {
        return $this->hasMany(Survey_user::class)->where('survey_id',$surveyId);
    }

    //get all SurveyUser related to this user
    public function mySurveyUsers()
    {
        return $this->hasMany(Survey_user::class);
    }

    //get all open SurveyUser related to this user
    public function myOpenSurveyUsers()
    {
        //get all serveys of clients in Daterange
        $surveys = $this->currentclient()->Surveys()
            ->where(function($query) {
                $query->where('dateStart', '<=', Carbon::now())
                ->orWhereNull('dateStart');
            })
            ->where(function($query) {
                $query->where('dateEnd', '>=', Carbon::now())
                    ->orWhereNull('dateEnd');
            })
            //check already voted surveys
            ->leftJoin('survey_users', function($join) {
                $join->on('survey_users.survey_id', '=', 'surveys.id')
                    ->where('survey_users.user_id', '=', $this->id);
            })
            ->where(function($query) {
                $query->whereNull('survey_users.survey_id')
                    ->orWhere(function($query) {
                        $query->whereNull('survey_users.vote')
                            ->whereDate('survey_users.rememberAt', '<=', Carbon::today());
                    });
            })
            //check qualification
            ->leftJoin('qualification_users', function($join) {
                $join->on('qualification_users.qualification_id', '=', 'surveys.qualification_id')
                    ->where('qualification_users.user_id', '=', $this->id);
            })
            ->where(function($query) {
                $query->whereNull('surveys.qualification_id')
                    ->orWhere(function($query) {
                        $query->whereNotNull('qualification_users.user_id');
                    });
            })
            ->select('surveys.*')
            ->orderBy('surveys.id');

        return $surveys;
    }
}
