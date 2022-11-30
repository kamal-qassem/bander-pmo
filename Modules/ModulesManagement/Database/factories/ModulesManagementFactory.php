<?php

$factory->define(Modules\ModulesManagement\Entities\ModulesManagement::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "slug" => $faker->name,
        "type" => collect(["Core","Custom",])->random(),
        "enabled" => collect(["Yes","No",])->random(),
        "description" => $faker->name,
    ];
});
