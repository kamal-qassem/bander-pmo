<?php

$factory->define(App\InvoicePayment::class, function (Faker\Generator $faker) {
    return [
        "invoice_id" => factory('App\Invoice')->create(),
        "date" => $faker->date("d-m-Y", $max = 'now'),
        "account_id" => factory('App\Account')->create(),
        "amount" => $faker->randomNumber(2),
        "transaction_id" => $faker->name,
    ];
});
