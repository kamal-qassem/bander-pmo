<h3 class="page-title">@lang('global.taxes.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.taxes.store'],'class'=>'formvalidation', 'id' => 'frmTax']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>

    <div class="alert message_bag_tax" style="display:none">
        <ul></ul>
    </div>
    
    <div class="panel-body">
        @include('admin.taxes.form-fields')            
    </div>
</div>

<?php
if ( empty( $is_ajax ) ) {
  $is_ajax = 'no';
}
?>
<input type="hidden" name="is_ajax" value="{{$is_ajax}}">

{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveButtonTax']) !!}
{!! Form::close() !!}

@if( ! empty( $is_ajax ) && 'yes' === $is_ajax )
<script type="text/javascript">
  $(".saveButtonTax").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.taxes.store')}}",
                type:'POST',
                data: $( '#frmTax' ).serializeArray(),
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
      $(".message_bag_tax").find("ul").html('');
      $(".message_bag_tax").css('display','block');
      $(".message_bag_tax").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $(".message_bag_tax").find("ul").append('<li>'+value+'</li>');
      });
  }
</script>
@endif