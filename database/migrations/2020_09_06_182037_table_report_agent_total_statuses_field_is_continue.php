<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableReportAgentTotalStatusesFieldIsContinue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_agent_total_statuses', function (Blueprint $table) {
            $table->boolean('is_continue')->after('duration')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_agent_total_statuses', function (Blueprint $table) {
            $table->dropColumn('is_continue');
        });
    }
}
