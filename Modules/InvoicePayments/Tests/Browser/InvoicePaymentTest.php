<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class InvoicePaymentTest extends DuskTestCase
{

    public function testCreateInvoicePayment()
    {
        $admin = factory('App\User', 'admin')->create();
        $invoice_payment = factory('App\InvoicePayment')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $invoice_payment) {
            $browser->loginAs($admin)
                ->visit(route('admin.invoice_payments.index'))
                ->clickLink('Add new')
                ->select("invoice_id", $invoice_payment->invoice_id)
                ->type("date", $invoice_payment->date)
                ->select("account_id", $invoice_payment->account_id)
                ->type("amount", $invoice_payment->amount)
                ->type("transaction_id", $invoice_payment->transaction_id)
                ->press('Save')
                ->assertRouteIs('admin.invoice_payments.index')
                ->assertSeeIn("tr:last-child td[field-key='invoice']", $invoice_payment->invoice->invoice_no)
                ->assertSeeIn("tr:last-child td[field-key='date']", $invoice_payment->date)
                ->assertSeeIn("tr:last-child td[field-key='account']", $invoice_payment->account->name)
                ->assertSeeIn("tr:last-child td[field-key='amount']", $invoice_payment->amount)
                ->assertSeeIn("tr:last-child td[field-key='transaction_id']", $invoice_payment->transaction_id)
                ->logout();
        });
    }

    public function testEditInvoicePayment()
    {
        $admin = factory('App\User', 'admin')->create();
        $invoice_payment = factory('App\InvoicePayment')->create();
        $invoice_payment2 = factory('App\InvoicePayment')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $invoice_payment, $invoice_payment2) {
            $browser->loginAs($admin)
                ->visit(route('admin.invoice_payments.index'))
                ->click('tr[data-entry-id="' . $invoice_payment->id . '"] .btn-info')
                ->select("invoice_id", $invoice_payment2->invoice_id)
                ->type("date", $invoice_payment2->date)
                ->select("account_id", $invoice_payment2->account_id)
                ->type("amount", $invoice_payment2->amount)
                ->type("transaction_id", $invoice_payment2->transaction_id)
                ->press('Update')
                ->assertRouteIs('admin.invoice_payments.index')
                ->assertSeeIn("tr:last-child td[field-key='invoice']", $invoice_payment2->invoice->invoice_no)
                ->assertSeeIn("tr:last-child td[field-key='date']", $invoice_payment2->date)
                ->assertSeeIn("tr:last-child td[field-key='account']", $invoice_payment2->account->name)
                ->assertSeeIn("tr:last-child td[field-key='amount']", $invoice_payment2->amount)
                ->assertSeeIn("tr:last-child td[field-key='transaction_id']", $invoice_payment2->transaction_id)
                ->logout();
        });
    }

    public function testShowInvoicePayment()
    {
        $admin = factory('App\User', 'admin')->create();
        $invoice_payment = factory('App\InvoicePayment')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $invoice_payment) {
            $browser->loginAs($admin)
                ->visit(route('admin.invoice_payments.index'))
                ->click('tr[data-entry-id="' . $invoice_payment->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='invoice']", $invoice_payment->invoice->invoice_no)
                ->assertSeeIn("td[field-key='date']", $invoice_payment->date)
                ->assertSeeIn("td[field-key='account']", $invoice_payment->account->name)
                ->assertSeeIn("td[field-key='amount']", $invoice_payment->amount)
                ->assertSeeIn("td[field-key='transaction_id']", $invoice_payment->transaction_id)
                ->logout();
        });
    }

}
