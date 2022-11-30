  
 <?php
        $total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', '=', $invoice->id)->where('payment_status', '=', 'Success')->sum('amount');
        $amount_due = $invoice->amount - $total_paid;
        if($amount_due < 0){
          $amount_due = 0;
        }
      ?>  

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>{{trans('custom.invoices.invoice_no') . $invoice->invoicenumberdisplay}} ({{digiCurrency( $amount_due, $invoice->currency_id )}})</h3>
</div>
<div class="modal-body">

<form class="form-horizontal" role="form" id="form_add_payment" method="post">


<div class="form-group">
    <label for="date" class="col-sm-3 control-label">{{trans('custom.invoices.date') . '*'}}</label>
    <div class="col-sm-9">
      <input type="text" class="form-control datepicker" value="{{digiTodayDate()}}" name="date" id="date" disabled="true">
    </div>
  </div>

<div class="form-group">
    <label for="description" class="col-sm-3 control-label">{{trans('custom.invoices.description')}}</label>
    <div class="col-sm-9">
      <textarea id="description" name="description" class="form-control" disabled="true">{{trans('custom.invoices.payment-for') . ' #' . $invoice->invoice_no}}</textarea>
    </div>
  </div>


  <div class="form-group">
    <label for="amount" class="col-sm-3 control-label">{{trans('custom.invoices.amount') . '*'}}</label>

     <?php
       $decimal_point = getSetting('decimals', 'currency_settings');
       ?>
    
    <div class="col-sm-9">
      <input type="number" id="amount" name="amount" class="form-control amount" value="{{ number_format($amount_due,$decimal_point, '.', '') }}" disabled="true">

    </div>
  </div>

  
  <div class="form-group">
    <label for="payer_name" class="col-sm-3 control-label">{{trans('custom.invoices.payer')}}</label>
    <div class="col-sm-9">
      <input type="text" id="payer_name" name="payer_name" class="form-control" value="{{$customer->first_name . ' ' . $customer->last_name}}" disabled>
    </div>
  </div>
   <div class="form-group">
    <label for="subject" class="col-sm-3 control-label">{{trans('custom.invoices.method') . '*'}}</label>
    <div class="col-sm-9">
      <?php
      $default_payment_gateway = getSetting('default_payment_gateway', 'site_settings', 'offline');
      ?>
      {!! Form::select('paymethod', $payment_gateways, old('paymethod', $default_payment_gateway), ['class' => 'form-control select2', 'id' => 'paymethod']) !!}
    </div>
  </div>

<input type="hidden" id="invoice_id" name="invoice_id" value="{{$invoice->id}}">
<input type="hidden" id="action" name="action" value="{{$action}}">
<input type="hidden" id="sub" name="sub" value="{{$sub}}">
<input type="hidden" name="payer" value="{{$customer->id}}">
</form>

</div>
