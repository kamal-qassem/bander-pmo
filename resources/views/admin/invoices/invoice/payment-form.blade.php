
    <?php
        $total_paid = \Modules\InvoicePayments\Entities\InvoicePayment::where('invoice_id', $invoice->id)->where('payment_status', 'Success')->sum('amount');
        $total_used = \App\CreditNoteCredit::where('invoice_id', $invoice->id)->sum('amount');
        $total_paid += $total_used;
        $amount_due = $invoice->amount - $total_paid;
        if($amount_due < 0){
          $amount_due = 0;
        }
        
    ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>{{ trans('custom.invoices.invoice_no') . $invoice->invoicenumberdisplay }} ({{digiCurrency( $amount_due, $invoice->currency_id )}}) </h3>
</div>

<div class="col-sm-12">
<div class="alert message_bag" style="display:none" id="message_bag">
    <ul></ul>
</div>
</div>

<div class="modal-body">
<form class="form-horizontal" role="form" id="form_add_payment" method="post">
@if ( isPluginActive('account') )
<div class="form-group">
    <label for="account" class="col-sm-3 control-label">{{trans('custom.invoices.account') . '*'}}</label>
    <div class="col-sm-9">
       <?php
       $default_account = getSetting('default-account', 'invoice-settings', 'offline');
       ?>
       {!! Form::select('account', $accounts, old('account', $default_account), ['class' => 'form-control select2', 'id' => 'account']) !!}
    </div>
</div>
@endif

<div class="form-group">
    <label for="date" class="col-sm-3 control-label">{{trans('custom.invoices.date') . '*'}}</label>
    <div class="col-sm-9">
      <input type="text" class="form-control datepicker" value="{{digiTodayDate()}}" name="date" id="date">
    </div>
  </div>

<div class="form-group">
    <label for="description" class="col-sm-3 control-label">{{trans('custom.invoices.description')}}</label>
    <div class="col-sm-9">
      <textarea id="description" name="description" class="form-control">{{trans('custom.invoices.payment-for') . ' #' . $invoice->invoice_no}}</textarea>
    </div>
  </div>
  
  
<div class="form-group">
    <label for="transaction_id" class="col-sm-3 control-label">{{trans('custom.invoices.transaction_id') . '*'}}</label>
    <div class="col-sm-9">
      <input type="text" id="transaction_id" name="transaction_id" class="form-control" value="">
    </div>
  </div>

  <div class="form-group">
    <label for="amount" class="col-sm-3 control-label">{{trans('custom.invoices.amount') . '*'}}</label>
   
    <div class="col-sm-9">
      <?php
       $decimal_point = getSetting('decimals', 'currency_settings');
       ?>
      <input type="number" id="amount" name="amount" class="form-control amount" value="{{ number_format($amount_due,$decimal_point, '.', '') }}" onkeypress="return isDecimalNumber(event, this);">

    </div>
  </div>

  
<div class="form-group">
    <label for="cats" class="col-sm-3 control-label">{{trans('custom.invoices.category') . '*'}}</label>
    <div class="col-sm-9">
       <?php
       $default_category = getSetting('default-category', 'invoice-settings', 'offline');
       ?>
       {!! Form::select('category', $categories, old('category', $default_category), ['class' => 'form-control select2', 'id' => 'category']) !!}
    </div>
  </div>
  <div class="form-group">
    <label for="payer_name" class="col-sm-3 control-label">{{trans('custom.invoices.payer')}}</label>
    <div class="col-sm-9">
      <?php
      $customer_name = '';
      $customer_id = 0;
      if ( $customer ) {
        $customer_name = $customer->name;
        $customer_id = $customer->id;
        if ( empty( $customer_name ) ) {
          $customer_name = $customer->first_name;
          if ( ! empty( $customer_name ) ) {
            $customer_name .= ' ' . $customer->last_name;
          }
          if ( $customer_name ) {
            $customer_name = $customer->email;
          }
        }
      }
      ?>
      <input type="text" id="payer_name" name="payer_name" class="form-control" value="{{$customer_name}}" disabled>
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
<input type="hidden" name="payer" value="{{$customer_id}}">
</form>

</div>
