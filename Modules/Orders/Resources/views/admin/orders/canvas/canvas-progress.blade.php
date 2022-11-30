<!-- summary body -->
  
            <div id="stats-top" class="" style="display: block;">
                <div id="invoices_total">
                    <div class="row">
                    <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr">
                              <?php
	                           $total_amount_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->sum('price');
	                           ?>
                                    <h3 class="text-muted _total">
                                        {{ digiCurrency($total_amount_orders, $currency_id) }}            
                                    </h3>
                                    <span class="text-info">
                                        @lang('others.statistics.total-orders-amount')
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr"  onclick="summarystatus('status', 'Active', 'progress',{{$currency_id}})">

                             <?php

	                           $total_active_orders_amount = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Active')->sum('price');

	                           ?>

                                    <h3 class="text-muted _total">
                                        {{digiCurrency( $total_active_orders_amount, $currency_id )}}           
                                    </h3>
                                    <span class="text-success">
                                        @lang('others.statistics.active-orders')
                                    </span>

                                        <span id="status_Active_loader_progress"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr" onclick="summarystatus('status', 'Pending', 'progress',{{$currency_id}})">
                               <?php

                             $total_pending_orders_amount = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Pending')->sum('price');

                             ?>
                                    <h3 class="text-muted _total">
                                        {{digiCurrency( $total_pending_orders_amount , $currency_id)}}              
                                    </h3>
                                    <span class="text-warning">
                                        @lang('others.statistics.pending-orders')
                                    </span>
                                    <span id="status_Pending_loader_progress"></span>
                                </div>
                            </div>
                        </div>

                    <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr" onclick="summarystatus('status', 'Cancelled', 'progress',{{$currency_id}})">
                             <?php

                             $total_cancelled_orders_amount = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Cancelled')->sum('price');

                             ?>
                                    <h3 class="text-muted _total">
                                        {{digiCurrency( $total_cancelled_orders_amount, $currency_id )}}              
                                    </h3>
                                    <span class="text-danger">
                                        @lang('others.statistics.cancelled-orders')
                                    </span>
                                    <span id="status_Cancelled_loader_progress"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                 </div>

            <div class="panel_s mtop20">
                <div class="panel-body-dr">
                    <div class="row text-left quick-top-stats">
                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-9">
                                    
                                        <h5 class="blue-text" onclick="summarystatus('status', '', 'progress',{{$currency_id}})">
                                            @lang('others.statistics.total-orders')
                                        </h5>
                                    
                                </div>
                                	
     <?php
       $total_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->count();
       ?>
                                <div class="col-md-12 progress-12">
                                    <div class="col-md-7 text-right blue-text " style="font-size:25px;">
                                     {{$total_orders}}           
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                   
                                        <h5 class="blue-text" onclick="summarystatus('status', 'Active', 'progress',{{$currency_id}})">
                                            @lang('others.statistics.active')
                                        </h5>
                                   
                                </div>


                         <?php
                           $total_active_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Active')->count();
                           if($total_orders > 0){
                           $percent = ($total_active_orders / $total_orders ) * 100;
                           }else{
                            $percent = 0;
                           }
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_active_orders .'/'. $total_orders }}            
                                </div>
                                <div class="col-md-12 progress-12">

                                    <div class="progress-list no-margin">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%;" data-percent="{{number_format($percent,2)}}">
                                            {{number_format($percent,1)}}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                  
                                        <h5 class="blue-text" onclick="summarystatus('status', 'Pending', 'progress',{{$currency_id}})">
                                            @lang('others.statistics.pending')
                                        </h5>
                                    
                                </div>

                          <?php
                           $total_pending_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Pending')->count();
                           if($total_orders > 0){
                           $percent = ($total_pending_orders / $total_orders ) * 100;
                          }else{
                           $percent = 0; 
                          }
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_pending_orders .'/'. $total_orders }}            
                                </div>
                                <div class="col-md-12 progress-12">
                                    <div class="progress-list no-margin">


                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%;" data-percent="{{number_format($percent,2)}}">
                                            {{number_format($percent,1)}}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                      <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                 
                                        <h5 class="blue-text" onclick="summarystatus('status', 'Cancelled', 'progress',{{$currency_id}})">
                                            @lang('others.statistics.cancelled')
                                        </h5>
                                    
                                </div>

                          <?php
                           $total_cancelled_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Cancelled')->count();
                           
                           if($total_orders > 0){
                           $percent = ($total_cancelled_orders / $total_orders ) * 100;
                            }else{
                                $percent = 0;
                            }
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_cancelled_orders .'/'. $total_orders }}            
                                </div>
                                <div class="col-md-12 progress-12">
                                    <div class="progress-list no-margin">


                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%;" data-percent="{{number_format($percent,2)}}">
                                            {{number_format($percent,1)}}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


@section('javascript') 
@parent

@include('admin.common.progress-summary-scripts')

@endsection
                <!--  end summary body -->