@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.assets-statuses.title')</h3>
    @can('assets_status_create')
    <p>
        <a href="{{ route('admin.assets_statuses.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($assets_statuses) > 0 ? 'datatable' : '' }} @can('assets_status_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('assets_status_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.assets-statuses.fields.title')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($assets_statuses) > 0)
                        @foreach ($assets_statuses as $assets_status)
                            <tr data-entry-id="{{ $assets_status->id }}">
                                @can('assets_status_delete_multi')
                                    <td></td>
                                @endcan

                                <td field-key='title'>{{ $assets_status->title }}</td>
                                                                <td>
                                    @can('assets_status_view')
                                    <a href="{{ route('admin.assets_statuses.show',[$assets_status->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('assets_status_edit')
                                    <a href="{{ route('admin.assets_statuses.edit',[$assets_status->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('assets_status_delete_multi')
                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.assets_statuses.destroy', $assets_status->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('assets_status_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.assets_statuses.mass_destroy') }}';
        @endcan

    </script>
@endsection