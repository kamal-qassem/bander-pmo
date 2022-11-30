<?php
$controller = getController( 'controller' );
?>
@can($gateKey.'delete')
    <?php
    $id = $row->id;
    if ( in_array($controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' )) ) {
        $route = [$routeKey.'.restore', $id ];
    } elseif ( in_array($controller, array( 'ProjectTasksController', 'TimeEntriesController', 'ProjectRemindersController', 'ProjectNotesController', 'MileStonesController', 'ProjectDiscussionController' )) ) {
        $route = [$routeKey.'.restore',$row->project_id, $id ];
    } else {
        $route = [$routeKey.'.restore', $id];
    }
    ?>
    
    {!! Form::open(array(
        'style' => 'display: inline-block;',
        'method' => 'POST',
        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
        'route' => $route)) !!}
    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
    {!! Form::close() !!}
@endcan
@can($gateKey.'delete')
    <?php
    $id = $row->id;
    if ( in_array($controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' )) ) {
        $route = [$routeKey.'.perma_del', $id ];
    } elseif ( in_array($controller, array( 'ProjectTasksController', 'TimeEntriesController', 'ProjectRemindersController', 'ProjectNotesController', 'MileStonesController', 'ProjectDiscussionController' )) ) {
        $route = [$routeKey.'.perma_del',$row->project_id, $id ];
    } else {
        $route = [$routeKey.'.perma_del', $id];
    }
    ?>
    {!! Form::open(array(
        'style' => 'display: inline-block;',
        'method' => 'DELETE',
        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
        'route' => $route)) !!}
    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
    {!! Form::close() !!}
@endcan