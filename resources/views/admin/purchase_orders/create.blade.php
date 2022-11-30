@extends('layouts.app')
@section('content')
<h3 class="page-title">@lang('global.purchase-orders.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.purchase_orders.store'],'class'=>'formvalidation']) !!}
<div class="panel panel-default">
   <div class="panel-heading">
      @lang('global.app_create')
   </div>
   <div class="panel-body">
      @include('admin.purchase_orders.form-fields')
   </div>

</div>
  
{!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
{!! Form::submit(trans('custom.common.save-manage'), ['class' => 'btn btn-info wave-effect', 'name' => 'btnsavemanage', 'value' => 'savemanage']) !!}  

{!! Form::close() !!}

@include('admin.common.modal-loading-submit')
@stop
@section('javascript')
@parent

@include('admin.common.standard-ckeditor')

<script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
@include('admin.common.scripts')
@include('admin.purchase_orders.modal-scripts')
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
@stop