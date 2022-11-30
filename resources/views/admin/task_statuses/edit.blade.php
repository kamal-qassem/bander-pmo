@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.task-statuses.title')</h3>
    
    {!! Form::model($task_status, ['method' => 'PUT', 'route' => ['admin.task_statuses.update', $task_status->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('name', trans('global.task-statuses.fields.name').'*', ['class' => 'control-label form-label']) !!}
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
         
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('color', trans('global.task-statuses.fields.color').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('color', $colors, old('color'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('color'))
                        <p class="help-block">
                            {{ $errors->first('color') }}
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

