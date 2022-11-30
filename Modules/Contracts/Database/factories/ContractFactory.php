<?php

$factory->define(Modules\Contracts\Entities\Contract::class, function (Faker\Generator $faker) {
    return [
        "customer_id" => factory('App\Contact')->create(),
        "currency_id" => factory('App\Currency')->create(),
        "title" => $faker->name,
        "address" => $faker->name,
        "invoice_prefix" => $faker->name,
        "show_quantity_as" => $faker->name,
        "invoice_no" => $faker->name,
        "status" => collect(["Published","Draft",])->random(),
        "reference" => $faker->name,
        "invoice_date" => $faker->date("d-m-Y", $max = 'now'),
        "invoice_due_date" => $faker->date("d-m-Y", $max = 'now'),
        "invoice_notes" => $faker->name,
        "tax_id" => factory('App\Tax')->create(),
        "discount_id" => factory('App\Discount')->create(),
        "recurring_period_id" => factory('odules\RecurringPeriods\Entities\RecurringPeriod')->create(),
        "amount" => $faker->randomNumber(2),
        "products" => $faker->name,
        "paymentstatus" => collect(["Unpaid","Paid","Partial","Cancelled","Due",])->random(),
    ];
});
