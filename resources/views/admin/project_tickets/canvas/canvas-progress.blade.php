<!-- summary body -->
  
            <div id="stats-top" class="" style="display: block;">
                <div id="invoices_total">
                    <div class="row">
                    <div class="col-lg-4 total-column">
                        <div class="panel_s">
                        <div class="panel-body-dr">
                        <?php
                        	  $total_amount_client_projects = App\ClientProject::projectTickets( $client_project->id )->count();
                        ?>
                            <h3 class="text-muted _total">
                                {{ $total_amount_client_projects }}            
                            </h3>
                            <span class="text-info">
                                @lang('others.statistics.total-project-tickets')
                            </span>
                        </div>
                        </div>
                        </div>

                        <div class="col-lg-4 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr">

                    <?php
                       $open_tickets = App\ClientProject::projectTickets( $client_project->id, 'Open' )->count();
                      ?>

                                    <h3 class="text-muted _total">
                                        {{ $open_tickets }}           
                                    </h3>
                                    <span class="text-danger">
                                        @lang('others.statistics.active-tickets')
                                    </span>
                                    <span id="priority_high_loader_progress"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 total-column">
                            <div class="panel_s">
                                <div class="panel-body-dr">
                          <?php
	                           $closed_tickets = App\ClientProject::projectTickets( $client_project->id, 'Closed' )->count();
	                         ?>

                                    <h3 class="text-muted _total">
                                        {{$closed_tickets}}              
                                    </h3>
                                    <span class="text-success">
                                        @lang('others.statistics.completed-tickets')
                                    </span>
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
    <script>


    (function() {
    if(typeof(init_selectpicker) == 'function'){
    init_selectpicker();
    }
    })();
    </script>

@endsection