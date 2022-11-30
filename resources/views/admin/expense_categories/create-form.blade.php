<h3 class="page-title">@lang('global.expense-category.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.expense_categories.store'],'class'=>'formvalidation', 'id' => 'frmExpenseCategories']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>

    <div class="alert message_bag" style="display:none">
        <ul></ul>
    </div>
    
    <div class="panel-body">
        @include('admin.expense_categories.form-fields')
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
  $selectedid = 'income_category_id';
}
?>
<input type="hidden" name="selectedid" value="{{$selectedid}}">

{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveButtonExpenseCategories']) !!}
{!! Form::close() !!}

@if( ! empty( $is_ajax ) && 'yes' === $is_ajax )
<script type="text/javascript">
  $(".saveButtonExpenseCategories").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.expense_categories.store')}}",
                type:'POST',
                data: $( '#frmExpenseCategories' ).serializeArray(),
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
      $(".message_bag").find("ul").html('');
      $(".message_bag").css('display','block');
      $(".message_bag").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $(".message_bag").find("ul").append('<li>'+value+'</li>');
      });
  }
</script>
@endif