<style>
  @media (min-width: 419px) {
    .col-sm-8 {
        width: 100% ;
    }
}
@media (min-width: 991px) {
    .col-sm-8 {
        width: 83.3%;
    }
}
</style>
<form class="form-horizontal" role="form" id="email_form" method="post">
<div class="modal-body">

<div class="form-group">
    <label for="toemail" class="col-sm-2 control-label" style="margin-top: 10px;">@lang('global.contacts.fields.name')</label>
    
    <div class="col-sm-8" style="margin-top: 10px; height: 34px; margin-bottom: 13px;"><div class="form-control" style="border: 1px solid #d2d6de;">{{$contact->name}}</div></div>
    
</div>

<div class="form-group">
    <label for="toemail" class="col-sm-2 control-label" style="margin-top: 7px;">{{trans('custom.email.to')}}</label>
    <div class="col-sm-10" style="margin-top: 7px; margin-bottom: 13px;">
      <input type="email" id="toemail" name="toemail" class="form-control" value="{{$contact->email}}">
    </div>
</div>


  <div class="form-group">
    <label for="ccemail" class="col-sm-2 control-label" style="margin-top: 7px;">{{trans('custom.email.cc')}}</label>
    <div class="col-sm-10" style="margin-top: 7px; margin-bottom: 13px;">
      <input type="email" id="ccemail" name="ccemail" class="form-control" value="">
    </div>
  </div>

  <div class="form-group">
    <label for="bccemail" class="col-sm-2 control-label" style="margin-top: 7px;">{{trans('custom.email.bcc')}}</label>
    <div class="col-sm-10" style="margin-top: 7px; margin-bottom: 13px;">
      <input type="email" id="bccemail" name="bccemail" class="form-control" value="">
    </div>
  </div>


  <div class="form-group">
    <label for="subject" class="col-sm-2 control-label" style="margin-top: 7px;">{{trans('custom.email.subject')}}</label>
    <div class="col-sm-10" style="margin-top: 7px; margin-bottom: 13px;">
      <input type="text" id="subject" name="subject" class="form-control" value="{{$template->subject}}">
    </div>
  </div>

  <div class="form-group">
    <label for="subject" class="col-sm-2 control-label" style="margin-top: 7px;">{{trans('custom.email.message-body')}}</label>
    <div class="col-sm-10" style="margin-top: 9px; margin-bottom: 12px;">
      
      <textarea class="form-control editor" rows="3" name="message" id="message">{{$template->content}}</textarea>
      <input type="hidden" id="contact_id" name="contact_id" value="{{$contact->id}}">
      <input type="hidden" id="action" name="action" value="{{$action}}">
    </div>
  </div>

</div>

</form>
<script type="text/javascript">
  function getFormData( formid ){
        if ( typeof( formid ) == 'undefined' ) {
          formid = 'email_form';
        }
        var unindexed_array = $( '#' + formid ).serializeArray();
        var indexed_array = {};
        
        $.each(unindexed_array, function( index, value ) {
          indexed_array[value.name] = value.value;
        });

        return indexed_array;
    }

    function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }

  $('#sendButton').click(function(e) {
        e.preventDefault();
        
        $('.error').remove();

        var toemail = $('#toemail').val();
        var subject = $('#subject').val();
        var message = $('#message').val();
        var selectedthumnail = false;
        $(".thumbnail").each(function(index){
             if($(this).is(':checked')){
                selectedthumnail = true;
             }
       });

        var errors = 0;
        if ( toemail == '' ) {
          $('#toemail').after('<span class="error">Please enter email address</a>');
          $('#toemail').focus();
          errors++;
        } else if( ! isEmail( toemail ) ) {
          $('#toemail').after('<span class="error">Please enter valid email address</a>');
          $('#toemail').focus();
          errors++;
        }
        if ( subject == '' ) {
          $('#subject').after('<span class="error">{{trans("custom.invoices.messages.date")}}</a>');
          $('#subject').focus();
          errors++;
        }
        if ( message == '' ) {
          $('#message').after('<span class="error">{{trans("custom.invoices.messages.date")}}</a>');
          $('#message').focus();
          errors++;
        }

        if ( selectedthumnail == false ) {
          $('.thumbnail').after('<span class="error">Please select at least one product</a>');
          $('.thumbnail').focus();
          errors++;
        }
      if ( errors == 0 ) {
        var data = getFormData( 'email_form' );
        $.ajax({
          url: '{{url('admin/products/sendthumb')}}',
          dataType: "json",
          method: 'post',
          data: {
            '_token': crsf_hash,
            'data': getFormData( 'email_form' )
          },
          success: function (data) {
            $('#loadingModal').modal('toggle');

            notifyMe('success', 'Mail sent successfully');         
          }
        });
      }
    });
</script>

