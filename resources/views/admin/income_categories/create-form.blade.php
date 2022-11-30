<h3 class="page-title">@lang('global.income-category.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.income_categories.store'],'class'=>'formvalidation', 'id' => 'frmIncomeCategories']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>

    <div class="alert message_bag" style="display:none">
        <ul></ul>
    </div>
    
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('name', trans('global.income-category.fields.name').'*', ['class' => 'control-label form-label']) !!}
                <div class="form-line">
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Name', 'required' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('name'))
                    <p class="help-block">
                        {{ $errors->first('name') }}
                    </p>
                @endif
            </div>
            </div>
            </div>
        </div>
        
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

{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveButtonIncomeCategories']) !!}
{!! Form::close() !!}

@if( ! empty( $is_ajax ) && 'yes' === $is_ajax )
<script type="text/javascript">
  $(".saveButtonIncomeCategories").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.income_categories.store')}}",
                type:'POST',
                data: $( '#frmIncomeCategories' ).serializeArray(),
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
