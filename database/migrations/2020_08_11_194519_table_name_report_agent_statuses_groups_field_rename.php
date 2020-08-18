<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableNameReportAgentStatusesGroupsFieldRename extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_agent_statuses_groups', function (Blueprint $table) {
            $table->renameColumn('start_at', 'time_start')->change();
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
            $table->renameColumn('time_start', 'start_at')->change();
        });
    }
}
