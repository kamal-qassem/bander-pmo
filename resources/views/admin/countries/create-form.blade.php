<h3 class="page-title">@lang('global.countries.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.countries.store'],'class'=>'formvalidation', 'id' => 'frmCountry']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>
    <div class="alert" style="display:none" id="message_bag_country">
        <ul></ul>
    </div>
    
    <div class="panel-body">
        @include('admin.countries.form-fields')        
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
  $selectedid = 'country_id';
}
?>
<input type="hidden" name="selectedid" value="{{$selectedid}}">

{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect', 'id' => 'saveCountry']) !!}
{!! Form::close() !!}

@if ( 'yes' === $is_ajax )
<script type="text/javascript">
  $("#saveCountry").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.countries.store')}}",
                type:'POST',
                data: $( '#frmCountry' ).serializeArray(),
                success: function(data) {
                    if($.isEmptyObject(data.error)){
                        notifyMe('success', data.success);
                        $('#loadingModal').modal('hide');

                        var value = data.record.id;
                        var title = data.record.title;
                        $('.country').append('<option value="'+value+'">'+title+'</option>');
                        $('#' + data.record.selectedid).val( value );
                    }else{
                        printErrorMsg(data.error);
                    }
                }
            });
  });

  function printErrorMsg (msg) {
      $("#message_bag_country").find("ul").html('');
      $("#message_bag_country").css('display','block');
      $("#message_bag_country").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $("#message_bag_country").find("ul").append('<li>'+value+'</li>');
      });
  }
</script>
@endif
