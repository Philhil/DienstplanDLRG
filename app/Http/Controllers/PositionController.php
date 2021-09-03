<?php

namespace App\Http\Controllers;

use App\Client;
use App\Events\NewPositioncandidature;
use App\Events\PositionAuthorized;
use App\Position;
use App\PositionCandidature;
use App\Training_user;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_notAuthorized()
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $positions = Position::has('candidatures')->join('services', 'services.id', '=', 'service_id')
            ->where('services.date','>=', DB::raw('CURDATE()'))
            ->where(['user_id' =>  null, 'services.client_id' => Auth::user()->currentclient_id])
            ->orderby('service_id')->with('qualification')->with('service')
            ->with('candidatures')->with('candidatures.user')->select('positions.*')->get();
        return view('position.index_notAuthorized', compact('positions'));
    }

    /**
     * Subscripe a user to a Position. Only for Admin or TrainingEditor
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function subscribe_user($position_id, $user_id)
    {
        $position = Position::findOrFail($position_id);

        //is admin or is training and trainingeditor
        if(Auth::user()->can('administration') || (!empty($position->training) && Auth::user()->can('trainingeditor'))) {

            return $this->subscribe_implementation($position, $user_id) == "false" ? "false" : $user_id;
        }

        abort(402, "Nope.");
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
        $user_id = Auth::user()->id;
        return $this->subscribe_implementation($position, $user_id);
    }
    /**
     * Subscripe to a Position. Implementation for Subscripe. Do not call from outside! Use other functions with auth check
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function subscribe_implementation($position, $user_id)
    {
        $user = User::FindOrFail($user_id);

        //is user at same client like position?
        if ($user->clients()->pluck('client_id')->contains($position->getClientId())) {
            //Training
            if (!empty($position->training_id)) {
                $training = $position->training()->first();

                //(user have to have the qualification and not a already assigned position) or Subscribe request is from a trainingeditor || admin
                if (($user->hasqualification($position->qualification()->first()->id) && !$position->training->hasUserPositions($user_id)) || Auth::user()->can('trainingeditor') || Auth::user()->can('administration')) {
                    Training_user::create(['training_id' => $training->id, 'user_id' => $user_id , 'position_id' => $position->id]);
                    return $position;
                }
            }

            //Service
            if (!empty($position->service_id) && $position->user_id == null)
            {
                $service = $position->service()->first();
                //(user have to have the qualification and not a already assigned position ) or Subscribe request is from a admin
                if (($user->hasqualification($position->qualification()->first()->id) && !$position->service->hasUserPositions($user_id))|| Auth::user()->can('administration')) {
                    if ($service->hastoauthorize == false) {
                        $position->user_id = $user_id;
                        event(new PositionAuthorized($position, null));
                        $position->save();
                    }
                    else {
                        //check if user is already Candidate
                        if (PositionCandidature::where(['user_id' => $user_id, 'position_id' => $position->id])->count() == 0) {
                            $positionCandidature = \App\PositionCandidature::Create(['user_id' => $user_id, 'position_id' => $position->id]);
                            event(new NewPositioncandidature($positionCandidature, Auth::user()->currentclient()));
                        } else {
                            return "false";
                        }
                    }
                    return $position;
                }
            }

            return "false";
        }
        abort(402, "Nope.");
    }

    /**
     * Unubscripe a user of a Position. Only for Admin or TrainingEditor
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe_user($position_id, $user_id)
    {
        $position = Position::findOrFail($position_id);

        //is admin or is training and trainingeditor
        if(Auth::user()->can('administration') || (!empty($position->training) && Auth::user()->can('trainingeditor'))) {

            Session::flash('returnUserId', 'true');
            return $this->unsubscribe_implementation($position, $user_id);
        }

        abort(402, "Nope.");
    }

    /**
     * Unubscripe a Position.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe($id)
    {
        $position = Position::findOrFail($id);
        return $this->unsubscribe_implementation($position, Auth::user()->id);
    }

    /**
     * Unubscripe a Position. Implementation for unsubscribe. Do not call from outside! Use other functions with auth check
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    private function unsubscribe_implementation($position, $user_id)
    {
        //is user at same client lice position?
        if (User::FindOrFail($user_id)->clients()->pluck('client_id')->contains($position->getClientId()))
        {
            //Training
            if (!empty($position->training_id)) {
                $training_user = Training_user::where(['position_id'=> $position->id, 'user_id' => $user_id])->select('id')->first();
                if (!empty($training_user)) {
                    return redirect()->action('TrainingController@delete_training_user', ['training_userid' => $training_user->id])->with('redirect', 'positionController@unsubscribe');
                }

                return "false";
            }
            //Service
            PositionCandidature::where(['position_id' => $position->id, 'user_id' =>  Auth::user()->id])->forceDelete();

            return $position->id;
        }
        abort(402, "Nope.");
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
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $candidature = PositionCandidature::findOrFail($id);
        $candidature->forceDelete();

        return $candidature;
    }

    /**
     * Display a List of users to assign to Position (positon or training_users).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function position_user($id)
    {
        if(!Auth::user()->currentclient()->module_training) {
            abort(402, "Module not activated.");
        }

        $position = Position::findOrFail($id);

        if((Auth::user()->currentclient_id != $position->getClientId()) ||
            !Auth::user()->can('administration') && !Auth::user()->can('trainingeditor')) {
            abort(402, "Nope.");
        }

        $comment = !empty($position->training) ? $position->training->title : $position->service->comment;
        $users = Client::FindOrFail($position->getClientId())->user()->with('qualifications')->get();

        //All users already assigned to the position
        $selected_users = collect();
        if (!empty($position->user_id)) $selected_users->push($position->user_id);
        if (!empty($position->training)) $selected_users = $selected_users->merge(Training_user::where(["position_id" => $position->id])->pluck('user_id'));

        //HOLIDAY //Service or Training
        $parent = empty($position->training) ? $position->service : $position->training;

        return view('position.position_user', compact('position', 'users', 'comment', 'selected_users', 'parent'));
    }
}
