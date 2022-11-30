<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>{{trans('custom.credit_notes.credit_note_no') . $invoice->invoicenumberdisplay}}</h3>
</div>

<div class="col-sm-12 col-sm-offset-2">
<div class="alert message_bag" style="display:none" id="message_bag">
    <ul></ul>
</div>
</div>

<div class="modal-body">

<form class="form-horizontal" role="form" id="email_form" method="post">


<div class="form-group">
    <label for="toemail" class="col-sm-2 control-label">{{trans('custom.email.to')}}</label>
    <div class="col-sm-10">
      <input type="email" id="toemail" name="toemail" class="form-control" value="{{$customer->email ?? ''}}">
    </div>

  </div>
    <br/>

  <div class="form-group">
    <label for="toname" class="col-sm-2 control-label">{{trans('custom.email.customer-name')}}</label>
    <div class="col-sm-10">
      <input type="text" id="toname" name="toname" class="form-control" value="{{$customer->name ?? ''}}">
    </div>
  </div>


  <div class="form-group">
    <label for="ccemail" class="col-sm-2 control-label">{{trans('custom.email.cc')}}</label>
    <div class="col-sm-10">
      <input type="email" id="ccemail" name="ccemail" class="form-control" value="">
    </div>
  </div>
<br/>
  <div class="form-group">
    <label for="bccemail" class="col-sm-2 control-label">{{trans('custom.email.bcc')}}</label>
    <div class="col-sm-10">
      <input type="email" id="bccemail" name="bccemail" class="form-control" value="">
    </div>
  </div>

  <div class="form-group">
		<label for="bcc_admin" class="col-sm-2 control-label">{{trans('custom.email.bcc-admin')}}</label>
		<div class="col-sm-10">
			<div class="checkbox c-checkbox">
				<label>
					<input type="checkbox" name="bcc_admin" id="bcc_admin" value="Yes">
				</label>
			</div>
		</div>
	</div>

    <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">{{trans('custom.email.subject')}}</label>
    <div class="col-sm-10">
      <input type="text" id="subject" name="subject" class="form-control" value="{{$template->subject}}">
    </div>
  </div>
  <br/>
  <div class="form-group">
    <label for="subject" class="col-sm-2 control-label">{{trans('custom.email.message-body')}}</label>
    <div class="col-sm-10">
      
      <textarea class="form-control editor" rows="3" name="message" id="message">{{$template->content}}</textarea>
      <input type="hidden" id="invoice_id" name="invoice_id" value="{{$invoice->id}}">
      <input type="hidden" id="action" name="action" value="{{$action}}">
      <input type="hidden" id="sub" name="sub" value="{{$sub}}">
    </div>
  </div>


	<div class="form-group">
		<label for="attach_pdf" class="col-sm-2 control-label">{{trans('custom.email.attach-pdf')}}</label>
		<div class="col-sm-10">
			<div class="checkbox c-checkbox">
				<label>
					<input type="checkbox" name="attach_pdf" id="attach_pdf" value="Yes" checked><i class="fa fa-paperclip"></i> 
					<?php
					$filename = $invoice->id . '_' . $invoice->invoice_no . '.pdf';
					?>
					<a href="{{ asset(env('UPLOAD_PATH').'/uploads/credit_notes/' . $filename ) }}" target="_blank">{{$filename}}</a>
				</label>
			</div>
		</div>
	</div>
</form>

</div>
