<div class="modal-header">
	<h3>{{trans('orders::global.orders.fields.id')}} #{{ $order->id ?? '' }} </h3>
</div>
<div class="panel-body">
 <?php
    $total_paid = \Modules\Orders\Entities\OrdersPayments::where('payment_status', 'success')->where('order_id', $order->id)->sum('amount');

    $amount_due = $order->price;
    if ( $total_paid > 0 ) {
        $amount_due = $order->price - $total_paid;
    }
    ?>
<table class="table table-bordered table-striped">
    <tr>
        <th>@lang('orders::global.orders.fields.id')</th>
        <td field-key='customer'>#{{ $order->id ?? '' }}</td>
    </tr>
    <tr>
        <th>@lang('custom.date')</th>
        <td field-key='customer'>{{ digiDate( $order->created_at, true ) ?? '' }}</td>
    </tr>
    <tr>
        <th>@lang('orders::global.orders.fields.customer')</th>
        <td field-key='customer'>{{ $order->customer->name ?? '' }}</td>
    </tr>
    
    <tr>
        <th>@lang('orders::global.orders.fields.price')</th>
        <td field-key='price'>{{ digiCurrency($order->price,$order->currency_id) }}</td>
    </tr>
    <tr>
        <th>@lang('orders::global.orders.fields.total-paid')</th>
        <td field-key='total-paid'>{{ digiCurrency( $total_paid, $order->currency_id ) }}</td>
    </tr>
     <tr>
        <th>@lang('orders::global.orders.fields.amount-due')</th>
        <td field-key='amount-due'>{{ digiCurrency( $amount_due, $order->currency_id ) }}</td>
    </tr>
</table>

<div class="alert" style="display:none" id="message_bag_department">
    <ul></ul>
</div>

 

{!! Form::open(['method' => 'POST', 'route' => ['admin.orders.save-payment'], 'class'=>'formvalidation', 'id' => 'frmPayment']) !!}

  <div class="col-xs-6">
  <div class="form-group" style="margin-left: -15px;">
    
    
      {!! Form::label('price', trans('custom.invoices.amount').'*', ['class' => 'control-label form-label']) !!}
      <div class="form-line">
      {!! Form::text('price', old('price', $amount_due), ['class' => 'form-control amount', 'placeholder' => trans('custom.invoices.amount'), 'required' => '']) !!}
      <p class="help-block"></p>
      @if($errors->has('price'))
          <p class="help-block">
              {{ $errors->first('price') }}
          </p>
      @endif
  </div>
  </div>
  </div>

  <div class="col-xs-6">
  <div class="form-group" style="margin-left: 15px;">
      {!! Form::label('paymethod', trans('custom.invoices.method').'*', ['class' => 'control-label form-label']) !!}
      <div class="form-line">
      <?php
      $payment_gateways = \App\PaymentGateway::where('status', '=', 'Active')->get()->pluck('name', 'key')->prepend(trans('global.app_please_select'), '');
      $default_payment_gateway = getSetting('default_payment_gateway', 'site_settings', 'offline');
      ?>
      {!! Form::select('paymethod', $payment_gateways, old('paymethod', $default_payment_gateway), ['class' => 'form-control select2', 'id' => 'paymethod']) !!}
      <p class="help-block"></p>
      @if($errors->has('paymethod'))
          <p class="help-block">
              {{ $errors->first('paymethod') }}
          </p>
      @endif
  </div>
  </div>
  </div>

  <div class="col-xs-6">
  <div class="form-group" style="margin-left: -15px;">
      {!! Form::label('payment_status', trans('custom.payment-status').'*', ['class' => 'control-label form-label']) !!}
      <div class="form-line">
      <?php
      $options = array(
        '' => trans('global.app_please_select'),
        'success' => trans('custom.common.success'),
        'pending' => trans('custom.common.pending'),
      );
      ?>
      {!! Form::select('payment_status', $options, old('payment_status'), ['class' => 'form-control select2', 'id' => 'payment_status']) !!}
      <p class="help-block"></p>
      @if($errors->has('payment_status'))
          <p class="help-block">
              {{ $errors->first('payment_status') }}
          </p>
      @endif
  </div>
  </div>
  </div>

    <div class="col-xs-6">
  <div class="form-group" style="margin-left: 15px;">
      {!! Form::label('order_status', trans('custom.order-status').'*', ['class' => 'control-label form-label']) !!}
      <div class="form-line">
      <?php
      $options = array(
        '' => trans('global.app_please_select'),
        'Active' => trans('custom.orders.active'),
        'Pending' => trans('custom.orders.pending'),
      );
      ?>
      {!! Form::select('order_status', $options, old('order_status'), ['class' => 'form-control select2', 'id' => 'order_status']) !!}
      <p class="help-block"></p>
      @if($errors->has('order_status'))
          <p class="help-block">
              {{ $errors->first('order_status') }}
          </p>
      @endif
  </div>
  </div>
  </div>

<input type="hidden" id="order_id" name="order_id" value="{{$order->id}}">
</div>

<div style="margin-left: 15px;">
{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect', 'id' => 'savePayment']) !!}
{!! Form::close() !!}
</div>

<script type="text/javascript">
$("#savePayment").click(function(e){
          e.preventDefault();

          $.ajax({
              url: "{{route('admin.orders.save-payment')}}",
              type:'POST',
              data: $( '#frmPayment' ).serializeArray(),
              success: function(data) {
                  if($.isEmptyObject(data.error)){
                      notifyMe('success', data.success);
                      $('#loadingModal').modal('hide');

                      location.reload();
                  }else{
                      printErrorMsg(data.error);
                  }
              }
          });
});

function printErrorMsg (msg) {
    $("#message_bag_department").find("ul").html('');
    $("#message_bag_department").css('display','block');
    $("#message_bag_department").addClass('alert-danger');
    $.each( msg, function( key, value ) {
        $("#message_bag_department").find("ul").append('<li>'+value+'</li>');
    });
}
</script>

