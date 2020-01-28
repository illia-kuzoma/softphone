<?php

/** @var Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'first_name' => $faker->firstNameMale,
        'last_name' => $faker->lastName,
        'password' => $faker->password,
        'role'  => $faker->randomElement(['agent', 'team_lead', 'user']),
        'token' => Str::random(10),
        'photo' => $faker->image('public/storage/images',640,480, null, false),
        'date_login' => $faker->dateTime(),
        'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
        'updated_at' => $faker->dateTimeBetween('-30 days', 'now'),
    ];
});
