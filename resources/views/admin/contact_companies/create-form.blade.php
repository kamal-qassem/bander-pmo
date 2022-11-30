<h3 class="page-title">@lang('global.contact-companies.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.contact_companies.store'],'class'=>'formvalidation', 'id' => 'frmCompany']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>
    <div class="alert" style="display:none" id="message_bag_company">
        <ul></ul>
    </div>
    
    <div class="panel-body">
        @include('admin.contact_companies.form-fields')
    </div>
</div>

<?php
if ( empty( $is_ajax ) ) {
  $is_ajax = 'no';
}
?>
<input type="hidden" name="is_ajax" value="{{$is_ajax}}">
<?php
if ( empty( $selectedid ) ) {
  $selectedid = 'contact_company_id';
}
?>
<input type="hidden" name="selectedid" value="{{$selectedid}}">

{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect', 'id' => 'saveCompany']) !!}
{!! Form::close() !!}

@if ( 'yes' === $is_ajax )
<script type="text/javascript">
  $("#saveCompany").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.contact_companies.store')}}",
                type:'POST',
                data: $( '#frmCompany' ).serializeArray(),
                success: function(data) {
                    if($.isEmptyObject(data.error)){
                        notifyMe('success', data.success);
                        $('#loadingModal').modal('hide');

                        var value = data.record.id;
                        var title = data.record.name;
                        $('#' + data.record.selectedid).append('<option value="'+value+'" selected="selected">'+title+'</option>');                        
                    }else{
                        printErrorMsg(data.error);
                    }
                }
            });
  });

  function printErrorMsg (msg) {
      $("#message_bag_company").find("ul").html('');
      $("#message_bag_company").css('display','block');
      $("#message_bag_company").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $("#message_bag_company").find("ul").append('<li>'+value+'</li>');
      });
  }
</script>
@endif
