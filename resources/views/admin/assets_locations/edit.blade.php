@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.assets-locations.title')</h3>
    
    {!! Form::model($assets_location, ['method' => 'PUT', 'route' => ['admin.assets_locations.update', $assets_location->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('title', trans('global.assets-locations.fields.title').'*', ['class' => 'control-label form-label']) !!}
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
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-primary wave-effect']) !!}
    {!! Form::close() !!}
@stop

