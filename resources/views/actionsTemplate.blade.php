<span>
<?php
$controller = getController( 'controller' );
$action = getController( 'action' );
?>
@can($gateKey.'view')
    @if ( ! in_array( $controller, array( 'DatabaseBackupsController' ) ) )
    <?php
    if ( in_array($controller, array( 'QuoteTasksController', 'QuotesRemindersController', 'QuotesNotesController' )) ) {
        ?>
    <a href="{{ route( $routeKey.'.show', [ 'quote_id' => $row->quote_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-primary">@lang('global.app_view')</a>
        <?php
    } elseif ( in_array($controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' )) ) {
        ?>
    <a href="{{ route( $routeKey.'.show', [ 'invoice_id' => $row->invoice_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-primary">@lang('global.app_view')</a>
        <?php
    } elseif ( in_array($controller, array( 'ProjectTasksController', 'TimeEntriesController', 'ProjectRemindersController', 'ProjectNotesController', 'MileStonesController', 'ProjectTicketsController', 'ProjectTicketsController', 'ProjectDiscussionController' )) ) {
        if ( ! in_array( $action, array('projectDiscussionComments') ) ) {
		?>
		<a href="{{ route( $routeKey.'.show', [ 'project_id' => $row->project_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-primary">@lang('global.app_view')</a>
        <?php
		}
    } elseif ( in_array($controller, array( 'ProposalTasksController', 'ProposalsRemindersController', 'ProposalsNotesController' )) ) {
        ?>
    <a href="{{ route( $routeKey.'.show', [ 'proposal_id' => $row->proposal_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-primary">@lang('global.app_view')</a>
        <?php
    }elseif ( in_array($controller, array( 'ContractTasksController', 'ContractsRemindersController', 'ContractsNotesController' )) ) {
        ?>
    <a href="{{ route( $routeKey.'.show', [ 'contract_id' => $row->contract_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-primary">@lang('global.app_view')</a>
        <?php
    }


    else {
    ?>
    <a href="{{ route( $routeKey.'.show', $row->id ) }}"
       class="btn btn-xs btn-primary">@lang('global.app_view')</a>
   <?php } ?>
    @endif
@endcan
@can($gateKey.'edit')
    <?php
    $noeditcontrollers = [ 'DatabaseBackupsController', 'PaymentGatewaysController' ];
    if ( ! isEnable('debug') ) {
        array_push($noeditcontrollers, 'ModulesManagementsController');
    }
    if ( 'TimeEntriesController' === $controller ) {
        if ( ! empty( $row->task->billed ) && 'yes' === $row->task->billed ) {
            array_push( $noeditcontrollers, 'TimeEntriesController' );
        }
    }
    ?>
    @if ( ! in_array( $controller, $noeditcontrollers ) )
    <?php
	$title = trans('global.app_edit');
	if ( 'SendSmsController' === $controller ) {
		$title = trans('sendsms::global.send-sms.re-send');
	}
	?>
    <?php
    if ( in_array($controller, array( 'QuoteTasksController', 'QuotesRemindersController', 'QuotesNotesController' )) ) {
    ?>
    <a href="{{ route($routeKey.'.edit', [ 'quote_id' => $row->quote_id, 'id' => $row->id ]) }}" class="btn btn-xs btn-info"><?php echo $title; ?></a>
    <?php } elseif ( in_array($controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' )) ) {
        ?>
        <a href="{{ route($routeKey.'.edit', [ 'invoice_id' => $row->invoice_id, 'id' => $row->id ]) }}" class="btn btn-xs btn-info"><?php echo $title; ?></a>
        <?php
    } elseif ( in_array($controller, array( 'ProjectTasksController', 'TimeEntriesController', 'ProjectRemindersController', 'ProjectNotesController', 'MileStonesController', 'ProjectTicketsController', 'ProjectDiscussionController' )) ) {
        if ( ! in_array( $action, array('projectDiscussionComments') ) ) {
		?>
    <a href="{{ route( $routeKey.'.edit', [ 'project_id' => $row->project_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-info"><?php echo $title; ?></a>
        <?php
		}
    } elseif ( in_array($controller, array( 'ProposalTasksController', 'ProposalsRemindersController', 'ProposalsNotesController' )) ) {
        ?>
    <a href="{{ route( $routeKey.'.edit', [ 'proposal_id' => $row->proposal_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-info">@lang('global.app_edit')</a>
        <?php
    }elseif ( in_array($controller, array( 'ContractTasksController', 'ContractsRemindersController', 'ContractsNotesController' )) ) {
        ?>
    <a href="{{ route( $routeKey.'.edit', [ 'contract_id' => $row->contract_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-info">@lang('global.app_edit')</a>
        <?php
    }
    elseif( 'ClientProjectsController' === $controller && 'invoices' === $action ) {
	?>
		<a href="{{ route( 'admin.client_projects.invoice-project-edit', [ 'project_id' => $row->project_id, 'id' => $row->id ] ) }}"
       class="btn btn-xs btn-primary"><?php echo $title; ?></a>
	<?php
	}else { ?>
	<a href="{{ route($routeKey.'.edit', $row->id) }}" class="btn btn-xs btn-info"><?php echo $title; ?></a>
    <?php } ?>
    @endif
@endcan
<?php
if ( 'LanguagesController' === $controller ) {
    $current_language = \Cookie::get('language');
    ?>
    @can($gateKey.'edit')
        @if( $current_language === $row->code )
        <p class="btn btn-xs btn-primary">@lang('global.app_default')</p>
        @else
        <a href="{{ url('admin/language/' . $row->code) }}" class="btn btn-xs btn-success">@lang('global.app_make_default')</a>
        @endif
    @endcan
    @can($gateKey.'edit')
        <?php
        $direction = trans('global.ltr');
        $title = trans('global.make-rtl');
        if ( 'Yes' === $row->is_rtl ) {
            $direction = trans('global.rtl');
            $title = trans('global.make-ltr');
        }
        ?>
        <a href="{{ route('admin.language.changedirection', $row->id) }}" class="btn btn-xs btn-success" title="{{$title}}">{{$direction}}</a>
    @endcan
    <?php
}
if ( in_array($controller, ['ContactsController'] ) ) {
    $contact_types = $row->contact_type->pluck('id')->toArray();
    ?>
    {{-- @can($gateKey.'edit')
        <a href="javascript:void(0);" class="btn btn-xs btn-success" onclick="sendMail('sendcontactemail', '{{$row->id}}', '{{$row->id}}')">@lang('global.app_send_email')</a>

    @endcan --}}
    @if ( ! in_array( LEADS_TYPE, $contact_types ) )
        @if( 'no' === $row->is_user && Gate::allows('user_create') )
        <a href="{{route('admin.users.create', $row->id)}}" class="btn btn-xs btn-warning">@lang('global.app_create_user')</a>
        @elseif ( Gate::allows('user_edit') )
        <a href="{{route('admin.users.edit', $row->id)}}" class="btn btn-xs btn-warning">@lang('global.app_edit_user')</a>
        @endif
    @endif
    <?php
}
if ( 'CurrenciesController' === $controller ) {
    ?>
    @can($gateKey.'edit')
        @if ( $row->is_default == 'yes')
			<a href="javascript:void(0);" class="btn btn-xs btn-default">@lang('global.app_make_default')</a>
		@else
			<a href="{{ route('admin.currency.makedefault', $row->id) }}" class="btn btn-xs btn-success">@lang('global.app_make_default')</a>
		@endif
    @endcan
    <?php
}
if ( 'TemplatesController' === $controller ) {
    ?>
    @can($gateKey.'edit')
        <a href="{{ route('admin.templates.duplicate', $row->id) }}" class="btn btn-xs btn-success">@lang('others.duplicate')</a>
    @endcan
    <?php
}
if ( 'ClientProjectsController' === $controller && 'index' === $action ) {
    ?>
    @can($gateKey.'edit')
        <a href="{{ route('admin.client_projects.duplicate', $row->id) }}" class="btn btn-xs btn-success" onclick="return confirm(window.are_you_sure_duplicate)">@lang('others.duplicate')</a>
    @endcan
    <?php
}
if ( 'UsersController' === $controller ) {
    ?>
    @can($gateKey.'change_status')
        @if ( Auth::id() != $row->id )
            @if ( 'Active' === $row->status )
                <a href="{{ route('admin.users.changestatus', $row->id) }}" class="btn btn-xs btn-success">@lang('global.users.suspend')</a>
            @else
                <a href="{{ route('admin.users.changestatus', $row->id) }}" class="btn btn-xs btn-success">@lang('global.users.activate')</a>
            @endif
        @endif
    @endcan
    <?php
}
if ( 'MasterSettingsController' === $controller ) {
    ?>
    @can($gateKey.'edit')
        <a href="{{ url('admin/mastersettings/settings/view', $row->slug) }}" class="btn btn-xs btn-info">@lang('global.app_settings')</a>
        @if( env('APP_DEV') )
        <a href="{{ url('admin/mastersettings/settings/add-sub-settings', $row->slug) }}" class="btn btn-xs btn-info">@lang('global.app_add_key')</a>
        @endif
    @endcan
    <?php
}
if ( 'SiteThemesController' === $controller ) {
    ?>
    @can($gateKey.'settings')
        @if( $row->is_active == 1 )
            <a href="{{ url('admin/make/default/theme', $row->id) }}" class="btn btn-xs btn-success">@lang('global.app_default')</a>
        @else
            <a href="{{ url('admin/make/default/theme', $row->id) }}" class="btn btn-xs btn-primary">@lang('global.app_make_default')</a>
        @endif
        @if( ! isDemo() )
        <a href="{{ url('admin/theme/settings', $row->slug) }}" class="btn btn-xs btn-info">@lang('global.app_settings')</a>
        <a href="{{ url('admin/theme/settings/add-sub-settings', $row->slug) }}" class="btn btn-xs btn-info">@lang('global.app_add_key')</a>
        @endif
    @endcan
    <?php
}
if ( 'ModulesManagementsController' === $controller ) {
    $label = trans( 'modulesmanagement::global.modules-management.active');
    $title = trans( 'modulesmanagement::global.modules-management.deactivate');
    $class = 'success';

        $label = trans( 'modulesmanagement::global.modules-management.active');

        $class = 'success';
        if ( 'No' === $row->enabled ) {
            $label = trans( 'modulesmanagement::global.modules-management.inactive');
            $title = trans( 'modulesmanagement::global.modules-management.activate');
            $class = 'warning';
        }
        ?>

        <a href="{{ route('admin.modules-management.changestatus', $row->id) }}" class="btn btn-xs btn-{{$class}} @if( 'no' === $row->can_inactive ) disabled @endif" title="{{$title}}" @if( 'no' === $row->can_inactive ) onclick="return false;" disabled @endif>{{$label}}</a>


        @if( ! isDemo() && 'Custom' === $row->type )
        <a href="{{ url('admin/plugin/settings', $row->slug) }}" class="btn btn-xs btn-info">@lang('global.app_settings')</a>
        <a href="{{ url('admin/plugin/settings/add-sub-settings', $row->slug) }}" class="btn btn-xs btn-info">@lang('global.app_add_key')</a>
        @endif
        <?php
    //}
}

if ( 'IncomesController' === $controller ) {
    ?>
    @can($gateKey.'receipt')
    <a href="{{ route('admin.incomes.receipt', $row->slug) }}" class="btn btn-xs btn-success">@lang('custom.incomes.receipt')</a>
    @endcan
    <?php
}
if ( 'OrdersController' === $controller ) {
        $class = 'info';
        if ( 'Pending' === $row->status ) {
            $class = 'warning';
        }
        if ( 'Accepted' === $row->status || 'Active' === $row->status ) {
            $class = 'success';
        }
        if ( 'Cancelled' === $row->status ) {
            $class = 'warning';
        }
    ?>
    @can('order_cancel')
        @if ( in_array($row->status, array( 'Pending', 'Cancelled', 'Returned' ) ) )


            <div class="btn-group ">
                <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-arrows-v" aria-hidden="true"></i>&nbsp;{{trans('custom.common.mark-as')}}&nbsp;<span class="caret"></span>
                </button>
                <div class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.orders.cancel', [ 'slug' => $row->slug, 'status' => 'Cancelled']) }}">{{trans('custom.orders.cancel')}}</a></li>

                    <li><a class="dropdown-item" href="{{ route('admin.orders.cancel', [ 'slug' => $row->slug, 'status' => 'Active'] ) }}">{{trans('custom.orders.active')}}</a></li>

                    <li><a class="dropdown-item" href="{{ route('admin.orders.cancel', [ 'slug' => $row->slug, 'status' => 'Pending'] ) }}">{{trans('custom.orders.pending')}}</a></li>

                    <li><a class="dropdown-item" href="{{ route('admin.orders.cancel', [ 'slug' => $row->slug, 'status' => 'Returned'] ) }}">{{trans('custom.orders.return')}}</a></li>

                </div>
            </div>

			<?php
			if ( isCustomer() ) {
			$payment = $row->payment( $row->id );

            $isExpired = false;
            if ( $payment ) {
			 $isExpired = \App\Http\Controllers\Admin\PaymentsController::isExpired( $payment );
            }

			if ( ! $isExpired ) {
			?>
			<a href="{{ route('admin.orders.payment-now', $row->slug) }}" class="btn btn-xs btn-info" onclick="return confirm('{{trans("orders::global.orders.app_are_you_sure")}}')">@lang('orders::global.orders.retry')</a>
			<?php }
			}
			?>

		@else
			<span class="label label-{{$class}} label-many">{{$row->status}}</span>
        @endif
    @endcan
	@if( isCustomer() )
    <a href="{{ route('admin.orders.reorder', $row->slug) }}" class="btn btn-xs btn-info" onclick="return confirm('{{trans("orders::global.orders.app_are_you_sure_reorder")}}')">@lang('orders::global.orders.reorder')</a>
    @endif
    <?php

}
?>
<?php
$candelete = true;
$nodeletecontrollers = array( 'SiteThemesController', 'PaymentGatewaysController' );
if( ! isEnable('debug') ) {
    array_push($nodeletecontrollers, 'MasterSettingsController', 'ModulesManagementsController');
}

if ( 'TimeEntriesController' === $controller ) {
    if ( ! empty( $row->task->billed ) && 'yes' === $row->task->billed ) {
        array_push( $nodeletecontrollers, 'TimeEntriesController' );
    }
}

if ( in_array($controller,  $nodeletecontrollers) ) {
    if ( 'default' === $row->slug ) {
        $candelete = false;
    }
    $candelete = false;
}

if ( in_array($controller, array( 'UsersController' ) ) ) {
    if ( Auth::id() == $row->id ) {
        $candelete = false;
    }
}
if( $candelete ) { ?>
@can($gateKey.'delete')
    <?php
    $id = $row->id;
	if ( in_array($controller, array( 'QuoteTasksController', 'QuotesRemindersController', 'QuotesNotesController' )) ) {
		$route = [$routeKey.'.destroy',$row->quote_id, $id ];
	} elseif ( in_array($controller, array( 'InvoiceTasksController', 'InvoiceRemindersController', 'InvoiceNotesController' )) ) {
        $route = [$routeKey.'.destroy',$row->invoice_id, $id ];
    } elseif ( in_array($controller, array( 'ProjectTasksController', 'TimeEntriesController', 'ProjectRemindersController', 'ProjectNotesController', 'MileStonesController', 'ProjectTicketsController', 'ProjectDiscussionController' )) ) {
        $route = [$routeKey.'.destroy',$row->project_id, $id ];
    } elseif ( in_array($controller, array( 'ProposalTasksController', 'ProposalsRemindersController', 'ProposalsNotesController' )) ) {

        $route = [$routeKey.'.destroy',$row->proposal_id, $id ];

    }elseif ( in_array($controller, array( 'ContractTasksController', 'ContractsRemindersController', 'ContractsNotesController' )) ) {

        $route = [$routeKey.'.destroy',$row->contract_id, $id ];

    }
    else {
		$route = [$routeKey.'.destroy', $id];
	}
    if ( 'DatabaseBackupsController' === $controller ) {
        $id = $row->file_name;
        $route = ['admin.databasebackups.delete', $id, $type ];
    }
    if ( 'ContactsController' === $controller ) {
        echo '<a href="'. route('admin.contacts.info', $row->id) .'" class="btn btn-xs btn-danger">'.trans('global.app_delete') . '</a>';
    } else {
    ?>
    {!! Form::open(array(
        'style' => 'display: inline-block;',
        'method' => 'DELETE',
        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
        'route' => $route)) !!}
    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
    {!! Form::close() !!}
    <?php } ?>
@endcan
<?php } ?>
</span>
