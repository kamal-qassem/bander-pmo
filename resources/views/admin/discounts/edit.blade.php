@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.discounts.title')</h3>
    
    {!! Form::model($discount, ['method' => 'PUT', 'route' => ['admin.discounts.update', $discount->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.discounts.form-fields')
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

