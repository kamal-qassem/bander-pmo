@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.dynamic-options.title')</h3>

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
  @if( $dynamic_option->module === 'invoices' && $dynamic_option->type === 'priorities' )  
<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>

<li role="presentation" class=""><a href="#invoice_priorities" aria-controls="invoice_priorities" role="tab" data-toggle="tab">Invoice priorities</a></li>

@endif

@if( $dynamic_option->module === 'invoices' && $dynamic_option->type === 'taskstatus' )  
<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>    

<li role="presentation" class=""><a href="#invoice_tasks" aria-controls="invoice_tasks" role="tab" data-toggle="tab">Invoice tasks</a></li>

@endif

@if( $dynamic_option->module === 'quotes' && $dynamic_option->type === 'priorities' )
 
<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>    

<li role="presentation" class=""><a href="#quote_priorities" aria-controls="quote_priorities" role="tab" data-toggle="tab">Quote priorities</a></li>

@endif

@if( $dynamic_option->module === 'quotes' && $dynamic_option->type === 'taskstatus' )

<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>

<li role="presentation" class=""><a href="#quote_tasks" aria-controls="quote_tasks" role="tab" data-toggle="tab">Quote tasks</a></li>
@endif

</ul>

<!-- Tab panes -->
<div class="tab-content">

<div role="tabpanel" class="tab-pane active" id="details">
       @if( Gate::allows('dynamic_option_edit') || Gate::allows('dynamic_option_delete'))
            <div class="pull-right">   
                @if( Gate::allows('dynamic_option_edit') )
                    <a href="{{ route('admin.dynamic_options.edit', $dynamic_option->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('dynamic_option_delete'))
                    @include('admin.common.delete-link', ['record' => $dynamic_option, 'routeName' => 'admin.dynamic_options.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif

     <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.dynamic-options.fields.title')</th>
                            <td field-key='title'>{{ $dynamic_option->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.description')</th>
                            <td field-key='description'>{!! clean($dynamic_option->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.module')</th>
                            <td field-key='module'>{{ ucfirst($dynamic_option->module) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.color')</th>
                            <td field-key='module'>
                                @if( ! empty( $dynamic_option->color ) )
                                <span style="color:{{ $dynamic_option->color }}">{{ $dynamic_option->color }}</span>
                                @else
                                {{ $dynamic_option->color }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('global.dynamic-options.fields.type')</th>
                            <td field-key='type'>{{ ucfirst($dynamic_option->type) }}</td>
                        </tr>
                    </table>        
    
    </div>

<div role="tabpanel" class="tab-pane" id="invoice_tasks">
<table class="table table-bordered table-striped {{ count($invoice_tasks) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.invoice-tasks.fields.name')</th>
            <th>@lang('global.invoice-tasks.fields.startdate')</th>
            <th>@lang('global.invoice-tasks.fields.duedate')</th>
            <th>@lang('global.invoice-tasks.fields.status')</th>
                    @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>

    <tbody>
        @if (count($invoice_tasks) > 0)
            @foreach ($invoice_tasks as $invoice_task)
                <tr data-entry-id="{{ $invoice_task->id }}">
                    <td field-key='name'>{{ $invoice_task->name }}</td>
                    <td field-key='startdate'>{{ digiDate( $invoice_task->startdate ) }}</td>
                    <td field-key='duedate'>{{ digiDate( $invoice_task->duedate ) }}</td>
                    <td field-key='status'>{{ $invoice_task->status->title ?? '' }}</td>
                   
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.invoice_tasks.restore', $invoice_task->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                     {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.invoice_tasks.perma_del', $invoice_task->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    </td>
                                @else
                                <td>
                                    @can('invoice_task_view')
                                    <a href="{{ route('admin.invoice_tasks.show',['invoice_id' => $invoice_task->invoice_id, 'id' => $invoice_task->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('invoice_task_edit')
                                    <a href="{{ route('admin.invoice_tasks.edit',['invoice_id' => $invoice_task->invoice_id, 'id' => $invoice_task->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('invoice_task_delete')
                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.invoice_tasks.destroy', $invoice_task->invoice_id, $invoice_task->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="31">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>

</div>


<!-- Invoice Priorities -->
  <div role="tabpanel" class="tab-pane" id="invoice_priorities">
<table class="table table-bordered table-striped {{ count($invoice_priorities) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.invoice-tasks.fields.name')</th>
            <th>@lang('global.invoice-tasks.fields.startdate')</th>
            <th>@lang('global.invoice-tasks.fields.duedate')</th>
            <th>@lang('global.invoice-tasks.fields.status')</th>
           
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>

    <tbody>
        @if (count($invoice_priorities) > 0)
            @foreach ($invoice_priorities as $invoice_priority)
                <tr data-entry-id="{{ $invoice_priority->id }}">
                    <td field-key='name'>{{ $invoice_priority->name }}</td>
                    <td field-key='startdate'>{{ digiDate( $invoice_priority->startdate ) }}</td>
                    <td field-key='duedate'>{{ digiDate( $invoice_priority->duedate ) }}</td>
                    <td field-key='status'>{{ $invoice_priority->status->title ?? '' }}</td>
                  
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.invoice_tasks.restore', $invoice_priority->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.invoice_tasks.perma_del', $invoice_priority->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td>
                                    @can('invoice_task_view')
                                    <a href="{{ route('admin.invoice_tasks.show',['invoice_id' => $invoice_priority->invoice_id, 'id' => $invoice_priority->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('invoice_task_edit')
                                    <a href="{{ route('admin.invoice_tasks.edit',['invoice_id' => $invoice_priority->invoice_id, 'id' => $invoice_priority->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('invoice_task_delete')
                             {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.invoice_tasks.destroy', $invoice_priority->invoice_id, $invoice_priority->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="31">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>  

<!-- quote tasks -->

<div role="tabpanel" class="tab-pane" id="quote_tasks">
<table class="table table-bordered table-striped {{ count($quote_tasks) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.quote-tasks.fields.name')</th>
            <th>@lang('global.quote-tasks.fields.startdate')</th>
            <th>@lang('global.quote-tasks.fields.duedate')</th>
            <th>@lang('global.quote-tasks.fields.status')</th>
           
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>

    <tbody>
        @if (count($quote_tasks) > 0)
            @foreach ($quote_tasks as $quote_task)
                <tr data-entry-id="{{ $quote_task->id }}">
                    <td field-key='name'>{{ $quote_task->name }}</td>
                    <td field-key='startdate'>{{ digiDate( $quote_task->startdate ) }}</td>
                    <td field-key='duedate'>{{ digiDate( $quote_task->duedate ) }}</td>
                 
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.quote_tasks.restore', $quote_task->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.quote_tasks.perma_del', $quote_task->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    </td>
                                @else
                                <td>
                                    @can('quote_task_view')
                                    <a href="{{ route('admin.quote_tasks.show',['quote_id' => $quote_task->quote_id, 'id' => $quote_task->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('quote_task_edit')
                                    <a href="{{ route('admin.quote_tasks.edit',['quote_id' => $quote_task->quote_id, 'id' => $quote_task->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('quote_task_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.quote_tasks.destroy', $quote_task->quote_id, $quote_task->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="31">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>    


<!-- end quote tasks -->


<!-- quote_priorities -->

<div role="tabpanel" class="tab-pane" id="quote_priorities">
<table class="table table-bordered table-striped {{ count($quote_priorities) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.quote-tasks.fields.name')</th>
            <th>@lang('global.quote-tasks.fields.startdate')</th>
            <th>@lang('global.quote-tasks.fields.duedate')</th>
            <th>@lang('global.quote-tasks.fields.status')</th>
           
            @if( request('show_deleted') == 1 )
            <th>&nbsp;</th>
            @else
            <th>&nbsp;</th>
            @endif
        </tr>
    </thead>

    <tbody>
        @if (count($quote_priorities) > 0)
            @foreach ($quote_priorities as $quote_priority)
                <tr data-entry-id="{{ $quote_priority->id }}">
                    <td field-key='name'>{{ $quote_priority->name }}</td>
                    <td field-key='startdate'>{{ digiDate( $quote_priority->startdate ) }}</td>
                    <td field-key='duedate'>{{ digiDate( $quote_priority->duedate ) }}</td>
                    <td field-key='status'>{{ $quote_priority->status->title ?? '' }}</td>
                  
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.quote_priorities.restore', $quote_priority->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.quote_priorities.perma_del', $quote_priority->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    </td>
                                @else
                                <td>
                                    @can('quote_priority_view')
                                    <a href="{{ route('admin.quote_priorities.show',['quote_id' => $quote_priority->quote_id, 'id' => $quote_priority->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('quote_priority_edit')
                                    <a href="{{ route('admin.quote_priorities.edit',['quote_id' => $quote_priority->quote_id, 'id' => $quote_priority->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('quote_priority_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.quote_priorities.destroy', $quote_priority->quote_id, $quote_priority->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="31">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>    


<!-- end quote_priorities -->


</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.dynamic_options.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


