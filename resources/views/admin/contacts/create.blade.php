@extends('layouts.app')

@section('content')
    <h3 class="page-title">
    <?php
    if ( empty( $type ) ) {
      $type = CUSTOMERS_TYPE;
    }
    $details = \App\ContactType::find( $type );
    if ( $details ) {
        echo $details->name;
    } else {
        echo trans('global.contacts.title');
    }
    ?>
    </h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.contacts.store'], 'files' => true,'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            @include('admin.contacts.form-fields')
        </div>
    </div>


    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
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