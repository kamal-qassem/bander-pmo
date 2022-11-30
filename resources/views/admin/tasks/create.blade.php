@extends('layouts.app')

@section('content')
 {{ config('app.date_format_moment') }}
    <h3 class="page-title">@lang('global.tasks.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.tasks.store'], 'files' => true,'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('name', trans('global.tasks.fields.name').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Name', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>

                     <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('status_id', trans('global.tasks.fields.status').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('status_id', $statuses, old('status_id', 1), ['class' => 'form-control select2', 'required' => '']) !!}
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
                    {!! Form::label('user_id', trans('global.tasks.fields.user').'', ['class' => 'control-label']) !!}
                    {!! Form::select('user_id', $users, old('user_id'), ['class' => 'form-control select2','placeholder' => 'Please select']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('user_id'))
                        <p class="help-block">
                            {{ $errors->first('user_id') }}
                        </p>
                    @endif
                </div>
                </div>

            </div>

                    
          <div class="row">
               <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('start_date', trans('global.tasks.fields.start-date').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('start_date', old('start_date'), ['class' => 'form-control date', 'placeholder' => 'Start Date']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('start_date'))
                        <p class="help-block">
                            {{ $errors->first('start_date') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::label('due_date', trans('global.tasks.fields.due-date').'', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('due_date', old('due_date'), ['class' => 'form-control date', 'placeholder' => 'Due Date']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('due_date'))
                        <p class="help-block">
                            {{ $errors->first('due_date') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>

                
         
                <div class="col-xs-4">
                <div class="form-group">
                    
                    {!! Form::label('attachment', trans('global.tasks.fields.thumbnail').'', ['class' => 'control-label']) !!}
                    {!! Form::hidden('attachment', old('attachment')) !!}
                    {!! Form::file('attachment', ['class' => 'form-control']) !!}
                    {!! Form::hidden('attachment_max_size', 8) !!}
                    <p class="help-block">{{trans('others.global_file_types_gallery')}}</p>
                    @if($errors->has('attachment'))
                        <p class="help-block">
                            {{ $errors->first('attachment') }}
                        </p>
                    @endif
                </div>
                </div>

            </div>

            <div class="row">
           
                <div class="col-xs-8">
                <div class="form-group">
                    {!! Form::label('description', trans('global.tasks.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Description','rows'=>'4']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('description'))
                        <p class="help-block">
                            {{ $errors->first('description') }}
                        </p>
                    @endif
                </div>
                </div>

             
          
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 0 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
    <script>
        $("#selectbtn-tag").click(function(){
            $("#selectall-tag > option").prop("selected","selected");
            $("#selectall-tag").trigger("change");
        });
        $("#deselectbtn-tag").click(function(){
            $("#selectall-tag > option").prop("selected","");
            $("#selectall-tag").trigger("change");
        });
    </script>
@stop