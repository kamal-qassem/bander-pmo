<?php

$factory->define(Modules\DatabaseBackup\Entities\DatabaseBackup::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "storage_location" => $faker->name,
    ];
});
