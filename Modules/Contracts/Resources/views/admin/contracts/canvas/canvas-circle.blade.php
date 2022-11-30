
                <!-- summary body -->

  <div class="panel-default" aria-hidden="false">
   <div class="crm-invoice-summary">
       
           <div class="row">
               <div class="col-md-12">
                   <div style="border-top-left-radius: 10px;" class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold" onclick="summarypaymentstatus('paymentstatus', '', 'circle')">
                          @lang('others.statistics.total-contracts')
                       </div>
                       <div class="box-content">
                           <?php
                           $total_contracts = Modules\Contracts\Entities\Contract::count();
                           ?>
                           <div class="sentTotal text-info" onclick="summarypaymentstatus('paymentstatus', '', 'circle')">
                               {{$total_contracts}}
                           </div>
                       </div>
                      
                   </div>


                    <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold" onclick="summarypaymentstatus('paymentstatus', 'accepted', 'circle')">
                           @lang('others.statistics.total-accepted')
                       </div>

                        <div class="box-content">
                           <?php
                           $total_accepted_contracts = Modules\Contracts\Entities\Contract::where('paymentstatus', '=', 'accepted')->count();
                           ?>
                           <div class="sentTotal text-success" onclick="summarypaymentstatus('paymentstatus', 'accepted', 'circle')">
                               {{$total_accepted_contracts}}
                           </div>
                           <span id="paymentstatus_accepted_loader_circle"></span>
                       </div>
                      
                   </div>


                   <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold" onclick="summarypaymentstatus('paymentstatus', 'delivered', 'circle')">
                           @lang('others.statistics.total-delivered')
                       </div>
                        <div class="box-content">
                           <?php
                           $total_delivered_contracts = Modules\Contracts\Entities\Contract::where('paymentstatus', '=', 'delivered')->count();
                           ?>
                           <div class="sentTotal text-warning" onclick="summarypaymentstatus('paymentstatus', 'delivered', 'circle')">
                               {{$total_delivered_contracts}}
                           </div>
                           <span id="paymentstatus_delivered_loader_circle"></span>
                       </div>
                   </div>



                     <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold" onclick="summarypaymentstatus('paymentstatus', 'rejected', 'circle')">
                           @lang('others.statistics.total-rejected')
                       </div>
                   <div class="box-content">
                           <?php
                           $total_rejected_contracts = Modules\Contracts\Entities\Contract::where('paymentstatus', '=', 'rejected')->count();
                           ?>
                           <div class="sentTotal text-danger" onclick="summarypaymentstatus('paymentstatus', 'rejected', 'circle')">
                               {{$total_rejected_contracts}}
                           </div>
                           <span id="paymentstatus_rejected_loader_circle"></span>
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