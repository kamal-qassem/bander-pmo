@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.invoices.title')</h3>
    
    {!! Form::model($invoice, ['method' => 'PUT', 'route' => ['admin.invoices.update', $invoice->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            @include('admin.invoices.form-fields')  
             {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect', 'name' => 'btnsave']) !!}&nbsp
    {!! Form::submit(trans('custom.common.update-manage'), ['class' => 'btn btn-info wave-effect', 'name' => 'btnsavemanage', 'value' => 'savemanage']) !!}
    {!! Form::submit(trans('custom.common.update-send'), ['class' => 'btn btn-success wave-effect', 'name' => 'btnsavesend', 'value' => 'savesend']) !!}          
        </div>

    </div>

    
    {!! Form::close() !!}

    @include('admin.common.modal-loading-submit')
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

    @include('admin.common.scripts', array('products_return' => $invoice))
    @include('admin.common.modal-scripts')
    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>

    <script>
        $("#selectbtn-allowed_paymodes").click(function(){
            $("#selectall-allowed_paymodes > option").prop("selected","selected");
            $("#selectall-allowed_paymodes").trigger("change");
        });
        $("#deselectbtn-allowed_paymodes").click(function(){
            $("#selectall-allowed_paymodes > option").prop("selected","");
            $("#selectall-allowed_paymodes").trigger("change");
        });
    </script>
            
@stop