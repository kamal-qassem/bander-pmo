<div class="col-md-{{$widget->columns}}">
    <div class="media state-media box-ws bg-3">
        <div class="media-left">
            <a href="{{ route('admin.expenses.index') }}"><div class="state-icn bg-icon-info"><i class="fa fa-list"></i></div></a>
        </div>
        <div class="media-body">
             <?php
                $expense_amount = \App\Expense::sum('amount');
            ?>
            <h4 class="card-title">{{ number_format($expense_amount,2) }}</h4>
            <a href="{{ route('admin.expenses.index') }}">@lang('custom.dashboard.total-expense')</a>
        </div>
    </div>
    <br/>
</div>

