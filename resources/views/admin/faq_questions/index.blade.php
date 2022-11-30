@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.faq-questions.title')</h3>
    @can('faq_question_create')
    <p>
        <a href="{{ route('admin.faq_questions.create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;@lang('global.app_add_new')</a>
        <a href="#" class="btn btn-warning" style="margin-left:5px;" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-o"></i>&nbsp;@lang('global.app_csvImport')</a>
        @include('csvImport.modal', ['model' => 'FaqQuestion', 'csvtemplatepath' => 'faq_questions.csv'])
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @include('admin.faq_questions.records-display')
        </div>
    </div>
@stop

@section('javascript') 
    @include('admin.faq_questions.records-display-scripts')
@endsection