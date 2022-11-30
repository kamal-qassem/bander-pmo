@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.roles.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.roles.store'],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('title', trans('global.roles.fields.title').'*', ['class' => 'control-label form-label']) !!}
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
            
               
            
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('color', 'Color', ['class' => 'control-label']) !!}
                    {!! Form::text('color', old('color'), ['class' => 'form-control colorpicker', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('color'))
                        <p class="help-block">
                            {{ $errors->first('color') }}
                        </p>
                    @endif
                </div>
                </div>

                 <div class="col-xs-12">
                <div class="form-group">
                    {!! Form::label('permission', trans('global.roles.fields.permission').'*', ['class' => 'control-label']) !!}
                    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-permission">
                        {{ trans('global.app_select_all') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-permission">
                        {{ trans('global.app_deselect_all') }}
                    </button>
                    {!! Form::select('permission[]', $permissions, old('permission'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-permission' , 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('permission'))
                        <p class="help-block">
                            {{ $errors->first('permission') }}
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

<link href="{{ url('css/cdn-styles-css/bootstrap/2.5.3/bootstrap-colorpicker.min.css') }}" rel="stylesheet">  

<script src="{{ url('js/cdn-js-files/bootstrap/2.5.3') }}/bootstrap-colorpicker.min.js"></script>

<script>
    $('.colorpicker').colorpicker();
</script>

    <script>
        $("#selectbtn-permission").click(function(){
            $("#selectall-permission > option").prop("selected","selected");
            $("#selectall-permission").trigger("change");
        });
        $("#deselectbtn-permission").click(function(){
            $("#selectall-permission > option").prop("selected","");
            $("#selectall-permission").trigger("change");
        });
    </script>
@stop