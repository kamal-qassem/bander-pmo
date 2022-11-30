@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('sitethemes::global.site-themes.title')</h3>
    
    {!! Form::model($site_theme, ['method' => 'PUT', 'route' => ['admin.site_themes.update', $site_theme->id], 'class' => 'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    <div class="form-line">
                    {!! Form::label('title', trans('sitethemes::global.site-themes.fields.title').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                    </div>
                </div>
          
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('theme_title_key', trans('sitethemes::global.site-themes.fields.theme-title-key').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('theme_title_key', old('theme_title_key'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('theme_title_key'))
                        <p class="help-block">
                            {{ $errors->first('theme_title_key') }}
                        </p>
                    @endif
                </div>
                </div>
           
                <div class="col-xs-{{COLUMNS}}">
                   <div class="form-group">
                    {!! Form::label('theme_color', trans('sitethemes::global.site-themes.fields.theme-color').'', ['class' => 'control-label']) !!}
                    {!! Form::text('theme_color', old('theme_color'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('theme_color'))
                        <p class="help-block">
                            {{ $errors->first('theme_color') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('is_active', trans('sitethemes::global.site-themes.fields.is-active').'', ['class' => 'control-label']) !!}
                    {!! Form::select('is_active', $enum_is_active, old('is_active'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('is_active'))
                        <p class="help-block">
                            {{ $errors->first('is_active') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>

              


            <div class="row">
                <div class="col-xs-6">
                     <div class="form-group">
                    {!! Form::label('description',trans('sitethemes::global.site-themes.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::text('description', old('description'), ['class' => 'form-control', 'placeholder' => '']) !!}
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

