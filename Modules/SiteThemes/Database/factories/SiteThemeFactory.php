<?php

$factory->define(Modules\SiteThemes\Entities\SiteTheme::class, function (Faker\Generator $faker) {
    return [
        "title" => $faker->name,
        "slug" => $faker->name,
        "theme_title_key" => $faker->name,
        "settings_data" => $faker->name,
        "description" => $faker->name,
        "is_active" => collect(["1","0",])->random(),
        "theme_color" => $faker->name,
    ];
});
