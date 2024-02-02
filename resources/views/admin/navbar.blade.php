        <!-- TOP Nav Bar -->

        <div class="iq-top-navbar">
            <div class="iq-navbar-custom">
                <div class="iq-sidebar-logo">
                    <div class="top-logo">
                        <a href="" class="logo">
                            <div class="iq-light-logo">
                                <img src="/admin/images/bjlogo3.png" class="img-fluid" alt="">
                            </div>
                            <div class="iq-dark-logo">
                                <img src="/admin/images/bjlogo3.png" class="img-fluid" alt="">
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
                                    class="nav-link font-weight-bold search-box-toggle"><i
                                        class="ri-home-4-line"></i></a>
                            </li>
                            @if (session('user_permissions.invoicemodule.invoice.show') == '1')
                                <li><a href="{{ route('admin.invoice') }}" class="nav-link"> <i
                                            class="ri-file-list-line"></i></a></li>
                            @endif
                            @if (session('user_permissions.invoicemodule.product.show') == '1')
                                <li><a href="{{ route('admin.product') }}" class="nav-link"> <i
                                            class="ri-product-hunt-line"></i></a></li>
                            @endif
                            @if (session('user_permissions.invoicemodule.purchase.show') == '1')
                                <li><a href="{{ route('admin.purchase') }}"
                                        class="nav-link router-link-exact-active router-link-active"><i
                                            class="ri-bank-card-2-line"></i></a></li>
                            @endif
                        </ul>
                        <div class="iq-search-bar d-none d-md-block">
                            <form action="" class="searchbox">
                                <input type="text" name="search" class="text search-input"
                                    placeholder="Type here to search " required>
                                <a href="" class="search-link "> <i class="ri-search-line"></i> </a>
                            </form>
                        </div>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-label="Toggle navigation">
                        <i class="ri-menu-3-line"></i>
                    </button>
                    <div class="iq-menu-bt align-self-center">
                        <div class="wrapper-menu">
                            <div class="main-circle"><i class="ri-arrow-left-s-line"></i></div>
                            <div class="hover-circle"><i class="ri-arrow-right-s-line"></i></div>
                        </div>
                    </div>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto navbar-list">
                            <li class="nav-item">
                                @if (session('menu') != null)
                                    <a id="menuOption" class="search-toggle iq-waves-effect language-title"
                                        href="#">Menu<i class="ri-arrow-down-s-line"></i></a>
                                @endif
                                <div class="iq-sub-dropdown">
                                    @if (Session::has('invoice') && Session::get('invoice') == 'yes')
                                        <a class="iq-sub-card changemenu " href="#" data-value="invoice"><i
                                                class="ri-file-list-3-line"></i> Invoice</a>
                                    @endif
                                    @if (Session::has('lead') && Session::get('lead') == 'yes')
                                        <a class="iq-sub-card changemenu" href="#" data-value="lead"> <i
                                                class="ri-globe-fill"></i> Lead</a>
                                    @endif
                                    @if (Session::has('customersupport') && Session::get('customersupport') == 'yes')
                                        <a class="iq-sub-card changemenu" href="#" data-value="Customer support">
                                            <i class="ri-customer-service-fill"></i> Customer Support</a>
                                    @endif
                                </div>
                            </li>
                        </ul>
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
                                                    <h6 class="mb-0 ">My Profile</h6>
                                                    <p class="mb-0 font-size-12">View personal profile details.</p>
                                                </div>
                                            </div>
                                        </a>
                                        @if (Session::get('admin_role') != 3)
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
                                        @endif
                                        <div class="d-inline-block w-100 text-center p-3">
                                            <a style="color: white !important"
                                                class="btn btn-primary dark-btn-primary"
                                                href=" {{ route('admin.logout') }}" role="button">Sign out<i
                                                    class="ri-login-box-line ml-2"></i></a>
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
