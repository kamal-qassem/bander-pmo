@extends('layouts.app')

@section('content')
    @include('admin.client_projects.operations.menu', array('client_project' => $project))
    <h3 class="page-title">@lang('custom.invoices.notes')</h3>
    {!! Form::model($notes, ['method' => 'POST', 'route' => ['admin.project_files.process-note', $project->id],'class'=>'formvalidation', 'files' => true]) !!}
      <div class="panel panel-default">
            <div class="panel-heading">
                @lang('custom.invoices.notes')
            </div>
            
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::label('description', trans('global.client-projects.fields.description').'', ['class' => 'control-label']) !!}
                        {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => 'Description']) !!}
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
            <input type="hidden" name="project_id" value="{{$project->id}}">
            <input type="hidden" name="user_id" value="{{Auth::id()}}">
        </div>
    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger', 'id' => 'savebutton']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
   
    @include('admin.common.standard-ckeditor')
    
@stop