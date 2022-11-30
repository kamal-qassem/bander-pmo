<table class="table table-bordered table-striped {{ count($faq_questions) > 0 ? 'datatable' : '' }} @can('faq_question_delete_multi') dt-select @endcan">
                <thead>
                    <tr>
                        @can('faq_question_delete_multi')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('global.faq-questions.fields.category')</th>
                        <th>@lang('global.faq-questions.fields.question-text')</th>
                        <th>@lang('global.faq-questions.fields.answer-text')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($faq_questions) > 0)
                        @foreach ($faq_questions as $faq_question)
                            <tr data-entry-id="{{ $faq_question->id }}">
                                @can('faq_question_delete_multi')
                                    <td></td>
                                @endcan

                                <td field-key='category'>{{ $faq_question->category->title ?? '' }}</td>
                                <td field-key='question_text'>{!! clean($faq_question->question_text) !!}</td>
                                <td field-key='answer_text'>{!! clean($faq_question->answer_text) !!}</td>
                                                                <td>
                                    @can('faq_question_view')
                                    <a href="{{ route('admin.faq_questions.show',[$faq_question->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('faq_question_edit')
                                    <a href="{{ route('admin.faq_questions.edit',[$faq_question->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('faq_question_delete_multi')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.faq_questions.destroy', $faq_question->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>