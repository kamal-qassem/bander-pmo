<div class="col-md-{{ ! empty($widget->columns) ? $widget->columns : 2 }}">
<div class="panel panel-default">
    <div class="panel-heading">@lang('others.dashboard.recent-invoices')</div>

    <div class="panel-body table-responsive">
        <table class="table table-bordered table-striped ajaxTable">
            <thead>
            <tr>
                
                <th> @lang('global.invoices.fields.amount')</th> 
                <th> @lang('global.invoices.fields.address')</th> 
                <th> @lang('global.invoices.fields.invoice-date')</th> 
                <th> @lang('global.invoices.fields.invoice-due-date')</th> 
                <th> @lang('global.invoices.fields.invoice-no')</th> 
                <th>&nbsp;</th>
            </tr>
            </thead>
            @foreach($invoices as $invoice)
                <tr>
                   
                    <td>{{ digiCurrency($invoice->amount,$invoice->currency_id) }} </td> 
                    <td>{!! clean($invoice->customer->first_name) . '<br>' . clean($invoice->address) !!} </td> 
                    <td>{{ $invoice->invoice_date ? digiDate( $invoice->invoice_date ) : ''}} </td> 
                    <td>{{ $invoice->invoice_due_date ? digiDate( $invoice->invoice_due_date ) : ''}} </td> 
                    <td>{{ $invoice->invoice_no }} </td> 
                    <td>

                        @can('invoice_view')
                        <a href="{{ route('admin.invoices.show',[$invoice->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                        @endcan

                        @can('invoice_edit')
                        <a href="{{ route('admin.invoices.edit',[$invoice->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                        @endcan

                        @can('invoice_delete')
{!! Form::open(array(
                            'style' => 'display: inline-block;',
                            'method' => 'DELETE',
                            'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                            'route' => ['admin.invoices.destroy', $invoice->id])) !!}
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