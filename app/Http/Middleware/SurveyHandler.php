<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SurveyHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //If user has a open survey
        $mySurvey_users = Auth::user()->myOpenSurveyUsers()->first();

        if(!empty($mySurvey_users))
        {
            Session::flash('survey', $mySurvey_users->id);
            Session::flash('survey_title', $mySurvey_users->title);
        }

        return $next($request);
    }
}
