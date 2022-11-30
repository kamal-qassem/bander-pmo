@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('sitethemes::global.site-themes.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.site_themes.store'], 'class' => 'formvalidation', 'files' => true]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    <div class="form-line">
                    {!! Form::label('theme', trans('sitethemes::global.site-themes.fields.title').'*', ['class' => 'control-label']) !!}
                    {!! Form::file('theme', ['class' => 'form-control', 'style' => 'margin-top: 4px;', 'accept' => '.zip']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('theme'))
                        <p class="help-block">
                            {{ $errors->first('theme') }}
                        </p>
                    @endif
                </div>
                    </div>
                </div>
            </div>
                        
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger waves-effect']) !!}
    {!! Form::close() !!}
@stop

