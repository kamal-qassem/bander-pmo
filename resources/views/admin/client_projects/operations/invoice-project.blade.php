@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
@include('admin.client_projects.operations.menu', array('client_project' => $project))


    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.client-projects.invoice-project')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                {!! Form::open(['method' => 'POST', 'route' => ['admin.client_projects.invoice-project-preview', $project->id],'class'=>'formvalidation']) !!}
                <div class="col-xs-12 form-group">
                    {!! Form::label('invoice_data_type', trans('global.client-projects.invoice-data-type').'*', ['class' => 'control-label']) !!}
                    <p class="help-block"></p><br/>
                    @if($errors->has('invoice_data_type'))
                        <p class="help-block">
                            {{ $errors->first('invoice_data_type') }}
                        </p>
                    @endif
                    <div class="col-xs-3 well-note">
                        <label>
                            <?php
                            $disabled = '';
                            if ( in_array($project->billing_type_id, [ PROJECT_BILLING_TYPE_TASK_HOURS, PROJECT_BILLING_TYPE_PROJECT_HOURS]) ) {
                                $disabled = ' disabled = ""';
                            }
                            ?>
                            {!! Form::radio('invoice_data_type', 'single_line', false, ['required' => '', $disabled ]) !!}
                            Single line  [ Fixed Rate ]{!! digi_get_help( trans( 'global.client-projects.single_line_help' ) ) !!}
                        </label>
                    </div>
                    <div class="col-xs-3 well-note">
                        <label>
                            <?php
                            $disabled = '';
                            if ( $project->billing_type_id == PROJECT_BILLING_TYPE_FIXED_PRICE ) {
                                $disabled = ' disabled';
                            }
                            
                            ?>
                            {!! Form::radio('invoice_data_type', 'task_per_item', false, ['required' => '', $disabled]) !!}
                            Task per item{!! digi_get_help( trans( 'global.client-projects.task_per_item_help' ) ) !!}
                        </label>
                    </div>

                    <div class="col-xs-3 well-note2">
                        <label>
                            <?php
                            $disabled = '';
                            if ( $project->billing_type_id == PROJECT_BILLING_TYPE_FIXED_PRICE ) {
                                $disabled = ' disabled = ""';
                            }
                            ?>
                            {!! Form::radio('invoice_data_type', 'timesheets_include_notes', false, ['required' => '', $disabled]) !!}
                            Include each timesheet note in item description{!! digi_get_help( trans( 'global.client-projects.timesheets_include_notes_help' ) ) !!}
                        </label>
                    </div>
                    
                </div>


                

                @if ( $tasks->count() > 0 )
                    <div class="col-xs-12 form-group">
                        {!! Form::checkbox('select_all_tasks', 1, old('select_all_tasks', false), ['id' => 'select_all_tasks']) !!}
                        {!! Form::label('select_all_tasks', trans('global.client-projects.select-all-tasks'), ['class' => 'control-label']) !!}
                    </div><br/>
                    <div class="col-xs-12" >
                    <div class="colxs10" >
                    <div class="row">
                        <div style="border: 2px solid whitesmoke;" class="col-xs-3 well-note1 text-center"><strong>@lang('global.project-tasks.title')</strong></div>
                        <div style="border: 2px solid whitesmoke;" class="col-xs-3 well-note1 text-center"><strong>@lang('global.project-tasks.fields.status')</strong></div>
                        <div style="border: 2px solid whitesmoke;" class="col-xs-3 well-note1 text-center"><strong>@lang('global.project-tasks.timer')</strong></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach( $tasks as $task )                        
                    <div class="col-xs-12">
                        
                        <div class="col-xs-3 colxs3" style="text-align: left;">
                            {!! Form::checkbox('tasks[]', $task->id, old('tasks', false), ['id' => 'task_' . $task->id, 'class' => 'task']) !!}
                            {!! Form::label('task_' . $task->id, $task->name, ['class' => 'control-label' ]) !!}
                            <?php
                            $status_color = $task->task_status->color;
                            if ( empty( $status_color ) ) {
                                $status_color = 'red';
                            }
                            ?>                            
                        </div>
                        
                        <div class="col-xs-3 colxs3" style="text-align: center;">
                            <small class="hgt6"><span class="label" style="border: 1px solid {{$status_color}};color:{{$status_color}};">{{$task->status ? $task->task_status->title : 'N/A'}}</span></small>
                        </div>
                        <div class="col-xs-3 colxs3" style="text-align: center;">
                            <small class="tesfhhhh">
                            <?php
                            $timer = \App\TimeEntry::where('task_id', $task->id)->first();
                            if ( $timer ) {
                                $running_timer = \App\TimeEntry::where('task_id', $task->id)->whereNull('end_date')->first();
                                if ( $running_timer ) {
                                    echo '<span class="text-info">' . trans('global.project-tasks.running-timer') . '</span>';
                                } else {

                                }
                            } else {
                                echo '<span class="text-danger">' . trans('global.project-tasks.no-timers') . '</span>';
                            }
                            ?>
                            </small>
                           </div>
                        </div>
                    @endforeach
                    </div>
                
                @endif

                <div class="row">
                    <div class="col-xs-12">    

                @if ( $expenses->count() > 0 )
                    <div class="col-xs-12" style="margin-top: 15px;">
                        {!! Form::checkbox('select_all_expenses', 0, old('select_all_expenses', false), ['id' => 'select_all_expenses']) !!}
                        {!! Form::label('select_all_expenses', trans('global.client-projects.select-all-expenses'), ['class' => 'control-label']) !!}
                    </div>
                    <div class="col-xs-12">
                    @foreach( $expenses as $expense )
                        <div class="col-xs-6">
                            {!! Form::checkbox('expenses[]', $expense->id, old('expenses', false), ['id' => 'expense_' . $expense->id, 'class' => 'expense']) !!}
                            {!! Form::label('expense_' . $expense->id, $expense->name, ['class' => 'control-label']) !!}                            
                        </div>
                    @endforeach

                     <div class="col-xs-6 form-group" style="margin-left: -25px;">
                <p class="text-danger">@lang('global.client-projects.marked-as-complete')</p>

                </div>
            </div>
                    </div>
                    </div>
                @endif

               
                </div>  
            </div>

        </div>
        <div class="col-xs-12 form-group">
               
                {!! Form::submit(trans('global.app_create'), ['class' => 'btn btn-danger wave-effect']) !!}
                {!! Form::close() !!}  
                </div>
    </div>
@stop

@section('javascript')
@parent
    <script>
        
        $('#select_all_tasks').click(function() {
            if ( $('#select_all_tasks').is(':checked') ) {
                $('.task').prop('checked', true);
            } else {
                $('.task').prop('checked', false);
            }
        });

        $('#select_all_expenses').click(function() {
            if ( $('#select_all_expenses').is(':checked') ) {
                $('.expense').prop('checked', true);
            } else {
                $('.expense').prop('checked', false);
            }
        });
    </script>
@stop