@extends('layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.income.title')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-12">
                    @include('admin.incomes.receipt-menu', compact('income'))
                    @include('admin.incomes.receipt-content', compact('income'))                    
                </div>
            </div>

            <a href="{{ route('admin.incomes.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>

    
@stop

@section('javascript')
    @parent
    @include('admin.incomes.scripts', compact('income'))            
@stop
