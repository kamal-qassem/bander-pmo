@extends('layouts.app')

@section('content')
   <h3 class="page-title">{{ $expense->name ?? '' }}
        
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        @endif
        <div class="panel-body table-responsive">
            @if( Gate::allows('expense_edit') || Gate::allows('expense_delete'))
            <div class="pull-right">   
                @if( Gate::allows('expense_edit') )
                    <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('expense_delete'))
                    @include('admin.common.delete-link', ['record' => $expense, 'routeName' => 'admin.expenses.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.expense.fields.name')</th>
                            <td field-key='name'>{{ $expense->name ?? '' }}</td>
                        </tr>

                         <tr>
                            <th>@lang('global.expense.fields.billable')</th>
                            <td field-key='billable'>{{ $expense->billable ?? '' }}</td>
                        </tr>

                          <tr>
                            <th>@lang('global.expense.fields.project')</th>
                            <td field-key='project_title'>{{ $expense->project->title ?? '' }}</td>
                        </tr>

                    

                       

                        <tr>
                            <th>@lang('global.expense.fields.currency')</th>
                            <td field-key='recurring_period'>{{ $expense->currency->name ?? '' }}</td>
                        </tr>


                        @if( ! empty( $expense->recurring_value ) )

                        <tr>
                            <th>@lang('global.expense.fields.recurring-value')</th>
                            <td field-key='recurring_value'>{{ $expense->recurring_value ?? '' }}</td>
                        </tr>

                         <tr>
                            <th>@lang('global.expense.fields.recurring-type')</th>
                            <td field-key='recurring_type'>{{ $expense->recurring_type.'(s)' ?? '' }}</td>
                        </tr>

                         <tr>
                            <th>@lang('global.expense.fields.cycles')</th>
                            <td field-key='total_cycles'>{{ $expense->cycles ?? '' }}</td>
                        </tr>

                        @endif

                        @if( ! empty( $expense->recurring_value ) )

                         <tr>
                            <th>@lang('global.expense.fields.total-cycles')</th>
                            <td field-key='total_cycles'>{{ $expense->total_cycles ?? '' }}</td>
                        </tr>

                        @endif

                        <tr>
                            <th>@lang('global.expense.fields.account')</th>
                            <td field-key='account'>{{ $expense->account->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.expense-category')</th>
                            <td field-key='expense_category'>{{ $expense->expense_category->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.entry-date')</th>
                            <td field-key='entry_date'>{{ digiDate( $expense->entry_date ) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.amount')</th>
                            <td field-key='amount'>{{ digiCurrency($expense->amount,$expense->currency_id) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.description')</th>
                            <td field-key='description'>{!! clean($expense->description) !!}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.description-file')</th>
                            <td field-key='description_file's> @foreach($expense->getMedia('description_file') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.payee')</th>
                            <td field-key='payee'>{{ $expense->payee->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.payment-method')</th>
                            <td field-key='payment_method'>{{ $expense->payment_method->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.expense.fields.ref-no')</th>
                            <td field-key='ref_no'>{{ $expense->ref_no }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            @if(! empty( $expense->project_id ) )
            <a href="{{ route('admin.client_projects.expenses', $expense->project_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
            @else
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
            @endif
        </div>
    </div>
@stop

@section('javascript')
    @parent
    
    @include('admin.common.standard-ckeditor')

    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>
            
@stop
