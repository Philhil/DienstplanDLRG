<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrainingsToPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->integer('training_id')->unsigned()->nullable();
            $table->foreign('training_id')->references('id')->on('trainings');

            $table->integer('service_id')->unsigned()->nullable()->change();

            $table->text('user_comment');
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
            Schema::dropIfExists('training_id');
            Schema::dropIfExists('user_comment');
        });
    }
}
