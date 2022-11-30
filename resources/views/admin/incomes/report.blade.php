<div class="row">

    <div class="col-md-3">
        
        <div class="card" style="width: 18rem;">
          <div class="card-body text-center">
            <div class="visual">
                <i class="fa fa-bar-chart"></i>
            </div>
            <h2 class="card-title">{{digiCurrency( $total_income )}}</h2>
            <?php
            $date_from = \App\Income::min('created_at');
            $date_to = \App\Income::max('created_at');
            ?>
            <h6 class="card-subtitle mb-2 text-muted"><b>{{digiDate( $date_from )}} - {{digiDate( $date_to )}}</b></h6>
            
            {{--<a href="{{route('admin.incomes.index')}}" class="card-link">@lang('custom.incomes.total-income')</a>--}}
            @lang('custom.incomes.total-income')
          </div>
        </div>

    </div>
    <div class="col-md-3">
        <div class="card" style="width: 18rem;">
          <div class="card-body text-center">
            <div class="visual">
                <i class="fa fa-line-chart"></i>
            </div>
            <h2 class="card-title">{{digiCurrency( $total_income_current_month )}}</h2>
            @php 
                $today = Carbon\Carbon::today();
                $date_from = date("Y-m-01", strtotime($today));
                $date_to = date("Y-m-t", strtotime($today)); 
            @endphp
            <h6 class="card-subtitle mb-2 text-muted"><b>{{digiDate( $date_from )}} - {{digiDate( $date_to )}}</b></h6>
            
            {{--<a href="{{route('admin.incomes.index')}}" class="card-link">@lang('custom.incomes.total-income-this-month')</a>--}}
            @lang('custom.incomes.total-income-this-month')
          </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="width: 18rem;">
          <div class="card-body text-center">
            <div class="visual">
                <i class="fa fa-calculator"></i>
            </div>
            <h2 class="card-title">{{digiCurrency( $total_income_current_week )}}</h2>
            @php 
                $date_from = Carbon\Carbon::today()->startOfWeek();
                $date_to = Carbon\Carbon::today()->endOfWeek();
            @endphp
            <h6 class="card-subtitle mb-2 text-muted"><b>{{digiDate( $date_from )}} - {{digiDate( $date_to )}}</b></h6>
            
            {{--<a href="{{route('admin.incomes.index')}}" class="card-link">@lang('custom.incomes.total-income-this-week')</a>--}}
            @lang('custom.incomes.total-income-this-week')
          </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="width: 18rem;">
          <div class="card-body text-center">
            <div class="visual">
                <i class="fa fa-columns"></i>
            </div>
            <h2 class="card-title">{{digiCurrency( $total_income_last_30_days )}}</h2>
            @php 
                $date_from = Carbon\Carbon::today()->subDays(30);
                $date_to = Carbon\Carbon::today();
            @endphp
            <h6 class="card-subtitle mb-2 text-muted"><b>{{digiDate( $date_from )}} - {{digiDate( $date_to )}}</b></h6>
            
            {{--<a href="{{route('admin.incomes.index')}}" class="card-link">@lang('custom.incomes.total-income-last-30days')</a>--}}
            @lang('custom.incomes.total-income-last-30days')
          </div>
        </div>
    </div>
</div>