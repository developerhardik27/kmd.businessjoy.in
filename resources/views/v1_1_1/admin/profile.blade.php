@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterpage')

@section('style')
    <style>
        #editicon {
            top: auto !important;
            right: 8px !important;
            bottom: 10px !important;
            transition: all .3s cubic-bezier(.175, .885, .32, 1.275) !important;
            background: var(--iq-primary) !important;
            color: var(--iq-white) !important;
            border-radius: 50% !important;
            height: 30px !important;
            width: 30px !important;
            line-height: 28px !important;
            text-align: center !important;
            font-size: 16px !important;
            cursor: pointer !important;
        }

        .iq-edit-profile.nav-pills .nav-link.active,
        .iq-edit-profile.nav-pills .show>.nav-link {
            color: var(--iq-white) !important;
        }

        .password-container {
            position: relative;
        }

        /* Style for the eye icon */
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            /* Adjust color as needed */
        }
    </style>
@endsection
@section('page_title')
    {{ config('app.name') }} - user profile
@endsection

@section('page-content')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-body p-0">
                            <div class="iq-edit-list">
                                <ul class="iq-edit-profile d-flex nav nav-pills">
                                    <li class="col-md-3 p-0" id="myprofile">
                                        <a class="nav-link active" data-toggle="pill" href="#personal-information">
                                            My Profile
                                        </a>
                                    </li>
                                    <li class="col-md-3 p-0" id="changepassword">
                                        <a class="nav-link" data-toggle="pill" href="#chang-pwd">
                                            Change Password
                                        </a>
                                    </li>
                                    <li class="col-md-3 p-0" id="companyprofile">
                                        <a class="nav-link" data-toggle="pill" href="#company-profile">
                                            Company Profile
                                        </a>
                                    </li>
                                    {{-- <li class="col-md-3 p-0" id="activities">
                                        <a class="nav-link" data-toggle="pill" href="#activities-tab">
                                            Activities
                                        </a>
                                    </li> --}}
                                    <li class="col-md-3 p-0" id="othersettings">
                                        <a class="nav-link" data-toggle="pill" href="#othersettings-tab">
                                            Others
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="iq-edit-list-data">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="personal-information" role="tabpanel">
                                <div class="iq-card" id="personal_information">
                                    <div class="iq-card-header d-flex justify-content-between">
                                        <div class="iq-header-title">
                                            <h4 class="card-title">Personal Information</h4>
                                        </div>
                                        {{-- @if (session('user_permissions.adminmodule.user.edit') == '1' && session('menu') == 'admin') --}}
                                        <div>
                                            <i data-userid="{{ $id }}" id="editicon" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Edit Profile"
                                                class="ri-pencil-fill float-right"></i>
                                        </div>
                                        {{-- @endif --}}
                                    </div>
                                    <div class="iq-card-body">
                                        <div class="form-group row align-items-center">
                                            <div class="col-md-12">
                                                <div class="profile-img-edit" id="profile_img">
                                                </div>
                                            </div>
                                        </div>
                                        <div class=" row align-items-center">
                                            <div class="form-group col-sm-6">
                                                <label for="userfname">First Name:</label><br>
                                                <p id="userfname"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="userlname">Last Name:</label><br>
                                                <p id="userlname"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="uname">Email:</label><br>
                                                <p id="useremail"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="uname">Contact:</label><br>
                                                <p id="usercontact"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="cname">City:</label><br>
                                                <p id="usercity"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label>State:</label><br>
                                                <p id="userstate"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label>Country:</label><br>
                                                <p id="usercountry"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label>Pincode:</label><br>
                                                <p id="userpincode"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="iq-card d-none" id="edit_personal_information">
                                    <div class="iq-card-header d-flex justify-content-between">
                                        <div class="iq-header-title">
                                            <h4 class="card-title">Personal Information</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body">
                                        <form method="POST" id="usereditform" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group row align-items-center">
                                                <div class="col-md-12">
                                                    <div class="profile-img-edit" id="userprofile">
                                                        <div class="p-image">
                                                            <i class="ri-pencil-line upload-button"></i>
                                                            <input type="file" accept="image/*" class="file-upload"
                                                                id="img" name="img">
                                                            <span class="error-msg" id="error-img"
                                                                style="color: red"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class=" row align-items-center">
                                                <div class="form-group col-sm-6">
                                                    <input type="hidden" name="editrole" class="form-control" value=1
                                                        placeholder="token" required />
                                                    <input type="hidden" name="token" class="form-control"
                                                        value="{{ session('api_token') }}" placeholder="token" required />
                                                    <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                        class="form-control">
                                                    <input type="hidden" value="{{ session('company_id') }}"
                                                        name="company_id" class="form-control">
                                                    <label for="firstname">FirstName</label><span
                                                        style="color:red;">*</span>
                                                    <input type="text" id="firstname" name='firstname'
                                                        class="form-control" placeholder="First name" required>
                                                    <span class="error-msg" id="error-firstname"
                                                        style="color: red"></span>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="lastname">LastName</label><span
                                                        style="color:red;">*</span>
                                                    <input type="text" id="lastname" name='lastname'
                                                        class="form-control" placeholder="Last name" required>
                                                    <span class="error-msg" id="error-lastname"
                                                        style="color: red"></span>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="email">Email</label><span style="color:red;">*</span>
                                                    <input type="email" name='email' class="form-control"
                                                        id="email" value="" placeholder="Enter Email" required>
                                                    <span class="error-msg" id="error-email" style="color: red"></span>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="contact_no">Contact Number</label><span
                                                        style="color:red;">*</span>
                                                    <input type="tel" name='contact_number' class="form-control"
                                                        id="contact_no" value="" placeholder="0123456789" required>
                                                    <span class="error-msg" id="error-contact_number"
                                                        style="color: red"></span>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="country">Select Country</label><span
                                                        style="color:red;">*</span>
                                                    <select id="country" class="form-control" name='country' required>
                                                        <option selected="" disabled="">Select your Country</option>
                                                    </select>
                                                    <span class="error-msg" id="error-country" style="color: red"></span>

                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="state">Select State</label><span
                                                        style="color:red;">*</span>
                                                    <select class="form-control" name='state' id="state" required>
                                                        <option selected="" disabled="">Select your State</option>
                                                    </select>
                                                    <span class="error-msg" id="error-state" style="color: red"></span>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="city">Select City</label><span
                                                        style="color:red;">*</span>
                                                    <select class="form-control" name='city' id="city" required>
                                                        <option selected="" disabled="">Select your City</option>
                                                    </select>
                                                    <span class="error-msg" id="error-city" style="color: red"></span>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label for="pincode">Pincode</label><span style="color:red;">*</span>
                                                    <input type="text" id="pincode" name='pincode'
                                                        class="form-control" placeholder="Pin Code" required>
                                                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-row">
                                                    <div class="col-sm-12">
                                                        <button type="button" id="cancel_edit" data-toggle="tooltip"
                                                            data-placement="bottom" data-original-title="Cancel"
                                                            class="btn iq-bg-danger float-right">Cancel</button>
                                                        <button type="submit"
                                                            class="btn btn-primary float-right my-0">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="chang-pwd" role="tabpanel">
                                <div class="iq-card">
                                    <div class="iq-card-header d-flex justify-content-between">
                                        <div class="iq-header-title">
                                            <h4 class="card-title">Change Password</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body">
                                        <form method="POST" id="changepasswordform">
                                            @csrf
                                            <div class="form-group">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" placeholder="token" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ session('company_id') }}"
                                                    name="company_id" class="form-control">
                                                <label for="current_password">Current Password:</label>
                                                <div class="password-container">
                                                    <input type="Password" class="form-control" name="current_password"
                                                        id="current_password">
                                                    <i class="toggle-password fa fa-eye-slash"
                                                        onclick="togglePasswordVisibility()"></i>
                                                </div>
                                                <span class="error-msg" id="error-current_password"
                                                    style="color: red"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="new_password">New Password:</label>
                                                <input type="Password" name="new_password" class="form-control"
                                                    id="new_password">
                                                <span class="error-msg" id="error-new_password"
                                                    style="color: red"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="new_password_confirmation">Confirm Password:</label>
                                                <input type="text" class="form-control"
                                                    name="new_password_confirmation" id="new_password_confirmation">
                                                <span class="error-msg" id="error-new_password_confirmation"
                                                    style="color: red"></span>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-row">
                                                    <div class="col-sm-12">
                                                        <button type="reset" data-toggle="tooltip"
                                                            data-placement="bottom" data-original-title="Reset"
                                                            class="btn iq-bg-danger float-right">Reset</button>
                                                        <button type="submit"
                                                            class="btn btn-primary float-right my-0">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="company-profile" role="tabpanel">
                                <div class="iq-card" id="personal_information">
                                    <div class="iq-card-header d-flex justify-content-between">
                                        <div class="iq-header-title">
                                            <h4 class="card-title">Company Information</h4>
                                        </div>
                                        {{-- @if (session('user_permissions.adminmodule.company.edit') == '1' && session('menu') == 'admin')
                                            <div>
                                                <i data-companyid="{{ $company_id }}" id="editicon"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Edit Profile"
                                                    class="ri-pencil-fill float-right"></i>

                                            </div>
                                        @endif --}}
                                    </div>
                                    <div class="iq-card-body">
                                        <div class="form-group row align-items-center">
                                            <div class="col-sm-6">
                                                <div class="user-detail text-left">
                                                    <div class="user-profile" id="company_profile_img">
                                                    </div>
                                                    <div class="profile-detail mt-3">
                                                        <h3 class="d-inline-block" id="name">Company Logo</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="user-detail text-left">
                                                    <div class="user-profile" id="company_signature_img">
                                                    </div>
                                                    <div class="profile-detail mt-3">
                                                        <h3 class="d-inline-block" id="name">Signature</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class=" row align-items-center">
                                            <div class="form-group col-sm-6">
                                                <label for="companyname">Name:</label><br>
                                                <p id="companyname"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="companyemail">Email:</label><br>
                                                <p id="companyemail"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="companycontact">Contact:</label><br>
                                                <p id="companycontact"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="companyaddress">Address:</label><br>
                                                <p id="companyaddress"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="companycity">City:</label><br>
                                                <p id="companycity"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="companystate">State:</label><br>
                                                <p id="companystate"></p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="companycountry">Country:</label><br>
                                                <p id="companycountry"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="tab-pane fade" id="activities-tab" role="tabpanel">
                                <div class="iq-card">
                                    <div class="iq-card-header d-flex justify-content-between">
                                        <div class="iq-header-title">
                                            <h4 class="card-title">Activities</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body">

                                    </div>
                                </div>
                            </div> --}}
                            <div class="tab-pane fade" id="othersettings-tab" role="tabpanel">
                                <div class="iq-card">
                                    <div class="iq-card-header d-flex justify-content-between">
                                        <div class="iq-header-title">
                                            <h4 class="card-title">Other Customization</h4>
                                        </div>
                                    </div>
                                    <div class="iq-card-body">
                                        <form id="defaultpageform" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="homepage">Home Page:</label>
                                                <select name="homepage" id="homepage" class="form-control"
                                                    aria-label="Select Homepage">
                                                    <option value="index">Dashboard</option>
                                                    <option value="welcome">Welcome </option>
                                                    @if (Session::has('invoice') && Session::get('invoice') == 'yes')
                                                        <optgroup data-module="invoice" label="invoice">
                                                            @if (session('user_permissions.invoicemodule.invoice.add') == '1')
                                                                <option value="addinvoice">Create Invoice </option>
                                                            @endif
                                                            @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                                                <option value="invoice">Invoice</option>
                                                            @endif
                                                            @if (session('user_permissions.invoicemodule.bank.show') == '1')
                                                                <option value="bank">Bank Details</option>
                                                            @endif
                                                            @if (session('user_permissions.invoicemodule.customer.show') == '1')
                                                                <option value="customer">Customers </option>
                                                            @endif
                                                            @if (session('user_permissions.reportmodule.report.show') == '1')
                                                                <option value="report">Report</option>
                                                            @endif
                                                        </optgroup>
                                                    @endif
                                                    @if (Session::has('lead') && Session::get('lead') == 'yes')
                                                        <optgroup label="lead" data-module="lead">
                                                            @if (session('user_permissions.leadmodule.lead.show') == '1')
                                                                <option value="lead">lead</option>
                                                            @endif
                                                        </optgroup>
                                                    @endif
                                                    @if (Session::has('customersupport') && Session::get('customersupport') == 'yes')
                                                        <optgroup label="customersupport" data-module="customersupport">
                                                            @if (session('user_permissions.customersupportmodule.customersupport.show') == '1')
                                                                <option value="customersupport">Customer Support</option>
                                                            @endif
                                                        </optgroup>
                                                    @endif
                                                    @if (Session::has('admin') && Session::get('admin') == 'yes')
                                                        <optgroup label="admin" data-module="admin">
                                                            @if (session('user_permissions.adminmodule.company.show') == '1')
                                                                <option value="company">Company</option>
                                                            @endif
                                                            @if (session('user_permissions.adminmodule.user.show') == '1')
                                                                <option value="user">User</option>
                                                            @endif
                                                            @if (session('user_permissions.adminmodule.techsupport.show') == '1')
                                                                <option value="techsupport">Tech Support</option>
                                                            @endif
                                                        </optgroup>
                                                    @endif
                                                    @if (Session::has('account') && Session::get('account') == 'yes')
                                                        <optgroup label="account" data-module="account">
                                                            @if (session('user_permissions.accountmodule.purchase.show') == '1')
                                                                <option value="purchase">Purchase</option>
                                                            @endif
                                                        </optgroup>
                                                    @endif
                                                    @if (Session::has('inventory') && Session::get('inventory') == 'yes')
                                                        <optgroup label="inventory" data-module="inventory">
                                                            @if (session('user_permissions.inventorymodule.product.show') == '1')
                                                                <option value="product">Products</option>
                                                            @endif
                                                        </optgroup>
                                                    @endif
                                                    @if (Session::has('reminder') && Session::get('reminder') == 'yes')
                                                        <optgroup label="reminder" data-module="reminder">
                                                            @if (session('user_permissions.remindermodule.reminder.show') == '1')
                                                                <option value="reminder">Reminder</option>
                                                            @endif
                                                            @if (session('user_permissions.remindermodule.remindercustomer.show') == '1')
                                                                <option value="remindercustomer">Reminder Customer</option>
                                                            @endif
                                                        </optgroup>
                                                    @endif
                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('ajax')
    <script>
        // show and hide password
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("current_password");
            var toggleIcon = document.querySelector(".toggle-password");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            }
        }
    </script>
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            let previousValue; // store default  page 

            // get user data for user profile
            $.ajax({
                type: 'GET',
                url: '{{ route('user.profile') }}',
                data: {
                    company_id: {{ session()->get('company_id') }},
                    user_id: {{ session()->get('user_id') }},
                    token: "{{ session()->get('api_token') }}",
                    id: {{ $id }}
                },
                success: function(response) {
                    if (response.status == 200 && response.user != '') {
                        var user = response.user[0];
                        $('#userfname').text(user.firstname);
                        $('#userlname').text(user.lastname);
                        $('#useremail').text(user.email);
                        $('#usercontact').text(user.contact_no);
                        $('#usercity').text(user.city_name);
                        $('#userstate').text(user.state_name);
                        $('#usercountry').text(user.country_name);
                        $('#userpincode').text(user.pincode);
                        if (user.img != null && user.img != '') {
                            var imgElement = $('<img>').attr('src', '/uploads/' + user.img).attr(
                                'alt', 'profile-img').attr('class', 'profile-pic rounded');
                        } else {
                            var imgElement = $('<img>').attr('src', '/admin/images/imgnotfound.jpg')
                                .attr(
                                    'alt', 'profile-img').attr('class', 'profile-pic rounded');
                        }
                        $('#profile_img').append(imgElement);
                        $('#homepage').val(user.default_page);
                        previousValue = user.default_page;
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                    } else {
                        toastr.error('something went wrong !');
                    }
                    loaderhide();
                },
                error: function(xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr
                        .responseText); // Log the full error response for debugging
                    var errorMessage = "";
                    try {
                        var responseJSON = JSON.parse(xhr.responseText);
                        errorMessage = responseJSON.message || "An error occurred";
                    } catch (e) {
                        errorMessage = "An error occurred";
                    }
                    toastr.error(errorMessage);
                }
            });

            // get company data for company profile
            $.ajax({
                type: 'GET',
                url: '{{ route('company.profile') }}',
                data: {
                    user_id: {{ $id }},
                    company_id: {{ $company_id }},
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.company != '') {
                        var company = response.company[0];
                        $('#companyname').text(company.name);
                        companyemail = company.email != null ? company.email : '-';
                        $('#companyemail').text(companyemail);
                        $('#companycontact').text(company.contact_no);
                        $('#companyaddress').html(company.house_no_building_name + '</br>' + company
                            .road_name_area_colony + '</br>' + company.pincode);
                        $('#companycity').text(company.city_name);
                        $('#companystate').text(company.state_name);
                        $('#companycountry').text(company.country_name);


                        if (company.img != null && company.img != '') {
                            var imgElement = $('<img>').attr('src', '/uploads/' + company.img).attr(
                                'alt', 'profile-img').attr('class', 'avatar-130 img-fluid');
                        } else {
                            var imgElement = $('<img>').attr('src', '/admin/images/imgnotfound.jpg')
                                .attr(
                                    'alt', 'profile-img').attr('class', 'avatar-130 img-fluid');
                        }
                        $('#company_profile_img').prepend(imgElement);
                        if (company.pr_sign_img != null && company.pr_sign_img != '') {
                            var signImgElement = $('<img>').attr('src', '/uploads/' + company
                                    .pr_sign_img)
                                .attr(
                                    'alt', 'Signature-img').attr('class', 'avatar-130 img-fluid');
                        } else {
                            var signImgElement = $('<img>').attr('src', '/admin/images/imgnotfound.jpg')
                                .attr('alt', 'Signature-img').attr('class', 'avatar-130 img-fluid');
                        }
                        $('#company_signature_img').prepend(signImgElement);

                    } else if (response.status == 500) {
                        toastr.error(response.message);
                    } else {
                        toastr.error('something went wrong !');
                    }
                    loaderhide();
                },
                error: function(xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr.responseText); // Log the full error response for debugging
                    var errorMessage = "";
                    try {
                        var responseJSON = JSON.parse(xhr.responseText);
                        errorMessage = responseJSON.message || "An error occurred";
                    } catch (e) {
                        errorMessage = "An error occurred";
                    }
                    toastr.error(errorMessage);
                }
            });

            // show update user form
            $('#editicon').on('click', function() {
                loadershow();
                $('#personal_information').addClass('d-none');
                $('#edit_personal_information').removeClass('d-none');

                var edit_id = $(this).data('userid');
                // show user old data in the form input fields

                $.ajax({
                    type: 'GET',
                    url: '/api/user/search/' + edit_id,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.user != '') {
                            user = response.user.user;
                            // You can update your HTML with the data here if needed
                            $('#firstname').val(user.firstname);
                            $('#lastname').val(user.lastname);
                            $('#email').val(user.email);
                            $('#contact_no').val(user.contact_no);
                            $('#pincode').val(user.pincode);
                            var imgElement = $('<img>').attr('src', '/uploads/' + user.img).attr('alt', 'profile-pic').attr('class', 'profile-pic rounded');
                            $('#userprofile').prepend(imgElement);
                            country = user.country_id;
                            state = user.state_id;
                            city = user.city_id;
                            company = user.company_id;
                            loadcountry(country);
                            loadstate(country, state);
                            loadcity(state, city);
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error('something went wrong !');
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // hide update user form
            $('#cancel_edit').on('click', function() {
                loadershow();
                $('#personal_information').removeClass('d-none');
                $('#edit_personal_information').addClass('d-none');
                $('#userprofile .profile-pic').remove();
                $('#usereditform')[0].reset();
                loaderhide();
            });

            // show country data in dropdown and old country selected
            function loadcountry(country) {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('country.index') }}',
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.country != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.country, function(key, value) {
                                $('#country').append(
                                    `<option value='${value.id}'> ${value.country_name}</option>`
                                )
                            });
                            $('#country').val(country);
                        } else {
                            $('#country').append(`<option disabled> No Data Found</option>`)
                        }
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }

            // show country data in dropdown and old country selected
            function loadstate(country, state) {
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option  value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`)
                        }
                        $('#state').val(state);
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }

            // show state data in dropdown and old state selected
            function loadcity(state, city) {
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                        } else {
                            $('#city').append(`<option disabled> No Data Found</option>`)
                        }
                        $('#city').val(city);
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }

            // show state of selected country
            $('#country').on('change', function() {
                loadershow();
                var country = $(this).val();
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // show city of selected state
            $('#state').on('change', function() {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                var state = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                );
                            });
                        } else {
                            $('#city').append(`<option disabled>No Data Found</option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            //submit user update form
            $('#usereditform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formData = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('user.update', $id) }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#personal_information').removeClass('d-none');
                            $('#edit_personal_information').addClass('d-none');
                            $('#userprofile .profile-pic').remove();
                            $('#usereditform')[0].reset();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            toastr.error(errorMessage);
                        }
                    }
                });
            });

            //submit password update form
            $('#changepasswordform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formData = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('user.changepassword', $id) }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#changepasswordform')[0].reset();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            toastr.error(errorMessage);
                        }
                    }
                });
            });

            // change home page 
            $('#homepage').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var optgroup = selectedOption.closest('optgroup');
                var dataModule = optgroup.data('module');
                var page = selectedOption.val();

                if (confirm('Are You Want to Change Homepage?')) {
                    loadershow();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('user.setdefaultpage', $id) }}",
                        data: {
                            default_module: dataModule,
                            default_page: page,
                            token: "{{ session('api_token') }}",
                            company_id: "{{ session('company_id') }}",
                            user_id: "{{ session('user_id') }}",
                        },
                        success: function(response) {
                            // Handle the response from the server
                            if (response.status == 200) {
                                loaderhide();
                                // You can perform additional actions, such as showing a success message or redirecting the user
                                toastr.success(response.message);
                                $('#defaultpage').val(page);
                                previousValue =
                                    page // Update previousValue to the current value
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                            loaderhide();
                        },
                        error: function(xhr, status,
                            error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText
                            ); // Log the full error response for debugging
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#error-' + key).text(value[0]);
                                });
                            } else {
                                var errorMessage = "";
                                try {
                                    var responseJSON = JSON.parse(xhr.responseText);
                                    errorMessage = responseJSON.message ||
                                        "An error occurred";
                                } catch (e) {
                                    errorMessage = "An error occurred";
                                }
                                toastr.error(errorMessage);
                            }
                        }
                    });

                } else {
                    $(this).val(previousValue); // Revert to the previous value
                }
            });

        });
    </script>
@endpush
