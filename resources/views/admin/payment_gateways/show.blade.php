@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.payment-gateways.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
               
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>    
<li role="presentation" class=""><a href="#transfers" aria-controls="transfers" role="tab" data-toggle="tab">@lang('global.transfers.title')</a></li>
<li role="presentation" class=""><a href="#income" aria-controls="income" role="tab" data-toggle="tab">@lang('global.income.title')</a></li>
<li role="presentation" class=""><a href="#expense" aria-controls="expense" role="tab" data-toggle="tab">@lang('global.expense.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
 
   <div role="tabpanel" class="tab-pane active" id="details">

         <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.payment-gateways.fields.name')</th>
                            <td field-key='name'>{{ $payment_gateway->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.payment-gateways.fields.description')</th>
                            <td field-key='description'>{{ $payment_gateway->description }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.payment-gateways.fields.logo')</th>
                            <td field-key='logo'>@if($payment_gateway->logo)<a href="{{ asset(env('UPLOAD_PATH').'/' . $payment_gateway->logo) }}" target="_blank"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $payment_gateway->logo) }}"/></a>@endif</td>
                        </tr>
                    </table>

    </div>

<div role="tabpanel" class="tab-pane" id="transfers">
<table class="table table-bordered table-striped {{ count($transfers) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.transfers.fields.from')</th>
                        <th>@lang('global.transfers.fields.to')</th>
                        <th>@lang('global.transfers.fields.date')</th>
                        <th>@lang('global.transfers.fields.amount')</th>
                        <th>@lang('global.transfers.fields.ref-no')</th>
                        <th>@lang('global.transfers.fields.payment-method')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
        </tr>
    </thead>

    <tbody>
        @if (count($transfers) > 0)
            @foreach ($transfers as $transfer)
                <tr data-entry-id="{{ $transfer->id }}">
                    <td field-key='from'>{{ $transfer->from->name ?? '' }}</td>
                                <td field-key='to'>{{ $transfer->to->name ?? '' }}</td>
                                <td field-key='date'>{{ $transfer->date }}</td>
                                <td field-key='amount'>{{ digiCurrency($transfer->amount) }}</td>
                                <td field-key='ref_no'>{{ $transfer->ref_no }}</td>
                                <td field-key='payment_method'>{{ $transfer->payment_method->name ?? '' }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.transfers.restore', $transfer->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.transfers.perma_del', $transfer->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td>
                                    @can('transfer_view')
                                    <a href="{{ route('admin.transfers.show',[$transfer->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('transfer_edit')
                                    <a href="{{ route('admin.transfers.edit',[$transfer->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('transfer_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.transfers.destroy', $transfer->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
<div role="tabpanel" class="tab-pane " id="income">
<table class="table table-bordered table-striped {{ count($incomes) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.income.fields.account')</th>
                        <th>@lang('global.income.fields.income-category')</th>
                        <th>@lang('global.income.fields.entry-date')</th>
                        <th>@lang('global.income.fields.amount')</th>
                        <th>@lang('global.income.fields.payer')</th>
                        <th>@lang('global.income.fields.pay-method')</th>
                                                <th>&nbsp;</th>

        </tr>
    </thead>

    <tbody>
        @if (count($incomes) > 0)
            @foreach ($incomes as $income)
                <tr data-entry-id="{{ $income->id }}">
                    <td field-key='account'>{{ $income->account->name ?? '' }}</td>
                                <td field-key='income_category'>{{ $income->income_category->name ?? '' }}</td>
                                <td field-key='entry_date'>{{ $income->entry_date }}</td>
                                <td field-key='amount'>{{ digiCurrency($income->amount) }}</td>
                                <td field-key='payer'>{{ $income->payer->first_name ?? '' }}</td>
                                <td field-key='pay_method'>{{ $income->pay_method->name ?? '' }}</td>
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
        @else
            <tr>
                <td colspan="14">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
<div role="tabpanel" class="tab-pane " id="expense">
<table class="table table-bordered table-striped {{ count($expenses) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.expense.fields.account')</th>
                        <th>@lang('global.expense.fields.expense-category')</th>
                        <th>@lang('global.expense.fields.entry-date')</th>
                        <th>@lang('global.expense.fields.amount')</th>
                        <th>@lang('global.expense.fields.payee')</th>
                        <th>@lang('global.expense.fields.payment-method')</th>
                                                <th>&nbsp;</th>

        </tr>
    </thead>

    <tbody>
        @if (count($expenses) > 0)
            @foreach ($expenses as $expense)
                <tr data-entry-id="{{ $expense->id }}">
                    <td field-key='account'>{{ $expense->account->name ?? '' }}</td>
                                <td field-key='expense_category'>{{ $expense->expense_category->name ?? '' }}</td>
                                <td field-key='entry_date'>{{ $expense->entry_date }}</td>
                                <td field-key='amount'>{{ digiCurrency($expense->amount,$expense->currency_id) }}</td>
                                <td field-key='payee'>{{ $expense->payee->first_name ?? '' }}</td>
                                <td field-key='payment_method'>{{ $expense->payment_method->name ?? '' }}</td>
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
        @else
            <tr>
                <td colspan="14">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.payment_gateways.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


