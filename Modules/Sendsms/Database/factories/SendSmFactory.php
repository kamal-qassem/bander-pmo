<?php

$factory->define(Modules\Sendsms\Entities\SendSm::class, function (Faker\Generator $faker) {
    return [
        "send_to" => $faker->name,
        "message" => $faker->name,
        "gateway_id" => factory('Modules\Sendsms\Entities\SmsGateway')->create(),
    ];
});
