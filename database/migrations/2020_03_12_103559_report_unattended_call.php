<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportUnattendedCall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_unattended_call', function (Blueprint $table) {
            $table->string('id', 128)->unique();
            $table->string('type', 30);
            $table->string('business_name', 200);
            $table->string('contact', 200);
            $table->string('priority', 15);
            $table->string('phone', 20);
            $table->dateTime('time_start')->index();
            $table->BigInteger('user_id');
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
        Schema::table('report_missed_calls', function (Blueprint $table) {
            //
        });
    }
}
