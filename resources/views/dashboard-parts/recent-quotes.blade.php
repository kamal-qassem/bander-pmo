<div class="col-md-{{ ! empty($widget->columns) ? $widget->columns : 2 }}">
<div class="panel panel-default">
    <div class="panel-heading">@lang('others.dashboard.recent-quotes')</div>

    <div class="panel-body table-responsive">
        <table class="table table-bordered table-striped ajaxTable">
            <thead>
            <tr>
                
                <th> @lang('global.recurring-invoices.fields.customer')</th> 
                <th> @lang('global.quotes.fields.quote-date')</th> 
                <th> @lang('global.quotes.fields.quote-expiry-date')</th> 
                <th> @lang('global.recurring-invoices.fields.amount')</th> 
                <th> @lang('global.quotes.fields.quote-no')</th> 
                <th>&nbsp;</th>
            </tr>
            </thead>
            @foreach($quotes as $quote)
                <tr>
                   
                    <td>{{ $quote->customer->name }} </td> 
                    <td>{{ $quote->invoice_date ? digiDate( $quote->invoice_date ) : ''}} </td> 
                    <td>{{ $quote->invoice_due_date  ?  digiDate( $quote->invoice_due_date ) : ''}} </td> 
                    <td>{{ digiCurrency($quote->amount,$quote->currency_id) }} </td> 
                    <td>{{ $quote->invoice_no }} </td> 
                    <td>

                        @can('quote_view')
                        <a href="{{ route('admin.quotes.show',[$quote->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                        @endcan

                        @can('quote_edit')
                        <a href="{{ route('admin.quotes.edit',[$quote->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                        @endcan

                        @can('quote_delete')
{!! Form::open(array(
                            'style' => 'display: inline-block;',
                            'method' => 'DELETE',
                            'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                            'route' => ['admin.quotes.destroy', $quote->id])) !!}
                        {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                        {!! Form::close() !!}
                        @endcan
                    
</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
</div>