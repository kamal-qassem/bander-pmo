@inject('request', 'Illuminate\Http\Request')
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <?php
        $userprofileplacement = getActiveTheme('user-profile-placement', 'Topbar');
        if ( 'Sidebar' === $userprofileplacement ) {
        ?>
        <!-- User Info -->
        <?php
        $contact = \App\Contact::where( 'id', '=', getContactId() )->first();
        if ( $contact ) {
            $name = $contact->first_name . ' ' . $contact->last_name;
            $image = '';
            $email = $contact->email;
            if ($contact->thumbnail && file_exists(public_path().'/thumb/' . $contact->thumbnail) ) {
                $image = asset(env('UPLOAD_PATH').'/thumb/'.$contact->thumbnail);
            }
        } else {                    
            $name = Auth::user()->name;
            $image = '';
            $email = Auth::user()->email;
        }
        ?>
        <div class="user-info">
            @if ( ! empty( $image ) )
            <div class="image">
                <img src="{{$image}}" width="48" height="48" alt="{{$name}}" />
            </div>
            @endif
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$name}}</div>
                <div class="email">{{$email}}</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="{{route('admin.contacts.profile.edit')}}"><i class="material-icons">person</i>Profile</a></li>
                        <li role="separator" class="divider"></li>
                        @can('invoice_access')
                        <li>
                            <a href="{{ route('admin.invoices.index') }}">
                                <i class="fa fa-credit-card"></i>
                                <span>@lang('global.invoices.title')</span>
                            </a>
                        </li>
                        @endcan
                        @can('product_access')
                        <li>
                            <a href="{{ route('admin.products.index') }}">
                                <i class="material-icons">shopping_cart</i>
                                <span>@lang('global.products.title')</span>
                            </a>
                        </li>@endcan
                        @can('order_access')
                        <li>
                            <a href="{{ route('admin.orders.index') }}">
                                <i class="fa fa-server"></i>
                                <span>@lang('orders::global.orders.list')</span>
                            </a>
                        </li>@endcan
                        
                        <li role="separator" class="divider"></li>
                        <li>
                        <a href="#logout" onclick="$('#logout').submit();">
                            <i class="material-icons">input</i>
                            @lang('global.app_logout')
                        </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <?php } ?>
        <!-- Menu -->

        <div class="menu">
            <ul class="list">

            <?php
            $parts = getController();
            $controller = $parts['controller'];
            $action = $parts['action'];
           
            ?>
            <?php
            $use_dynamic_menu = getSetting('use-dynamic-menu', 'site_settings', 'No');
            if ( ! isAdmin() && 'Yes' === $use_dynamic_menu ) {
                $role = auth()->user()->role->first()->slug;
                
                if ( ! empty( $role ) ) {
                    $public_menu = \Harimayco\Menu\Models\Menus::byName( $role );
                    $theme = \Cookie::get('theme');
                    if ( empty( $theme ) ) {
                        $theme = 'bsb';
                    }

                    if ( $public_menu ) {
                        $public_menu = Menu::getByName( $role );
                        $parent_start = false;
                        foreach ($public_menu as $menu) {
                            if ( ! empty( $menu['theme'] ) && $theme != $menu['theme'] ) {
                                continue;
                            }                            

                            $query_string = str_replace($menu['link'], '',$request->fullUrl());

                            if ( 'heading' === $menu['link'] ) {
                                echo '<li class="header">'.$menu['label'].'</li>';
                                $parent_start = false;
                            } elseif ( ! empty( $menu['child'] ) ) {                               
                                
                                $isactive = false;
                                if ( (strpos($query_string, $menu['link'] ) ) !== false ) {
                                    $isactive = true;
                                }
                                foreach( $menu['child'] as $child ) {
                                    $query_string = str_replace($child['link'], '',$request->fullUrl());
                                    if ( (strpos($query_string, $child['link'] ) ) !== false ) {
                                        $isactive = true;
                                    }
                                }
                                ?>

                                <li class="{{ ( $isactive ) ? 'active' : '' }}">
                                    <a href="#" class="menu-toggle">
                                        @if ( ! empty( $menu['icon_html'] ) )
                                            {!! clean($menu['icon_html']) !!}
                                        @endif
                                        <span>{{$menu['label']}}-{{$query_string}}</span>                                        
                                    </a>
                                    @foreach( $menu['child'] as $child )
                                    <ul class="ml-menu">                                       
                                        <li class="{{ ( (strpos($query_string, $child['link'] ) ) !== false ) ? 'active' : '' }}">
                                            <a href="{{ url( $child['link'] ) }}">
                                                @if ( ! empty( $child['icon_html'] ) )
                                                    {!! clean($child['icon_html']) !!}
                                                @endif
                                                <span>{{$child['label']}}</span>
                                            </a>
                                        </li>
                                    </ul>
                                    @endforeach
                                </li>                                
                                <?php
                            } else {
                                ?>
                                <li class="{{ ( (strpos($query_string, $menu['link'] ) ) !== false ) ? 'active' : '' }}">
                                    <a href="{{ url( $menu['link'] ) }}">
                                        @if ( ! empty( $menu['icon_html'] ) )
                                            {!! clean($menu['icon_html']) !!}
                                        @endif
                                        <span>{{$menu['label']}}</span>
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
                <li class="{{ $controller == 'HomeController' ? 'active' : '' }}">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="material-icons">dashboard</i>
                        <span class="title">@lang('global.app_dashboard')</span>
                    </a>
                </li>

                @if( isPluginActive( ['invoice', 'credit_note', 'quotes'] ) )
                    @can('sale_access')
                    <li class="header">@lang('custom.menu.sales')</li>
                    <li class="{{ ( in_array($controller, array('InvoicesController', 'QuotesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                        <a href="#" class="menu-toggle">
                            <i class="material-icons">view_list</i><span>@lang('global.sales.title')</span>
                            
                        </a>
                        <ul class="ml-menu">
                            @if( isPluginActive('invoice') )
                                @can('invoice_access')
                                <li class="{{ ( in_array($controller, array('InvoicesController') ) && in_array($action, array('index', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                                    <a href="{{ route('admin.invoices.index') }}">
                                        <i class="material-icons">event_note</i><span>@lang('global.invoices.title')</span>
                                    </a>
                                </li>
                                @endcan
                                @can('invoice_create')
                                <li class="{{ ( in_array($controller, array('InvoicesController') ) && in_array($action, array('create') ) ) ? 'active' : '' }}">
                                    <a href="{{ route('admin.invoices.create') }}">
                                        <i class="material-icons">add_box</i><span>@lang('custom.menu.create-invoice')</span>
                                    </a>
                                </li>
                                @endcan
                            @endif


                            @if( isPluginActive('credit_note') )
                                  @can('credit_note_access')
                                <li>
                                    <a href="{{ route('admin.credit_notes.index') }}">
                                        <i class="material-icons">file_copy</i>
                                        <span>Credit notes</span>
                                    </a>
                                </li>
                                @endcan

                                   @can('credit_note_create')
                                <li>
                                    <a href="{{ route('admin.credit_notes.create') }}">
                                        <i class="material-icons">add_box</i>
                                        <span>New credit note</span>
                                    </a>
                                </li>
                                @endcan
                            @endif


                            @if( File::exists(config('modules.paths.modules') . '/Quotes') && Module::find('quotes')->active && isPluginActive('quotes'))
                                @can('quote_access')
                                <li class="{{ ( in_array($controller, array('QuotesController') ) && in_array($action, array('index', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                                    <a href="{{ route('admin.quotes.index') }}">
                                        <i class="material-icons">format_quote</i>
                                        <span>@lang('global.quotes.title')</span>
                                    </a>
                                </li>
                                @endcan
                            

                                @can('quote_create')
                                <li class="{{ ( in_array($controller, array('QuotesController') ) && in_array($action, array('create') ) ) ? 'active' : '' }}">
                                    <a href="{{ route('admin.quotes.create') }}">
                                        <i class="material-icons">add_circle</i>
                                        <span>@lang('custom.menu.create-quote')</span>
                                    </a>
                                </li>
                                @endcan  
                            @endif 

                            @if( File::exists(config('modules.paths.modules') . '/Proposals') && Module::find('proposals')->active && isPluginActive('proposals'))
                                @can('proposal_access')
                                <li>
                                    <a href="{{ route('admin.proposals.index') }}">
                                        <i class="material-icons">note</i>
                                        <span>@lang('proposals::custom.proposals.title')</span>
                                    </a>
                                </li>
                                @endcan 

                                @can('proposal_create')
                                <li>
                                    <a href="{{ route('admin.proposals.create') }}">
                                        <i class="material-icons">add_circle</i>
                                        <span>@lang('custom.menu.create-proposal')</span>
                                    </a>
                                </li>
                                @endcan
                            @endif

                            @if( File::exists(config('modules.paths.modules') . '/Contracts') && Module::find('contracts')->active && isPluginActive('contracts'))
                                @can('contract_access')
                                <li>
                                    <a href="{{ route('admin.contracts.index') }}">
                                         <i class="material-icons">book</i>
                                        <span>@lang('contracts::global.contracts.title')</span>
                                    </a>
                                </li>
                                @endcan 

                                @can('contract_create')
                                <li>
                                    <a href="{{ route('admin.contracts.create') }}">
                                        <i class="material-icons">add_circle</i>
                                        <span>@lang('custom.menu.create-contract')</span>
                                    </a>
                                </li>
                                @endcan
                                
                            @endif                 
                           
                            
                        </ul>
                    </li>
                    @endcan
                @endif

                @if( File::exists(config('modules.paths.modules') . '/RecurringInvoices') && Module::find('recurringinvoices')->active && isPluginActive('recurringinvoices'))
            @can('recurring_invoice_access')
            <li class="{{ ( in_array($controller, array('RecurringInvoicesController', 'RecurringPeriodsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">autorenew</i>
                    <span>@lang('global.recurring-invoices.title')</span>
                   
                </a>
                <ul class="ml-menu">                
                    @can('recurring_invoice_access')
                    <li class="{{ ( in_array($controller, array('RecurringInvoicesController') ) && in_array($action, array('index', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.recurring_invoices.index') }}">
                            <i class="material-icons">autorenew</i>
                            <span>@lang('global.recurring-invoices.title')</span>
                        </a>
                    </li>
                    @endcan

                    @can('recurring_invoice_create')
                    <li class="{{ ( in_array($controller, array('RecurringInvoicesController') ) && in_array($action, array('create') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.recurring_invoices.create') }}">
                            <i class="material-icons">add_circle</i>
                            <span>@lang('global.app_create')</span>
                        </a>
                    </li>
                    @endcan

                    @can('recurring_period_access')
                    <li class="{{ ( in_array($controller, array('RecurringPeriodsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.recurring_periods.index') }}">
                            <i class="material-icons">autorenew</i>
                            <span>@lang('global.recurring-periods.title')</span>
                        </a>
                    </li>@endcan
                    
                </ul>
            </li>@endcan
            @endif

            @if( isPluginActive('product') )   
                @can('product_management_access')
                <li class="header">@lang('custom.menu.stock')</li>
                <li class="{{ ( in_array( $request->segment(2), array( 'products', 'product_categories', 'product-tags', 'products_transfers', 'brands' ) ) || in_array( $controller, array('BrandsController', 'MeasurementUnitsController', 'WarehousesController') ) ) ? 'active' : '' }}">
                    <a href="#" class="menu-toggle">
                        <i class="material-icons">store</i>
                        <span>@lang('global.product-management.title')</span>
                        
                    </a>
                    <ul class="ml-menu">
                        @can('product_access')
                        <li class="{{ ( in_array($controller, array('ProductsController') ) && in_array($action, array('index', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                            <a href="{{ route('admin.products.index') }}">
                                <i class="material-icons">shopping_cart</i>
                                <span>@lang('global.products.title')</span>
                            </a>
                        </li>@endcan

                        @can('product_category_access')
                        <li class="{{ ( $request->segment(2) == 'product_categories' ) ? 'active' : '' }}">
                            <a href="{{ url('admin/product_categories') }}">
                                <i class="material-icons">category</i>
                                <span>@lang('global.product-categories.title')</span>
                            </a>
                        </li>@endcan
                        
                        @can('products_transfer_access')
                        <li class="{{ ( $request->segment(2) == 'products_transfers' ) ? 'active' : '' }}">
                            <a href="{{ route('admin.products_transfers.index') }}">
                                <i class="material-icons">transfer_within_a_station</i>
                                <span>@lang('global.products-transfer.title')</span>
                            </a>
                        </li>@endcan
                        
                        
                        
                        @can('brand_access')
                        <li class="{{ ( in_array($controller, array('BrandsController') ) && in_array($action, array('index', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                            <a href="{{ route('admin.brands.index') }}">
                                <i class="material-icons">branding_watermark</i>
                                <span>@lang('global.brands.title')</span>
                            </a>
                        </li>@endcan

                        @can('measurement_unit_access')
                        <li class="{{ ( in_array($controller, array('MeasurementUnitsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                            <a href="{{ route('admin.measurement_units.index') }}">
                                <i class="material-icons">adjust</i>
                                <span>@lang('global.measurement-units.title')</span>
                            </a>
                        </li>@endcan

                        @can('warehouse_access')
                        <li class="{{ ( in_array($controller, array('WarehousesController') ) && in_array($action, array('index', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                            <a href="{{ route('admin.warehouses.index') }}">
                                <i class="material-icons">language</i>
                                <span>@lang('global.warehouses.title')</span>
                            </a>
                        </li>@endcan
                        
                    </ul>
                </li>@endcan
               @endif

               @if( isPluginActive('purchase_order') )
                @can('purchase_order_access')
                <li class="{{ ( $request->segment(2) == 'purchase_orders' ) ? 'active' : '' }}">
                    <a href="{{ route('admin.purchase_orders.index') }}">
                        <i class="material-icons">add_shopping_cart</i>
                        <span>@lang('global.purchase-orders.title')</span>
                    </a>
                </li>@endcan
            @endif

            @if( Gate::allows('contact_access') 
        && Gate::allows('contact_create') 
        && Gate::allows('contact_company_access') 
        && Gate::allows('country_access') 
        && Gate::allows('contact_group_access') 
        && Gate::allows('contact_type_access') 
        && Gate::allows('contact_note_access') 
        && Gate::allows('contact_document_access') 
        && Gate::allows('contact_mailchimp_email_campaigns') 
        ) 
            <!-- start contact management -->
            <!-- contacts -->
            <li class="header">@lang('custom.menu.crm')</li>
               <li class="{{ ( in_array($controller, array('ContactsController', 'ContactCompaniesController', 'ContactGroupsController', 'ContactTypesController', 'ContactNotesController', 'ContactDocumentsController', 'CountriesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">contact_phone</i>
                    <span>@lang('global.contact-management.title')</span>
                    
                </a>

                
                <ul class="ml-menu">
                    @can('contact_access')
                    <li class="{{ ( in_array($controller, array('ContactsController') ) && in_array($action, array('index', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.contacts.index') }}">
                            <i class="material-icons">add_circle</i>
                            <span>@lang('global.contacts.title')</span>
                        </a>
                    </li>
                     @endcan
                    @can('contact_create')
                    <li class="{{ ( in_array($controller, array('ContactsController') ) && in_array($action, array('create') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.contacts.create') }}">
                            <i class="material-icons">add_box</i>
                            <span>@lang('custom.menu.create-contact')</span>
                        </a>
                    </li>
                    @endcan
                    @can('contact_company_access')
                    <li class="{{ ( in_array($controller, array('ContactCompaniesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.contact_companies.index') }}">
                            <i class="material-icons">view_comfy</i>
                            <span>@lang('global.contact-companies.title')</span>
                        </a>
                    </li>@endcan
                    @can('country_access')
                    <li class="{{ ( in_array($controller, array('CountriesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.countries.index') }}">
                            <i class="material-icons">language</i>
                            <span>@lang('global.countries.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_group_access')
                    <li class="{{ ( $request->segment(2) == 'contact_groups' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.contact_groups.index') }}">
                            <i class="material-icons">contacts</i>
                            <span>@lang('global.contact-groups.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_type_access')
                    <li class="{{ ( $request->segment(2) == 'contact_types' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.contact_types.index') }}">
                            <i class="material-icons">perm_contact_calendar</i>
                            <span>@lang('global.contact-types.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_note_access')
                    <li class="{{ ( $request->segment(2) == 'contact_notes') ? 'active' : '' }}">
                        <a href="{{ route('admin.contact_notes.index') }}">
                            <i class="material-icons">import_contacts</i>
                            <span>@lang('global.contact-notes.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_document_access')
                    <li class="{{ ( $request->segment(2) == 'contact_documents' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.contact_documents.index') }}">
                            <i class="material-icons">file_copy</i>
                            <span>@lang('global.contact-documents.title')</span>
                        </a>
                    </li>@endcan
                    @can('contact_mailchimp_email_campaigns')
                    <li>
                        <a href="{{ route('admin.contacts.mailchimp-email-campaigns') }}">
                            <i class="material-icons">email</i>
                            <span>@lang('global.contacts.mailchimp-email-campaigns')</span>
                        </a>
                    </li>@endcan
                </ul>
            </li>
           @endif 

           @if( isPluginActive('user') )
          @can('user_management_access')
            <li class="{{ ( in_array( $request->segment(2), array( 'permissions', 'roles', 'users', 'user_actions', 'departments' ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">supervisor_account</i>
                    <span>@lang('global.user-management.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @if( isEnable('debug') )
                            @can('permission_access')
                            <li class="{{ ( $request->segment(2) == 'permissions' ) ? 'active' : '' }}">
                                <a href="{{ url('admin/permissions') }}">
                                    <i class="material-icons">business_center</i>
                                    <span>@lang('global.permissions.title')</span>
                                </a>
                            </li>@endcan
                    @endif
                    
                    @can('role_access')
                    <li class="{{ ( $request->segment(2) == 'roles' ) ? 'active' : '' }}">
                        <a href="{{ url('admin/roles') }}">
                            <i class="material-icons">security</i>
                            <span>@lang('global.roles.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('user_access')
                    <li class="{{ ( $request->segment(2) == 'users' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i class="material-icons">people</i>
                            <span>@lang('global.users.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('user_action_access')
                    <li class="{{ ( $request->segment(2) == 'user_actions' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.user_actions.index') }}">
                            <i class="material-icons">accessible_forward</i>
                            <span>@lang('global.user-actions.title')</span>
                        </a>
                    </li>@endcan

           

                    
                    @can('department_access')
                    <li class="{{ ( $request->segment(2) == 'departments' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.departments.index') }}">
                            <i class="material-icons">domain</i>
                            <span>@lang('global.departments.title')</span>
                        </a>
                    </li>@endcan
                    
                    
                </ul>
            </li>@endcan
            @endif

            @if( isPluginActive('lead') )
                @can('contact_access')
                <li class="{{ ( $request->segment(2) == 'user_actions' ) ? 'active' : '' }}">
                    <a href="{{ route('admin.list_contacts.index', [ 'type' => 'contact_type', 'type_id' => LEADS_TYPE ]) }}">
                        <i class="material-icons">accessibility</i>
                        <span>@lang('global.contacts.title_leads')</span>
                    </a>
                </li>
                @endcan
            @endif

            <!-- client projects -->
        @if( isPluginActive('client_project') )
            @can('project_access')
            <li class="header">@lang('custom.menu.project')</li>
            <li class="{{ ( in_array( $request->segment(2), array( 'client_projects', 'project_billing_types', 'project_statuses' ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">assignment</i>
                    <span>@lang('global.projects.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('client_project_access')
                    <li class="{{ ( $request->segment(2) == 'client_projects' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.client_projects.index') }}">
                            <i class="material-icons">assessment</i>
                            <span>@lang('global.client-projects.title')</span>
                        </a>
                    </li>@endcan


                       @can('project_status_access')
                    <li class="{{ ( $request->segment(2) == 'project_statuses' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.project_statuses.index') }}">
                            <i class="material-icons">trending_up</i>
                            <span>@lang('global.project-statuses.title')</span>
                        </a>
                    </li>@endcan

                   @if( isEnable('debug') )    
                        @can('project_billing_type_access')
                        <li class="{{ ( $request->segment(2) == 'project_billing_types' ) ? 'active' : '' }}">
                            <a href="{{ route('admin.project_billing_types.index') }}">
                                <i class="material-icons">money</i>
                                <span>@lang('global.project-billing-types.title')</span>
                            </a>
                        </li>@endcan
                    @endif

                    @if( isEnable('debug') )    
                    @can('project_tab_access')
                    <li class="{{ ( $request->segment(2) == 'project_statuses' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.project_tabs.index') }}">
                            <i class="material-icons">tab</i>
                            <span>@lang('global.project-tabs.title')</span>
                        </a>
                    </li>@endcan
                    @endif




                    
                </ul>
            </li>@endcan
        @endif
            <!-- end client projects -->

         @if( isPluginActive('account') )
            @can('expense_management_access')
            <li class="header">@lang('custom.menu.balance')</li>
            <li class="{{ ( in_array( $request->segment(2), array( 'incomes', 'expenses', 'expense_categories', 'income_categories', 'monthly_reports', 'transfers', 'accounts' ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">credit_card</i>
                    <span>@lang('global.expense-management.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('income_access')
                    <li class="{{ ( $request->segment(2) == 'incomes' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.incomes.index') }}">
                            <i class="material-icons">arrow_forward</i>
                            <span>@lang('global.income.title-incomes')</span>
                        </a>
                    </li>@endcan
                    
                    @can('expense_access')
                    <li class="{{ ( $request->segment(2) == 'expenses' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.expenses.index') }}">
                            <i class="material-icons">arrow_back</i>
                            <span>@lang('global.expense.title')</span>
                        </a>
                    </li>@endcan

                     @can('income_category_access')
                    <li class="{{ ( $request->segment(2) == 'income_categories' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.income_categories.index') }}">
                            <i class="material-icons">input</i>
                            <span>@lang('global.income-category.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('expense_category_access')
                    <li class="{{ ( $request->segment(2) == 'expense_categories' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.expense_categories.index') }}">
                            <i class="material-icons">account_balance_wallet</i>
                            <span>@lang('global.expense-category.title')</span>
                        </a>
                    </li>@endcan
                    
                    
                    @can('monthly_report_access')
                    <li class="{{ ( $request->segment(2) == 'monthly_reports' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.monthly_reports.index') }}">
                            <i class="material-icons">pie_chart</i>
                            <span>@lang('global.monthly-report.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('transfer_access')
                    <li class="{{ ( $request->segment(2) == 'transfers' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.transfers.index') }}">
                            <i class="material-icons">account_balance</i>
                            <span>@lang('global.transfers.title')</span>
                        </a>
                    </li>@endcan                   
                    

                    @can('account_access')
                    <li class="{{ ( $request->segment(2) == 'accounts' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.accounts.index') }}">
                            <i class="material-icons">business_center</i>
                            <span>@lang('global.accounts.title')</span>
                        </a>
                    </li>@endcan
                    
                </ul>
            </li>@endcan 
        @endif

        @if( isPluginActive('order') )
            @can('order_access')
            <li class="{{ ( in_array( $request->segment(2), array( 'orders' ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">add_shopping_cart</i>
                    <span>@lang('orders::global.orders.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('order_access')
                    <li class="{{ ( $request->segment(2) == 'orders' && empty( $request->segment(3) ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}">
                            <i class="material-icons">view_list</i>
                            <span>@lang('orders::global.orders.list')</span>
                        </a>
                    </li>@endcan
                    @can('order_create')
                    <li class="{{ ( $request->segment(2) == 'orders' && $request->segment(3) == 'create' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.create') }}">
                            <i class="material-icons">create_new_folder</i>
                            <span>@lang('orders::global.orders.place-new-order')</span>
                        </a>
                    </li>@endcan
                </ul>
            </li>            
            @endcan   
        @endif 

        <li class="header">@lang('custom.menu.miscellaneous')</li>
            @can('task_management_access')
            <li class="{{ ( in_array( $request->segment(2), array( 'tasks', 'task_statuses', 'task_tags', 'task_calendars', 'calendartasks' ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">chrome_reader_mode</i>
                    <span>@lang('global.task-management.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('task_access')
                    <li class="{{ ( $request->segment(2) == 'tasks' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.tasks.index') }}">
                            <i class="material-icons">list</i>
                            <span>@lang('global.tasks.title')</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('task_status_access')
                    <li class="{{ ( $request->segment(2) == 'task_statuses') ? 'active' : '' }}">
                        <a href="{{ route('admin.task_statuses.index') }}">
                            <i class="material-icons">hourglass_full</i>
                            <span>@lang('global.task-statuses.title')</span>
                        </a>
                    </li>@endcan
                    
                
                    
                    @can('task_calendar_access')
                    <li class="{{ ( $request->segment(2) == 'task_calendars' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.task_calendars.index') }}">
                            <i class="material-icons">calendar_today</i>
                            <span>@lang('global.task-calendar.title')</span>
                        </a>
                    </li>@endcan

                    @can('task_calendar_access')
                    <li class="{{ ( $request->segment(2) == 'calendartasks' && $request->segment(3) == 'tasksstatuses' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.calendartasks.calendar.taskstatus') }}">
                            <i class="material-icons">vertical_split</i>
                            <span>@lang('global.task-calendar.status-wise')</span>
                        </a>
                    </li>@endcan

                    
                    
                </ul>
            </li>@endcan

            @can('assets_management_access')
            <li class="{{ ( in_array($controller, array('AssetsController', 'AssetsCategoriesController', 'AssetsLocationsController', 'AssetsStatusesController', 'AssetsHistoriesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">assignment_turned_in</i>
                    <span>@lang('global.assets-management.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('asset_access')
                    <li class="{{ ( in_array($controller, array('AssetsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.assets.index') }}">
                            <i class="material-icons">assignment</i>
                            <span>@lang('global.assets.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('assets_category_access')
                    <li class="{{ ( in_array($controller, array('AssetsCategoriesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.assets_categories.index') }}">
                            <i class="material-icons">category</i>
                            <span>@lang('global.assets-categories.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('assets_location_access')
                    <li class="{{ ( in_array($controller, array('AssetsLocationsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.assets_locations.index') }}">
                            <i class="material-icons">location_on</i>
                            <span>@lang('global.assets-locations.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('assets_status_access')
                    <li class="{{ ( in_array($controller, array('AssetsStatusesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.assets_statuses.index') }}">
                            <i class="material-icons">hourglass_full</i>
                            <span>@lang('global.assets-statuses.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('assets_history_access')
                    <li class="{{ ( in_array($controller, array('AssetsHistoriesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'uploadDocuments') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.assets_histories.index') }}">
                            <i class="material-icons">history</i>
                            <span>@lang('global.assets-history.title')</span>
                        </a>
                    </li>@endcan
                    
                </ul>
            </li>
            @endcan



            @can('internal_notification_access')
            
            <li class="{{ ( in_array( $request->segment(1), array( 'internal_notifications' ) ) || ( in_array($controller, array('SendSmsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">notifications</i><span>@lang('global.internal-notifications.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('internal_notification_access')
                    <li class="{{ ( $request->segment(2) == 'internal_notifications' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.internal_notifications.index') }}">
                            <i class="material-icons">notifications</i><span>@lang('global.internal-notifications.title')</span>
                        </a>
                    </li>
                    @endcan

                    @if( File::exists(config('modules.paths.modules') . '/Sendsms') && Module::find('sendsms')->active && isPluginActive('Sendsms'))
                        @can('send_sm_access')
                        <li class="{{ ( in_array($controller, array('SendSmsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                            <a href="{{ route('admin.send_sms.index') }}">
                                <i class="material-icons">sms</i><span>@lang('sendsms::global.send-sms.title')</span>
                            </a>
                        </li>
                        @endcan
                    @endif
                    
                    
                </ul>
            </li>@endcan

         <!-- knowledgebase_access -->

            @can('knowledgebase_access')
            <li class="{{ ( in_array( $request->segment(1), array( 'tickets' ) ) || in_array( $request->segment(2), array(  'articles', 'faq_questions', 'faq_categories' ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">battery_charging_full</i>
                    <span>@lang('global.knowledgebase.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('support_access')
                    <li class="{{ ( $request->segment(1) == 'tickets' ) ? 'active' : '' }}">
                        <a href="{{ route('tickets.index') }}">
                            <i class="material-icons">phone_in_talk</i>
                            <span>@lang('global.support.title')</span>
                        </a>
                    </li>
                    @endcan



                   
                    @can('faq_management_access')
                    <li class="{{ ( $request->segment(2) == 'faq_questions' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.faq_questions.index') }}">
                            <i class="material-icons">contact_support</i>
                            <span>@lang('global.faq-management.faq')</span>
                        </a>
                    </li>
                    @endcan
                    @can('faq_category_access')
                    <li class="{{ ( $request->segment(2) == 'faq_categories' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.faq_categories.index') }}">
                            <i class="material-icons">category</i>
                            <span>@lang('global.faq-categories.title')</span>
                        </a>
                    </li>
                    @endcan
                    
                </ul>
            </li>@endcan
             <!-- end knowledgebase_access  -->

                    <!-- content management -->
              @if( isPluginActive( ['content_management', 'article'] ) )      
                  @can('content_management_access')
            <li class="{{ ( in_array( $request->segment(2), array( 'content_categories', 'content_tags', 'content_pages' ) ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">ballot</i>
                    <span>@lang('global.content-management.title')</span>
                    
                </a>
                <ul class="ml-menu">
                   @if( isPluginActive( 'content_management' ) )  
                    @can('content_category_access')

                    <li class="{{ ( $request->segment(2) == 'content_categories' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.content_categories.index') }}">
                            <i class="material-icons">view_stream</i>
                            <span>@lang('global.content-categories.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('content_tag_access')
                    <li class="{{ ( $request->segment(2) == 'content_tags' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.content_tags.index') }}">
                            <i class="material-icons">label</i>
                            <span>@lang('global.content-tags.title')</span>
                        </a>
                    </li>@endcan


                    @can('content_page_access')
                    <li class="{{ ( $request->segment(2) == 'content_pages' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.content_pages.index') }}">
                            <i class="material-icons">pages</i>
                            <span>@lang('global.content-pages.title')</span>
                        </a>
                    </li>@endcan

                   @endif 
                      
                    @if( isPluginActive( 'article' ) )  
                       @can('article_access')
                    <li class="{{ ( $request->segment(2) == 'articles' ) ? 'active' : '' }}">
                        <a href="{{ route('admin.articles.index') }}">
                            <i class="material-icons">view_array</i>
                            <span>@lang('global.articles.title')</span>
                        </a>
                    </li>@endcan
                    @endif
                    
                </ul>
            </li>
            @endcan
            @endif   

             @can('global_setting_access')
            <li class="{{ ( in_array($controller, array('MasterSettingsController', 'GeneralSettingsController', 'CurrenciesController', 'TemplatesController', 'SmstemplatesController', 'CompaniesController', 'PaymentGatewaysController', 'TaxesController', 'DiscountsController', 'Controller', 'DatabaseBackupsController', 'NavigationMenuesController', 'SiteThemesController', 'LanguagesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy', 'viewSettings', 'addSubSettings', 'getIndex') ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">perm_data_setting</i>
                    <span>@lang('global.global-settings.title')</span>
                    
                </a>
                <ul class="ml-menu">
                    @can('master_setting_access')
                    <li class="{{ ( in_array($controller, array('MasterSettingsController', 'GeneralSettingsController') ) && in_array($action, array('index', 'edit', 'show', 'destroy', 'viewSettings', 'addSubSettings') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.master_settings.index') }}">
                            <i class="material-icons">settings_applications</i>
                            <span>@lang('global.master-settings.title')</span>
                        </a>
                    </li>@endcan

                     @can('dynamic_option_access')
                    <li class="{{ ( in_array($controller, array('DynamicOptionsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.dynamic_options.index') }}">
                            <i class="material-icons">settings_overscan</i>
                            <span>@lang('global.dynamic-options.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('currency_access')
                    <li class="{{ ( in_array($controller, array('CurrenciesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.currencies.index') }}">
                            <i class="material-icons">attach_money</i>
                            <span>@lang('global.currencies.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('template_access')
                    <li class="{{ ( in_array($controller, array('TemplatesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.templates.index') }}">
                            <i class="material-icons">view_module</i>
                            <span>@lang('templates::global.templates.email-templates')</span>
                        </a>
                    </li>@endcan

                    @can('smstemplate_access')
                    <li class="{{ ( in_array($controller, array('SmstemplatesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.smstemplates.index') }}">
                            <i class="material-icons">textsms</i>
                            <span>@lang('smstemplates::global.smstemplates.title')</span>
                        </a>
                    </li>@endcan    
                    
                    
                    @can('payment_gateway_access')
                    <li class="{{ ( in_array($controller, array('PaymentGatewaysController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.payment_gateways.index') }}">
                            <i class="material-icons">payment</i>
                            <span>@lang('global.payment-gateways.title')</span>
                        </a>
                    </li>@endcan
                    
                    
                    
                    @can('tax_access')
                    <li class="{{ ( in_array($controller, array('TaxesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.taxes.index') }}">
                            <i class="material-icons">settings_overscan</i>
                            <span>@lang('global.taxes.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('discount_access')
                    <li class="{{ ( in_array($controller, array('DiscountsController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.discounts.index') }}">
                            <i class="material-icons">attach_money</i>
                            <span>@lang('global.discounts.title')</span>
                        </a>
                    </li>@endcan
                                       
                   
                    @can('translation_manager')
                    <li class="{{ ( in_array($controller, array('Controller') ) && in_array($action, array('getIndex', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ URL_TRANSLATIONS }}">
                            <i class="material-icons">translate</i>
                            <span>@lang('custom.translations.title')</span>
                        </a>
                    </li>@endcan

                    @can('language_access')
                    <li class="{{ ( in_array($controller, array('LanguagesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.languages.index') }}">
                            <i class="material-icons">language</i>
                            <span>@lang('global.languages.title')</span>
                        </a>
                    </li>@endcan
                                        
                    

                    @can('database_backup_access')
                    <li class="{{ ( in_array($controller, array('DatabaseBackupsController') ) && in_array($action, array('index', 'create', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.database_backups.index') }}">
                            <i class="material-icons">backup</i>
                            <span>@lang('global.database-backup.title')</span>
                        </a>
                    </li>@endcan



                    @can('site_theme_access')
                    <li class="{{ ( in_array($controller, array('SiteThemesController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.site_themes.index') }}">
                            <i class="material-icons">color_lens</i>
                            <span>@lang('sitethemes::global.site-themes.title')</span>
                        </a>
                    </li>@endcan

                    <?php
                    echo $value = Eventy::filter('global_settings.menu', '');
                    ?>

                       @can('widget_access')
                    <li class="{{ ( in_array($controller, array('HomeController') ) && in_array($action, array('index', 'create', 'edit', 'show', 'destroy') ) ) ? 'active' : '' }}">
                        <a href="{{ route('admin.home.dashboard-widgets') }}">
                            <i class="material-icons">widgets</i>
                            <span>@lang('global.dashboard-widgets.title')</span>
                        </a>
                    </li>@endcan
                    
                </ul>
            </li>@endcan
            
            
            
            @can('reports_access')
            <li class="{{ ( in_array($controller, array('ReportsController') ) ) ? 'active' : '' }}">
                <a href="#" class="menu-toggle">
                    <i class="material-icons">report</i>
                    <span class="title">@lang('custom.reports.generated-reports')</span>
                    
                </a>
                <ul class="ml-menu">

                     @can('reports_income_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('incomeReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/income-report') }}">
                            <i class="material-icons">show_chart</i>
                            <span class="title">@lang('custom.reports.income-report')</span>
                        </a>
                    </li>
                     @endcan

                   @can('reports_expense_access')
                   <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('expenseReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/expense-report') }}">
                            <i class="material-icons">bar_chart</i>
                            <span class="title">@lang('custom.reports.expense-report')</span>
                        </a>
                    </li>
                    @endcan                    
                   

                    @can('reports_users_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('usersReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/users-report') }}">
                            <i class="material-icons">table_chart</i>
                            <span class="title">@lang('custom.reports.users-report')</span>
                        </a>
                    </li>
                     @endcan

                       @can('reports_users_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('rolesUsersReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/roles-users-report') }}">
                            <i class="material-icons">graphic_eq</i>
                            <span class="title">@lang('others.reports.users-roles-report')</span>
                        </a>
                    </li>
                     @endcan

                    @can('reports_projects_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('contactsProjectsReports') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/contacts-projects-reports') }}">
                            <i class="material-icons">insert_chart</i>
                            <span class="title">@lang('custom.reports.projects-report')</span>
                        </a>
                    </li>
                     @endcan

                    @can('reports_tasks_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('tasksReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/tasks-report') }}">
                            <i class="material-icons">insert_chart_outlined</i>
                            <span class="title">@lang('custom.reports.tasks-report')</span>
                        </a>
                    </li>
                     @endcan

                    @can('reports_assets_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('assetsReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/assets-report') }}">
                            <i class="material-icons">pie_chart</i>
                            <span class="title">@lang('custom.reports.assets-report')</span>
                        </a>
                    </li>
                     @endcan

                    @can('reports_products_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('productsReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/products-report') }}">
                            <i class="material-icons">bubble_chart</i>
                            <span class="title">@lang('custom.reports.products-report')</span>
                        </a>
                    </li>
                    @endcan

                    @can('reports_purchase_access')
                    <li class="{{ ( in_array($controller, array('ReportsController') ) && in_array($action, array('purchaseOrdersReport') ) ) ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports/purchase-orders-report') }}">
                            <i class="material-icons">bar_chart</i>
                            <span class="title">@lang('custom.reports.purchase-order-report')</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan


                 @can('modules_management_access')
            <li class="{{ ( in_array($controller, array('ModulesManagementsController') ) && in_array($action, array('index', 'create', 'edit', 'show') ) ) ? 'active' : '' }}">
                <a href="{{ route('admin.modules_managements.index') }}">
                    <i class="material-icons">iso</i>
                    <span>@lang('modulesmanagement::global.modules-management.title')</span>
                </a>
            </li>
            @endcan

            @if ($unread = App\MessengerTopic::countUnread())
            <li class="{{ $request->segment(2) == 'messenger' ? 'active' : '' }} {{ ($unread > 0 ? 'unread' : '') }}">
                <a href="{{ route('admin.messenger.index') }}">
                    <i class="material-icons">email</i>

                    <span>@lang('custom.app_messages')</span>
                    @if($unread > 0)
                        {{ ($unread > 0 ? '('.$unread.')' : '') }}
                    @endif
                </a>
            </li>

            @endif

            <li class="{{ $request->segment(1) == 'change_password' ? 'active' : '' }}">
                <a href="{{ route('auth.change_password') }}">
                    <i class="material-icons">vpn_key</i>
                    <span class="title">@lang('global.app_change_password')</span>
                </a>
            </li>

            <li>
                <a href="#logout" onclick="$('#logout').submit();">
                    <i class="material-icons">subdirectory_arrow_left</i>
                    <span class="title">@lang('global.app_logout')</span>
                </a>
            </li>


            @endif
        </ul>
    </div>

    @if ( ! empty( getSetting('rights_reserved','site_settings') ) )
        <!-- Footer -->
        <div class="legal">
            
            <?php
            $copyrights = getSetting('rights_reserved','site_settings');
            ?>
            <div class="copyright">{{$copyrights}}</div>
            
        </div>
        <!-- #Footer -->
        @endif
        </aside>
    </section>