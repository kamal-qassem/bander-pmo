@extends('layouts.app')

@section('content')
<h3 class="page-title">{{$contact->name}}
    @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.contacts.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'contact_edit',
            ], 
            [
                'route' => 'admin.contacts.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'contact_delete',
            ],
        ],
        'record' => $contact,
        ] )
</h3>

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
        'products_return_active' => '',
        'contact_notes_active' => '',
        'client_projects_active' => '',
        'contact_documents_active' => '',
        'users_active' => '',
        'income_active' => '',
        'expense_active' => '',
        'credit_notes_active' => '',
        'proposals_active' => '',
        'contracts_active' => '',
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
        
        @if( ! isLead($contact->id) )
            @if( isPluginActive('invoice') )
            <li role="presentation" class="{{$tabs['invoices_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'invoices' ])}}">@lang('others.canvas.invoices')</a></li>
            @endif

            @if( File::exists(config('modules.paths.modules') . '/Quotes') && Module::find('quotes')->active && isPluginActive('quotes'))
            <li role="presentation" class="{{$tabs['quotes_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'quotes' ])}}">@lang('global.quotes.title')</a></li>
            @endif

             @if( File::exists(config('modules.paths.modules') . '/RecurringInvoices') && Module::find('recurringinvoices')->active && isPluginActive('recurringinvoices'))
            <li role="presentation" class="{{$tabs['recurring_invoices_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'recurring_invoices' ])}}">@lang('global.recurring-invoices.title')</a></li>
            @endif

            @if ( isSupplier( $contact->id, 'contact' ) ) 
            <li role="presentation" class="{{$tabs['purchase_orders_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'purchase_orders' ])}}">@lang('global.purchase-orders.title')</a></li>
            @endif

            @if( isPluginActive('credit_note') )
            <li role="presentation" class="{{$tabs['credit_notes_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'credit_notes' ])}}">@lang('global.credit_notes.title')</a></li>
            @endif
            
            @if ( isClient( $contact->id, 'contact' ) )
            <li role="presentation" class="{{$tabs['client_projects_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'client_projects' ])}}">@lang('global.client-projects.title')</a></li>
            @endif

            @if( File::exists(config('modules.paths.modules') . '/Contracts') && Module::find('contracts')->active && isPluginActive('contracts'))
            <li role="presentation" class="{{$tabs['contracts_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'contracts' ])}}">@lang('contracts::global.contracts.title')</a></li>
            @endif    
        @endif
        <li role="presentation" class="{{$tabs['contact_notes_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'contact_notes' ])}}">@lang('global.contact-notes.title')</a></li>
        <li role="presentation" class="{{$tabs['contact_documents_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'contact_documents' ])}}">@lang('global.contact-documents.title')</a></li>  

        @if( File::exists(config('modules.paths.modules') . '/Proposals') && Module::find('proposals')->active && isPluginActive('proposals'))
        <li role="presentation" class="{{$tabs['proposals_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'proposals' ])}}">@lang('proposals::global.proposals.title')</a></li>
        @endif
        
        @if( ! isLead($contact->id) )
        <li role="presentation" class="{{$tabs['income_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'income' ])}}">@lang('global.income.title')</a></li>
        <li role="presentation" class="{{$tabs['expense_active']}}"><a href="{{route('admin.contacts.show', [ 'contact_id' => $contact->id, 'list' => 'expense' ])}}">@lang('global.expense.title')</a></li>
        @endif
        </ul>

