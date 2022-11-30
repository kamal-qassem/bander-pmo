<div class="row white-bg page-heading">

    <div class="col-lg-12">
        
        <div class="title-action">

            <a href="{{ route('admin.incomes.edit', $income->id) }}" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>

            <div class="btn-group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;{{trans('custom.invoices.email')}}&nbsp;<span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="#loadingModal" data-toggle="modal" data-remote="false" class="dropdown-item sendBill" data-action="income-created-ema" data-income_id="{{$income->id}}">{{trans('custom.invoices.receipt-notification')}}</a></li>
              </ul>
            </div>


            <div class="btn-group ">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" target="_blank">                                    
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;{{trans('custom.common.pdf')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('admin.incomes.receiptpdf', ['slug' => $income->slug, 'operation' => 'view'] )}}" target="_blank">{{trans('custom.common.view-pdf')}}</a></li>
                    
                    <li><a class="dropdown-item" href="{{route('admin.incomes.receiptpdf', $income->slug)}}">{{trans('custom.common.download-pdf')}}</a></li>

                </div>
            </div>
            <a href="{{route('admin.incomes.receiptpdf', ['slug' => $income->slug, 'operation' => 'print'] )}}" class="btn btn-large btn-primary buttons-print ml-sm" title="{{trans('custom.common.print')}}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> @lang('custom.common.print')</a>
            </div>
        </div>
</div>