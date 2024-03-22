@php
    $folder = session('folder_name');
@endphp
@extends($folder.'.admin.masterlayout')

@section('page_title')
    Update User
@endsection
@section('title')
    Update User
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
                    <label for="firstname">FirstName</label>
                    <input type="text" id="firstname" name='firstname' class="form-control" placeholder="First name"
                        required>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="lastname">LastName</label>
                    <input type="text" id="lastname" name='lastname' class="form-control" placeholder="Last name"
                        required>
                    <span class="error-msg" id="error-lastname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="email">Email</label>
                    <input type="email" name='email' class="form-control" id="email" value=""
                        placeholder="Enter Email" required>
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="password">Password</label>
                    <input type="text" id="password" name='password' class="form-control" id="exampleInputPassword3"
                        value="" placeholder="update Password (not mandatory)">
                    <span class="error-msg" id="error-password" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="contact_no">Contact Number</label>
                    <input type="tel" name='contact_number' class="form-control" id="contact_no" value=""
                        placeholder="0123456789" required>
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="country">Select Country</label>
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
                    <label for="state">Select State</label>
                    <select class="form-control" name='state' id="state" required>
                        <option selected="" disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="city">Select City</label>
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
                    <label for="pincode">Pincode</label>
                    <input type="text" id="pincode" name='pincode' class="form-control" placeholder="Pin Code"
                        required>
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary submitBtn" id="formsubmit">Submit</button>
            <button type="reset" class="btn iq-bg-danger resetbtn" id="formreset">Reset</button>
        </div>
        <div class="row">
            <div class="col-sm-12">
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
                                            <input type="checkbox" class="clicksubmenu" data-value='showinvoicemenu'
                                                id="alldatainvoice" name="alldatainvoice" value="1">
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
                                            <input type="checkbox" class="clicksubmenu" data-value='showmngcolmenu'
                                                id="alldatamngcol" name="alldatamngcol" value="1">
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
                                            id="showinvoicesettingmenu" name="showinvoicesettingmenu" value="1">
                                    </td>
                                    <td>Invoice/Settings</td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showinvoicesettingmenu'
                                            id="addinvoicesetting" name="addinvoicesetting" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showinvoicesettingmenu'
                                            id="viewinvoicesetting" name="viewinvoicesetting" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showinvoicesettingmenu'
                                            id="editinvoicesetting" name="editinvoicesetting" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showinvoicesettingmenu'
                                            id="deleteinvoicesetting" name="deleteinvoicesetting" value="1">
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
                                @if (session('user_permissions.invoicemodule.company.add') == '1')
                                    <tr id="company">
                                        <td>
                                            <input type="checkbox" class="clickmenu" data-value='company'
                                                id="showcompanymenu" name="showcompanymenu" value="1">
                                        </td>
                                        <td>Company</td>
                                        <td>
                                            <input type="checkbox" class="clicksubmenu" data-value='showcompanymenu'
                                                id="addcompany" name="addcompany" value="1">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="clicksubmenu" data-value='showcompanymenu'
                                                id="viewcompany" name="viewcompany" value="1">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="clicksubmenu" data-value='showcompanymenu'
                                                id="editcompany" name="editcompany" value="1">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="clicksubmenu" data-value='showcompanymenu'
                                                id="deletecompany" name="deletecompany" value="1">
                                        </td>
                                        <td>
                                            @if (session('user_permissions.invoicemodule.company.alldata') == '1')
                                                <input type="checkbox" class="clicksubmenu" data-value='showcompanymenu'
                                                    id="alldatacompany" name="alldatacompany" value="1">
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr id="bank">
                                    <td>
                                        <input type="checkbox" class="clickmenu" data-value='bank' id="showbankmenu"
                                            name="showbankmenu" value="1">
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
                                <tr id="user">
                                    <td>
                                        <input type="checkbox" class="clickmenu" data-value='user' id="showusermenu"
                                            name="showusermenu" value="1">
                                    </td>
                                    <td>User</td>
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
                                        @if (session('user_permissions.invoicemodule.user.alldata') == '1')
                                            <input type="checkbox" class="clicksubmenu" data-value='showusermenu'
                                                id="alldatauser" name="alldatauser" value="1">
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
                                            <input type="checkbox" class="clicksubmenu" data-value='showcustomermenu'
                                                id="alldatacustomer" name="alldatacustomer" value="1">
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
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
                                        @if (session('user_permissions.invoicemodule.product.alldata') == '1')
                                            <input type="checkbox" class="clicksubmenu" data-value='showproductmenu'
                                                id="alldataproduct" name="alldataproduct" value="1">
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
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
                                        @if (session('user_permissions.invoicemodule.purchase.alldata') == '1')
                                            <input type="checkbox" class="clicksubmenu" data-value='showpurchasemenu'
                                                id="alldatapurchase" name="alldatapurchase" value="1">
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="button-container">
                            <button type="submit" class="btn btn-primary submitBtn"
                                id="invoicemodulsubmit">Submit</button>
                            <button type="reset" class="btn iq-bg-danger resetbtn"
                                id="invoicemodulereset">Reset</button>
                        </div>
                    </div>
                </div>
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
                                        <input type="checkbox" class="clickmenu" data-value='lead' id="showleadmenu"
                                            name="showleadmenu" value="1">
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
                        <div class="button-container">
                            <button type="submit" class="btn btn-primary submitBtn"
                                id="leadmodulesubmit">Submit</button>
                            <button type="reset" class="btn iq-bg-danger resetbtn" id="leadmodulereset">Reset</button>
                        </div>
                    </div>
                </div>
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
                                            id="showcustomersupportmenu" name="showcustomersupportmenu" value="1">
                                    </td>
                                    <td>Customer Support</td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showcustomersupportmenu'
                                            id="addcustomersupport" name="addcustomersupport" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showcustomersupportmenu'
                                            id="viewcustomersupport" name="viewcustomersupport" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showcustomersupportmenu'
                                            id="editcustomersupport" name="editcustomersupport" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="clicksubmenu" data-value='showcustomersupportmenu'
                                            id="deletecustomersupport" name="deletecustomersupport" value="1">
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
                        <div class="button-container">
                            <button type="submit" class="btn btn-primary submitBtn"
                                id="customersupportmodulesubmit">Submit</button>
                            <button type="reset" class="btn iq-bg-danger resetbtn"
                                id="customersupportmodulereset">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

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
                                    //    console.log(`${key} => ${key2} => ${key3} : ${value3}`);
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
                        loadcity(state, city);
                        loaderhide();
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    } else {
                        loaderhide();
                        toastr.error('something went wrong !');
                    }
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
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
                            loaderhide();
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
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
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#city').append(`<option disabled>No Data Found</option>`);
                        }
                    },
                    // error: function(error) {
                    //     console.error('Error:', error);
                    // }
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
                            loaderhide();
                            toastr.success(response.message);
                            window.location = "{{ route('admin.user') }}";

                        } else if (response.status == 422) {
                            loaderhide();
                            toastr.error(response.errors);
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            })
        });
    </script>
@endpush
