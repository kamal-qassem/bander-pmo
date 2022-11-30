@extends('layouts.app')

@section('content')
    <h3 class="page-title">Monthly report</h3>

    {!! Form::open(['method' => 'get']) !!}
        <div class="row">
            <div class="col-xs-2 col-md-2" style="margin-left:15px;">
            <div class="form-group">
                {!! Form::label('year','Year',['class' => 'control-label']) !!}
                {!! Form::select('y', array_combine(range(date("Y"), 1900), range(date("Y"), 1900)), old('y', Request::get('y', date('Y'))), ['class' => 'form-control', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
            </div>
        </div>
            <div class="col-xs-2 col-md-2">
            <div class="form-group">
                {!! Form::label('month','Month',['class' => 'control-label']) !!}
                {!! Form::select('m', cal_info(0)['months'], old('m', Request::get('m', date('m'))), ['class' => 'form-control', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
            </div>
        </div>
            <div class="col-xs-4 col-md-4" style="padding-top:6px;"><br/>
                <label class="control-label">&nbsp;</label>
                {!! Form::submit('View Report',['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        <br/>

    <div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-heading">
            Report
        </div>
        {!! Form::close() !!}
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Income</th>
                            <td>{{ digiCurrency($inc_total) }}</td>
                        </tr>
                        <tr>
                            <th>Expenses</th>
                            <td>{{ digiCurrency($exp_total) }}</td>
                        </tr>
                        <tr>
                            <th>Profit</th>
                            <td>{{ digiCurrency($profit) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Income by category</th>
                            <th>{{ digiCurrency($inc_total) }}</th>
                        </tr>
                    @foreach($inc_summary as $inc)
                        <tr>
                            <th>{{ $inc['name'] }}</th>
                            <td>{{ digiCurrency($inc['amount']) }}</td>
                        </tr>
                    @endforeach
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Expenses by category</th>
                            <th>{{ digiCurrency($exp_total) }}</th>
                        </tr>
                    @foreach($exp_summary as $inc)
                        <tr>
                            <th>{{ $inc['name'] }}</th>
                            <td>{{ digiCurrency($inc['amount']) }}</td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    
    @include('admin.monthly_reports.graph', compact( 'reportTitle', 'results', 'chartType', 'reportLabel', 'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense' ) )
@stop