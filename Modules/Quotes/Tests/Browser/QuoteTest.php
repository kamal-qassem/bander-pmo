<?php

namespace Modules\Quotes\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\Quotes\Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class RecurringInvoiceTest extends DuskTestCase
{

    public function testCreateRecurringInvoice()
    {
        $admin = factory('App\User', 'admin')->create();
        $recurring_invoice = factory('Modules\Quotes\Entities\Quote')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $recurring_invoice) {
            $browser->loginAs($admin)
                ->visit(route('admin.recurring_invoices.index'))
                ->clickLink('Add new')
                ->select("customer_id", $recurring_invoice->customer_id)
                ->select("currency_id", $recurring_invoice->currency_id)
                ->type("title", $recurring_invoice->title)
                ->type("address", $recurring_invoice->address)
                ->type("invoice_prefix", $recurring_invoice->invoice_prefix)
                ->type("show_quantity_as", $recurring_invoice->show_quantity_as)
                ->type("invoice_no", $recurring_invoice->invoice_no)
                ->select("status", $recurring_invoice->status)
                ->type("reference", $recurring_invoice->reference)
                ->type("invoice_date", $recurring_invoice->invoice_date)
                ->type("invoice_due_date", $recurring_invoice->invoice_due_date)
                ->type("invoice_notes", $recurring_invoice->invoice_notes)
                ->select("tax_id", $recurring_invoice->tax_id)
                ->select("discount_id", $recurring_invoice->discount_id)
                ->select("recurring_period_id", $recurring_invoice->recurring_period_id)
                ->type("amount", $recurring_invoice->amount)
                ->select("paymentstatus", $recurring_invoice->paymentstatus)
                ->press('Save')
                ->assertRouteIs('admin.recurring_invoices.index')
                ->assertSeeIn("tr:last-child td[field-key='customer']", $recurring_invoice->customer->first_name)
                ->assertSeeIn("tr:last-child td[field-key='currency']", $recurring_invoice->currency->name)
                ->assertSeeIn("tr:last-child td[field-key='title']", $recurring_invoice->title)
                ->assertSeeIn("tr:last-child td[field-key='invoice_no']", $recurring_invoice->invoice_no)
                ->assertSeeIn("tr:last-child td[field-key='status']", $recurring_invoice->status)
                ->assertSeeIn("tr:last-child td[field-key='invoice_date']", $recurring_invoice->invoice_date)
                ->assertSeeIn("tr:last-child td[field-key='invoice_due_date']", $recurring_invoice->invoice_due_date)
                ->assertSeeIn("tr:last-child td[field-key='recurring_period']", $recurring_invoice->recurring_period->title)
                ->assertSeeIn("tr:last-child td[field-key='amount']", $recurring_invoice->amount)
                ->assertSeeIn("tr:last-child td[field-key='paymentstatus']", $recurring_invoice->paymentstatus)
                ->logout();
        });
    }

    public function testEditRecurringInvoice()
    {
        $admin = factory('App\User', 'admin')->create();
        $recurring_invoice = factory('Modules\Quotes\Entities\Quote')->create();
        $recurring_invoice2 = factory('Modules\Quotes\Entities\Quote')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $recurring_invoice, $recurring_invoice2) {
            $browser->loginAs($admin)
                ->visit(route('admin.recurring_invoices.index'))
                ->click('tr[data-entry-id="' . $recurring_invoice->id . '"] .btn-info')
                ->select("customer_id", $recurring_invoice2->customer_id)
                ->select("currency_id", $recurring_invoice2->currency_id)
                ->type("title", $recurring_invoice2->title)
                ->type("address", $recurring_invoice2->address)
                ->type("invoice_prefix", $recurring_invoice2->invoice_prefix)
                ->type("show_quantity_as", $recurring_invoice2->show_quantity_as)
                ->type("invoice_no", $recurring_invoice2->invoice_no)
                ->select("status", $recurring_invoice2->status)
                ->type("reference", $recurring_invoice2->reference)
                ->type("invoice_date", $recurring_invoice2->invoice_date)
                ->type("invoice_due_date", $recurring_invoice2->invoice_due_date)
                ->type("invoice_notes", $recurring_invoice2->invoice_notes)
                ->select("tax_id", $recurring_invoice2->tax_id)
                ->select("discount_id", $recurring_invoice2->discount_id)
                ->select("recurring_period_id", $recurring_invoice2->recurring_period_id)
                ->type("amount", $recurring_invoice2->amount)
                ->select("paymentstatus", $recurring_invoice2->paymentstatus)
                ->press('Update')
                ->assertRouteIs('admin.recurring_invoices.index')
                ->assertSeeIn("tr:last-child td[field-key='customer']", $recurring_invoice2->customer->first_name)
                ->assertSeeIn("tr:last-child td[field-key='currency']", $recurring_invoice2->currency->name)
                ->assertSeeIn("tr:last-child td[field-key='title']", $recurring_invoice2->title)
                ->assertSeeIn("tr:last-child td[field-key='invoice_no']", $recurring_invoice2->invoice_no)
                ->assertSeeIn("tr:last-child td[field-key='status']", $recurring_invoice2->status)
                ->assertSeeIn("tr:last-child td[field-key='invoice_date']", $recurring_invoice2->invoice_date)
                ->assertSeeIn("tr:last-child td[field-key='invoice_due_date']", $recurring_invoice2->invoice_due_date)
                ->assertSeeIn("tr:last-child td[field-key='recurring_period']", $recurring_invoice2->recurring_period->title)
                ->assertSeeIn("tr:last-child td[field-key='amount']", $recurring_invoice2->amount)
                ->assertSeeIn("tr:last-child td[field-key='paymentstatus']", $recurring_invoice2->paymentstatus)
                ->logout();
        });
    }

    public function testShowRecurringInvoice()
    {
        $admin = factory('App\User', 'admin')->create();
        $recurring_invoice = factory('Modules\Quotes\Entities\Quote')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $recurring_invoice) {
            $browser->loginAs($admin)
                ->visit(route('admin.recurring_invoices.index'))
                ->click('tr[data-entry-id="' . $recurring_invoice->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='customer']", $recurring_invoice->customer->first_name)
                ->assertSeeIn("td[field-key='currency']", $recurring_invoice->currency->name)
                ->assertSeeIn("td[field-key='title']", $recurring_invoice->title)
                ->assertSeeIn("td[field-key='address']", $recurring_invoice->address)
                ->assertSeeIn("td[field-key='invoice_prefix']", $recurring_invoice->invoice_prefix)
                ->assertSeeIn("td[field-key='show_quantity_as']", $recurring_invoice->show_quantity_as)
                ->assertSeeIn("td[field-key='invoice_no']", $recurring_invoice->invoice_no)
                ->assertSeeIn("td[field-key='status']", $recurring_invoice->status)
                ->assertSeeIn("td[field-key='reference']", $recurring_invoice->reference)
                ->assertSeeIn("td[field-key='invoice_date']", $recurring_invoice->invoice_date)
                ->assertSeeIn("td[field-key='invoice_due_date']", $recurring_invoice->invoice_due_date)
                ->assertSeeIn("td[field-key='invoice_notes']", $recurring_invoice->invoice_notes)
                ->assertSeeIn("td[field-key='tax']", $recurring_invoice->tax->name)
                ->assertSeeIn("td[field-key='discount']", $recurring_invoice->discount->name)
                ->assertSeeIn("td[field-key='recurring_period']", $recurring_invoice->recurring_period->title)
                ->assertSeeIn("td[field-key='amount']", $recurring_invoice->amount)
                ->assertSeeIn("td[field-key='products']", $recurring_invoice->products)
                ->assertSeeIn("td[field-key='paymentstatus']", $recurring_invoice->paymentstatus)
                ->logout();
        });
    }

}
