<?php

namespace App\Http\Controllers;

use App\Position;
use Illuminate\Http\Request;
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
        $positions = Position::where([['isauthorized', '=', false],['user_id', '!=', null]])->with('service')->with('user')->with('qualification')->get();
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
            if (Auth::user()->hasqualification($position->qualification()->first()->id) && !$service->hasUserPositions(Auth::user()->id)){
                $position->user_id = Auth::user()->id;
                if ($service->hastoauthorize == false) {
                    $position->isauthorized = true;
                }
                $position->save();
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

        $position = Position::findOrFail($id);
        $position->isauthorized = true;
        $position->save();

        return $position;
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

        $position = Position::findOrFail($id);
        $position->user_id = null;
        $position->save();

        return $position;
    }
}
