@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.master-settings.title')</h3>
    
    {!! Form::model($master_setting, ['method' => 'PUT', 'route' => ['admin.master_settings.update', $master_setting->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('module', trans('global.master-settings.fields.module').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('module', old('module'), ['class' => 'form-control', 'placeholder' => 'Module', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('module'))
                        <p class="help-block">
                            {{ $errors->first('module') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('key', trans('global.master-settings.fields.key').'*', ['class' => 'control-label form-label']) !!}
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
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('moduletype', trans('custom.settings.moduletype').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('moduletype', $enum_moduletype, old('moduletype'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('moduletype'))
                        <p class="help-block">
                            {{ $errors->first('moduletype') }}
                        </p>
                    @endif
                </div>
                </div>
                @php
                $statuses = array(
                    'Active' => trans( 'custom.common.active' ),
                'Inactive' => trans( 'custom.common.inactive' ),
                );
                @endphp
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('status', trans('custom.settings.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status', $statuses, old('status'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status'))
                        <p class="help-block">
                            {{ $errors->first('status') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">

                    {!! Form::label('description', trans('global.master-settings.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Description','rows'=>'4']) !!}
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

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

