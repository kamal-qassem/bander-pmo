<?php
$profile = Auth::user();
?>
<div class="col-md-{{$widget->columns}}">
<div class="panel panel-default">
    <div class="panel-heading">{{$profile->first_name . ' ' . $profile->last_name}}&nbsp;<a href="{{route('admin.contacts.profile.edit')}}">@lang('global.app_edit')</a></div>

    <div class="panel-body table-responsive">
        <div class="col-md-6">
            <p><b>@lang('global.contacts.fields.email') : </b>{{$profile->email}}</p>
            <p><b>@lang('global.contacts.fields.phone1') : </b> +{{$profile->phone1_code . '-' . $profile->phone1}}</p>
            @if( ! empty( $profile->skype ) )
            <p><b>@lang('global.contacts.fields.skype') : </b> {{$profile->skype}}</p>
            @endif
            @if( ! empty( $profile->address ) )
            <p><b>@lang('global.contacts.fields.address') : </b> {{$profile->address}}</p>
            @endif
            @if( ! empty( $profile->company ) )
            <p><b>@lang('global.contacts.fields.company') : </b> {{$profile->company->name}}</p>
            @endif
        </div>
        <div class="col-md-6">
            @if( ! empty( $profile->phone2 ) )
            <p><b>@lang('global.contacts.fields.phone2') : </b> +{{$profile->phone2_code . '-' . $profile->phone2}}</p>
            @endif
            @if( ! empty( $profile->city ) )
            <p><b>@lang('global.contacts.fields.city') : </b> {{$profile->city}}</p>
            @endif
            @if( ! empty( $profile->state_region ) )
            <p><b>@lang('global.contacts.fields.state-region') : </b> {{$profile->state_region}}</p>
            @endif
            @if( ! empty( $profile->zip_postal_code ) )
            <p><b>@lang('global.contacts.fields.zip-postal-code') : </b> {{$profile->zip_postal_code}}</p>
            @endif
            @if( ! empty( $profile->tax_id ) )
            <p><b>@lang('global.contacts.fields.tax-id') : </b> {{$profile->tax_id}}</p>
            @endif
            @if( ! empty( $profile->country ) )
            <p><b>@lang('global.contacts.fields.country') : </b> {{$profile->country->title}}</p>
            @endif
        </div>
    </div>
</div>
</div>