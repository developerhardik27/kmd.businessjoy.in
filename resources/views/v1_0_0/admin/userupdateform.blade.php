@php
    $folder = session('folder_name');
    if (
        Session::has('user_permissions.adminmodule.company.max') &&
        session('user_permissions.adminmodule.company.max') == '1'
    ) {
        $rowspan = 3;
    } else {
        $rowspan = 1;
    }
@endphp
@extends($folder . '.admin.masterlayout')

@section('page_title')
    Update User
@endsection
@section('title')
    Update User
@endsection
@section('style')
    <style>
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

@section('form-content')
    <form id="userupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                        placeholder="company_id" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                    <label for="firstname">FirstName</label><span style="color:red;">*</span>
                    <input type="text" id="firstname" name='firstname' class="form-control" placeholder="First name"
                        required>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="lastname">LastName</label><span style="color:red;">*</span>
                    <input type="text" id="lastname" name='lastname' class="form-control" placeholder="Last name"
                        required>
                    <span class="error-msg" id="error-lastname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="email">Email</label><span style="color:red;">*</span>
                    <input type="email" name='email' class="form-control" id="email" value=""
                        placeholder="Enter Email" required>
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name='password' class="form-control"
                            id="exampleInputPassword3" value="" placeholder="update Password (not mandatory)">
                        <i class="toggle-password fa fa-eye-slash" onclick="togglePasswordVisibility()"></i>
                    </div>
                    <span class="error-msg" id="error-password" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="contact_no">Contact Number</label><span style="color:red;">*</span>
                    <input type="tel" name='contact_number' class="form-control" id="contact_no" value=""
                        placeholder="0123456789" required>
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="country">Select Country</label><span style="color:red;">*</span>
                    <select id="country" class="form-control" name='country' required>
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="state">Select State</label><span style="color:red;">*</span>
                    <select class="form-control" name='state' id="state" required>
                        <option selected="" disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="city">Select City</label><span style="color:red;">*</span>
                    <select class="form-control" name='city' id="city" required>
                        <option selected="" disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="pincode">Pincode</label><span style="color:red;">*</span>
                    <input type="text" id="pincode" name='pincode' class="form-control" placeholder="Pin Code"
                        required>
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="reset" id="formreset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset" class="btn iq-bg-danger float-right resetbtn">
                        <i class='ri-refresh-line'></i>
                    </button>
                    <button type="submit" id="formsubmit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Update" class="btn btn-primary float-right my-0 submitBtn">
                        <i class='ri-check-line'></i>
                    </button>
                </div>
            </div>
        </div>
        @if (session('user_permissions.adminmodule.userpermission.add') == '1')
            <div class="row">
                <div class="col-sm-12">
                    @if (Session::has('admin') && Session::get('admin') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Admin Modules</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right"><b>Select All </b>
                                                <input type="checkbox" id="adminallcheck" data-module="admin"
                                                    class="allcheck">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15%">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="admincheckboxes">
                                        @if (session('user_permissions.adminmodule.company.add') == '1')
                                            <tr id="company">
                                                <td rowspan="{{ $rowspan }}"> <input type="checkbox" class="clickmenu"
                                                        data-value='company' id="showcompanymenu" name="showcompanymenu"
                                                        value="1"></td>
                                                <td rowspan="{{ $rowspan }}">Company</td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.company.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcompanymenu' id="addcompany" name="addcompany"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.company.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcompanymenu' id="viewcompany" name="viewcompany"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.company.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcompanymenu' id="editcompany" name="editcompany"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.company.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcompanymenu' id="deletecompany"
                                                            name="deletecompany" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.company.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcompanymenu' id="alldatacompany"
                                                            name="alldatacompany" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            @if (Session::has('user_permissions.adminmodule.company.max') &&
                                                    session('user_permissions.adminmodule.company.max') == '1')
                                                <tr>
                                                    <td>Max Users</td>
                                                    {{-- add more option title here if needed --}}
                                                </tr>
                                                <tr id="company">
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.max') == '1')
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcompanymenu' id="maxcompany" name="maxuser"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    {{-- add more checkboxe title here if needed --}}
                                                </tr>
                                            @endif
                                        @endif
                                        @if (session('user_permissions.adminmodule.user.add') == '1')
                                            <tr id="user">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='user'
                                                        id="showusermenu" name="showusermenu" value="1">
                                                </td>
                                                <td>User</td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.user.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                            id="adduser" name="adduser" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.user.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                            id="viewuser" name="viewuser" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.user.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                            id="edituser" name="edituser" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.user.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                            id="deleteuser" name="deleteuser" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.user.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                            id="alldatauser" name="alldatauser" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if (Session::has('admin_role') && Session::get('admin_role') == 1)
                                            <tr id="techsupport">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='techsupport'
                                                        id="showtechsupportmenu" name="showtechsupportmenu" value="1">
                                                </td>
                                                <td>Tech support</td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showtechsupportmenu' id="addtechsupport"
                                                        name="addtechsupport" value="1">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showtechsupportmenu' id="viewtechsupport"
                                                        name="viewtechsupport" value="1">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showtechsupportmenu' id="edittechsupport"
                                                        name="edittechsupport" value="1">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showtechsupportmenu' id="deletetechsupport"
                                                        name="deletetechsupport" value="1">
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.user.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showtechsupportmenu' id="alldatatechsupport"
                                                            name="alldatatechsupport" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if (session('user_permissions.adminmodule.userpermission.add') == '1')
                                            <tr id="userpermission">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='userpermission'
                                                        id="showuserpermissionmenu" name="showuserpermissionmenu"
                                                        value="1">
                                                </td>
                                                <td>User Permission</td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.userpermission.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showuserpermissionmenu' id="adduserpermission"
                                                            name="adduserpermission" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.userpermission.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showuserpermissionmenu' id="viewuserpermission"
                                                            name="viewuserpermission" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.userpermission.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showuserpermissionmenu' id="edituserpermission"
                                                            name="edituserpermission" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.userpermission.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showuserpermissionmenu' id="deleteuserpermission"
                                                            name="deleteuserpermission" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.adminmodule.userpermission.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showuserpermissionmenu' id="alldatauserpermission"
                                                            name="alldatauserpermission" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="adminmodulereset" data-module="admin"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Admin Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="adminmodulesubmit"
                                                class="btn btn-primary float-right my-0 submitBtn" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('invoice') &&
                            Session::get('invoice') == 'yes' &&
                            Session::has('showinvoicesettings') &&
                            Session::get('showinvoicesettings') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Invoice Module</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right">
                                                <b>Select All </b>
                                                <input type="checkbox" id="invoiceallcheck" data-module="invoice"
                                                    class="allcheck">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15% ;">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoicecheckboxes">
                                        @if (session('user_permissions.invoicemodule.invoice.add') == '1')
                                            <tr id="invoice">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='invoice'
                                                        id="showinvoicemenu" name="showinvoicemenu" value="1">
                                                </td>
                                                <td>Invoice</td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoice.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicemenu' id="addinvoice" name="addinvoice"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicemenu' id="viewinvoice" name="viewinvoice"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoice.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicemenu' id="editinvoice" name="editinvoice"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoice.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicemenu' id="deleteinvoice"
                                                            name="deleteinvoice" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoice.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicemenu' id="alldatainvoice"
                                                            name="alldatainvoice" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if (session('user_permissions.invoicemodule.mngcol.add') == '1')
                                            <tr id="mngcol">
                                                <td> <input type="checkbox" class="clickmenu" data-value='mngcol'
                                                        id="showmngcolmenu" name="showmngcolmenu" value="1">
                                                </td>
                                                <td>Manage Invoice Column</td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.mngcol.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showmngcolmenu' id="addmngcol" name="addmngcol"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.mngcol.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showmngcolmenu' id="viewmngcol" name="viewmngcol"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.mngcol.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showmngcolmenu' id="editmngcol" name="editmngcol"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.mngcol.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showmngcolmenu' id="deletemngcol" name="deletemngcol"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.mngcol.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showmngcolmenu' id="alldatamngcol"
                                                            name="alldatamngcol" value="1">
                                                    @else
                                                        -
                                                    @endif

                                                </td>
                                            </tr>
                                        @endif
                                        @if (session('user_permissions.invoicemodule.formula.add') == '1')
                                            <tr id="formula">
                                                <td> <input type="checkbox" class="clickmenu" data-value='formula'
                                                        id="showformulamenu" name="showformulamenu" value="1">
                                                </td>
                                                <td>Invoice Formula</td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.formula.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showformulamenu' id="addformula" name="addformula"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.formula.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showformulamenu' id="viewformula" name="viewformula"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.formula.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showformulamenu' id="editformula" name="editformula"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.formula.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showformulamenu' id="deleteformula"
                                                            name="deleteformula" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.formula.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicesettingmenu' id="alldataformula"
                                                            name="alldataformula" value="1">
                                                    @else
                                                        -
                                                    @endif

                                                </td>
                                            </tr>
                                        @endif
                                        @if (session('user_permissions.invoicemodule.invoicesetting.add') == '1')
                                            <tr id="invoicesetting">
                                                <td> <input type="checkbox" class="clickmenu" data-value='invoicesetting'
                                                        id="showinvoicesettingmenu" name="showinvoicesettingmenu"
                                                        value="1">
                                                </td>
                                                <td>Invoice/Settings</td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoicesetting.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicesettingmenu' id="addinvoicesetting"
                                                            name="addinvoicesetting" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoicesetting.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicesettingmenu' id="viewinvoicesetting"
                                                            name="viewinvoicesetting" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoicesetting.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicesettingmenu' id="editinvoicesetting"
                                                            name="editinvoicesetting" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoicesetting.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicesettingmenu' id="deleteinvoicesetting"
                                                            name="deleteinvoicesetting" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.invoicesetting.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showinvoicesettingmenu' id="alldatainvoicesetting"
                                                            name="alldatainvoicesetting" value="1">
                                                    @else
                                                        -
                                                    @endif

                                                </td>
                                            </tr>
                                        @endif
                                        @if (session('user_permissions.invoicemodule.bank.add') == '1')
                                            <tr id="bank">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='bank'
                                                        id="showbankmenu" name="showbankmenu" value="1">
                                                </td>
                                                <td>Bank</td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.bank.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                            id="addbank" name="addbank" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.bank.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                            id="viewbank" name="viewbank" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.bank.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                            id="editbank" name="editbank" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.bank.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                            id="deletebank" name="deletebank" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.bank.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                            id="alldatabank" name="alldatabank" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if (session('user_permissions.invoicemodule.customer.add') == '1')
                                            <tr id="customer">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='customer'
                                                        id="showcustomermenu" name="showcustomermenu" value="1">
                                                </td>
                                                <td>Customer</td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.customer.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomermenu' id="addcustomer" name="addcustomer"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.customer.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomermenu' id="viewcustomer"
                                                            name="viewcustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.customer.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomermenu' id="editcustomer"
                                                            name="editcustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.customer.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomermenu' id="deletecustomer"
                                                            name="deletecustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.invoicemodule.customer.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomermenu' id="alldatacustomer"
                                                            name="alldatacustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="invoicemodulereset" data-module="invoice"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Invoice Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="invoicemodulsubmit" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"
                                                class="btn btn-primary float-right my-0 submitBtn"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('lead') && Session::get('lead') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Lead Modules</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right">
                                                <b>Select All </b>
                                                <input type="checkbox" id="leadallcheck" data-module="lead"
                                                    class="allcheck">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15%">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="leadcheckboxes">
                                        @if (session('user_permissions.leadmodule.lead.add') == '1')
                                            <tr id="lead">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='lead'
                                                        id="showleadmenu" name="showleadmenu" value="1">
                                                </td>
                                                <td>Lead</td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                            id="addlead" name="addlead" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                            id="viewlead" name="viewlead" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                            id="editlead" name="editlead" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                            id="deletelead" name="deletelead" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                            id="alldatalead" name="alldatalead" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="leadmodulereset" data-module="lead"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Lead Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="leadmodulesubmit" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"
                                                class="btn btn-primary float-right my-0 submitBtn"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('customersupport') && Session::get('customersupport') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Customer Support Modules</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right">
                                                <b>Select All </b>
                                                <input type="checkbox" id="customersupportallcheck"
                                                    data-module="customersupport" class="allcheck">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15%">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customersupportcheckboxes">
                                        @if (session('user_permissions.customersupportmodule.customersupport.add') == '1')
                                            <tr id="customersupport">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='customersupport'
                                                        id="showcustomersupportmenu" name="showcustomersupportmenu"
                                                        value="1">
                                                </td>
                                                <td>Customer Support</td>
                                                <td>
                                                    @if (session('user_permissions.customersupportmodule.customersupport.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomersupportmenu' id="addcustomersupport"
                                                            name="addcustomersupport" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.customersupportmodule.customersupport.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomersupportmenu' id="viewcustomersupport"
                                                            name="viewcustomersupport" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomersupportmenu' id="editcustomersupport"
                                                            name="editcustomersupport" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.customersupportmodule.customersupport.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomersupportmenu' id="deletecustomersupport"
                                                            name="deletecustomersupport" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.customersupportmodule.customersupport.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showcustomersupportmenu' id="alldatacustomersupport"
                                                            name="alldatacustomersupport" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="customersupportmodulereset"
                                                data-module="customersupport" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Customer Support Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="customersupportmodulesubmit" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"
                                                class="btn btn-primary float-right my-0 submitBtn"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('account') && Session::get('account') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Account Modules</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right"><b>Select All </b> <input type="checkbox"
                                                    id="accountallcheck" data-module="account" class="allcheck"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15%">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="accountcheckboxes">
                                        @if (session('user_permissions.accountmodule.purchase.add') == '1')
                                            <tr id="purchase">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='purchase'
                                                        id="showpurchasemenu" name="showpurchasemenu" value="1">
                                                </td>
                                                <td>Purchase</td>
                                                <td>
                                                    @if (session('user_permissions.accountmodule.purchase.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showpurchasemenu' id="addpurchase" name="addpurchase"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.accountmodule.purchase.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showpurchasemenu' id="viewpurchase"
                                                            name="viewpurchase" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.accountmodule.purchase.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showpurchasemenu' id="editpurchase"
                                                            name="editpurchase" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.accountmodule.purchase.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showpurchasemenu' id="deletepurchase"
                                                            name="deletepurchase" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.accountmodule.purchase.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showpurchasemenu' id="alldatapurchase"
                                                            name="alldatapurchase" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="accountmodulereset" data-module="account"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Account Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="accountsubmit" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"
                                                class="btn btn-primary float-right my-0 submitBtn"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('inventory') && Session::get('inventory') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Inventory Modules</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right"><b>Select All </b> <input type="checkbox"
                                                    id="inventoryallcheck" data-module="inventory" class="allcheck"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15%">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventorycheckboxes">
                                        @if (session('user_permissions.inventorymodule.product.add') == '1')
                                            <tr id="product">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='product'
                                                        id="showproductmenu" name="showproductmenu" value="1">
                                                </td>
                                                <td>Product</td>
                                                <td>
                                                    @if (session('user_permissions.inventorymodule.product.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showproductmenu' id="addproduct" name="addproduct"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.inventorymodule.product.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showproductmenu' id="viewproduct" name="viewproduct"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.inventorymodule.product.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showproductmenu' id="editproduct" name="editproduct"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.inventorymodule.product.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showproductmenu' id="deleteproduct"
                                                            name="deleteproduct" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.inventorymodule.product.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showproductmenu' id="alldataproduct"
                                                            name="alldataproduct" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="inventorymodulereset" data-module="inventory"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Inventory Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="inventorymodulesubmit" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"
                                                class="btn btn-primary float-right my-0 submitBtn"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('reminder') && Session::get('reminder') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Reminder Modules</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right"><b>Select All </b> <input type="checkbox"
                                                    id="reminderallcheck" data-module="reminder" class="allcheck"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15%">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="remindercheckboxes">
                                        @if (session('user_permissions.remindermodule.reminder.add') == '1')
                                            <tr id="reminder">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='reminder'
                                                        id="showremindermenu" name="showremindermenu" value="1">
                                                </td>
                                                <td>Reminder</td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.reminder.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindermenu' id="addreminder" name="addreminder"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.reminder.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindermenu' id="viewreminder"
                                                            name="viewreminder" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.reminder.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindermenu' id="editreminder"
                                                            name="editreminder" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.reminder.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindermenu' id="deletereminder"
                                                            name="deletereminder" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.reminder.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindermenu' id="alldatareminder"
                                                            name="alldatareminder" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if (session('user_permissions.remindermodule.remindercustomer.add') == '1')
                                            <tr id="remindercustomer">
                                                <td>
                                                    <input type="checkbox" class="clickmenu" data-value='remindercustomer'
                                                        id="showremindercustomermenu" name="showremindercustomermenu"
                                                        value="1">
                                                </td>
                                                <td>Reminder Customer</td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.remindercustomer.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindercustomermenu' id="addremindercustomer"
                                                            name="addremindercustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.remindercustomer.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindercustomermenu' id="viewremindercustomer"
                                                            name="viewremindercustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.remindercustomer.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindercustomermenu' id="editremindercustomer"
                                                            name="editremindercustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.remindercustomer.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindercustomermenu' id="deleteremindercustomer"
                                                            name="deleteremindercustomer" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.remindermodule.remindercustomer.alldata') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showremindercustomermenu'
                                                            id="alldataremindercustomer" name="alldataremindercustomer"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="remindermodulereset" data-module="reminder"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Reminder Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="remindermodulesubmit" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"
                                                class="btn btn-primary float-right my-0 submitBtn"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('report') && Session::get('report') == 'yes')
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Report Modules</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-right"><b>Select All </b> <input
                                                    type="checkbox" id="reportallcheck" data-module="report"
                                                    class="allcheck"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="width:15%">Show/Hide</th>
                                            <th scope="col">Menus</th>
                                            <th scope="col">Add</th>
                                            <th scope="col">View</th>
                                            <th scope="col">Edit</th>
                                            <th scope="col">Delete</th>
                                            <th scope="col">All Record</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportcheckboxes">
                                        @if (session('user_permissions.reportmodule.report.add') == '1')
                                            <tr id="report">
                                                <td rowspan="3">
                                                    <input type="checkbox" class="clickmenu" data-value='report'
                                                        id="showreportmenu" name="showreportmenu" value="1">
                                                </td>
                                                <td rowspan="3">report</td>
                                                <td>
                                                    @if (session('user_permissions.reportmodule.report.add') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showreportmenu' id="addreport" name="addreport"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.reportmodule.report.view') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showreportmenu' id="viewreport" name="viewreport"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.reportmodule.report.edit') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showreportmenu' id="editreport" name="editreport"
                                                            value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.reportmodule.report.delete') == '1')
                                                        <input type="checkbox" class="clicksubmenu"
                                                            data-value='showreportmenu' id="deletereport"
                                                            name="deletereport" value="1">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <select name="assignedto[]" class="form-control multiple"
                                                        id="assignedto" multiple>
                                                        <option value="" disabled selected>Select User</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Log</td>
                                                {{-- add more option title here if needed --}}
                                            </tr>
                                            <tr id="report">
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showreportmenu' id="logreport" name="logreport"
                                                        value="1">
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="button" id="reportmodulereset" data-module="report"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset report Module"
                                                class="btn iq-bg-danger float-right resetbtn"><i
                                                    class='ri-refresh-line'></i></button>
                                            <button type="submit" id="reportmodulesubmit" data-toggle="tooltip"
                                                data-placement="bottom" data-original-title="Save"
                                                class="btn btn-primary float-right my-0 submitBtn"><i
                                                    class='ri-check-line'></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        @endif
    </form>
@endsection

@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("password");
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



            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('user.invoiceuserindex') }}',
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }



            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [userDataResponse] = await Promise.all(
                        [
                            getUserData()
                        ]);

                    // Check if user data is successfully fetched
                    if (userDataResponse.status == 200 && userDataResponse.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(userDataResponse.user, function(key, value) {
                            var optionValue = ((value.firstname != null) ? value.firstname : '') + ' ' +
                                ((value.lastname != null) ? value.lastname : '');
                            $('#assignedto').append(
                                `<option value="${value.id}">${optionValue}</option>`);
                        });
                        $('#assignedto').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (userDataResponse.status == 500) {
                        toastr.error(userDataResponse.message);
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                    }

                    loaderhide();

                    // Further code execution after successful AJAX calls and HTML appending


                } catch (error) {
                    console.error('Error:', error);
                    toastr.error("An error occurred while initializing");
                    loaderhide();
                }
            }

            initialize();
            $('#assignedto').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });


            $('#assignedto').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select User --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#assignedto').multiselect('rebuild');
            });

            var edit_id = @json($edit_id);
            // show old data in fields
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
                        var user = response.user[0];
                        var rp = JSON.parse(user.rp); // user role and permissions
                        if (rp.reportmodule) {
                            var assignedreportuser = rp.reportmodule.report.alldata;
                            $('#assignedto').find('option:disabled').remove(); // remove disabled option
                            if (assignedreportuser != 'null' && assignedreportuser != null) {
                                assignedreportuser.forEach(function(value) {
                                    $('#assignedto').multiselect('select', value);
                                });
                                $('#assignedto').multiselect('rebuild');
                            }
                        }
                        $.each(rp, function(key, value) {
                            $.each(value, function(key2, value2) {
                                $.each(value2, function(key3, value3) {
                                    if (value3 == 1) {
                                        if (key3 == "show") {
                                            $(`#show${key2}menu`).attr(
                                                'checked', true)
                                        } else {
                                            $(`#${key3}${key2}`).attr('checked',
                                                true)
                                        }
                                    }
                                    // console.log(
                                    //     `${key} => ${key2} => ${key3} : ${value3}`
                                    // );
                                });
                            });
                        });
                        // You can update your HTML with the data here if needed
                        $('#firstname').val(user.firstname);
                        $('#lastname').val(user.lastname);
                        $('#email').val(user.email);
                        $('#contact_no').val(user.contact_no);
                        $('#pincode').val(user.pincode);
                        country = user.country_id;
                        state = user.state_id;
                        city = user.city_id;
                        company = user.company_id;
                        loadcountry(country);
                        loadstate(country, state);
                        loadcity(state, city);;
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



            $(document).on('click', '.allcheck', function() {
                var module = $(this).data('module');
                if (!$(`#${module}allcheck`).prop('checked')) {
                    $(`#${module}checkboxes input[type="checkbox"]`).prop('checked', false);
                    if (module == 'report') {
                        $('#assignedto option').prop('selected', false);
                        if ($("#assignedto option:disabled").length == 0) {
                            $("#assignedto").prepend(
                                '<option value="" disabled selected>-- Select User --</option>');
                        }
                        $('#assignedto option:first').prop('selected', true);
                    }
                } else {
                    $(`#${module}checkboxes input[type="checkbox"]`).prop('checked', $(this).prop(
                        'checked'));
                    if (module == 'report') {
                        $('#reportcheckboxes input[type="checkbox"]').prop('checked', $(this).prop(
                            'checked'));
                        $('#assignedto').find('option:disabled').remove(); // remove disabled option
                        $('#assignedto option').prop('selected', true);
                    }
                }

                if (module == 'report') {
                    $('#assignedto').multiselect('refresh');
                    $('#assignedto').multiselect('rebuild');
                }
            })


            // check all checkboxes in the row if click on any menu
            $(document).on('change', '.clickmenu', function() {
                value = $(this).data('value');
                if (!$(this).prop('checked')) {
                    $(`#${value} input[type="checkbox"]`).prop('checked', false);
                    if (value == 'report') {
                        $('#assignedto option').prop('selected', false);
                        if ($("#assignedto option:disabled").length == 0) {
                            $("#assignedto").prepend(
                                '<option value="" disabled selected>-- Select User --</option>');
                        }
                        $('#assignedto option:first').prop('selected', true);
                        $('#assignedto').multiselect('refresh');
                        $('#assignedto').multiselect('rebuild');
                    }
                } else {
                    $(`#${value} input[type="checkbox"]`).prop('checked', $(this).prop('checked'));
                    if (value == 'report') {
                        $('#assignedto').find('option:disabled').remove(); // remove disabled option
                        $('#assignedto option').prop('selected', true);
                        $('#assignedto').multiselect('refresh');
                        $('#assignedto').multiselect('rebuild');
                    }
                }
            })

            // check menu if check any submenu(edit,delete,add...)
            $(document).on('change', '.clicksubmenu', function() {
                value = $(this).data('value');
                if (!$(`#${value}`).prop('checked')) {
                    $(`#${value}`).prop('checked', true);
                }
            })


            //for checkboxes reset

            $(document).on('click', '.resetbtn', function() {
                var module = $(this).data('module');
                $(`#${module}checkboxes input[type="checkbox"] , #${module}allcheck`).prop(
                    'checked', false);

                if (module == 'report') {
                    $('#assignedto option').prop('selected', false);
                    console.log($("#assignedto option:disabled").length);
                    if ($("#assignedto option:disabled").length == 0) {
                        $("#assignedto").prepend(
                            '<option value="" disabled selected>-- Select User --</option>');
                    }
                    $('#assignedto option:first').prop('selected', true);
                    $('#assignedto').multiselect('refresh');
                    $('#assignedto').multiselect('rebuild');
                }
            })




            //submit form
            $('#userupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('user.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        // You can perform additional actions, such as showing a success message or redirecting the user
                        if (response.status == 200) {
                            toastr.success(response.message);
                            window.location = "{{ route('admin.user') }}";
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
            })
        });
    </script>
@endpush
