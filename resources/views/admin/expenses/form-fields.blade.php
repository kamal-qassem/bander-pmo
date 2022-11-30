 <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">                        
    {!! Form::label('name', trans('global.expense.fields.name').'*', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('name'))
        <p class="help-block">
            {{ $errors->first('name') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('account_id', trans('global.expense.fields.account').'*', ['class' => 'control-label']) !!}
    @if ( Gate::allows('account_create') )
        @if( 'button' === $addnew_type )
        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createaccount" data-selectedid="account_id" data-redirect="{{route('admin.incomes.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.account') )])}}">{{ trans('global.app_add_new') }}</button>
        @else        
        &nbsp;<a class="modalForm" data-action="createaccount" data-selectedid="account_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.account') )])}}"><i class="fa fa-plus-square"></i></a>
        @endif
    @endif

    {!! Form::select('account_id', $accounts, old('account_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('account_id'))
        <p class="help-block">
            {{ $errors->first('account_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('expense_category_id', trans('global.expense.fields.expense-category').'*', ['class' => 'control-label']) !!}
    @if ( Gate::allows('expense_category_create'))
        @if( 'button' === $addnew_type )
        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createexpensecategory" data-selectedid="expense_category_id" data-redirect="{{route('admin.expenses.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.expense-category') )])}}">{{ trans('global.app_add_new') }}</button>
        @else        
        &nbsp;<a class="modalForm" data-action="createexpensecategory" data-selectedid="expense_category_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.expense-category') )])}}"><i class="fa fa-plus-square"></i></a>
        @endif
    @endif
    {!! Form::select('expense_category_id', $expense_categories, old('expense_category_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('expense_category_id'))
        <p class="help-block">
            {{ $errors->first('expense_category_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group"> 
     
    {!! Form::label('entry_date', trans('global.expense.fields.entry-date').'*', ['class' => 'control-label form-label']) !!}

     <?php
        $entry_date = ! empty( $expense->entry_date ) ? digiDate( $expense->entry_date ) : '';
    ?> 
    <div class="form-line">
    {!! Form::text('entry_date', old('entry_date', $entry_date),['class' => 'form-control date', 'placeholder' => 'Entry date', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('entry_date'))
        <p class="help-block">
            {{ $errors->first('entry_date') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">    
    {!! Form::label('amount', trans('global.expense.fields.amount').'*', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::number('amount', old('amount'), ['class' => 'form-control amount','min'=>'1','step'=>'.01','placeholder' => 'amount', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('amount'))
        <p class="help-block">
            {{ $errors->first('amount') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('billable', trans('global.expense.fields.billable').'', ['class' => 'control-label']) !!}
    {!! Form::select('billable', yesnooptions(), old('billable'), ['class' => 'form-control select2']) !!}
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
    {!! Form::label('currency_id', trans('global.recurring-invoices.fields.currency').'*', ['class' => 'control-label']) !!}{!!digi_get_help(trans('global.expense.currency-help'), 'fa fa-question-circle')!!}
    <?php
    $default_currency = getDefaultCurrency('id');
    if ( ! empty( $expense ) ) {
        $default_currency = $expense->currency_id;
    }
    ?>
    {!! Form::select('currency_id', $currencies, old('currency_id', $default_currency), ['class' => 'form-control', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('currency_id'))
        <p class="help-block">
            {{ $errors->first('currency_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    {!! Form::label('payee_id', trans('global.expense.fields.payee').'', ['class' => 'control-label']) !!}
    @if ( Gate::allows('contact_create') && empty( $project ) )
        @if( 'button' === $addnew_type )
        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcustomer" data-selectedid="payee_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.payee') )])}}">{{ trans('global.app_add_new') }}</button>
        @else        
        &nbsp;<a class="modalForm" data-action="createcustomer" data-selectedid="payee_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.payee') )])}}"><i class="fa fa-plus-square"></i></a>
        @endif
    @endif
    {!! Form::select('payee_id', $payees, old('payee_id'), ['class' => 'form-control select2']) !!}
    <p class="help-block"></p>
    @if($errors->has('payee_id'))
        <p class="help-block">
            {{ $errors->first('payee_id') }}
        </p>
    @endif
</div>
</div>

@if ( empty( $project_id ) )
<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('project_id', trans('global.expense.fields.project'), ['class' => 'control-label']) !!}
    {!! Form::select('project_id', $projects, old('project_id'), ['class' => 'form-control select2']) !!}
    <p class="help-block"></p>
    @if($errors->has('project_id'))
        <p class="help-block">
            {{ $errors->first('project_id') }}
        </p>
    @endif
</div>
</div>
@else
<input type="hidden" name="project_id" value="{{$project_id}}">
@endif

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">                    
    {!! Form::label('recurring_period_id', trans('global.recurring-invoices.fields.recurring-period'), ['class' => 'control-label']) !!}
    @if ( Gate::allows('recurring_period_create'))
        @if( 'button' === $addnew_type )
        &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createrecurringperiod" data-selectedid="recurring_period_id" data-redirect="{{route('admin.expenses.create')}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.recurring-period') )])}}">{{ trans('global.app_add_new') }}</button>
        @else        
        &nbsp;<a class="modalForm" data-action="createrecurringperiod" data-selectedid="recurring_period_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.expense.fields.recurring-period') )])}}"><i class="fa fa-plus-square"></i></a>
        @endif
    @endif
    {!! Form::select('recurring_period_id', $recurring_periods, old('recurring_period_id'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('recurring_period_id'))
        <p class="help-block">
            {{ $errors->first('recurring_period_id') }}
        </p>
    @endif
</div>
</div>



<div class="col-xs-{{COLUMNS}}">
<div class="form-group">                   
    {!! Form::label('recurring_value', trans('global.recurring-invoices.fields.recurring_value'), ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('recurring_value', old('recurring_value'), ['class' => 'form-control number', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('recurring_value'))
        <p class="help-block">
            {{ $errors->first('recurring_value') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">                   
    {!! Form::label('recurring_type', trans('global.recurring-invoices.fields.recurring_type'), ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    <?php
    $recurring_types = array(
        'day' => trans('custom.common.days'),
        'week' => trans('custom.common.weeks'),
        'month' => trans('custom.common.months'),
        'year' => trans('custom.common.years'),
    );
    ?>
    
    {!! Form::select('recurring_type', $recurring_types, old('recurring_type'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true', 'required' => '', 'id' => 'recurring_type']) !!}
    <p class="help-block"></p>
    @if($errors->has('recurring_type'))
        <p class="help-block">
            {{ $errors->first('recurring_type') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
<div class="form-group">                   
    {!! Form::label('cycles', trans('global.expense.fields.cycles').'', ['class' => 'control-label form-label']) !!}{!!digi_get_help(trans('global.recurring-invoices.total-cycles-help'), 'fa fa-question-circle')!!}
    <div class="form-line">
    <?php
    $cycles = 0;
    if ( ! empty( $expense ) ) {
        $cycles = $expense->cycles;
    }
    if ( empty( $cycles ) ) {
        $cycles = 0;
    }
    ?>
    {!! Form::text('cycles', old('cycles', $cycles), ['class' => 'form-control number', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('cycles'))
        <p class="help-block">
            {{ $errors->first('cycles') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-12">
<div class="form-group">
    {!! Form::label('description', trans('global.expense.fields.description').'', ['class' => 'control-label']) !!}
    {!! Form::textarea('description', old('description'), ['class' => 'form-control editor', 'placeholder' => 'Description']) !!}
    <p class="help-block"></p>
    @if($errors->has('description'))
        <p class="help-block">
            {{ $errors->first('description') }}
        </p>
    @endif
</div>
</div>


<div class="col-xs-4">
<div class="form-group">
    {!! Form::label('payment_method_id', trans('global.expense.fields.payment-method').'*', ['class' => 'control-label']) !!}
    {!! Form::select('payment_method_id', $payment_methods, old('payment_method_id'), ['class' => 'form-control select2', 'required' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('payment_method_id'))
        <p class="help-block">
            {{ $errors->first('payment_method_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-4">
<div class="form-group">
    
    {!! Form::label('ref_no', trans('global.expense.fields.ref-no').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('ref_no', old('ref_no'), ['class' => 'form-control', 'placeholder' => 'Reference no / Transaction id']) !!}
    <p class="help-block"></p>
    @if($errors->has('ref_no'))
        <p class="help-block">
            {{ $errors->first('ref_no') }}
        </p>
    @endif
</div>
</div>
</div>

       <div class="col-xs-6">
<div class="form-group">
    {!! Form::label('description_file', trans('global.expense.fields.description-file').'', ['class' => 'control-label']) !!}
    {!! Form::file('description_file[]', [
        'multiple',
        'class' => 'form-control file-upload',
        'data-url' => route('admin.media.upload'),
        'data-bucket' => 'description_file',
        'data-filekey' => 'description_file',
        'data-accept' => FILE_TYPES_GENERAL,
        ]) !!}
    <p class="help-block">{{trans('others.global_file_types_general')}}</p>
    <div class="photo-block">
        <div class="progress-bar">&nbsp;</div>
        <div class="files-list">
            @if( ! empty( $expense ) )
            @foreach($expense->getMedia('description_file') as $media)
                <p class="form-group">
                    <a href="{{ route('admin.home.media-download', $media->id) }}" >{{ $media->name }} ({{ $media->size }} KB)</a>
                    <a href="#" class="btn btn-xs btn-danger remove-file">Remove</a>
                    <input type="hidden" name="description_file_id[]" value="{{ $media->id }}">
                </p>
            @endforeach
            @endif
        </div>
    </div>
    @if($errors->has('description_file'))
        <p class="help-block">
            {{ $errors->first('description_file') }}
        </p>
    @endif
</div>
</div>

@include('admin.common.modal-loading-submit')