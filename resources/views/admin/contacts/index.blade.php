@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">
    @if( ! empty( $type_id ) )
        <?php
        $details = \App\ContactType::find( $type_id );
        ?>
        @if( $details )
            {{str_plural($details->title)}}
        @else
            @lang('global.contacts.title')
        @endif
    @else
        @lang('global.contacts.title')
    @endif
</h3>
    
    <p>
        @can('contact_create')
        @if( ! empty( $type_id ) )
            <a href="{{ route('admin.contacts.create', $type_id) }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        @else
            <a href="{{ route('admin.contacts.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        @endif

        <a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        
        @endcan
        @include('admin.contacts.menu')
        @include('csvImport.modal', ['model' => 'Contact', 'csvtemplatepath' => 'contacts.csv', 'duplicatecheck' => 'email', 'contact_type' => ( ! empty( $type_id ) ) ? $type_id : CUSTOMERS_TYPE])
    </p>
    



    <p>
        <ul class="list-inline">
            <?php
            $route = route('admin.contacts.index');
            if ( ! empty( $type_id ) ) {
                $route = route('admin.list_contacts.index', [ 'type' => 'contact_type', 'type_id' => $type_id ]);
            }
            ?>
            <li><a href="{{ $route }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">
            @lang('global.app_all')
            <span class="badge">
               @if ( ! empty( $type_id ) )
                {{\App\Contact::whereHas("contact_type",
                function ($query) use( $type_id ) {
                    $query->where('id', $type_id);
                })->count()}}
               @else
                {{\App\Contact::count()}}
               @endif
            </span>
            </a></li>
            
            |
            <?php
            $route = route('admin.contacts.index') . '?show_deleted=1';
            if ( ! empty( $type_id ) ) {
                $route = route('admin.list_contacts.index', [ 'type' => 'contact_type', 'type_id' => $type_id ]) . '?show_deleted=1';
            }
            ?>
            @can('contact_delete')
            <li><a href="{{ $route }}" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge">
               @if ( ! empty( $type_id ) )
                {{\App\Contact::whereHas("contact_type",
                function ($query) use( $type_id ) {
                    $query->where('id', $type_id);
                })->onlyTrashed()->count()}}
               @else
                {{\App\Contact::onlyTrashed()->count()}}
               @endif
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
            @include('admin.contacts.records-display')
        </div>
    </div>
    @include('admin.contacts.mail.modal-loading')
@stop

@section('javascript') 
    @include('admin.contacts.records-display-scripts')
@endsection