@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.project-billing-types.title')</h3>
    
    {!! Form::model($project_billing_type, ['method' => 'PUT', 'route' => ['admin.project_billing_types.update', $project_billing_type->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('title', trans('global.project-billing-types.fields.title').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Title','required'=>'']) !!}
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
                    {!! Form::label('description', trans('global.project-billing-types.fields.description').'', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ', 'placeholder' => 'Description','rows'=>'3']) !!}
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


    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger wave-effect']) !!}
    {!! Form::close() !!}
@stop

