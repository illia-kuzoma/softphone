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
        Schema::create('agent_statuses_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id');
            $table->string('status_name', 100);
            $table->string('status_value', 100);
            $table->timestamp('start_at', 0)->comment("Time enter in status.");
            $table->timestamps();
            $table->unique(['agent_id', 'status_name', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_statuses_groups');
    }
}
