@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contract_types.title')</h3>
    @can('contract_type_create')
    <p>
        <a href="{{ route('admin.contract_types.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($contract_types) > 0 ? 'datatable' : '' }} @can('contract_type_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('contract_type_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.contract_types.fields.name')</th>
                       
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($contract_types) > 0)
                        @foreach ($contract_types as $contract_type)
                            <tr data-entry-id="{{ $contract_type->id }}">
                                @can('contract_type_delete')
                                    <td></td>
                                @endcan

                                <td field-key='name'>{{ $contract_type->name }}</td>
                                <td>
                                    @can('contract_type_view')
                                    <a href="{{ route('admin.contract_types.show',[$contract_type->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('contract_type_edit')
                                    <a href="{{ route('admin.contract_types.edit',[$contract_type->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('contract_type_delete')
                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.contract_types.destroy', $contract_type->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
      
        @can('contract_type_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.contract_types.mass_destroy') }}';
        @endcan

    </script>
@endsection