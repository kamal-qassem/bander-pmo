@inject('request', 'Illuminate\Http\Request')
<div class="sidebar-wrapper">
    <?php
    $parts = getController();
    $controller = $parts['controller'];
    $action = $parts['action'];
    ?>
    <div>
        <div class="logo-wrapper">
            <a href=""><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}"
                    alt=""><img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                    alt=""></a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
        </div>
        <div class="logo-icon-wrapper"><a href="#"><img class="img-fluid"
                    src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
<hr/>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title active" href="#" data-bs-original-title=""
                            title=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg><span class="lan-3">@lang('global.app_dashboard')</span>
                        </a>
                    </li>

 
                    @if (isPluginActive('client_project'))
                        @can('project_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa  fa-cubes" style="padding-right:12px"></i><span>@lang('global.projects.title')</span>
                                    <div class="according-menu">
                                    <i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}">
                                            </i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
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
                                    @if (isEnable('debug'))
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

                    @if (isPluginActive('task'))
                        @can('task_management_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa  fa-list" style="padding-right:12px"></i><span>@lang('global.task-management.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
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

                    @if (isPluginActive('asset'))
                        @can('assets_management_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa   fa-book" style="padding-right:12px"></i>
                                    <span>@lang('global.assets-management.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @can('asset_access')
                                        <li>
                                            <a href="{{ route('admin.assets.index') }}">
                                                <i class="fa fa-book"></i>
                                                <span>@lang('global.assets.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('assets_category_access')
                                        <li>
                                            <a href="{{ route('admin.assets_categories.index') }}">
                                                <i class="fa fa-tags"></i>
                                                <span>@lang('global.assets-categories.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('assets_location_access')
                                        <li>
                                            <a href="{{ route('admin.assets_locations.index') }}">
                                                <i class="fa fa-map-marker"></i>
                                                <span>@lang('global.assets-locations.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('assets_status_access')
                                        <li>
                                            <a href="{{ route('admin.assets_statuses.index') }}">
                                                <i class="fa fa-server"></i>
                                                <span>@lang('global.assets-statuses.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('assets_history_access')
                                        <li>
                                            <a href="{{ route('admin.assets_histories.index') }}">
                                                <i class="fa fa-th-list"></i>
                                                <span>@lang('global.assets-history.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                </ul>
                            </li>
                        @endcan
                    @endif

                    @if (isPluginActive(['invoice', 'credit_note', 'quotes']))
                        @can('sale_access')
                            <li class="mega-menu">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa fa-life-saver" style="padding-right:12px"></i>
                                    <span>@lang('global.sales.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @if (isPluginActive('invoice'))
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

                                    @if (isPluginActive('credit_note'))
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
                                    @if (isPluginActive('purchase_order'))
                                        @can('purchase_order_access')
                                            <li>
                                                <a href="{{ route('admin.purchase_orders.index') }}">
                                                    <i class="fa fa-anchor"></i>
                                                    <span>@lang('global.purchase-orders.title')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                    @if (File::exists(config('modules.paths.modules') . '/Quotes') &&
                                        Module::find('quotes')->active &&
                                        isPluginActive('quotes'))
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

                                    @if (File::exists(config('modules.paths.modules') . '/Proposals') &&
                                        Module::find('proposals')->active &&
                                        isPluginActive('proposals'))
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

                                    @if (File::exists(config('modules.paths.modules') . '/Contracts') &&
                                        Module::find('contracts')->active &&
                                        isPluginActive('contracts'))
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



                                    @if (File::exists(config('modules.paths.modules') . '/RecurringInvoices') &&
                                        Module::find('recurringinvoices')->active &&
                                        isPluginActive('recurringinvoices'))
                                        @can('recurring_invoice_access')
                                            @can('recurring_invoice_access')
                                                <li>
                                                    <a href="{{ route('admin.recurring_invoices.index') }}">
                                                        <i class="fa fa-recycle"></i>
                                                        <span>@lang('global.recurring-invoices.title')</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('recurring_period_access')
                                                <li>
                                                    <a href="{{ route('admin.recurring_periods.index') }}">
                                                        <i class="fa fa-recycle"></i>
                                                        <span>@lang('global.recurring-periods.title')</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        @endcan
                                    @endif
                                </ul>
                            </li>
                        @endcan

                    @endif

                    @if (isPluginActive('order'))
                        @can('order_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa  fa-cart-plus" style="padding-right:12px"></i>
                                    <span>@lang('orders::global.orders.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @can('order_access')
                                        <li>
                                            <a href="{{ route('admin.orders.index') }}">
                                                <i class="fa fa-server"></i>
                                                <span>@lang('orders::global.orders.list')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('order_create')
                                        <li>
                                            <a href="{{ route('admin.orders.create') }}">
                                                <i class="fa fa-plus"></i>
                                                <span>@lang('orders::global.orders.place-new-order')</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                    @endif

                    @if (Gate::allows('contact_access') &&
                        Gate::allows('contact_create') &&
                        Gate::allows('contact_company_access') &&
                        Gate::allows('country_access') &&
                        Gate::allows('contact_group_access') &&
                        Gate::allows('contact_type_access') &&
                        Gate::allows('contact_note_access') &&
                        Gate::allows('contact_document_access') &&
                        Gate::allows('contact_mailchimp_email_campaigns'))
                        @can('contact_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa fa-phone-square" style="padding-right:12px"
                                        data-feather="USER"></i><span>@lang('global.contact-management.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
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
                                        </li>
                                    @endcan
                                    @can('country_access')
                                        <li>
                                            <a href="{{ route('admin.countries.index') }}">
                                                <i class="fa fa-globe"></i>
                                                <span>@lang('global.countries.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('contact_group_access')
                                        <li>
                                            <a href="{{ route('admin.contact_groups.index') }}">
                                                <i class="fa fa-connectdevelop"></i>
                                                <span>@lang('global.contact-groups.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('contact_type_access')
                                        <li>
                                            <a href="{{ route('admin.contact_types.index') }}">
                                                <i class="fa fa-align-justify"></i>
                                                <span>@lang('global.contact-types.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('contact_note_access')
                                        <li>
                                            <a href="{{ route('admin.contact_notes.index') }}">
                                                <i class="fa fa-sticky-note-o"></i>
                                                <span>@lang('global.contact-notes.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('contact_document_access')
                                        <li>
                                            <a href="{{ route('admin.contact_documents.index') }}">
                                                <i class="fa fa-files-o"></i>
                                                <span>@lang('global.contact-documents.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('contact_mailchimp_email_campaigns')
                                        <li>
                                            <a href="{{ route('admin.contacts.mailchimp-email-campaigns') }}">
                                                <i class="fa fa-files-o"></i>
                                                <span>@lang('global.contacts.mailchimp-email-campaigns')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @if (isPluginActive('lead'))
                                        @can('contact_access')
                                            <li>
                                                <a
                                                    href="{{ route('admin.list_contacts.index', ['type' => 'contact_type', 'type_id' => LEADS_TYPE]) }}">

                                                    <i class="fa fa-tty"></i>
                                                    <span>@lang('global.contacts.title_leads')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </li>
                        @endcan
                    @endif

                    @if (isPluginActive('user'))
                        @can('user_management_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa fa-users" style="padding-right:12px"
                                        data-feather="USER"></i><span>@lang('global.user-management.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @can('user_access')
                                        <li>
                                            <a href="{{ route('admin.users.index') }}">
                                                <i class="fa fa-user"></i>
                                                <span>@lang('global.users.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @if (isEnable('debug'))
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
                                        </li>
                                    @endcan



                                    @can('user_action_access')
                                        <li>
                                            <a href="{{ route('admin.user_actions.index') }}">
                                                <i class="fa fa-th-list"></i>
                                                <span>@lang('global.user-actions.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('department_access')
                                        <li>
                                            <a href="{{ route('admin.departments.index') }}">
                                                <i class="fa fa-codepen"></i>
                                                <span>@lang('global.departments.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                </ul>
                            </li>
                        @endcan
                    @endif


                    @if (isPluginActive('product'))
                        @can('product_management_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa fa-shopping-cart" style="padding-right:12px"></i>
                                    <span>@lang('custom.menu.stock')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @can('product_access')
                                        <li>
                                            <a href="{{ route('admin.products.index') }}">
                                                <i class="fa fa-shopping-cart"></i>
                                                <span>@lang('global.products.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @if (isPluginActive('productcategory'))
                                        @can('product_category_access')
                                            <li>
                                                <a href="{{ url('admin/product_categories') }}">
                                                    <i class="fa fa-folder"></i>
                                                    <span>@lang('global.product-categories.title')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif


                                    @can('products_transfer_access')
                                        <li>
                                            <a href="{{ route('admin.products_transfers.index') }}">
                                                <i class="fa fa-transgender-alt"></i>
                                                <span>@lang('global.products-transfer.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @if (isPluginActive('productbrand'))
                                        @can('brand_access')
                                            <li>
                                                <a href="{{ route('admin.brands.index') }}">
                                                    <i class="fa fa-adn"></i>
                                                    <span>@lang('global.brands.title')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                    @if (isPluginActive('productmeasurementunits'))
                                        @can('measurement_unit_access')
                                            <li>
                                                <a href="{{ route('admin.measurement_units.index') }}">
                                                    <i class="fa fa-dot-circle-o"></i>
                                                    <span>@lang('global.measurement-units.title')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                    @if (isPluginActive('productwarehouse'))
                                        @can('warehouse_access')
                                            <li>
                                                <a href="{{ route('admin.warehouses.index') }}">
                                                    <i class="fa fa-life-bouy"></i>
                                                    <span>@lang('global.warehouses.title')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif

                                </ul>
                            </li>
                        @endcan
                    @endif

                    @if (isPluginActive('account'))
                        @can('expense_management_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa fa-money" style="padding-right:12px"></i>
                                    <span>@lang('custom.menu.balance')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @can('income_access')
                                        <li>
                                            <a href="{{ route('admin.incomes.index') }}">
                                                <i class="fa fa-arrow-circle-right"></i>
                                                <span>@lang('global.income.title-incomes')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('expense_access')
                                        <li>
                                            <a href="{{ route('admin.expenses.index') }}">
                                                <i class="fa fa-arrow-circle-left"></i>
                                                <span>@lang('global.expense.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('income_category_access')
                                        <li>
                                            <a href="{{ route('admin.income_categories.index') }}">
                                                <i class="fa fa-list"></i>
                                                <span>@lang('global.income-category.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('expense_category_access')
                                        <li>
                                            <a href="{{ route('admin.expense_categories.index') }}">
                                                <i class="fa fa-list"></i>
                                                <span>@lang('global.expense-category.title')</span>
                                            </a>
                                        </li>
                                    @endcan



                                    @can('monthly_report_access')
                                        <li>
                                            <a href="{{ route('admin.monthly_reports.index') }}">
                                                <i class="fa fa-line-chart"></i>
                                                <span>@lang('global.monthly-report.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('transfer_access')
                                        <li>
                                            <a href="{{ route('admin.transfers.index') }}">
                                                <i class="fa fa-bank"></i>
                                                <span>@lang('global.transfers.title')</span>
                                            </a>
                                        </li>
                                    @endcan


                                    @can('account_access')
                                        <li>
                                            <a href="{{ route('admin.accounts.index') }}">
                                                <i class="fa fa-anchor"></i>
                                                <span>@lang('global.accounts.title')</span>
                                            </a>
                                        </li>
                                    @endcan

                                </ul>
                            </li>
                        @endcan
                    @endif

                    @if (isPluginActive(['quick_notification', 'Sendsms']))
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                href="#">
                                <i class="fa fa-envelope" style="padding-right:12px"></i>
                                <span>@lang('global.internal-notifications.title')</span>
                                <div class="according-menu"><i
                                        class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                </div>
                            </a>
                            <ul class="sidebar-submenu"
                                style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                @if (isPluginActive('quick_notification'))
                                    @can('internal_notification_access')
                                        <li
                                            class="{{ $request->segment(1) == 'internal_notifications' ? 'active' : '' }}">
                                            <a href="{{ route('admin.internal_notifications.index') }}">
                                                <i class="fa fa-briefcase"></i>
                                                <span>@lang('global.internal-notifications.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                                @if (File::exists(config('modules.paths.modules') . '/Sendsms') &&
                                    Module::find('sendsms')->active &&
                                    isPluginActive('Sendsms'))
                                    @can('send_sm_access')
                                        <li
                                            class="{{ in_array($controller, ['SendSmsController']) && in_array($action, ['index', 'create', 'edit', 'show', 'destroy']) ? 'active' : '' }}">
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

                    @if (isPluginActive(['support', 'faq']))
                        @if (Gate::allows('support_access') ||
                            Gate::allows('faq_management_access') ||
                            Gate::allows('faq_category_access'))
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa fa-building-o" style="padding-right:12px"></i>
                                    <span>@lang('global.knowledgebase.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @if (isPluginActive('support') && Route::has('tickets.index'))
                                        @can('support_access')
                                            <li class="{{ $request->segment(1) == 'tickets' ? 'active' : '' }}">
                                                <a href="{{ route('tickets.index') }}">
                                                    <i class="fa fa-sun-o"></i>
                                                    <span>@lang('global.support.title')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif

                                    @if (isPluginActive('faq'))
                                        @can('faq_management_access')
                                            <li class="{{ $request->segment(2) == 'faq_questions' ? 'active' : '' }}">
                                                <a href="{{ route('admin.faq_questions.index') }}">
                                                    <i class="fa fa-question"></i>
                                                    <span>@lang('global.faq-management.faq')</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('faq_category_access')
                                            <li class="{{ $request->segment(2) == 'faq_categories' ? 'active' : '' }}">
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

                    @if (isPluginActive(['content_management', 'article']))
                        @can('content_management_access')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                    href="#">
                                    <i class="fa fa-book" style="padding-right:12px"></i>
                                    <span>@lang('global.content-management.title')</span>
                                    <div class="according-menu"><i
                                            class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                    </div>
                                </a>
                                <ul class="sidebar-submenu"
                                    style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                    @if (isPluginActive('content_management'))
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

                                    @if (isPluginActive('article'))
                                        @can('article_access')
                                            <li class="{{ $request->segment(2) == 'articles' ? 'active' : '' }}">
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

                    @can('reports_access')
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                href="#">
                                <i class="fa fa-line-chart" style="padding-right:12px"></i>
                                <span>@lang('custom.reports.generated-reports')</span>
                                <div class="according-menu"><i
                                        class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                </div>
                            </a>
                            <ul class="sidebar-submenu"
                                style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                <li class="{{ in_array($controller, ['ReportsController']) ? 'active' : '' }}">
                                    @can('reports_income_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['incomeReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/income-report') }}">
                                            <i class="fa fa-signal"></i>
                                            <span class="title">@lang('custom.reports.income-report')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('reports_expense_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['expenseReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/expense-report') }}">
                                            <i class="fa fa-area-chart"></i>
                                            <span class="title">@lang('custom.reports.expense-report')</span>
                                        </a>
                                    </li>
                                @endcan


                                @can('reports_users_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['usersReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/users-report') }}">
                                            <i class="fa fa-bar-chart"></i>
                                            <span class="title">@lang('custom.reports.users-report')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('reports_users_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['rolesUsersReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/roles-users-report') }}">
                                            <i class="fa fa-pie-chart"></i>
                                            <span class="title">@lang('others.reports.users-roles-report')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('reports_projects_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['contactsProjectsReports']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/contacts-projects-reports') }}">
                                            <i class="fa fa-bar-chart"></i>
                                            <span class="title">@lang('custom.reports.projects-report')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('reports_tasks_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['tasksReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/tasks-report') }}">
                                            <i class="fa fa-signal"></i>
                                            <span class="title">@lang('custom.reports.tasks-report')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('reports_assets_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['assetsReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/assets-report') }}">
                                            <i class="fa fa-bar-chart"></i>
                                            <span class="title">@lang('custom.reports.assets-report')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('reports_products_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['productsReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/products-report') }}">
                                            <i class="fa fa-line-chart"></i>
                                            <span class="title">@lang('custom.reports.products-report')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('reports_purchase_access')
                                    <li
                                        class="{{ in_array($controller, ['ReportsController']) && in_array($action, ['purchaseOrdersReport']) ? 'active' : '' }}">
                                        <a href="{{ url('/admin/reports/purchase-orders-report') }}">
                                            <i class="fa fa-bar-chart"></i>
                                            <span class="title">@lang('custom.reports.purchase-order-report')</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>

                    @endcan
                    @can('global_setting_access')
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                                href="#">
                                <i class="fa fa-gears" style="padding-right:12px"></i>
                                <span>@lang('global.global-settings.title')</span>
                                <div class="according-menu"><i
                                        class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                                </div>
                            </a>
                            <ul class="sidebar-submenu"
                                style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                                @can('master_setting_access')
                                    <li>
                                        <a href="{{ route('admin.master_settings.index') }}">
                                            <i class="fa fa-gear"></i>
                                            <span>@lang('global.master-settings.title')</span>
                                        </a>
                                    </li>
                                @endcan

                                @if (File::exists(config('modules.paths.modules') . '/DynamicOptions') &&
                                    Module::find('dynamicoptions')->active &&
                                    isPluginActive('dynamicoptions'))
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
                                    </li>
                                @endcan

                                @can('template_access')
                                    <li>
                                        <a href="{{ route('admin.templates.index') }}">
                                            <i class="fa fa-sitemap"></i>
                                            <span>@lang('templates::global.templates.email-templates')</span>
                                        </a>
                                    </li>
                                @endcan

                                @if (File::exists(config('modules.paths.modules') . '/Sendsms') &&
                                    Module::find('sendsms')->active &&
                                    isPluginActive('sendsms'))
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
                                    </li>
                                @endcan



                                @can('tax_access')
                                    <li>
                                        <a href="{{ route('admin.taxes.index') }}">
                                            <i class="fa fa-database"></i>
                                            <span>@lang('global.taxes.title')</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('discount_access')
                                    <li>
                                        <a href="{{ route('admin.discounts.index') }}">
                                            <i class="fa fa-dollar"></i>
                                            <span>@lang('global.discounts.title')</span>
                                        </a>
                                    </li>
                                @endcan




                                @can('translation_manager')
                                    <li>
                                        <a href="{{ URL_TRANSLATIONS }}">
                                            <i class="fa fa-language"></i>
                                            <span>@lang('custom.translations.title')</span>
                                        </a>
                                    </li>
                                @endcan

                                @if (isPluginActive('languages'))
                                    @can('language_access')
                                        <li
                                            class="{{ in_array($controller, ['LanguagesController']) && in_array($action, ['index', 'create', 'edit', 'show', 'destroy']) ? 'active' : '' }}">
                                            <a href="{{ route('admin.languages.index') }}">
                                                <i class="fa fa-sign-language"></i>
                                                <span>@lang('global.languages.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                @endif


                                @if (File::exists(config('modules.paths.modules') . '/DatabaseBackup') &&
                                    Module::find('databasebackup')->active &&
                                    isPluginActive('databasebackup'))
                                    @can('database_backup_access')
                                        <li>
                                            <a href="{{ route('admin.database_backups.index') }}">
                                                <i class="fa fa-database"></i>
                                                <span>@lang('global.database-backup.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                                @if (File::exists(config('modules.paths.modules') . '/SiteThemes') &&
                                    Module::find('sitethemes')->active &&
                                    isPluginActive('sitethemes'))
                                    @can('site_theme_access')
                                        <li>
                                            <a href="{{ route('admin.site_themes.index') }}">
                                                <i class="fa fa-shopping-bag"></i>
                                                <span>@lang('sitethemes::global.site-themes.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                                @if (isPluginActive('dashboardwidgets'))
                                    @can('widget_access')
                                        <li>
                                            <a href="{{ route('admin.home.dashboard-widgets') }}">
                                                <i class="fa fa-shopping-bag"></i>
                                                <span>@lang('global.dashboard-widgets.title')</span>
                                            </a>
                                        </li>
                                    @endcan
                                @endif



                            </ul>
                        </li>
                    @endcan  







{{-- 
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title {{ request()->route()->getPrefix() == '/ecommerce'? 'active': '' }}"
                            href="#"><i
                                data-feather="shopping-bag"></i><span>{{ trans('lang.Ecommerce') }}</span>
                            <div class="according-menu"><i
                                    class="fa fa-angle-{{ request()->route()->getPrefix() == '/ecommerce'? 'down': 'right' }}"></i>
                            </div>
                        </a>
                        <ul class="sidebar-submenu"
                            style="display: {{ request()->route()->getPrefix() == '/ecommerce'? 'block': 'none;' }};">
                            <li><a href=""
                                    class="{{ Route::currentRouteName() == 'product' ? 'active' : '' }}">Product</a>
                            </li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'product-page' ? 'active' : '' }}">Product
                                    page</a></li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'list-products' ? 'active' : '' }}">Product
                                    list</a></li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'payment-details' ? 'active' : '' }}">Payment
                                    Details</a></li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'order-history' ? 'active' : '' }}">Order
                                    History</a></li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'invoice-template' ? 'active' : '' }}">Invoice</a>
                            </li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'cart' ? 'active' : '' }}">Cart</a>
                            </li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'list-wish' ? 'active' : '' }}">Wishlist</a>
                            </li>
                            <li><a href=" "
                                    class="{{ Route::currentRouteName() == 'checkout' ? 'active' : '' }}">Checkout</a>
                            </li>
                            <li><a href=""
                                    class="{{ Route::currentRouteName() == 'pricing' ? 'active' : '' }}">Pricing</a>
                            </li>
                        </ul>
                    </li> --}}




                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
