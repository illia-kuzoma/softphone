<?php

/** @var Factory $factory */

use App\Models\ReportMissedGraph;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(ReportMissedGraph::class, function (Faker $faker) {
    $user = User::all()->random();
    return [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'count' => $faker->randomNumber(),
        'order' => $faker->randomNumber(),
        'user_id' => $user->id,
        'day' => $faker->dateTime(),
        'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
        'updated_at' => $faker->dateTimeBetween('-30 days', 'now'),
    ];
});
