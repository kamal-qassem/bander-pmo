@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.products.title')</h3>
    @can('product_create')
    <p>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        <a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        @include('csvImport.modal', ['model' => 'Product', 'csvtemplatepath' => 'products.csv', 'duplicatecheck' => 'name'])
        
    </p>
    @endcan

       <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.products.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">
            @lang('global.app_all')
            <?php
            $all = \App\Product::count();
            $trashed = \App\Product::onlyTrashed()->count();
            ?>
            <span class="badge">{{$all}}</span>
            </a></li>
            @can('product_delete')
            |
            <li><a href="{{ route('admin.products.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">@lang('global.app_trash')
            <span class="badge"> {{$trashed}}</span>
            </a></li>
            @endcan
        </ul>
    </p>   


    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.products.records-display')
        </div>
    </div>
@stop

    @section('javascript') 
          @include('admin.products.records-display-scripts')
    @endsection