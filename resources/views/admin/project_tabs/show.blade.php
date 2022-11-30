@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $project_tab->title }}

    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
      
        <div class="panel-body table-responsive">
            @if( Gate::allows('project_tab_edit') || Gate::allows('project_tab_delete'))
            <div class="pull-right">   
                @if( Gate::allows('project_tab_edit') )
                    <a href="{{ route('admin.project_tabs.edit', $project_tab->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('project_tab_delete'))
                    @include('admin.common.delete-link', ['record' => $project_tab, 'routeName' => 'admin.project_tabs.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.project-tabs.fields.title')</th>
                            <td field-key='title'>{{ $project_tab->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.project-tabs.fields.description')</th>
                            <td field-key='description'>{!! clean($project_tab->description) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.project_tabs.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


