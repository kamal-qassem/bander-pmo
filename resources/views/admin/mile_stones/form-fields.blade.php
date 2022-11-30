<div class="row">
    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('name', trans('global.mile-stones.fields.name').'*', ['class' => 'control-label']) !!}
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('name'))
            <p class="help-block">
                {{ $errors->first('name') }}
            </p>
        @endif
    </div>

    

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('description_visible_to_customer', trans('global.mile-stones.fields.description-visible-to-customer').'', ['class' => 'control-label']) !!}
        {!! Form::select('description_visible_to_customer', $enum_description_visible_to_customer, old('description_visible_to_customer'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('description_visible_to_customer'))
            <p class="help-block">
                {{ $errors->first('description_visible_to_customer') }}
            </p>
        @endif
    </div>

    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('due_date', trans('global.mile-stones.fields.due-date').'', ['class' => 'control-label']) !!}
        {!! Form::text('due_date', old('due_date'), ['class' => 'form-control date', 'placeholder' => '','required'=>'']) !!}
        <p class="help-block"></p>
        @if($errors->has('due_date'))
            <p class="help-block">
                {{ $errors->first('due_date') }}
            </p>
        @endif
    </div>

    
    <input type="hidden" name="project_id" value="{{$project->id}}">



    <div class="col-xs-{{COLUMNS}}">
        {!! Form::label('milestone_order', trans('global.mile-stones.fields.milestone-order').'', ['class' => 'control-label']) !!}
        {!! Form::number('milestone_order', old('milestone_order'), ['class' => 'form-control', 'placeholder' => '','min'=>'0','required'=>'']) !!}
        <p class="help-block"></p>
        @if($errors->has('milestone_order'))
            <p class="help-block">
                {{ $errors->first('milestone_order') }}
            </p>
        @endif
    </div>

    <div class="col-xs-12">
        {!! Form::label('description', trans('global.mile-stones.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
</div>