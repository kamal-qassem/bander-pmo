@extends('layouts.app')

@section('content')
   <h3 class="page-title">{{$account->name}}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.accounts.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'account_edit',
            ], 
            [
                'route' => 'admin.accounts.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'account_delete',
            ],
        ],
        'record' => $account,
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
            'income_active' => '',
            'expense_active' => '',
            'transfer_active' => '',
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
<li role="presentation" class="{{$tabs['income_active']}}"><a href="{{route('admin.accounts.show', [ 'account_id' => $account->id, 'list' => 'income' ])}}">@lang('global.income.title')</a></li>
<li role="presentation" class="{{$tabs['expense_active']}}"><a href="{{route('admin.accounts.show', [ 'account_id' => $account->id, 'list' => 'expense' ])}}">@lang('global.expense.title')</a></li>
<li role="presentation" class="{{$tabs['transfer_active']}}"><a href="{{route('admin.accounts.show', [ 'account_id' => $account->id, 'list' => 'transfer' ])}}">@lang('global.transfers.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
 
 <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

     <div class="pull-right">
            @can('account_edit')
                <a href="{{ route('admin.accounts.edit',[$account->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
        </div>

        <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.accounts.fields.name')</th>
                            <td field-key='name'>{{ $account->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.accounts.fields.description')</th>
                            <td field-key='description'>{!! clean($account->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.accounts.fields.initial-balance')</th>
                            <td field-key='initial_balance'>{{ digiCurrency($account->initial_balance) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.accounts.fields.account-number')</th>
                            <td field-key='account_number'>{{ $account->account_number }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.accounts.fields.contact-person')</th>
                            <td field-key='contact_person'>{{ $account->contact_person }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.accounts.fields.phone')</th>
                            <td field-key='phone'>{{ $account->phone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.accounts.fields.url')</th>
                            <td field-key='url'><a href="{{ $account->url }}" target="_blank">{{ $account->url }}</a></td>
                        </tr>
                    </table>

    </div>   
@if ( 'active' === $tabs['income_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['income_active']}}" id="income">
    @include('admin.incomes.records-display')
</div>
@endif
@if ( 'active' === $tabs['expense_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['expense_active']}}" id="expense">
    @include('admin.expenses.records-display')
</div>
@endif
@if ( 'active' === $tabs['transfer_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['transfer_active']}}" id="transfer">
    @include('admin.transfers.records-display')
</div>
@endif

</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.accounts.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript') 

    @if ( 'active' === $tabs['income_active'] )
        @include('admin.incomes.records-display-scripts', [ 'type' => 'account', 'type_id' => $account->id ])
    @endif

    @if ( 'active' === $tabs['expense_active'] )
        @include('admin.expenses.records-display-scripts', [ 'type' => 'account', 'type_id' => $account->id ])
    @endif

    @if ( 'active' === $tabs['transfer_active'] )
        @include('admin.transfers.records-display-scripts', ['type' => 'account', 'type_id' => $account->id ])
    @endif
@endsection