<div id="income">
<style type="text/css">

body{
  -webkit-print-color-adjust:exact;
}
.invoice-box {
	max-width: 1170px;
	margin: auto;
	padding: 30px;
	border: 2px solid #eee;
	box-shadow: 0 0 10px rgba(0, 0, 0, .15);
	font-size: 16px;
	line-height: 24px;
	font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
	color: #555;
	margin-top:10px;
}

.invoice-box table {
	
	text-align: left;
	
}

.invoice-box table td {
	padding: 5px;
	
	margin-right: -50px;
}

.invoice-box table tr td:nth-child(2) {
	text-align: right;
}

.invoice-box table tr.top table td {
	padding-bottom: 20px;
}

.invoice-box table tr.top table td.title {
	font-size: 45px;
	line-height: 45px;
	color: #333;
}

.invoice-box table tr.information table td {
	padding-bottom: 40px;
}

.invoice-box table tr.heading td {
	background: #eee;
	border-bottom: 1px solid #ddd;
	font-weight: bold;
}

.invoice-box table tr.details td {
	padding-bottom: 20px;
}

.invoice-box table tr.item td{
	border-bottom: 1px solid #eee;
}

.invoice-box table tr.item.last td {
	border-bottom: none;
}

.invoice-box table tr.total td:nth-child(2) {
	border-top: 2px solid #eee;
	font-weight: bold;
}

@media only screen and (max-width: 600px) {
	.invoice-box table tr.top table td {
		width: 100%;
		display: block;
		text-align: center;
	}
	
	.invoice-box table tr.information table td {
		width: 100%;
		display: block;
		text-align: center;
	}
}

/** RTL **/
.rtl {
	direction: rtl;
	font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
}

.rtl table {
	text-align: right;
}

.rtl table tr td:nth-child(2) {
	text-align: left;
}
.logo-head{
	 font-size: 20px;
    font-weight: bold;
    text-align: left;
    width: 80%;
    line-height: 1.2;
}
.bottom-text-p{
	font-size: 12px;
	font-weight: none;
	text-decoration: none;
}
.total{
	width: 100%;
}
</style>
<div class="invoice-box">
<table cellpadding="0" cellspacing="0" style="width: 100%;">
	<tr class="top">
		<td colspan="2">
			<?php
			$company_name = getSetting('company-name', 'receipt-settings');
			if ( empty( $company_name ) ) {
				$company_name = getSetting('site_title', 'site_settings');
			}

			$address = getSetting('address', 'receipt-settings');
			if ( empty( $address ) ) {
				$address = getSetting('site_address', 'site_settings');
			}

			$phone = getSetting('phone', 'receipt-settings');
			if ( empty( $phone ) ) {
				$phone = getSetting('site_phone', 'site_settings');
			}

			$email = getSetting('email', 'receipt-settings');
			if ( empty( $email ) ) {
				$email = getSetting('contact_email', 'site_settings');
			}

			$logo = getSetting('receipt-logo', 'receipt-settings');
            if ( empty( $logo ) ) {
                $logo = getSetting('site_logo', 'site_settings');
            }
			?>
			<table style="width: 100%;">
				<tr>
					<td class="title" style="width:70%;">
						
						<img src="{{asset( 'uploads/settings/' . $logo )}}" style="width: auto; height: 70px;">
						<p class="logo-head">{!! clean($company_name) !!}</p>
					</td>
					
					<td style="width:30%;">
						<b>@lang('custom.incomes.receipt-no')</b> {{$income->id}}<br/>
						<b>@lang('custom.incomes.reference-no')</b> {{$income->ref_no}}<br/>
						<b>@lang('custom.incomes.created')</b>  {{digiDate($income->created_at)}}
						

					</td>

				</tr>
			</table>
		</td>
	</tr>
	
	<tr class="information">
		<td colspan="2">
			<table style="width: 100%;">
				<tr>
					<td>
					<p>{{$address}}</p>
					<p><b>@lang('others.phone')</b>{{$phone}}</p>
					<p><b>@lang('others.email')</b>{{$email}}</p>
					</td>
					
					<td>
						@if ( ! empty( $income->payer->first_name ))
                        <strong>{{$income->payer->first_name . ' ' . $income->payer->last_name}}</strong>
                        @endif
                        <p>{{$income->payer->fulladdress}}</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	 
	<tr class="heading">
		<td><p>@lang('custom.incomes.amount')</p></td>
		
		<td>
			<?php
			$amount = $income->amount;
			if ( ! empty( $income->original_amount ) ) {
				$amount = $income->original_amount;
			}					
			?>
			{{ digiCurrency( $amount, $income->original_currency_id ) }}
		</td>
	</tr>
	
	<tr class="details">
		<td>
			@lang('custom.incomes.date')
		</td>
		
		<td>
			{{$income->entry_date}}
		</td>
	</tr>
	
	<tr class="heading">
		<td>
			@lang('custom.incomes.paymethod')
		</td>
		
		<td>
			{{ $income->pay_method->name ?? '' }}
		</td>
	</tr>
	
	<tr class="item">
		<td>
			@lang('custom.incomes.receipt-no')
		</td>
		
		<td>
			{{$income->id}}
		</td>
	</tr>
	
	<tr class="item">
		<td>
			@lang('custom.incomes.description')
		</td>
		
		<td>
			{!! clean($income->description) !!}
		</td>
	</tr>
	
	<tr class="total">
		
		<td class="bottom-text-p">
		   @lang('custom.incomes.receipt-generated-on'){{digiTodayDate(true)}}
		</td>
	</tr>
</table>
</div>
</div>


