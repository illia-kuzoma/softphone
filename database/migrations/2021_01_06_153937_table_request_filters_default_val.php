<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableRequestFiltersDefaultVal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_filters', function (Blueprint $table) {
            $table->string('s_chart_status')->default('')->change();
            $table->string('s_chart_phone_status')->default('')->change();
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
            $table->string('s_chart_status')->default('OFFLINE')->change();
            $table->string('s_chart_phone_status')->default('OFFLINE')->change();
        });
    }
}
