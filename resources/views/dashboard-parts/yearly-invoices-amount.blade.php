@if ( ! empty( $InvoicesYearsDataAreaChart ) )
<div class="col-md-{{$widget->columns}}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">@lang('custom.messages.yearly-invoices-amount')<ul class="rad-panel-action">
                                                
                                                <li><i class="fa fa-rotate-right"></i></li>
                                               
                                            </ul></h3>
        </div>
        <div class="panel-body">
            <div id="InvoicesYearsDataAreaChart" class="rad-chart"></div>
        </div>
    </div>
</div>

@section('javascript')
@parent
@include('dashboard-parts.yearly-invoices-amount-scripts')
@endsection

@endif