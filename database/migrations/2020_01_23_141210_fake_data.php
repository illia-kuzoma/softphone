<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FakeData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')->insert(
            array(
                'id' => '100',
                'email' => "asd2a@sd.tt",
                'first_name' => "asd2a@sd.tt",
                'last_name' => "asd2a@sd.tt",
                'password' => "asd2a@sd.tt",
                'role' => "agent",
                'token' => "asd2a@sd.tt",
                'photo' => "asd2a@sd.tt",
                'date_login' => date("Y-m-d H:i:s")
            )
        );
        DB::table('report_missed_graphs')->insert(
            [
                array(
                    'id' => '1',
                    'first_name' => "asd2a@sd.tt",
                    'last_name' => "asd2a@sd.tt",
                    'count' => 33,
                    'order' => 1,
                    'user_id' => "23",
                    'day' => date("Y-m-d")
                ),
                array(
                    'id' => '2',
                    'first_name' => "SSss",
                    'last_name' => "Asd",
                    'count' => 3,
                    'order' => 2,
                    'user_id' => "231",
                    'day' => date("Y-m-d")
                )
            ]
        );
        DB::table('report_missed_calls')->insert(
            [
                array(
                    'id' => '1',
                    'type' => 'call',
                    'first_name' => "asd2a@sd.tt",
                    'last_name' => "asd2a@sd.tt",
                    'business_name' => 'SPA 1',
                    'contact' => 'contact 2',
                    'priority' => 'low',
                    'phone' => '1231243123',
                    'time_start' => date("Y-m-d H:i:s"),
                    'user_id' => '33'
                ),
                array(
                    'id' => '2',
                    'type' => 'call',
                    'first_name' => "SSss",
                    'last_name' => "Asd",
                    'business_name' => 'business_name 1',
                    'contact' => 'contact 1',
                    'priority' => 'medium',
                    'phone' => '123123123',
                    'time_start' => date("Y-m-d H:i:s"),
                    'user_id' => 23
                )
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->delete(100);
        DB::table('report_missed_graphs')->delete(1);
        DB::table('report_missed_graphs')->delete(2);
        DB::table('report_missed_calls')->delete(1);
        DB::table('report_missed_calls')->delete(2);
    }
}
