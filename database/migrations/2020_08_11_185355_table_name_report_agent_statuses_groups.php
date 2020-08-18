<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableNameReportAgentStatusesGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_statuses_groups', function (Blueprint $table) {
            $table->rename('report_agent_statuses_groups')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_agent_statuses_groups', function (Blueprint $table) {
            $table->rename('agent_statuses_groups')->change();
        });
    }
}
