 @if( ! empty( $yearly_data['expenses'] ) && $yearly_data['expenses']->count() > 0 )
  <div class="col-md-{{$widget->columns}}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">@lang('custom.messages.expense-category')<ul class="rad-panel-action">
                                                
                                                <li><i class="fa fa-rotate-right"></i></li>
                                                
                                            </ul></h3>
        </div>
        <div class="panel-body">
            <div id="expensecategorydonutChart" class="rad-chart"></div>
        </div>
    </div>
</div>

@section('javascript')
@parent
@include('dashboard-parts.expensecategorydonutChart-scripts')
@endsection
@endif