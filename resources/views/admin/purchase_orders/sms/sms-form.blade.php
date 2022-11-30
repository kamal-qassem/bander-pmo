<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>{{trans('custom.purchase_orders.purchase-order') . $invoice->invoicenumberdisplay }}</h3>
</div>

<div class="col-sm-12 col-sm-offset-2">
<div class="alert" style="display:none" id="message_bag">
    <ul></ul>
</div>
</div>
<div class="modal-body">
<form class="form-horizontal" role="form" id="email_form" method="post">
  <div class="form-group">
    <label for="toemail" class="col-sm-2 control-label">{{trans('custom.email.to')}}</label>
    <div class="col-sm-10">
      <?php
      $phone = ! empty( $customer->phone1_code ) ? $customer->phone1_code : '';
      $phone = ! empty( $customer->phone1 ) ? $phone . $customer->phone1 : '';
      ?>
      <input type="text" id="tonumber" name="tonumber" class="form-control" value="{{$phone}}">
      <p class="help-block">Phone number should include the country code. But not '+' sign. Eg: 919855266889</p>
    </div>
  </div>

  <div class="form-group">
    <label for="toname" class="col-sm-2 control-label">{{trans('custom.email.customer-name')}}</label>
    <div class="col-sm-10">
      <input type="text" id="toname" name="toname" class="form-control" value="{{$customer->name ?? ''}}">
    </div>
  </div>

  
  <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">{{trans('custom.email.message-body')}}</label>
    <div class="col-sm-10">
      
      <textarea class="form-control" rows="3" name="message" id="message">{{$template->content}}</textarea>
      <input type="hidden" id="invoice_id" name="invoice_id" value="{{$invoice->id}}">
      <input type="hidden" id="action" name="action" value="{{$action}}">
      <input type="hidden" id="sub" name="sub" value="{{$sub}}">
    </div>
  </div>

</form>


</div>
