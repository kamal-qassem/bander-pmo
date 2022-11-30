<div class="pull-right">
@can('client_project_edit')
    <a href="{{ route('admin.client_projects.edit',[$client_project->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
@endcan
</div> 
<?php
$currency_id = getDefaultCurrency('id');
if ( ! empty( $client_project->currency_id ) ) {
    $currency_id = $client_project->currency_id;
} elseif ( ! empty( $client_project->client->currency_id ) ) {
    $currency_id = $client_project->client->currency_id;
}
?>
 <table class="table table-bordered table-striped">
    <tr>
        <th>@lang('global.client-projects.fields.title')</th>
        <td field-key='title' class="trash">{{ $client_project->title }}</td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.client')</th>
        <td field-key='client'>{{ $client_project->client->name ?? '' }}</td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.priority')</th>
        <td field-key='priority'>{{ $client_project->priority }}</td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.billing-type')</th>
        <td field-key='billing_type'>{{ $client_project->billing_type->title ?? '' }}</td>
    </tr>
    @if ( $client_project->billing_type_id == PROJECT_BILLING_TYPE_FIXED_PRICE)
    <tr>
        <th>@lang('global.client-projects.fields.budget')</th>
        <td field-key='budget'>{{ digiCurrency($client_project->budget, $currency_id) }}</td>
    </tr>
    @endif
    @if ( $client_project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS)
    <tr>
        <th>@lang('global.client-projects.fields.project_rate_per_hour')</th>
        <td field-key='budget'>{{ digiCurrency( $client_project->project_rate_per_hour, $currency_id ) }}</td>
    </tr>
    @endif
    @if ( $client_project->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS)
    <tr>
        <th>@lang('global.client-projects.fields.hourly_rate')</th>
        <td field-key='budget'>{{ digiCurrency( $client_project->hourly_rate, $currency_id )}}</td>
    </tr>
    @endif
    
    <tr>
        <th>@lang('global.client-projects.fields.phase')</th>
        <td field-key='phase'>{{ $client_project->phase }}</td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.progress_from_tasks')</th>
        <td field-key='phase'>{{ ucfirst( $client_project->progress_from_tasks ) }}</td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.start-date')</th>
        <td field-key='start_date'>{{ digiDate( $client_project->start_date ) }}</td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.due-date')</th>
        <td field-key='due_date'>{{ digiDate( $client_project->due_date ) }}</td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.status')</th>
        <td field-key='status'>{{ $client_project->status->name ?? '' }}</td>
    </tr>
    @if( ! empty( $client_project->demo_url ) )
    <tr>
        <th>@lang('global.client-projects.fields.demo-url')</th>
        <td field-key='demo_url'><a href="{{ $client_project->demo_url }}" target="_blank">{{ $client_project->demo_url }}</td>
    </tr>
    @endif
    <tr>
        <th>@lang('global.client-projects.fields.assigned-to')</th>
        <td field-key='assigned_to'>
            @foreach ($client_project->assigned_to as $singleAssignedTo)
                <span class="label label-info label-many">{{ $singleAssignedTo->name }}</span>
            @endforeach
        </td>
    </tr>
    <tr>
        <th>@lang('global.client-projects.fields.project-tabs')</th>
        <td field-key='assigned_to'>
            @foreach ($client_project->project_tabs as $singleTab)
                <span class="label label-info label-many">{{ $singleTab->title }}</span>
            @endforeach
        </td>
    </tr>    
    
    <tr>
        <th>@lang('global.client-projects.fields.description')</th>
        <td field-key='description'>{!! clean($client_project->description) !!}</td>
    </tr>
    
</table>  

