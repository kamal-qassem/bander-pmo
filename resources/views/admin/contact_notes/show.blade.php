@extends('layouts.app')

@section('content')

    <h3 class="page-title">@lang('global.contact-notes.title')</h3>
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $contact_note->title }}
        </div>

        <div class="panel-body table-responsive">
             @if( Gate::allows('contact_note_edit') || Gate::allows('contact_note_delete'))
            <div class="pull-right">   
                @if( Gate::allows('contact_note_edit') )
                    <a href="{{ route('admin.contact_notes.edit', $contact_note->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('contact_note_delete'))
                    @include('admin.common.delete-link', ['record' => $contact_note, 'routeName' => 'admin.contact_notes.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.contact-notes.fields.title')</th>
                            <td field-key='title'>{{ $contact_note->title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contact-notes.fields.contact')</th>
                            <td field-key='contact'>{{ $contact_note->contact->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contact-notes.fields.notes')</th>
                            <td field-key='notes'style="padding: 0px;">{!! clean($contact_note->notes) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contact-notes.fields.attachment')</th>
                            <td field-key='attachment'> @foreach($contact_note->getMedia('attachment') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}" >{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.contact_notes.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

@stop
