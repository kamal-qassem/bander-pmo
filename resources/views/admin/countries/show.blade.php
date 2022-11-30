@extends('layouts.app')

@section('content')
     <h3 class="page-title">{{ $country->title }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.countries.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'country_edit',
            ], 
            [
                'route' => 'admin.countries.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'country_delete',
            ],
        ],
        'record' => $country,
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
            'companies_active' => '',
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
<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="{{$tabs['details_active']}}"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">@lang('others.canvas.details')</a></li>   
<li role="presentation" class="{{$tabs['companies_active']}}"><a href="{{route('admin.countries.show', [ 'country_id' => $country->id, 'list' => 'companies' ])}}" title="@lang('global.contact-companies.title')">@lang('global.contact-companies.title')</a></li>
<li role="presentation" class="{{$tabs['contacts_active']}}"><a href="{{route('admin.countries.show', [ 'country_id' => $country->id, 'list' => 'contacts' ])}}" title="@lang('global.contacts.title')">@lang('global.contacts.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

 <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

        <div class="pull-right">
        @can('country_edit')
          <a href="{{ route('admin.countries.edit',[$country->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
         @endcan
        </div>

    <table class="table table-bordered table-striped">

                         <tr>
                            <th>@lang('global.countries.fields.dailcode')</th>
                            <td field-key='dialcode'>{{ $country->dialcode }}</td>
                        </tr>    
                        <tr>
                            <th>@lang('global.countries.fields.shortcode')</th>
                            <td field-key='shortcode'>{{ $country->shortcode }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.countries.fields.title')</th>
                            <td field-key='title'>{{ $country->title }}</td>
                        </tr>
                    </table>    

</div>
@if ( 'active' === $tabs['companies_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['companies_active']}}" id="contact_companies">
    @include('admin.contact_companies.records-display')
</div>
@endif

@if ( 'active' === $tabs['contacts_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['contacts_active']}}" id="contacts">
    @include('admin.contacts.records-display')
</div>
@endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.countries.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript') 
    @if ( 'active' ===  $tabs['companies_active'])
        @include('admin.contact_companies.records-display-scripts', [ 'type' => 'country', 'type_id' => $country->id ])
    @endif

    @if ( 'active' === $tabs['contacts_active'] )
        @include('admin.contacts.records-display-scripts', [ 'type' => 'country', 'type_id' => $country->id ])
    @endif
@endsection


