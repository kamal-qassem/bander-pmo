@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <?php
    $parts = getController();
    $controller = $parts['controller'];
    $action = $parts['action'];
    ?>
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="display: block;  overflow: auto;">
        <a href="{{ url('/admin/dashboard') }}" class="logo" width="100px" height="150px"
             style="font-size: 16px;">
         <!-- mini logo for sidebar mini 50x50 pixels -->
         <?php
         $site_title = getSetting('site_title','site_settings', 'LaraOffice');
         $site_logo = getSetting('site_logo','site_settings');
         $destinationPath      = getSettingsPath();
         ?>
         @if ( ! empty( $site_logo ) && file_exists($destinationPath.$site_logo))
            <img src="{{IMAGE_PATH_SETTINGS.$site_logo}}" class="logo-main" alt="{{$site_title}}" title="{{$site_title}}">
         {{-- @else
           <img src="{{asset('images/logo3.png')}}" class="logo-main" alt="{{$site_title}}" title="{{$site_title}}"> --}}
         @endif
         <!-- logo for regular state and mobile devices -->


         {{-- <span class="logo-mini">{{$site_title}}</span>
         <!-- logo for regular state and mobile devices -->
         <span class="logo-lg">{{$site_title}}</span> --}}
     </a>
        <ul class="sidebar-menu">

			@if ( isAdmin() )
            <li>
                <select class="searchable-field form-control"></select>
            </li>
            @endif

            <?php
            $use_dynamic_menu = getSetting('use-dynamic-menu', 'site_settings', 'No');
            if ( ! isAdmin() && 'Yes' === $use_dynamic_menu ) {
                $role = auth()->user()->role->first()->slug;

                if ( ! empty( $role ) ) {
                    $public_menu = \Harimayco\Menu\Models\Menus::byName( $role );
                    $theme = \Cookie::get('theme');
                    if ( empty( $theme ) ) {
                        $theme = 'default';
                    }

                    if ( $public_menu ) {
                        $public_menu = Menu::getByName( $role );
                        $parent_start = false;
                        foreach ($public_menu as $menu) {
                            if ( ! empty($menu['theme']) && $theme != $menu['theme'] ) {
                                continue;
                            }

                            $query_string = str_replace($menu['link'], '',$request->fullUrl());

                            if ( 'heading' === $menu['link'] ) {
                                echo '<li class="header">'.$menu['label'].'</li>';
                                $parent_start = false;
                            } elseif ( ! empty( $menu['child'] ) ) {

                                ?>
                                <li class="treeview">
                                    <a href="#">
                                        @if ( ! empty( $menu['icon_html'] ) )
                                            {!! $menu['icon_html'] !!}
                                        @endif
                                        <span>{{$menu['label']}}</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>

                                    @foreach( $menu['child'] as $child )
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ url( $child['link'] ) }}">
                                                @if ( ! empty( $child['icon_html'] ) )
                                                    {!! $child['icon_html'] !!}
                                                @endif
                                                <span>{{$child['label']}}</span>
                                            </a>
                                        </li>
                                    </ul>
                                    @endforeach
                                <?php
                            } else {
                                ?>
                                <li class="{{ ( (strpos($query_string, $menu['link'] ) ) !== false ) ? 'active' : '' }}">
                                    <a href="{{ url( $menu['link'] ) }}">
                                        <i class="fa fa-anchor"></i>
                                        <span>{{$child['label']}}</span>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                        }
                    }
                }
            }
            ?>
            @if( isAdmin() || 'No' === $use_dynamic_menu )

            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="fa fa-wrench"></i>
                    <span class="title">@lang('global.app_dashboard')</span>
                </a>
            </li>

            {{-- @if( isPluginActive( ['invoice', 'credit_note', 'quotes'] ) )
                @can('sale_access')
                <li class="header">@lang('custom.menu.sales')</li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-life-saver"></i>
                        <span>@lang('global.sales.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if( isPluginActive('invoice') )
                            @can('invoice_access')
                            <li>
                                <a href="{{ route('admin.invoices.index') }}">
                                    <i class="fa fa-credit-card"></i>
                                    <span>@lang('global.invoices.title')</span>
                                </a>
                            </li>
                            @endcan
                            @can('invoice_create')
                            <li>
                                <a href="{{ route('admin.invoices.create') }}">
                                    <i class="fa fa-plus"></i>
                                    <span>@lang('custom.menu.create-invoice')</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                        @if( isPluginActive('credit_note') )
                            @can('credit_note_access')
                            <li>
                                <a href="{{ route('admin.credit_notes.index') }}">
                                    <i class="fa fa-file"></i>
                                    <span>@lang('global.credit_notes.title')</span>
                                </a>
                            </li>
                            @endcan

                            @can('credit_note_create')
                            <li>
                                <a href="{{ route('admin.credit_notes.create') }}">
                                    <i class="fa fa-plus"></i>
                                    <span>New credit note</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                        @if( File::exists(config('modules.paths.modules') . '/Quotes') && Module::find('quotes')->active && isPluginActive('quotes'))
                            @can('quote_access')
                            <li>
                                <a href="{{ route('admin.quotes.index') }}">
                                    <i class="fa fa-question-circle"></i>
                                    <span>@lang('global.quotes.title')</span>
                                </a>
                            </li>
                            @endcan

                            @can('quote_create')
                            <li>
                                <a href="{{ route('admin.quotes.create') }}">
                                    <i class="fa fa-plus"></i>
                                    <span>@lang('custom.menu.create-quote')</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                        @if( File::exists(config('modules.paths.modules') . '/Proposals') && Module::find('proposals')->active && isPluginActive('proposals'))
                            @can('proposal_access')
                            <li>
                                <a href="{{ route('admin.proposals.index') }}">
                                    <i class="fa fa-sticky-note-o"></i>
                                    <span>@lang('proposals::custom.proposals.title')</span>
                                </a>
                            </li>
                            @endcan

                            @can('proposal_create')
                            <li>
                                <a href="{{ route('admin.proposals.create') }}">
                                    <i class="fa fa-plus"></i>
                                    <span>@lang('custom.menu.create-proposal')</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                        @if( File::exists(config('modules.paths.modules') . '/Contracts') && Module::find('contracts')->active && isPluginActive('contracts'))
                            @can('contract_access')
                            <li>
                                <a href="{{ route('admin.contracts.index') }}">
                                    <i class="fa fa-paper-plane"></i>
                                    <span>@lang('contracts::global.contracts.title')</span>
                                </a>
                            </li>
                            @endcan

                            @can('contract_create')
                            <li>
                                <a href="{{ route('admin.contracts.create') }}">
                                    <i class="fa fa-plus"></i>
                                    <span>@lang('custom.menu.create-contract')</span>
                                </a>
                            </li>
                            @endcan

                        @endif


                    </ul>
                </li>
                @endcan
            @endif --}}

            {{-- @if( File::exists(config('modules.paths.modules') . '/RecurringInvoices') && Module::find('recurringinvoices')->active && isPluginActive('recurringinvoices'))
                @can('recurring_invoice_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-recycle"></i>
                        <span>@lang('global.recurring-invoices.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">

                        @can('recurring_invoice_access')
                        <li>
                            <a href="{{ route('admin.recurring_invoices.index') }}">
                                <i class="fa fa-recycle"></i>
                                <span>@lang('global.recurring-invoices.title')</span>
                            </a>
                        </li>@endcan

                        @can('recurring_period_access')
                        <li>
                            <a href="{{ route('admin.recurring_periods.index') }}">
                                <i class="fa fa-recycle"></i>
                                <span>@lang('global.recurring-periods.title')</span>
                            </a>
                        </li>@endcan

                    </ul>
                </li>
                @endcan
            @endif --}}


            {{-- @if( isPluginActive('product') )
                @can('product_management_access')
                <li class="header">@lang('custom.menu.stock')</li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-shopping-cart"></i>
                        <span>@lang('global.product-management.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('product_access')
                        <li>
                            <a href="{{ route('admin.products.index') }}">
                                <i class="fa fa-shopping-cart"></i>
                                <span>@lang('global.products.title')</span>
                            </a>
                        </li>@endcan
                        @if( isPluginActive('productcategory') )
                        @can('product_category_access')
                        <li>
                            <a href="{{ url('admin/product_categories') }}">
                                <i class="fa fa-folder"></i>
                                <span>@lang('global.product-categories.title')</span>
                            </a>
                        </li>@endcan
                        @endif


                        @can('products_transfer_access')
                        <li>
                            <a href="{{ route('admin.products_transfers.index') }}">
                                <i class="fa fa-transgender-alt"></i>
                                <span>@lang('global.products-transfer.title')</span>
                            </a>
                        </li>@endcan

                        @if( isPluginActive('productbrand') )
                        @can('brand_access')
                        <li>
                            <a href="{{ route('admin.brands.index') }}">
                                <i class="fa fa-adn"></i>
                                <span>@lang('global.brands.title')</span>
                            </a>
                        </li>@endcan
                        @endif
                        @if( isPluginActive('productmeasurementunits') )
                        @can('measurement_unit_access')
                        <li>
                            <a href="{{ route('admin.measurement_units.index') }}">
                                <i class="fa fa-dot-circle-o"></i>
                                <span>@lang('global.measurement-units.title')</span>
                            </a>
                        </li>@endcan
                        @endif
                        @if( isPluginActive('productwarehouse') )
                           @can('warehouse_access')
                        <li>
                            <a href="{{ route('admin.warehouses.index') }}">
                                <i class="fa fa-life-bouy"></i>
                                <span>@lang('global.warehouses.title')</span>
                            </a>
                        </li>@endcan
                        @endif

                    </ul>
                </li>
                @endcan
            @endif --}}

            {{-- @if( isPluginActive('purchase_order') )
                @can('purchase_order_access')
                <li>
                    <a href="{{ route('admin.purchase_orders.index') }}">
                        <i class="fa fa-anchor"></i>
                        <span>@lang('global.purchase-orders.title')</span>
                    </a>
                </li>
                @endcan
            @endif --}}

            @if( isPluginActive('client_project') )
            @can('project_access')
            {{-- <li class="header">@lang('custom.menu.project')</li> --}}
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cubes"></i>
                    <span>@lang('global.projects.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('client_project_access')
                    <li>
                        <a href="{{ route('admin.client_projects.index') }}">
                            <i class="fa fa-briefcase"></i>
                            <span>@lang('global.client-projects.title')</span>
                        </a>
                    </li>
                    @endcan
                    @can('project_status_access')
                    <li>
                        <a href="{{ route('admin.project_statuses.index') }}">
                            <i class="fa fa-flask"></i>
                            <span>@lang('global.project-statuses.title')</span>
                        </a>
                    </li>
                    @endcan
                    @if( isEnable('debug') )
                    @can('project_billing_type_access')
                    <li>
                        <a href="{{ route('admin.project_billing_types.index') }}">
                            <i class="fa fa-dollar"></i>
                            <span>@lang('global.project-billing-types.title')</span>
                        </a>
                    </li>
                    @endcan

                    @can('project_tab_access')
                    <li>
                        <a href="{{ route('admin.project_tabs.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('global.project-tabs.title')</span>
                        </a>
                    </li>
                    @endcan
                    @endif
                </ul>
            </li>
            @endcan
        @endif
        @if( isPluginActive('task') )
        @can('task_management_access')
        <li class="treeview">
            <a href="#">
                <i class="fa fa-list"></i>
                <span>@lang('global.task-management.title')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                @can('task_access')
                <li>
                    <a href="{{ route('admin.tasks.index') }}">
                        <i class="fa fa-briefcase"></i>
                        <span>@lang('global.tasks.title')</span>
                    </a>
                </li>
                @endcan

                @can('task_status_access')
                <li>
                    <a href="{{ route('admin.task_statuses.index') }}">
                        <i class="fa fa-server"></i>
                        <span>@lang('global.task-statuses.title')</span>
                    </a>
                </li>
                @endcan

                @can('task_calendar_access')
                <li>
                    <a href="{{ route('admin.task_calendars.index') }}">
                        <i class="fa fa-calendar"></i>
                        <span>@lang('global.task-calendar.title')</span>
                    </a>
                </li>
                @endcan

                @can('task_calendar_access')
                <li>
                    <a href="{{ route('admin.calendartasks.calendar.taskstatus') }}">
                        <i class="fa fa-server"></i>
                        <span>@lang('global.task-calendar.status-wise')</span>
                    </a>
                </li>
                @endcan

            </ul>
        </li>
        @endcan
    @endif

        @if( Gate::allows('contact_access')
        && Gate::allows('contact_create')
        && Gate::allows('contact_company_access')
        && Gate::allows('country_access')
        && Gate::allows('contact_group_access')
        && Gate::allows('contact_type_access')
        && Gate::allows('contact_note_access')
        && Gate::allows('contact_document_access')
        // && Gate::allows('contact_mailchimp_email_campaigns')
        )
        @can('contact_access')
            {{-- <li class="header">@lang('custom.menu.crm')</li> --}}
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-phone-square"></i>
                    <span>@lang('global.contact-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('contact_access')
                    <li>
                        <a href="{{ route('admin.contacts.index') }}">
                            <i class="fa fa-user-plus"></i>
                            <span>@lang('global.contacts.title')</span>
                        </a>
                    </li>
                     @endcan
                    @can('contact_create ')
                    <li>
                        <a href="{{ route('admin.contacts.create') }}">
                            <i class="fa fa-plus"></i>
                            <span>@lang('custom.menu.create-contact')</span>
                        </a>
                    </li>
                    @endcan
                    @can('contact_company_access')
                    <li>
                        <a href="{{ route('admin.contact_companies.index') }}">
                            <i class="fa fa-building-o"></i>
                            <span>@lang('global.contact-companies.title')</span>
                        </a>
                    </li>@endcan
					@can('country_access')
                    <li>
                        <a href="{{ route('admin.countries.index') }}">
                            <i class="fa fa-globe"></i>
                            <span>@lang('global.countries.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_group_access')
                    <li>
                        <a href="{{ route('admin.contact_groups.index') }}">
                            <i class="fa fa-connectdevelop"></i>
                            <span>@lang('global.contact-groups.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_type_access')
                    <li>
                        <a href="{{ route('admin.contact_types.index') }}">
                            <i class="fa fa-align-justify"></i>
                            <span>@lang('global.contact-types.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_note_access')
                    <li>
                        <a href="{{ route('admin.contact_notes.index') }}">
                            <i class="fa fa-sticky-note-o"></i>
                            <span>@lang('global.contact-notes.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_document_access')
                    <li>
                        <a href="{{ route('admin.contact_documents.index') }}">
                            <i class="fa fa-files-o"></i>
                            <span>@lang('global.contact-documents.title')</span>
                        </a>
                    </li>@endcan
                    {{-- @can('contact_mailchimp_email_campaigns')
                    <li>
                        <a href="{{ route('admin.contacts.mailchimp-email-campaigns') }}">
                            <i class="fa fa-files-o"></i>
                            <span>@lang('global.contacts.mailchimp-email-campaigns')</span>
                        </a>
                    </li>@endcan --}}
                </ul>
            </li>
            @endif
          @endif



           @if( isPluginActive('user') )
            @can('user_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>@lang('global.user-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                     @can('user_access')
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-user"></i>
                            <span>@lang('global.users.title')</span>
                        </a>
                    </li>@endcan

                    @if( isEnable('debug') )
	                   @can('permission_access')
	                    <li>
	                        <a href="{{ url('admin/permissions') }}">
	                            <i class="fa fa-briefcase"></i>
	                            <span>@lang('global.permissions.title')</span>
	                        </a>
	                    </li>
	                    @endcan
                    @endif

                    @can('role_access')
                    <li>
                        <a href="{{ url('admin/roles') }}">
                            <i class="fa fa-briefcase"></i>
                            <span>@lang('global.roles.title')</span>
                        </a>
                    </li>@endcan



                    @can('user_action_access')
                    <li>
                        <a href="{{ route('admin.user_actions.index') }}">
                            <i class="fa fa-th-list"></i>
                            <span>@lang('global.user-actions.title')</span>
                        </a>
                    </li>@endcan

                    @can('department_access')
                    <li>
                        <a href="{{ route('admin.departments.index') }}">
                            <i class="fa fa-codepen"></i>
                            <span>@lang('global.departments.title')</span>
                        </a>
                    </li>@endcan

                </ul>
            </li>
            @endcan
            @endif
            {{-- @if( isPluginActive('lead') )
                @can('contact_access')
                <li>
                    <a href="{{ route('admin.list_contacts.index', [ 'type' => 'contact_type', 'type_id' => LEADS_TYPE ]) }}">
                        <i class="fa fa-tty"></i>
                        <span>@lang('global.contacts.title_leads')</span>
                    </a>
                </li>
                @endcan
            @endif --}}




            @if( isPluginActive('account') )
                @can('expense_management_access')
                {{-- <li class="header">@lang('custom.menu.balance')</li> --}}
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-money"></i>
                        <span>@lang('global.expense-management.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('income_access')
                        <li>
                            <a href="{{ route('admin.incomes.index') }}">
                                <i class="fa fa-arrow-circle-right"></i>
                                <span>@lang('global.income.title-incomes')</span>
                            </a>
                        </li>@endcan

                        @can('expense_access')
                        <li>
                            <a href="{{ route('admin.expenses.index') }}">
                                <i class="fa fa-arrow-circle-left"></i>
                                <span>@lang('global.expense.title')</span>
                            </a>
                        </li>@endcan

                         @can('income_category_access')
                        <li>
                            <a href="{{ route('admin.income_categories.index') }}">
                                <i class="fa fa-list"></i>
                                <span>@lang('global.income-category.title')</span>
                            </a>
                        </li>@endcan

                        @can('expense_category_access')
                        <li>
                            <a href="{{ route('admin.expense_categories.index') }}">
                                <i class="fa fa-list"></i>
                                <span>@lang('global.expense-category.title')</span>
                            </a>
                        </li>@endcan



                        {{-- @can('monthly_report_access')
                        <li>
                            <a href="{{ route('admin.monthly_reports.index') }}">
                                <i class="fa fa-line-chart"></i>
                                <span>@lang('global.monthly-report.title')</span>
                            </a>
                        </li>@endcan --}}

                        @can('transfer_access')
                        <li>
                            <a href="{{ route('admin.transfers.index') }}">
                                <i class="fa fa-bank"></i>
                                <span>@lang('global.transfers.title')</span>
                            </a>
                        </li>@endcan


                        @can('account_access')
                        <li>
                            <a href="{{ route('admin.accounts.index') }}">
                                <i class="fa fa-anchor"></i>
                                <span>@lang('global.accounts.title')</span>
                            </a>
                        </li>@endcan

                    </ul>
                </li>
                @endcan
            @endif


            @if( isPluginActive('order') )
                @can('order_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-cart-plus"></i>
                        <span>@lang('orders::global.orders.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('order_access')
                        <li>
                            <a href="{{ route('admin.orders.index') }}">
                                <i class="fa fa-server"></i>
                                <span>@lang('orders::global.orders.list')</span>
                            </a>
                        </li>@endcan
                        @can('order_create')
                        <li>
                            <a href="{{ route('admin.orders.create') }}">
                                <i class="fa fa-plus"></i>
                                <span>@lang('orders::global.orders.place-new-order')</span>
                            </a>
                        </li>@endcan
                    </ul>
                </li>
                @endcan
            @endif



            @if( isPluginActive('asset') )
                @can('assets_management_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-book"></i>
                        <span>@lang('global.assets-management.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('asset_access')
                        <li>
                            <a href="{{ route('admin.assets.index') }}">
                                <i class="fa fa-book"></i>
                                <span>@lang('global.assets.title')</span>
                            </a>
                        </li>@endcan

                        @can('assets_category_access')
                        <li>
                            <a href="{{ route('admin.assets_categories.index') }}">
                                <i class="fa fa-tags"></i>
                                <span>@lang('global.assets-categories.title')</span>
                            </a>
                        </li>@endcan

                        @can('assets_location_access')
                        <li>
                            <a href="{{ route('admin.assets_locations.index') }}">
                                <i class="fa fa-map-marker"></i>
                                <span>@lang('global.assets-locations.title')</span>
                            </a>
                        </li>@endcan

                        @can('assets_status_access')
                        <li>
                            <a href="{{ route('admin.assets_statuses.index') }}">
                                <i class="fa fa-server"></i>
                                <span>@lang('global.assets-statuses.title')</span>
                            </a>
                        </li>@endcan

                        @can('assets_history_access')
                        <li>
                            <a href="{{ route('admin.assets_histories.index') }}">
                                <i class="fa fa-th-list"></i>
                                <span>@lang('global.assets-history.title')</span>
                            </a>
                        </li>@endcan

                    </ul>
                </li>
                @endcan
            @endif


            @if( isPluginActive(['quick_notification', 'Sendsms']) )
                <li class="treeview {{ ( in_array( $request->segment(1), array( 'internal_notifications' ) ) || ( in_array($controller, array('SendSmsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-envelope"></i>
                        <span>@lang('global.internal-notifications.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if( isPluginActive('quick_notification') )
                            @can('internal_notification_access')
                            <li class="{{ ( $request->segment(1) == 'internal_notifications' ) ? 'active' : '' }}">
                                <a href="{{ route('admin.internal_notifications.index') }}">
                                    <i class="fa fa-briefcase"></i>
                                    <span>@lang('global.internal-notifications.title')</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                        @if( File::exists(config('modules.paths.modules') . '/Sendsms') && Module::find('sendsms')->active && isPluginActive('Sendsms'))
                            @can('send_sm_access')
                            <li class="{{ ( in_array($controller, array('SendSmsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                                <a href="{{ route('admin.send_sms.index') }}">
                                    <i class="fa fa-envelope-open" aria-hidden="true"></i>
                                    <span>@lang('sendsms::global.send-sms.title')</span>
                                </a>
                            </li>
                            @endcan
                        @endif
                    </ul>
                </li>
            @endif


            @if( isPluginActive( ['support', 'faq'] ) )
                @if( Gate::allows('support_access') || Gate::allows('faq_management_access') || Gate::allows('faq_category_access') )
                <li class="treeview {{ ( in_array( $request->segment(1), array( 'tickets' ) ) || in_array( $request->segment(2), array(  'articles', 'faq_questions', 'faq_categories' ) ) ) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-building-o"></i>
                        <span>@lang('global.knowledgebase.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if( isPluginActive('support') && Route::has('project_tickets.index') )
                            @can('support_access')
                            <li class="{{ ( $request->segment(1) == 'tickets' ) ? 'active' : '' }}">
                                <a href="{{ route('project_tickets.index') }}">
                                   <i class="fa fa-sun-o"></i>
                                    <span>@lang('global.support.title')</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                        @if( isPluginActive('faq') )
                            @can('faq_management_access')
                            <li class="{{ ( $request->segment(2) == 'faq_questions' ) ? 'active' : '' }}">
                                <a href="{{ route('admin.faq_questions.index') }}">
                                    <i class="fa fa-question"></i>
                                    <span>@lang('global.faq-management.faq')</span>
                                </a>
                            </li>
                            @endcan

                            @can('faq_category_access')
                            <li class="{{ ( $request->segment(2) == 'faq_categories' ) ? 'active' : '' }}">
                                <a href="{{ route('admin.faq_categories.index') }}">
                                    <i class="fa fa-briefcase"></i>
                                    <span>@lang('global.faq-categories.title')</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                    </ul>
                </li>
                @endif
            @endif


            @if( isPluginActive( ['content_management', 'article'] ) )
                @can('content_management_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-book"></i>
                        <span>@lang('global.content-management.title')</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if( isPluginActive( 'content_management' ) )
                            @can('content_category_access')
                            <li>
                                <a href="{{ route('admin.content_categories.index') }}">
                                    <i class="fa fa-folder"></i>
                                    <span>@lang('global.content-categories.title')</span>
                                </a>
                            </li>
                            @endcan

                            @can('content_tag_access')
                            <li>
                                <a href="{{ route('admin.content_tags.index') }}">
                                    <i class="fa fa-tags"></i>
                                    <span>@lang('global.content-tags.title')</span>
                                </a>
                            </li>
                            @endcan

                            @can('content_page_access')
                            <li>
                                <a href="{{ route('admin.content_pages.index') }}">
                                    <i class="fa fa-file-o"></i>
                                    <span>@lang('global.content-pages.title')</span>
                                </a>
                            </li>
                            @endcan
                        @endif

                        @if( isPluginActive( 'article' ) )
                            @can('article_access')
                            <li class="{{ ( $request->segment(2) == 'articles' ) ? 'active' : '' }}">
                                <a href="{{ route('admin.articles.index') }}">
                                    <i class="fa fa-bookmark-o"></i>
                                    <span>@lang('global.articles.title')</span>
                                </a>
                            </li>
                            @endcan
                        @endif
                    </ul>
                </li>
                @endcan
            @endif


            @can('global_setting_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-gears"></i>
                    <span>@lang('global.global-settings.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('master_setting_access')
                    <li>
                        <a href="{{ route('admin.master_settings.index') }}">
                            <i class="fa fa-gear"></i>
                            <span>@lang('global.master-settings.title')</span>
                        </a>
                    </li>@endcan

                    @if( File::exists(config('modules.paths.modules') . '/DynamicOptions') && Module::find('dynamicoptions')->active && isPluginActive('dynamicoptions'))
                        @can('dynamic_option_access')
                        <li>
                            <a href="{{ route('admin.dynamic_options.index') }}">
                                <i class="fa fa-money"></i>
                                <span>@lang('global.dynamic-options.title')</span>
                            </a>
                        </li>
                        @endcan
                    @endif

                    @can('currency_access')
                    <li>
                        <a href="{{ route('admin.currencies.index') }}">
                            <i class="fa fa-money"></i>
                            <span>@lang('global.currencies.title')</span>
                        </a>
                    </li>@endcan

                    @can('template_access')
                    <li>
                        <a href="{{ route('admin.templates.index') }}">
                            <i class="fa fa-sitemap"></i>
                            <span>@lang('templates::global.templates.email-templates')</span>
                        </a>
                    </li>@endcan

                    @if( File::exists(config('modules.paths.modules') . '/Sendsms') && Module::find('sendsms')->active && isPluginActive('sendsms'))
                        @can('smstemplate_access')
                        <li>
                            <a href="{{ route('admin.smstemplates.index') }}">
                                <i class="fa fa-commenting-o"></i>
                                <span>@lang('smstemplates::global.smstemplates.title')</span>
                            </a>
                        </li>
                        @endcan
                    @endif

                    @can('payment_gateway_access')
                    <li>
                        <a href="{{ route('admin.payment_gateways.index') }}">
                            <i class="fa fa-creative-commons"></i>
                            <span>@lang('global.payment-gateways.title')</span>
                        </a>
                    </li>@endcan



                    @can('tax_access')
                    <li>
                        <a href="{{ route('admin.taxes.index') }}">
                            <i class="fa fa-database"></i>
                            <span>@lang('global.taxes.title')</span>
                        </a>
                    </li>@endcan

                    @can('discount_access')
                    <li>
                        <a href="{{ route('admin.discounts.index') }}">
                            <i class="fa fa-dollar"></i>
                            <span>@lang('global.discounts.title')</span>
                        </a>
                    </li>@endcan




                    @can('translation_manager')
                    <li>
                        <a href="{{ URL_TRANSLATIONS }}">
                            <i class="fa fa-language"></i>
                            <span>@lang('custom.translations.title')</span>
                        </a>
                    </li>@endcan

                    @if( isPluginActive('languages'))
                        @can('language_access')
                        <li class="{{ ( in_array($controller, array('LanguagesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                            <a href="{{ route('admin.languages.index') }}">
                                <i class="fa fa-sign-language"></i>
                                <span>@lang('global.languages.title')</span>
                            </a>
                        </li>
                        @endcan
                    @endif


                    @if( File::exists(config('modules.paths.modules') . '/DatabaseBackup') && Module::find('databasebackup')->active && isPluginActive('databasebackup'))
                    @can('database_backup_access')
                    <li>
                        <a href="{{ route('admin.database_backups.index') }}">
                            <i class="fa fa-database"></i>
                            <span>@lang('global.database-backup.title')</span>
                        </a>
                    </li>@endcan
                    @endif
                     @if( File::exists(config('modules.paths.modules') . '/SiteThemes') && Module::find('sitethemes')->active && isPluginActive('sitethemes'))
                    @can('site_theme_access')
                    <li>
                        <a href="{{ route('admin.site_themes.index') }}">
                            <i class="fa fa-shopping-bag"></i>
                            <span>@lang('sitethemes::global.site-themes.title')</span>
                        </a>
                    </li>@endcan
                    @endif
                    @if( isPluginActive('dashboardwidgets') )
                    @can('widget_access')
                    <li>
                        <a href="{{ route('admin.home.dashboard-widgets') }}">
                            <i class="fa fa-shopping-bag"></i>
                            <span>@lang('global.dashboard-widgets.title')</span>
                        </a>
                    </li>@endcan
                    @endif



                </ul>
            </li>@endcan



             @can('reports_access')
            <li class="{{ ( in_array($controller, array('ReportsController') ) ) ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-line-chart"></i>
                    <span class="title">@lang('custom.reports.generated-reports')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    {{-- @can('reports_income_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('incomeReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/income-report') }}">
                            <i class="fa fa-signal"></i>
                            <span class="title">@lang('custom.reports.income-report')</span>
                        </a>
                    </li>
                     @endcan --}}

                       {{-- @can('reports_expense_access')
                   <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('expenseReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/expense-report') }}">
                            <i class="fa fa-area-chart"></i>
                            <span class="title">@lang('custom.reports.expense-report')</span>
                        </a>
                    </li>
                    @endcan --}}


                    {{-- @can('reports_users_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('usersReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/users-report') }}">
                            <i class="fa fa-bar-chart"></i>
                            <span class="title">@lang('custom.reports.users-report')</span>
                        </a>
                    </li>
                     @endcan --}}

                     {{-- @can('reports_users_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('rolesUsersReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/roles-users-report') }}">
                            <i class="fa fa-pie-chart"></i>
                            <span class="title">@lang('others.reports.users-roles-report')</span>
                        </a>
                    </li>
                     @endcan --}}

                    @can('reports_projects_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('contactsProjectsReports') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/contacts-projects-reports') }}">
                            <i class="fa fa-bar-chart"></i>
                            <span class="title">@lang('custom.reports.projects-report')</span>
                        </a>
                    </li>
                     @endcan

                    @can('reports_tasks_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('tasksReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/tasks-report') }}">
                            <i class="fa fa-signal"></i>
                            <span class="title">@lang('custom.reports.tasks-report')</span>
                        </a>
                    </li>
                     @endcan

                    @can('reports_assets_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('assetsReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/assets-report') }}">
                            <i class="fa fa-bar-chart"></i>
                            <span class="title">@lang('custom.reports.assets-report')</span>
                        </a>
                    </li>
                     @endcan

                    {{-- @can('reports_products_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('productsReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/products-report') }}">
                            <i class="fa fa-line-chart"></i>
                            <span class="title">@lang('custom.reports.products-report')</span>
                        </a>
                    </li>
                    @endcan --}}
{{--
                    @can('reports_purchase_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('purchaseOrdersReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/purchase-orders-report') }}">
                            <i class="fa fa-bar-chart"></i>
                            <span class="title">@lang('custom.reports.purchase-order-report')</span>
                        </a>
                    </li>
                    @endcan --}}
                </ul>
            </li>
            @endcan




            @if( File::exists(config('modules.paths.modules') . '/ModulesManagement') && Module::find('modulesmanagement')->active && isPluginActive('modulesmanagement'))
                @can('modules_management_access')
                <li class="{{ ( in_array($controller, array('ModulesManagementsController') ) && in_array($action, array('index', 'create', 'edit', 'show') ) ) ? 'active' : '' }}">
                    <a href="{{ route('admin.modules_managements.index') }}">
                        <i class="fa fa-tasks"></i>
                        <span>@lang('modulesmanagement::global.modules-management.title')</span>
                    </a>
                </li>
                @endcan
            @endif

            @php ($unread = App\MessengerTopic::countUnread())
            <li class="{{ $request->segment(2) == 'messenger' ? 'active' : '' }} {{ ($unread > 0 ? 'unread' : '') }}">
                <a href="{{ route('admin.messenger.index') }}">
                    <i class="fa fa-envelope"></i>

                    <span>@lang('custom.app_messages')</span>
                    @if($unread > 0)
                        {{ ($unread > 0 ? '('.$unread.')' : '') }}
                    @endif
                </a>
            </li>
            @endif



            <li class="{{ $request->segment(1) == 'change_password' ? 'active' : '' }}">
                <a href="{{ route('auth.change_password') }}">
                    <i class="fa fa-key"></i>
                    <span class="title">@lang('global.app_change_password')</span>
                </a>
            </li>

            <li>
                <a href="#logout" onclick="$('#logout').submit();">
                    <i class="fa fa-arrow-left"></i>
                    <span class="title">@lang('global.app_logout')</span>
                </a>
            </li>
        </ul>
    </section>
</aside>

