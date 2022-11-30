<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class SmstemplateTest extends DuskTestCase
{

    public function testCreateSmstemplate()
    {
        $admin = factory('App\User', 'admin')->create();
        $smstemplate = factory('App\Smstemplate')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $smstemplate) {
            $browser->loginAs($admin)
                ->visit(route('admin.smstemplates.index'))
                ->clickLink('Add new')
                ->type("title", $smstemplate->title)
                ->type("key", $smstemplate->key)
                ->type("content", $smstemplate->content)
                ->press('Save')
                ->assertRouteIs('admin.smstemplates.index')
                ->assertSeeIn("tr:last-child td[field-key='title']", $smstemplate->title)
                ->assertSeeIn("tr:last-child td[field-key='key']", $smstemplate->key)
                ->assertSeeIn("tr:last-child td[field-key='content']", $smstemplate->content)
                ->logout();
        });
    }

    public function testEditSmstemplate()
    {
        $admin = factory('App\User', 'admin')->create();
        $smstemplate = factory('App\Smstemplate')->create();
        $smstemplate2 = factory('App\Smstemplate')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $smstemplate, $smstemplate2) {
            $browser->loginAs($admin)
                ->visit(route('admin.smstemplates.index'))
                ->click('tr[data-entry-id="' . $smstemplate->id . '"] .btn-info')
                ->type("title", $smstemplate2->title)
                ->type("key", $smstemplate2->key)
                ->type("content", $smstemplate2->content)
                ->press('Update')
                ->assertRouteIs('admin.smstemplates.index')
                ->assertSeeIn("tr:last-child td[field-key='title']", $smstemplate2->title)
                ->assertSeeIn("tr:last-child td[field-key='key']", $smstemplate2->key)
                ->assertSeeIn("tr:last-child td[field-key='content']", $smstemplate2->content)
                ->logout();
        });
    }

    public function testShowSmstemplate()
    {
        $admin = factory('App\User', 'admin')->create();
        $smstemplate = factory('App\Smstemplate')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $smstemplate) {
            $browser->loginAs($admin)
                ->visit(route('admin.smstemplates.index'))
                ->click('tr[data-entry-id="' . $smstemplate->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='title']", $smstemplate->title)
                ->assertSeeIn("td[field-key='key']", $smstemplate->key)
                ->assertSeeIn("td[field-key='content']", $smstemplate->content)
                ->logout();
        });
    }

}
