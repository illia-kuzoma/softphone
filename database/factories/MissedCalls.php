<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReportMissedCall;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(ReportMissedCall::class, function (Faker $faker) {
    $user = User::all()->random();
    return [
        'type' => $faker->randomElement(['call', 'team_lead_call', 'user_call']),
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'business_name' => $faker->company,
        'contact' => $faker->text,
        'priority' => $faker->randomElement(['low', 'medium', 'high']),
        'phone' => $faker->phoneNumber,
        'time_start' => $faker->dateTime,
        'user_id' => $user->user_id,
        'call_id' => $faker->randomNumber(),
        'created_at' => $faker->dateTimeBetween('-30 days', '-1 days'),
        'updated_at' => $faker->dateTimeBetween('-30 days', '-1 days'),
    ];
});
