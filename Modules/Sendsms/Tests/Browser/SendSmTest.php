<?php

namespace Modules\Sendsms\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class SendSmTest extends DuskTestCase
{

    public function testCreateSendSm()
    {
        $admin = factory('App\User', 'admin')->create();
        $send_sm = factory('Modules\Sendsms\Entities\SendSm')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $send_sm) {
            $browser->loginAs($admin)
                ->visit(route('admin.send_sms.index'))
                ->clickLink('Add new')
                ->type("send_to", $send_sm->send_to)
                ->type("message", $send_sm->message)
                ->select("gateway_id", $send_sm->gateway_id)
                ->press('Save')
                ->assertRouteIs('admin.send_sms.index')
                ->assertSeeIn("tr:last-child td[field-key='send_to']", $send_sm->send_to)
                ->assertSeeIn("tr:last-child td[field-key='message']", $send_sm->message)
                ->assertSeeIn("tr:last-child td[field-key='gateway']", $send_sm->gateway->name)
                ->logout();
        });
    }

    public function testEditSendSm()
    {
        $admin = factory('App\User', 'admin')->create();
        $send_sm = factory('Modules\Sendsms\Entities\SendSm')->create();
        $send_sm2 = factory('Modules\Sendsms\Entities\SendSm')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $send_sm, $send_sm2) {
            $browser->loginAs($admin)
                ->visit(route('admin.send_sms.index'))
                ->click('tr[data-entry-id="' . $send_sm->id . '"] .btn-info')
                ->type("send_to", $send_sm2->send_to)
                ->type("message", $send_sm2->message)
                ->select("gateway_id", $send_sm2->gateway_id)
                ->press('Update')
                ->assertRouteIs('admin.send_sms.index')
                ->assertSeeIn("tr:last-child td[field-key='send_to']", $send_sm2->send_to)
                ->assertSeeIn("tr:last-child td[field-key='message']", $send_sm2->message)
                ->assertSeeIn("tr:last-child td[field-key='gateway']", $send_sm2->gateway->name)
                ->logout();
        });
    }

    public function testShowSendSm()
    {
        $admin = factory('App\User', 'admin')->create();
        $send_sm = factory('Modules\Sendsms\Entities\SendSm')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $send_sm) {
            $browser->loginAs($admin)
                ->visit(route('admin.send_sms.index'))
                ->click('tr[data-entry-id="' . $send_sm->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='send_to']", $send_sm->send_to)
                ->assertSeeIn("td[field-key='message']", $send_sm->message)
                ->assertSeeIn("td[field-key='gateway']", $send_sm->gateway->name)
                ->logout();
        });
    }

}
