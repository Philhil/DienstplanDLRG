<?php

namespace App\Http\Controllers;

use App\Events\PositionAuthorized;
use App\Position;
use App\PositionCandidature;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_notAuthorized()
    {
        $positions = Position::has('candidatures')->where(['user_id' =>  null])->with('qualification')->with('service')->with('candidatures')->with('candidatures.user')->get();

        return view('position.index_notAuthorized', compact('positions'));
    }

    /**
     * Subscripe to a Position.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function subscribe($id)
    {
        $position = Position::findOrFail($id);

        if ($position->user_id == null)
        {
            $service = $position->service()->first();
            if (Auth::user()->hasqualification($position->qualification()->first()->id)){

                if ($service->hastoauthorize == false) {
                    $position->user_id = Auth::user()->id;

                    event(new PositionAuthorized($position, null));
                    $position->save();
                }
                else {
                    \App\PositionCandidature::updateOrCreate(['user_id' => Auth::user()->id, 'position_id' => $position->id]);
                }
                return $position;
            }
        }

        return "false";
    }

    /**
     * Authorize a Position.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function authorizePos($id)
    {
        if(!Auth::user()->can('administration')) {
            abort(402, "Nope.");
        }

        $candidature = PositionCandidature::findOrFail($id);

        $position = Position::findOrFail($candidature->position_id);

        if ($position->user_id == null)
        {
            $service = $position->service()->first();
            if(!$service->hasUserPositions($candidature->user_id))
            {
                $position->user_id = $candidature->user_id;
                $position->save();

                event(new PositionAuthorized($position, Auth::user()));

                return $position;
            }
        }

        return "false";
    }

    /**
     * Remove user of Position.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deauthorizePos($id)
    {
        if(!Auth::user()->can('administration')) {
            abort(402, "Nope.");
        }

        $candidature = PositionCandidature::findOrFail($id);
        $candidature->forceDelete();

        return $candidature;
    }
}
