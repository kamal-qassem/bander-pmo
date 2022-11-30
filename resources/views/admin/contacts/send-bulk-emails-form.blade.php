@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.contacts.send-bulk-emails')</h3>    

    {!! Form::open(['method' => 'POST', 'route' => ['admin.contacts.send-bulk-emails-post', $contact_type], 'class'=>'formvalidation']) !!}

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
                        {!! Form::label('send_after', trans('global.contacts.send_after'), ['class' => 'control-label']) !!} {!! digi_get_help( trans('global.contacts.value-in-minutes') ) !!}
                        {!! Form::selectRange('send_after', 0, 60, old('send_after', 'no'), ['class' => 'form-control select2', 'required' => '']) !!} 
                        <p class="help-block"></p>
                        @if($errors->has('send_after'))
                            <p class="help-block">
                                {{ $errors->first('send_after') }}
                            </p>
                        @endif
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

                <div class="col-xs-12">
                    <div class="form-group">                           
                    {!! Form::label('emails', trans('custom.email.message-body').'*', ['class' => 'control-label form-label']) !!}
                    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-emails">
                        {{ trans('global.app_select_all') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-emails">
                        {{ trans('global.app_deselect_all') }}
                    </button>
                        <div class="form-line">
                           <?php
                           $emails_arr = old('emails') ? old('emails') : $emails;
                           ?>
                            {!! Form::select('emails[]', $emails, $emails_arr, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-emails' , 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
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

    <script type="text/javascript">
       $("#selectbtn-emails").click(function(){
           $("#selectall-emails > option").prop("selected","selected");
           $("#selectall-emails").trigger("change");
       });
       $("#deselectbtn-emails").click(function(){
           $("#selectall-emails > option").prop("selected","");
           $("#selectall-emails").trigger("change");
       });
    </script>

@stop
