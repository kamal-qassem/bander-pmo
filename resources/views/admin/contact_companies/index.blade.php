@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contact-companies.title')</h3>
    @can('contact_company_create')
    <p>
        <a href="{{ route('admin.contact_companies.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
        <a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>

        @include('csvImport.modal', ['model' => 'ContactCompany', 'csvtemplatepath' => 'contact-companies.csv'])        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.contact_companies.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.contact_companies.records-display-scripts')
@endsection