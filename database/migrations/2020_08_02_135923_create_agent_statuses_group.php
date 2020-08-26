<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentStatusesGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_agent_statuses_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id');
            $table->string('status_name', 100);
            $table->string('status_value', 100);
            $table->timestamp('time_start', 0)->nullable()->comment("Time enter in status.");
            $table->timestamp('time_end', 0)->nullable()->comment("Time leave out status.");
            $table->timestamps();
            $table->unique(['agent_id', 'status_name', 'time_start'], 'report_agent_statuses_groups__uniq_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_agent_statuses_groups');
    }
}
