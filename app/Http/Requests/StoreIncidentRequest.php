<?php

namespace App\Http\Requests;

use App\Incident;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // creating needs editor rights, updating is checked per incident in the controller
        if ($this->isMethod('post')) {
            return Incident::canCreate(Auth::user(), Auth::user()->currentclient_id);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in(array_keys(Incident::TYPES))],
            'title' => 'required|max:255',
            'keyword' => 'nullable|max:255',
            'location' => 'nullable|max:1000',
            'dispatch_number' => 'nullable|max:255',
            'start' => 'required|date_format:d m Y H:i',
            'end' => 'nullable|date_format:d m Y H:i|after_or_equal:start',
            'note' => 'nullable|max:5000',
            'leader_user_id' => 'nullable|integer',
        ];
    }

    /**
     * The leader has to be an approved member of the current client.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $leader = $this->input('leader_user_id');
            if (!empty($leader)) {
                $user = \App\User::find($leader);
                if (is_null($user) || !$user->isApprovedMemberOfClient(Auth::user()->currentclient_id)) {
                    $validator->errors()->add('leader_user_id', 'Der Einsatzleiter muss Mitglied dieser Gliederung sein.');
                }
            }
        });
    }
}
