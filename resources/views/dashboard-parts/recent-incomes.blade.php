<div class="col-md-{{$widget->columns}}">
<div class="panel panel-default">
    <div class="panel-heading">@lang('others.dashboard.recent-incomes')</div>

    <div class="panel-body table-responsive">
        <table class="table table-bordered table-striped ajaxTable">
            <thead>
            <tr>
                
                <th> @lang('global.income.fields.entry-date')</th> 
                <th> @lang('global.income.fields.amount')</th> 
                <th> @lang('global.income.fields.payer')</th> 
                <th> @lang('global.income.fields.ref-no')</th> 
                <th>&nbsp;</th>
            </tr>
            </thead>
            @foreach($incomes as $income)
                <tr>
                   
                    <td>{{ $income->entry_date }} </td> 
                    <td>{{ digiCurrency($income->amount,$income->original_currency_id) }} </td> 
                    <td>{{ $income->payer->name }} </td> 
                    <td>{{ $income->ref_no }} </td> 
                    <td>

                        @can('income_view')
                        <a href="{{ route('admin.incomes.show',[$income->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                        @endcan

                        @can('income_edit')
                        <a href="{{ route('admin.incomes.edit',[$income->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                        @endcan

                        @can('income_delete')
{!! Form::open(array(
                            'style' => 'display: inline-block;',
                            'method' => 'DELETE',
                            'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                            'route' => ['admin.incomes.destroy', $income->id])) !!}
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