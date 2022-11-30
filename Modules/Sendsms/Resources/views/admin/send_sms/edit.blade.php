@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('sendsms::global.send-sms.title')</h3>
    
    {!! Form::model($send_sm, ['method' => 'PUT', 'route' => ['admin.send_sms.update', $send_sm->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                    {!! Form::label('send_to', trans('sendsms::global.send-sms.fields.send-to').'*', ['class' => 'control-label']) !!}
                    <div class="form-line">
                    {!! Form::text('send_to', old('send_to'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('send_to'))
                        <p class="help-block">
                            {{ $errors->first('send_to') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>

                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('gateway_id', trans('sendsms::global.send-sms.fields.gateway').'', ['class' => 'control-label']) !!}
                    {!! Form::select('gateway_id', $gateways, old('gateway_id'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('gateway_id'))
                        <p class="help-block">
                            {{ $errors->first('gateway_id') }}
                        </p>
                    @endif
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
    </div>

    {!! Form::submit(trans('sendsms::global.send-sms.re-send'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

