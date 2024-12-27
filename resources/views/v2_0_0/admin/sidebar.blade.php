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
                            @if (Session::has('menu') && Session::get('menu') == 'invoice')
                                @if (session('user_permissions.invoicemodule.invoicesetting.show') == '1' ||
                                        session('user_permissions.invoicemodule.mngcol.show') == '1' ||
                                        session('user_permissions.invoicemodule.formula.show') == '1')
                                    <li
                                        class="{{ request()->routeIs('admin.invoicesettings', 'admin.invoicemanagecolumn', 'admin.invoiceformula', 'admin.invoiceothersettings')
                                            ? 'active'
                                            : '' }}">
                                        <a href="#invoicesettinginfo" class="iq-waves-effect collapsed"
                                            data-toggle="collapse" aria-expanded="false">
                                            <i class="ri-list-settings-line"></i>
                                            <span> Invoice Settings</span>
                                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                                        </a>
                                        <ul id="invoicesettinginfo" class="iq-submenu collapse"
                                            data-parent="#invoiceinfo">
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
                                                <li class="{{ request()->routeIs('admin.invoiceformula') ? 'active' : '' }}">
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
                                    class="{{ request()->routeIs('admin.quotatoin', 'admin.addquotation') ? 'active' : '' }}">
                                    <a href="#quotationinfo" class="iq-waves-effect collapsed" data-toggle="collapse"
                                        aria-expanded="false"><i
                                            class="ri ri-quill-pen-line"></i><span>Quotation</span><i
                                            class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                                    <ul id="quotationinfo" class="iq-submenu collapse"
                                        data-parent="#iq-sidebar-toggle">
                                        @if (session('user_permissions.quotationmodule.quotation.add') == '1')
                                            <li class="{{ request()->routeIs('admin.addquotation') ? 'active' : '' }}">
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
                                        class="{{ request()->routeIs('admin.quotationsettings', 'admin.quotationmanagecolumn', 'admin.quotationformula', 'admin.quotationothersettings')
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
                                            @if (session('user_permissions.quotationmodule.quotationmngcol.view') == '1')
                                                <li
                                                    class="{{ request()->routeIs('admin.quotationmanagecolumn') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.quotationmanagecolumn') }}">
                                                        <i class="ri-file-add-line"></i>
                                                        Manage Quotation Columns
                                                    </a>
                                                </li>
                                            @endif
                                            @if (session('user_permissions.quotationmodule.quotationformula.view') == '1')
                                                <li class="{{ request()->routeIs('admin.quotationformula') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.quotationformula') }}">
                                                        <i class="ri-file-list-line"></i>
                                                        Set Quotation Formula
                                                    </a>
                                                </li>
                                            @endif
                                            @if (session('user_permissions.quotationmodule.quotationsetting.view') == '1')
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
                        @elseif(Session::has('menu') && Session::get('menu') == 'account')
                            @if (session('user_permissions.accountmodule.purchase.show') == '1')
                                <li class="{{ request()->routeIs('admin.purchase') ? 'active' : '' }}">
                                    <a href="{{ route('admin.purchase') }}" class="iq-waves-effect">
                                        <i class="ri-bank-card-2-line"></i>
                                        <span>purchases</span>
                                    </a>
                                </li>
                            @endif
                        @elseif(Session::has('menu') && Session::get('menu') == 'inventory')
                            @if (session('user_permissions.inventorymodule.product.show') == '1')
                                <li class="{{ request()->routeIs('admin.product') ? 'active' : '' }}">
                                    <a href="{{ route('admin.product') }}" class="iq-waves-effect">
                                        <i class="ri-product-hunt-line"></i>
                                        <span>products</span>
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
                        @elseif (Session::has('menu') && Session::get('menu') == 'Customer support')
                            @if (session('user_permissions.customersupportmodule.customersupport.show') == '1')
                                <li class="{{ request()->routeIs('admin.customersupport') ? 'active' : '' }}">
                                    <a href="{{ route('admin.customersupport') }}" class="iq-waves-effect">
                                        <i class="ri-customer-service-fill"></i>
                                        <span>cusotmer Support</span>
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
