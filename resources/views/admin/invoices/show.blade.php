@extends('layouts.app')

@section('content')
    @if( ! empty( $invoice->project_id ) )
            @include('admin.client_projects.operations.menu', ['client_project' => \App\ClientProject::find($invoice->project_id)])
        @endif
        
    <h3 class="page-title">@lang('global.invoices.title')</h3>

    <div class="panel panel-default">

        <div class="panel-heading">
            @lang('global.app_view')
        </div>


        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-9">
                    @include('admin.invoices.invoice.invoice', compact('invoice'))
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.invoices.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

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
            
@stop
