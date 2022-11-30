@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.dynamic-options.title')</h3>
    
    {!! Form::model($dynamic_option, ['method' => 'PUT', 'route' => ['admin.dynamic_options.update', $dynamic_option->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('title', trans('global.recurring-periods.fields.title').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                </div>
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('value', trans('global.dynamic-options.fields.module').'*', ['class' => 'control-label']) !!}
                    <?php
                    $recurring_types = array(
                        '' => trans('global.app_please_select'),
                        'quotes' => trans('global.dynamic-options.quotes'),
                        'proposals' => trans('global.dynamic-options.proposals'),
                        'contracts' => trans('global.dynamic-options.contracts'),
                        'invoices' => trans('global.dynamic-options.invoices'),
                        
                        'projecttasks' => trans('global.dynamic-options.project-tasks'),
                    );
                    ?>                    
                    {!! Form::select('module', $recurring_types, old('module'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('module'))
                        <p class="help-block">
                            {{ $errors->first('module') }}
                        </p>
                    @endif
                </div>
                </div>
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('type', trans('global.recurring-periods.fields.type').'*', ['class' => 'control-label']) !!}
                    <?php
                    $recurring_types = array(
                        '' => trans('global.app_please_select'),
                        'priorities' => trans('global.dynamic-options.priorities'),
                        'taskstatus' => trans('global.dynamic-options.taskstatus'),
                        
                    );
                    ?>
                    
                    {!! Form::select('type', $recurring_types, old('type'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'required' => '']) !!}
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
                    {!! Form::label('color', 'Color', ['class' => 'control-label']) !!}
                    {!! Form::text('color', old('color'), ['class' => 'form-control colorpicker', 'placeholder' => '', 'autocomplete' => 'off']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('color'))
                        <p class="help-block">
                            {{ $errors->first('color') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('description', trans('global.recurring-periods.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => '','rows'=>'4']) !!}
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

<link href="{{ url('css/cdn-styles-css/bootstrap/2.5.3/bootstrap-colorpicker.min.css') }}" rel="stylesheet">  

<script src="{{ url('js/cdn-js-files/bootstrap/2.5.3') }}/bootstrap-colorpicker.min.js"></script>

<script>
    $('.colorpicker').colorpicker();
</script>
@stop

