@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.task-tags.title')</h3>
    @can('task_tag_create')
    <p>
        <a href="{{ route('admin.task_tags.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($task_tags) > 0 ? 'datatable' : '' }} @can('task_tag_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('task_tag_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.task-tags.fields.name')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($task_tags) > 0)
                        @foreach ($task_tags as $task_tag)
                            <tr data-entry-id="{{ $task_tag->id }}">
                                @can('task_tag_delete_multi')
                                    <td></td>
                                @endcan

                                <td field-key='name'>{{ $task_tag->name }}</td>
                                                                <td>
                                    @can('task_tag_view')
                                    <a href="{{ route('admin.task_tags.show',[$task_tag->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('task_tag_edit')
                                    <a href="{{ route('admin.task_tags.edit',[$task_tag->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('task_tag_delete_multi')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.task_tags.destroy', $task_tag->id])) !!}
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
        @can('task_tag_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.task_tags.mass_destroy') }}';
        @endcan

    </script>
@endsection