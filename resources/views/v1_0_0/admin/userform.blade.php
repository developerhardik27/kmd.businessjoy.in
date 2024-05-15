@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')
@section('page_title')
    Add New User
@endsection
@section('title')
    New User
@endsection



@section('form-content')

    @if ($allow == 'no')
        <p class="text-primary">You are reached your max user limit</p>
    @else
        <form id="userform" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                            placeholder="token" required />
                        <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                        <input type="hidden" value="{{ $company_id }}" name="company_id" class="form-control">
                        <label for="firstname">FirstName</label><span style="color:red;">*</span>
                        <input type="text" id="firstname" name='firstname' class="form-control" placeholder="First name"
                            required />
                        <span class="error-msg" id="error-firstname" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="lastname">LastName</label><span style="color:red;">*</span>
                        <input type="text" id="lastname" name='lastname' class="form-control" placeholder="Last name"
                            required />
                        <span class="error-msg" id="error-lastname" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <label for="email">Email</label><span style="color:red;">*</span>
                        <input type="email" name='email' class="form-control" id="email" value=""
                            placeholder="Enter Email" required />
                        <span class="error-msg" id="error-email" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="password">Password</label>
                        <input type="password" name='password' class="form-control" id="password" value=""
                            placeholder="Enter Password"  />
                        <span class="error-msg" id="error-password" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <label for="contact_number">Contact Number</label><span style="color:red;">*</span>
                        <input type="tel" name='contact_number' class="form-control" id="contact_number" value=""
                            placeholder="0123456789" required />
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
                            required />
                        <span class="error-msg" id="error-pincode" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="img">Image</label><br>
                        <input type="file" name="img" id="img" width="100%" />
                        <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that is smaller than 2 MB.
                        </p>
                        <span class="error-msg" id="error-img" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                     <div class="col-sm-12">
                         <button type="reset" id="formreset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                         <button type="submit" id="formsubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
                     </div>
                </div>
             </div>
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
                                            <th colspan="7" class="text-right"><b>Select All </b> <input
                                                    type="checkbox" id="adminallcheck"></th>
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
                                                <td> <input type="checkbox" class="clickmenu" data-value='company'
                                                        id="showcompanymenu" name="showcompanymenu" value="1"></td>
                                                <td>Company</td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showcompanymenu' id="addcompany" name="addcompany"
                                                        value="1">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showcompanymenu' id="viewcompany" name="viewcompany"
                                                        value="1">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showcompanymenu' id="editcompany" name="editcompany"
                                                        value="1">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showcompanymenu' id="deletecompany"
                                                        name="deletecompany" value="1">
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
                                        @endif
                                        <tr id="user">
                                            <td rowspan="3">
                                                <input type="checkbox" class="clickmenu" data-value='user'
                                                    id="showusermenu" name="showusermenu" value="1">
                                            </td>
                                            <td rowspan="3">User</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                    id="adduser" name="adduser" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                    id="viewuser" name="viewuser" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                    id="edituser" name="edituser" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                    id="deleteuser" name="deleteuser" value="1">
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
                                        <tr>
                                            @if (Session::has('user_permissions.adminmodule.user.max') && session('user_permissions.adminmodule.user.max') == '1')
                                                <td>Max Users</td>
                                                {{-- add more option title here if needed --}}
                                            @endif
                                        </tr>
                                        <tr id="user">
                                            @if (Session::has('user_permissions.adminmodule.user.max') && session('user_permissions.adminmodule.user.max') == '1')
                                                <td>
                                                    <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                        id="maxuser" name="maxuser" value="1">
                                                    {{-- add more checkboxe title here if needed --}}
                                                </td>
                                            @endif
                                        </tr>
                                        @if (Session::has('admin_role') && Session::get('admin_role') == 1)
                                            <tr id="techsupport">
                                                <td rowspan="3">
                                                    <input type="checkbox" class="clickmenu" data-value='techsupport'
                                                        id="showtechsupportmenu" name="showtechsupportmenu"
                                                        value="1">
                                                </td>
                                                <td rowspan="3">Tech support</td>
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
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                         <div class="col-sm-12">
                                             <button type="button" id="adminmodulereset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                                             <button type="submit"  id="adminmodulesubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
                                         </div>
                                    </div>
                                 </div>
                            </div>
                        </div>
                    @endif

                    @if (Session::has('invoice') && Session::get('invoice') == 'yes')
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
                                                <input type="checkbox" id="invoiceallcheck">
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
                                        <tr id="invoice">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='invoice'
                                                    id="showinvoicemenu" name="showinvoicemenu" value="1">
                                            </td>
                                            <td>Invoice</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showinvoicemenu'
                                                    id="addinvoice" name="addinvoice" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showinvoicemenu'
                                                    id="viewinvoice" name="viewinvoice" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showinvoicemenu'
                                                    id="editinvoice" name="editinvoice" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showinvoicemenu'
                                                    id="deleteinvoice" name="deleteinvoice" value="1">
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
                                        <tr id="mngcol">
                                            <td> <input type="checkbox" class="clickmenu" data-value='mngcol'
                                                    id="showmngcolmenu" name="showmngcolmenu" value="1">
                                            </td>
                                            <td>Manage Invoice Column</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showmngcolmenu'
                                                    id="addmngcol" name="addmngcol" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showmngcolmenu'
                                                    id="viewmngcol" name="viewmngcol" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showmngcolmenu'
                                                    id="editmngcol" name="editmngcol" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showmngcolmenu'
                                                    id="deletemngcol" name="deletemngcol" value="1">
                                            </td>
                                            <td>
                                                @if (session('user_permissions.invoicemodule.invoicesetting.alldata') == '1')
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showmngcolmenu' id="alldatamngcol"
                                                        name="alldatamngcol" value="1">
                                                @else
                                                    -
                                                @endif

                                            </td>
                                        </tr>
                                        <tr id="formula">
                                            <td> <input type="checkbox" class="clickmenu" data-value='formula'
                                                    id="showformulamenu" name="showformulamenu" value="1">
                                            </td>
                                            <td>Invoice Formula</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showformulamenu'
                                                    id="addformula" name="addformula" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showformulamenu'
                                                    id="viewformula" name="viewformula" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showformulamenu'
                                                    id="editformula" name="editformula" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showformulamenu'
                                                    id="deleteformula" name="deleteformula" value="1">
                                            </td>
                                            <td>
                                                @if (session('user_permissions.invoicemodule.invoicesetting.alldata') == '1')
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showinvoicesettingmenu' id="alldataformula"
                                                        name="alldataformula" value="1">
                                                @else
                                                    -
                                                @endif

                                            </td>
                                        </tr>
                                        <tr id="invoicesetting">
                                            <td> <input type="checkbox" class="clickmenu" data-value='invoicesetting'
                                                    id="showinvoicesettingmenu" name="showinvoicesettingmenu"
                                                    value="1">
                                            </td>
                                            <td>Invoice/Settings</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showinvoicesettingmenu' id="addinvoicesetting"
                                                    name="addinvoicesetting" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showinvoicesettingmenu' id="viewinvoicesetting"
                                                    name="viewinvoicesetting" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showinvoicesettingmenu' id="editinvoicesetting"
                                                    name="editinvoicesetting" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showinvoicesettingmenu' id="deleteinvoicesetting"
                                                    name="deleteinvoicesetting" value="1">
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
                                        <tr id="bank">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='bank'
                                                    id="showbankmenu" name="showbankmenu" value="1">
                                            </td>
                                            <td>Bank</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                    id="addbank" name="addbank" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                    id="viewbank" name="viewbank" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                    id="editbank" name="editbank" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showbankmenu'
                                                    id="deletebank" name="deletebank" value="1">
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
                                        <tr id="customer">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='customer'
                                                    id="showcustomermenu" name="showcustomermenu" value="1">
                                            </td>
                                            <td>Customer</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showcustomermenu'
                                                    id="addcustomer" name="addcustomer" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showcustomermenu'
                                                    id="viewcustomer" name="viewcustomer" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showcustomermenu'
                                                    id="editcustomer" name="editcustomer" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showcustomermenu'
                                                    id="deletecustomer" name="deletecustomer" value="1">
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
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                         <div class="col-sm-12">
                                             <button type="button" id="invoicemodulereset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                                             <button type="submit"  id="invoicemodulsubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
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
                                                <input type="checkbox" id="leadallcheck">
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
                                        <tr id="lead">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='lead'
                                                    id="showleadmenu" name="showleadmenu" value="1">
                                            </td>
                                            <td>Lead</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                    id="addlead" name="addlead" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                    id="viewlead" name="viewlead" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                    id="editlead" name="editlead" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showleadmenu'
                                                    id="deletelead" name="deletelead" value="1">
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
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                         <div class="col-sm-12">
                                             <button type="button" id="leadmodulereset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                                             <button type="submit"  id="leadmodulesubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
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
                                                <input type="checkbox" id="customersupportallcheck">
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
                                        <tr id="customersupport">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='customersupport'
                                                    id="showcustomersupportmenu" name="showcustomersupportmenu"
                                                    value="1">
                                            </td>
                                            <td>Customer Support</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showcustomersupportmenu' id="addcustomersupport"
                                                    name="addcustomersupport" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showcustomersupportmenu' id="viewcustomersupport"
                                                    name="viewcustomersupport" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showcustomersupportmenu' id="editcustomersupport"
                                                    name="editcustomersupport" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showcustomersupportmenu' id="deletecustomersupport"
                                                    name="deletecustomersupport" value="1">
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
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                         <div class="col-sm-12">
                                             <button type="button" id="customersupportmodulereset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                                             <button type="submit"  id="customersupportmodulesubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
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
                                            <th colspan="7" class="text-right"><b>Select All </b> <input
                                                    type="checkbox" id="accountallcheck"></th>
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
                                        <tr id="purchase">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='purchase'
                                                    id="showpurchasemenu" name="showpurchasemenu" value="1">
                                            </td>
                                            <td>Purchase</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showpurchasemenu'
                                                    id="addpurchase" name="addpurchase" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showpurchasemenu'
                                                    id="viewpurchase" name="viewpurchase" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showpurchasemenu'
                                                    id="editpurchase" name="editpurchase" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showpurchasemenu'
                                                    id="deletepurchase" name="deletepurchase" value="1">
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
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                         <div class="col-sm-12">
                                             <button type="button" id="accountmodulereset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                                             <button type="submit"  id="accountsubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
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
                                            <th colspan="7" class="text-right"><b>Select All </b> <input
                                                    type="checkbox" id="inventoryallcheck"></th>
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
                                        <tr id="product">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='product'
                                                    id="showproductmenu" name="showproductmenu" value="1">
                                            </td>
                                            <td>Product</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showproductmenu'
                                                    id="addproduct" name="addproduct" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showproductmenu'
                                                    id="viewproduct" name="viewproduct" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showproductmenu'
                                                    id="editproduct" name="editproduct" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showproductmenu'
                                                    id="deleteproduct" name="deleteproduct" value="1">
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

                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                         <div class="col-sm-12">
                                             <button type="button" id="inventorymodulereset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                                             <button type="submit"  id="inventorymodulesubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
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
                                            <th colspan="7" class="text-right"><b>Select All </b> <input
                                                    type="checkbox" id="reminderallcheck"></th>
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
                                        <tr id="reminder">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='reminder'
                                                    id="showremindermenu" name="showremindermenu" value="1">
                                            </td>
                                            <td>Reminder</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showremindermenu'
                                                    id="addreminder" name="addreminder" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showremindermenu'
                                                    id="viewreminder" name="viewreminder" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showremindermenu'
                                                    id="editreminder" name="editreminder" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu" data-value='showremindermenu'
                                                    id="deletereminder" name="deletereminder" value="1">
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
                                        <tr id="remindercustomer">
                                            <td>
                                                <input type="checkbox" class="clickmenu" data-value='remindercustomer'
                                                    id="showremindercustomermenu" name="showremindercustomermenu"
                                                    value="1">
                                            </td>
                                            <td>Reminder Customer</td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showremindercustomermenu' id="addremindercustomer"
                                                    name="addremindercustomer" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showremindercustomermenu' id="viewremindercustomer"
                                                    name="viewremindercustomer" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showremindercustomermenu' id="editremindercustomer"
                                                    name="editremindercustomer" value="1">
                                            </td>
                                            <td>
                                                <input type="checkbox" class="clicksubmenu"
                                                    data-value='showremindercustomermenu' id="deleteremindercustomer"
                                                    name="deleteremindercustomer" value="1">
                                            </td>
                                            <td>
                                                @if (session('user_permissions.remindermodule.remindercustomer.alldata') == '1')
                                                    <input type="checkbox" class="clicksubmenu"
                                                        data-value='showremindercustomermenu' id="alldataremindercustomer"
                                                        name="alldataremindercustomer" value="1">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="form-row">
                                         <div class="col-sm-12">
                                             <button type="button" id="remindermodulereset" class="btn iq-bg-danger float-right resetbtn">Reset</button>
                                             <button type="submit"  id="remindermodulesubmit" class="btn btn-primary float-right my-0 submitBtn" >Submit</button>
                                         </div>
                                    </div>
                                 </div>  
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </form>
    @endif

