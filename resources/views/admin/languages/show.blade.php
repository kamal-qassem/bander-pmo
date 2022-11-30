@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $language->language }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.languages.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'language_edit',
            ], 
            [
                'route' => 'admin.languages.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'language_delete',
            ],
        ],
        'record' => $language,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>    
<li role="presentation" class=""><a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">@lang('global.contacts.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

   <div role="tabpanel" class="tab-pane active" id="details">

         <div class="pull-right">
            @can('language_edit')
                <a href="{{ route('admin.languages.edit',[$language->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
         </div>   
    
        <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.languages.fields.language')</th>
                            <td field-key='language'>{{ $language->language }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.languages.fields.code')</th>
                            <td field-key='code'>{{ $language->code }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.languages.fields.is-rtl')</th>
                            <td field-key='is_rtl'>{{ $language->is_rtl }}</td>
                        </tr>
                    </table>    

    </div>   
    
<div role="tabpanel" class="tab-pane" id="contacts">
<table class="table table-bordered table-striped {{ count($contacts) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
                <th>@lang('global.contacts.fields.first-name')</th>
                <th>@lang('global.contacts.fields.contact-type')</th>
                <th>@lang('global.contacts.fields.email')</th>
                <th>@lang('global.contacts.fields.address')</th> 
                <th>&nbsp;</th>

        </tr>
    </thead>

    <tbody>
        @if (count($contacts) > 0)
            @foreach ($contacts as $contact)
                <tr data-entry-id="{{ $contact->id }}">
                                <td field-key='first_name'>{{ $contact->first_name }}</td>
                                  <td field-key='contact_type'>
                                    @foreach ($contact->contact_type as $singleContactType)
                                        <span class="label label-info label-many">{{ $singleContactType->name }}</span>
                                    @endforeach
                                </td>
                                <td field-key='email'>{{ $contact->email }}</td>
                                <td field-key='address'>{{ $contact->address }}</td>
                              
                                                                <td>
                                    @can('contact_view')
                                    <a href="{{ route('admin.contacts.show',[$contact->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('contact_edit')
                                    <a href="{{ route('admin.contacts.edit',[$contact->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('contact_delete')
                    {!! Form::open(array(
                        'style' => 'display: inline-block;',
                        'method' => 'DELETE',
                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                        'route' => ['admin.contacts.destroy', $contact->id])) !!}
                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                    {!! Form::close() !!}
                    @endcan
                    </td>

                    </tr>
                    @endforeach
            @else
                <tr>
                    <td colspan="21">@lang('global.app_no_entries_in_table')</td>
                </tr>
            @endif
    </tbody>
</table>
</div>
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.languages.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


