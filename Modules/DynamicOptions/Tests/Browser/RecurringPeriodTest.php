<?php

namespace Modules\RecurringPeriods\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\RecurringPeriods\Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class RecurringPeriodTest extends DuskTestCase
{

    public function testCreateRecurringPeriod()
    {
        $admin = factory('App\User', 'admin')->create();
        $recurring_period = factory('App\RecurringPeriod')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $recurring_period) {
            $browser->loginAs($admin)
                ->visit(route('admin.recurring_periods.index'))
                ->clickLink('Add new')
                ->type("title", $recurring_period->title)
                ->type("value", $recurring_period->value)
                ->type("description", $recurring_period->description)
                ->press('Save')
                ->assertRouteIs('admin.recurring_periods.index')
                ->assertSeeIn("tr:last-child td[field-key='title']", $recurring_period->title)
                ->assertSeeIn("tr:last-child td[field-key='value']", $recurring_period->value)
                ->logout();
        });
    }

    public function testEditRecurringPeriod()
    {
        $admin = factory('App\User', 'admin')->create();
        $recurring_period = factory('App\RecurringPeriod')->create();
        $recurring_period2 = factory('App\RecurringPeriod')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $recurring_period, $recurring_period2) {
            $browser->loginAs($admin)
                ->visit(route('admin.recurring_periods.index'))
                ->click('tr[data-entry-id="' . $recurring_period->id . '"] .btn-info')
                ->type("title", $recurring_period2->title)
                ->type("value", $recurring_period2->value)
                ->type("description", $recurring_period2->description)
                ->press('Update')
                ->assertRouteIs('admin.recurring_periods.index')
                ->assertSeeIn("tr:last-child td[field-key='title']", $recurring_period2->title)
                ->assertSeeIn("tr:last-child td[field-key='value']", $recurring_period2->value)
                ->logout();
        });
    }

    public function testShowRecurringPeriod()
    {
        $admin = factory('App\User', 'admin')->create();
        $recurring_period = factory('App\RecurringPeriod')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $recurring_period) {
            $browser->loginAs($admin)
                ->visit(route('admin.recurring_periods.index'))
                ->click('tr[data-entry-id="' . $recurring_period->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='title']", $recurring_period->title)
                ->assertSeeIn("td[field-key='value']", $recurring_period->value)
                ->assertSeeIn("td[field-key='description']", $recurring_period->description)
                ->logout();
        });
    }

}
