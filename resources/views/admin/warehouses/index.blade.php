@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.warehouses.title')</h3>
    @can('warehouse_create')
    <p>
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.warehouses.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
                <span class="badge"> 
            
               {{\App\Warehouse::count()}}

            </span>
            </a></li> 
            @can('warehouse_delete')|
            <li><a href="{{ route('admin.warehouses.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

                <span class="badge"> 
            
               {{\App\Warehouse::onlyTrashed()->count()}}
                        </span>

            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('warehouse_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('warehouse_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.warehouses.fields.name')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('warehouse_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.warehouses.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            @if ( ! empty( $type ) && ! empty( $type_id ) )
                window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.list_warehouses.index', [ 'type' => $type, 'type_id' => $type_id ]) !!}?show_deleted={{ request('show_deleted') }}';
            @else
                window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.warehouses.index') !!}?show_deleted={{ request('show_deleted') }}';
            @endif
    
            window.dtDefaultOptionsNew.columns = [@can('warehouse_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'name', name: 'name'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection