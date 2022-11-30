@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.accounts.title')</h3>
    @can('account_create')
    <p>
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.accounts.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')
                <span class="badge">{{\App\Account::count()}}</span>
            </a></li> 
            @can('account_delete')
             |
            <li><a href="{{ route('admin.accounts.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')

              <span class="badge"> {{\App\Account::onlyTrashed()->count()}}</span>  

            </a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('account_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('account_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.accounts.fields.name')</th>
                        <th>@lang('global.accounts.fields.initial-balance')</th>
                        <th>@lang('global.accounts.fields.account-number')</th>
                        <th>@lang('global.accounts.fields.contact-person')</th>
                        <th>@lang('global.accounts.fields.phone')</th>
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
        @can('account_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.accounts.mass_destroy') }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptions.buttons = [];
            window.dtDefaultOptions.ajax = '{!! route('admin.accounts.index') !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptions.columns = [@can('account_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'name', name: 'name'},
                {data: 'initial_balance', name: 'initial_balance'},
                {data: 'account_number', name: 'account_number'},
                {data: 'contact_person', name: 'contact_person'},
                {data: 'phone', name: 'phone'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTables();
        });
    </script>

    <script>
        jQuery.fn.dataTable.Api.register( 'sum()', function ( ) {
            return this.flatten().reduce( function ( a, b ) {
                if ( typeof a === 'string' ) {
                    a = a.replace(/[^\d.-]/g, '') * 1;
                }
                if ( typeof b === 'string' ) {
                    b = b.replace(/[^\d.-]/g, '') * 1;
                }

                return a + b;
            }, 0 );
        } );
    </script>

    <script>
        $(function() {
            var table = $('.ajaxTable').DataTable();
            $('.ajaxTable').on( 'draw.dt', function () {
                @if(request('show_deleted'))
                var tablesum = table.column(1).data().sum();
                @else
                var tablesum = table.column(2).data().sum();
                @endif
                $(".dataTables_info").append('{{trans("custom.accounts.sum-of-all-accounts")}}<b>' + tablesum + '</b>');
            } );
        });
    </script>
@endsection