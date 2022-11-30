@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.income.title')</h3>
    
    {!! Form::model($income, ['method' => 'PUT', 'route' => ['admin.incomes.update', $income->id], 'files' => true,'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('account_id', trans('global.income.fields.account').'*', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('account_create'))
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createaccount" data-selectedid="account_id" data-redirect="{{route('admin.incomes.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.income.fields.account') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="createaccount" data-selectedid="account_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.income.fields.account') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('account_id', $accounts, old('account_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('account_id'))
                        <p class="help-block">
                            {{ $errors->first('account_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                    {!! Form::label('income_category_id', trans('global.income.fields.income-category').'*', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('income_create'))
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createincomecategory" data-selectedid="income_category_id" data-redirect="{{route('admin.incomes.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.income.fields.income-category') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="createincomecategory" data-selectedid="income_category_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.income.fields.income-category') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('income_category_id', $income_categories, old('income_category_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('income_category_id'))
                        <p class="help-block">
                            {{ $errors->first('income_category_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                        
                    {!! Form::label('entry_date', trans('global.income.fields.entry-date').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('entry_date', old('entry_date'), ['class' => 'form-control date', 'placeholder' => 'Entry date', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('entry_date'))
                        <p class="help-block">
                            {{ $errors->first('entry_date') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                <div class="form-group">
                        
                    {!! Form::label('amount', trans('global.income.fields.amount').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                        <?php
                           $income_amt = $income->amount;
                           if( $income_amt ){
                             $income_number_format_amt = number_format( $income_amt,2,".","" );
                           } 
                        ?>

                    {!! Form::number('amount',  old('amount',$income_number_format_amt), ['class' => 'form-control amount','min'=>'0','step'=>'.01', 'placeholder' => 'Amount', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('amount'))
                        <p class="help-block">
                            {{ $errors->first('amount') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('description', trans('global.income.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => 'Description']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('description'))
                        <p class="help-block">
                            {{ $errors->first('description') }}
                        </p>
                    @endif
                </div>
                </div>
            
          
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('payer_id', trans('global.income.fields.payer').'*', ['class' => 'control-label']) !!}
                    @if ( Gate::allows('contact_create'))
                        @if( 'button' === $addnew_type )
                        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcustomer" data-selectedid="payer_id" data-redirect="{{route('admin.incomes.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.income.fields.payer') )])}}">{{ trans('global.app_add_new') }}</button>
                        @else        
                        &nbsp;<a class="modalForm" data-action="createcustomer" data-selectedid="payer_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.income.fields.payer') )])}}"><i class="fa fa-plus-square"></i></a>
                        @endif
                    @endif
                    {!! Form::select('payer_id', $payers, old('payer_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('payer_id'))
                        <p class="help-block">
                            {{ $errors->first('payer_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('pay_method_id', trans('global.income.fields.pay-method').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('pay_method_id', $pay_methods, old('pay_method_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('pay_method_id'))
                        <p class="help-block">
                            {{ $errors->first('pay_method_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                        
                    {!! Form::label('ref_no', trans('global.income.fields.ref-no').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('ref_no', old('ref_no'), ['class' => 'form-control', 'placeholder' => 'Reference no / Transaction id ']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('ref_no'))
                        <p class="help-block">
                            {{ $errors->first('ref_no') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>

                      <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('description_file', trans('global.income.fields.description-file').'', ['class' => 'control-label']) !!}
                    {!! Form::file('description_file[]', [
                        'multiple',
                        'class' => 'form-control file-upload',
                        'data-url' => route('admin.media.upload'),
                        'data-bucket' => 'description_file',
                        'data-filekey' => 'description_file',
                        'data-accept' => FILE_TYPES_GENERAL,
                        ]) !!}
                    <p class="help-block">{{trans('others.global_file_types_general')}}</p>
                    <div class="photo-block">
                        <div class="form-group">
                        <div class="progress-bar">&nbsp;</div>
                    </div>
                        <div class="files-list">
                            @foreach($income->getMedia('description_file') as $media)
                                <p class="form-group">
                                    <a href="{{ $media->getUrl() }}" target="_blank">{{ $media->name }} ({{ $media->size }} KB)</a>
                                    <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                                    <input type="hidden" name="description_file_id[]" value="{{ $media->id }}">
                                </p>
                            @endforeach
                        </div>
                    </div>
                    @if($errors->has('description_file'))
                        <p class="help-block">
                            {{ $errors->first('description_file') }}
                        </p>
                    @endif
                </div>
                </div>

            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}

     @include('admin.common.modal-loading-submit')
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}"
            });
            
        });
    </script>
            
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
                        model_name: 'Income',
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
    @include('admin.common.modal-scripts')
@stop