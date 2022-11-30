@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contacts.mailchimp-lists')</h3>    

    {!! Form::open(['method' => 'POST', 'route' => ['admin.contacts.run-mailchimp-email-campaigns-post', $list_id], 'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">                           
                    {!! Form::label('subject', trans('custom.email.subject').'*', ['class' => 'control-label form-label']) !!}
                        <div class="form-line">
                            {!! Form::text('subject', old('subject', $template->subject), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('subject'))
                                <p class="help-block">
                                    {{ $errors->first('subject') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">                           
                    {!! Form::label('from_name', trans('custom.common.from-name').'*', ['class' => 'control-label form-label']) !!}
                        <div class="form-line">
                            {!! Form::text('from_name', old('from_name', $template->from_name), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('from_name'))
                                <p class="help-block">
                                    {{ $errors->first('from_name') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">                           
                    {!! Form::label('from_email', trans('custom.common.from-name').'*', ['class' => 'control-label form-label']) !!}
                        <div class="form-line">
                            {!! Form::email('from_email', old('from_email', $template->from_email), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('from_email'))
                                <p class="help-block">
                                    {{ $errors->first('from_email') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xs-{{COLUMNS}} ">
                    <div class="form-group">
                        {!! Form::label('is_schedule', trans('custom.messages.is-schedule'), ['class' => 'control-label']) !!}
                        {!! Form::select('is_schedule', yesnooptions(), old('is_schedule', 'no'), ['class' => 'form-control select2', 'required' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('is_schedule'))
                            <p class="help-block">
                                {{ $errors->first('is_schedule') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">                           
                    {!! Form::label('schedule_date', trans('custom.messages.date'), ['class' => 'control-label form-label']) !!}
                        <div class="form-line">
                            {!! Form::text('schedule_date', old('schedule_date'), ['class' => 'form-control date', 'placeholder' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('schedule_date'))
                                <p class="help-block">
                                    {{ $errors->first('schedule_date') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">                           
                    {!! Form::label('message', trans('custom.email.message-body').'*', ['class' => 'control-label form-label']) !!}
                        <div class="form-line">
                            {!! Form::textarea('message', old('message', $template->content), ['class' => 'form-control editor', 'placeholder' => '', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('message'))
                                <p class="help-block">
                                    {{ $errors->first('message') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="list_name" value="{{! empty( $list_deails['name'] ) ? $list_deails['name'] : ''}}">
    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
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
                format: "{{ config('app.datetime_format_moment') }}",
                locale: "{{ App::getLocale() }}",
                minDate:  moment(),
                sideBySide: true
            });
            
        });
    </script>

@stop
