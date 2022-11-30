@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.warehouses.title')</h3>
    
    {!! Form::model($warehouse, ['method' => 'PUT', 'route' => ['admin.warehouses.update', $warehouse->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.warehouses.form-fields')
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

