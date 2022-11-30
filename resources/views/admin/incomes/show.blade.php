@extends('layouts.app')

@section('content')
    <h3 class="page-title"> {{ digiCurrency($income->amount,$income->currency_id) }} - {{' ('.$income->entry_date.')' }}
        
    </h3>

    <div class="panel panel-default">
        @if ( 'yes' === $show_page_heading )
            <div class="panel-heading">
                @lang('global.app_view')
            </div>
        @endif

        <div class="panel-body table-responsive">
            @if( Gate::allows('income_edit') || Gate::allows('income_delete'))
            <div class="pull-right">   
                @if( Gate::allows('income_edit') )
                    <a href="{{ route('admin.incomes.edit', $income->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('income_delete'))
                    @include('admin.common.delete-link', ['record' => $income, 'routeName' => 'admin.incomes.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('global.income.fields.account')</th>
                            <td field-key='account'>{{ $income->account->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.income.fields.income-category')</th>
                            <td field-key='income_category'>{{ $income->income_category->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.income.fields.entry-date')</th>
                            <td field-key='entry_date'>{{ $income->entry_date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.income.fields.amount')</th>
                            <td field-key='amount'>{{ digiCurrency($income->amount,$income->currency_id) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.income.fields.description')</th>
                            <td field-key='description'>{!! clean($income->description) !!}</td>
                        </tr>
                       
                        <tr>
                            <th>@lang('global.income.fields.description-file')</th>
                            <td field-key='description_file's> @foreach($income->getMedia('description_file') as $media)
                                <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}" >{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                        <tr>
                            <th>@lang('global.income.fields.payer')</th>
                            <td field-key='payer'>{{ $income->payer->first_name ?? $income->payer_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.income.fields.pay-method')</th>
                            <td field-key='pay_method'>{{ $income->pay_method->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.income.fields.ref-no')</th>
                            <td field-key='ref_no'>{{ $income->ref_no }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.incomes.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
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
