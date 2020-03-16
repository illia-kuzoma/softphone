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
            $table->unsignedBigInteger('id')->index();
            $table->unsignedBigInteger('agent_id')->index();
            $table->unsignedBigInteger('user_id');
            $table->string('business_name', 200);
            $table->string('contact', 200);
            $table->string('priority', 15);
            $table->string('phone', 20);
            $table->dateTime('time_start')->index();
            $table->timestamps();
            $table->unique(['agent_id', 'id', 'user_id']); // User key + Call key. Call key can be one for two agents (users).
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_unattended_call', function (Blueprint $table) {
            //
        });
    }
}
