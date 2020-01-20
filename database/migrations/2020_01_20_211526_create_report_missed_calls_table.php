<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportMissedCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_missed_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['call']);
            $table->string('first_name', 20);
            $table->string('last_name', 20);
            $table->string('business_name', 200);
            $table->string('contact', 200);
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->string('phone', 13);
            $table->dateTime('time_start');
            $table->string('user_id', 32);
            $table->string('call_id', 128);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_missed_calls');
    }
}
