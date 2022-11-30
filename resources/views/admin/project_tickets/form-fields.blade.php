<div class="row">
<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('subject', trans('global.client-projects.subject').'*', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('subject', old('subject'), ['class' => 'form-control', 'placeholder' => trans('global.client-projects.subject'), 'required' => '']) !!}
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
    {!! Form::label('status_id', trans('ticketit::lang.status').'*', ['class' => 'control-label form-label']) !!}
    {!! Form::select('status_id', $status_lists, old('status_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('status_id'))
        <p class="help-block">
            {{ $errors->first('status_id') }}
        </p>
    @endif
</div>
</div>

@if( ! empty( $ticket ) )
<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('agent_id', trans('ticketit::lang.agent').'*', ['class' => 'control-label form-label']) !!}
    {!! Form::select('agent_id', $agent_lists, old('agent_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('agent_id'))
        <p class="help-block">
            {{ $errors->first('agent_id') }}
        </p>
    @endif
</div>
</div>
@endif



<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('priority_id', trans('global.client-projects.fields.priority').'*', ['class' => 'control-label']) !!}
    {!! Form::select('priority_id', $priorities, old('priority_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('priority_id'))
        <p class="help-block">
            {{ $errors->first('priority_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('category_id', trans('global.client-projects.category').'*', ['class' => 'control-label']) !!}
    {!! Form::select('category_id', $categories, old('category_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('category_id'))
        <p class="help-block">
            {{ $errors->first('category_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-12">
<div class="form-group">
    {!! Form::label('content', trans('global.client-projects.fields.description').'', ['class' => 'control-label']) !!}
    {!! Form::textarea('content', old('content'), ['class' => 'form-control editor', 'placeholder' => 'Description']) !!}
    <p class="help-block"></p>
    @if($errors->has('content'))
        <p class="help-block">
            {{ $errors->first('content') }}
        </p>
    @endif
</div>
</div>


</div>