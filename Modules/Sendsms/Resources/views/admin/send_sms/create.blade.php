@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('sendsms::global.send-sms.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.send_sms.store'],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="col-xs-form-group">
                    {!! Form::label('client_id', trans('global.client-projects.fields.client').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('client_id', $clients, '', ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true' ]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('client_id'))
                        <p class="help-block">
                            {{ $errors->first('client_id') }}
                        </p>
                    @endif
                </div>
                </div>
				
				<div class="col-xs-6">
                    <div class="form-group">
                    {!! Form::label('send_to', trans('sendsms::global.send-sms.fields.send-to').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('send_to', old('send_to'), ['class' => 'form-control number', 'placeholder' => '', 'required' => '']) !!}
                    <input type="hidden" name="send_to_number" id="send_to_number" value="">
                    <p class="help-block"></p>
                    @if($errors->has('send_to'))
                        <p class="help-block">
                            {{ $errors->first('send_to') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
                

                
            
                <div class="col-xs-8">
                <div class="form-group">
                    {!! Form::label('message', trans('sendsms::global.send-sms.fields.message').'*', ['class' => 'control-label']) !!}
                    {!! Form::textarea('message', old('message'), ['class' => 'form-control ', 'placeholder' => '', 'required' => '','rows'=>'4']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('message'))
                        <p class="help-block">
                            {{ $errors->first('message') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
      
            
        </div>
    

    {!! Form::submit(trans('sendsms::global.send-sms.send'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent

    <script>
        
        $('#client_id').change(function() {
            $.ajax({
                url: '{{route("admin.sendsms.getuserbyid")}}',
                dataType: "json",
                method: 'post',
                data: {                            
                    '_token': crsf_hash,
                    contact_id: $(this).val(),
                },
                success: function (result) {
                    if ( result.data.contact ) {
                        $('#send_to').val(result.data.contact.phone);
                        $('#send_to_number').val(result.data.phone);                         
                    }
                    if ( 'danger' === result.status ) {                        
                        $('#client_id').closest('div.form-group').find('.help-block').html('<span class="error">'+result.edit_message+'</span>');
                        notifyMe(result.status, result.message);
                    }
                }
            });
        });
    </script>
@stop