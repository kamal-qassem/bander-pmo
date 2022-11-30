@extends('layouts.app')

@section('content')
        <h3 class="page-title">{{ $tax->name }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.taxes.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'tax_edit',
            ], 
            [
                'route' => 'admin.taxes.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'tax_delete',
            ],
        ],
        'record' => $tax,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif

        <?php
        $tabs = [
            'details_active' => 'active',
            'invoices_active' => '',
            'quotes_active' => '',
            'recurring_invoices_active' => '',
            'purchase_orders_active' => '',
            'credit_notes_active' => '',
            'client_projects_active' => '',
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
            <div class="row">
                <div class="col-md-6">
              
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>    
@if( isPluginActive('invoice') )
<li role="presentation" class="{{$tabs['invoices_active']}}"><a href="{{route('admin.taxes.show', [ 'tax_id' => $tax->id, 'list' => 'invoices' ])}}">@lang('others.canvas.invoices')</a></li>
@endif
@if( isPluginActive('quotes') )
<li role="presentation" class="{{$tabs['quotes_active']}}"><a href="{{route('admin.taxes.show', [ 'tax_id' => $tax->id, 'list' => 'quotes' ])}}">@lang('global.quotes.title')</a></li>
@endif
@if( isPluginActive('recurringinvoices') )
<li role="presentation" class="{{$tabs['recurring_invoices_active']}}"><a href="{{route('admin.taxes.show', [ 'tax_id' => $tax->id, 'list' => 'recurring_invoices' ])}}">@lang('global.recurring-invoices.title')</a></li>
@endif
@if( isPluginActive('purchase_order') )
<li role="presentation" class="{{$tabs['purchase_orders_active']}}"><a href="{{route('admin.taxes.show', [ 'tax_id' => $tax->id, 'list' => 'purchase_orders' ])}}">@lang('global.purchase-orders.title')</a></li>
@endif
@if( isPluginActive('credit_note') )
<li role="presentation" class="{{$tabs['credit_notes_active']}}"><a href="{{route('admin.taxes.show', [ 'tax_id' => $tax->id, 'list' => 'credit_notes' ])}}">@lang('global.credit_notes.title')</a></li>
@endif
</ul>

<!-- Tab panes -->
<div class="tab-content">
    
   <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

          <div class="pull-right">
            @can('tax_edit')
                <a href="{{ route('admin.taxes.edit',[$tax->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
          </div>   

          <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.taxes.fields.name')</th>
                            <td field-key='name'>{{ $tax->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.taxes.fields.rate')</th>
                            <td field-key='rate'>{{ $tax->rate }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.taxes.fields.rate-type')</th>
                            <td field-key='rate_type'>{{ $tax->rate_type }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.taxes.fields.description')</th>
                            <td field-key='description'>{!! clean($tax->description) !!}</td>
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

            <a href="{{ route('admin.taxes.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript') 
    @if ( 'active' === $tabs['invoices_active'] )
        @include('admin.invoices.records-display-scripts', ['type' => 'tax', 'type_id' => $tax->id ])
    @endif

    @if ( 'active' === $tabs['quotes_active'] )
        @include('quotes::admin.quotes.records-display-scripts', ['type' => 'tax', 'type_id' => $tax->id ])
    @endif

    @if ( 'active' === $tabs['recurring_invoices_active'] )
        @include('recurringinvoices::admin.recurring_invoices.records-display-scripts', ['type' => 'tax', 'type_id' => $tax->id ])
    @endif

    @if ( 'active' === $tabs['purchase_orders_active'] )
        @include('admin.purchase_orders.records-display-scripts', ['type' => 'tax', 'type_id' => $tax->id ])
    @endif

    @if ( 'active' === $tabs['credit_notes_active'] )
        @include('admin.credit_notes.records-display-scripts', ['type' => 'tax', 'type_id' => $tax->id ])
    @endif
@endsection


