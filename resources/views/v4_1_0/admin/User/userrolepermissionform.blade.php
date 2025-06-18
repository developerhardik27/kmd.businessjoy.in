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

    $user_id = session('user_id');

@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Add New User Role Permission
@endsection
@section('title')
    New User Role Permission
@endsection

@section('style')
    <style>
        .special-color {
            color: #6546d2;
        }

        table,
        table th,
        table td {
            border-right: transparent !important;
            border-left: transparent !important;
        }
    </style>
@endsection

@section('form-content')
    <form id="userrolepermissionform" name="userrolepermissionform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                        placeholder="user_id" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                        placeholder="company_id" required />
                    <label for="role_name">Role Name</label><span style="color:red;">*</span>
                    <input id="role_name" type="text" name="role_name" class="form-control" placeholder="Role Name"
                        required />
                    <span class="error-msg" id="error-role_name" style="color: red"></span>
                </div>
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                         class="btn btn-secondary float-right cancelbtn">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset Details" class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save Details" class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>

        @if (session('user_permissions.adminmodule.userpermission.view') == '1' ||
                    session('user_permissions.adminmodule.userpermission.add') == '1' ||
                    $user_id == 1)
                <div class="row permission-row">
                    <div class="col-sm-12">
                        @if ((Session::has('admin') && Session::get('admin') == 'yes') || $user_id == 1)
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
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15%">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="admincheckboxes">
                                            @if (session('user_permissions.adminmodule.admindashboard.add') == '1' || $user_id == 1)
                                                <tr id="admindashboard">
                                                    <td>Admin Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='admindashboard'
                                                            id="showadmindashboardmenu" name="showadmindashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.admindashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showadmindashboardmenu' id="addadmindashboard"
                                                                name="addadmindashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.admindashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showadmindashboardmenu' id="viewadmindashboard"
                                                                name="viewadmindashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.admindashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showadmindashboardmenu' id="editadmindashboard"
                                                                name="editadmindashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.admindashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showadmindashboardmenu' id="deleteadmindashboard"
                                                                name="deleteadmindashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.admindashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showadmindashboardmenu' id="alldataadmindashboard"
                                                                name="alldataadmindashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.adminmodule.company.add') == '1' || $user_id == 1)
                                                <tr id="company">
                                                    <td rowspan="{{ $rowspan }}">Company</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='company'
                                                            id="showcompanymenu" name="showcompanymenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcompanymenu' id="addcompany"
                                                                name="addcompany" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcompanymenu' id="viewcompany"
                                                                name="viewcompany" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcompanymenu' id="editcompany"
                                                                name="editcompany" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcompanymenu' id="deletecompany"
                                                                name="deletecompany" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcompanymenu' id="alldatacompany"
                                                                name="alldatacompany" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if (
                                                    (Session::has('user_permissions.adminmodule.company.max') &&
                                                        session('user_permissions.adminmodule.company.max') == '1') ||
                                                        $user_id == 1)
                                                    <tr>
                                                        <td>Max Users</td>
                                                        {{-- add more option title here if needed --}}
                                                    </tr>
                                                    <tr id="company">
                                                        <td>
                                                            @if (session('user_permissions.adminmodule.company.max') == '1' || $user_id == 1)
                                                                <input type="checkbox" class="clicksubmenu"
                                                                    data-value='showcompanymenu' id="maxcompany"
                                                                    name="maxuser" value="1">
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        {{-- add more checkboxe title here if needed --}}
                                                    </tr>
                                                @endif
                                            @endif

                                            @if (session('user_permissions.adminmodule.user.add') == '1' || $user_id == 1)
                                                <tr id="user">
                                                    <td>User</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='user'
                                                            id="showusermenu" name="showusermenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.user.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showusermenu' id="adduser" name="adduser"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.user.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showusermenu' id="viewuser" name="viewuser"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.user.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showusermenu' id="edituser" name="edituser"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.user.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showusermenu' id="deleteuser"
                                                                name="deleteuser" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.user.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showusermenu' id="alldatauser"
                                                                name="alldatauser" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if ((Session::has('admin_role') && Session::get('admin_role') == 1) || $user_id == 1)
                                                <tr id="techsupport">
                                                    <td>Tech support</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='techsupport'
                                                            id="showtechsupportmenu" name="showtechsupportmenu"
                                                            value="1">
                                                    </td>
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
                                                        @if (session('user_permissions.adminmodule.user.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showtechsupportmenu' id="alldatatechsupport"
                                                                name="alldatatechsupport" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.adminmodule.user.add') == '1' || $user_id == 1)
                                                <tr id="userpermission">
                                                    <td>User Permission</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='userpermission' id="showuserpermissionmenu"
                                                            name="showuserpermissionmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.userpermission.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showuserpermissionmenu' id="adduserpermission"
                                                                name="adduserpermission" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.userpermission.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showuserpermissionmenu'
                                                                id="viewuserpermission" name="viewuserpermission"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.userpermission.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showuserpermissionmenu'
                                                                id="edituserpermission" name="edituserpermission"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.userpermission.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showuserpermissionmenu'
                                                                id="deleteuserpermission" name="deleteuserpermission"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.userpermission.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showuserpermissionmenu'
                                                                id="alldatauserpermission" name="alldatauserpermission"
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
                                            <div class="col-sm-12 text-right">
                                                <button type="submit" id="adminmodulesubmit"
                                                    class="btn btn-primary submitBtn" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save">Save</button>
                                                <button type="button" id="adminmodulereset" data-module="admin"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset Admin Module"
                                                    class="btn iq-bg-danger resetbtn">
                                                    Reset
                                                </button>
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel" class="btn btn-secondary cancelbtn">
                                                    Cancel
                                                </button>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('quotation') && Session::get('quotation') == 'yes') || $user_id == 1)
                            <div class="iq-card">
                                <div class="iq-card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">Quotation Module</h4>
                                    </div>
                                </div>
                                <div class="iq-card-body">
                                    <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                        <thead>
                                            <tr>
                                                <th colspan="7" class="text-right">
                                                    <b>Select All </b>
                                                    <input type="checkbox" id="quotationallcheck" data-module="quotation"
                                                        class="allcheck">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15% ;">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="quotationcheckboxes">

                                            @if (session('user_permissions.quotationmodule.quotationdashboard.add') == '1' || $user_id == 1)
                                                <tr id="quotationdashboard">
                                                    <td>Quotation Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='quotationdashboard'
                                                            id="showquotationdashboardmenu" name="showquotationdashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationdashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationdashboardmenu' id="addquotationdashboard"
                                                                name="addquotationdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationdashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationdashboardmenu' id="viewquotationdashboard"
                                                                name="viewquotationdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationdashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationdashboardmenu' id="editquotationdashboard"
                                                                name="editquotationdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationdashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationdashboardmenu' id="deletequotationdashboard"
                                                                name="deletequotationdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationdashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationdashboardmenu' id="alldataquotationdashboard"
                                                                name="alldataquotationdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotation.add') == '1' || $user_id == 1)
                                                <tr id="quotation">
                                                    <td>Quotation</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='quotation'
                                                            id="showquotationmenu" name="showquotationmenu"
                                                            value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotation.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmenu' id="addquotation"
                                                                name="addquotation" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotation.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmenu' id="viewquotation"
                                                                name="viewquotation" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotation.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmenu' id="editquotation"
                                                                name="editquotation" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotation.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmenu' id="deletequotation"
                                                                name="deletequotation" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotation.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmenu' id="alldataquotation"
                                                                name="alldataquotation" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationsetting.add') == '1' || $user_id == 1)
                                                <tr>
                                                    <td class="p-0" style="border-right: 0;"> 
                                                        <span class="btn expandsettingsbutton" data-target="quotation_settings_rows">
                                                            <i class="ri ri-2x ri-arrow-right-circle-fill"></i>
                                                            Quotation Settings
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationmngcol.add') == '1' || $user_id == 1)
                                                <tr id="quotationmngcol" class="quotation_settings_rows subsettingrows">
                                                    <td>Manage Quotation Column</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationmngcol' id="showquotationmngcolmenu"
                                                            name="showquotationmngcolmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationmngcol.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmngcolmenu'
                                                                id="addquotationmngcol" name="addquotationmngcol"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationmngcol.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmngcolmenu'
                                                                id="viewquotationmngcol" name="viewquotationmngcol"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationmngcol.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmngcolmenu'
                                                                id="editquotationmngcol" name="editquotationmngcol"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationmngcol.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmngcolmenu'
                                                                id="deletequotationmngcol" name="deletequotationmngcol"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationmngcol.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationmngcolmenu'
                                                                id="alldataquotationmngcol" name="alldataquotationmngcol"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationformula.add') == '1' || $user_id == 1)
                                                <tr id="quotationformula" class="quotation_settings_rows subsettingrows">
                                                    <td>Quotation Formula</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationformula' id="showquotationformulamenu"
                                                            name="showquotationformulamenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationformula.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationformulamenu'
                                                                id="addquotationformula" name="addquotationformula"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationformula.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationformulamenu'
                                                                id="viewquotationformula" name="viewquotationformula"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationformula.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationformulamenu'
                                                                id="editquotationformula" name="editquotationformula"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationformula.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationformulamenu'
                                                                id="deletequotationformula" name="deletequotationformula"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationformula.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationformulamenu'
                                                                id="alldataquotationformula"
                                                                name="alldataquotationformula" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationsetting.add') == '1' || $user_id == 1)
                                                <tr id="quotationsetting" class="quotation_settings_rows subsettingrows">
                                                    <td><span class="btn expandsettingsbutton"
                                                            data-target="quotation_other_settings_rows"><i
                                                                class="ri ri-2x ri-arrow-right-circle-fill"></i>
                                                            Quotation/Settings</span></td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationsetting' id="showquotationsettingmenu"
                                                            name="showquotationsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationsettingmenu'
                                                                id="addquotationsetting" name="addquotationsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationsettingmenu'
                                                                id="viewquotationsetting" name="viewquotationsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationsettingmenu'
                                                                id="editquotationsetting" name="editquotationsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationsettingmenu'
                                                                id="deletequotationsetting" name="deletequotationsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationsettingmenu'
                                                                id="alldataquotationsetting"
                                                                name="alldataquotationsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationnumbersetting.add') == '1' || $user_id == 1)
                                                <tr id="quotationnumbersetting"
                                                    class="quotation_other_settings_rows subsettingrows">
                                                    <td>Quotation Number Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationnumbersetting'
                                                            id="showquotationnumbersettingmenu"
                                                            name="showquotationnumbersettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationnumbersetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationnumbersettingmenu'
                                                                id="addquotationnumbersetting"
                                                                name="addquotationnumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationnumbersetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationnumbersettingmenu'
                                                                id="viewquotationnumbersetting"
                                                                name="viewquotationnumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationnumbersetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationnumbersettingmenu'
                                                                id="editquotationnumbersetting"
                                                                name="editquotationnumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationnumbersetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationnumbersettingmenu'
                                                                id="deletequotationnumbersetting"
                                                                name="deletequotationnumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationnumbersetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationnumbersettingmenu'
                                                                id="alldataquotationnumbersetting"
                                                                name="alldataquotationnumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationtandcsetting.add') == '1' || $user_id == 1)
                                                <tr id="quotationtandcsetting"
                                                    class="quotation_other_settings_rows subsettingrows">
                                                    <td>Quotation T&C Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationtandcsetting'
                                                            id="showquotationtandcsettingmenu"
                                                            name="showquotationtandcsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationtandcsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationtandcsettingmenu'
                                                                id="addquotationtandcsetting"
                                                                name="addquotationtandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationtandcsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationtandcsettingmenu'
                                                                id="viewquotationtandcsetting"
                                                                name="viewquotationtandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationtandcsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationtandcsettingmenu'
                                                                id="editquotationtandcsetting"
                                                                name="editquotationtandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationtandcsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationtandcsettingmenu'
                                                                id="deletequotationtandcsetting"
                                                                name="deletequotationtandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationtandcsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationtandcsettingmenu'
                                                                id="alldataquotationtandcsetting"
                                                                name="alldataquotationtandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationstandardsetting.add') == '1' || $user_id == 1)
                                                <tr id="quotationstandardsetting"
                                                    class="quotation_other_settings_rows subsettingrows">
                                                    <td>Quotation Standard Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationstandardsetting'
                                                            id="showquotationstandardsettingmenu"
                                                            name="showquotationstandardsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationstandardsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationstandardsettingmenu'
                                                                id="addquotationstandardsetting"
                                                                name="addquotationstandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationstandardsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationstandardsettingmenu'
                                                                id="viewquotationstandardsetting"
                                                                name="viewquotationstandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationstandardsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationstandardsettingmenu'
                                                                id="editquotationstandardsetting"
                                                                name="editquotationstandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationstandardsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationstandardsettingmenu'
                                                                id="deletequotationstandardsetting"
                                                                name="deletequotationstandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationstandardsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationstandardsettingmenu'
                                                                id="alldataquotationstandardsetting"
                                                                name="alldataquotationstandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotationgstsetting.add') == '1' || $user_id == 1)
                                                <tr id="quotationgstsetting"
                                                    class="quotation_other_settings_rows subsettingrows">
                                                    <td>Quotation GST Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationgstsetting'
                                                            id="showquotationgstsettingmenu"
                                                            name="showquotationgstsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationgstsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationgstsettingmenu'
                                                                id="addquotationgstsetting" name="addquotationgstsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationgstsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationgstsettingmenu'
                                                                id="viewquotationgstsetting"
                                                                name="viewquotationgstsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationgstsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationgstsettingmenu'
                                                                id="editquotationgstsetting"
                                                                name="editquotationgstsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationgstsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationgstsettingmenu'
                                                                id="deletequotationgstsetting"
                                                                name="deletequotationgstsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationgstsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationgstsettingmenu'
                                                                id="alldataquotationgstsetting"
                                                                name="alldataquotationgstsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.quotationmodule.quotatoincustomer.add') == '1' || $user_id == 1)
                                                <tr id="quotationcustomer">
                                                    <td>Customer</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='quotationcustomer' id="showquotationcustomermenu"
                                                            name="showquotationcustomermenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationcustomer.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationcustomermenu'
                                                                id="addquotationcustomer" name="addquotationcustomer"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationcustomer.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationcustomermenu'
                                                                id="viewquotationcustomer" name="viewquotationcustomer"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationcustomer.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationcustomermenu'
                                                                id="editquotationcustomer" name="editquotationcustomer"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationcustomer.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationcustomermenu'
                                                                id="deletequotationcustomer"
                                                                name="deletequotationcustomer" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.quotationmodule.quotationcustomer.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showquotationcustomermenu'
                                                                id="alldataquotationcustomer"
                                                                name="alldataquotationcustomer" value="1">
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="quotationmodulereset" data-module="quotation"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset Quotation Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="quotationmodulsubmit" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('invoice') && Session::get('invoice') == 'yes') || $user_id == 1)
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
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15% ;">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoicecheckboxes">
                                            @if (session('user_permissions.invoicemodule.invoicedashboard.add') == '1' || $user_id == 1)
                                                <tr id="invoicedashboard">
                                                    <td>Invoice Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='invoicedashboard'
                                                            id="showinvoicedashboardmenu" name="showinvoicedashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicedashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicedashboardmenu' id="addinvoicedashboard"
                                                                name="addinvoicedashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicedashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicedashboardmenu' id="viewinvoicedashboard"
                                                                name="viewinvoicedashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicedashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicedashboardmenu' id="editinvoicedashboard"
                                                                name="editinvoicedashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicedashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicedashboardmenu' id="deleteinvoicedashboard"
                                                                name="deleteinvoicedashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicedashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicedashboardmenu' id="alldatainvoicedashboard"
                                                                name="alldatainvoicedashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoice.add') == '1' || $user_id == 1)
                                                <tr id="invoice">
                                                    <td>Invoice</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='invoice'
                                                            id="showinvoicemenu" name="showinvoicemenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoice.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicemenu' id="addinvoice"
                                                                name="addinvoice" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoice.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicemenu' id="viewinvoice"
                                                                name="viewinvoice" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoice.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicemenu' id="editinvoice"
                                                                name="editinvoice" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoice.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicemenu' id="deleteinvoice"
                                                                name="deleteinvoice" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoice.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicemenu' id="alldatainvoice"
                                                                name="alldatainvoice" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoicesetting.add') == '1' || $user_id == 1)
                                                <tr>
                                                    <td class="p-0" style="border-right: 0;">
                                                        <span class="btn expandsettingsbutton" data-target="invoice_settings_rows">
                                                            <i class="ri ri-2x ri-arrow-right-circle-fill"></i>
                                                            Invoice Settings
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.mngcol.add') == '1' || $user_id == 1)
                                                <tr id="mngcol" class="invoice_settings_rows subsettingrows">
                                                    <td>Manage Invoice Column</td>
                                                    <td> <input type="checkbox" class="clickmenu" data-value='mngcol'
                                                            id="showmngcolmenu" name="showmngcolmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.mngcol.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showmngcolmenu' id="addmngcol"
                                                                name="addmngcol" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.mngcol.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showmngcolmenu' id="viewmngcol"
                                                                name="viewmngcol" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.mngcol.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showmngcolmenu' id="editmngcol"
                                                                name="editmngcol" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.mngcol.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showmngcolmenu' id="deletemngcol"
                                                                name="deletemngcol" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.mngcol.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showmngcolmenu' id="alldatamngcol"
                                                                name="alldatamngcol" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.formula.add') == '1' || $user_id == 1)
                                                <tr id="formula" class="invoice_settings_rows subsettingrows">
                                                    <td>Invoice Formula</td>
                                                    <td> <input type="checkbox" class="clickmenu" data-value='formula'
                                                            id="showformulamenu" name="showformulamenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.formula.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showformulamenu' id="addformula"
                                                                name="addformula" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.formula.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showformulamenu' id="viewformula"
                                                                name="viewformula" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.formula.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showformulamenu' id="editformula"
                                                                name="editformula" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.formula.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showformulamenu' id="deleteformula"
                                                                name="deleteformula" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.formula.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showformulamenu' id="alldataformula"
                                                                name="alldataformula" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoicesetting.add') == '1' || $user_id == 1)
                                                <tr id="invoicesetting" class="invoice_settings_rows subsettingrows">
                                                    <td class="p-0"><span class="btn expandsettingsbutton"
                                                            data-target="invoice_other_settings_rows"><i
                                                                class="ri ri-2x ri-arrow-right-circle-fill"></i>
                                                            Invoice/Settings</span></td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='invoicesetting' id="showinvoicesettingmenu"
                                                            name="showinvoicesettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicesetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicesettingmenu' id="addinvoicesetting"
                                                                name="addinvoicesetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicesetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicesettingmenu'
                                                                id="viewinvoicesetting" name="viewinvoicesetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicesetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicesettingmenu'
                                                                id="editinvoicesetting" name="editinvoicesetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicesetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicesettingmenu'
                                                                id="deleteinvoicesetting" name="deleteinvoicesetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicesetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicesettingmenu'
                                                                id="alldatainvoicesetting" name="alldatainvoicesetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoicenumbersetting.add') == '1' || $user_id == 1)
                                                <tr id="invoicenumbersetting"
                                                    class="invoice_other_settings_rows subsettingrows">
                                                    <td>Invoice Number Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='invoicenumbersetting'
                                                            id="showinvoicenumbersettingmenu"
                                                            name="showinvoicenumbersettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicenumbersetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicenumbersettingmenu'
                                                                id="addinvoicenumbersetting"
                                                                name="addinvoicenumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicenumbersetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicenumbersettingmenu'
                                                                id="viewinvoicenumbersetting"
                                                                name="viewinvoicenumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicenumbersetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicenumbersettingmenu'
                                                                id="editinvoicenumbersetting"
                                                                name="editinvoicenumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicenumbersetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicenumbersettingmenu'
                                                                id="deleteinvoicenumbersetting"
                                                                name="deleteinvoicenumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicenumbersetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicenumbersettingmenu'
                                                                id="alldatainvoicenumbersetting"
                                                                name="alldatainvoicenumbersetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoicetandcsetting.add') == '1' || $user_id == 1)
                                                <tr id="invoicetandcsetting"
                                                    class="invoice_other_settings_rows subsettingrows">
                                                    <td>Invoice T&C Settings</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='invoicetandcsetting'
                                                            id="showinvoicetandcsettingmenu"
                                                            name="showinvoicetandcsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicetandcsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicetandcsettingmenu'
                                                                id="addinvoicetandcsetting" name="addinvoicetandcsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicetandcsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicetandcsettingmenu'
                                                                id="viewinvoicetandcsetting"
                                                                name="viewinvoicetandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicetandcsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicetandcsettingmenu'
                                                                id="editinvoicetandcsetting"
                                                                name="editinvoicetandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicetandcsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicetandcsettingmenu'
                                                                id="deleteinvoicetandcsetting"
                                                                name="deleteinvoicetandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicetandcsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicetandcsettingmenu'
                                                                id="alldatainvoicetandcsetting"
                                                                name="alldatainvoicetandcsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoicestandardsetting.add') == '1' || $user_id == 1)
                                                <tr id="invoicestandardsetting"
                                                    class="invoice_other_settings_rows subsettingrows">
                                                    <td>Invoice Standard Settings</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='invoicestandardsetting'
                                                            id="showinvoicestandardsettingmenu"
                                                            name="showinvoicestandardsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicestandardsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicestandardsettingmenu'
                                                                id="addinvoicestandardsetting"
                                                                name="addinvoicestandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicestandardsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicestandardsettingmenu'
                                                                id="viewinvoicestandardsetting"
                                                                name="viewinvoicestandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicestandardsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicestandardsettingmenu'
                                                                id="editinvoicestandardsetting"
                                                                name="editinvoicestandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicestandardsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicestandardsettingmenu'
                                                                id="deleteinvoicestandardsetting"
                                                                name="deleteinvoicestandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicestandardsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicestandardsettingmenu'
                                                                id="alldatainvoicestandardsetting"
                                                                name="alldatainvoicestandardsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoicegstsetting.add') == '1' || $user_id == 1)
                                                <tr id="invoicegstsetting"
                                                    class="invoice_other_settings_rows subsettingrows">
                                                    <td>Invoice GST Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='invoicegstsetting'
                                                            id="showinvoicegstsettingmenu"
                                                            name="showinvoicegstsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicegstsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicegstsettingmenu'
                                                                id="addinvoicegstsetting" name="addinvoicegstsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicegstsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicegstsettingmenu'
                                                                id="viewinvoicegstsetting" name="viewinvoicegstsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicegstsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicegstsettingmenu'
                                                                id="editinvoicegstsetting" name="editinvoicegstsetting"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicegstsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicegstsettingmenu'
                                                                id="deleteinvoicegstsetting"
                                                                name="deleteinvoicegstsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicegstsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicegstsettingmenu'
                                                                id="alldatainvoicegstsetting"
                                                                name="alldatainvoicegstsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.add') == '1' || $user_id == 1)
                                                <tr id="invoicecustomeridsetting"
                                                    class="invoice_other_settings_rows subsettingrows">
                                                    <td>Invoice Customer Id Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='invoicecustomeridsetting'
                                                            id="showinvoicecustomeridsettingmenu"
                                                            name="showinvoicecustomeridsettingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicecustomeridsettingmenu'
                                                                id="addinvoicecustomeridsetting"
                                                                name="addinvoicecustomeridsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicecustomeridsettingmenu'
                                                                id="viewinvoicecustomeridsetting"
                                                                name="viewinvoicecustomeridsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicecustomeridsettingmenu'
                                                                id="editinvoicecustomeridsetting"
                                                                name="editinvoicecustomeridsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicecustomeridsettingmenu'
                                                                id="deleteinvoicecustomeridsetting"
                                                                name="deleteinvoicecustomeridsetting" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinvoicecustomeridsettingmenu'
                                                                id="alldatainvoicecustomeridsetting"
                                                                name="alldatainvoicecustomeridsetting" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.bank.add') == '1' || $user_id == 1)
                                                <tr id="bank">
                                                    <td>Bank</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='bank'
                                                            id="showbankmenu" name="showbankmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.bank.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showbankmenu' id="addbank"
                                                                name="addbank" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.bank.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showbankmenu' id="viewbank"
                                                                name="viewbank" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.bank.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showbankmenu' id="editbank"
                                                                name="editbank" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.bank.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showbankmenu' id="deletebank"
                                                                name="deletebank" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.bank.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showbankmenu' id="alldatabank"
                                                                name="alldatabank" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.invoicemodule.customer.add') == '1' || $user_id == 1)
                                                <tr id="customer">
                                                    <td>Customer</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='customer'
                                                            id="showcustomermenu" name="showcustomermenu"
                                                            value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.customer.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomermenu' id="addcustomer"
                                                                name="addcustomer" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.customer.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomermenu' id="viewcustomer"
                                                                name="viewcustomer" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.customer.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomermenu' id="editcustomer"
                                                                name="editcustomer" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.customer.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomermenu' id="deletecustomer"
                                                                name="deletecustomer" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.customer.alldata') == '1' || $user_id == 1)
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="invoicemodulereset" data-module="invoice"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset Invoice Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="invoicemodulsubmit" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('lead') && Session::get('lead') == 'yes') || $user_id == 1)
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
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15%">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="leadcheckboxes">
                                            @if (session('user_permissions.leadmodule.leaddashboard.add') == '1' || $user_id == 1)
                                                <tr id="leaddashboard">
                                                    <td>Lead Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='leaddashboard'
                                                            id="showleaddashboardmenu" name="showleaddashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leaddashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleaddashboardmenu' id="addleaddashboard"
                                                                name="addleaddashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leaddashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleaddashboardmenu' id="viewleaddashboard"
                                                                name="viewleaddashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leaddashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleaddashboardmenu' id="editleaddashboard"
                                                                name="editleaddashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leaddashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleaddashboardmenu' id="deleteleaddashboard"
                                                                name="deleteleaddashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leaddashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleaddashboardmenu' id="alldataleaddashboard"
                                                                name="alldataleaddashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.leadmodule.lead.add') == '1' || $user_id == 1)
                                                <tr id="lead">
                                                    <td>Lead</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='lead'
                                                            id="showleadmenu" name="showleadmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.lead.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadmenu' id="addlead"
                                                                name="addlead" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.lead.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadmenu' id="viewlead"
                                                                name="viewlead" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.lead.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadmenu' id="editlead"
                                                                name="editlead" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.lead.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadmenu' id="deletelead"
                                                                name="deletelead" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.lead.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadmenu' id="alldatalead"
                                                                name="alldatalead" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.leadmodule.upcomingfollowup.add') == '1' || $user_id == 1)
                                                <tr id="upcomingfollowup">
                                                    <td>Upcoming Follow-up</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='upcomingfollowup'
                                                            id="showupcomingfollowupmenu" name="showupcomingfollowupmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.upcomingfollowup.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showupcomingfollowupmenu' id="addupcomingfollowup"
                                                                name="addupcomingfollowup" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.upcomingfollowup.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showupcomingfollowupmenu' id="viewupcomingfollowup"
                                                                name="viewupcomingfollowup" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.upcomingfollowup.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showupcomingfollowupmenu' id="editupcomingfollowup"
                                                                name="editupcomingfollowup" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.upcomingfollowup.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showupcomingfollowupmenu' id="deleteupcomingfollowup"
                                                                name="deleteupcomingfollowup" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.upcomingfollowup.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showupcomingfollowupmenu' id="alldataupcomingfollowup"
                                                                name="alldataupcomingfollowup" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.leadmodule.analysis.add') == '1' || $user_id == 1)
                                                <tr id="analysis">
                                                    <td>Analysis</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='analysis'
                                                            id="showanalysismenu" name="showanalysismenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.analysis.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showanalysismenu' id="addanalysis"
                                                                name="addanalysis" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.analysis.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showanalysismenu' id="viewanalysis"
                                                                name="viewanalysis" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.analysis.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showanalysismenu' id="editanalysis"
                                                                name="editanalysis" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.analysis.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showanalysismenu' id="deleteanalysis"
                                                                name="deleteanalysis" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.analysis.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showanalysismenu' id="alldataanalysis"
                                                                name="alldataanalysis" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.leadmodule.leadownerperformance.add') == '1' || $user_id == 1)
                                                <tr id="leadownerperformance">
                                                    <td>Lead Owner Performance</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='leadownerperformance'
                                                            id="showleadownerperformancemenu" name="showleadownerperformancemenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leadownerperformance.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadownerperformancemenu' id="addleadownerperformance"
                                                                name="addleadownerperformance" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leadownerperformance.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadownerperformancemenu' id="viewleadownerperformance"
                                                                name="viewleadownerperformance" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leadownerperformance.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadownerperformancemenu' id="editleadownerperformance"
                                                                name="editleadownerperformance" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leadownerperformance.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadownerperformancemenu' id="deleteleadownerperformance"
                                                                name="deleteleadownerperformance" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.leadownerperformance.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showleadownerperformancemenu' id="alldataleadownerperformance"
                                                                name="alldataleadownerperformance" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.leadmodule.recentactivity.add') == '1' || $user_id == 1)
                                                <tr id="recentactivity">
                                                    <td>Recent Activity</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='recentactivity'
                                                            id="showrecentactivitymenu" name="showrecentactivitymenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.recentactivity.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showrecentactivitymenu' id="addrecentactivity"
                                                                name="addrecentactivity" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.recentactivity.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showrecentactivitymenu' id="viewrecentactivity"
                                                                name="viewrecentactivity" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.recentactivity.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showrecentactivitymenu' id="editrecentactivity"
                                                                name="editrecentactivity" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.recentactivity.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showrecentactivitymenu' id="deleterecentactivity"
                                                                name="deleterecentactivity" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.recentactivity.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showrecentactivitymenu' id="alldatarecentactivity"
                                                                name="alldatarecentactivity" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.leadmodule.calendar.add') == '1' || $user_id == 1)
                                                <tr id="calendar">
                                                    <td>Calendar</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='calendar'
                                                            id="showcalendarmenu" name="showcalendarmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.calendar.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcalendarmenu' id="addcalendar"
                                                                name="addcalendar" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.calendar.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcalendarmenu' id="viewcalendar"
                                                                name="viewcalendar" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.calendar.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcalendarmenu' id="editcalendar"
                                                                name="editcalendar" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.calendar.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcalendarmenu' id="deletecalendar"
                                                                name="deletecalendar" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.calendar.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcalendarmenu' id="alldatacalendar"
                                                                name="alldatacalendar" value="1">
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="leadmodulereset" data-module="lead"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset Lead Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="leadmodulesubmit" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('customersupport') && Session::get('customersupport') == 'yes') || $user_id == 1)
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
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15%">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="customersupportcheckboxes">
                                            @if (session('user_permissions.customersupportmodule.customersupportdashboard.add') == '1' || $user_id == 1)
                                                <tr id="customersupportdashboard">
                                                    <td>Customer Support Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='customersupportdashboard'
                                                            id="showcustomersupportdashboardmenu" name="showcustomersupportdashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupportdashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportdashboardmenu' id="addcustomersupportdashboard"
                                                                name="addcustomersupportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupportdashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportdashboardmenu' id="viewcustomersupportdashboard"
                                                                name="viewcustomersupportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupportdashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportdashboardmenu' id="editcustomersupportdashboard"
                                                                name="editcustomersupportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupportdashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportdashboardmenu' id="deletecustomersupportdashboard"
                                                                name="deletecustomersupportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupportdashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportdashboardmenu' id="alldatacustomersupportdashboard"
                                                                name="alldatacustomersupportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.customersupportmodule.customersupport.add') == '1' || $user_id == 1)
                                                <tr id="customersupport">
                                                    <td>Customer Support</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='customersupport' id="showcustomersupportmenu"
                                                            name="showcustomersupportmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportmenu'
                                                                id="addcustomersupport" name="addcustomersupport"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportmenu'
                                                                id="viewcustomersupport" name="viewcustomersupport"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportmenu'
                                                                id="editcustomersupport" name="editcustomersupport"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportmenu'
                                                                id="deletecustomersupport" name="deletecustomersupport"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showcustomersupportmenu'
                                                                id="alldatacustomersupport"
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="customersupportmodulereset"
                                                    data-module="customersupport" data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    data-original-title="Reset Customer Support Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="customersupportmodulesubmit"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('inventory') && Session::get('inventory') == 'yes') || $user_id == 1)
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
                                                        type="checkbox" id="inventoryallcheck" data-module="inventory"
                                                        class="allcheck"></th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15%">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inventorycheckboxes">
                                            @if (session('user_permissions.inventorymodule.inventorydashboard.add') == '1' || $user_id == 1)
                                                <tr id="inventorydashboard">
                                                    <td>Inventory Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='inventorydashboard'
                                                            id="showinventorydashboardmenu" name="showinventorydashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventorydashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorydashboardmenu' id="addinventorydashboard"
                                                                name="addinventorydashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventorydashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorydashboardmenu' id="viewinventorydashboard"
                                                                name="viewinventorydashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventorydashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorydashboardmenu' id="editinventorydashboard"
                                                                name="editinventorydashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventorydashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorydashboardmenu' id="deleteinventorydashboard"
                                                                name="deleteinventorydashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventorydashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorydashboardmenu' id="alldatainventorydashboard"
                                                                name="alldatainventorydashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.inventorymodule.product.add') == '1' || $user_id == 1)
                                                <tr id="product">
                                                    <td>Product</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='product'
                                                            id="showproductmenu" name="showproductmenu"
                                                            value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.product.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductmenu' id="addproduct"
                                                                name="addproduct" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.product.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductmenu' id="viewproduct"
                                                                name="viewproduct" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.product.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductmenu' id="editproduct"
                                                                name="editproduct" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.product.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductmenu' id="deleteproduct"
                                                                name="deleteproduct" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.product.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductmenu' id="alldataproduct"
                                                                name="alldataproduct" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td class="p-0" style="border-right: 0;">
                                                    <span class="btn expandsettingsbutton"
                                                        data-target="product_settings_rows">
                                                        <i class="ri ri-2x ri-arrow-right-circle-fill"></i>
                                                        Product Settings
                                                    </span>
                                                </td>
                                            </tr>

                                            @if (session('user_permissions.inventorymodule.productcategory.add') == '1' || $user_id == 1)
                                                <tr id="productcategory" class="product_settings_rows subsettingrows">
                                                    <td>Product Category</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='productcategory' id="showproductcategorymenu"
                                                            name="showproductcategorymenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcategory.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcategorymenu'
                                                                id="addproductcategory" name="addproductcategory"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcategory.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcategorymenu'
                                                                id="viewproductcategory" name="viewproductcategory"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcategory.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcategorymenu'
                                                                id="editproductcategory" name="editproductcategory"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcategory.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcategorymenu'
                                                                id="deleteproductcategory" name="deleteproductcategory"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcategory.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcategorymenu'
                                                                id="alldataproductcategory"
                                                                name="alldataproductcategory" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.inventorymodule.productcolumnmapping.add') == '1' || $user_id == 1)
                                                <tr id="productcolumnmapping"
                                                    class="product_settings_rows subsettingrows">
                                                    <td>Product Column Mapping</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='productcolumnmapping'
                                                            id="showproductcolumnmappingmenu"
                                                            name="showproductcolumnmappingmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcolumnmapping.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcolumnmappingmenu'
                                                                id="addproductcolumnmapping"
                                                                name="addproductcolumnmapping" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcolumnmapping.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcolumnmappingmenu'
                                                                id="viewproductcolumnmapping"
                                                                name="viewproductcolumnmapping" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcolumnmapping.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcolumnmappingmenu'
                                                                id="editproductcolumnmapping"
                                                                name="editproductcolumnmapping" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcolumnmapping.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcolumnmappingmenu'
                                                                id="deleteproductcolumnmapping"
                                                                name="deleteproductcolumnmapping" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.productcolumnmapping.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showproductcolumnmappingmenu'
                                                                id="alldataproductcolumnmapping"
                                                                name="alldataproductcolumnmapping" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.inventorymodule.purchase.add') == '1' || $user_id == 1)
                                                <tr id="purchase">
                                                    <td>Purchase</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='purchase'
                                                            id="showpurchasemenu" name="showpurchasemenu"
                                                            value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.purchase.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showpurchasemenu' id="addpurchase"
                                                                name="addpurchase" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.purchase.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showpurchasemenu' id="viewpurchase"
                                                                name="viewpurchase" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.purchase.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showpurchasemenu' id="editpurchase"
                                                                name="editpurchase" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.purchase.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showpurchasemenu' id="deletepurchase"
                                                                name="deletepurchase" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.purchase.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showpurchasemenu' id="alldatapurchase"
                                                                name="alldatapurchase" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.inventorymodule.inventory.add') == '1' || $user_id == 1)
                                                <tr id="inventory">
                                                    <td>Inventory</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='inventory' id="showinventorymenu"
                                                            name="showinventorymenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventory.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorymenu' id="addinventory"
                                                                name="addinventory" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventory.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorymenu' id="viewinventory"
                                                                name="viewinventory" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventory.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorymenu' id="editinventory"
                                                                name="editinventory" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventory.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorymenu' id="deleteinventory"
                                                                name="deleteinventory" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.inventory.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showinventorymenu' id="alldatainventory"
                                                                name="alldatainventory" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.inventorymodule.supplier.add') == '1' || $user_id == 1)
                                                <tr id="supplier">
                                                    <td>Supplier</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='supplier'
                                                            id="showsuppliermenu" name="showsuppliermenu"
                                                            value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.supplier.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showsuppliermenu' id="addsupplier"
                                                                name="addsupplier" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.supplier.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showsuppliermenu' id="viewsupplier"
                                                                name="viewsupplier" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.supplier.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showsuppliermenu' id="editsupplier"
                                                                name="editsupplier" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.supplier.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showsuppliermenu' id="deletesupplier"
                                                                name="deletesupplier" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.supplier.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showsuppliermenu' id="alldatasupplier"
                                                                name="alldatasupplier" value="1">
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="inventorymodulereset"
                                                    data-module="inventory" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Reset Inventory Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="inventorymodulesubmit"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('reminder') && Session::get('reminder') == 'yes') || $user_id == 1)
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
                                                        type="checkbox" id="reminderallcheck" data-module="reminder"
                                                        class="allcheck"></th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15%">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="remindercheckboxes">
                                            @if (session('user_permissions.remindermodule.reminderdashboard.add') == '1' || $user_id == 1)
                                                <tr id="reminderdashboard">
                                                    <td>Reminder Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='reminderdashboard'
                                                            id="showreminderdashboardmenu" name="showreminderdashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminderdashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreminderdashboardmenu' id="addreminderdashboard"
                                                                name="addreminderdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminderdashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreminderdashboardmenu' id="viewreminderdashboard"
                                                                name="viewreminderdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminderdashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreminderdashboardmenu' id="editreminderdashboard"
                                                                name="editreminderdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminderdashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreminderdashboardmenu' id="deletereminderdashboard"
                                                                name="deletereminderdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminderdashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreminderdashboardmenu' id="alldatareminderdashboard"
                                                                name="alldatareminderdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.remindermodule.reminder.add') == '1' || $user_id == 1)
                                                <tr id="reminder">
                                                    <td>Reminder</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='reminder'
                                                            id="showremindermenu" name="showremindermenu"
                                                            value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminder.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindermenu' id="addreminder"
                                                                name="addreminder" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminder.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindermenu' id="viewreminder"
                                                                name="viewreminder" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminder.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindermenu' id="editreminder"
                                                                name="editreminder" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminder.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindermenu' id="deletereminder"
                                                                name="deletereminder" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.reminder.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindermenu' id="alldatareminder"
                                                                name="alldatareminder" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.remindermodule.remindercustomer.add') == '1' || $user_id == 1)
                                                <tr id="remindercustomer">
                                                    <td>Reminder Customer</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu"
                                                            data-value='remindercustomer' id="showremindercustomermenu"
                                                            name="showremindercustomermenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.remindercustomer.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindercustomermenu'
                                                                id="addremindercustomer" name="addremindercustomer"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.remindercustomer.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindercustomermenu'
                                                                id="viewremindercustomer" name="viewremindercustomer"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.remindercustomer.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindercustomermenu'
                                                                id="editremindercustomer" name="editremindercustomer"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.remindercustomer.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindercustomermenu'
                                                                id="deleteremindercustomer"
                                                                name="deleteremindercustomer" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.remindermodule.remindercustomer.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showremindercustomermenu'
                                                                id="alldataremindercustomer"
                                                                name="alldataremindercustomer" value="1">
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="remindermodulereset" data-module="reminder"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset Reminder Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="remindermodulesubmit" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('report') && Session::get('report') == 'yes') || $user_id == 1)
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
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15%">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reportcheckboxes">
                                            @if (session('user_permissions.reportmodule.reportdashboard.add') == '1' || $user_id == 1)
                                                <tr id="reportdashboard">
                                                    <td>Report Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='reportdashboard'
                                                            id="showreportdashboardmenu" name="showreportdashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.reportdashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportdashboardmenu' id="addreportdashboard"
                                                                name="addreportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.reportdashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportdashboardmenu' id="viewreportdashboard"
                                                                name="viewreportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.reportdashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportdashboardmenu' id="editreportdashboard"
                                                                name="editreportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.reportdashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportdashboardmenu' id="deletereportdashboard"
                                                                name="deletereportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.reportdashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportdashboardmenu' id="alldatareportdashboard"
                                                                name="alldatareportdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.reportmodule.report.add') == '1' || $user_id == 1)
                                                <tr id="report">
                                                    <td rowspan="3">report</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='report'
                                                            id="showreportmenu" name="showreportmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.report.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportmenu' id="addreport"
                                                                name="addreport" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.report.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportmenu' id="viewreport"
                                                                name="viewreport" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.report.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showreportmenu' id="editreport"
                                                                name="editreport" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.reportmodule.report.delete') == '1' || $user_id == 1)
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
                                                            data-value='showreportmenu' id="logreport"
                                                            name="logreport" value="1">
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-12">
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="reportmodulereset" data-module="report"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset report Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="reportmodulesubmit" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('blog') && Session::get('blog') == 'yes') || $user_id == 1)
                            <div class="iq-card">
                                <div class="iq-card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">blog Modules</h4>
                                    </div>
                                </div>
                                <div class="iq-card-body">
                                    <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                        <thead>
                                            <tr>
                                                <th colspan="7" class="text-right">
                                                    <b>Select All </b> 
                                                    <input type="checkbox" id="blogallcheck" data-module="blog" class="allcheck">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15%">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="blogcheckboxes">
                                            @if (session('user_permissions.blogmodule.blogdashboard.add') == '1' || $user_id == 1)
                                                <tr id="blogdashboard">
                                                    <td>Blog Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='blogdashboard'
                                                            id="showblogdashboardmenu" name="showblogdashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blogdashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogdashboardmenu' id="addblogdashboard"
                                                                name="addblogdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blogdashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogdashboardmenu' id="viewblogdashboard"
                                                                name="viewblogdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blogdashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogdashboardmenu' id="editblogdashboard"
                                                                name="editblogdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blogdashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogdashboardmenu' id="deleteblogdashboard"
                                                                name="deleteblogdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blogdashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogdashboardmenu' id="alldatablogdashboard"
                                                                name="alldatablogdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.blogmodule.blog.add') == '1' || $user_id == 1)
                                                <tr id="blog">
                                                    <td rowspan="3">blog</td>
                                                    <td rowspan="3">
                                                        <input type="checkbox" class="clickmenu" data-value='blog'
                                                            id="showblogmenu" name="showblogmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blog.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogmenu' id="addblog"
                                                                name="addblog" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blog.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogmenu' id="viewblog"
                                                                name="viewblog" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blog.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogmenu' id="editblog"
                                                                name="editblog" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blog.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogmenu' id="deleteblog"
                                                                name="deleteblog" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.blogmodule.blog.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showblogmenu' id="alldatablog"
                                                                name="alldatablog" value="1">
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="blogmodulereset" data-module="blog"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset blog Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="blogmodulesubmit" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ((Session::has('logistic') && Session::get('logistic') == 'yes') || $user_id == 1)
                            <div class="iq-card">
                                <div class="iq-card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">Logistic Module</h4>
                                    </div>
                                </div>
                                <div class="iq-card-body">
                                    <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                                        <thead>
                                            <tr>
                                                <th colspan="7" class="text-right">
                                                    <b>Select All </b>
                                                    <input type="checkbox" id="logisticallcheck" data-module="logistic"
                                                        class="allcheck">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Menus</th>
                                                <th scope="col" style="width:15% ;">Show/Hide</th>
                                                <th scope="col">Add</th>
                                                <th scope="col">View</th>
                                                <th scope="col">Edit</th>
                                                <th scope="col">Delete</th>
                                                <th scope="col">All Record</th>
                                            </tr>
                                        </thead>
                                        <tbody id="logisticcheckboxes">
                                            @if (session('user_permissions.logisticmodule.logisticdashboard.add') == '1' || $user_id == 1)
                                                <tr id="logisticdashboard">
                                                    <td>Logistic Dashboard</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='logisticdashboard'
                                                            id="showlogisticdashboardmenu" name="showlogisticdashboardmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticdashboard.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticdashboardmenu' id="addlogisticdashboard"
                                                                name="addlogisticdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticdashboard.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticdashboardmenu' id="viewlogisticdashboard"
                                                                name="viewlogisticdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticdashboard.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticdashboardmenu' id="editlogisticdashboard"
                                                                name="editlogisticdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticdashboard.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticdashboardmenu' id="deletelogisticdashboard"
                                                                name="deletelogisticdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticdashboard.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticdashboardmenu' id="alldatalogisticdashboard"
                                                                name="alldatalogisticdashboard" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr> 
                                            @endif

                                            @if (session('user_permissions.logisticmodule.consignorcopy.add') == '1' || $user_id == 1)
                                                <tr id="consignorcopy">
                                                    <td>Consignor Copy</td>
                                                    <td>
                                                        <input type="checkbox" class="clickmenu" data-value='consignorcopy'
                                                            id="showconsignorcopymenu" name="showconsignorcopymenu"
                                                            value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopy.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopymenu' id="addconsignorcopy"
                                                                name="addconsignorcopy" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopy.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopymenu' id="viewconsignorcopy"
                                                                name="viewconsignorcopy" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopy.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopymenu' id="editconsignorcopy"
                                                                name="editconsignorcopy" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopy.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopymenu' id="deleteconsignorcopy"
                                                                name="deleteconsignorcopy" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopy.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopymenu' id="alldataconsignorcopy"
                                                                name="alldataconsignorcopy" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif 
          
                                            @if (session('user_permissions.logisticmodule.logisticsettings.add') == '1' || $user_id == 1)
                                                <tr id="logisticsettings" class="logistic_settings_rows">
                                                    <td>
                                                        <span class="btn expandsettingsbutton" data-target="logistic_other_settings_rows">
                                                            <i class="ri ri-2x ri-arrow-right-circle-fill"></i>
                                                            logistic/Settings
                                                        </span>
                                                    </td>
                                                    <td> 
                                                        <input type="checkbox" class="clickmenu" data-value='logisticsettings' id="showlogisticsettingsmenu" name="showlogisticsettingsmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticsettings.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticsettingsmenu'
                                                                id="addlogisticsettings" name="addlogisticsettings"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticsettings.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticsettingsmenu'
                                                                id="viewlogisticsettings" name="viewlogisticsettings"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticsettings.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticsettingsmenu'
                                                                id="editlogisticsettings" name="editlogisticsettings"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticsettings.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticsettingsmenu'
                                                                id="deletelogisticsettings" name="deletelogisticsettings"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.logisticsettings.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showlogisticsettingsmenu'
                                                                id="alldatalogisticsettings"
                                                                name="alldatalogisticsettings" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.logisticmodule.consignmentnotenumbersettings.add') == '1' || $user_id == 1)
                                                <tr id="consignmentnotenumbersettings"
                                                    class="logistic_other_settings_rows subsettingrows">
                                                    <td>Consignment Note Number Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='consignmentnotenumbersettings'
                                                            id="showconsignmentnotenumbersettingsmenu"
                                                            name="showconsignmentnotenumbersettingsmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignmentnotenumbersettings.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignmentnotenumbersettingsmenu'
                                                                id="addconsignmentnotenumbersettings"
                                                                name="addconsignmentnotenumbersettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignmentnotenumbersettings.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignmentnotenumbersettingsmenu'
                                                                id="viewconsignmentnotenumbersettings"
                                                                name="viewconsignmentnotenumbersettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignmentnotenumbersettings.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignmentnotenumbersettingsmenu'
                                                                id="editconsignmentnotenumbersettings"
                                                                name="editconsignmentnotenumbersettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignmentnotenumbersettings.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignmentnotenumbersettingsmenu'
                                                                id="deleteconsignmentnotenumbersettings"
                                                                name="deleteconsignmentnotenumbersettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignmentnotenumbersettings.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignmentnotenumbersettingsmenu'
                                                                id="alldataconsignmentnotenumbersettings"
                                                                name="alldataconsignmentnotenumbersettings" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.logisticmodule.consignorcopytandcsettings.add') == '1' || $user_id == 1)
                                                <tr id="consignorcopytandcsettings"
                                                    class="logistic_other_settings_rows subsettingrows">
                                                    <td>Consignor Copy T&C Settings</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='consignorcopytandcsettings'
                                                            id="showconsignorcopytandcsettingsmenu"
                                                            name="showconsignorcopytandcsettingsmenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopytandcsettings.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopytandcsettingsmenu'
                                                                id="addconsignorcopytandcsettings"
                                                                name="addconsignorcopytandcsettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopytandcsettings.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopytandcsettingsmenu'
                                                                id="viewconsignorcopytandcsettings"
                                                                name="viewconsignorcopytandcsettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopytandcsettings.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopytandcsettingsmenu'
                                                                id="editconsignorcopytandcsettings"
                                                                name="editconsignorcopytandcsettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopytandcsettings.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopytandcsettingsmenu'
                                                                id="deleteconsignorcopytandcsettings"
                                                                name="deleteconsignorcopytandcsettings" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignorcopytandcsettings.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignorcopytandcsettingsmenu'
                                                                id="alldataconsignorcopytandcsettings"
                                                                name="alldataconsignorcopytandcsettings" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif  

                                            @if (session('user_permissions.logisticmodule.consignee.add') == '1' || $user_id == 1)
                                                <tr id="consignee">
                                                    <td>Consignee</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='consignee' id="showconsigneemenu"
                                                            name="showconsigneemenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignee.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsigneemenu'
                                                                id="addconsignee" name="addconsignee"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignee.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsigneemenu'
                                                                id="viewconsignee" name="viewconsignee"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignee.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsigneemenu'
                                                                id="editconsignee" name="editconsignee"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignee.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsigneemenu'
                                                                id="deleteconsignee"
                                                                name="deleteconsignee" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignee.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsigneemenu'
                                                                id="alldataconsignee"
                                                                name="alldataconsignee" value="1">
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                            @if (session('user_permissions.logisticmodule.consignor.add') == '1' || $user_id == 1)
                                                <tr id="consignor">
                                                    <td>consignor</td>
                                                    <td> <input type="checkbox" class="clickmenu"
                                                            data-value='consignor' id="showconsignormenu"
                                                            name="showconsignormenu" value="1">
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignor.add') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignormenu'
                                                                id="addconsignor" name="addconsignor"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignor.view') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignormenu'
                                                                id="viewconsignor" name="viewconsignor"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignor.edit') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignormenu'
                                                                id="editconsignor" name="editconsignor"
                                                                value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignor.delete') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignormenu'
                                                                id="deleteconsignor"
                                                                name="deleteconsignor" value="1">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.logisticmodule.consignor.alldata') == '1' || $user_id == 1)
                                                            <input type="checkbox" class="clicksubmenu"
                                                                data-value='showconsignormenu'
                                                                id="alldataconsignor"
                                                                name="alldataconsignor" value="1">
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
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
                                                    class="btn btn-secondary float-right cancelbtn">
                                                    Cancel
                                                </button>
                                                <button type="button" id="logisticmodulereset" data-module="logistic"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset Logistic Module"
                                                    class="btn iq-bg-danger float-right resetbtn mr-2">
                                                    Reset
                                                </button>
                                                <button type="submit" id="logisticmodulsubmit" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Save"
                                                    class="btn btn-primary float-right my-0 submitBtn">
                                                    Save
                                                </button>
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
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            loaderhide();

            //for checkboxes reset 
            $(document).on('click', '.resetbtn', function() {
                if (userrp == 1) {
                    var module = $(this).data('module');
                    $(`#${module}checkboxes input[type="checkbox"] , #${module}allcheck`).prop(
                        'checked', false);

                    if (module == 'report') {
                        $('#assignedto option').prop('selected', false);
                        $('#assignedto').multiselect('refresh');
                        $('#assignedto').multiselect('rebuild');
                    }
                }

            })
            
            var userrp = "{{ session('user_permissions.adminmodule.userpermission.add') }}";

            if (userrp != 1) {
                $('.permission-row input[type="checkbox"]').attr('disabled',
                    true); // permission related all checkoboxes disable if user has not permission
            }

            $('.subsettingrows').slideUp();

            $('.expandsettingsbutton').on('click', function() {
                let targetrows = $(this).data('target');
                let isInvoiceSettings = targetrows === 'invoice_settings_rows';
                let isQuotationSettings = targetrows === 'quotation_settings_rows';
                let isLogistitcSettings = targetrows === 'logistitc_settings_rows';
                $('tr.' + targetrows).slideToggle();
                // Toggle 'special-color' class on both the button and the target rows
                $(this).toggleClass('special-color'); // Add/remove class on the button
                $('tr.' + targetrows).toggleClass('special-color'); // Add/remove class on the target rows
                $(this).find('i').toggleClass('ri-arrow-right-circle-fill ri-arrow-down-circle-fill');

                // If 'Invoice Settings' is collapsed, also collapse 'Invoice Other Settings'
                if (isInvoiceSettings) {
                    let isInvoiceSettingsVisible = $('tr.' + targetrows).hasClass("special-color");
                    if (!
                        isInvoiceSettingsVisible
                    ) { // If Invoice Settings is collapsed, collapse Invoice Other Settings as well
                        $('tr.invoice_other_settings_rows').slideUp();
                        $('tr.invoice_other_settings_rows').removeClass('special-color');
                        $('.expandsettingsbutton[data-target="invoice_other_settings_rows"]').find('i')
                            .removeClass('ri-arrow-down-circle-fill').addClass(
                                'ri-arrow-right-circle-fill');
                    }
                }

                // If 'Quotation Settings' is collapsed, also collapse 'Quotation Other Settings'
                else if (isQuotationSettings) {
                    let isQuotationSettingsVisible = $('tr.' + targetrows).hasClass("special-color");
                    if (!
                        isQuotationSettingsVisible
                    ) { // If Invoice Settings is collapsed, collapse Invoice Other Settings as well
                        $('tr.quotation_other_settings_rows').slideUp();
                        $('tr.quotation_other_settings_rows').removeClass('special-color');
                        $('.expandsettingsbutton[data-target="quotation_other_settings_rows"]').find('i')
                            .removeClass('ri-arrow-down-circle-fill').addClass(
                                'ri-arrow-right-circle-fill');
                    }
                }

                // If 'Logistic Settings' is collapsed, also collapse 'Logistic Other Settings'
                else if (islogisticsettingss) {
                    let islogisticsettingssVisible = $('tr.' + targetrows).hasClass("special-color");
                    if (!
                        islogisticsettingssVisible
                    ) { // If Invoice Settings is collapsed, collapse Invoice Other Settings as well
                        $('tr.logistic_other_settings_rows').slideUp();
                        $('tr.logistic_other_settings_rows').removeClass('special-color');
                        $('.expandsettingsbutton[data-target="logistic_other_settings_rows"]').find('i')
                            .removeClass('ri-arrow-down-circle-fill').addClass(
                                'ri-arrow-right-circle-fill');
                    }
                }
            });

            // function for get user record - it will return promise
            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.invoiceuserindex') }}",
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
                        if (userrp != 1) {
                            $('.permission-row input[type="checkbox"]').attr('disabled', true);
                        }
                    } else if (userDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: userDataResponse.message
                        });
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                    }

                    loaderhide();

                    // Further code execution after successful AJAX calls and HTML appending


                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: "error",
                        title: "An error occurred while initializing"
                    });

                }
            }

            initialize();

            $('#assignedto').multiselect({
                nonSelectedText: 'Select User',
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });


             // check/uncheck all checkboxes module wise
            $(document).on('click', '.allcheck', function() {
                if (userrp == 1) {
                    var module = $(this).data('module');
                    if (!$(`#${module}allcheck`).prop('checked')) {
                        $(`#${module}checkboxes input[type="checkbox"]`).prop('checked', false);
                        if (module == 'report') {
                            $('#assignedto option').prop('selected', false);
                        }
                    } else {
                        $(`#${module}checkboxes input[type="checkbox"]`).prop('checked', $(this).prop(
                            'checked'));
                        if (module == 'report') {
                            $('#reportcheckboxes input[type="checkbox"]').prop('checked', $(this).prop(
                                'checked'));
                            $('#assignedto option').prop('selected', true);
                        }
                    }

                    if (module == 'report') {
                        $('#assignedto').multiselect('refresh');
                        $('#assignedto').multiselect('rebuild');
                    }
                }
            })


            // redirect to user list page if click any cancel btn
            $(document).on('click', '.cancelbtn', function() {
                loadershow();
                window.location.href = "{{ route('admin.userrolepermission') }}"
            });


            // submit bank form data
            $('#userrolepermissionform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('userrolepermission.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location =
                            "{{ route('admin.userrolepermission') }}"; // after succesfully data submit redirect on list page
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });;
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
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
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                })
            });
        });
    </script>
@endpush
