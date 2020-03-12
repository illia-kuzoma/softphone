<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportUnattendedGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_unattended_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('count')->default(0);
            $table->integer('order')->default(0);
            $table->BigInteger('user_id');
            $table->dateTime('day');
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
        Schema::dropIfExists('report_unattended_group');
    }
}
