<?php

$factory->define(Modules\Quotes\Entities\Quote::class, function (Faker\Generator $faker) {
      $data = [
        'title' => $faker->word,
        'address' => $faker->address,
        'invoice_prefix' => $faker->iban('QUO'),
        'show_quantity_as' => $faker->randomFloat(2,1),
        'invoice_no' => $faker->numberBetween(1,10000 ),
        'status' => $faker->randomElement(['Published', 'Draft']),
        'reference' => $faker->word,
        'invoice_date' => $faker->date('Y-m-d'),
        'invoice_due_date' =>$faker->date('Y-m-d'),
        'invoice_notes' =>$faker->text(200),
        'amount' => $faker->randomFloat(2,1),
        'customer_id' => function () {
                    return App\Contact::inRandomOrder()->whereHas("contact_type",
                    function ($query) {
                    $query->where('id', CUSTOMERS_TYPE);
                    })->first()->id;
        },
        'currency_id' => function () {
            return App\Currency::inRandomOrder()->first()->id;
        },
        'tax_id' => function () {
                  return App\Tax::inRandomOrder()->first()->id;
        },
        'discount_id' => function () {
            return App\Discount::inRandomOrder()->first()->id;
        },
        'products' => $faker->name,
        'slug' => $faker->slug,
        'delivery_address' => $faker->address,
        'admin_notes' => $faker->text(200),
        'sale_agent' => function () {
                    return App\Contact::inRandomOrder()->whereHas("contact_type",
                    function ($query) {
                    $query->where('id', CONTACT_SALE_AGENT);
                    })->first()->id;
        },
        'terms_conditions' => $faker->text(200),
       
    ];
});
