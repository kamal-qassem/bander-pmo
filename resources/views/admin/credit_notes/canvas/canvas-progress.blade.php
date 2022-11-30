<!-- summary body -->
<div id="stats-top" class="" style="display: block;">
    <div id="invoices_total">
        <div class="row">
        <div class="col-lg-4 total-column">
                <div class="panel_s">
                <div class="panel-body-dr" onclick="summarypaymentstatus('all', 'all', 'progress', {{$currency_id}})">
                  <?php
                   $total_amount_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->sum('amount');
                   ?>
                    <h3 class="text-muted _total">
                        {{ digiCurrency($total_amount_invoices, $currency_id) }}            
                    </h3>
                   
                    <a class="text-info" href="javascript:void(0);">
                            Total Credit Notes Amount
                    </a>

                    <span id="paymentstatus_all_loader_progress"></span>
                    </div>
                </div>
            </div>



            <div class="col-lg-4 total-column">
                <div class="panel_s">
                    <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'closedbottom', 'progress', {{$currency_id}})">


                  <?php

                   $total_paid_invoices =  \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->whereIn('paymentstatus', array( 'paid','Paid' ) )->where('credit_status', '=' , 'Closed')->sum('amount');

                   ?>

                        <h3 class="text-muted _total">
                             {{ digiCurrency( $total_paid_invoices, $currency_id ) }}             
                        </h3>
                        <a class="text-success" href="javascript:void(0);">
                            @lang('others.statistics.closed-credit-notes')
                        </a>
                        <span id="paymentstatus_closedbottom_loader_progress"></span>
                    </div>
                </div>
            </div>

        


              <div class="col-lg-4 total-column">
                <div class="panel_s">
                  <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'openbottom', 'progress', {{$currency_id}})">
                  <?php

                    $invoice_unpaid_amount =  \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->whereIn('paymentstatus', array( 'unpaid','partial' ) )->where('credit_status', '=' , 'Open')->sum('amount');

                   ?>
                        <h3 class="text-muted _total">
                            {{digiCurrency( $invoice_unpaid_amount, $currency_id )}}              
                        </h3>
                        <span class="text-warning">
                            @lang('others.statistics.open')
                        </span>
                        <span id="paymentstatus_openbottom_loader_progress"></span>
                    </div>
                </div>
            </div>

          

         
        </div>
        </div>
     </div>

<div class="panel_s mtop20">
    <div class="panel-body-dr">
        <div class="row text-left quick-top-stats">
            <div class="col-lg-6ths col-md-6ths">
                <div class="row">
                    <div class="col-md-9">
                      
                            <h5 class="blue-text">
                                Total Credit Notes
                            </h5>
                        
                    </div>
                    	
                            <?php
                            $total_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->count();
                            ?>
                    <div class="col-md-12 progress-12">
                        <div class="col-md-7 text-right blue-text " style="font-size:25px;" onclick="summarypaymentstatus('all', 'allbottom', 'progress', {{$currency_id}})">
                         {{$total_invoices}}           
                    </div>
                    <span id="status_allbottom_loader_progress"></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6ths col-md-6ths">
                <div class="row">
                    <div class="col-md-7">
                         
                            <h5 class="blue-text" onclick="summarypaymentstatus('paymentstatus', 'closedbottomcreditnote', 'progress', {{$currency_id}})">
                                @lang('others.statistics.closed-credit-notes')
                            </h5>
                            <span id="status_closedbottomcreditnote_loader_progress"></span>
                        
                    </div>

            <?php
              $total_published_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->count();
               $total_published_paid_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->whereIn('paymentstatus', array( 'paid','Paid' ) )->where('credit_status', '=' , 'Closed')->count();

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

            <div class="col-lg-6ths col-md-6ths">
                <div class="row">
                    <div class="col-md-7">
                        
            <h5 class="blue-text" onclick="summarycreditnotestatus('status', 'openbottom', 'progress', {{$currency_id}})">
                                @lang('others.statistics.open')
                            </h5>
                            <span id="status_openbottom_loader_progress"></span>
                        
                    </div>

              <?php	

               $total_published_unpaid_invoices = \App\CreditNote::where('currency_id', '=', $currency_id)->where('status', '=', 'Published')->where('credit_status', '=' , 'Open')->count();

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

        </div>
    </div>
</div>
<!--  end summary body -->

<!-- end summary -->
@section('javascript') 
@parent
  
  @include('admin.common.progress-summary-scripts')

@endsection