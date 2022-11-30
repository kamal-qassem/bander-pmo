<!-- summary body -->
<div class="panel-default" aria-hidden="false">
<div class="crm-invoice-summary">

<div class="row">
   <div class="col-md-12">
    <div class="row">
      <div class="col-md-12">
       <div class="col-md-4">
       <div style="border-top-left-radius: 10px;" class="crm-right-border-b1 crm-invoice-summaries-b1 crm-invoice-summaries-b5" onclick="summarypaymentstatus('all', 'all', 'circle', {{$currency_id}})">
           <div class="box-header text-uppercase text-bold">
               Total Credit Notes
           </div>
           <div class="box-content">
               <?php
               $total_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->count();
               ?>
               <div class="sentTotal">
                   <a href="javascript:void(0)">{{$total_invoices}}</a>
               </div>
           </div>
           <div class="box-foot">
               <div class="sendTime box-foot-left">
                   @lang('others.statistics.amount')
                   <br>
                   <?php
                   $total_amount_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->sum('amount');
                   ?>
                   <span class="box-foot-stats">
                       <strong>
                           {{digiCurrency( $total_amount_invoices, $currency_id )}}
                       </strong>
                   </span>

               </div>
           </div>
           <span id="paymentstatus_all_loader_circle"></span>
       </div>
      </div>
      <div class="col-md-4">
       <div class="crm-right-border-b1 crm-invoice-summaries-b1 crm-invoice-summaries-b5" onclick="summarypaymentstatus('paymentstatus', 'closedbottom', 'circle', {{$currency_id}})">
           <div class="box-header text-uppercase text-bold">
               @lang('others.statistics.closed-credit-notes')
           </div>
           <div class="box-content invoice-percent" data-target="100" >
               <?php
               $total_published_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->count();
               $total_published_paid_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->whereIn('paymentstatus', array( 'paid','Paid' ) )->where('credit_status', '=' , 'Closed')->count();
               if($total_published_invoices > 0){
               $percent = ($total_published_paid_invoices / $total_published_invoices ) * 100;
             }else{
                $percent = 0;
             }
               ?>
               <div class="easypiechart" id="easypiechart-teal" data-percent="{{$percent}}">
                   <span class="percent">{{number_format($percent,1)}}%</span>
               </div>
               

           </div>
           <div class="box-foot">
              
               <div class="box-foot-left">
                   @lang('others.statistics.amount')
                    <?php

                   $total_paid_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->whereIn('paymentstatus', array( 'paid','Paid' ) )->where('credit_status', '=' , 'Closed')->sum('amount');
                   ?>
                   <br>
                   <span class="box-foot-stats">
                       <strong>
                           {{digiCurrency( $total_paid_invoices, $currency_id )}}
                       </strong>
                   </span>
               </div>


                    <div class="box-foot-left pull-right">
                   @lang('others.statistics.closed-credit-notes')
                    <?php


                 $total_paid_invoices =\App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->whereIn('paymentstatus', array( 'paid','Paid' ) )->where('credit_status', '=' , 'Closed')->count();

                 ?>
                   <br>
                   <span class="box-foot-stats">
                       <strong>
                           {{ $total_paid_invoices .'/'. $total_invoices }}
                       </strong>
                   </span>
               </div>
               
           </div>
           <span id="paymentstatus_closedbottom_loader_circle"></span>
       </div>
      </div>

      <div class="col-md-4">
         <div class="crm-right-border-b1 crm-invoice-summaries-b1 crm-invoice-summaries-b5" onclick="summarypaymentstatus('paymentstatus', 'openbottom', 'circle', {{$currency_id}})">
           <div class="box-header text-uppercase text-bold" >
               @lang('others.statistics.open')
           </div>
           <div class="box-content invoice-percent-3" data-target="100" style="width: 130px; height: 130px;">

           	 <?php

               $total_published_unpaid_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->where('credit_status', '=' , 'Open')->count();

               if($total_published_invoices > 0){
               $percent = ($total_published_unpaid_invoices / $total_published_invoices ) * 100;
             }else{
                $percent = 0;
             }
              ?>	


               <div class="easypiechart" id="easypiechart-orange" data-percent="{{$percent}}"> 
                   <span class="percent">{{number_format($percent,1)}}%</span>
               </div>

               
               
           </div>
           <div class="box-foot">
              
               <div class="box-foot-left">
                   @lang('others.statistics.amount')
                   <?php

                   $total_unpaid_invoices_amount = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->whereIn('paymentstatus', array( 'unpaid','partial' ) )->where('credit_status', '=' , 'Open')->sum('amount');

                   ?>
                   <br>
                   <span class="box-foot-stats">
                       <strong>
                           {{digiCurrency( $total_unpaid_invoices_amount, $currency_id )}}
                       </strong>
                   </span>
               </div>
             
              
           </div>

           	      <div class="box-foot-left pull-right" style="margin-right:15px;">
                   @lang('others.statistics.open')
                    
                   <br>
                   <span class="box-foot-stats">
                       <strong>
                           {{ $total_published_unpaid_invoices .'/'. $total_invoices }}
                       </strong>
                   </span>
               </div>
               
                <span id="paymentstatus_openbottom_loader_circle"></span>

       </div>
     </div>
   </div>
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
