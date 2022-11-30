            
                <!-- summary body -->

		<div class="panel-default" aria-hidden="false">
		   <div class="crm-invoice-summary">
       
           <div class="row">
               <div class="col-md-12">
                   <div style="border-top-left-radius: 10px;" class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarystatus('status', '', 'circle')">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.total-orders')
                       </div>
                       <div class="box-content">
                           <?php
                           $total_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->count();
                           ?>
                           <div class="sentTotal">
                               {{$total_orders}}
                           </div>
                       </div>
                       <div class="box-foot">
                           <div class="sendTime box-foot-left">
                               @lang('others.statistics.amount')
                               <br>
                               <?php
	                           $total_amount_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->sum('price');
	                           ?>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_amount_orders, $currency_id )}}
                                   </strong>
                               </span>
                           </div>
                       </div>
                   </div>


                   <div class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarystatus('status', 'Active', 'circle')">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.active-orders')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_active_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Active')->count();
                           if($total_orders > 0){
                           $percent = ($total_active_orders / $total_orders ) * 100;
                       }else{
                       	$percent = 0;
                       }
                           ?>
                           <div class="easypiechart" id="easypiechart-teal" data-percent="{{number_format($percent,1)}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                                <?php

	                           $total_active_orders_amount = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Active')->sum('price');

	                           ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_active_orders_amount, $currency_id )}}
                                   </strong>
                               </span>
                           </div>

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.active')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_active_orders .'/'. $total_orders }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                       <span id="status_Active_loader_circle"></span>
                   </div>


                   <div class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarystatus('status', 'Pending', 'circle')">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.pending-orders')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_pending_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Pending')->count();
                           if($total_orders > 0){
                           $percent = ($total_pending_orders / $total_orders ) * 100;
                           }else{
                           	$percent = 0;
                           }
                           ?>
                           <div class="easypiechart" id="easypiechart-orange" data-percent="{{number_format($percent,1)}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                              <?php

                             $total_pending_orders_amount = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Pending')->sum('price');

                             ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_pending_orders_amount , $currency_id)}}
                                   </strong>
                               </span>
                           </div>

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.pending')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_pending_orders .'/'. $total_orders }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                       <span id="status_Pending_loader_circle"></span>
                   </div>



                   <div class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarystatus('status', 'Cancelled', 'circle')">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.cancelled-orders')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_cancelled_orders = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Cancelled')->count();
                           if($total_orders > 0){
                           $percent = ($total_cancelled_orders / $total_orders ) * 100;
                          }else{
                          	$percent = 0;
                          }
                           ?>
                           <div class="easypiechart" id="easypiechart-red" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                                <?php

                             $total_cancelled_orders_amount = Modules\Orders\Entities\Order::where('currency_id', '=', $currency_id)->where('status', '=', 'Cancelled')->sum('price');

                             ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_cancelled_orders_amount, $currency_id )}}
                                   </strong>
                               </span>
                           </div>

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.cancelled')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_cancelled_orders .'/'. $total_orders }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                       <span id="status_Cancelled_loader_circle"></span>
                   </div>

             
             
       </div>
       </div>
    </div>

</div>
                <!--  end summary body -->



@section('javascript') 
@parent
@include('admin.common.circle-summary-scripts')
@endsection