<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePositionsOnDeleteUserReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // SQLite does not support dropping foreign keys by name; skip on SQLite (test env)
        if (config('database.default') !== 'sqlite') {
            Schema::table('positions', function (Blueprint $table) {
                $table->dropForeign('positions_user_id_foreign');
            });
        }

        Schema::table('positions', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            //
        });
    }
}
