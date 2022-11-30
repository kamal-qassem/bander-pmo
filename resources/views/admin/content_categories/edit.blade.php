@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.content-categories.title')</h3>
    
    {!! Form::model($content_category, ['method' => 'PUT', 'route' => ['admin.content_categories.update', $content_category->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('title', trans('global.content-categories.fields.title').'', ['class' => 'control-label form-label']) !!}
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
                    {!! Form::label('slug', trans('global.content-categories.fields.slug').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('slug', old('slug'), ['class' => 'form-control', 'placeholder' => 'Slug']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('slug'))
                        <p class="help-block">
                            {{ $errors->first('slug') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

