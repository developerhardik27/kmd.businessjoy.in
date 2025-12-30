        <!-- TOP Nav Bar -->
        <div class="iq-top-navbar">
            <div class="iq-navbar-custom">
                <div class="iq-sidebar-logo">
                    <div class="top-logo">
                        <a href="" class="logo">
                            <div class="iq-light-logo">
                                <img src="{{asset('admin/images/bjlogo3.png')}}" class="img-fluid" alt="">
                            </div>
                            <div class="iq-dark-logo">
                                <img src="{{asset('admin/images/bjlogo3.png')}}" class="img-fluid" alt="">
                            </div>
                            <span>Business Joy</span>
                        </a>
                    </div>
                </div>
                <nav class="navbar navbar-expand-lg navbar-light p-0">
                    <div class="navbar-left">
                        <ul id="topbar-data-icon" class="d-flex p-0 topbar-menu-icon">
                            <li class="nav-item">
                                <a href="{{ route('admin.index') }}"
                                    class="nav-link font-weight-bold search-box-toggle">
                                    <i class="ri-home-4-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Dashboard"></i>
                                </a>
                            </li>
                            @if (session('menu') == 'invoice')
                                @if (session('user_permissions.invoicemodule.invoice.show') == '1')
                                    <li>
                                        <a href="{{ route('admin.invoice') }}" class="nav-link">
                                            <i class="ri-file-list-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Invoice List"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (session('user_permissions.invoicemodule.bank.show') == '1')
                                    <li>
                                        <a href="{{ route('admin.bank') }}" class="nav-link">
                                            <i class="ri-bank-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Bank Account List"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (session('user_permissions.invoicemodule.customer.show') == '1')
                                    <li>
                                        <a href="{{ route('admin.invoicecustomer') }}" class="nav-link">
                                            <i class="ri-group-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Customers List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif
                            @if (session('menu') == 'inventory')
                                @if (session('user_permissions.inventorymodule.product.show') == '1')
                                    <li>
                                        <a href="{{ route('admin.product') }}" class="nav-link">
                                            <i class="ri-product-hunt-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Products List"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (session('user_permissions.inventorymodule.purchase.show') == '1')
                                    <li>
                                        <a href="{{ route('admin.purchase') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-bank-card-2-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Purchases List" title="Purchases List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif  
                            @if (session('menu') == 'quotation')
                                @if (session('user_permissions.quotationmodule.quotation.show') == 1)
                                    <li>
                                        <a href="{{ route('admin.quotation') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-file-list-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Qoutation List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif 
                            @if (session('menu') == 'lead')
                                @if (session('user_permissions.leadmodule.lead.show') == 1)
                                    <li>
                                        <a href="{{ route('admin.lead') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-globe-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Leads List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif 
                            @if (session('menu') == 'Customer support')
                                @if (session('user_permissions.customersupportmodule.customersupport.show') == 1)
                                    <li>
                                        <a href="{{ route('admin.customersupport') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-customer-service-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Customer Support Complain List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif 
                            @if (session('menu') == 'admin')
                                @if (session('user_permissions.adminmodule.company.show') == 1)
                                    <li>
                                        <a href="{{ route('admin.company') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-government-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Companies List"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (session('user_permissions.adminmodule.user.show') == 1)
                                    <li>
                                        <a href="{{ route('admin.user') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-user-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Users List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif 
                            @if (session('menu') == 'reminder')
                                @if (session('user_permissions.remindermodule.reminder.show') == 1)
                                    <li>
                                        <a href="{{ route('admin.reminder') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-alarm-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Reminders List"></i>
                                        </a>
                                    </li>
                                @endif
                                @if (session('user_permissions.remindermodule.remindercustomer.show') == 1)
                                    <li>
                                        <a href="{{ route('admin.remindercustomer') }}"
                                            class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-customer-service-2-line" data-toggle="tooltip" data-placement="bottom" data-original-title="Customers List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif
                            @if (session('menu') == 'logistic')
                                @if (session('user_permissions.logisticmodule.consignorcopy.show') == '1')
                                    <li>
                                        <a href="{{ route('admin.consignorcopy') }}" class="nav-link router-link-exact-active router-link-active">
                                            <i class="ri-clipboard-line"  data-toggle="tooltip" data-placement="bottom" data-original-title="LR Copy List"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                        <div class="iq-search-bar d-none d-md-block">
                            <form action="" class="searchbox">
                                <input type="text" name="search" class="text search-input"
                                    placeholder="Type here to search {{ session('menu') }} data" required>
                                <a href="" class="search-link "> <i class="ri-search-line"></i> </a>
                            </form>
                        </div>
                    </div>
                    @if (count(session('navmanu')) > 1)
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-label="Toggle navigation">
                            <i class="ri-menu-3-line"></i>
                        </button>
                    @endif
                    <div class="iq-menu-bt align-self-center">
                        <div class="wrapper-menu">
                            <div class="main-circle">
                                <i class="ri-menu-fill" data-toggle="tooltip" data-placement="bottom" data-original-title="Menu"></i>
                            </div>
                            <div class="hover-circle">
                                <i class="ri-menu-fill" data-toggle="tooltip" data-placement="bottom" data-original-title="Menu"></i>
                            </div>
                        </div>
                    </div>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        @if (count(session('navmanu')) > 1)
                            <ul class="navbar-nav ml-auto navbar-list">
                                <li class="nav-item">
                                    @if (session('menu') != null)
                                        <a id="menuOption" class="search-toggle iq-waves-effect language-title"
                                            href="#">Menu<i class="ri-arrow-down-s-line"></i></a>
                                    @endif
                                    <div class="iq-sub-dropdown">
                                        @if (Session::has('invoice') && Session::get('invoice') == 'yes')
                                            <a class="iq-sub-card changemenu " href="#" data-value="invoice">
                                                <i class="ri-file-list-3-line"></i> Invoice
                                            </a>
                                        @endif
                                        @if (Session::has('quotation') && Session::get('quotation') == 'yes')
                                            <a class="iq-sub-card changemenu " href="#" data-value="quotation"><i
                                                class="ri ri-clipboard-line"></i> Quotation</a>
                                        @endif
                                        @if (Session::has('lead') && Session::get('lead') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="lead"> <i
                                                    class="ri-globe-line"></i> Lead</a>
                                        @endif
                                        @if (Session::has('customersupport') && Session::get('customersupport') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="Customer support">
                                                <i class="ri-customer-service-line"></i> Customer Support</a>
                                        @endif
                                        @if (Session::has('admin') && Session::get('admin') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="admin">
                                                <i class="ri-admin-line"></i> Admin</a>
                                        @endif
                                        @if (Session::has('account') && Session::get('account') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="account">
                                                <i class="ri-calculator-line"></i> Account</a>
                                        @endif
                                        @if (Session::has('inventory') && Session::get('inventory') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="inventory">
                                                <i class="ri-list-check-2"></i> Inventory</a>
                                        @endif
                                        @if (Session::has('reminder') && Session::get('reminder') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="reminder">
                                                <i class="ri-alarm-line"></i> Reminder</a>
                                        @endif
                                        @if (Session::has('blog') && Session::get('blog') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="blog">
                                                <i class="ri-article-line"></i> Blog</a>
                                        @endif
                                        @if (Session::has('logistic') && Session::get('logistic') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="logistic">
                                                <i class="ri-truck-line"></i> Logistic</a>
                                        @endif
                                        @if (Session::has('developer') && Session::get('developer') == 'yes')
                                            <a class="iq-sub-card changemenu" href="#" data-value="developer">
                                                <i class="ri-code-s-slash-line"></i> Developer</a>
                                        @endif
                                    </div>
                                </li>
                            </ul>
                        @endif
                    </div>
                    <ul class="navbar-list">
                        <li>
                            <a id="userimg" href="#"
                                class="search-toggle iq-waves-effect d-flex align-items-center bg-primary rounded">

                                <div class="caption">
                                    <h6 class="mb-0 line-height text-white" id="username"></h6>
                                    <span class="font-size-12 text-white" id="loggedcompanyname"></span>
                                </div>
                            </a>
                            <div class="iq-sub-dropdown iq-user-dropdown">
                                <div class="iq-card shadow-none m-0">
                                    <div class="iq-card-body p-0 ">
                                        <div class="bg-primary p-3">
                                            <h5 class="mb-0 text-white line-height" id="usernamein">Hello </h5>
                                            <span class="text-white font-size-12" id="afterclickcompanyname"></span>
                                        </div>
                                        <a href="{{ route('admin.userprofile', ['id' => Session::get('user_id')]) }}"
                                            class="iq-sub-card iq-bg-primary-hover">
                                            <div class="media align-items-center">
                                                <div class="rounded iq-card-icon iq-bg-primary">
                                                    <i class="ri-file-user-line"></i>
                                                </div>
                                                <div class="media-body ml-3">
                                                    <h6 class="mb-0 ">Profile</h6>
                                                    <p class="mb-0 font-size-12">View profile details.</p>
                                                </div>
                                            </div>
                                        </a>
                                        @if (session('user_permissions.adminmodule.loginhistory.show') == '1')
                                            <a href="{{ route('admin.myloginhistory', ['id' => Session::get('user_id')]) }}"
                                                class="iq-sub-card iq-bg-primary-hover">
                                                <div class="media align-items-center">
                                                    <div class="rounded iq-card-icon iq-bg-primary">
                                                        <i class="ri-user-shared-2-line"></i>
                                                    </div>
                                                    <div class="media-body ml-3">
                                                        <h6 class="mb-0 ">Login History</h6>
                                                        <p class="mb-0 font-size-12">View login history.</p>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        {{-- @if (session('user_permissions.adminmodule.company.show') == '1')
                                            <a href="{{ route('admin.companyprofile', ['id' => Session::get('company_id')]) }}"
                                                class="iq-sub-card iq-bg-primary-hover">
                                                <div class="media align-items-center">
                                                    <div class="rounded iq-card-icon iq-bg-primary">
                                                        <i class="ri-profile-line"></i>
                                                    </div>
                                                    <div class="media-body ml-3">
                                                        <h6 class="mb-0 ">Company Profile</h6>
                                                        <p class="mb-0 font-size-12">View company details.</p>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif --}}
                                        <div class="d-inline-block w-100 text-center p-3">
                                            @if (session('user_permissions.adminmodule.techsupport.show') == '1')
                                                <a style="color: white !important" class="btn btn-primary dark-btn-primary" href=" {{ route('admin.techsupport') }}" role="button">
                                                    My Ticket<i class="ri-question-line ml-1"></i>
                                                </a>
                                            @endif
                                            <a style="color: white !important" class="btn btn-primary dark-btn-primary" href=" {{ route('admin.logout') }}" role="button">
                                                Sign out<i class="ri-login-box-line ml-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- TOP Nav Bar END -->
