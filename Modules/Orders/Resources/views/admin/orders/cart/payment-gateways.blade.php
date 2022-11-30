<div class="row">
	<div class="col-md-12">
        
		@php
		$payment_gateways = \App\Settings::where('moduletype', '=', 'payment')->where('status', '=', 'Active')->get()->pluck('module', 'key');
		$default = getSetting('default_payment_gateway', 'site_settings', 'offline');
		@endphp
		@foreach( $payment_gateways as $key => $title )
            <div class="col-xs-12 form-group">
                {{ Form::radio('payment_gateway', $key, ( $key == $default ), ['id' => $key] ) }} <label for="{{$key}}">{{$title}}</label>
                <p class="help-block"></p>
                @if($errors->has('payment_gateway'))
                    <p class="help-block">
                        {{ $errors->first('payment_gateway') }}
                    </p>
                @endif
            </div>
        @endforeach        
	</div>
</div>