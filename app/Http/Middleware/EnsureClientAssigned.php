<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Support\Facades\Response;

class EnsureClientAssigned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //If user has a currentclient with not assignment anymore or no client assignment at all
        if(empty(Auth::user()->currentclient()))
        {
            if (empty($client)) {
                $user = Auth::user();
                $client = $user->clients()->first();

                if (empty($client)) {
                    $data = array('message', "Kein Client zugeordnet");
                    return Response::view('errors.noclient', $data, 404);
                }

                $user->currentclient_id = $client->id;
                $user->save();
            }
        }

        return $next($request);
    }
}
