<?php

namespace App\Http\Requests;

use App\Incident_attendance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentAttendanceRequest extends FormRequest
{
    /**
     * Permissions are checked per incident in the controller.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'status' => ['required', Rule::in(array_keys(Incident_attendance::STATUSES))],
            'user_id' => 'nullable|integer',
            'guest_name' => 'nullable|string|max:255',
            'station_id' => 'nullable|integer',
        ];
    }

    /**
     * Exactly one of user_id (member) and guest_name (helper without account).
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // only when recording a helper; reporting himself and updating do not change who it is
            if ($this->routeIs('incident.attendance.store')) {
                $hasUser = !empty($this->input('user_id'));
                $hasGuest = trim((string) $this->input('guest_name')) !== '';

                if ($hasUser == $hasGuest) {
                    $validator->errors()->add('user_id', 'Bitte entweder ein Mitglied oder einen Namen (ohne Account) angeben.');
                }
            }
        });
    }
}
