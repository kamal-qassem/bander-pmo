@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('modulesmanagement::global.modules-management.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.plugins.upload_store'], 'files' => true]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('modulesmanagement::global.modules-management.upload')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    <div class="form-line">
                    {!! Form::label('plugin', trans('modulesmanagement::global.modules-management.fields.title').'*', ['class' => 'control-label']) !!}
                    {!! Form::file('plugin', ['class' => 'form-control', 'style' => 'margin-top: 4px;', 'accept' => '.zip']) !!}
                    <p class="help-block">@lang('modulesmanagement::global.modules-management.upload-instruction')</p>
                    @if($errors->has('plugin'))
                        <p class="help-block">
                            {{ $errors->first('plugin') }}
                        </p>
                    @endif
                    </div>
                </div>
            </div>                        
        </div>

    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop