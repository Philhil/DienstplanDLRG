<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTimestampToDateTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dateTime('date')->change();
            $table->dateTime('dateEnd')->nullable()->default(null)->change();
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dateTime('date')->change();
            $table->dateTime('dateEnd')->nullable()->default(null)->change();
            $table->dateTime('sendbydatetime')->nullable()->default(null)->change();
            $table->dateTime('sended')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
