@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.transfers.title')</h3>
    
    {!! Form::model($transfer, ['method' => 'PUT', 'route' => ['admin.transfers.update', $transfer->id],'class'=>'formvalidation']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('from_id', trans('global.transfers.fields.from').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('from_id', $froms, old('from_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('from_id'))
                        <p class="help-block">
                            {{ $errors->first('from_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                    {!! Form::label('to_id', trans('global.transfers.fields.to').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('to_id', $tos, old('to_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('to_id'))
                        <p class="help-block">
                            {{ $errors->first('to_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                        
                    {!! Form::label('date', trans('global.transfers.fields.date').'*', ['class' => 'control-label form-label']) !!}
                  <div class="form-line">
                    {!! Form::text('date', old('date'), ['class' => 'form-control date', 'placeholder' => 'Date', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('date'))
                        <p class="help-block">
                            {{ $errors->first('date') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-{{COLUMNS}}">
                    <div class="form-group">
                        
                    {!! Form::label('amount', trans('global.transfers.fields.amount').'*', ['class' => 'control-label form-label']) !!}
                    <div class="form-line">
                    {!! Form::text('amount', old('amount'), ['class' => 'form-control', 'placeholder' => 'Amount', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('amount'))
                        <p class="help-block">
                            {{ $errors->first('amount') }}
                        </p>
                    @endif
                </div>
                </div>
                </div>
            
                <div class="col-xs-6">
                    <div class="form-group">
                    {!! Form::label('ref_no', trans('global.transfers.fields.ref-no').'', ['class' => 'control-label form-label']) !!}
                  <div class="form-line">
                    {!! Form::text('ref_no', old('ref_no'), ['class' => 'form-control', 'placeholder' => 'Reference Number']) !!}
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
                    {!! Form::label('payment_method_id', trans('global.transfers.fields.payment-method').'*', ['class' => 'control-label']) !!}
                    {!! Form::select('payment_method_id', $payment_methods, old('payment_method_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('payment_method_id'))
                        <p class="help-block">
                            {{ $errors->first('payment_method_id') }}
                        </p>
                    @endif
                </div>
                </div>
            
                <div class="col-xs-6">
                    <div class="form-group">
                        
                    {!! Form::label('description', trans('global.transfers.fields.description').'', ['class' => 'control-label']) !!}
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

@section('javascript')
    @parent

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
@stop