@extends('layouts.app')

@section('content')
    @include('contracts::admin.contracts.invoice.invoice-menu', ['invoice' => $contract])
    <h3 class="page-title">@lang('global.contract-tasks.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-4">
                <table class="table table-bordered table-striped">


                    <div class="task-single-related-wrapper">
    <h4 class="bold font-medium mbot15">
        @lang('global.contract-tasks.fields.name') :
        <a>
            {{ $contract_task->name }}
        </a>
    </h4>
</div>
<hr class="hr-10">
<div class="clearfix">
</div>
<h4 class="th font-medium mbot15 pull-left">
    @lang('global.contract-tasks.fields.description')
</h4>
<a  class="pull-left mtop10 mleft5 font-medium-xs editor-initiated">
    <i class="fa fa-pencil-square-o">
    </i>
</a>
<div class="clearfix">
</div>
<div class="no-margin tc-content task-no-description" id="task_view_description" style="position: relative;" spellcheck="false">
    <p>
        <span class="text-muted">
            {!! clean($contract_task->description) !!}
        </span>
    </p>
</div>
<div class="clearfix">
</div>


</div>

  
                    </table>
                </div>



                <!-- right design -->
<div class="row">
    <div class="col-md-5 task-single-col-right">
      
        <h4 class="task-info-heading">
            @lang('global.contract-tasks.fields.task-info')                     
        </h4>
       
        <h5 class="no-mtop task-info-created">
            <small class="text-dark">
                <strong>Created at :</strong> 
                <span class="text-dark">
                    <strong>{{ digiDate( $contract_task->created_at,true ) }}</strong>
                </span>
            </small>
        </h5>
        <hr class="task-info-separator">
        <div class="task-info task-status task-info-status">
            <h5>
                <i class="fa fa-star-half-o pull-left task-info-icon fa-fw fa-lg">
                </i>
                <strong>@lang('global.contract-tasks.fields.status'):</strong>
                <span class="task-single-menu task-menu-status">
                    <span class="trigger pointer manual-popover text-has-action" data-original-title="" title="">
                        <span class="" style="color:#03A9F4;">
                            {{ $contract_task->status->title }}
                        </span>
                    </span>
                    
                </span>
            </h5>
        </div>




        <div class="task-info task-info-start-date">
            <h5>
                <i class="fa task-info-icon fa-fw fa-lg fa-calendar-plus-o pull-left fa-margin">
                </i>
                <strong>@lang('global.contract-tasks.fields.startdate'):</strong>
                 <span class="bold">
                   {{ digiDate($contract_task->startdate) }}               
                  </span>
                </h5>
            </div>
            <div class="task-info task-info-duedate">
                <h5>
                    <i class="fa fa-calendar-check-o task-info-icon fa-fw fa-lg pull-left">
                    </i>
                     <?php
                     $duedate = digiTodayDateAdd(2);
                        if ( ! empty( $contract_task ) ) {
                        $duedate = ! empty( $contract_task->duedate ) ? digiDate( $contract_task->duedate ) : '';
                        }
                        ?>
                    <strong>@lang('global.contract-tasks.fields.duedate'):</strong>
                     <span class="bold">
                      {{ $duedate }}               
                      </span>
                    </h5>
                </div>
                 <div class="task-info task-info-datefinished">
                <h5>
                    <i class="fa fa-calendar task-info-icon fa-fw fa-lg pull-left">
                    </i>
            <?php
        $datefinished = digiTodayDateAdd(4);
        if ( ! empty( $contract_task ) ) {
            $datefinished = ! empty( $contract_task->datefinished ) ? digiDate( $contract_task->datefinished ) : '';
        }
        ?>
                    <strong>@lang('global.contract-tasks.fields.datefinished'):</strong>
                    <span class="bold">
                      {{ $datefinished }}               
                      </span>
                    </h5>
                </div>
                 <div class="task-info task-info-priority">
                    <h5>
                        <i class="fa task-info-icon fa-fw fa-lg pull-left fa-bolt">
                        </i>
                        <strong>@lang('global.contract-tasks.fields.priority') :</strong>
                        <span class="task-single-menu task-menu-priority">
                            <span class="trigger pointer manual-popover text-has-action" style="color:#03a9f4;" data-original-title="" title="">
                                {{ $contract_task->priority->title }}                  
                            </span>
                          
                        </span>
                    </h5>
                </div>

               

           @if( ! empty( $contract_task->recurring_value ) )     

             <div class="task-info task-info-recurring-value">
                <h5>
                    <i class="fa fa-repeat task-info-icon fa-fw fa-lg pull-left">
                    </i>
                    <strong>@lang('global.contract-tasks.fields.recurring-value'):</strong>
                  
                    <span class="bold">
                      {{ $contract_task->recurring_value }}               
                      </span>

                    </h5>
                </div>

   
                 <div class="task-info task-info-recurring-type">
                <h5>
                    <i class="fa fa-id-badge task-info-icon fa-fw fa-lg pull-left">
                    </i>
                    <strong>@lang('global.contract-tasks.fields.recurring-type'):</strong>
                    <span class="bold">
                      {{ $contract_task->recurring_type.'(s)' }}               
                      </span>

                    </h5>
                </div>
              

               
                 <div class="task-info task-info-cycles">
                <h5>
                    <i class="fa fa-history task-info-icon fa-fw fa-lg pull-left">
                    </i>
                    <strong>@lang('global.contract-tasks.fields.cycles'):</strong>
                    <span class="bold">
                      {{ $contract_task->cycles }}               
                      </span>
                    </h5>
                </div>
              @endif  

                <div class="task-info task-info-recurring-date">
                <h5>
                    <i class="fa fa-calendar-check-o task-info-icon fa-fw fa-lg pull-left">
                    </i>
                    <strong>@lang('global.contract-tasks.fields.last-recurring-date'):</strong>
                   
                    <span class="bold">
                      {{ $contract_task->last_recurring_date }}               
                      </span>
                    </h5>
                </div>
             




                <div class="task-info task-billable-amount">
                    <h5>
                        <i class="fa task-info-icon fa-fw fa-lg pull-left fa fa-file-text-o">
                        </i>
                       <strong> @lang('global.contract-tasks.fields.billable'):</strong>
                        <span class="bold">
                            {{ $contract_task->billable }}               
                        </span> 
                    </h5>
                </div>
                <div class="task-info task-info-billable">
                    <h5>
                        <i class="fa task-info-icon fa-fw fa-lg pull-left fa fa-credit-card">
                        </i>
                        <strong>@lang('global.contract-tasks.fields.billed'):</strong>
                        <span class="bold">
                            {{ $contract_task->billed }}               
                        </span>
                    </h5>
                </div>
                <div class="task-info task-status task-info-status">
                        <h5>
                        <i class="fa fa-file-text pull-left task-info-icon fa-fw fa-lg">
                        </i>
                        <strong>@lang('global.contract-tasks.fields.contract'):</strong>
                        <span class="task-single-menu task-menu-status">
                            <span class="trigger pointer manual-popover text-has-action" data-original-title="" title="">
                                <span class="" style="color:#03A9F4;">
                                    
                                    {{ $contract_task->invoice->invoice_no ?? '' }}
                                </span>
                            </span>
                        </span>
                    </h5>
                </div>
            
                <div class="task-info task-info-user-logged-time">
                    <h5>
                        <i class="fa fa-eye task-info-icon fa-fw fa-lg" aria-hidden="true">
                        </i>
                        <strong>@lang('global.contract-tasks.fields.visible-to-client'):</strong>               {{ $contract_task->visible_to_client }}            
                    </h5>
                </div>
                <div class="task-info task-info-user-logged-time">
                    <h5>
                        <i class="fa fa-hourglass-end task-info-icon fa-fw fa-lg" aria-hidden="true">
                        </i>
                        <strong>@lang('global.contract-tasks.fields.deadline-notified'):</strong>               
                        {{ $contract_task->deadline_notified }}            
                    </h5>
                </div>

                      <div class="task-info task-attachments task-info-attachments">
                        <h5>
                       
                       
                        <table>
                             <tr>
                            <th> <i class="fa fa-files-o pull-left task-info-icon fa-fw fa-lg">
                        </i>@lang('global.contract-tasks.fields.attachments'):</th>
                            <td field-key='attachments'> @foreach($contract_task->getMedia('attachments') as $media)
                               <p class="form-group">
                                    <a href="{{ route('admin.home.media-download', $media->id) }}">{{ $media->name }} ({{ $media->size }} KB)</a>
                                </p>
                            @endforeach</td>
                        </tr>
                    </table>
                    </h5>
                </div>


                <div class="task-info task-info-user-logged-time">
                    <h5>
                        <i class="fa fa-plus-square task-info-icon fa-fw fa-lg" aria-hidden="true">
                        </i>
                        <strong>@lang('global.contract-tasks.fields.created-by'):</strong>               
                        {{ $contract_task->created_by->name ?? '' }}            
                    </h5>
                </div>
            </div>
        </div>
        <!-- /right design -->

            <p>&nbsp;</p>

        </div>

            <a href="{{ route('admin.contract_tasks.index', $contract_task->contract_id) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
    </div>
@stop

@section('javascript')
    @parent
        @include('admin.common.standard-ckeditor')
    <script src="{{ url('adminlte/plugins/datetimepicker/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function(){
            moment.updateLocale('{{ App::getLocale() }}', {
                week: { dow: 1 } // Monday is the first day of the week
            });
            
            $('.date').datetimepicker({
                format: "{{ config('app.date_format_moment') }}",
                locale: "{{ App::getLocale() }}",
            });
            
        });
    </script>

    
            
@stop
