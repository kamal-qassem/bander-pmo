@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contacts.mailchimp-lists')</h3>    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped ajaxTable">
                <thead>
                    <tr>
                        @if ( empty( $list_id ) )
                        <th>@lang('global.contacts.fields.name')</th>
                        <th>@lang('global.contacts.member-count')</th>
                        <th>@lang('global.contacts.is-schedule')</th>
                        <th>@lang('global.contacts.last-run')</th>
                        @else
                        <th>@lang('global.contacts.fields.email')</th>
                        <th>@lang('global.contacts.member-status')</th>
                        @endif
                        @if ( empty( $list_id ) )
                        <th>&nbsp;</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('admin.contacts.mail.modal-loading')
@stop

@section('javascript') 
    <script>
        $(document).ready(function () {
            @if( ! empty( $list_id ) )
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.contacts.mailchimp-email-campaigns', $list_id) !!}';
            @else
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.contacts.mailchimp-email-campaigns') !!}';
            @endif
            window.dtDefaultOptionsNew.columns = [
                @if ( ! empty( $list_id ) )
                {data: 'email_address', name: 'email_address'},
                {data: 'status', name: 'status', sortable: false},

                @else
                {data: 'name', name: 'name'},
                {data: 'member_count', name: 'member_count', sortable: false},
                {data: 'is_schedule', name: 'is_schedule', sortable: false},
                {data: 'last_run', name: 'last_run', sortable: false},
                @endif
                @if ( empty( $list_id ) )
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
                @endif
            ];
            processAjaxTablesNew();
        });
    </script>
   <script src="{{ url('adminlte/plugins/ckeditor/ckeditor.js') }}"></script>
    @include('admin.contacts.mail.scripts')
@endsection