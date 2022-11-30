<?php

$factory->define(App\Smstemplate::class, function (Faker\Generator $faker) {
    return [
        "title" => $faker->name,
        "key" => $faker->name,
        "content" => $faker->name,
    ];
});
