@if( ! empty( $yearly_data['income_monthwise'] ) && $yearly_data['income_monthwise']->count() > 0 )
<div class="col-md-{{$widget->columns}}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">@lang('custom.messages.last-12-months-income')
                <ul class="rad-panel-action">
           
            <li><i class="fa fa-rotate-right"></i></li>
            
        </ul></h3>
        </div>
        <div class="panel-body">
            <div id="last12monthsincome" class="rad-chart"></div>
        </div>
    </div>
</div>

@section('javascript')
@parent
@include('dashboard-parts.last12monthsincome-scripts')
@endsection
@endif