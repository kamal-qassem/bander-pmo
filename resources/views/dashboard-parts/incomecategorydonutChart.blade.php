 @if( ! empty( $yearly_data['income'] ) && $yearly_data['income']->count() > 0)
 <div class="col-md-{{$widget->columns}}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">@lang('custom.messages.income-category')<ul class="rad-panel-action">
                                                
                                                <li><i class="fa fa-rotate-right"></i></li>
                                                
                                            </ul></h3>
        </div>
        <div class="panel-body">
            <div id="incomecategorydonutChart" class="rad-chart"></div>
        </div>
    </div>
</div>

@section('javascript')
@parent
@include('dashboard-parts.incomecategorydonutChart-scripts')
@endsection
@endif