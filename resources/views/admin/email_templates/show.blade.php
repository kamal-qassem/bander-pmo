@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.email-templates.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.email-templates.fields.name')</th>
                            <td field-key='name'>{{ $email_template->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.email-templates.fields.subject')</th>
                            <td field-key='subject'>{{ $email_template->subject }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.email-templates.fields.body')</th>
                            <td field-key='body'>{!! clean($email_template->body) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.email_templates.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

@stop
