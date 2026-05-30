<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereNull('ical_token')->orderBy('id')->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update(['ical_token' => Str::uuid()]);
        });
    }

    public function down(): void
    {
        // intentionally left blank — tokens are not removed on rollback
    }
};
