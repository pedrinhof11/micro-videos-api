<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    return [
        "title" => $faker->realText(40),
        "description" => $faker->sentence(10),
        "year_launched" => $faker->numberBetween(1990, 2020),
        "opened" => $faker->boolean,
        "rating" => $faker->randomElement(Video::RATING_LIST),
        "duration" => $faker->numberBetween(1, 30)
    ];
});
