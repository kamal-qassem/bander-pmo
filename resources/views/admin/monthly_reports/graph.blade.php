<script src="{{ url('js/cdn-js-files/chartjs250') }}/Chart.min.js"></script>

<div class="row">
    <div class="col-md-6" style="padding:10px;">
        @include('admin.monthly_reports.income-bar-graph', compact( 'reportTitle', 'results', 'chartType', 'reportLabel', 'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense', 'reportTitle_income_pie', 'results_income_pie', 'chartType_income_pie', 'reportLabel_income_pie' ) )
    </div>

    <div class="col-md-6" style="padding:10px;">
        @include('admin.monthly_reports.expense-bar-graph', compact( 'reportTitle', 'results', 'chartType', 'reportLabel', 'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense', 'reportTitle_income_pie', 'results_income_pie', 'chartType_income_pie', 'reportLabel_income_pie' ) )
    </div>
</div>

<div class="row">
    <!-- Pie Graph -->
    <div class="col-md-6" style="padding:10px;">
        @include('admin.monthly_reports.income-pie-graph', compact( 'reportTitle', 'results', 'chartType', 'reportLabel', 'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense', 'reportTitle_income_pie', 'results_income_pie', 'chartType_income_pie', 'reportLabel_income_pie' ) )
    </div>

    <!-- Expense Pie Graph -->
    <div class="col-md-6" style="padding:10px;">
        @include('admin.monthly_reports.expense-pie-graph', compact( 'reportTitle', 'results', 'chartType', 'reportLabel', 'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense', 'reportTitle_income_pie', 'results_income_pie', 'chartType_income_pie', 'reportLabel_income_pie' ) )
    </div>
</div>
