            
                <!-- summary body -->

      <div class="panel-default" aria-hidden="false">
   <div class="crm-invoice-summary">
       
           <div class="row">
               <div class="col-md-12">
                   <div style="border-top-left-radius: 10px;" class="crm-right-border-b1 crm-invoice-summaries-b1" >
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.total-tasks')
                       </div>
                       <div class="box-content">
                           <?php
                           $total_tasks = App\Task::count();
                           ?>
                           <div class="sentTotal">
                               {{$total_tasks}}
                           </div>
                       </div>
                      <span id="paymentstatus_loader_circle"></span>
                   </div>


                   <div class="crm-right-border-b1 crm-invoice-summaries-b1" >
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.open-tasks')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_open_tasks = App\Task::where('status_id', '=', 1)->count();
                           if($total_tasks > 0){
                           $percent = ($total_open_tasks / $total_tasks ) * 100;
                           }else{
                           	$percent = 0;
                           }
                           ?>
                           <div class="easypiechart" id="easypiechart-red" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.open-tasks')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_open_tasks .'/'. $total_tasks }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                       <span id="paymentstatus_open_loader_circle"></span>
                   </div>


                   <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.in-progress')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_inprogress_tasks = App\Task::where('status_id', '=', 2)->count();
                           
                           if($total_tasks > 0){
                           $percent = ($total_inprogress_tasks / $total_tasks ) * 100;
                          }else{
                          	$percent = 0;
                          }
                           ?>
                           <div class="easypiechart" id="easypiechart-orange" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">

                           <div class="box-foot-left pull-right">
                               Total InProgress
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_inprogress_tasks .'/'. $total_tasks }}
                                   </strong>
                               </span>
                           </div>

                          
                       </div>
                       <span id="paymentstatus_inprogress_loader_circle"></span>
                   </div>



                   <div class="crm-right-border-b1 crm-invoice-summaries-b1">
                       <div class="box-header text-uppercase text-bold">
                           @lang('others.statistics.completed-tasks')
                       </div>
                       <div class="box-content invoice-percent" data-target="100">
                           <?php
                           $total_completed_tasks = App\Task::where('status_id', '=', 4)->count();
                           
                           if($total_tasks > 0){
                           $percent = ($total_completed_tasks / $total_tasks ) * 100;
                           }else{
                           	$percent = 0;
                           }
                           ?>
                           <div class="easypiechart" id="easypiechart-teal" data-percent="{{$percent}}">
                               <span class="percent">{{number_format($percent,1)}}%</span>
                           </div>

                       </div>
                       <div class="box-foot">
                          

                           <div class="box-foot-left pull-right">
                               @lang('others.statistics.completed-tasks')
                               <br>
                               <span class="box-foot-stats">
                                   <strong>
                                       {{ $total_completed_tasks .'/'. $total_tasks }}
                                   </strong>
                               </span>
                           </div>
                       </div>
                       <span id="paymentstatus_completed_loader_circle"></span>
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