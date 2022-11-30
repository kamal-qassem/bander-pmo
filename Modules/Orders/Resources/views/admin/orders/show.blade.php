@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('orders::global.orders.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>
        

        <div class="panel-body table-responsive">
            @if( Gate::allows('order_edit') || Gate::allows('order_delete'))
            <div class="pull-right">   
                @if( Gate::allows('order_edit') )
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>{{trans('global.app_edit')}}</a>
                @endif
                @if( Gate::allows('order_delete'))
                    @include('admin.common.delete-link', ['record' => $order, 'routeName' => 'admin.orders.destroy', 'redirect_url' => url()->previous()] )
                @endif
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('orders::global.orders.fields.id')</th>
                            <td field-key='customer'>#{{ $order->id ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('custom.date')</th>
                            <td field-key='customer'>{{ digiDate( $order->created_at, true ) ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('orders::global.orders.fields.customer')</th>
                            <td field-key='customer'>{{ $order->customer->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('orders::global.orders.fields.status')</th>
                            <td field-key='status' class="mt-1">
                                <?php
                                $title = $order->status;
                                $class = 'danger';
                                if ( 'Active' === $title ) {
                                    $title = 'Success';
                                    $class = 'success';
                                }
                                $status = $order->status ? '<span class="label label-'.$class.' label-many">'.$title.'</span>' : '';
                                ?>
                                {!! clean($status) !!}                              
                                @if ( isAdmin() || isExecutive() )
                                    <a class="modalForm" data-action="makeorderpayment" data-selectedid="customer_id" data-id="{{$order->id}}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{trans('global.add_new_title', ['title' => strtolower( trans('custom.payment') )])}}">{{trans('custom.invoices.make-payment')}}</a>
                                @endif
                            
                            </td>
                        </tr>
                        @if( ! empty( $order->invoice_id ) )
                        <tr>
                            <th>@lang('orders::global.orders.fields.customer')</th>
                            <td field-key='customer'>{{ $order->customer->name ?? '' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>@lang('orders::global.orders.fields.price')</th>
                            <td field-key='price'>{{ digiCurrency($order->price,$order->currency_id) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('orders::global.orders.fields.billing-cycle')</th>
                            <td field-key='billing_cycle'>{{ $order->billingcycledisplay }}</td>
                        </tr>
                        
                        <tr>                            
                            <td colspan="2">
                                @include('orders::admin.orders.show-products', array('products_return' => $order))
                            </td>
                        </tr>
                        
                    </table>
                
                <?php
                $transactions = \Modules\Orders\Entities\OrdersPayments::where('order_id', '=', $order->id)->get();
                ?>
    
                <table class="table table-bordered table-striped {{ count($transactions) > 0 ? 'datatable' : '' }}">
                    <thead>
                        <tr>
                            <th>@lang('orders::global.orders.amount')</th>
                            <th>@lang('orders::global.orders.date')</th>
                            <th>@lang('orders::global.orders.status')</th>
                            <th>@lang('orders::global.orders.paymentmethod')</th>
                            @if(isAdmin() || isExecutive() )
                           
                            @endif
                        </tr>
                    </thead>

                    <tbody>                       
                        @if (count($transactions) > 0)
                        @foreach ($transactions as $row)
                            <tr data-entry-id="{{ $row->id }}">
                                <td field-key='amount'>{{ digiCurrency( $row->amount, $order->currency_id ) ?? '' }}</td>
                                <td field-key='date'>{{ digiDate( $row->date ) ?? '' }}</td>
                                <td field-key='payment_status'>{{ $row->payment_status }}</td>
                                <td field-key='paymentmethod'>{{ ucfirst( $row->paymentmethod ) }}</td>
                                @if(isAdmin() || isExecutive())
                               
                                @endif
                            </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="{{isAdmin() || isExecutive() ? 5 : 4}}">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                        @endif
                        
                    </tbody>
                </table>
      
              </div>  
            </div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.orders.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
    @include('admin.common.modal-loading-submit')
@stop

@section('javascript')
    @parent
    @include('admin.common.modal-scripts')
    <script type="text/javascript">
    $(document).ready(function() {
      $(window).keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
      });
    });
    </script>
@stop


