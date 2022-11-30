@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contact-documents.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $contact_document->name }}
        </div>

        <div class="panel-body table-responsive">
            @if( Gate::allows('contact_document_edit') || Gate::allows('contact_document_delete'))
            <div class="pull-right">   
                @if( Gate::allows('contact_document_edit') )
                    <a href="{{ route('admin.contact_documents.edit', $contact_document->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('contact_document_delete'))
                    @include('admin.common.delete-link', ['record' => $contact_document, 'routeName' => 'admin.contact_documents.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.contact-documents.fields.name')</th>
                            <td field-key='name'>{{ $contact_document->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contact-documents.fields.description')</th>
                            <td field-key='description'>{!! clean($contact_document->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contact-documents.fields.attachments')</th>
                            <td field-key='attachments'> @foreach($contact_document->getMedia('attachment') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}" >{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contact-documents.fields.contact')</th>
                            <td field-key='contact'>{{ $contact_document->contact->name ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.contact_documents.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop


