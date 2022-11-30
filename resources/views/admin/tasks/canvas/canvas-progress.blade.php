 <!-- summary body -->

    <div class="panel_s mtop20">
        <div class="panel-body-dr">
            <div class="row text-left quick-top-stats">
                <div class="col-lg-5ths col-md-5ths">
                    <div class="row">
                        <div class="col-md-9">
                            
                           <h5 class="blue-text">
                              @lang('others.statistics.total-tasks')
                            </h5>
                            
                        </div>
                        	
                        <?php
                          $total_tasks = App\Task::count();
                         ?>
                        <div class="col-md-12 progress-12">
                            <div class="col-md-7 text-right blue-text " style="font-size:25px;" >
                             {{$total_tasks}}           
                        </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-5ths col-md-5ths">
                    <div class="row">
                        <div class="col-md-7">
                            
                                <h5 class="blue-text">
                                    @lang('others.statistics.open-tasks')
                                </h5>
                            
                        </div>


                 <?php
                   $total_open_tasks = App\Task::where('status_id', '=', 1)->count();

                   if($total_tasks > 0){
                   $percent = ($total_open_tasks / $total_tasks ) * 100;
                   }else{
                    $percent = 0;
                   }
                   ?>

                        <div class="col-md-5 text-right blue-text-rt">
                            {{ $total_open_tasks .'/'. $total_tasks }}            
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
    
                <div class="col-lg-5ths col-md-5ths">
                    <div class="row">
                        <div class="col-md-7">
                           
                                <h5 class="blue-text">
                                	@lang('others.statistics.total-inprogress')
                                </h5>
                            
                        </div>

                   <?php
                   $total_inprogress_tasks = App\Task::where('status_id', '=', 2)->count();
                   
                   if($total_tasks > 0 ){
                   $percent = ($total_inprogress_tasks / $total_tasks ) * 100;
                   }else{
                    $percent = 0;
                   } 
                   ?>

                        <div class="col-md-5 text-right blue-text-rt">
                              {{ $total_inprogress_tasks .'/'. $total_tasks }}            
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
                           
                                <h5 class="blue-text">                                           
                                    @lang('others.statistics.completed-tasks')
                                </h5>
                        </div>
               
                <?php
                   $total_completed_tasks = App\Task::where('status_id', '=', 4)->count();
                   
                   if($total_tasks > 0){
                   $percent = ($total_completed_tasks / $total_tasks ) * 100;
                   }else{
                    $percent = 0;
                   }
                   ?>

                        <div class="col-md-5 text-right blue-text-rt">
                            {{ $total_completed_tasks .'/'. $total_tasks }}           
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
            </div>
        </div>
    </div>

                <!--  end summary body -->
       


            <!-- end summary -->
    @section('javascript') 
    @parent

   @include('admin.common.progress-summary-scripts')

    @endsection
