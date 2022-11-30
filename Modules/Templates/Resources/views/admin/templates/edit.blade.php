@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('templates::global.templates.title')</h3>
    
    {!! Form::model($template, ['method' => 'PUT', 'route' => ['admin.templates.update', $template->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('title', trans('templates::global.templates.fields.title').'*', ['class' => 'control-label form-label']) !!}
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
                    {!! Form::label('key', trans('templates::global.templates.fields.key').'*', ['class' => 'control-label form-label']) !!}
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
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('type', trans('templates::global.templates.fields.type').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('type', $enum_type, old('type'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('type'))
                        <p class="help-block">
                            {{ $errors->first('type') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('subject', trans('templates::global.templates.fields.subject').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('subject', old('subject'), ['class' => 'form-control', 'placeholder' => 'Subject', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('subject'))
                        <p class="help-block">
                            {{ $errors->first('subject') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('from_email', trans('templates::global.templates.fields.from-email'), ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('from_email', old('from_email'), ['class' => 'form-control', 'placeholder' => 'From Email']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('from_email'))
                        <p class="help-block">
                            {{ $errors->first('from_email') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('from_name', trans('templates::global.templates.fields.from-name'), ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('from_name', old('from_name'), ['class' => 'form-control', 'placeholder' => 'From Name']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('from_name'))
                        <p class="help-block">
                            {{ $errors->first('from_name') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('content', trans('templates::global.templates.fields.content').'*', ['class' => 'control-label']) !!}
                    {!! Form::textarea('content', old('content'), ['class' => 'form-control editor', 'placeholder' => 'Content', 'required' => '']) !!}
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

@section('javascript')
    @parent
    
   @include('admin.common.standard-ckeditor')

@stop