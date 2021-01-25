<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableRequestFiltersAddChartFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_filters', function (Blueprint $table) {
            $table->string('s_chart_status')->after('text_status_type')->default('OFFLINE');
            $table->string('s_chart_phone_status')->after('s_chart_status')->default('OFFLINE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_filters', function (Blueprint $table) {
            $table->dropColumn('s_chart_status');
            $table->dropColumn('s_chart_phone_status');
        });
    }
}
