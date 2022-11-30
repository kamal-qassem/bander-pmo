@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.languages.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.languages.store'],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('language', trans('global.languages.fields.language').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('language', old('language'), ['class' => 'form-control', 'placeholder' => 'Language', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('language'))
                        <p class="help-block">
                            {{ $errors->first('language') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
           
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('code', trans('global.languages.fields.code').'*', ['class' => 'control-label form-label']) !!}
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
         
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('is_rtl', trans('global.languages.fields.is-rtl').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('is_rtl', $enum_is_rtl, old('is_rtl'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('is_rtl'))
                        <p class="help-block">
                            {{ $errors->first('is_rtl') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

