<div class="col-md-{{$widget->columns}}">
<div class="panel panel-default">
        <div class="panel-heading">@lang('others.dashboard.recent-expenses')</div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    
                    <th> @lang('global.expense.fields.entry-date')</th> 
                    <th> @lang('global.expense.fields.amount')</th> 
                    <th> @lang('global.expense.fields.payment-method')</th> 
                    <th> @lang('global.expense.fields.ref-no')</th> 
                    <th>&nbsp;</th>
                </tr>
                </thead>
                
                @foreach($expenses as $expense)
                    <tr>
                       
                        <td>{{ $expense->entry_date }} </td> 
                        <td>{{ $expense->amount }} </td> 
                        <td>{{ $expense->payment_method->name ?? ''}} </td> 
                        <td>{{ $expense->ref_no }} </td> 
                        <td>

                            @can('expense_view')
                            <a href="{{ route('admin.expenses.show',[$expense->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                            @endcan

                            @can('expense_edit')
                            <a href="{{ route('admin.expenses.edit',[$expense->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                            @endcan

                            @can('expense_delete')
                      {!! Form::open(array(
                                'style' => 'display: inline-block;',
                                'method' => 'DELETE',
                                'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                'route' => ['admin.expenses.destroy', $expense->id])) !!}
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