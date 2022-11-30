@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.countries.title')</h3>
    
    {!! Form::model($country, ['method' => 'PUT', 'route' => ['admin.countries.update', $country->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.countries.form-fields')            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