@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action */*/*/
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data


            //for checkboxes reset

            $('#adminmodulereset').click(function() {
                $('#admincheckboxes input[type="checkbox"],#adminallcheck').prop('checked', false);
            });

            $('#invoicemodulereset').click(function() {
                $('#invoicecheckboxes input[type="checkbox"],#invoiceallcheck').prop('checked', false);
            });

            $('#leadmodulereset').click(function() {
                $('#leadcheckboxes input[type="checkbox"],#leadallcheck').prop('checked', false);
            });
            $('#customersupportmodulereset').click(function() {
                $('#customersupportcheckboxes input[type="checkbox"],#customersupportallcheck').prop(
                    'checked', false);
            });
            $('#accountmodulereset').click(function() {
                $('#accountcheckboxes input[type="checkbox"],#accountallcheck').prop('checked', false);
            });
            $('#inventorymodulereset').click(function() {
                $('#inventorycheckboxes input[type="checkbox"],#inventoryallcheck').prop('checked', false);
            });
            $('#remindermodulereset').click(function() {
                $('#remindercheckboxes input[type="checkbox"],#reminderallcheck').prop('checked', false);
            });

            // show country data in dropdown
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
                    } else {
                        $('#country').append(`<option> No Data Found</option>`);
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


            // show state data when country select
            $('#country').on('change', function() {
                loadershow();
                var country_id = $(this).val();
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country_id,
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

            // show city data when state select
            $('#state').on('change', function() {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                var state_id = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state_id,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                        } else {
                            $('#city').append(`<option disabled> No city Found</option>`)
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

            // invoicemodule - check all checkboxes of invoicemodule on click select all
            $('#invoiceallcheck').change(function() {
                if (!$(this).prop('checked')) {
                    $('#invoicecheckboxes input[type="checkbox"]').prop('checked', false);
                } else {
                    $('#invoicecheckboxes input[type="checkbox"]').prop('checked', $(this).prop('checked'));
                }

            });

            // leadmodule - check all checkboxes of leadmodule on click select all
            $('#leadallcheck').change(function() {
                if (!$(this).prop('checked')) {
                    $('#leadcheckboxes input[type="checkbox"]').prop('checked', false);
                } else {
                    $('#leadcheckboxes input[type="checkbox"]').prop('checked', $(this).prop('checked'));
                }

            });

            // customersupportmodule - check all checkboxes of customersupportmodule on click select all
            $('#customersupportallcheck').change(function() {
                if (!$(this).prop('checked')) {
                    $('#customersupportcheckboxes input[type="checkbox"]').prop('checked', false);
                } else {
                    $('#customersupportcheckboxes input[type="checkbox"]').prop('checked', $(this).prop(
                        'checked'));
                }

            });
            // adminmodule - check all checkboxes of adminmodule on click select all
            $('#adminallcheck').change(function() {
                if (!$(this).prop('checked')) {
                    $('#admincheckboxes input[type="checkbox"]').prop('checked', false);
                } else {
                    $('#admincheckboxes input[type="checkbox"]').prop('checked', $(this).prop(
                        'checked'));
                }

            });

            // accountmodule - check all checkboxes of accountmodule on click select all
            $('#accountallcheck').change(function() {
                if (!$(this).prop('checked')) {
                    $('#accountcheckboxes input[type="checkbox"]').prop('checked', false);
                } else {
                    $('#accountcheckboxes input[type="checkbox"]').prop('checked', $(this).prop(
                        'checked'));
                }

            });

            // inventorymodule - check all checkboxes of inventorymodule on click select all
            $('#inventoryallcheck').change(function() {
                if (!$(this).prop('checked')) {
                    $('#inventorycheckboxes input[type="checkbox"]').prop('checked', false);
                } else {
                    $('#inventorycheckboxes input[type="checkbox"]').prop('checked', $(this).prop(
                        'checked'));
                }

            });

            // remindermodule - check all checkboxes of remindermodule on click select all
            $('#reminderallcheck').change(function() {
                if (!$(this).prop('checked')) {
                    $('#remindercheckboxes input[type="checkbox"]').prop('checked', false);
                } else {
                    $('#remindercheckboxes input[type="checkbox"]').prop('checked', $(this).prop(
                        'checked'));
                }

            });
            // check all checkboxes in the row if click on any menu
            $(document).on('change', '.clickmenu', function() {
                value = $(this).data('value');
                if (!$(this).prop('checked')) {
                    $(`#${value} input[type="checkbox"]`).prop('checked', false);
                } else {
                    $(`#${value} input[type="checkbox"]`).prop('checked', $(this).prop('checked'));
                }
            })

            // check menu if check any submenu(edit,delete,add...)
            $(document).on('change', '.clicksubmenu', function() {
                value = $(this).data('value');
                if (!$(`#${value}`).prop('checked')) {
                    $(`#${value}`).prop('checked', true);
                }
            })
            //submit form
            $('#userform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('user.store') }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
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
