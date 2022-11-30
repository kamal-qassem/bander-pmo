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

                @include('admin.invoices.invoice.invoice-menu', compact('invoice'))

                @include('admin.invoices.invoice.recurring-notice', compact('invoice'))
                
                @include('admin.invoices.invoice.invoice-content', compact('invoice'))
                
            </div>

            <?php
            $enable_online_signature = getSetting('enable-online-signature', 'invoice-settings'); 
            if('yes'=== $enable_online_signature){
            ?>
                @include ('admin.invoices.invoice.invoice-online-signature', compact('invoice'))
           <?php } ?>

            <div class="col-xl-12">
            @include ('admin.invoices.invoice.invoice-transactions', compact('invoice'))
            </div>

            @if( isAdmin() )                
                @include ('admin.invoices.invoice.invoice-access-log', compact('invoice'))
            @endif
        </section>
    </div>
</div>
@include('admin.invoices.modal-loading', compact('invoice'))
@section('javascript')
    @parent

    <script src="{{ url('js/cdn-js-files/mdbootstrap') }}/mdb.min.js"></script>

    
    @include('admin.invoices.scripts', compact('invoice'))
@stop
