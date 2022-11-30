<?php

$factory->define(Modules\RecurringPeriods\Entities\RecurringPeriod::class, function (Faker\Generator $faker) {
    return [
        "title" => $faker->name,
        "value" => $faker->name,
        "description" => $faker->name,
    ];
});
