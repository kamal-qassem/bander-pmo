@extends('layouts.app')

@section('content')
    <h3 class="page-title">
        {{$user->name}}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.users.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
            ], 
            [
                'route' => 'admin.users.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
            ],
        ],
        'record' => $user,
        ] )
    </h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        <?php
    $tabs = [
        'details_active' => 'active',
        'user_actions_active'   => '',
        'internal_notifications_active' => '',
        'departments_active'    =>  '',
        'assets_history_active' =>  '',
        'tasks_active'           =>  '',
        'client_projects_active'=>  '',
        'support_active'        =>  '',
        'assets_active'         => '',
        'support_created_active'=> '',
        'invoices_active' => '',
        'recurring_invoices_active' => '',
        'quotes_active' => '',
        'recurring_invoices'=>'',
        'user_actions_active' => '',
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
        <!-- Nav tabs -->
<div class="panel-body table-responsive">
<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>
<li role="presentation" class="{{$tabs['user_actions_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'user_actions' ])}}" title="@lang('global.user-actions.title')">@lang('global.user-actions.title')</a></li>
@if( isPluginActive('quick_notification') )
<li role="presentation" class="{{$tabs['internal_notifications_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'internal_notifications' ])}}" title="@lang('others.canvas.notifications')">@lang('others.canvas.notifications')</a></li>
@endif
<li role="presentation" class="{{$tabs['departments_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'departments' ])}}" title="@lang('global.departments.title')">@lang('global.departments.title')</a></li>
@if( isPluginActive('asset') )
<li role="presentation" class="{{$tabs['assets_history_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'assets_history' ])}}" title="@lang('global.assets-history.title')">@lang('global.assets-history.title')</a></li>
@endif
@if( isPluginActive('task') )
<li role="presentation" class="{{$tabs['tasks_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'tasks' ])}}" title="@lang('global.tasks.title')">@lang('global.tasks.title')</a></li>
@endif
@if( isPluginActive('client_project') )
<li role="presentation" class="{{$tabs['client_projects_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'client_projects' ])}}" title="@lang('global.client-projects.title')">@lang('global.client-projects.title')</a></li>
@endif
@if( isPluginActive( 'support' ) )
<li role="presentation" class="{{$tabs['support_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'support' ])}}" title="@lang('global.support.title')">@lang('global.support.title')</a></li>
@endif
@if( isPluginActive('asset') )
<li role="presentation" class="{{$tabs['assets_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'assets' ])}}" title="@lang('global.assets.title')">@lang('global.assets.title')</a></li>
@endif
@if( isPluginActive( ['support', 'faq'] ) )
<li role="presentation" class="{{$tabs['support_created_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'support_created' ])}}" title="@lang('others.canvas.support-created')">@lang('others.canvas.support-created')</a></li>
@endif
@if( isPluginActive( 'invoice' ) )
<li role="presentation" class="{{$tabs['invoices_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'invoices' ])}}" title="@lang('others.canvas.invoices')">@lang('others.canvas.invoices')</a></li>
@endif
@if( isPluginActive( ['quotes'] ) )
<li role="presentation" class="{{$tabs['quotes_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'quotes' ])}}" title="@lang('global.quotes.title')">@lang('global.quotes.title')</a></li>
@endif
@if( File::exists(config('modules.paths.modules') . '/RecurringInvoices') && Module::find('recurringinvoices')->active && isPluginActive('recurringinvoices'))
<li role="presentation" class="{{$tabs['recurring_invoices_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'recurring_invoices' ])}}" title="@lang('global.recurring-invoices.title')">@lang('global.recurring-invoices.title')</a></li>
@endif
@if( File::exists(config('modules.paths.modules') . '/Proposals') && Module::find('proposals')->active && isPluginActive('proposals'))
        <li role="presentation" class="{{$tabs['proposals_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'proposals' ])}}">
@lang('proposals::global.proposals.title')</a></li>
@endif
@if( File::exists(config('modules.paths.modules') . '/Contracts') && Module::find('contracts')->active && isPluginActive('contracts'))
            <li role="presentation" class="{{$tabs['contracts_active']}}"><a href="{{route('admin.users.show', [ 'user_id' => $user->id, 'list' => 'contracts' ])}}">@lang('contracts::global.contracts.title')</a></li>
@endif  
</ul>

<!-- Tab panes -->
<div class="tab-content">

 <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

        <div class="pull-right">
          @can('user_edit')
            <a href="{{ route('admin.users.edit',[ $user->id ]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
          @endcan
        </div> 

        <table class="table1 table-bordered table-striped">
                        <tr>
                            <th>@lang('global.users.fields.name')</th>
                            <td field-key='name'>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.users.fields.email')</th>
                            <td field-key='email'>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.users.fields.role')</th>
                            <td field-key='role'>
                                @foreach ($user->role as $singleRole)
                                    <span class="label label-info label-many">{{ $singleRole->title }}</span>
                                @endforeach
                            </td>
                        </tr>
                        
                        <tr>
                            <th>@lang('global.users.fields.department')</th>
                            <td field-key='department'>{{ $user->department->name ?? '' }}</td>
                        </tr>
                       
                        <tr>
                            <th>@lang('others.users.is-support-admin')</th>
                            <td field-key='department'>{{ $user->ticketit_admin == 0  ? 'No' : 'Yes' }}</td>
                        </tr>
                        <tr>
                          <th>@lang('others.users.is-support-agent')</th>
                          <td field-key='department'>{{ $user->ticketit_agent == 0 ? 'No' : 'Yes' }}</td>
                        </tr>
                    </table>

    </div>    
    
@if ( 'active' === $tabs['user_actions_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['user_actions_active']}}" id="user_actions">
    @include('admin.user_actions.records-display')
</div>
@endif
@if ( 'active' === $tabs['internal_notifications_active'])
<div role="tabpanel" class="tab-pane {{$tabs['internal_notifications_active']}}" id="internal_notifications">
    @include('admin.internal_notifications.records-display')
</div>
@endif
@if ( 'active' === $tabs['departments_active'])
<div role="tabpanel" class="tab-pane {{$tabs['departments_active']}}" id="departments">
    @include('admin.departments.records-display')
</div>
@endif
@if ( 'active' === $tabs['assets_history_active'])
<div role="tabpanel" class="tab-pane {{$tabs['assets_history_active']}}" id="assets_history">
    @include('admin.assets_histories.records-display')
</div>
@endif
@if ( 'active' === $tabs['tasks_active'])
<div role="tabpanel" class="tab-pane {{$tabs['tasks_active']}}" id="tasks">
    @include('admin.tasks.records-display')
</div>
@endif
@if ( 'active' === $tabs['client_projects_active'])
<div role="tabpanel" class="tab-pane {{$tabs['client_projects_active']}}" id="client_projects">
    @include('admin.client_projects.records-display')
</div>
@endif
@if ( 'active' === $tabs['support_active'])
<div role="tabpanel" class="tab-pane {{$tabs['support_active']}}" id="support">
    @include('ticketit::tickets.partials.datatable')
</div>
@endif
@if ( 'active' === $tabs['assets_active'])
<div role="tabpanel" class="tab-pane {{$tabs['assets_active']}}" id="assets">
    @include('admin.assets.records-display')
</div>
@endif
@if ( 'active' === $tabs['support_created_active'])
<div role="tabpanel" class="tab-pane {{$tabs['support_created_active']}}" id="support_created">
    @include('ticketit::tickets.partials.datatable')
</div>
@endif
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

            <a href="{{ route('admin.users.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 

    @if ( 'active' === $tabs['user_actions_active'] )
        @include('admin.user_actions.records-display-scripts', [ 'type' => 'user', 'type_id' => $user->id ])
    @endif  
    @if ( 'active' === $tabs['internal_notifications_active'] )
        @include('admin.internal_notifications.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif  
    @if ( 'active' === $tabs['departments_active'] )
        @include('admin.departments.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif  
    @if ( 'active' === $tabs['assets_history_active'] )
        @include('admin.assets_histories.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif  
    @if ( 'active' === $tabs['tasks_active'] )
        @include('admin.tasks.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif 
    @if ( 'active' === $tabs['client_projects_active'] )
        @include('admin.client_projects.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif 
    @if ( 'active' === $tabs['support_active'] )
        @include('ticketit::tickets.partials.datatable-scripts', [ 'type' => 'agent', 'type_id' => $user->id, 'complete' => false ])
    @endif 
    @if ( 'active' === $tabs['assets_active'] )
        @include('admin.assets.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif
    @if ( 'active' === $tabs['support_created_active'] )
        @include('ticketit::tickets.partials.datatable-scripts', [ 'type' => 'usersupportcreated', 'type_id' => $user->id, 'complete' => false ])
    @endif
    @if ( 'active' === $tabs['invoices_active'] )
        @include('admin.invoices.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif
    @if ( 'active' === $tabs['quotes_active'] )
        @include('quotes::admin.quotes.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif
    @if ( 'active' === $tabs['recurring_invoices_active'] )
        @include('recurringinvoices::admin.recurring_invoices.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif
    @if ( 'active' === $tabs['proposals_active'] )
        @include('proposals::admin.proposals.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id])
    @endif
    @if ( 'active' === $tabs['contracts_active'] )
        @include('contracts::admin.contracts.records-display-scripts', [ 'type' => 'contact', 'type_id' => $user->id ])
    @endif
    
@endsection


