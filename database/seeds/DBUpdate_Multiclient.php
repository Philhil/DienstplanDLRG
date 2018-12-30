<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class DBUpdate_Multiclient extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //check if no client exist
        if (DB::table('clients')->get()->count() == 0)
        {
            //create client
            $clientid = DB::table('clients')->insertGetId([
                'name' => "Bezirk Stuttgart",
                'seasonStart' => \Carbon\Carbon::parse('2000-01-01'),
                'isMailinglistCommunication' => true,
                'mailinglistAddress' => NULL,
                'mailSenderName' => "DLRG Dienstplan",
                'mailSenderAddress' => "INSERT_SENDERADDR",
                'mailServer' => "INSERT_SERVER",
                'mailPort' => "INSERT_PORT",
                'mailUser' => "INSERT_USR",
                'mailPassword' => "INSERT_PASSWD",
                'defaultServiceStart' => \Carbon\Carbon::createFromTime(9, 15, 00),
                'defaultServiceEnd' => \Carbon\Carbon::createFromTime(19, 15, 00)
            ]);

            //add all users to this client and set currentclientid
            $users = DB::table('users')->get();
            foreach ($users as $user)
            {
                DB::table('users')
                    ->where('id',$user->id)
                    ->update([
                        "currentclient_id" => $clientid
                    ]);

                DB::table('client_user')->insert([
                    'client_id' => $clientid,
                    'user_id' => $user->id,
                    'isAdmin' => $user->role == "admin" ? true : false,
                    'approved' => $user->approved
                ]);
            }

            //add all news to this client
            $newss = DB::table('newss')->get();
            foreach ($newss as $news)
            {
                DB::table('newss')
                    ->where('id',$news->id)
                    ->update([
                        "client_id" => $clientid
                    ]);
            }

            //add all qualifications to this client
            $qualifications = DB::table('qualifications')->get();
            foreach ($qualifications as $qualification)
            {
                DB::table('qualifications')
                    ->where('id',$qualification->id)
                    ->update([
                        "client_id" => $clientid
                    ]);
            }

            //add all services to this client
            $services = DB::table('services')->get();
            foreach ($services as $service)
            {
                DB::table('services')
                    ->where('id',$service->id)
                    ->update([
                        "client_id" => $clientid
                    ]);
            }
        }

    }
}
