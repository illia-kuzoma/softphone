<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
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
        'email' => $faker->unique()->safeEmail,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'role'  => $faker->randomElement(['agent', 'team_lead', 'user']),
        'token' => $faker->regexify('[A-Za-z0-9]{10}'),
        'photo' => $faker->image('public/storage/images',640,480, null, false),
        'date_login' => $faker->dateTime,
        'created_at' => $faker->dateTimeBetween('-30 days', '-1 days'),
        'updated_at' => $faker->dateTimeBetween('-30 days', '-1 days'),
    ];
});
