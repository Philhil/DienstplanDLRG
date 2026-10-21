<?php

namespace Tests\Feature\Concerns;

use App\Client;
use App\Client_user;
use App\Incident;
use App\Incident_attendance;
use App\Qualification;
use App\Qualification_user;
use App\Station;
use App\User;
use Carbon\Carbon;

/**
 * Fixtures for the incident tests. Everything is created directly (no demo client), so the
 * tests do not depend on other modules.
 */
trait CreatesIncidentFixtures
{
    protected function makeClient($name = 'Testgliederung', array $attributes = [])
    {
        return Client::create($attributes + [
            'name' => $name,
            'seasonStart' => '2000-01-01',
            'isMailinglistCommunication' => false,
            'weeklyServiceviewEmail' => false,
            'mailSenderName' => 'Dienstplan',
            'mailReplyAddress' => 'dienstplan@example.com',
            'module_incident' => true,
            'module_statistic' => true,
        ]);
    }

    /**
     * @param array $roles isAdmin, isIncidentEditor, isStatisticEditor, approved (default true)
     */
    protected function makeMember(Client $client, array $roles = [], array $userAttributes = [])
    {
        $user = User::factory()->create($userAttributes + ['currentclient_id' => $client->id]);

        $clientUser = Client_user::create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'isAdmin' => $roles['isAdmin'] ?? false,
            'isIncidentEditor' => $roles['isIncidentEditor'] ?? false,
        ]);
        $clientUser->approved = $roles['approved'] ?? true;
        $clientUser->isStatisticEditor = $roles['isStatisticEditor'] ?? false;
        $clientUser->save();

        return $user;
    }

    /** Member with a qualification that may record the attendance of others (Führungskraft). */
    protected function makeLeaderQualified(Client $client)
    {
        $user = $this->makeMember($client);
        $qualification = new Qualification([
            'name' => 'Gruppenführer', 'short' => 'GF', 'isservicedefault' => false, 'defaultcount' => 0,
            'defaultrequiredasposition' => false, 'can_record_incident' => true,
        ]);
        $qualification->client_id = $client->id;
        $qualification->save();
        Qualification_user::create(['qualification_id' => $qualification->id, 'user_id' => $user->id]);

        return $user;
    }

    protected function makeIncident(Client $client, array $attributes = [])
    {
        return Incident::create($attributes + [
            'client_id' => $client->id,
            'type' => Incident::TYPE_INCIDENT,
            'title' => 'Wassernotfall',
            'start' => Carbon::now()->subHours(2),
        ]);
    }

    protected function makeFinalizedIncident(Client $client, array $attributes = [])
    {
        $incident = $this->makeIncident($client, $attributes + [
            'start' => Carbon::now()->subHours(5)->startOfMinute(),
            'end' => Carbon::now()->subHours(2)->startOfMinute(),
        ]);
        $incident->finalized_at = Carbon::now()->subHour();
        $incident->save();

        return $incident;
    }

    protected function makeAttendance(Incident $incident, $user = null, array $attributes = [])
    {
        return Incident_attendance::create($attributes + [
            'incident_id' => $incident->id,
            'user_id' => $user ? $user->id : null,
            'guest_name' => $user ? null : 'Gast',
            'status' => Incident_attendance::STATUS_PRESENT,
            'reported_at' => Carbon::now(),
            'source' => Incident_attendance::SOURCE_SELF,
            'confirmed_at' => Carbon::now(),
        ]);
    }

    protected function makeStation(Client $client, $name = 'Wache Mitte')
    {
        return Station::create(['client_id' => $client->id, 'name' => $name]);
    }
}
