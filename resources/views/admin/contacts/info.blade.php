@extends('layouts.app')

@section('content')
<style>
.badge{
     background-color: #069478; 

}
</style>
    <h3 class="page-title">{{ $contact->name }}</h3>
    <div class="alert alert-warning" role="alert">
      <span style="font-size: 18px;">@lang('global.contacts.fields.alert-msg')<br/>
      @lang('global.contacts.fields.alert-msg-continue') </span>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.contacts.fields.record-info')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.contacts.fields.invoices-count')</th>
                            <td field-key='invoices_count'><span class="badge">{{ $invoices ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <th>@lang('global.contacts.fields.quotes-count')</th>
                            <td field-key='quotes_count'><span class="badge">{{ $quotes ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <th>@lang('global.contacts.fields.orders-count')</th>
                            <td field-key='orders_count'><span class="badge">{{ $orders ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <th> @lang('global.contacts.fields.credit-notes-count')</th>
                            <td field-key='credit_notes_count'><span class="badge">{{ $credit_notes ?? ''}}</span></td>
                        </tr>
                        
                      
                    </table>


                </div>
            <div class="col-md-2">
                
                {!! Form::open(array(
                    'style' => 'display: inline-block;',
                    'method' => 'post',
                    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                    'route' => ['admin.contacts.del_permanent', $contact->id])) !!}
                {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                <input type="hidden" name="redirect_url" value="{{url()->previous()}}">
                {!! Form::close() !!}
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-warning">@lang('global.cancel')</a>
            </div>

            </div>

            <p>&nbsp;</p>

            
        </div>
    </div>
@stop


