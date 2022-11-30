@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu')
    <h3 class="page-title">@lang('global.client-projects.ticket')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.project_tickets.store', $client_project->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            @include('admin.project_tickets.form-fields')
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
     @include('admin.common.standard-ckeditor')
@stop