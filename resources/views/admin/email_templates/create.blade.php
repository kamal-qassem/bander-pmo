@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.email-templates.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.email_templates.store'],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    <div class="form-line">
                    {!! Form::label('name', trans('global.email-templates.fields.name').'*', ['class' => 'control-label form-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <div class="form-line">
                    {!! Form::label('subject', trans('global.email-templates.fields.subject').'*', ['class' => 'control-label form-label']) !!}
                    {!! Form::text('subject', old('subject'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('subject'))
                        <p class="help-block">
                            {{ $errors->first('subject') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('body', trans('global.email-templates.fields.body').'*', ['class' => 'control-label']) !!}
                    {!! Form::textarea('body', old('body'), ['class' => 'form-control editor', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('body'))
                        <p class="help-block">
                            {{ $errors->first('body') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
    
   @include('admin.common.standard-ckeditor')

@stop