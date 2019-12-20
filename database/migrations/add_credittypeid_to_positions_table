<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCredittypeidToPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->integer('credittype_id')->unsigned()->nullable(); //->nullable() for upgrade on exsisting DB
            $table->foreign('credittype_id')->references('id')->on('credittypes')->onDelete('cascade');


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
