<div class="row">
    <div class="col-xs-6">
        {!! Form::label('subject', trans('global.project-discussions.fields.subject').'*', ['class' => 'control-label']) !!}
        {!! Form::text('subject', old('subject'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('subject'))
            <p class="help-block">
                {{ $errors->first('subject') }}
            </p>
        @endif
    </div>

    

    <div class="col-xs-6">
        {!! Form::label('show_to_customer', trans('global.project-discussions.fields.visible-to-customer').'', ['class' => 'control-label']) !!}
        {!! Form::select('show_to_customer', $show_to_customer, old('show_to_customer'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('show_to_customer'))
            <p class="help-block">
                {{ $errors->first('show_to_customer') }}
            </p>
        @endif
    </div>
    <input type="hidden" name="project_id" value="{{$project->id}}">
    <div class="col-xs-12 form-group">
        {!! Form::label('description', trans('global.project-discussions.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
</div>