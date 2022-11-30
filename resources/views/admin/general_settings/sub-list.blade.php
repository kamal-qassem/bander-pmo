@extends('layouts.app')

@section('header_scripts')
<link href="{{CSS}}checkbox.css" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <h3 class="page-title">{{trans('custom.settings.settings')}}</h3>

     <div class="panel panel-default">
        <div class="panel-heading">
            {{ isset($title) ? $title : ''}}
        </div>

       
        
        	
            <div class="panel-body packages">
                    <div class="row">
                        @if($record->image)
                        <img src="{{IMAGE_PATH_SETTINGS.$record->image}}" width="100" height="100">
                        @endif

                    </div>
                    <?php
                    $roles = \App\Role::all();
                    ?>
                    {!! Form::open(array('url' => URL_SETTINGS_ADD_SUBSETTINGS.$record->slug, 'method' => 'PATCH', 
                        'novalidate'=>'','name'=>'formSettings ', 'files'=>'true')) !!}
                        <div class="row"> 
                        <ul class="list-group">
                        @if(!empty($settings_data))
                        @foreach($settings_data as $key=>$value)
                        <?php
                        $except_keys = array( 'default_payment_gateway', 'default_sms_gateway', 'default-account', 'default-category', 'default-category-recurring', 'mailchimp_default_lists', 'default_expense_caterory' );
                        foreach( $roles as $role ) {
                          $except_keys[] = 'default-mailchimplist-' . strtolower($role->slug);
                        }
                        if ( in_array( $key, $except_keys ) ) {
                            continue;
                        }
                        $type_name = 'text';

                        if($value->type == 'number' || $value->type == 'email' || $value->type=='password')
                            $type_name = 'text';
                        else
                            $type_name = $value->type;
                        ?>
                         {{-- {{dd($value)}} --}}
                        @include(
                                    'admin.general_settings.sub-list-views.'.$type_name.'-type', 
                                    array('key'=>$key, 'value'=>$value)
                                )
                          @endforeach

                          @else
                              <li class="list-group-item">{{ trans('custom.settings.no_records_found')}}</li>
                          @endif
                        </ul>

                        </div>
                        @if( $record->slug == 'mailchimp-settings')
                        <?php
                        $api_key = getSetting('mailchimp_api_key', 'mailchimp-settings', '');

                        $MailChimp = new DrewM\MailChimp\MailChimp( trim( $api_key ) );
                        $mailchimp_lists = $MailChimp->get('lists');
                        
                        if ( ! empty( $mailchimp_lists['lists'] ) && ! empty( $roles ) ) {
                        ?>
                        <div class="row">
                            
                            @foreach( $roles as $role )
                            <div class="col-md-6">
                              <fieldset class="form-group">
                               <label for="default-account">@lang('custom.settings.default-mailchimp-list', [ 'role' => $role->title] )</label>
                               <?php                              
                               $selected = getSetting('default-mailchimplist-'.strtolower($role->slug), 'mailchimp-settings', '');
                               ?>                              
                               <select name="default-mailchimplist-{{strtolower($role->slug)}}[value]" class="form-control" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Default mailchimplist for: {{strtolower($role->slug)}}" aria-describedby="tooltip382729">
                                    <option value="" @if( '' == $selected ) selected="selected" @endif >@lang('orders::global.orders.no-account')</option>
                                    @foreach($mailchimp_lists['lists'] as $list)                                                                          
                                      <option value="{{$list['id']}}" @if( $list['id'] == $selected ) selected="selected" @endif >{{$list['name']}}</option>
                                     @endforeach
                                </select>
                                <input type="hidden" name="default-mailchimplist-{{strtolower($role->slug)}}[type]" value="select">                    
                                <input type="hidden" name="default-mailchimplist-{{strtolower($role->slug)}}[tool_tip]" value="@lang('custom.settings.default-mailchimp-list', [ 'role' => $role->title] )">
                                </fieldset>
                            </div>
                            @endforeach
                            
                        </div>
                      <?php } ?>
                        @endif

                        @if( 'credit-note-settings' === $record->slug )
                          <div class="row">
                            <div class="col-md-6">
                               <fieldset class="form-group">
                               <label for="default_expense_caterory">@lang('custom.settings.default-expense-category')</label>

                               <?php
                               $expense_categories = \App\ExpenseCategory::get();
                               $selected = getSetting('default_expense_caterory', 'credit-note-settings', '');
                               ?>                              
                               <select name="default_expense_caterory[value]" class="form-control" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="@lang('custom.settings.default-expense-category')" aria-describedby="tooltip382729">
                                    <option value="" @if( '' == $selected ) selected="selected" @endif >
                                    @foreach($expense_categories as $expense_category)
                                    <option value="{{$expense_category->id}}" @if( $expense_category->id == $selected ) selected="selected" @endif >{{$expense_category->name}}</option>
                                     @endforeach
                                </select>
                                <input type="hidden" name="default_expense_caterory[type]" value="select">                    
                                <input type="hidden" name="default_expense_caterory[tool_tip]" value="@lang('custom.settings.default-expense-category')">
                                </fieldset>
                            </div>
                          </div>
                        @endif

                        @if( in_array( $record->slug, array( 'order-settings', 'invoice-settings', 'credit-note-settings', 'purchase-orders-settings') ) )
                        <div class="row"> 
                            <div class="col-md-6">
                               <fieldset class="form-group">
                               <label for="default-account">@lang('custom.settings.default-account')</label>

                               <?php
                               $accounts = \App\Account::all();
                               $selected = '';
                               if ( 'order-settings' === $record->slug ) {
                                  $selected = getSetting('default-account', 'order-settings', '');
                              }
                              if ( 'invoice-settings' === $record->slug ) {
                                  $selected = getSetting('default-account', 'invoice-settings', '');
                              }
                              if ( 'credit-note-settings' === $record->slug ) {
                                  $selected = getSetting('default-account', 'credit-note-settings', '');
                              }
                              if ( 'purchase-orders-settings' === $record->slug ) {
                                  $selected = getSetting('default-account', 'purchase-orders-settings', '');
                              }
                              
                               ?>                              
                               <select name="default-account[value]" class="form-control" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="@lang('custom.settings.default-account')" aria-describedby="tooltip382729">
                                    <option value="" @if( '' == $selected ) selected="selected" @endif >@lang('orders::global.orders.no-account')</option>
                                    @foreach($accounts as $account)
                                    <option value="{{$account->id}}" @if( $account->id == $selected ) selected="selected" @endif >{{$account->name}}</option>
                                     @endforeach
                                </select>
                                <input type="hidden" name="default-account[type]" value="select">                    
                                <input type="hidden" name="default-account[tool_tip]" value="@lang('custom.settings.default-account')">
                                </fieldset>
                            </div>
                        </div>
						
						
            @if( $record->slug == 'invoice-settings')
							<div class="row"> 
								<div class="col-md-6">
								   <fieldset class="form-group">
								   <label for="default-category">@lang('custom.settings.default-category')</label>

								   <?php
								   $accounts = \App\IncomeCategory::all();
								   $selected = getSetting('default-category', 'invoice-settings', '');
								   ?>                           
								   <select name="default-category[value]" class="form-control" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="@lang('custom.settings.default-category')" aria-describedby="tooltip382729">
										<option value="" @if( '' == $selected ) selected="selected" @endif >@lang('custom.settings.no-category')</option>
										@foreach($accounts as $account)
										<option value="{{$account->id}}" @if( $account->id == $selected ) selected="selected" @endif >{{$account->name}}</option>
										 @endforeach
									</select>
									<input type="hidden" name="default-category[type]" value="select">                    
									<input type="hidden" name="default-category[tool_tip]" value="@lang('custom.settings.default-category')">
									</fieldset>
								</div>
							
								<div class="col-md-6">
								   <fieldset class="form-group">
								   <label for="default-category-recurring">@lang('custom.settings.default-category-recurring')</label>

								   <?php
								   $accounts = \App\IncomeCategory::all();
								   $selected = getSetting('default-category-recurring', 'invoice-settings', '');
								   ?>                           
								   <select name="default-category-recurring[value]" class="form-control" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="@lang('custom.settings.default-category-recurring')" aria-describedby="tooltip382729">
										<option value="" @if( '' == $selected ) selected="selected" @endif >@lang('custom.settings.no-category')</option>
										@foreach($accounts as $account)
										<option value="{{$account->id}}" @if( $account->id == $selected ) selected="selected" @endif >{{$account->name}}</option>
										 @endforeach
									</select>
									<input type="hidden" name="default-category-recurring[type]" value="select">                    
									<input type="hidden" name="default-category-recurring[tool_tip]" value="@lang('custom.settings.default-category-recurring')">
									</fieldset>
								</div>
							</div>
						@endif
                        @endif

                        @if( 'site-settings' === $record->slug )
                        <div class="row"> 
                            <div class="col-md-6">
                               <fieldset class="form-group">
                               <label for="default_payment_gateway">@lang('custom.settings.default-payment-gateway')</label>

                               <?php
                               $payment_gateways = \App\Settings::where('moduletype', 'payment')->get();
                               $selected = getSetting('default_payment_gateway', 'site_settings', 'offline');
                               ?>                              
                               <select name="default_payment_gateway[value]" class="form-control" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="@lang('custom.settings.default-payment-gateway')" aria-describedby="tooltip382729">
                                    @foreach($payment_gateways as $gateway)
                                    <option value="{{$gateway->key}}" @if( $gateway->key == $selected ) selected="selected" @endif >{{$gateway->module}}</option>
                                     @endforeach
                                </select>
                                <input type="hidden" name="default_payment_gateway[type]" value="select">                    
                                <input type="hidden" name="default_payment_gateway[tool_tip]" value="@lang('custom.settings.default-payment-gateway')">
                                </fieldset>
                            </div>

                            <div class="col-md-6">
                               <fieldset class="form-group">
                               <label for="default_sms_gateway">@lang('custom.settings.default-sms-gateway')</label>

                               <?php
                               $sms_gateways = \App\Settings::where('moduletype', 'sms')->get();
                               $selected = getSetting('default_sms_gateway', 'site_settings', '');
                               ?>                              
                               <select name="default_sms_gateway[value]" class="form-control" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="@lang('custom.settings.default-sms-gateway')" aria-describedby="tooltip382729">
                                    @foreach($sms_gateways as $gateway)
                                    <option value="{{$gateway->key}}" @if( $gateway->key == $selected ) selected="selected" @endif >{{$gateway->module}}</option>
                                     @endforeach
                                </select>
                                <input type="hidden" name="default_sms_gateway[type]" value="select">                    
                                <input type="hidden" name="default_sms_gateway[tool_tip]" value="@lang('custom.settings.default-sms-gateway')">
                                </fieldset>
                            </div>

                            <?php
                            Eventy::action('settings.site_settings.action', $record);
                            ?>
                        </div>
                        @endif
                        
                        @if(!empty($settings_data))
                        <br>
                        <div class="form-group pull-right">
                            <button class="btn btn-success" ng-disabled='!formTopics.$valid'
                            >{{ getPhrase('update') }}</button>
                        </div>
                        @endif
                        
                        


                            {!! Form::close() !!}
                    </div>



    	
@endsection


@section('footer_scripts')

<script src="{{JS}}bootstrap-toggle.min.js"></script>
@stop    