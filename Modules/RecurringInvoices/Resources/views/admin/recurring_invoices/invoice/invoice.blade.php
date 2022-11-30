<?php
/**
 * Targetted Invocie Actions:
 * Email
    - Invoice Created
    - Invoice Payment Reminder
    - Invocie Overdue Notice
    - Invocie Payment Confirmation
    - Invoice Refund Confirmation
 * SMS
    - Invoice Created
    - Invoice Payment Reminder
    - Invocie Overdue Notice
    - Invocie Payment Confirmation
    - Invoice Refund Confirmation
 * Mark As
    - Unpaid
    - Partially Paid
    - Cancelled
 * Add Payment
 * Preview
 * Edit
 * PDF
    - View PDF
    - Download PDF
 * Upload Documents
 * Clone
 * Print
 */
?>
<input type="hidden" name="invoice_id" value="{{$invoice->id}}" id="invoice_id">
<div class="invoice-wrapper" id="application_ajaxrender">
    <div class="content-body">
        <section class="card"> 

            <div id="invoice-template" class="card-block">

                @include('recurringinvoices::admin.recurring_invoices.invoice.invoice-menu', compact('invoice'))

                @include('admin.invoices.invoice.recurring-notice', compact('invoice'))

                @include('recurringinvoices::admin.recurring_invoices.invoice.invoice-content', compact('invoice'))
                
            </div>
            <div class="col-xl-12">
            @include ('recurringinvoices::admin.recurring_invoices.invoice.invoice-transactions', compact('invoice'))
        </div>

            @include ('recurringinvoices::admin.recurring_invoices.invoice.invoice-access-log', compact('invoice'))
        </section>
    </div>
</div>
@include('recurringinvoices::admin.recurring_invoices.modal-loading', compact('invoice'))
@section('javascript')
    @parent
    @include('recurringinvoices::admin.recurring_invoices.scripts', compact('invoice'))
@stop
