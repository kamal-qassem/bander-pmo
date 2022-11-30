@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.income-category.title')</h3>
    @can('income_category_create')
    <p>
        <a href="{{ route('admin.income_categories.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        <a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        @include('csvImport.modal', ['model' => 'IncomeCategory', 'csvtemplatepath' => 'income_categories.csv','duplicatecheck' => 'name'])
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($income_categories) > 0 ? 'datatable' : '' }} @can('income_category_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('income_category_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.income-category.fields.name')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($income_categories) > 0)
                        @foreach ($income_categories as $income_category)
                            <tr data-entry-id="{{ $income_category->id }}">
                                @can('income_category_delete_multi')
                                    <td></td>
                                @endcan

                                <td field-key='name'>{{ $income_category->name }}</td>
                                                                <td>
                                    @can('income_category_view')
                                    <a href="{{ route('admin.income_categories.show',[$income_category->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('income_category_edit')
                                    <a href="{{ route('admin.income_categories.edit',[$income_category->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('income_category_delete_multi')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.income_categories.destroy', $income_category->id])) !!}
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
        @can('income_category_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.income_categories.mass_destroy') }}';
        @endcan

    </script>
@endsection