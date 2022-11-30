<input type="hidden" name="redirect" value="{{$redirect ?? ''}}">
<?php
if ( empty( $fetchaddress ) ) {
    $fetchaddress = 'no';
}
?>
<input type="hidden" name="fetchaddress" id="fetchaddress" value="{{$fetchaddress}}">
<?php
if ( empty( $selectedid ) ) {
    $selectedid = 'customer_id';
}
?>
<input type="hidden" name="selectedid" id="selectedid" value="{{$selectedid}}">

<div class="row">

    <div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
       
    {!! Form::label('first_name', trans('global.contacts.fields.first-name').'*', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('first_name'))
        <p class="help-block">
            {{ $errors->first('first_name') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        
    {!! Form::label('last_name', trans('global.contacts.fields.last-name').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('last_name'))
        <p class="help-block">
            {{ $errors->first('last_name') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('email', trans('global.contacts.fields.email').'*', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => '','required'=>'']) !!}
    <p class="help-block"></p>
    @if($errors->has('email'))
        <p class="help-block">
            {{ $errors->first('email') }}
        </p>
    @endif
</div>
</div>
</div>

@if( ! empty( $type ) && $type != LEADS_TYPE )
<div class="col-xs-{{COLUMNS}}">
<div class="form-group">
    <?php
    $options = array(
        'no' => trans('custom.common.no'),
        'yesinactivate' => trans('custom.common.yesinactivate'),
        'yesactivate' => trans('custom.common.yesactivate'),
    );
    ?>
    {!! Form::label('create_user', trans('global.contacts.fields.create_user'), ['class' => 'control-label']) !!}
    {!! Form::select('create_user',  $options, old('create_user'), ['class' => 'form-control select2', 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('create_user'))
        <p class="help-block">
            {{ $errors->first('create_user') }}
        </p>
    @endif
</div>
</div>
@endif
</div>

<div class="row">

    <div class="col-xs-6">
     <div class="form-group">
       
    {!! Form::label('contact_type', trans('global.contacts.fields.contact-type').'*', ['class' => 'control-label']) !!}
    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-contact_type">
        {{ trans('global.app_select_all') }}
    </button>
    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-contact_type">
        {{ trans('global.app_deselect_all') }}
    </button>
    <?php
    if ( empty( $type ) ) {
        $default = CUSTOMERS_TYPE;
    } else {
        $default = $type;
    }
    
    if ( ! empty( $default_contact_type ) ) {
        $default = $default_contact_type;
    }
    $contact_types_arr = old('contact_type') ? old('contact_type') : array( $default );
    if ( ! empty( $contact ) ) {
        $contact_types_arr = $contact->contact_type->pluck('id')->toArray();
    }
    ?>
    {!! Form::select('contact_type[]', $contact_types, $contact_types_arr, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-contact_type' , 'required' => '','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('contact_type'))
        <p class="help-block">
            {{ $errors->first('contact_type') }}
        </p>
    @endif
</div>
</div>


<div class="col-xs-6">
<div class="form-group">
    {!! Form::label('language', trans('global.contacts.fields.language').'', ['class' => 'control-label']) !!}
    <button type="button" class="btn btn-primary btn-xs" id="selectbtn-language">
        {{ trans('global.app_select_all') }}
    </button>
    <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-language">
        {{ trans('global.app_deselect_all') }}
    </button>
    <?php
    $contact_languages = ! empty($contact->language) ? $contact->language->pluck('id')->toArray() : array();
    ?>
    {!! Form::select('language[]', $languages, old('language') ? old('language') : $contact_languages, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-language' ,'data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('language'))
        <p class="help-block">
            {{ $errors->first('language') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="row">

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('currency_id', trans('global.invoices.fields.currency') . '*', ['class' => 'control-label']) !!}
        <?php
        $currencies = \App\Currency::get()->pluck('name', 'id')->prepend(trans('global.app_please_select'), '');
        $default_currency = getDefaultCurrency('id');
        $disable = '';
        if ( ! empty( $contact ) && haveTransactions( $contact->id ) ) {
            $disable = ' disabled';
        }
        ?>
        {!! Form::select('currency_id', $currencies, old('currency_id'), ['class' => 'form-control select2', 'data-live-search' => 'true', 'data-show-subtext' => 'true', 'required' => '', $disable]) !!}
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
    {!! Form::label('company_id', trans('global.contacts.fields.company').'', ['class' => 'control-label']) !!}
    @if( empty( $is_ajax ) || ( ! empty( $is_ajax ) && $is_ajax == 'no' ) )
        @if ( Gate::allows('contact_company_create') )
            @if( 'button' === $addnew_type )
            &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcompany" data-selectedid="company_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.contacts.fields.company') )])}}">{{ trans('global.app_add_new') }}</button>
            @else        
            &nbsp;<a class="modalForm" data-action="createcompany" data-selectedid="company_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.contacts.fields.company') )])}}"><i class="fa fa-plus-square"></i></a>
            @endif
        @endif
    @endif
    {!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control select2', 'data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('company_id'))
        <p class="help-block">
            {{ $errors->first('company_id') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('group_id', trans('global.contacts.fields.group').'', ['class' => 'control-label']) !!}
    @if( empty( $is_ajax ) || ( ! empty( $is_ajax ) && $is_ajax == 'no' ) )
        @if ( Gate::allows('contact_group_create') )
            @if( 'button' === $addnew_type )
            &nbsp;<button type="button" class="btn btn-danger modalForm"  data-action="creategroup" data-selectedid="group_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.contacts.fields.group') )])}}">{{ trans('global.app_add_new') }}</button>
            @else        
                &nbsp;<a class="modalForm" data-action="creategroup" data-selectedid="group_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.contacts.fields.group') )])}}"><i class="fa fa-plus-square"></i></a>
            @endif
        @endif
    @endif
    {!! Form::select('group_id', $groups, old('group_id'), ['class' => 'form-control select2', 'data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('group_id'))
        <p class="help-block">
            {{ $errors->first('group_id') }}
        </p>
    @endif
</div>
</div>



            




<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
       
    {!! Form::label('phone1_code', trans('global.contacts.fields.phone1_code').'', ['class' => 'control-label']) !!}
    {!! Form::select('phone1_code', $countries_code, old('phone1_code'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('phone1_code'))
        <p class="help-block">
            {{ $errors->first('phone1_code') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
       
    {!! Form::label('phone1', trans('global.contacts.fields.phone1').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('phone1', old('phone1'), ['class' => 'form-control number', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('phone1'))
        <p class="help-block">
            {{ $errors->first('phone1') }}
        </p>
    @endif
</div>
</div>
</div>




<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('phone2_code', trans('global.contacts.fields.phone2_code').'', ['class' => 'control-label']) !!}
    {!! Form::select('phone2_code', $countries_code, old('phone2_code'), ['class' => 'form-control select2','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('phone2_code'))
        <p class="help-block">
            {{ $errors->first('phone2_code') }}
        </p>
    @endif
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('phone2', trans('global.contacts.fields.phone2').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('phone2', old('phone2'), ['class' => 'form-control number', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('phone2'))
        <p class="help-block">
            {{ $errors->first('phone2') }}
        </p>
    @endif
</div>
</div>
</div>





<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('skype', trans('global.contacts.fields.skype').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('skype', old('skype'), ['class' => 'form-control', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('skype'))
        <p class="help-block">
            {{ $errors->first('skype') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('tax_id', trans('global.contacts.fields.tax-id').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('tax_id', old('tax_id'), ['class' => 'form-control', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('tax_id'))
        <p class="help-block">
            {{ $errors->first('tax_id') }}
        </p>
    @endif
</div>
</div>
</div>





<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('address', trans('global.contacts.fields.address').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('address'))
        <p class="help-block">
            {{ $errors->first('address') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('city', trans('global.contacts.fields.city').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('city', old('city'), ['class' => 'form-control', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('city'))
        <p class="help-block">
            {{ $errors->first('city') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('state_region', trans('global.contacts.fields.state-region').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::text('state_region', old('state_region'), ['class' => 'form-control', 'placeholder' => '']) !!}
    <p class="help-block"></p>
    @if($errors->has('state_region'))
        <p class="help-block">
            {{ $errors->first('state_region') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('zip_postal_code', trans('global.contacts.fields.zip-postal-code').'', ['class' => 'control-label form-label']) !!}
    <div class="form-line">
    {!! Form::number('zip_postal_code', old('zip_postal_code'), ['class' => 'form-control', 'placeholder' => '','min'=>'0']) !!}
    <p class="help-block"></p>
    @if($errors->has('zip_postal_code'))
        <p class="help-block">
            {{ $errors->first('zip_postal_code') }}
        </p>
    @endif
</div>
</div>
</div>

<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
    {!! Form::label('country_id', trans('global.contacts.fields.country').'', ['class' => 'control-label']) !!}
    @if( empty( $is_ajax ) || ( ! empty( $is_ajax ) && $is_ajax == 'no' ) )
        @if ( Gate::allows('country_create') )
            @if( 'button' === $addnew_type )
            &nbsp;<button type="button" class="btn btn-danger modalForm" data-action="createcountry" data-selectedid="country_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.contacts.fields.country') )])}}">{{ trans('global.app_add_new') }}</button>
            @else        
            &nbsp;<a class="modalForm" data-action="createcountry" data-selectedid="country_id" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('global.contacts.fields.country') )])}}"><i class="fa fa-plus-square"></i></a>
            @endif
        @endif
    @endif
    {!! Form::select('country_id', $countries, old('country_id'), ['class' => 'form-control select2 country','data-live-search' => 'true','data-show-subtext' => 'true']) !!}
    <p class="help-block"></p>
    @if($errors->has('country_id'))
        <p class="help-block">
            {{ $errors->first('country_id') }}
        </p>
    @endif
</div>
</div>
 


@if( ! empty( $type ) && $type != LEADS_TYPE )
<div class="col-xs-{{COLUMNS}}">
    <div class="form-group">
        {!! Form::label('language_id', trans('global.contacts.fields.site-language'), ['class' => 'control-label']) !!}
        {!! Form::select('language_id', $languages, old('language_id'), ['class' => 'form-control select2', 'required' => '', 'data-live-search' => 'true', 'data-show-subtext' => 'true']) !!}
        <p class="help-block">@lang('global.contacts.site-language-help')</p>
        @if($errors->has('language_id'))
            <p class="help-block">
                {{ $errors->first('language_id') }}
            </p>
        @endif                    
    </div>
</div>
@endif

@if( empty( $is_ajax ) )
<div class="col-xs-8">
<div class="form-group"> 
    
    @if (!empty($contact->thumbnail) && file_exists(public_path() . '/thumb/' . $contact->thumbnail) )
        <a href="{{ asset(env('UPLOAD_PATH').'/'.$contact->thumbnail) }}" target="_blank"><img src="{{ asset(env('UPLOAD_PATH').'/thumb/'.$contact->thumbnail) }}" style="margin-top: 10px; margin-bottom: 5px;"></a>
    @endif
    {!! Form::label('thumbnail', trans('global.products.fields.thumbnail').'', ['class' => 'control-label']) !!}
    {!! Form::file('thumbnail', ['class' => 'form-control', 'style' => 'margin-top: 4px;']) !!}
    {!! Form::hidden('thumbnail_max_size', 10) !!}
    {!! Form::hidden('thumbnail_max_width', 4096) !!}
    {!! Form::hidden('thumbnail_max_height', 4096) !!}
    <p class="help-block"></p>
    @if($errors->has('thumbnail'))
        <p class="help-block">
            {{ $errors->first('thumbnail') }}
        </p>
    @endif
</div>
</div>
@endif
</div>

<div class="row">
<div class="col-xs-12">
<h4>@lang('global.contacts.title_delivery_address')</h4>
</div>
</div>
<div class="row">
@include('admin.contacts.delivery-address-fields', compact('contact'))
</div>