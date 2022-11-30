@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contact-groups.title')</h3>
    
    {!! Form::model($contact_group, ['method' => 'PUT', 'route' => ['admin.contact_groups.update', $contact_group->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.contact_groups.form-fields')            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

