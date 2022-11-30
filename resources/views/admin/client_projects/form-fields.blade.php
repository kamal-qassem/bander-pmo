<div class="row">
<div class="col-xs-6">
<div class="form-group">
    {!! Form::label('title', trans('global.client-projects.fields.title').'*', ['class' => 'control-label form-label']) !!}

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
    {!! Form::label('client_id', trans('global.client-projects.fields.client').'*', ['class' => 'control-label']) !!}
    @if ( Gate::allows('contact_create'))
        @if( 'button' === $addnew_type )
        &nbsp;<button type="button" class="btn btn-danger modalForm" data-id="0" data-selectedid="client_id" data-action="createclient" data-redirect="{{route('admin.client_projects.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.client-projects.fields.client') )])}}">{{ trans('global.app_add_new') }}</button>
        @else        
        &nbsp;<a class="modalForm" data-action="createclient" data-selectedid="client_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.client-projects.fields.client') )])}}"><i class="fa fa-plus-square"></i></a>
        @endif
    @endif
    {!! Form::select('client_id', $clients, old('client_id'), ['class' => 'form-control select2', 'required' => '', 'id' => 'client_id']) !!}
    <p class="help-block"></p>
    @if($errors->has('client_id'))
        <p class="help-block">
            {{ $errors->first('client_id') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="row">

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('priority', trans('global.client-projects.fields.priority').'*', ['class' => 'control-label']) !!}
    {!! Form::select('priority', $enum_priority, old('priority'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('priority'))
        <p class="help-block">
            {{ $errors->first('priority') }}
        </p>
    @endif
</div>
</div>



<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('billing_type_id', trans('global.client-projects.fields.billing-type').'*', ['class' => 'control-label']) !!}
    <?php
    $disable = '';
    $budget_div = 'display: none;';
    $project_rate_per_hour_div = 'display: none;';
    $hourly_rate_div = 'display: none;';
    if ( ! empty( $client_project ) ) {
        $billed_tasks = \App\ProjectTask::where('project_id', $client_project->id)->where('billable', 'yes')->where('billed', 'yes')->first();
        $billed_expenses = \App\Expense::where('project_id', $client_project->id)->where('billable', 'yes')->where('billed', 'yes')->first();

        if ( $billed_tasks || $billed_expenses ) {
            $disable = ' disabled';
        }

        if ( $client_project->billing_type_id == PROJECT_BILLING_TYPE_FIXED_PRICE ) {
            $budget_div = '';
        }
        if ( $client_project->billing_type_id == PROJECT_BILLING_TYPE_PROJECT_HOURS ) {
            $project_rate_per_hour_div = '';
        }
        if ( $client_project->billing_type_id == PROJECT_BILLING_TYPE_TASK_HOURS ) {
            $hourly_rate_div = '';
        }
    }

    $billing_type_id_old = old('billing_type_id');
    if ( ! empty( $billing_type_id_old ) ) {
        if ( $billing_type_id_old == PROJECT_BILLING_TYPE_FIXED_PRICE ) {
            $budget_div = '';
        }
        if ( $billing_type_id_old == PROJECT_BILLING_TYPE_PROJECT_HOURS ) {
            $project_rate_per_hour_div = '';
        }
        if ( $billing_type_id_old == PROJECT_BILLING_TYPE_TASK_HOURS ) {
            $hourly_rate_div = '';
        }
    }
    ?>
    {!! Form::select('billing_type_id', $billing_types, old('billing_type_id'), ['class' => 'form-control select2', 'required' => '','id'=>'billing_type_id', $disable]) !!}
    @if( ! empty( $disable ) && ! empty( $client_project ) )
    <input type="hidden" name="billing_type_id" value="{{$client_project->billing_type_id}}">
    @endif
    <p class="help-block"></p>
    @if($errors->has('billing_type_id'))
        <p class="help-block">
            {{ $errors->first('billing_type_id') }}
        </p>
    @endif

</div>
</div>

<div class="col-xs-{{COLUMNS}}" id="budget_div" style="{{$budget_div}}">
<div class="form-group">
    {!! Form::label('budget', trans('global.client-projects.fields.budget').' ' . getDefaultCurrency(), ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::number('budget', old('budget'), ['class' => 'form-control','min'=>'0','step'=>'.01','placeholder' => trans('global.client-projects.fields.budget') . ' ' . getDefaultCurrency()]) !!}
    <p class="help-block"></p>
    @if($errors->has('budget'))
        <p class="help-block">
            {{ $errors->first('budget') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}" id="hourly_rate_div" style="{{$hourly_rate_div}}">
<div class="form-group">
    {!! Form::label('hourly_rate', trans('global.client-projects.fields.hourly_rate').' ' . getDefaultCurrency(), ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::number('hourly_rate', old('hourly_rate'), ['class' => 'form-control','min'=>'0','step'=>'.01' , 'placeholder' => trans('global.client-projects.fields.hourly_rate') . ' ' . getDefaultCurrency()]) !!}
    <p class="help-block"></p>
    @if($errors->has('hourly_rate'))
        <p class="help-block">
            {{ $errors->first('hourly_rate') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}" id="project_rate_per_hour_div" style="{{$project_rate_per_hour_div}}">
<div class="form-group">
    {!! Form::label('project_rate_per_hour', trans('global.client-projects.fields.project_rate_per_hour').' ' . getDefaultCurrency(), ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::number('project_rate_per_hour', old('project_rate_per_hour'), ['class' => 'form-control','min'=>'0', 'step'=>'.01' ,'placeholder' => trans('global.client-projects.fields.project_rate_per_hour') . ' ' . getDefaultCurrency()]) !!}
    <p class="help-block"></p>
    @if($errors->has('project_rate_per_hour'))
        <p class="help-block">
            {{ $errors->first('project_rate_per_hour') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}" id="estimated_hours">
<div class="form-group">
    {!! Form::label('estimated_hours', trans('global.client-projects.fields.estimated_hours').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::number('estimated_hours', old('estimated_hours'), ['class' => 'form-control amount','min'=>'0', 'placeholder' => '','min'=>'1']) !!}
    <p class="help-block"></p>
    @if($errors->has('estimated_hours'))
        <p class="help-block">
            {{ $errors->first('estimated_hours') }}
        </p>
    @endif
</div>
</div>
</div>
</div>

<div class="row">

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('phase', trans('global.client-projects.fields.phase').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::number('phase', old('phase'), ['class' => 'form-control amount','min'=>'0','step'=>'.01' , 'placeholder' => 'Eg: 1, 2 etc.']) !!}
    <p class="help-block"></p>
    @if($errors->has('phase'))
        <p class="help-block">
            {{ $errors->first('phase') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('progress_from_tasks', trans('global.client-projects.fields.progress_from_tasks').'*', ['class' => 'control-label']) !!}
    {!! Form::select('progress_from_tasks', yesnooptions(), old('progress_from_tasks'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('progress_from_tasks'))
        <p class="help-block">
            {{ $errors->first('progress_from_tasks') }}
        </p>
    @endif
</div>
</div>




<div class="col-xs-{{COLUMNS}}">
<div class="form-group">

    {!! Form::label('start_date', trans('global.client-projects.fields.start-date').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    <?php
    $start_date = digiTodayDateAdd();
    if ( ! empty( $client_project->start_date ) ) {
        $start_date = digiDate( $client_project->start_date );
    }
    ?>
    {!! Form::text('start_date', old('start_date', $start_date), ['class' => 'form-control date', 'placeholder' => 'Start Date']) !!}
    <p class="help-block"></p>
    @if($errors->has('start_date'))
        <p class="help-block">
            {{ $errors->first('start_date') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
 
    {!! Form::label('due_date', trans('global.client-projects.fields.due-date').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    <?php
    $due_date = digiTodayDateAdd(2);
    if ( ! empty( $client_project->due_date ) ) {
        $due_date = digiDate( $client_project->due_date );
    }
    ?>
    {!! Form::text('due_date', old('due_date', $due_date), ['class' => 'form-control date', 'placeholder' => 'Due Date']) !!}
    <p class="help-block"></p>
    @if($errors->has('due_date'))
        <p class="help-block">
            {{ $errors->first('due_date') }}
        </p>
    @endif
</div>
</div>
</div>

</div>

<div class="row">

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('status_id', trans('global.client-projects.fields.status').'*', ['class' => 'control-label']) !!}
    {!! Form::select('status_id', $statuses, old('status_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('status_id'))
        <p class="help-block">
            {{ $errors->first('status_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('demo_url', trans('global.client-projects.fields.demo-url').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('demo_url', old('demo_url'), ['class' => 'form-control', 'placeholder' => 'Demo url']) !!}
    <p class="help-block"></p>
    @if($errors->has('demo_url'))
        <p class="help-block">
            {{ $errors->first('demo_url') }}
        </p>
    @endif
</div>
</div>
</div>
</div>



<div class="row">
<div class="col-xs-6">
<div class="form-group">
    {!! Form::label('assigned_to', trans('global.client-projects.fields.assigned-to').'', ['class' => 'control-label']) !!}
    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-assigned_to">
        {{ trans('global.app_select_all') }}
    </button>
    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-assigned_to">
        {{ trans('global.app_deselect_all') }}
    </button>
     <?php
        $assigned_to = array();
        if ( ! empty( $client_project ) ) {
            $assigned_to = $client_project->assigned_to->pluck('id')->toArray();
        }
        ?>
    {!! Form::select('assigned_to[]', $assigned_tos, old('assigned_to'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-assigned_to' ]) !!}
    <p class="help-block"></p>
    @if($errors->has('assigned_to'))
        <p class="help-block">
            {{ $errors->first('assigned_to') }}
        </p>
    @endif
</div>
</div>


<div class="col-xs-6">
<div class="form-group">
    {!! Form::label('project_tabs', trans('global.client-projects.fields.project-tabs').'', ['class' => 'control-label']) !!}
    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-project_tabs">
        {{ trans('global.app_select_all') }}
    </button>
    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-project_tabs">
        {{ trans('global.app_deselect_all') }}
    </button>
    {!! Form::select('project_tabs[]', $project_tabs, old('project_tabs'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-project_tabs' ]) !!}
    <p class="help-block"></p>
    @if($errors->has('project_tabs'))
        <p class="help-block">
            {{ $errors->first('project_tabs') }}
        </p>
    @endif
</div>
</div>

</div>
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

 



