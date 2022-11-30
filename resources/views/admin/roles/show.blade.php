@extends('layouts.app')

@section('content')
   <h3 class="page-title">{{ $role->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.roles.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'role_edit',
            ], 
            [
                'route' => 'admin.roles.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'role_delete',
            ],
        ],
        'record' => $role,
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
            'users_active' => '',
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
<li role="presentation" class="{{$tabs['users_active']}}"><a href="{{route('admin.roles.show', [ 'role_id' => $role->id, 'list' => 'users' ])}}" title= "@lang('global.users.title')">@lang('global.users.title')</a></li>


<!-- Tab panes -->
<div class="tab-content">
 
  <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

        <div class="pull-right">
           @can('role_edit')
             <a href="{{ route('admin.roles.edit',[$role->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
           @endcan
        </div>   
    
        <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.roles.fields.title')</th>
                            <td field-key='title'>{{ $role->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.roles.fields.permission')</th>
                            <td field-key='permission'>
                                @foreach ($role->permission as $singlePermission)
                                    <span class="label label-info label-many">{{ $singlePermission->title }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </table>

</div>
@if ( 'active' === $tabs['users_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['users_active']}}" id="users">
    @include('admin.users.records-display')
</div>
@endif

</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['users_active'] )
        @include('admin.users.records-display-scripts', [ 'type' => 'role', 'type_id' => $role->id ])
     @endif
@endsection

