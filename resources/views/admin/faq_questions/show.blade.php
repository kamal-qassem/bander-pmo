@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('global.faq-questions.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
           {!! $faq_question->question_text !!}
        </div>

        <div class="panel-body table-responsive">
             @if( Gate::allows('faq_question_edit') || Gate::allows('faq_question_delete'))
            <div class="pull-right">   
                @if( Gate::allows('faq_question_edit') )
                    <a href="{{ route('admin.faq_questions.edit', $faq_question->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('faq_question_delete'))
                    @include('admin.common.delete-link', ['record' => $faq_question, 'routeName' => 'admin.faq_questions.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.faq-questions.fields.category')</th>
                            <td field-key='category'>{{ $faq_question->category->title ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.faq-questions.fields.question-text')</th>
                            <td field-key='question_text'>{!! $faq_question->question_text !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.faq-questions.fields.answer-text')</th>
                            <td field-key='answer_text'>{!! $faq_question->answer_text !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.faq_questions.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

@stop
