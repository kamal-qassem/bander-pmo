<?php

namespace Modules\Orders\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class OrderTest extends DuskTestCase
{

    public function testCreateOrder()
    {
        $admin = factory('App\User', 'admin')->create();
        $order = factory('Modules\Orders\Entities\Order')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $order) {
            $browser->loginAs($admin)
                ->visit(route('admin.orders.index'))
                ->clickLink('Add new')
                ->select("customer_id", $order->customer_id)
                ->select("status", $order->status)
                ->select("billing_cycle_id", $order->billing_cycle_id)
                ->press('Save')
                ->assertRouteIs('admin.orders.index')
                ->assertSeeIn("tr:last-child td[field-key='customer']", $order->customer->first_name)
                ->assertSeeIn("tr:last-child td[field-key='status']", $order->status)
                ->assertSeeIn("tr:last-child td[field-key='billing_cycle']", $order->billing_cycle->title)
                ->logout();
        });
    }

    public function testEditOrder()
    {
        $admin = factory('App\User', 'admin')->create();
        $order = factory('Modules\Orders\Entities\Order')->create();
        $order2 = factory('Modules\Orders\Entities\Order')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $order, $order2) {
            $browser->loginAs($admin)
                ->visit(route('admin.orders.index'))
                ->click('tr[data-entry-id="' . $order->id . '"] .btn-info')
                ->select("customer_id", $order2->customer_id)
                ->select("status", $order2->status)
                ->select("billing_cycle_id", $order2->billing_cycle_id)
                ->press('Update')
                ->assertRouteIs('admin.orders.index')
                ->assertSeeIn("tr:last-child td[field-key='customer']", $order2->customer->first_name)
                ->assertSeeIn("tr:last-child td[field-key='status']", $order2->status)
                ->assertSeeIn("tr:last-child td[field-key='billing_cycle']", $order2->billing_cycle->title)
                ->logout();
        });
    }

    public function testShowOrder()
    {
        $admin = factory('App\User', 'admin')->create();
        $order = factory('Modules\Orders\Entities\Order')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $order) {
            $browser->loginAs($admin)
                ->visit(route('admin.orders.index'))
                ->click('tr[data-entry-id="' . $order->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='customer']", $order->customer->first_name)
                ->assertSeeIn("td[field-key='status']", $order->status)
                ->assertSeeIn("td[field-key='price']", $order->price)
                ->assertSeeIn("td[field-key='billing_cycle']", $order->billing_cycle->title)
                ->logout();
        });
    }

}
