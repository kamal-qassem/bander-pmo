@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contacts.title_contact')</h3>
    {!! Form::model($contact, ['method' => 'PUT', 'route' => ['admin.contacts.update', $contact->id], 'files' => true,'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.contacts.form-fields')
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}

    @include('admin.common.modal-loading-submit')
@stop

@section('javascript')
    @parent

    <script>
        $("#selectbtn-contact_type").click(function(){
            $("#selectall-contact_type > option").prop("selected","selected");
            $("#selectall-contact_type").trigger("change");
        });
        $("#deselectbtn-contact_type").click(function(){
            $("#selectall-contact_type > option").prop("selected","");
            $("#selectall-contact_type").trigger("change");
        });
    </script>

    <script>
        $("#selectbtn-language").click(function(){
            $("#selectall-language > option").prop("selected","selected");
            $("#selectall-language").trigger("change");
        });
        $("#deselectbtn-language").click(function(){
            $("#selectall-language > option").prop("selected","");
            $("#selectall-language").trigger("change");
        });
    </script>
    @include('admin.common.modal-scripts')
@stop
