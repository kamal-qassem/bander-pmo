<h3 class="page-title">@lang('global.discounts.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.discounts.store'],'class'=>'formvalidation', 'id' => 'frmDiscount']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>

    <div class="alert message_bag_discount" style="display:none">
        <ul></ul>
    </div>
    
    <div class="panel-body">
        @include('admin.discounts.form-fields')            
    </div>
</div>

<?php
if ( empty( $is_ajax ) ) {
  $is_ajax = 'no';
}
?>
<input type="hidden" name="is_ajax" value="{{$is_ajax}}">

{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveButtonDiscount']) !!}
{!! Form::close() !!}

@if( ! empty( $is_ajax ) && 'yes' === $is_ajax )
<script type="text/javascript">
  $(".saveButtonDiscount").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.discounts.store')}}",
                type:'POST',
                data: $( '#frmDiscount' ).serializeArray(),
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
      $(".message_bag_discount").find("ul").html('');
      $(".message_bag_discount").css('display','block');
      $(".message_bag_discount").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $(".message_bag_discount").find("ul").append('<li>'+value+'</li>');
      });
  }
</script>
@endif
