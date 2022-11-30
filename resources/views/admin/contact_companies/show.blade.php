@extends('layouts.app')

@section('content')
    <h3 class="page-title">{{ $contact_company->name }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.contact_companies.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'contact_company_edit',
            ], 
            [
                'route' => 'admin.contact_companies.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'contact_company_delete',
            ],
        ],
        'record' => $contact_company,
        ] )
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
         <?php
        $tabs = [
            'details_active' => 'active',
            'contacts_active' => '',
        ];
        
        if ( ! empty( $list ) ) {
            foreach ($tabs as $key => $value) {
                $tabs[ $key ] = '';
                if ( substr( $key, 0, -7) == $list ) {
                    $tabs[ $key ] = 'active';
                }
            }
        }
        ?>
        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                   
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    
<li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>
<li role="presentation" class="{{$tabs['contacts_active']}}"><a href="{{route('admin.contact_companies.show', [ 'contact_company_id' => $contact_company->id, 'list' => 'contacts' ])}}" title="@lang('global.contacts.title')">@lang('global.contacts.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    
<div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">
<div class="pull-right">
            
            @can('contact_company_edit')
                <a href="{{ route('admin.contact_companies.edit',[$contact_company->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
                @endcan
                       </div> 
     <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.contact-companies.fields.name')</th>
                            <td field-key='name'>{{ $contact_company->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.contact-companies.fields.email')</th>
                            <td field-key='email'>{{ $contact_company->email }}</td>
                        </tr>
                        

                          <tr>
            <th>@lang('global.contacts.fields.country')</th>
            <td field-key='country'>{{ $contact_company->country->title ?? '' }} {{ isset($contact_company->country) ? '(' . $contact_company->country->shortcode . ')' : '' }}</td>
        </tr>

                        <tr>
                            <th>@lang('global.contact-companies.fields.address')</th>
                            <td field-key='address'>{{ $contact_company->address }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.companies.fields.url')</th>
                            <td field-key='website'><a href="{{ $contact_company->website }}" target="_blank">{{ $contact_company->website }}</a></td>
                        </tr>
                    </table>

</div>
@if ( 'active' === $tabs['contacts_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['contacts_active']}}" id="contacts">
    @include('admin.contacts.records-display')
</div>
@endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.contact_companies.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript') 
    @if ( 'active' === $tabs['contacts_active'] )
        @include('admin.contacts.records-display-scripts', [ 'type' => 'contact_company', 'type_id' => $contact_company->id ])
    @endif
@endsection
