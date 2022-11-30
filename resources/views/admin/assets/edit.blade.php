@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.assets.title')</h3>
    
    {!! Form::model($asset, ['method' => 'PUT', 'route' => ['admin.assets.update', $asset->id], 'files' => true,'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('category_id', trans('global.assets.fields.category').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('category_id', $categories, old('category_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('category_id'))
                        <p class="help-block">
                            {{ $errors->first('category_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                
                    {!! Form::label('serial_number', trans('global.assets.fields.serial-number').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('serial_number', old('serial_number'), ['class' => 'form-control', 'placeholder' => 'Serial Number']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('serial_number'))
                        <p class="help-block">
                            {{ $errors->first('serial_number') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                
                    {!! Form::label('title', trans('global.assets.fields.title').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Title', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4">
                <div class="form-group">
                    @if ($asset->photo1)
                        <a href="{{ asset(env('UPLOAD_PATH').'/'.$asset->photo1) }}" target="_blank"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/'.$asset->photo1) }}"></a>
                    @endif
                    {!! Form::label('photo1', trans('global.assets.fields.photo1').'', ['class' => 'control-label']) !!}
                    {!! Form::file('photo1', ['class' => 'form-control', 'style' => 'margin-top: 4px;']) !!}
                    {!! Form::hidden('photo1_max_size', 8) !!}
                    {!! Form::hidden('photo1_max_width', 6000) !!}
                    {!! Form::hidden('photo1_max_height', 6000) !!}
                    <p class="help-block">{{trans('others.global_file_types_gallery')}}</p>
                    <div class="photo-block">
                        <div class="progress-bar">&nbsp;</div>
                        <div class="files-list"></div>
                    </div>
                    @if($errors->has('photo1'))
                        <p class="help-block">
                            {{ $errors->first('photo1') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('photo2', trans('global.assets.fields.photo2').'', ['class' => 'control-label']) !!}
                    {!! Form::file('photo2[]', [
                        'multiple',
                        'class' => 'form-control file-upload',
                        'data-url' => route('admin.media.upload'),
                        'data-bucket' => 'photo2',
                        'data-filekey' => 'photo2',
                        'data-accept' => FILE_TYPES_GALLERY,
                        ]) !!}
                    <p class="help-block">{{trans('others.global_file_types_gallery')}}</p>
                    <div class="photo-block">
                        <div class="progress-bar">&nbsp;</div>
                        <div class="files-list">
                            @foreach($asset->getMedia('photo2') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                    <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                                    <input type="hidden" name="photo2_id[]" value="{{ $media->id }}">
                                </p>
                            @endforeach
                        </div>
                    </div>
                    @if($errors->has('photo2'))
                        <p class="help-block">
                            {{ $errors->first('photo2') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('attachments', trans('global.assets.fields.attachments').'', ['class' => 'control-label']) !!}
                    {!! Form::file('attachments[]', [
                        'multiple',
                        'class' => 'form-control file-upload',
                        'data-url' => route('admin.media.upload'),
                        'data-bucket' => 'attachments',
                        'data-filekey' => 'attachments',
                        'data-accept' => FILE_TYPES_GENERAL,
                        ]) !!}
                    <p class="help-block">{{trans('others.global_file_types_general')}}</p>
                    <div class="photo-block">
                        <div class="progress-bar">&nbsp;</div>
                        <div class="files-list">
                            @foreach($asset->getMedia('attachments') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}" >{{ $media->name }} ({{ $media->size }} KB)</a>
                                    <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                                    <input type="hidden" name="attachments_id[]" value="{{ $media->id }}">
                                </p>
                            @endforeach
                        </div>
                    </div>
                    @if($errors->has('attachments'))
                        <p class="help-block">
                            {{ $errors->first('attachments') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4">
                <div class="form-group">
                
                    {!! Form::label('status_id', trans('global.assets.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status_id', $statuses, old('status_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('status_id'))
                        <p class="help-block">
                            {{ $errors->first('status_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                
                    {!! Form::label('location_id', trans('global.assets.fields.location').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('location_id', $locations, old('location_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('location_id'))
                        <p class="help-block">
                            {{ $errors->first('location_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                
                    {!! Form::label('assigned_user_id', trans('global.assets.fields.assigned-user').'', ['class' => 'control-label']) !!}
                    {!! Form::select('assigned_user_id', $assigned_users, old('assigned_user_id'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('assigned_user_id'))
                        <p class="help-block">
                            {{ $errors->first('assigned_user_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-6">
                <div class="form-group">
                
                    {!! Form::label('notes', trans('global.assets.fields.notes').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', old('notes'), ['class' => 'form-control ', 'placeholder' => 'Notes']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('notes'))
                        <p class="help-block">
                            {{ $errors->first('notes') }}
                        </p>
                    @endif
                </div>
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent

    <script src="{{ asset('adminlte/plugins/fileUpload/js/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/fileUpload/js/jquery.fileupload.js') }}"></script>
    <script>
        $(function () {
            $('.file-upload').each(function () {
                var $this = $(this);
                var $parent = $(this).parent();

                $(this).fileupload({
                    dataType: 'json',
                    formData: {
                        model_name: 'Asset',
                        bucket: $this.data('bucket'),
                        file_key: $this.data('filekey'),
                        accept: $this.data('accept'),
                        _token: '{{ csrf_token() }}'
                    },
                    add: function (e, data) {
                        data.submit();
                    },
                    done: function (e, data) {
                        $.each(data.result.files, function (index, file) {
                            if ( file.size > 0 ) {
                                var $line = $($('<p/>', {class: "form-group"}).html(file.name + ' (' + file.size + ' bytes)').appendTo($parent.find('.files-list')));
                                $line.append('<a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>');
                                $line.append('<input type="hidden" name="' + $this.data('bucket') + '_id[]" value="' + file.id + '"/>');
                                if ($parent.find('.' + $this.data('bucket') + '-ids').val() != '') {
                                    $parent.find('.' + $this.data('bucket') + '-ids').val($parent.find('.' + $this.data('bucket') + '-ids').val() + ',');
                                }
                                $parent.find('.' + $this.data('bucket') + '-ids').val($parent.find('.' + $this.data('bucket') + '-ids').val() + file.id);
                            } else {
                                var $line = $($('<p/>', {class: "form-group"}).html(file.name).appendTo($parent.find('.files-list')));
                                $line.append('<a href="#" class="btn btn-xs btn-danger remove-file">Not accepted</a>');
                            }
                        });
                        $parent.find('.progress-bar').hide().css(
                            'width',
                            '0%'
                        );
                    }
                }).on('fileuploadprogressall', function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $parent.find('.progress-bar').show().css(
                        'width',
                        progress + '%'
                    );
                });
            });
            $(document).on('click', '.remove-file', function () {
                var $parent = $(this).parent();
                $parent.remove();
                return false;
            });
        });
    </script>
@stop