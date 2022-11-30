@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.brands.title')</h3>
    
    {!! Form::model($brand, ['method' => 'PUT', 'route' => ['admin.brands.update', $brand->id], 'files' => true,'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.brands.form-fields')
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

