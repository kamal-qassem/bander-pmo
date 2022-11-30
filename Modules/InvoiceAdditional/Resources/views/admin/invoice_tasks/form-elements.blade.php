 <div class="row">
    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('name', trans('global.invoice-tasks.fields.name').'*', ['class' => 'control-label']) !!}
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('name'))
            <p class="help-block">
                {{ $errors->first('name') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('priority_id', trans('global.invoice-tasks.fields.priority').'*', ['class' => 'control-label']) !!}
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
        {!! Form::label('startdate', trans('global.invoice-tasks.fields.startdate').'*', ['class' => 'control-label']) !!}
        <?php
        $startdate = digiTodayDateAdd();
        if ( ! empty( $invoice_task ) ) {
            $startdate = ! empty( $invoice_task->startdate ) ? digiDate( $invoice_task->startdate ) : '';
        }
        ?>
        {!! Form::text('startdate', old('startdate', $startdate), ['class' => 'form-control date', 'placeholder' => '', 'required' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('startdate'))
            <p class="help-block">
                {{ $errors->first('startdate') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('duedate', trans('global.invoice-tasks.fields.duedate').'', ['class' => 'control-label']) !!}
        <?php
        $duedate = digiTodayDateAdd(2);
        if ( ! empty( $invoice_task ) ) {
            $duedate = ! empty( $invoice_task->duedate ) ? digiDate( $invoice_task->duedate ) : '';
        }
        ?>
        {!! Form::text('duedate', old('duedate', $duedate), ['class' => 'form-control date', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('duedate'))
            <p class="help-block">
                {{ $errors->first('duedate') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('datefinished', trans('global.invoice-tasks.fields.datefinished').'', ['class' => 'control-label']) !!}
        <?php
        $datefinished = digiTodayDateAdd(4);
        if ( ! empty( $invoice_task ) ) {
            $datefinished = ! empty( $invoice_task->datefinished ) ? digiDate( $invoice_task->datefinished ) : '';
        }
        ?>
        {!! Form::text('datefinished', old('datefinished', $datefinished), ['class' => 'form-control date', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('datefinished'))
            <p class="help-block">
                {{ $errors->first('datefinished') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('status_id', trans('global.invoice-tasks.fields.status').'', ['class' => 'control-label']) !!}
        {!! Form::select('status_id', $statuses, old('status_id'), ['class' => 'form-control select2']) !!}
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
        {!! Form::label('is_public', trans('global.invoice-tasks.fields.is-public').'', ['class' => 'control-label']) !!}
        {!! Form::select('is_public', $enum_is_public, old('is_public'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('is_public'))
            <p class="help-block">
                {{ $errors->first('is_public') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('billable', trans('global.invoice-tasks.fields.billable').'', ['class' => 'control-label']) !!}
        {!! Form::select('billable', $enum_billable, old('billable'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('billable'))
            <p class="help-block">
                {{ $errors->first('billable') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('recurring_id', trans('global.invoice-tasks.fields.recurring').'', ['class' => 'control-label']) !!}
        {!! Form::select('recurring_id', $recurrings, old('recurring_id'), ['class' => 'form-control select2', 'id' => 'recurring_id']) !!}
        <p class="help-block"></p>
        @if($errors->has('recurring_id'))
            <p class="help-block">
                {{ $errors->first('recurring_id') }}
            </p>
        @endif
        </div>
    </div>

    

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('recurring_value', trans('global.invoice-tasks.fields.recurring-value').'', ['class' => 'control-label']) !!}{!!digi_get_help(trans('global.recurring-invoices.recurring-value-help'), 'fa fa-question-circle')!!}
        <?php
        $recurring_value = 0;
        if ( ! empty( $invoice_task ) ) {
            $recurring_value = $invoice_task->recurring_value;
        }
        ?>
        {!! Form::number('recurring_value', old('recurring_value', $recurring_value), ['class' => 'form-control', 'placeholder' => '','min'=>'0']) !!}
        <p class="help-block"></p>
        @if($errors->has('recurring_value'))
            <p class="help-block">
                {{ $errors->first('recurring_value') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('recurring_type', trans('global.invoice-tasks.fields.recurring-type').'', ['class' => 'control-label']) !!}
        {!! Form::select('recurring_type', $enum_recurring_type, old('recurring_type'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('recurring_type'))
            <p class="help-block">
                {{ $errors->first('recurring_type') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('cycles', trans('global.invoice-tasks.fields.cycles').'', ['class' => 'control-label']) !!}{!!digi_get_help(trans('global.recurring-invoices.total-cycles-help'), 'fa fa-question-circle')!!}
        <?php
        $cycles = 0;
        if ( ! empty( $invoice_task ) ) {
            $cycles = $invoice_task->cycles;
        }
        ?>
        {!! Form::number('cycles', old('cycles', $cycles), ['class' => 'form-control', 'placeholder' => '','min'=>'0']) !!}
        <p class="help-block"></p>
        @if($errors->has('cycles'))
            <p class="help-block">
                {{ $errors->first('cycles') }}
            </p>
        @endif
        </div>
    </div>


    

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('billed', trans('global.invoice-tasks.fields.billed').'', ['class' => 'control-label']) !!}
        {!! Form::select('billed', $enum_billed, old('billed'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('billed'))
            <p class="help-block">
                {{ $errors->first('billed') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('hourly_rate', trans('global.invoice-tasks.fields.hourly-rate').'', ['class' => 'control-label']) !!}
        {!! Form::number('hourly_rate', old('hourly_rate'), ['class' => 'form-control', 'min'=>'0','placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('hourly_rate'))
            <p class="help-block">
                {{ $errors->first('hourly_rate') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('visible_to_client', trans('global.invoice-tasks.fields.visible-to-client').'', ['class' => 'control-label']) !!}
        {!! Form::select('visible_to_client', $enum_visible_to_client, old('visible_to_client'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('visible_to_client'))
            <p class="help-block">
                {{ $errors->first('visible_to_client') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('mile_stone_id', trans('global.invoice-tasks.fields.mile-stone').'', ['class' => 'control-label']) !!}
        {!! Form::select('mile_stone_id', $mile_stones, old('mile_stone_id'), ['class' => 'form-control select2']) !!}
        <p class="help-block"></p>
        @if($errors->has('mile_stone_id'))
            <p class="help-block">
                {{ $errors->first('mile_stone_id') }}
            </p>
        @endif
        </div>
    </div>

    <div class="col-xs-6">
    <div class="form-group">
        {!! Form::label('description', trans('global.invoice-tasks.fields.description').'', ['class' => 'control-label']) !!}
        {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => '']) !!}
        <p class="help-block"></p>
        @if($errors->has('description'))
            <p class="help-block">
                {{ $errors->first('description') }}
            </p>
        @endif
        </div>
    </div>


    <div class="col-xs-6">
    <div class="form-group">
        {!! Form::label('assigned_to', trans('global.invoice-tasks.fields.assigned-to').'', ['class' => 'control-label']) !!}
        <button type="button" class="btn btn-primary btn-xs" id="selectbtn-assigned_to">
            {{ trans('global.app_select_all') }}
        </button>
        <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-assigned_to">
            {{ trans('global.app_deselect_all') }}
        </button>
        <?php
        $assigned_to = array();
        if ( ! empty( $invoice_task ) ) {
            $assigned_to = $invoice_task->assigned_to->pluck('id')->toArray();
        }
        ?>
        {!! Form::select('assigned_to[]', $assigned_tos, old('assigned_to') ? old('assigned_to') : $assigned_to, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-assigned_to' ]) !!}
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
        {!! Form::label('attachments', trans('global.invoice-tasks.fields.attachments').'', ['class' => 'control-label']) !!}
        {!! Form::file('attachments[]', [
            'multiple',
            'class' => 'form-control file-upload',
            'data-url' => route('admin.media.upload'),
            'data-bucket' => 'attachments',
            'data-filekey' => 'attachments',
            'data-accept' => FILE_TYPES_GENERAL,
            ]) !!}
        <p class="help-block">{{trans('others.global_file_types_general')}}</p>
        <div class="photo-block">
            <div class="progress-bar">&nbsp;</div>
           
            <div class="files-list">   
            </div>
        

        </div>
        
        @if($errors->has('attachments'))
            <p class="help-block">
                {{ $errors->first('attachments') }}
            </p>
        @endif
        </div>
    </div>


</div>

