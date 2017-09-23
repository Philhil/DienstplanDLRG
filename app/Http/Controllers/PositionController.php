<?php

namespace App\Http\Controllers;

use App\Events\NewPositioncandidature;
use App\Events\PositionAuthorized;
use App\Position;
use App\PositionCandidature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_notAuthorized()
    {
        $positions = Position::has('candidatures')->join('services', 'services.id', '=', 'service_id')->where('services.date','>=', DB::raw('CURDATE()'))
            ->where(['user_id' =>  null])->orderby('service_id')->with('qualification')->with('service')->with('candidatures')->with('candidatures.user')->select('positions.*')->get();
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
                    //has user already approved position in this service?
                    if(!$position->service->hasUserPositions(Auth::user()->id)) {
                        $position->user_id = Auth::user()->id;

                        event(new PositionAuthorized($position, null));
                        $position->save();
                    } else {
                        return "false";
                    }
                }
                else {
                    $positionCandidature = \App\PositionCandidature::Create(['user_id' => Auth::user()->id, 'position_id' => $position->id]);
                    event(new NewPositioncandidature($positionCandidature));
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

                //Delete all other candidates of this Position
                PositionCandidature::where('position_id', '=', $position->id)->forceDelete();
                //Delete all other candidates of this user in the same service
                foreach (Position::where('service_id', '=', $service->id)->get() as $pos)
                {
                    $pos->candidaturesOfUser($candidature->user_id)->forceDelete();
                }

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
