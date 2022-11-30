@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contact-notes.title')</h3>
    
    {!! Form::model($contact_note, ['method' => 'PUT', 'route' => ['admin.contact_notes.update', $contact_note->id], 'files' => true,'class'=>'formvalidation']) !!}
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('title', trans('global.contact-notes.fields.title').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'title', 'required' => '']) !!}
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
                    {!! Form::label('contact_id', trans('global.contact-notes.fields.contact').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('contact_id', $contacts, old('contact_id'), ['class' => 'form-control select2', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('contact_id'))
                        <p class="help-block">
                            {{ $errors->first('contact_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('notes', trans('global.contact-notes.fields.notes').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', old('notes'), ['class' => 'form-control editor', 'placeholder' => 'notes']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('notes'))
                        <p class="help-block">
                            {{ $errors->first('notes') }}
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