@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{$currency->name . '('.$currency->symbol.')'}}</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <?php
        $tabs = [
            'details_active' => 'active',
            'invoices_active' => '',
            'quotes_active' => '',
            'recurring_invoices_active' => '',
            'purchase_orders_active' => '',
            'credit_notes_active' => '',
        ];
        
        if ( ! empty( $list ) ) {
            foreach ($tabs as $key => $value) {
                $tabs[ $key ] = '';
                if ( substr( $key, 0, -7) == $list ) {
                    $tabs[ $key ] = 'active';
                }
            }
        }
        ?>

        <div class="panel-body table-responsive">

<ul class="nav nav-tabs" role="tablist">
 
    <li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li> 
    @if( isPluginActive('invoice') )
    <li role="presentation" class="{{$tabs['invoices_active']}}"><a href="{{route('admin.currencies.show', [ 'currency_id' => $currency->id, 'list' => 'invoices' ])}}">@lang('others.canvas.invoices')</a></li>
    @endif
    @if( isPluginActive('quotes') )
    <li role="presentation" class="{{$tabs['quotes_active']}}"><a href="{{route('admin.currencies.show', [ 'currency_id' => $currency->id, 'list' => 'quotes' ])}}">@lang('global.quotes.title')</a></li>
    @endif
    @if( isPluginActive('recurringinvoices') )
    <li role="presentation" class="{{$tabs['recurring_invoices_active']}}"><a href="{{route('admin.currencies.show', [ 'contact_id' => $currency->id, 'list' => 'recurring_invoices' ])}}">@lang('global.recurring-invoices.title')</a></li>
    @endif
    @if( isPluginActive('purchase_order') )
    <li role="presentation" class="{{$tabs['purchase_orders_active']}}"><a href="{{route('admin.currencies.show', [ 'contact_id' => $currency->id, 'list' => 'purchase_orders' ])}}">@lang('global.purchase-orders.title')</a></li>
    @endif
    @if( isPluginActive('credit_note') )
    <li role="presentation" class="{{$tabs['credit_notes_active']}}"><a href="{{route('admin.currencies.show', [ 'contact_id' => $currency->id, 'list' => 'credit_notes' ])}}">@lang('global.credit_notes.title')</a></li>
    @endif
</ul>

<!-- Tab panes -->
<div class="tab-content">

  <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

        <div class="pull-right">
            @can('currency_edit')
                <a href="{{ route('admin.currencies.edit',[$currency->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
       </div>   

      <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.currencies.fields.name')</th>
                            <td field-key='name'>{{ $currency->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.currencies.fields.symbol')</th>
                            <td field-key='symbol'>{{ $currency->symbol }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.currencies.fields.code')</th>
                            <td field-key='code'>{{ $currency->code }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.currencies.fields.rate')</th>
                            <td field-key='rate'>{{ $currency->rate }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.currencies.fields.status')</th>
                            <td field-key='status'>{{ $currency->status }}</td>
                        </tr>
                    </table>

    </div> 
@if ( 'active' === $tabs['invoices_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['invoices_active']}}" id="invoices">
    @include('admin.invoices.records-display')
</div>
@endif

@if ( 'active' === $tabs['quotes_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['quotes_active']}}" id="quotes">
    @include('quotes::admin.quotes.records-display')
</div>
@endif

@if ( 'active' === $tabs['recurring_invoices_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['recurring_invoices_active']}}" id="recurring_invoices">
    @include('recurringinvoices::admin.recurring_invoices.records-display')
</div>
@endif

@if ( 'active' === $tabs['purchase_orders_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['purchase_orders_active']}}" id="purchase_orders">
    @include('admin.purchase_orders.records-display')
</div>
@endif

@if ( 'active' === $tabs['credit_notes_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['credit_notes_active']}}" id="credit_notes">
    @include('admin.credit_notes.records-display')
</div>
@endif

</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.currencies.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @if ( 'active' === $tabs['invoices_active'] )
        @include('admin.invoices.records-display-scripts', ['type' => 'currency', 'type_id' => $currency->id ])
    @endif

    @if ( 'active' === $tabs['quotes_active'] )
        @include('quotes::admin.quotes.records-display-scripts', ['type' => 'currency', 'type_id' => $currency->id ])
    @endif

    @if ( 'active' === $tabs['recurring_invoices_active'] )
        @include('recurringinvoices::admin.recurring_invoices.records-display-scripts', ['type' => 'currency', 'type_id' => $currency->id ])
    @endif

    @if ( 'active' === $tabs['purchase_orders_active'] )
        @include('admin.purchase_orders.records-display-scripts', ['type' => 'currency', 'type_id' => $currency->id ])
    @endif

    @if ( 'active' === $tabs['credit_notes_active'] )
        @include('admin.credit_notes.records-display-scripts', ['type' => 'currency', 'type_id' => $currency->id ])
    @endif
@endsection