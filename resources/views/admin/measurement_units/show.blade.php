@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.measurement-units.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $measurement_unit->title }}
        </div>

        <div class="panel-body table-responsive">
            @if( Gate::allows('measurement_unit_edit') || Gate::allows('measurement_unit_delete'))
            <div class="pull-right">   
                @if( Gate::allows('measurement_unit_edit') )
                    <a href="{{ route('admin.measurement_units.edit', $measurement_unit->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('measurement_unit_delete'))
                    @include('admin.common.delete-link', ['record' => $measurement_unit, 'routeName' => 'admin.measurement_units.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.measurement-units.fields.title')</th>
                            <td field-key='title'>{{ $measurement_unit->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.measurement-units.fields.status')</th>
                            <td field-key='status'>{{ $measurement_unit->status }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.measurement-units.fields.description')</th>
                            <td field-key='description'>{!! clean($measurement_unit->description) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.measurement_units.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


