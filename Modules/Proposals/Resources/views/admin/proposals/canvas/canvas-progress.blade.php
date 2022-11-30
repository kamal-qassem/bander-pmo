<!-- summary body -->
  
            <div id="stats-top" class="" style="display: block;">
                <div id="invoices_total">
                    <div class="row">
                    <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', '', 'progress')">
                              <?php
                             $total_proposals = Modules\Proposals\Entities\Proposal::count();
                             ?>
                                    <h3 class="text-muted _total">
                                        {{ $total_proposals }}            
                                    </h3>
                                    <span class="text-info">
                                        @lang('others.statistics.total-proposals')
                                    </span>

                                </div>
                            </div>
                        </div>

                         <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'accepted', 'progress')">
                              <?php

                             $total_accepted_proposals = Modules\Proposals\Entities\Proposal::where('paymentstatus', '=', 'accepted')->count();

                             ?>
                                    <h3 class="text-muted _total">
                                        {{$total_accepted_proposals }}              
                                    </h3>
                                    <span class="text-success" >
                                        @lang('others.statistics.total-accepted')
                                    </span>
                                    <span id="paymentstatus_accepted_loader_progress"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'delivered', 'progress')">

                              <?php

                             $total_delivered_proposals = Modules\Proposals\Entities\Proposal::where('paymentstatus', '=', 'delivered')->count();

                             ?>

                                    <h3 class="text-muted _total">
                                        {{ $total_delivered_proposals }}           
                                    </h3>
                                    <span class="text-warning" >
                                        @lang('others.statistics.total-delivered')
                                    </span>
                                <span id="paymentstatus_delivered_loader_progress"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr" onclick="summarypaymentstatus('paymentstatus', 'rejected', 'progress')">
                              <?php

                             $total_rejected_proposals = Modules\Proposals\Entities\Proposal::where('paymentstatus', '=', 'rejected')->count();

                             ?>
                                    <h3 class="text-muted _total">
                                        {{ $total_rejected_proposals }}              
                                    </h3>
                                    <span class="text-danger" >
                                        @lang('others.statistics.total-rejected')
                                    </span>
                                    <span id="paymentstatus_rejected_loader_progress"></span>
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
