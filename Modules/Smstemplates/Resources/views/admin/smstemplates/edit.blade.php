@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('smstemplates::global.smstemplates.title')</h3>
    
    {!! Form::model($smstemplate, ['method' => 'PUT', 'route' => ['admin.smstemplates.update', $smstemplate->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('title', trans('smstemplates::global.smstemplates.fields.title').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Title', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
         
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('key', trans('smstemplates::global.smstemplates.fields.key').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('key', old('key'), ['class' => 'form-control', 'placeholder' => 'Key', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('key'))
                        <p class="help-block">
                            {{ $errors->first('key') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('content', trans('smstemplates::global.smstemplates.fields.content').'*', ['class' => 'control-label']) !!}
                    {!! Form::textarea('content', old('content'), ['class' => 'form-control ', 'placeholder' => 'Content', 'required' => '','rows'=>'4']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('content'))
                        <p class="help-block">
                            {{ $errors->first('content') }}
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

