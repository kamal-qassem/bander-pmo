@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.expense-category.title')</h3>
    
    {!! Form::model($expense_category, ['method' => 'PUT', 'route' => ['admin.expense_categories.update', $expense_category->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.expense_categories.form-fields')            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

