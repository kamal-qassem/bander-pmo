<h3 class="page-title">@lang('global.warehouses.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.warehouses.store'],'class'=>'formvalidation', 'id' => 'frmWarehouse']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>

    <div class="alert message_bag_warehouse" style="display:none">
        <ul></ul>
    </div>
    
    <div class="panel-body">
        @include('admin.warehouses.form-fields')            
    </div>
</div>
<?php
if ( empty( $is_ajax ) ) {
  $is_ajax = 'no';
}
?>
<input type="hidden" name="is_ajax" value="{{$is_ajax}}">
{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveButtonWarehouse']) !!}
{!! Form::close() !!}

@if( ! empty( $is_ajax ) && 'yes' === $is_ajax )
<script type="text/javascript">
  $(".saveButtonWarehouse").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.warehouses.store')}}",
                type:'POST',
                data: $( '#frmWarehouse' ).serializeArray(),
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
      $(".message_bag_warehouse").find("ul").html('');
      $(".message_bag_warehouse").css('display','block');
      $(".message_bag_warehouse").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $(".message_bag_warehouse").find("ul").append('<li>'+value+'</li>');
      });
  }
</script>
@endif
