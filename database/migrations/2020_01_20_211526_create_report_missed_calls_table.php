<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportMissedCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_missed_calls', function (Blueprint $table) {
            $table->string('id', 128)->unique();
            $table->string('type', 30);
            $table->string('first_name', 20);
            $table->string('last_name', 20);
            $table->string('business_name', 200);
            $table->string('contact', 200);
            $table->string('priority', 15);
            $table->string('phone', 20);
            $table->dateTime('time_start')->index();
            $table->BigInteger('user_id');
            $table->timestamps();

            // user_id will reference to table users in future
            $table->index('business_name');
            $table->index(['contact', 'business_name']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_missed_calls');
    }
}
