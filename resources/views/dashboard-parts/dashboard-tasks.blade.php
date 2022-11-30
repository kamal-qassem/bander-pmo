<?php
$tasks = \App\Task::where('due_date', '>=', \Carbon\Carbon::now()->toDateString())->get()->sortBy('due_date');
?>
@if ( $tasks->count() > 0 )
<div class="col-md-{{$widget->columns}}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">@lang('custom.messages.upcoming-tasks')
                <ul class="rad-panel-action">
                    <li><i class="fa fa-rotate-right"></i></li>
                </ul>
            </h3>
        </div>


        <div class="panel-body">
            <div class="rad-activity-body">
                <div class="rad-list-group group">
                    @foreach( $tasks as $task )
                        <div class="rad-list-group-item">
                            <div class="rad-list-icon icon-shadow rad-bg-danger pull-left"><i class="fa fa-pencil"></i></div>
                            <div class="rad-list-content"><strong><a href="{{route('admin.tasks.show', $task->id)}}" target="_blank">{{$task->name}}</a></strong>
                                <div class="md-text">{{$task->description}}<br>{{digiDate($task->due_date)}}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif