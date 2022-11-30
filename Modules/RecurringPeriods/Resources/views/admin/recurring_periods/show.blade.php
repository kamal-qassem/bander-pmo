@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $recurring_period->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.recurring_periods.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'recurring_period_edit',
            ], 
            [
                'route' => 'admin.recurring_periods.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'recurring_period_delete',
            ],
        ],
        'record' => $recurring_period,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                 
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

 <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>   
    

</ul>

<!-- Tab panes -->
<div class="tab-content">

 <div role="tabpanel" class="tab-pane active" id="details">

            <div class="pull-right">
            @can('recurring_period_edit')
                <a href="{{ route('admin.recurring_periods.edit',[$recurring_period->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
            </div>   

       <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.recurring-periods.fields.title')</th>
                            <td field-key='title'>{{ $recurring_period->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.recurring-periods.fields.value')</th>
                            <td field-key='value'>{{ $recurring_period->value }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.recurring-periods.fields.description')</th>
                            <td field-key='description'>{!! clean($recurring_period->description) !!}</td>
                        </tr>
                    </table>

 </div>   
    

</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.recurring_periods.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


