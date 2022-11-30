@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.expense.title')</h3>
    @can('expense_create')
    <p>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>        
    </p>
    @endcan

    <button data-toggle="collapse" data-target="#report">@lang('custom.incomes.show-report')</button>
    <div id="report" class="collapse">
        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('custom.expense.summary-in') <b>{{getDefaultCurrency()}}</b>
            </div>
            <div class="panel-body table-responsive">
                @include('admin.expenses.report', compact( 'total_expense', 'total_expense_current_month', 'total_expense_current_week', 'total_expense_last_30_days' ) )
            </div>
        </div>
    </div>

    <button data-toggle="collapse" data-target="#report-graph">@lang('custom.incomes.show-graph')</button>
    <div id="report-graph" class="collapse">
        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('custom.expense.summary-in') <b>{{getDefaultCurrency()}}</b>
            </div>
            <div class="panel-body table-responsive">
                <script src="{{ url('js/cdn-js-files/chartjs250') }}/Chart.min.js"></script>
                @include('admin.monthly_reports.expense-bar-graph', compact( 'reportTitle_expense', 'reportLabel_expense', 'chartType_expense', 'results_expense' ) )
            </div>
        </div>
    </div>

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.expenses.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.expenses.records-display-scripts')
@endsection