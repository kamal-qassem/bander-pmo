<h3 class="page-title">@lang('global.contract_types.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.contract_types.store'],'class'=>'formvalidation', 'id' => 'frmContractType']) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('global.app_create')
    </div>

    <div class="alert message_bag_contract_type" style="display:none">
        <ul></ul>
    </div>
    
    <div class="panel-body">
     
            <div class="row">
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('name', trans('global.contract_types.fields.name').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('description', trans('global.contract_types.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => '', 'rows' => 2]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('description'))
                        <p class="help-block">
                            {{ $errors->first('description') }}
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
{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect saveButtonContractType']) !!}
{!! Form::close() !!}

@if( ! empty( $is_ajax ) && 'yes' === $is_ajax )
<script type="text/javascript">
  $(".saveButtonContractType").click(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{route('admin.contract_types.store')}}",
                type:'POST',
                data: $( '#frmContractType' ).serializeArray(),
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
      $(".message_bag_contract").find("ul").html('');
      $(".message_bag_contract").css('display','block');
      $(".message_bag_contract").addClass('alert-danger');
      $.each( msg, function( key, value ) {
          $(".message_bag_contract").find("ul").append('<li>'+value+'</li>');
      });
  }
</script>
@endif
