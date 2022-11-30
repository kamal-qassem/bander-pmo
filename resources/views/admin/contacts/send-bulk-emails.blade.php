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
                        <th>@lang('global.contacts.fields.name')</th>
                        <th>@lang('global.contacts.member-count')</th>
                                          
                        <th>&nbsp;</th>                        
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
            
            window.dtDefaultOptionsNew.ajax.url = '{!! route('admin.contacts.send-bulk-emails') !!}';
            
            window.dtDefaultOptionsNew.columns = [                
                {data: 'name', name: 'name'},
                {data: 'member_count', name: 'member_count', sortable: false},
                {data: 'actions', name: 'actions', searchable: false, sortable: false}
            ];
            processAjaxTablesNew();
        });
    </script>
@endsection