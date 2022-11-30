@extends('layouts.app')

@section('content')
   <h3 class="page-title">{{ $assets_status->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.assets_statuses.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'assets_status_edit',
            ], 
            [
                'route' => 'admin.assets_statuses.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'assets_status_delete',
            ],
        ],
        'record' => $assets_status,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        <?php
        $tabs = [
            'details_active' => 'active',
            'assets_history_active' => '',
            'assets_active' => '',
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
<li role="presentation" class="{{$tabs['assets_history_active']}}"><a href="{{route('admin.assets_statuses.show', [ 'assets_status_id' => $assets_status->id, 'list' => 'assets_history' ])}}" title= "@lang('global.assets-history.title')">@lang('global.assets-history.title')</a></li>
<li role="presentation" class="{{$tabs['assets_active']}}"><a href="{{route('admin.assets_statuses.show', [ 'assets_status_id' => $assets_status->id, 'list' => 'assets' ])}}" title= "@lang('global.assets.title')">@lang('global.assets.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    
 <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

          <div class="pull-right">
            @can('assets_status_edit')
                <a href="{{ route('admin.assets_statuses.edit',[$assets_status->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
            </div>   

     <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.assets-statuses.fields.title')</th>
                            <td field-key='title'>{{ $assets_status->title }}</td>
                        </tr>
                    </table>

    </div>
    @if ( 'active' === $tabs['assets_history_active'] )
        <div role="tabpanel" class="tab-pane {{$tabs['assets_history_active']}}" id="assets_history">
            @include('admin.assets_histories.records-display')
        </div>
    @endif
     @if ( 'active' === $tabs['assets_active'] )
        <div role="tabpanel" class="tab-pane {{$tabs['assets_active']}}" id="assets">
            @include('admin.assets.records-display')
        </div>
    @endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.assets_statuses.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['assets_history_active'] )
        @include('admin.assets_histories.records-display-scripts', [ 'type' => 'assets_status', 'type_id' => $assets_status->id ])
    @endif 
    
    @if ( 'active' === $tabs['assets_active'] )
        @include('admin.assets.records-display-scripts', [ 'type' => 'assets_status', 'type_id' => $assets_status->id ])
    @endif

@endsection


