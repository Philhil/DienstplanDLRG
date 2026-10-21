<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * user_id, station_id and recorded_by are plain ids without foreign key on purpose
     * (see CreateIncidentsTable): the attendance list must survive deleted users/stations.
     * Only the incident itself cascades, because deleting an incident is a manual action.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('incident_id')->unsigned();
            $table->foreign('incident_id')->references('id')->on('incidents')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable()->default(null)->index();
            $table->string('guest_name')->nullable()->default(null);
            $table->integer('station_id')->unsigned()->nullable()->default(null);
            $table->string('status')->default('present');
            $table->dateTime('reported_at');
            $table->string('source')->default('self');
            $table->integer('recorded_by')->unsigned()->nullable()->default(null);
            $table->dateTime('confirmed_at')->nullable()->default(null);
            $table->timestamps();

            $table->unique(['incident_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_attendances');
    }
}
