@extends('layouts.app')

@section('content')
   <h3 class="page-title">{{ $asset->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.assets.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'asset_edit',
            ], 
            [
                'route' => 'admin.assets.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'asset_delete',
            ],
        ],
        'record' => $asset,
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
<li role="presentation" class="{{$tabs['assets_history_active']}}"><a href="{{route('admin.assets.show', [ 'asset_id' => $asset->id, 'list' => 'assets_history' ])}}" title= "@lang('global.assets-history.title')">@lang('global.assets-history.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

 <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

          <div class="pull-right">
            @can('asset_edit')
                <a href="{{ route('admin.assets.edit',[$asset->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
            </div>   

        <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.assets.fields.category')</th>
                            <td field-key='category'>{{ $asset->category->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.serial-number')</th>
                            <td field-key='serial_number'>{{ $asset->serial_number }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.title')</th>
                            <td field-key='title'>{{ $asset->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.photo1')</th>
                            <td field-key='photo1'>
                                @if($asset->photo1 && file_exists(public_path() . '/thumb/' . $asset->photo1))
                                <img src="{{ asset(env('UPLOAD_PATH').'/thumb/' . $asset->photo1) }}"/>
                            @endif
                        </td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.photo2')</th>
                            <td field-key='photo2'> @foreach($asset->getMedia('photo2') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.attachments')</th>
                            <td field-key='attachments's> @foreach($asset->getMedia('attachments') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.status')</th>
                            <td field-key='status'>{{ $asset->status->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.location')</th>
                            <td field-key='location'>{{ $asset->location->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.assigned-user')</th>
                            <td field-key='assigned_user'>{{ $asset->assigned_user->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.assets.fields.notes')</th>
                            <td field-key='notes'>{!! clean($asset->notes) !!}</td>
                        </tr>
                    </table>

    </div>

      @if ( 'active' === $tabs['assets_history_active'])
     <div role="tabpanel" class="tab-pane {{$tabs['assets_history_active']}}" id="assets_history">
       @include('admin.assets_histories.records-display')
     </div>
     @endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.assets.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['assets_history_active'] )
        @include('admin.assets_histories.records-display-scripts', [ 'type' => 'asset', 'type_id' => $asset->id ])
    @endif 
@endsection

