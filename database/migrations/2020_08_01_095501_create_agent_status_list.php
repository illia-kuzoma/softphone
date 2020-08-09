<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentStatusList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id');
            $table->string('chat_status', 100)->default('');
            $table->string('phone_mode', 100)->default('');
            $table->string('phone_status', 100)->default('');
            $table->string('mail_status', 100)->default('');
            $table->string('presence_status', 100)->default('');
            $table->string('status', 100)->default('');
            $table->timestamp('request_at', 0);
            $table->timestamp('created_at', 0);
            $table->unique(['agent_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_statuses');
    }
}
