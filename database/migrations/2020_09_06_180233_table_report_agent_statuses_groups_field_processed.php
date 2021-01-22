<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableReportAgentStatusesGroupsFieldProcessed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_agent_statuses_groups', function (Blueprint $table) {
            $table->boolean('is_processed')->after('time_end')->default(false);
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
            $table->dropColumn('is_processed');
        });
    }
}
