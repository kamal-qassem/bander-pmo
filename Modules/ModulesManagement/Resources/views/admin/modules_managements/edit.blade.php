@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('modulesmanagement::global.modules-management.title')</h3>
    
    {!! Form::model($modules_management, ['method' => 'PUT', 'route' => ['admin.modules_managements.update', $modules_management->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('name', trans('modulesmanagement::global.modules-management.fields.name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('slug', trans('modulesmanagement::global.modules-management.fields.slug').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('slug', old('slug'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('slug'))
                        <p class="help-block">
                            {{ $errors->first('slug') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('type', trans('modulesmanagement::global.modules-management.fields.type').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('type', $enum_type, old('type'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('type'))
                        <p class="help-block">
                            {{ $errors->first('type') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('enabled', trans('modulesmanagement::global.modules-management.fields.enabled').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('enabled', $enum_enabled, old('enabled'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('enabled'))
                        <p class="help-block">
                            {{ $errors->first('enabled') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('description', trans('modulesmanagement::global.modules-management.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => '']) !!}
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

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

@stop