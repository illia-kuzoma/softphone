<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestFilterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('text_name', 32)->unique();
            $table->text('text_functionality')->comment('missed calls, statuses, ...');
            $table->date('day')->nullable();
            $table->string('text_period', 10)->comment('today, day, week, month, year')->nullable();
            $table->text('text_department_id')->comment('department_id list separated by comma')->nullable();
            $table->text('text_team_id')->comment('team_id list separated by comma')->nullable();
            $table->text('text_user_id')->comment('agent_id list separated by comma')->nullable();
            $table->text('text_status_type')->nullable();
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
        Schema::dropIfExists('request_filters');
    }
}
