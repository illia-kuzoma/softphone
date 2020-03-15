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
            $table->unsignedBigInteger('agent_id');
            $table->dateTime('day');
            $table->integer('count')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['day', 'agent_id']);
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
