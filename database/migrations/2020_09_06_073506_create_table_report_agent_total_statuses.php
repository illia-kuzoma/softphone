<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReportAgentTotalStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_agent_total_statuses', function (Blueprint $table) {
            $table->date('day');
            $table->unsignedBigInteger('agent_id');
            $table->string('name', 100);
            $table->string('value', 100);
            $table->integer('duration', 0)->comment('Duration being in status in seconds');
            $table->timestamps();
            $table->unique(['day', 'agent_id', 'name', 'value'], 'report_agent_statuses_total__uniq_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_agent_total_statuses');
    }
}
