<div class="col-md-{{$widget->columns}}">
    <div class="media state-media box-ws bg-2">
        <div class="media-left">
            <a href="{{ route('admin.incomes.index') }}"><div class="state-icn bg-icon-info"><i class="fa fa-usd"></i></div></a>
        </div>
        <div class="media-body">
             <?php
                $income_amount = \App\Income::sum('amount');
            ?>
            <h4 class="card-title">{{ number_format($income_amount,2) }}</h4>
            <a href="{{ route('admin.incomes.index') }}">@lang('custom.dashboard.total-income')</a>
        </div>
    </div>
    <br/>
</div>

