<div class="panel_s project-menu-panel">
    <div class="panel-body">
        <div class="horizontal-scrollable-tabs">

            
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs no-margin project-tabs nav-tabs-horizontal" role="tablist">
                    
                    <?php
                    $active = '';
                    if ( Route::current()->getName() == 'admin.client_projects.show' ) {
                        $active = ' active';
                    }
                    
                    ?>
                    <li class="project_tab_project_overview{{$active}}">
                        <a data-group="project_overview" role="tab" href="{{route('admin.client_projects.show', $client_project->id)}}">
                        <i class="fa fa-th" aria-hidden="true"></i> @lang('global.client-projects.details')</a>
                    </li>
                    
                    @if ( isClient() )
                        <?php
                        $tabs = $client_project->project_tabs->pluck('id')->toArray();
                        ?>
                        @if( in_array( PROJECT_TASKS_VIEW, $tabs ) )
                        <?php
                        $active = '';
                        if (\Request::is('admin/project_tasks/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_tasks{{$active}}">
                            <a data-group="project_tasks" role="tab" href="{{route('admin.project_tasks.index', $client_project->id)}}">
                        <i class="fa fa-check-circle" aria-hidden="true"></i> @lang('global.client-projects.tasks')</a>
                        </li>
                        @endif

                        @if( in_array( PROJECT_TIMESHEETS_VIEW, $tabs ) )
                        <?php
                        $active = '';
                        if (\Request::is('admin/time_entries/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_timesheets{{$active}}">
                            <a data-group="project_timesheets" role="tab" href="{{route('admin.time_entries.index', $client_project->id)}}">
                            <i class="fa fa-clock-o" aria-hidden="true"></i> @lang('global.client-projects.timesheets')</a>
                        </li>
                        @endif

                        @if( in_array( PROJECT_MILESTONES_VIEW, $tabs ) )
                        <?php
                        $active = '';
                        if (\Request::is('admin/mile_stones*/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_milestones{{$active}}">
                            <a href="{{route('admin.mile_stones.index', $client_project->id)}}">
                            <i class="fa fa-rocket" aria-hidden="true"></i> @lang('global.client-projects.milestones')</a>
                        </li>
                        @endif

                        @if( in_array( PROJECT_TASKS_ATTACHMENTS, $tabs ) )
                        <?php
                        $active = '';
                        if (\Request::is('admin/project/files/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_files{{$active}}">
                            <a data-group="project_files" role="tab" href="{{route('admin.project_files.upload', $client_project->id)}}">
                            <i class="fa fa-files-o" aria-hidden="true"></i> @lang('global.client-projects.files')</a>
                        </li>
                        @endif
                        
                        @if( in_array( PROJECT_OPEN_DISCUSSIONS, $tabs ) )
                        <?php
                        $active = '';
                        if (\Request::is('admin/project_discussions*/*')) {
                            $active = ' active';
                        }
                        ?>

                        <li class="project_tab_project_discussions{{$active}}">
                            <a data-group="project_discussions" role="tab" href="{{route('admin.project_discussions.index', $client_project->id)}}">
                            <i class="fa fa-commenting" aria-hidden="true"></i> @lang('global.client-projects.discussions')</a>
                        </li>
                        @endif
                        
                        @if( in_array( PROJECT_TICKETS_VIEW, $tabs ) )
                        <?php
                        $active = '';
                        if (\Request::is('admin/project_tickets/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_tickets{{$active}}">
                            <a data-group="project_tickets" role="tab" href="{{route('admin.project_tickets.index', $client_project->id)}}">
                            <i class="fa fa-life-ring" aria-hidden="true"></i> @lang('global.client-projects.tickets')</a>
                        </li>
                        @endif
                        
                        @if( in_array( PROJECT_SALES_VIEW, $tabs ) )
                        <?php
                        $active = '';
                        if (
                            \Request::is('admin/client_projects/invoice*/*') 
                            || \Request::is('admin/client_projects/expense*/*') 
                            || \Request::is('admin/expenses/*')
                            || \Request::is('admin/invoices/*')
                        ) {
                            $active = ' active';
                        }
                        ?>
                        <li class="{{$active}}">                        
                              <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-life-saver" aria-hidden="true"></i>&nbsp;{{trans('custom.menu.sales')}}&nbsp;<span class="caret"></span>
                              </a>
                              <ul class="dropdown-menu">                            
                                <li><a href="{{route('admin.client_projects.invoices', $client_project->id)}}">{{trans('global.invoices.title')}}</a></li>
                                <li><a href="{{route('admin.client_projects.invoice-project', $client_project->id)}}">{{trans('global.client-projects.invoice-project')}}</a></li>
                                
                                <li><a href="{{route('admin.client_projects.expenses', $client_project->id)}}">{{trans('global.client-projects.expenses')}}</a></li>                            
                              </ul>
                        </li>
                        @endif

                        @if( in_array( PROJECT_NOTES_VIEW, $tabs ) )
                        <?php
                        $active = '';
                        if (\Request::is('admin/project/notes/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_notes{{$active}}">
                            <a data-group="project_notes" role="tab" href="{{route('admin.project_files.note', $client_project->id)}}">
                            <i class="fa fa-file-o" aria-hidden="true"></i> @lang('global.client-projects.notes')</a>
                        </li>
                        @endif
                    @else
                        @can('project_task_access')
                        <?php
                        $active = '';
                        if (\Request::is('admin/project_tasks/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_tasks{{$active}}">
                            <a data-group="project_tasks" role="tab" href="{{route('admin.project_tasks.index', $client_project->id)}}">
                        <i class="fa fa-check-circle" aria-hidden="true"></i> @lang('global.client-projects.tasks')</a>
                        </li>
                        @endcan

                        @can('time_entry_access')
                        <?php
                        $active = '';
                        if (\Request::is('admin/time_entries/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_timesheets{{$active}}">
                            <a data-group="project_timesheets" role="tab" href="{{route('admin.time_entries.index', $client_project->id)}}">
                            <i class="fa fa-clock-o" aria-hidden="true"></i> @lang('global.client-projects.time-entries')</a>
                        </li>
                        @endcan

                        @can('mile_stone_access')
                        <?php
                        $active = '';
                        if (\Request::is('admin/mile_stones*/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_milestones{{$active}}">
                            <a href="{{route('admin.mile_stones.index', $client_project->id)}}">
                            <i class="fa fa-rocket" aria-hidden="true"></i> @lang('global.client-projects.milestones')</a>
                        </li>
                        @endcan

                        @can('project_files_access')
                        <?php
                        $active = '';
                        if (\Request::is('admin/project/files/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_files{{$active}}">
                            <a data-group="project_files" role="tab" href="{{route('admin.project_files.upload', $client_project->id)}}">
                            <i class="fa fa-files-o" aria-hidden="true"></i> @lang('global.client-projects.files')</a>
                        </li>
                        @endcan
                        
                        @can('project_discussion_access')
                        <?php
                        $active = '';
                        if (\Request::is('admin/project_discussions*/*')) {
                            $active = ' active';
                        }
                        ?>

                        <li class="project_tab_project_discussions{{$active}}">
                            <a data-group="project_discussions" role="tab" href="{{route('admin.project_discussions.index', $client_project->id)}}">
                            <i class="fa fa-commenting" aria-hidden="true"></i> @lang('global.client-projects.discussions')</a>
                        </li>
                        @endcan
                        
                        @can('project_ticket_access')
                        <?php
                        $active = '';
                        if (\Request::is('admin/project_tickets/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_tickets{{$active}}">
                            <a data-group="project_tickets" role="tab" href="{{route('admin.project_tickets.index', $client_project->id)}}">
                            <i class="fa fa-life-ring" aria-hidden="true"></i> @lang('global.client-projects.tickets')</a>
                        </li>
                        @endcan
                        
                        @can('project_sales_access')
                        <?php
                        $active = '';
                        if (
                            \Request::is('admin/client_projects/invoice*/*') 
                            || \Request::is('admin/client_projects/expense*/*') 
                            || \Request::is('admin/expenses/*')
                        ) {
                            $active = ' active';
                        }
                        ?>
                        <li class="salemenu{{$active}}">                                           
                              <a  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="salemenu">
                                <i class="fa fa-life-saver" aria-hidden="true"></i>&nbsp;{{trans('custom.menu.sales')}}&nbsp;<span class="caret"></span>
                              </a>
                              <ul class="dropdown-menu" aria-labelledby="salemenu">                            
                                <li><a href="{{route('admin.client_projects.invoices', $client_project->id)}}">{{trans('global.invoices.title')}}</a></li>
                                <li><a href="{{route('admin.client_projects.invoice-project', $client_project->id)}}">{{trans('global.client-projects.invoice-project')}}</a></li>
                                
                                <li><a href="{{route('admin.client_projects.expenses', $client_project->id)}}">{{trans('global.client-projects.expenses')}}</a></li>                            
                              </ul>
                        </li>
                        @endcan

                        @can('project_notes_access')
                        <?php
                        $active = '';
                        if (\Request::is('admin/project/notes/*')) {
                            $active = ' active';
                        }
                        ?>
                        <li class="project_tab_project_notes{{$active}}">
                            <a data-group="project_notes" role="tab" href="{{route('admin.project_files.note', $client_project->id)}}">
                            <i class="fa fa-file-o" aria-hidden="true"></i> @lang('global.client-projects.notes')</a>
                        </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </div>

    </div>
</div>