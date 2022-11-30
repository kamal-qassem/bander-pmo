@extends('layouts.app')
@section('content')
<h3 class="page-title">@lang('global.purchase-orders.title')</h3>
{!! Form::model($invoice, ['method' => 'PUT', 'route' => ['admin.purchase_orders.update', $invoice->id],'class'=>'formvalidation']) !!}
<div class="panel panel-default">
   <div class="panel-heading">
      @lang('global.app_edit')
   </div>
   <div class="panel-body">
      @include('admin.purchase_orders.form-fields')
   </div>

</div>
   
   {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}

{!! Form::submit(trans('custom.common.update-manage'), ['class' => 'btn btn-info wave-effect', 'name' => 'btnsavemanage', 'value' => 'savemanage']) !!}

{!! Form::close() !!}

@include('admin.common.modal-loading-submit')
@stop
@section('javascript')
@parent

@include('admin.common.standard-ckeditor')

@include('admin.common.scripts')
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
@include('admin.common.modal-scripts')
@stop