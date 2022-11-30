<!-- summary body -->
  
            <div id="stats-top" class="" style="display: block;">
                <div id="invoices_total">
                    <div class="row">
                        
                    <div class="col-lg-3 total-column">
                        <div class="panel_s">
                        <div class="panel-body-dr" onclick="summarypriority('priority', '', 'progress',{{$currency_id}})">
                            
                    <?php
                    if( isEmployee() ){
                   
                    $total_amount_client_projects = \App\Invoice::
                        join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')
                        ->join('client_projects', 'client_projects.id', '=', 'invoices.project_id')
                        ->join('client_project_user', 'client_projects.id', '=', 'client_project_user.client_project_id')
                        
                        ->where('invoices.currency_id', '=', $currency_id)
                        ->where('client_project_user.user_id', Auth::id())
                        ->whereNotNull('project_id')
                        ->where('payment_status', 'Success')
                        ->sum('invoice_payments.amount');
                        
                    }
                    else{
                     $total_amount_client_projects = \App\Invoice::join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')->where('invoices.currency_id', '=', $currency_id)->whereNotNull('project_id')->where('payment_status', 'Success')->sum('invoice_payments.amount');
                     } 
                     
                    ?>
                            <h3 class="text-muted _total">
                                {{ digiCurrency( $total_amount_client_projects, $currency_id )  }}            
                            </h3>
                            <span class="text-info">
                                @lang('others.statistics.total-client-projects')
                            </span>

                           <span id="priority_loader_progress"></span> 
                        </div>
                        </div>
                        </div>

                         <div class="col-lg-3 total-column">
                            <div class="panel_s">
                        <div class="panel-body-dr" onclick="summarypriority('priority', 'medium', 'progress',{{$currency_id}})">
                      <?php

                        if( isEmployee() ){


                    $total_amount_medium_client_projects = \App\Invoice::
                        join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')
                        ->join('client_projects', 'client_projects.id', '=', 'invoices.project_id')
                        ->join('client_project_user', 'client_projects.id', '=', 'client_project_user.client_project_id')
                        

                        ->whereNotNull('project_id')
                        ->where('priority','=','Medium')
                        ->where('invoices.currency_id', '=', $currency_id)
                        ->where('client_project_user.user_id', Auth::id())
                        ->where('payment_status', 'Success')
                        ->sum('invoice_payments.amount');


                        }else{

                      
                        $total_amount_medium_client_projects = \App\Invoice::join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')
                        ->join('client_projects', 'client_projects.id', '=', 'invoices.project_id')
                        ->where('client_projects.priority','=','Medium')
                        ->where('invoices.currency_id', '=', $currency_id)
                        ->whereNotNull('project_id')
                        ->where('payment_status', 'Success')
                          ->sum('invoice_payments.amount');

                        } 
                       ?>
                        <h3 class="text-muted _total">
                            {{digiCurrency( $total_amount_medium_client_projects, $currency_id )}}              
                        </h3>
                        <span class="text-success">
                            @lang('others.statistics.medium-priority-client-projects')
                        </span>
                        <span id="priority_medium_loader_progress"></span>
                    </div>
                </div>
            </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                        <div class="panel-body-dr"  onclick="summarypriority('priority', 'high', 'progress',{{$currency_id}})">

                            <?php
                            if( isEmployee() ){
                        $total_amount_high_priority_client_projects = \App\Invoice::
                        join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')
                        ->join('client_projects', 'client_projects.id', '=', 'invoices.project_id')
                        ->join('client_project_user', 'client_projects.id', '=', 'client_project_user.client_project_id')
                        
                        ->whereNotNull('project_id')
                        ->where('priority','=','High')
                        ->where('invoices.currency_id', '=', $currency_id)
                        ->where('client_project_user.user_id', Auth::id())
                        ->where('payment_status', 'Success')
                        ->sum('invoice_payments.amount');
                       }
                       else{

                        $total_amount_high_priority_client_projects = \App\Invoice::join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')
                        ->join('client_projects', 'client_projects.id', '=', 'invoices.project_id')
                        ->where('client_projects.priority','=','High')
                        ->where('invoices.currency_id', '=', $currency_id)
                        ->whereNotNull('project_id')
                        ->where('payment_status', 'Success')
                        ->sum('invoice_payments.amount');

                             } 
                              ?>

                            <h3 class="text-muted _total">
                             {{digiCurrency( $total_amount_high_priority_client_projects , $currency_id )}}           
                            </h3>
                                    <span class="text-warning">
                                        @lang('others.statistics.high-priority-client-projects')
                                    </span>

                                    <span id="priority_high_loader_progress"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr" onclick="summarypriority('priority', 'urgent', 'progress',{{$currency_id}})">
                          <?php
                        if( isEmployee() ){
                        $total_amount_urgent_client_projects = \App\Invoice::
                        join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')
                        ->join('client_projects', 'client_projects.id', '=', 'invoices.project_id')
                        ->join('client_project_user', 'client_projects.id', '=', 'client_project_user.client_project_id')
                        
                        ->whereNotNull('project_id')
                        ->where('priority','=','Urgent')
                        ->where('invoices.currency_id', '=', $currency_id)
                        ->where('client_project_user.user_id', Auth::id())
                        ->where('payment_status', 'Success')
                        ->sum('invoice_payments.amount');

                            }
                            else{
                            

                        $total_amount_urgent_client_projects = \App\Invoice::join('invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id')
                        ->join('client_projects', 'client_projects.id', '=', 'invoices.project_id')
                        ->where('client_projects.priority','=','Urgent')
                        ->where('invoices.currency_id', '=', $currency_id)
                        ->whereNotNull('project_id')
                        ->where('payment_status', 'Success')
                        ->sum('invoice_payments.amount');

                            }
                             ?>

                                    <h3 class="text-muted _total">
                                        {{digiCurrency( $total_amount_urgent_client_projects , $currency_id)}}              
                                    </h3>
                                    <span class="text-danger">
                                        @lang('others.statistics.urgent-priority-client-projects')
                                    </span>
                                    <span id="priority_urgent_loader_progress"></span>
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
                                <div class="col-md-9" style="margin-bottom:16px;">
                                   
                                        <h5 class="blue-text">
                                            @lang('others.statistics.total-client-projects')
                                        </h5>
                                    
                                </div>
                                    
                          <?php

                          
                            $emp = \App\ClientProject::whereHas("assigned_to",
                            function ($query) {
                            $query->where('id', Auth::id());
                            });
                            $emp_count = $emp->count();

                            if( isEmployee() ){ 
                             $total_client_projects = $emp_count;
                            }else{
                             $total_client_projects = App\ClientProject::where('currency_id', '=', $currency_id)->count();     
                            }
                           ?>
                                <div class="col-md-12 progress-12">
                                    <div class="col-md-7 text-right blue-text " style="font-size:25px;" onclick="summarypriority('priority', '', 'progress',{{$currency_id}})">
                                     {{$total_client_projects}}           
                                </div>
                                <span id="priority_loader_progress"></span>
                                </div>
                            </div>
                        </div>

                          <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                    
                                        <h5 class="blue-text">
                                            @lang('others.statistics.medium')
                                        </h5>
                                    
                                </div>

                        <?php
                        if( isEmployee() ){
                          $total_medium_priority_client_projects = App\ClientProject::whereHas("assigned_to",
                            function ($query) {
                            $query->where('id', Auth::id());
                            })->where('priority','=','Medium')->count();
                        }else{
                           $total_medium_priority_client_projects = App\ClientProject::where('currency_id', '=', $currency_id)->where('priority','=','Medium')->count();
                        }
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_medium_priority_client_projects .'/'. $total_client_projects }}            
                                </div>
                            <div class="col-md-12 progress-12">
                                <div class="col-md-7 text-right blue-text " style="font-size:25px;" onclick="summarypriority('priority', 'medium', 'progress',{{$currency_id}})">
                                     {{$total_medium_priority_client_projects}}           
                                </div>
                                <span id="priority_medium_loader_progress"></span>    
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                    
                                        <h5 class="blue-text">
                                            @lang('others.statistics.high')
                                        </h5>
                                    
                                </div>

                        <?php
                      if( isEmployee() ){
                           
                        $total_high_priority_client_projects = App\ClientProject:: whereHas("assigned_to",
                            function ($query) {
                            $query->where('id', Auth::id());
                            })->where('priority','=','High')->count();
                         }else{
                           $total_high_priority_client_projects = App\ClientProject::where('currency_id', '=', $currency_id)->where('priority','=','High')->count();
                            }
                           ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                  {{ $total_high_priority_client_projects .'/'. $total_client_projects }}            
                                </div>
                                <div class="col-md-12 progress-12">

                                <div class="col-md-7 text-right blue-text " style="font-size:25px;" onclick="summarypriority('priority', 'high', 'progress',{{$currency_id}})">
                                     {{$total_high_priority_client_projects}}           
                                </div>      
                                <span id="priority_high_loader_progress"></span> 
                                </div>
                            </div>
                        </div>
           
                        <div class="col-lg-5ths col-md-5ths">
                            <div class="row">
                                <div class="col-md-7">
                                   
                                        <h5 class="blue-text">
                                            @lang('others.statistics.urgent')
                                        </h5>
                                    
                                </div>

                       <?php
                        if( isEmployee() ){
                          $total_urgent_priority_client_projects = App\ClientProject::whereHas("assigned_to",
                            function ($query) {
                            $query->where('id', Auth::id());
                            })->where('priority','=','Urgent')->count();
                         }else{
                           $total_urgent_priority_client_projects = App\ClientProject::where('currency_id', '=', $currency_id)->where('priority','=','Urgent')->count();
                           }
                         ?>

                                <div class="col-md-5 text-right blue-text-rt">
                                    {{ $total_urgent_priority_client_projects .'/'. $total_client_projects }}            
                                </div>
                                <div class="col-md-12 progress-12">
                                    <div class="col-md-7 text-right blue-text " style="font-size:25px;" onclick="summarypriority('priority', 'urgent', 'progress',{{$currency_id}})">
                                     {{$total_urgent_priority_client_projects,$currency_id}}           
                                </div>
                                <span id="priority_urgent_loader_progress"></span> 
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