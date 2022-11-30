  <!-- summary body -->

        <div class="panel-default" aria-hidden="false">
   <div class="crm-invoice-summary">
       
           <div class="row">
               <div class="col-md-12">
                   <div style="border-top-left-radius: 10px;" class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarypaymentstatus('all', 'all', 'circle', {{$currency_id}})">
                       <div class="box-header text-uppercase text-bold" >
                           @lang('others.statistics.total-recurring-invoices')
                       </div>
                       <div class="box-content">
                           <?php
                           $total_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->count();
                           ?>
                           <div class="sentTotal">
                               <a href="javascript:void(0)">{{$total_recurring_invoices}}</a>
                           </div>
                           
                       </div>
                       <div class="box-foot">
                           <div class="sendTime box-foot-left">
                               @lang('others.statistics.amount')
                               <br>
                               <?php
	                           $total_amount_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->sum('amount');
	                           ?>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_amount_recurring_invoices, $currency_id )}}
                                   </strong>
                               </span>
                           </div>
                       </div>
                   </div>

                   <div class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarypaymentstatus('paymentstatus', 'paid', 'circle', {{$currency_id}})">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.paid')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_published_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->count();

                           $total_published_paid_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->where('paymentstatus', '=', 'paid')->count();

                       if($total_published_recurring_invoices > 0){
                           $percent = ($total_published_paid_recurring_invoices / $total_published_recurring_invoices ) * 100;
                       }else {
                       	 $percent = 0;
                       }
                           ?>
                           <div class="easypiechart" id="easypiechart-teal" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>
                           <span id="paymentstatus_paid_loader_circle"></span>
                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                                <?php

	                           $total_paid_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->where('paymentstatus', '=', 'paid')->sum('amount');

	                           ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_paid_recurring_invoices, $currency_id )}}
                                   </strong>
                               </span>
                           </div>




                           <div class="box-foot-left pull-right" >
                               @lang('others.statistics.paid')
                                <?php
  

                             $total_paid_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->where('paymentstatus', '=', 'paid')->count();

                             ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_paid_recurring_invoices .'/'. $total_recurring_invoices }}
                                   </strong>
                               </span>
                           </div>

                       </div>
                       <span id="paymentstatus_paid_loader_circle"></span>
                   </div>



                     <div class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarypaymentstatus('paymentstatus', 'unpaid', 'circle', {{$currency_id}})">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.unpaid')
                       </div>
                       <div class="box-content invoice-percent-3" data-target="100" style="width: 130px; height: 130px;">

                       	<?php
                          $total_published_unpaid_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date >= DATE(NOW())')->count();


                          if($total_published_recurring_invoices > 0){
                           $percent = ($total_published_unpaid_recurring_invoices  / $total_published_recurring_invoices ) * 100;
                           }else {
                           	$percent = 0;
                           }
                         ?>	


                           <div class="easypiechart" id="easypiechart-orange" data-percent="{{$percent}}"> 
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                           <span id="paymentstatus_unpaid_loader_circle"></span>
                           
                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left" >
                               @lang('others.statistics.amount')
                           <?php

	                       $total_unpaid_recurring_invoices = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date >= DATE(NOW())' )->sum('amount');


	                      ?>
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{digiCurrency( $total_unpaid_recurring_invoices, $currency_id )}}
                                   </strong>
                               </span>
                           </div>


                      <div class="box-foot-left pull-right" >
                      @lang('others.statistics.unpaid')

                      <br>
                      <span class="box-foot-stats">
                      <strong>
                      {{ $total_published_unpaid_recurring_invoices .'/'. $total_published_recurring_invoices }}
                      </strong>
                      </span>
                      </div>  
                       </div>
                       <span id="paymentstatus_unpaid_loader_circle"></span>
                   </div>





                    <div class="crm-right-border-b1 crm-invoice-summaries-b1" onclick="summarypaymentstatus('paymentstatus', 'overdue', 'circle', {{$currency_id}})">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.overdue')
                       </div>
                       <div class="box-content invoice-percent-2" data-target="100" style="width: 130px; height: 130px;">
                       		<?php
                       			$from = date('Y-m-d'.'00:00:00',time());
                       			$to   = date('Y-m-d'.'24:60:60',time()); 

                       			$recurring_invoice_overdue = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date < DATE(NOW())')->count();

                       			$recurring_invoice_overdue_unpaid_amount = Modules\RecurringInvoices\Entities\RecurringInvoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date < DATE(NOW())' )->sum('amount');

                       			if($total_published_recurring_invoices > 0){	
                       			$percent = ($recurring_invoice_overdue / $total_published_recurring_invoices ) * 100;
                       		}else{
                       			$percent = 0;
                       		}
                       		?>
                           <div class="easypiechart" id="easypiechart-red" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>
                          	
                          	<span id="paymentstatus_overdue_loader_circle"></span>

                       </div>
                       <div class="box-foot">
                          
                           <div class="box-foot-left">
                               @lang('others.statistics.amount')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>{{digiCurrency( $recurring_invoice_overdue_unpaid_amount, $currency_id )}}</strong>
                               </span>
                           </div>
                           

                             <div class="box-foot-left pull-right" >
                               @lang('others.statistics.overdue')
                                
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $recurring_invoice_overdue .'/'. $total_recurring_invoices }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                       <span id="paymentstatus_overdue_loader_circle"></span>
                   </div>

             
             
       </div>
       </div>
    </div>

</div>
                <!--  end summary body -->


            <!-- end summary -->
@section('javascript') 
@parent

@include('admin.common.circle-summary-scripts')

@endsection
