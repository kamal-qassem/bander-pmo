@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.taxes.title')</h3>
    
    {!! Form::model($tax, ['method' => 'PUT', 'route' => ['admin.taxes.update', $tax->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.taxes.form-fields')
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

