<?php

/** @var Factory $factory */

use App\Models\ReportMissedCall;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(ReportMissedCall::class, function (Faker $faker) {
    $user = User::all()->random();
    return [
        'id' => $faker->unique()->randomNumber(),
        'type' => $faker->randomElement(['call' ,'team_lead_call', 'user_call']),
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'business_name' => $faker->company,
        'contact' => $faker->text,
        'priority' => $faker->randomElement(['low', 'medium', 'high']),
        'phone' => $faker->numberBetween(10, 13),
        'time_start' => $faker->dateTime(),
        'user_id' => $user->id,
        'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
        'updated_at' => $faker->dateTimeBetween('-30 days', 'now'),
    ];
});
