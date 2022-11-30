<table class="table table-bordered table-striped ajaxTable @can('income_delete_multi') dt-select @endcan">
    <thead>
        <tr>
            @can('income_delete_multi')
                <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
            @endcan
            <th>@lang('global.income.fields.account')</th>
            <th>@lang('global.income.fields.income-category')</th>
            <th>@lang('global.income.fields.entry-date')</th>
            <th>@lang('global.income.fields.amount')</th>
            <th>@lang('global.income.fields.payer')</th>
            <th>@lang('global.income.fields.pay-method')</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
</table>