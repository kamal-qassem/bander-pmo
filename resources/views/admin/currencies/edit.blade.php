@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.currencies.title')</h3>
    
    {!! Form::model($currency, ['method' => 'PUT', 'route' => ['admin.currencies.update', $currency->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <p style="padding: 10px;">@lang('custom.currencies.currency_layer_message', ['url' => '<a href="https://currencylayer.com" target="_blank">https://currencylayer.com</a>', 'settings_url' => '<a href="'.url('admin/mastersettings/settings/view/currency-settings').'" target="_blank">here</a>'])</p>
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('name', trans('global.currencies.fields.name').'*', ['class' => 'control-label form-label']) !!}
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
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('symbol', trans('global.currencies.fields.symbol').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('symbol', old('symbol'), ['class' => 'form-control', 'placeholder' => 'Symbol', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('symbol'))
                        <p class="help-block">
                            {{ $errors->first('symbol') }}
                        </p>
                    @endif
                </div>
                    </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('code', trans('global.currencies.fields.code').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('code', old('code'), ['class' => 'form-control', 'placeholder' => 'Code', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('code'))
                        <p class="help-block">
                            {{ $errors->first('code') }}
                        </p>
                    @endif
                </div>
                    </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('rate', trans('global.currencies.fields.rate').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::number('rate', old('rate'), ['class' => 'form-control', 'placeholder' => 'Rate', 'required' => '','min'=>'1','step'=>'0.01']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('rate'))
                        <p class="help-block">
                            {{ $errors->first('rate') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('status', trans('global.currencies.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $enum_status, old('status'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    
                    {!! Form::label('is_default', trans('global.currencies.fields.is_default').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('is_default', $enum_is_default, old('is_default'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('is_default'))
                        <p class="help-block">
                            {{ $errors->first('is_default') }}
                        </p>
                    @endif
                </div>
                </div>
           
                <div class="col-xs-4">
                <div class="form-group">
                    
                    {!! Form::label('update_currency_online', trans('custom.currencies.update_currency_online'), ['class' => 'control-label']) !!}
                    {!! Form::select('update_currency_online', $enum_is_default, 'no', ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('update_currency_online'))
                        <p class="help-block">
                            {{ $errors->first('update_currency_online') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

