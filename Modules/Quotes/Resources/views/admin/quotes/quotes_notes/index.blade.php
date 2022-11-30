@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    @include('quotes::admin.quotes.invoice.invoice-menu', ['invoice' => $quote])
    
    <h3 class="page-title">@lang('global.quotes-notes.title')</h3>
    @can('quotes_note_create')
    <p>
        <a href="{{ route('admin.quotes_notes.create', $quote->id) }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <?php
             $count = Modules\Quotes\Entities\QuotesNote::where('quote_id', '=',$quote->id )->count();
            ?>
            <li><a href="{{ route('admin.quotes_notes.index', $quote->id) }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">@lang('global.app_all')<span class="badge">{{$count}}</span></a></li> @can('quotes_note_delete') |
                <?php
                 $trash_count = Modules\Quotes\Entities\QuotesNote::where('quote_id', '=',$quote->id )->onlyTrashed()->count();
                ?> 
            <li><a href="{{ route('admin.quotes_notes.index', $quote->id) }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')<span class="badge">{{$trash_count}}</span></a></li>
            @endcan
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable @can('quotes_note_delete_multi') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('quotes_note_delete_multi')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.quotes-notes.fields.description')</th>
                        <th>@lang('global.invoice-notes.fields.created-by')</th>
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
        @can('quotes_note_delete_multi')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.quotes_notes.mass_destroy', $quote->id) }}'; @endif
        @endcan
        $(document).ready(function () {
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.quotes_notes.index', $quote->id) !!}?show_deleted={{ request('show_deleted') }}';
            window.dtDefaultOptionsNew.columns = [@can('quotes_note_delete_multi')
                @if ( request('show_deleted') != 1 )
                    {data: 'massDelete', name: 'id', searchable: false, sortable: false},
                @endif
                @endcan{data: 'description', name: 'description'},
                {data: 'created_by.name', name: 'created_by.name'},
                
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection