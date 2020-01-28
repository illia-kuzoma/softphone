<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 32)->unique();
            $table->string('first_name', 20);
            $table->string('last_name', 20);
            $table->string('password', 32);
            $table->enum('role', ['agent', 'team_lead', 'user']);
            $table->string('token', 256);
            $table->string('photo', 256)->nullable();
            $table->dateTime('date_login');
            $table->timestamps();

            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