<!-- Tab panes -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">
    @if( in_array( LEADS_TYPE, $contact->contact_type->pluck('id')->toArray() ) )
        @can('contact_edit')
        <div class="pull-right">
            <div class="btn-group">
               <a href="#" class="dropdown-toggle btn btn-xs btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
               {{trans('global.contacts.convert-to')}}&nbsp;<span class="caret"></span>
               </a>
               <ul class="dropdown-menu dropdown-menu-right dropdown-menu-contact">
                  <?php
                  $contacts_types = \App\ContactType::where('type', 'role')->where('status', 'active')->orderBy('priority')->get();
                  ?>
                  @foreach( $contacts_types as $contacts_type )                     
                     <li>
                     <a href="{{ route('admin.contacts.lead_convert', [ 'contact_id' => $contact->id, 'contact_type_id' => $contacts_type->id ]) }}" class="" style="padding: 5px 93px 7px 18px;">&nbsp;{{$contacts_type->title}}</a>
                     </li>                    
                  @endforeach
               </ul>
            </div>

            <a href="{{ route('admin.contacts.edit', $contact->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
        </div>
        @endcan
    @else
        @can('contact_edit')
        <div class="pull-right">   
            <a href="{{ route('admin.contacts.edit', $contact->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
        </div>
        @endcan
    @endif

    <table class="table table-bordered table-striped">
        <tr>
            <th>@lang('global.contacts.fields.company')</th>
            <td field-key='company'>{{ $contact->company->name ?? '' }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.group')</th>
            <td field-key='group'>{{ $contact->group->name ?? '' }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.contact-type')</th>
            <td field-key='contact_type'>
                @foreach ($contact->contact_type as $singleContactType)
                    <span class="label label-info label-many">{{ $singleContactType->title }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.first-name')</th>
            <td field-key='name'>{{ $contact->first_name }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.last-name')</th>
            <td field-key='last_name'>{{ $contact->last_name }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.language')</th>
            <td field-key='language'>
                @foreach ($contact->language as $singleLanguage)
                    <span class="label label-info label-many">{{ $singleLanguage->language }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.phone1')</th>
            <td field-key='phone1'>{{ ! empty( $contact->phone1_code ) ? '('.$contact->phone1_code .') - ' : ''}} {{ $contact->phone1 }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.phone2')</th>
            <td field-key='phone2'>{{ ! empty( $contact->phone2_code ) ? '('.$contact->phone2_code .') - ' : ''}} {{ $contact->phone2 }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.email')</th>
            <td field-key='email'>{{ $contact->email }}</td>
        </tr>

         <tr>

    
         <th>@lang('global.products.fields.thumbnail')</th>
         <td field-key='icon'>
            @if (!empty($contact->thumbnail) && file_exists(public_path() . '/thumb/' . $contact->thumbnail) )
            <a href="{{ route('admin.home.media-file-download', ['model' => 'Contact', 'field' => 'thumbnail', 'record_id' => $contact->id]) }}" ><img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $contact->thumbnail) }}"/></a>@endif</td>
        </tr>

        <tr>
            <th>@lang('global.contacts.fields.skype')</th>
            <td field-key='skype'>{{ $contact->skype }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.address')</th>
            <td field-key='address'>{{ $contact->address }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.city')</th>
            <td field-key='city'>{{ $contact->city }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.state-region')</th>
            <td field-key='state_region'>{{ $contact->state_region }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.zip-postal-code')</th>
            <td field-key='zip_postal_code'>{{ $contact->zip_postal_code }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.tax-id')</th>
            <td field-key='tax_id'>{{ $contact->tax_id }}</td>
        </tr>
        <tr>
            <th>@lang('global.contacts.fields.country')</th>
            <td field-key='country'>{{ $contact->country->title ?? '' }} {{ ! empty($contact->country->shortcode) ? '(' . $contact->country->shortcode . ')' : '' }}</td>
        </tr>
        <tr>
            <th>@lang('global.invoices.fields.currency')</th>
            <td field-key='country'>{{ $contact->currency->name ?? '' }}</td>
        </tr>

        @if( $contact->user )
        <tr>
            <th>@lang('global.users.title')</th>
            <td field-key='user_details'>
                <?php
                $user = $contact->user;
                ?>
                @lang('global.users.fields.name') : {{ $user->name }}
                &nbsp;<a href="{{route('admin.users.edit', $user->id)}}"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a><br>
                @lang('global.users.fields.email') : {{ $user->email }}<br>
                @lang('global.users.fields.role') : @foreach ($user->role as $singleRole)
                                <span class="label label-info label-many">{{ $singleRole->title }}</span>
                            @endforeach
                            <br>
                @lang('global.users.fields.status') : {{ $user->status }}
                
            </td>
        </tr>
        @endif
        
        <tr>
            <th>@lang('global.contacts.title_delivery_address')</th>
            <td field-key='delivery_address'>
            <?php

            $delivery_address = ! empty( $contact->delivery_address ) ? json_decode( $contact->delivery_address, true ) : array();
            echo ! empty( $delivery_address['first_name'] ) ? '<b>' . trans('global.contacts.fields.first-name') . '</b>: ' . $delivery_address['first_name'] . '<br>' : '';
            echo ! empty( $delivery_address['last_name'] ) ? '<b>' . trans('global.contacts.fields.last-name') . '</b>: ' . $delivery_address['last_name'] . '<br>' : '';
            echo ! empty( $delivery_address['address'] ) ? '<b>' . trans('global.contacts.fields.address') . '</b>: ' . $delivery_address['address'] . '<br>' : '';
            echo ! empty( $delivery_address['city'] ) ? '<b>' . trans('global.contacts.fields.city') . '</b>: ' . $delivery_address['city']  . '<br>': '';
            echo ! empty( $delivery_address['state_region'] ) ? '<b>' . trans('global.contacts.fields.state-region') . '</b>: ' . $delivery_address['state_region']  . '<br>': '';
            echo ! empty( $delivery_address['zip_postal_code'] ) ? '<b>' . trans('global.contacts.fields.zip-postal-code') . '</b>: ' . $delivery_address['zip_postal_code'] . '<br>' : '';
            echo ! empty( $delivery_address['country_id'] ) ? '<b>' . trans('global.contacts.fields.country') . '</b>: ' . getCountryname( $delivery_address['country_id'] ) . '<br>' : '';
            ?>
            <a href="{{route('admin.contacts.delivery-address.edit', $contact->id)}}"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            </td>
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

@if ( 'active' === $tabs['contact_notes_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['contact_notes_active']}}" id="contact_notes">
    @include('admin.contact_notes.records-display')
</div>
@endif

@if ( 'active' === $tabs['contact_documents_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['contact_documents_active']}}" id="contact_documents">
    @include('admin.contact_documents.records-display')
</div>
@endif

@if ( 'active' === $tabs['client_projects_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['client_projects_active']}}" id="client_projects">
    @include('admin.client_projects.records-display')
</div>
@endif

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

@if ( 'active' === $tabs['proposals_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['proposals_active']}}" id="proposals">
    @include('proposals::admin.proposals.records-display')
</div>
@endif

@if ( 'active' === $tabs['contracts_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['contracts_active']}}" id="contracts">
    @include('contracts::admin.contracts.records-display')
</div>
@endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.contacts.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript') 
    @if ( 'active' === $tabs['quotes_active'] )
        @include('quotes::admin.quotes.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['invoices_active'] )
        @include('admin.invoices.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['recurring_invoices_active'] )
        @include('recurringinvoices::admin.recurring_invoices.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['purchase_orders_active'] )
        @include('admin.purchase_orders.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif    

    @if ( 'active' === $tabs['contact_notes_active'] )
        @include('admin.contact_notes.records-display-scripts', ['contact_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['contact_documents_active'] )
        @include('admin.contact_documents.records-display-scripts', [ 'contact_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['client_projects_active'] )
        @include('admin.client_projects.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['income_active'] )
        @include('admin.incomes.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['expense_active'] )
        @include('admin.expenses.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['credit_notes_active'] )
        @include('admin.credit_notes.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif

    @if ( 'active' === $tabs['proposals_active'] )
        @include('proposals::admin.proposals.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif
    @if ( 'active' === $tabs['contracts_active'] )
        @include('contracts::admin.contracts.records-display-scripts', [ 'type' => 'contact', 'type_id' => $contact->id ])
    @endif
@endsection

