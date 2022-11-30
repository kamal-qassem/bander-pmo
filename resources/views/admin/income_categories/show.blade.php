@extends('layouts.app')

@section('content')
   <h3 class="page-title">{{ $income_category->name }}
        @include('admin.common.drop-down-menu', [
        'links' => [
            [
                'route' => 'admin.income_categories.edit', 
                'title' => trans('global.app_edit'), 
                'type' => 'edit',
                'icon' => '<i class="fa fa-pencil-square-o" style="margin-right: 15px;"></i>',
                'permission_key' => 'income_category_edit',
            ], 
            [
                'route' => 'admin.income_categories.destroy', 
                'title' => trans('global.app_delete'), 
                'type' => 'delete',
                'icon' => '<i class="fa fa-trash-o" style="margin-right: 5px;color:#ff0000;padding-left: 20px;"></i>',
                'redirect_url' => url()->previous(),
                'permission_key' => 'income_category_delete',
            ],
        ],
        'record' => $income_category,
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
            'income_active' => '',
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
<li role="presentation" class="{{$tabs['income_active']}}"><a href="{{route('admin.income_categories.show', [ 'income_category_id' => $income_category->id, 'list' => 'income' ])}}" title= "@lang('global.income.title')">@lang('global.income.title')</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

  <div role="tabpanel" class="tab-pane {{$tabs['details_active']}}" id="details">

        <div class="pull-right">
            @can('income_category_edit')
                <a href="{{ route('admin.income_categories.edit',[$income_category->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>@lang('global.app_edit')</a>
            @endcan
        </div> 
        
          <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.income-category.fields.name')</th>
                            <td field-key='name'>{{ $income_category->name }}</td>
                        </tr>
                    </table>

    </div>   
    
@if ( 'active' === $tabs['income_active'] )
<div role="tabpanel" class="tab-pane {{$tabs['income_active']}}" id="income">
    @include('admin.incomes.records-display')
</div>
@endif
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.income_categories.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop
@section('javascript') 
    @if ( 'active' === $tabs['income_active'] )
        @include('admin.incomes.records-display-scripts', [ 'type' => 'income_category', 'type_id' => $income_category->id ])
     @endif
@endsection



