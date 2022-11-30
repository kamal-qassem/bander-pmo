<!-- summary body -->
<div id="stats-top" class="" style="display: block;">
    <div id="invoices_total">
        <div class="row">
        <div class="col-lg-3 total-column">
                <div class="panel_s">
                    <div class="panel-body-dr" onclick="summarypaymentstatus('all', 'all', 'progress', {{$currency_id}})">
                  <?php
                   $total_amount_invoices = \App\Invoice::where('currency_id', $currency_id);
                   
                   if ( ! empty( $project ) ) {
                    $total_amount_invoices->where('project_id', $project->id);
                   }
                     $total_amount_invoices = $total_amount_invoices->sum('amount');
                   ?>
                    <h3 class="text-muted _total">
                        {{ digiCurrency($total_amount_invoices, $currency_id) }}            
                    </h3>
                    
                    <a class="text-info" href="javascript:void(0);">
                            @lang('others.statistics.total-invoices-amount')
                    </a>

                    <span id="paymentstatus_all_loader_progress"></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 total-column">
                <div class="panel_s">
                    <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'paid', 'progress', {{$currency_id}})">
                  <?php
                   $total_paid_invoices = \App\Invoice::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->where('paymentstatus', '=', 'paid');
                   if ( ! empty( $project ) ) {
                    $total_paid_invoices->where('project_id', $project->id);
                   }
                   $total_paid_invoices = $total_paid_invoices->sum('amount');
                   ?>

                        <h3 class="text-muted _total">
                            {{digiCurrency( $total_paid_invoices, $currency_id )}}           
                        </h3>
                        <a class="text-success" href="javascript:void(0);">
                            @lang('others.statistics.paid')
                        </a>
                        <span id="paymentstatus_paid_loader_progress"></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 total-column">
                <div class="panel_s">
                    <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'unpaid', 'progress', {{$currency_id}})">
                 
                   <?php

                   

                   $total_unpaid_invoices= \App\Invoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date >= DATE(NOW())');
                    if ( ! empty( $project ) ) {
                    $total_unpaid_invoices->where('project_id', $project->id);
                   }
                   $total_unpaid_invoices = $total_unpaid_invoices->count();                     


                    $invoice_unpaid_amount = \App\Invoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date >= DATE(NOW())' );

                     if ( ! empty( $project ) ) {
                    $invoice_unpaid_amount->where('project_id', $project->id);
                   }
                   $invoice_unpaid_amount = $invoice_unpaid_amount->sum('amount'); 

                   ?>


                        <h3 class="text-muted _total">
                            {{digiCurrency( $invoice_unpaid_amount, $currency_id )}}              
                        </h3>
                        <span class="text-warning">
                            @lang('others.statistics.unpaid')
                        </span>
                        <span id="paymentstatus_unpaid_loader_progress"></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 total-column">
                <div class="panel_s">
                 <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'overdue', 'progress', {{$currency_id}})">
                      <?php
                $from = date('Y-m-d'.'00:00:00',time());
                $to   = date('Y-m-d'.'24:60:60',time()); 


                    $invoice_overdue = \App\Invoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date < DATE(NOW())');

                  if ( ! empty( $project ) ) {
                    $invoice_overdue->where('project_id', $project->id);
                   }
                   $invoice_overdue = $invoice_overdue->count(); 

                    $invoice_overdue_unpaid_amount = \App\Invoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date < DATE(NOW())' );

                  if ( ! empty( $project ) ) {
                    $invoice_overdue_unpaid_amount->where('project_id', $project->id);
                   }
                   $invoice_overdue_unpaid_amount = $invoice_overdue_unpaid_amount->sum('amount'); 



                
              ?>
              
                        <h3 class="text-muted _total">
                            {{digiCurrency( $invoice_overdue_unpaid_amount, $currency_id )}}            
                        </h3>
                        <span class="text-red">
                            @lang('others.statistics.overdue')
                        </span>
                        <span id="paymentstatus_overdue_loader_progress"></span>
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
                      
                            <h5 class="blue-text">
                                @lang('others.statistics.total-invoices')
                            </h5>
                        
                    </div>
                      
                <?php
                $total_invoices = \App\Invoice::where('currency_id', '=', $currency_id);

                 if ( ! empty( $project ) ) {
                    $total_invoices->where('project_id', $project->id);
                   }
                   $total_invoices = $total_invoices->count();

                ?>
                    <div class="col-md-12 progress-12">
                        <div class="col-md-7 text-right blue-text " style="font-size:25px;" onclick="summarypaymentstatus('all', 'allbottom', 'progress', {{$currency_id}})">
                         {{$total_invoices}}           
                    </div>
                    <span id="paymentstatus_allbottom_loader_progress"></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5ths col-md-5ths">
                <div class="row">
                    <div class="col-md-7">
                         
                            <h5 class="blue-text" onclick="summarypaymentstatus('paymentstatus', 'paidbottom', 'progress', {{$currency_id}})">
                                @lang('others.statistics.paid')
                            </h5>
                            <span id="paymentstatus_paidbottom_loader_progress"></span>
                        
                    </div>

            <?php
              $total_published_invoices = \App\Invoice::where('currency_id', '=', $currency_id)->where('status', '=', 'Published');

               if ( ! empty( $project ) ) {
                    $total_published_invoices->where('project_id', $project->id);
                   }
                   $total_published_invoices = $total_published_invoices->count();


               $total_published_paid_invoices = \App\Invoice::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->where('paymentstatus', '=', 'paid');

                if ( ! empty( $project ) ) {
                    $total_published_paid_invoices->where('project_id', $project->id);
                   }
                   $total_published_paid_invoices = $total_published_paid_invoices->count();


               if($total_published_invoices > 0){
               $percent = ($total_published_paid_invoices / $total_published_invoices ) * 100;
           }else{
            $percent = 0;
           }
             ?>

                    <div class="col-md-5 text-right blue-text-rt">
                        {{ $total_published_paid_invoices .'/'. $total_invoices }}            
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
                        
                            <h5 class="blue-text" onclick="summarypaymentstatus('paymentstatus', 'unpaidbottom', 'progress', {{$currency_id}})">
                                @lang('others.statistics.unpaid')
                            </h5>
                            <span id="paymentstatus_unpaidbottom_loader_progress"></span>
                        
                    </div>


                   

              <?php 

               $total_published_unpaid_invoices = \App\Invoice::where('currency_id', '=', $currency_id)->whereIn('paymentstatus', array( 'unpaid', 'Unpaid', 'due' ) )->where('status', '=', 'Published')->whereRaw('invoice_due_date >= DATE(NOW())');

                   if ( ! empty( $project ) ) {
                    $total_published_unpaid_invoices->where('project_id', $project->id);
                   }
                   $total_published_unpaid_invoices = $total_published_unpaid_invoices->count();

            if($total_published_invoices > 0){
               $percent = ($total_published_unpaid_invoices / $total_published_invoices ) * 100;
           }else{
            $percent = 0;
           }
             ?>

                    <div class="col-md-5 text-right blue-text-rt">
                        {{ $total_published_unpaid_invoices .'/'. $total_invoices }}            
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
                        
                            <h5 class="blue-text" onclick="summarypaymentstatus('paymentstatus', 'overduebottom', 'progress', {{$currency_id}})">
                                @lang('others.statistics.overdue')
                            </h5>
                            <span id="paymentstatus_overduebottom_loader_progress"></span>
                        
                    </div>

                    <div class="col-md-5 text-right blue-text-rt">
                        {{ $invoice_overdue .'/'. $total_invoices }}            
                    </div>

                    <div class="col-md-12 progress-12">
                        <div class="progress-list no-margin">
                          <?php
                            if($total_published_invoices > 0){
                            $percent = ($invoice_overdue / $total_published_invoices ) * 100;
                            }else{
                                $percent = 0;
                            }
                          ?>

                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%;" data-percent="{{number_format($percent,2)}}">
                                {{number_format($percent,2)}}%
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