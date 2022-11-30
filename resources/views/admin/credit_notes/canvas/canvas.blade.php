<button class="pull-right btn-summary" data-toggle="collapse" data-target=".filters" aria-expanded="true"><i class="fa fa-filter"></i>&nbsp;@lang('others.statistics.filter')</button>

<button class="pull-right btn-summary" data-toggle="collapse" data-target=".canvas" aria-expanded="true"><i class="fa fa-bar-chart"></i>&nbsp;@lang('others.statistics.summary')</button>&nbsp;


<div id="canvas" class="collapse canvas show">
    <div class="panel panel-default canvas">
        <div class="panel-heading">
        	@lang('others.statistics.credit-notes-summary') 
       <div class="col-sm-2 pull-right" style="margin-top:-7px;">
            <?php
            $currencies = \App\Currency::get()->pluck('name', 'id');
            $default_currency = getDefaultCurrency('id');
            $disable = '';
            if ( isCustomer() ) {
                $default_currency = ! empty(Auth::user()->contact_reference->currency_id) ? Auth::user()->contact_reference->currency_id : $default_currency;
                $disable = ' disabled';
            }
            ?>
            {!! Form::select('currency_id', $currencies, $default_currency, ['class' => 'form-control select2', 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true', 'id' => 'currency_id', 'data-target' => route('admin.credit_notes.refresh-stats'), $disable]) !!}
        </div>
        </div>
   
        <div class="panel-body table-responsive" id="canvas-panel-body">
			 @include('admin.credit_notes.canvas.canvas-panel-body', ['currency_id' => $default_currency])
		</div>
	</div>
</div>
@section('javascript') 
@parent
<script>
	$('#currency_id').change(function() {
		$.ajax({
            method: 'POST',
            url: $(this).data('target'),
            data: {
                _token: _token,
                currency: $(this).val()
            }
        }).done(function ( data ) {
            $('#canvas-panel-body').html( data );
        });
	});
</script>
@endsection