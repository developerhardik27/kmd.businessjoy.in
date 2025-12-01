<body class="sidebar-main-active right-column-fixed header-top-bgcolor">
    <!-- loader Start -->
    <div id="loader-container" class="loader-container">
        <img id="loader" class="loader-img" src="{{ asset('admin/images/BusinessJoyLoader.gif') }}" alt="Loader">
    </div>
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper blurred-content">
        <!-- Sidebar  -->
        <div class="iq-sidebar">
            <div class="iq-sidebar-logo d-flex justify-content-between">
                <a href="{{ route('admin.index') }}">
                    <div class="iq-light-logo">
                        <div class="iq-light-logo">
                            <img id="sidebar-logo-img" src="{{ asset('admin/images/favicon.png') }} " class="img-fluid"
                                alt="">
                            <img id="sidebar-logo-img2" src="{{ asset('admin/images/bjlogo3.png') }} " class="img-fluid"
                                alt="">
                        </div>
                    </div>
                    {{-- <span>Business Joy</span> --}}
                </a>
                <div class="iq-menu-bt-sidebar">
                    <div class="iq-menu-bt align-self-center">
                        <div class="wrapper-menu">
                            <div class="main-circle"><i class="ri-arrow-left-s-line"></i></div>
                            <div class="hover-circle"><i class="ri-arrow-right-s-line"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sidebar-scrollbar">
                <nav class="iq-sidebar-menu">
                    <ul id="iq-sidebar-toggle" class="iq-menu">
                        <li class="iq-menu-title"><i class="ri-subtract-line"></i><span>Home</span></li>
                        @if (session('menu') != null)
                            <li class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                                <a href=" {{ route('admin.index') }} " class="iq-waves-effect"><i
                                        class="ri-home-4-line"></i><span>Dashboard</span></a>
                            </li>
                        @endif

                        <li class="iq-menu-title"><i class="ri-subtract-line"></i><span>Apps</span></li>
                        <!-- <li><a href="todo.html" class="iq-waves-effect" aria-expanded="false"><i class="ri-chat-check-line"></i><span>Todo</span></a></li> -->

                        @if (Session::has('menu') && Session::get('menu') == 'invoice')
                            @if (session('user_permissions.invoicemodule.invoice.show') == '1')
                                <li
                                    class="{{ request()->routeIs('admin.invoice', 'admin.addinvoice') ? 'active' : '' }}">
                                    <a href="#invoiceinfo" class="iq-waves-effect collapsed" data-toggle="collapse"
                                        aria-expanded="false"><i class="ri-file-list-3-line"></i><span>Invoice</span><i
                                            class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                                    <ul id="invoiceinfo" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                        @if (session('user_permissions.invoicemodule.invoice.add') == '1')
                                            <li class="{{ request()->routeIs('admin.addinvoice') ? 'active' : '' }}">
                                                <a href="{{ route('admin.addinvoice') }}">
                                                    <i class="ri-file-add-line"></i>
                                                    Create Invoice
                                                </a>
                                            </li>
                                        @endif
                                        @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                            <li class="{{ request()->routeIs('admin.invoice') ? 'active' : '' }}">
                                                <a href="{{ route('admin.invoice') }}">
                                                    <i class="ri-file-list-line"></i>
                                                    Invoice List
                                                </a>
                                            </li>
                                        @endif 
                                    </ul>
                                </li>
                            @endif
                            @if (session('user_permissions.invoicemodule.tdsregister.show') == '1')
                                <li class="{{ request()->routeIs('admin.tdsregister') ? 'active' : '' }}">
                                    <a href="{{ route('admin.tdsregister') }}" class="iq-waves-effect">
                                        <i class="ri-file-copy-line"></i>
                                        <span>TDS Register</span>
                                    </a>
                                </li>
                            @endif
                            @if (Session::has('menu') && Session::get('menu') == 'invoice')
                                @if (session('user_permissions.invoicemodule.invoicesetting.show') == '1' ||
                                        session('user_permissions.invoicemodule.mngcol.show') == '1' ||
                                        session('user_permissions.invoicemodule.formula.show') == '1')
                                    <li
                                        class="{{ request()->routeIs(
                                            'admin.invoicesettings',
                                            'admin.invoicemanagecolumn',
                                            'admin.invoiceformula',
                                            'admin.invoiceothersettings',
                                        )
                                            ? 'active'
                                            : '' }}">
                                        <a href="#invoicesettinginfo" class="iq-waves-effect collapsed"
                                            data-toggle="collapse" aria-expanded="false">
                                            <i class="ri-list-settings-line"></i>
                                            <span> Invoice Settings</span>
                                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                                        </a>
                                        <ul id="invoicesettinginfo" class="iq-submenu collapse"
                                            data-parent="#iq-sidebar-toggle">
                                            @if (session('user_permissions.invoicemodule.mngcol.view') == '1')
                                                <li
                                                    class="{{ request()->routeIs('admin.invoicemanagecolumn') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.invoicemanagecolumn') }}">
                                                        <i class="ri-file-add-line"></i>
                                                        Manage Columns
                                                    </a>
                                                </li>
                                            @endif
                                            @if (session('user_permissions.invoicemodule.formula.view') == '1')
                                                <li
                                                    class="{{ request()->routeIs('admin.invoiceformula') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.invoiceformula') }}">
                                                        <i class="ri-file-list-line"></i>
                                                        Set Formula
                                                    </a>
                                                </li>
                                            @endif
                                            @if (session('user_permissions.invoicemodule.invoicesetting.view') == '1')
                                                <li
                                                    class="{{ request()->routeIs('admin.invoiceothersettings') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.invoiceothersettings') }}">
                                                        <i class="ri-settings-5-line"></i>
                                                        Other Setting
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif
                            @endif 
                            @if (session('user_permissions.invoicemodule.bank.show') == '1')
                                <li class="{{ request()->routeIs('admin.bank') ? 'active' : '' }}">
                                    <a href="{{ route('admin.bank') }}" class="iq-waves-effect">
                                        <i class="ri-bank-line"></i>
                                        <span>Bank Details</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.invoicemodule.customer.show') == '1')
                                <li class="{{ request()->routeIs('admin.invoicecustomer') ? 'active' : '' }}">
                                    <a href="{{ route('admin.invoicecustomer') }}" class="iq-waves-effect">
                                        <i class="ri-group-line"></i>
                                        <span>customers</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.reportmodule.report.show') == '1')
                                <li class="{{ request()->routeIs('admin.report') ? 'active' : '' }}">
                                    <a href="{{ route('admin.report') }}" class="iq-waves-effect">
                                        <i class="ri-file-copy-2-line"></i>
                                        <span>Report</span>
                                    </a>
                                </li>
                            @endif
                        @elseif (Session::has('menu') && Session::get('menu') == 'quotation')
                            @if (session('user_permissions.quotationmodule.quotation.show') == '1')
                                <li
                                    class="{{ request()->routeIs('admin.quotation', 'admin.addquotation') ? 'active' : '' }}">
                                    <a href="#quotationinfo" class="iq-waves-effect collapsed" data-toggle="collapse"
                                        aria-expanded="false"><i
                                            class="ri ri-clipboard-line"></i><span>Quotation</span><i
                                            class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                                    <ul id="quotationinfo" class="iq-submenu collapse"
                                        data-parent="#iq-sidebar-toggle">
                                        @if (session('user_permissions.quotationmodule.quotation.add') == '1')
                                            <li
                                                class="{{ request()->routeIs('admin.addquotation') ? 'active' : '' }}">
                                                <a href="{{ route('admin.addquotation') }}">
                                                    <i class="ri-file-add-line"></i>
                                                    Create Quotation
                                                </a>
                                            </li>
                                        @endif
                                        @if (session('user_permissions.quotationmodule.quotation.view') == '1')
                                            <li class="{{ request()->routeIs('admin.quotation') ? 'active' : '' }}">
                                                <a href="{{ route('admin.quotation') }}">
                                                    <i class="ri-file-list-line"></i>
                                                    Quotation List
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            @if (Session::has('menu') && Session::get('menu') == 'quotation')
                                @if (session('user_permissions.quotationmodule.quotationsetting.show') == '1' ||
                                        session('user_permissions.quotationmodule.quotationmngcol.show') == '1' ||
                                        session('user_permissions.quotationmodule.quotationformula.show') == '1')
                                    <li
                                        class="{{ request()->routeIs(
                                            'admin.quotationsettings',
                                            'admin.quotationmanagecolumn',
                                            'admin.quotationformula',
                                            'admin.quotationothersettings',
                                        )
                                            ? 'active'
                                            : '' }}">
                                        <a href="#quotationsettinginfo" class="iq-waves-effect collapsed"
                                            data-toggle="collapse" aria-expanded="false">
                                            <i class="ri-list-settings-line"></i>
                                            <span> Quotation Settings</span>
                                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                                        </a>
                                        <ul id="quotationsettinginfo" class="iq-submenu collapse"
                                            data-parent="#quotationinfo">
                                            @if (session('user_permissions.quotationmodule.quotationmngcol.show') == '1')
                                                <li
                                                    class="{{ request()->routeIs('admin.quotationmanagecolumn') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.quotationmanagecolumn') }}">
                                                        <i class="ri-file-add-line"></i>
                                                        Manage Quotation Columns
                                                    </a>
                                                </li>
                                            @endif
                                            @if (session('user_permissions.quotationmodule.quotationformula.show') == '1')
                                                <li
                                                    class="{{ request()->routeIs('admin.quotationformula') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.quotationformula') }}">
                                                        <i class="ri-file-list-line"></i>
                                                        Set Quotation Formula
                                                    </a>
                                                </li>
                                            @endif
                                            @if (session('user_permissions.quotationmodule.quotationsetting.show') == '1')
                                                <li
                                                    class="{{ request()->routeIs('admin.quotationothersettings') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.quotationothersettings') }}">
                                                        <i class="ri-settings-5-line"></i>
                                                        Quotation Other Setting
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif
                            @endif
                            @if (session('user_permissions.quotationmodule.quotationcustomer.show') == '1')
                                <li class="{{ request()->routeIs('admin.quotationcustomer') ? 'active' : '' }}">
                                    <a href="{{ route('admin.quotationcustomer') }}" class="iq-waves-effect">
                                        <i class="ri-group-line"></i>
                                        <span>customers</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Session::has('menu') && Session::get('menu') == 'lead')
                            @if (session('user_permissions.leadmodule.lead.show') == '1')
                                <li class="{{ request()->routeIs('admin.lead') ? 'active' : '' }}">
                                    <a href="{{ route('admin.lead') }}" class="iq-waves-effect">
                                        <i class="ri-globe-fill"></i>
                                        <span>lead</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.leadsettings.show') == '1')
                                <li class="{{ request()->routeIs('admin.leadsettings') ? 'active' : '' }}">
                                    <a href="{{ route('admin.leadsettings') }}" class="iq-waves-effect">
                                        <i class="ri-settings-5-line "></i>
                                        <span>Lead Settings</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.export.show') == '1')
                                <li class="{{ request()->routeIs('admin.exportleadhistory') ? 'active' : '' }}">
                                    <a href="{{ route('admin.exportleadhistory') }}" class="iq-waves-effect">
                                        <i class="ri-download-2-line"></i>
                                        <span>Export History</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.upcomingfollowup.show') == '1')
                                <li class="{{ request()->routeIs('admin.upcomingfollowup') ? 'active' : '' }}">
                                    <a href="{{ route('admin.upcomingfollowup') }}" class="iq-waves-effect">
                                        <i class="ri-notification-3-line"></i>
                                        <span>Upcoming Follow Up</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.analysis.show') == '1')
                                <li class="{{ request()->routeIs('admin.analysis') ? 'active' : '' }}">
                                    <a href="{{ route('admin.analysis') }}" class="iq-waves-effect">
                                        <i class="ri-bar-chart-box-line"></i>
                                        <span>Analysis</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.leadownerperformance.show') == '1')
                                <li class="{{ request()->routeIs('admin.leadownerperformance') ? 'active' : '' }}">
                                    <a href="{{ route('admin.leadownerperformance') }}" class="iq-waves-effect">
                                        <i class="ri-award-line"></i>
                                        <span>Lead Owner Performance</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.recentactivity.show') == '1')
                                <li class="{{ request()->routeIs('admin.recentactivity') ? 'active' : '' }}">
                                    <a href="{{ route('admin.recentactivity') }}" class="iq-waves-effect">
                                        <i class="ri-flashlight-line"></i>
                                        <span>Recent Activity</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.calendar.show') == '1')
                                <li class="{{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                                    <a href="{{ route('admin.calendar') }}" class="iq-waves-effect">
                                        <i class="ri-calendar-event-line"></i>
                                        <span>Calendar</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.leadmodule.leadapi.show') == '1')
                                <li class="{{ request()->routeIs('admin.leadapi') ? 'active' : '' }}">
                                    <a href="{{ route('admin.leadapi') }}" class="iq-waves-effect">
                                        <i class="ri-signal-tower-line"></i>
                                        <span>API</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Session::has('menu') && Session::get('menu') == 'admin')
                            @if (session('user_permissions.adminmodule.company.show') == '1')
                                <li class="{{ request()->routeIs('admin.company') ? 'active' : '' }}">
                                    <a href="{{ route('admin.company') }}" class="iq-waves-effect">
                                        <i class="ri-government-line"></i>
                                        <span>company</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.adminmodule.user.show') == '1')
                                <li class="{{ request()->routeIs('admin.user') ? 'active' : '' }}">
                                    <a href="{{ route('admin.user') }}" class="iq-waves-effect">
                                        <i class="ri-user-line"></i>
                                        <span>User</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.adminmodule.userpermission.show') == '1')
                                <li class="{{ request()->routeIs('admin.userrolepermission') ? 'active' : '' }}">
                                    <a href="{{ route('admin.userrolepermission') }}" class="iq-waves-effect">
                                        <i class="ri-user-settings-line"></i>
                                        <span>User Permission Group</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.adminmodule.techsupport.show') == '1')
                                <li class="{{ request()->routeIs('admin.techsupport') ? 'active' : '' }}">
                                    <a href="{{ route('admin.techsupport') }}" class="iq-waves-effect">
                                        <i class="ri-mail-line"></i>
                                        <span>Tech Support</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('admin_role') == 1)
                                <li class="{{ request()->routeIs('admin.versionupdate') ? 'active' : '' }}">
                                    <a href="{{ route('admin.versionupdate') }}" class="iq-waves-effect">
                                        <i class="ri-file-settings-line"></i>
                                        <span>Version Control</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Session::has('menu') && Session::get('menu') == 'inventory')
                            @if (session('user_permissions.inventorymodule.product.show') == '1')
                                <li class="{{ request()->routeIs('admin.product') ? 'active' : '' }}">
                                    <a href="{{ route('admin.product') }}" class="iq-waves-effect">
                                        <i class="ri-price-tag-3-line"></i>
                                        <span>Products</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.inventorymodule.productcategory.show') == '1')
                                <li class="{{ request()->routeIs('admin.productcategory') ? 'active' : '' }}">
                                    <a href="{{ route('admin.productcategory') }}" class="iq-waves-effect">
                                        <i class="ri-apps-line"></i>
                                        <span>Product Category</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.inventorymodule.productcolumnmapping.show') == '1')
                                <li class="{{ request()->routeIs('admin.productcolumnmapping') ? 'active' : '' }}">
                                    <a href="{{ route('admin.productcolumnmapping') }}" class="iq-waves-effect">
                                        <i class="ri-links-line"></i>
                                        <span>Product Column Mapping</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.inventorymodule.inventory.show') == '1')
                                <li class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                                    <a href="{{ route('admin.inventory') }}" class="iq-waves-effect">
                                        <i class="ri-archive-line"></i>
                                        <span>Inventory</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.inventorymodule.supplier.show') == '1')
                                <li class="{{ request()->routeIs('admin.supplier') ? 'active' : '' }}">
                                    <a href="{{ route('admin.supplier') }}" class="iq-waves-effect">
                                        <i class="ri-group-line"></i>
                                        <span>Suppliers</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.inventorymodule.purchase.show') == '1')
                                <li class="{{ request()->routeIs('admin.purchase') ? 'active' : '' }}">
                                    <a href="{{ route('admin.purchase') }}" class="iq-waves-effect">
                                        <i class="ri-bank-card-2-line"></i>
                                        <span>Purchases</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Session::has('menu') && Session::get('menu') == 'reminder')
                            @if (session('user_permissions.remindermodule.reminder.show') == '1')
                                <li class="{{ request()->routeIs('admin.reminder') ? 'active' : '' }}">
                                    <a href="{{ route('admin.reminder') }}" class="iq-waves-effect">
                                        <i class="ri-alarm-line"></i>
                                        <span>reminder</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.remindermodule.remindercustomer.show') == '1')
                                <li class="{{ request()->routeIs('admin.remindercustomer') ? 'active' : '' }}">
                                    <a href="{{ route('admin.remindercustomer') }}" class="iq-waves-effect">
                                        <i class="ri-user-3-line"></i>
                                        <span>Customer</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Session::has('menu') && Session::get('menu') == 'blog')
                            @if (session('user_permissions.blogmodule.blog.show') == '1')
                                <li class="{{ request()->routeIs('admin.blog') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blog') }}" class="iq-waves-effect">
                                        <i class="ri-article-line"></i>
                                        <span>Blog</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('admin.blogcategory') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blogcategory') }}" class="iq-waves-effect">
                                        <i class="ri-book-line"></i>
                                        <span>Category</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('admin.blogtag') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blogtag') }}" class="iq-waves-effect">
                                        <i class="ri-hashtag"></i>
                                        <span>Tag</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.blogmodule.blogsettings.show') == '1')
                                <li class="{{ request()->routeIs('admin.blogsettings') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blogsettings') }}" class="iq-waves-effect">
                                        <i class="ri-settings-2-line"></i>
                                        <span>Blog Settings</span>
                                    </a>
                                </li> 
                            @endif
                            @if (session('user_permissions.blogmodule.blogapi.show') == '1')
                                <li class="{{ request()->routeIs('admin.blogapi') ? 'active' : '' }}">
                                    <a href="{{ route('admin.blogapi') }}" class="iq-waves-effect">
                                        <i class="ri-signal-tower-line"></i>
                                        <span>API</span>
                                    </a>
                                </li>
                            @endif
                        @elseif (Session::has('menu') && Session::get('menu') == 'Customer support')
                            @if (session('user_permissions.customersupportmodule.customersupport.show') == '1')
                                <li class="{{ request()->routeIs('admin.customersupport') ? 'active' : '' }}">
                                    <a href="{{ route('admin.customersupport') }}" class="iq-waves-effect">
                                        <i class="ri-customer-service-fill"></i>
                                        <span>cusotmer Support</span>
                                    </a>
                                </li>
                            @endif
                        @elseif (Session::has('menu') && Session::get('menu') == 'logistic')
                            @if (session('user_permissions.logisticmodule.consignorcopy.show') == '1')
                                <li class="{{ request()->routeIs('admin.consignorcopy') ? 'active' : '' }}">
                                    <a href="{{ route('admin.consignorcopy') }}" class="iq-waves-effect">
                                        <i class="ri-clipboard-line"></i>
                                        <span>Consignor Copy</span>
                                    </a>
                                </li>
                            @endif 
                            {{-- currently not requirs submenu --}}
                            {{-- @if (session('user_permissions.logisticmodule.logisticsettings.show') == '1')
                                <li class="{{ request()->routeIs('admin.logisticothersettings') ? 'active' : '' }}">
                                    <a href="#logisticsettinginfo" class="iq-waves-effect collapsed"
                                        data-toggle="collapse" aria-expanded="false">
                                        <i class="ri-list-settings-line"></i>
                                        <span>Settings</span>
                                        <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                                    </a>
                                    <ul id="logisticsettinginfo" class="iq-submenu collapse"
                                        data-parent="#iq-sidebar-toggle">
                                        @if (session('user_permissions.logisticmodule.logisticsettings.show') == '1')
                                            <li
                                                class="{{ request()->routeIs('admin.logisticothersettings') ? 'active' : '' }}">
                                                <a href="{{ route('admin.logisticothersettings') }}">
                                                    <i class="ri-settings-5-line"></i>
                                                    Other Settings
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif --}}
                            @if (session('user_permissions.logisticmodule.logisticsettings.show') == '1')
                                <li class="{{ request()->routeIs('admin.logisticothersettings') ? 'active' : '' }}">
                                    <a href="{{ route('admin.logisticothersettings') }}" class="iq-waves-effect">
                                        <i class="ri-settings-5-line"></i>
                                        <span>Other Settings</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.logisticmodule.consignee.show') == '1')
                                <li class="{{ request()->routeIs('admin.consignee') ? 'active' : '' }}">
                                    <a href="{{ route('admin.consignee') }}" class="iq-waves-effect">
                                        <i class="ri-user-received-2-line"></i>
                                        <span>Consignee</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.logisticmodule.consignor.show') == '1')
                                <li class="{{ request()->routeIs('admin.consignor') ? 'active' : '' }}">
                                    <a href="{{ route('admin.consignor') }}" class="iq-waves-effect">
                                        <i class="ri-user-shared-2-line"></i>
                                        <span>Consignor</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.logisticmodule.transporterbilling.show') == '1')
                                <li class="{{ request()->routeIs('admin.transporterbilling') ? 'active' : '' }}">
                                    <a href="{{ route('admin.transporterbilling') }}" class="iq-waves-effect">
                                        <i class="ri-bill-line"></i>
                                        <span>Transporter Bills</span>
                                    </a>
                                </li>
                            @endif
                        @elseif (Session::has('menu') && Session::get('menu') == 'developer')
                            @if (session('user_permissions.developermodule.slowpage.show') == '1')
                                <li class="{{ request()->routeIs('admin.slowpages') ? 'active' : '' }}">
                                    <a href="{{ route('admin.slowpages') }}" class="iq-waves-effect">
                                        <i class="ri-clipboard-line"></i>
                                        <span>Slow Pages</span>
                                    </a>
                                </li> 
                            @endif
                            @if (session('user_permissions.developermodule.errorlog.show') == '1')     
                                <li class="{{ request()->routeIs('admin.errorlogs') ? 'active' : '' }}">
                                    <a href="{{ route('admin.errorlogs') }}" class="iq-waves-effect">
                                        <i class="ri-alert-line"></i>
                                        <span>Error Logs</span>
                                    </a>
                                </li> 
                            @endif
                            @if (session('user_permissions.developermodule.cronjob.show') == '1')  
                                <li class="{{ request()->routeIs('admin.cronjobs') ? 'active' : '' }}">
                                    <a href="{{ route('admin.cronjobs') }}" class="iq-waves-effect">
                                        <i class="ri-calendar-line"></i>
                                        <span>Cron Jobs</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.developermodule.techdoc.show') == '1')  
                                <li class="{{ request()->routeIs('admin.techdocs') ? 'active' : '' }}">
                                    <a href="{{ route('admin.techdocs') }}" class="iq-waves-effect">
                                        <i class="ri-book-2-line"></i>
                                        <span>Technical Docs</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.developermodule.versiondoc.show') == '1')      
                                <li class="{{ request()->routeIs('admin.versiondocs') ? 'active' : '' }}">
                                    <a href="{{ route('admin.versiondocs') }}" class="iq-waves-effect">
                                        <i class="ri-stack-line"></i>
                                        <span>Version Docs</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.developermodule.recentactivitydata.show') == '1')      
                                <li class="{{ request()->routeIs('admin.recentactivitydata') ? 'active' : '' }}">
                                    <a href="{{ route('admin.recentactivitydata') }}" class="iq-waves-effect">
                                        <i class="ri-flashlight-line"></i>
                                        <span>Activity/Recent Data</span>
                                    </a>
                                </li>
                            @endif
                            @if (session('user_permissions.developermodule.cleardata.show') == '1')      
                                <li class="{{ request()->routeIs('admin.cleardata') ? 'active' : '' }}">
                                    <a href="{{ route('admin.cleardata') }}" class="iq-waves-effect">
                                        <i class="ri-delete-bin-line"></i>
                                        <span>Clear Data</span>
                                    </a>
                                </li>
                            @endif
                        @endif
                        <li>
                            <a href="{{ route('admin.logout') }}" class="iq-waves-effect">
                                <i class="ri-logout-circle-line"></i>
                                <span>logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
