@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')

@section('page_title')
    {{ config('app.name') }} - Invoice Other Settings
@endsection
@section('title')
    Other settings
@endsection
@section('style')
    <style>
        .my-custom-scrollbar {
            position: relative;
            height: 200px;
            overflow: auto;
        }

        .table-wrapper-scroll-y {
            display: block;
        }

        .note-editable * {
            margin: 0;
            padding: 0;
        }

        .note-editable ul,
        .note-editable ol {
            padding-left: 20px;
        }

        .note-editable li {
            margin-bottom: 5px;
        }

        #tcdiv * {
            margin: 0;
            padding: 0;
        }
    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            @if (session('user_permissions.invoicemodule.invoicenumbersetting.edit') == 1)
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Invoice Number Settings"
                            type="button" id="editinvoicenumberBtn"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0 patternsettingBtn">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Invoice Number Settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body" id="">
                                <form id="invoicenumberpatternform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-4">
                                                <label for="invoicepattern"> Invoice Pattern : </label>
                                                <select name="invoicepattern" class="form-control" id="invoicepattern">
                                                    <option value="" selected disabled>Select Input Type</option>
                                                    <option value="text">Text</option>
                                                    <option value="year">Year</option>
                                                    <option value="nextyear">Next Year</option>
                                                    <option value="month">Month</option>
                                                    <option value="date">Date</option>
                                                    <option value="customerid">Customer Id</option>
                                                    <option value="ai">Auto Increment According To Invoice</option>
                                                    <option value="cidai">Auto Increment According To Customer</option>
                                                </select>
                                                <span class="text-info">You can use auto increment input only once</span>
                                                <span class="error-msg" id="error-overdue_day" style="color: red"></span><br>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="patterntype">Invoice Pattern Type</label>
                                                <select id="patterntype" class="form-control" required>
                                                    <option value="domestic">Domestic</option>
                                                    <option value="global">Global</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-row" id="invoicenumberinputs">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-sm-12">
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Cancel" id="cancelnumberpattern"
                                                        class="btn btn-secondary float-right">Cancel</button>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Reset Inputs" id="resetpatterninput"
                                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Save"
                                                        class="btn btn-primary float-right my-0">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <hr>
                                <table>
                                    <tr>
                                        <td>Domestic Invoice Pattern :</td>
                                        <td>
                                            <span id="domesticinvoicepattern"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Global Invoice Pattern :</td>
                                        <td>
                                            <span id="globalinvoicepattern"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Manual Invoice Number :</td>
                                        <td>
                                            <span id="view_manual_inv_number">
                                                <div class="custom-control custom-switch custom-control-inline">
                                                    <input type="checkbox" class="custom-control-input" checked=""
                                                        id="manualinvnumberswitch" value="yes">
                                                    <label class="custom-control-label" for="manualinvnumberswitch">Yes</label>
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Manual Invoice Date :</td>
                                        <td>
                                            <span id="view_manual_inv_date">
                                                <div class="custom-control custom-switch custom-control-inline">
                                                    <input type="checkbox" class="custom-control-input" checked=""
                                                        id="manualinvdateswitch" value="yes">
                                                    <label class="custom-control-label" for="manualinvdateswitch">Yes</label>
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
            @if (session('user_permissions.invoicemodule.invoicetandcsetting.edit') == 1)
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                        <button type="btn" id="newtcBtn" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Add New Terms and Conditions"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-add-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Invoice Terms & conditions Settings </h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="tcform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-6">
                                                <input type="hidden" name="edit_id" class="form-control" id="edit_id"
                                                    required />
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <textarea class="form-control" name='t_and_c' id="t_and_c" rows="2"
                                                    placeholder="Enter your Invoice Terms & Conditions..."></textarea>
                                                <span class="error-msg" id="error-t_and_c" style="color: red"></span>
                                            </div>
                                            <div class="col-sm-6 text-right text-md-left">
                                                <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Submit Terms & Conditions"
                                                    class="btn btn-primary">Submit</button>
                                                <button type="reset" id="resettcBtn" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Reset Terms & Conditions"
                                                    class="btn iq-bg-danger mr-2">Reset</button>
                                                <button type="btn" id="canceltcBtn" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Cancel"
                                                    class="btn btn-secondary">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <hr>
                                <div class="table-wrapper-scroll-y my-custom-scrollbar">
                                    <table id="data"
                                        class="table  table-bordered display table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>Terms & Conditions</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabledata">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="container-fluid">
            <div class="row">
                @if (session('user_permissions.invoicemodule.invoicestandardsetting.edit') == 1)
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Other Settings"
                            type="button" id="editoverdueday"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Other Settings </h4>
                                </div>
                            </div>
                            <div class="iq-card-body" id="appendform">
                                <form id="overduedaysform" style="display: none">
                                    @csrf

                                    <input type="hidden" name="token" value="{{ session('api_token') }}">
                                    <input type="hidden" name="user_id" value="{{ $user_id }}">
                                    <input type="hidden" name="company_id" value="{{ $company_id }}">

                                    <div class="form-row">

                                        <div class="col-sm-6 mb-2">
                                            Invoice Overdue Days :
                                            <input type="number" id="overdue_day" name="overdue_day" class="form-control"
                                                min="1" required>
                                            <span class="error-msg text-danger" id="error-overdue_day"></span>
                                        </div>

                                        <div class="col-sm-6 mb-2">
                                            Year Starting Date :
                                            <input type="date" id="year_start_date" name="year_start_date" class="form-control"
                                                required>
                                            <span class="error-msg text-danger" id="error-year_start_date"></span>
                                        </div>

                                        <div class="col-sm-6 mb-2">
                                            No. Of Blank Rows :
                                            <input type="number" min="0" id="no_of_blank_rows" name="no_of_blank_rows"
                                                class="form-control" required>
                                            <span class="error-msg text-danger" id="error-no_of_blank_rows"></span>
                                        </div>

                                        <div class="col-sm-6 mb-2 mt-4">
                                            Gods Name :

                                            <!-- IMPORTANT: hidden input -->
                                            <input type="hidden" name="god_name" value="0">

                                            <div
                                                class="custom-control custom-control-inline custom-switch custom-switch-text d-inline">
                                                <input type="checkbox" class="custom-control-input" id="godnameswith"
                                                    name="god_name" value="1">
                                                <label class="custom-control-label" for="godnameswith" data-on-label="On"
                                                    data-off-label="Off"></label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="mt-3">
                                        <button type="button" id="othersetting-cancelbtn"
                                            class="btn btn-secondary float-right">Cancel</button>

                                        <button type="reset" class="btn iq-bg-danger float-right mr-2">Reset</button>

                                        <button type="submit" class="btn btn-primary float-right">Update</button>
                                    </div>

                                    <hr>
                                </form>

                                Current Invoice Overdue Days : <span id="overduedays"></span> <br>
                                Year Starting Date : <span id="yearstartdate"></span> <br>
                                Blank Rows(Invoice PDF) : <span id="noofblankrows"></span> <br>
                                God Name Show / Hide :
                                <span id="viewgst">
                                    <div class="custom-control custom-switch custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" disabled
                                            id="god_namedisabledswitch">
                                        <label class="custom-control-label" for="god_namedisabledswitch" id="godnamestatus">
                                        </label>
                                    </div>
                                </span>

                            </div>
                        </div>
                    </div>
                @endif
                @if (session('user_permissions.invoicemodule.invoicegstsetting.edit') == 1)
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit GST Settings"
                            type="button" id="editgstsettings"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Gst Settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body" id="gstappendform">
                                <form id="gstsettingsform" style="display: none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-6">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" placeholder="token" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                SGST : <input type="number" id="sgst" name='sgst' class="form-control"
                                                    placeholder="SGST" min="1" step="0.01" required />
                                                <span class="error-msg" id="error-sgst" style="color: red"></span><br>
                                            </div>
                                            <div class="col-sm-6">
                                                CGST : <input type="number" id="cgst" name='cgst' class="form-control"
                                                    placeholder="CGST" step="0.01" required />
                                                <span class="error-msg" id="error-cgst" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="form-row mt-1">
                                            <div class="col-sm-6">
                                                Combined GST : <div
                                                    class="custom-control custom-switch custom-switch-text custom-control-inline">
                                                    <div class="custom-switch-inner">
                                                        <p class="mb-0">
                                                            <input type="checkbox" name="gst" class="custom-control-input"
                                                                id="gstswitch" value="1">
                                                            <label class="custom-control-label" for="gstswitch"
                                                                data-on-label="On" data-off-label="Off">
                                                            </label>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-sm-12 mt-2">
                                                    <button type="button" id="gst-cancelbtn" data-toggle="tooltip"
                                                        data-placement="bottom" data-original-title="Cancel"
                                                        class="btn btn-secondary float-right">Cancel</button>
                                                    <button type="reset" id="gstsettingreset" data-toggle="tooltip"
                                                        data-placement="bottom" data-original-title="Reset GSt Settings"
                                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Update GST Settings"
                                                        class="btn btn-primary float-right my-0">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </form>
                                SGST : <span id="viewsgst"> </span> <br>
                                CGST : <span id="viewcgst"> </span> <br>
                                Combined GST : <span id="viewgst">
                                    <div class="custom-control custom-switch custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" disabled="" checked=""
                                            id="gstdisabledswitch">
                                        <label class="custom-control-label" for="gstdisabledswitch">active</label>
                                    </div>
                                </span> <br>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.edit') == 1)
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Where To Start Customer ID"
                            type="button" id="editcustomeridBtn"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Customer ID settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="cidform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-3">
                                                <input type="hidden" name="edit_id" class="form-control" id="edit_id"
                                                    required />
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="customer_id">Customer Id</label>
                                                <input type="number" class="form-control" min="0" name="customer_id"
                                                    id="customer_id">
                                                <span class="error-msg" id="error-customer_id" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="btn" id="cancelcustomeridBtn" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Cancel"
                                                    class="btn btn-secondary float-right">Cancel</button>
                                                <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset"
                                                    class="btn iq-bg-danger float-right mr-2">Reset</button>
                                                <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Save"
                                                    class="btn btn-primary float-right my-0">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                Customer Id : <span id="startcid"> </span>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('user_permissions.invoicemodule.invoicecustomeridsetting.edit') == 1)
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Form Settings" type="button"
                            id="editcustomerformsettingsBtn"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Form Settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="customerformsettingform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-3">
                                                <input type="hidden" name="edit_id" class="form-control" id="edit_id"
                                                    required />
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="customer_type">Custom Drop Down</label>

                                                <div class="checkbox-group">
                                                    <label>
                                                        <input type="checkbox" name="customer_type[]" value="invoice" checked>
                                                        invoice
                                                    </label>

                                                    <label>
                                                        <input type="checkbox" name="customer_type[]" value="consignor">
                                                        Consignors
                                                    </label>

                                                    <label>
                                                        <input type="checkbox" name="customer_type[]" value="consignee">
                                                        Consignees
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" name="customer_type[]" value="quotation" checked>
                                                        Quotation
                                                    </label>

                                                </div>

                                                <span class="error-msg" id="error-customer_id" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="btn" id="cancelcustomeridBtn" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Cancel"
                                                    class="btn btn-secondary float-right">Cancel</button>
                                                <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset"
                                                    class="btn iq-bg-danger float-right mr-2">Reset</button>
                                                <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Save"
                                                    class="btn btn-primary float-right my-0">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                Custom Drop Down <span id="startformsetting"> </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('ajax')

    @isset($message)
        <script>
            // if  company has not any invoice number pattern so user will be redirect here when he click on create invoice link
            alert('You have not invoice number pattern. Please first make invoice number pattern!');
        </script>
    @endisset

    <script>
        $('document').ready(function () {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data


            /*
             * overview  ----------------
             * initialize summernote editor
             * overdue day and year start settings
             * gst settings 
             * terms and conditions settings
             * invoice number pattern settings
             * customer id settings
             */

            // intialize summernote editor for terms and conditions
            $('#t_and_c').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['table']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                placeholder: 'Add Notes',
                tabsize: 2,
                height: 100,
            });


            loaderhide();

            /*
             *overdue and year start settings code start
             */
            var overdueday = '';

            // get ouverdue days , year start from , sgst , cgst , gst , invoice number(manually) , invoice date (manually) etc.. setttings function
            function getoverduedays() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('getoverduedays.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.overdueday != '') {
                            var data = response.overdueday[0];
                            customer_dropdown = data['customer_dropdown'];

                            document.querySelectorAll('input[name="customer_type[]"]').forEach((checkbox) => {
                                if (customer_dropdown.includes(checkbox.value)) {
                                    checkbox.checked = true;
                                }
                            });
                            let names = JSON.parse(customer_dropdown);
                            $('#startformsetting').html(`<b>${names.join(', ')}</b> `);
                            overdueday = data['overdue_day'];
                            year_start = data['year_start'];
                            no_of_blank_rows = data['no_of_blank_row'];
                            sgst = data['sgst'];
                            cgst = data['cgst'];
                            gst = data['gst'];
                            invoice_number = data['invoice_number'];
                            invoice_date = data['invoice_date'];


                            $('#overduedays').html(`<b>${overdueday}</b> `);
                            $('#yearstartdate').html(`<b>${year_start}</b> `);
                            $('#noofblankrows').html(`<b>${no_of_blank_rows}</b> `);
                            $('#overdue_day').val(overdueday);
                            $('#year_start_date').val(year_start);
                            $('#no_of_blank_rows').val(no_of_blank_rows);
                            $('#overdue_day').attr('data-id', data['id']);
                            god_name = data['god_name_show/hide'];
                            $('#godnameswith').prop('checked', data.god_name == 1);
                            $('#god_namedisabledswitch').prop('checked', data.god_name == 1);

                            $('#godnamestatus').text(god_name == 1 ? 'active' : 'inactive');
                            $('#viewsgst').html(`<b>${sgst}</b> `);
                            $('#viewcgst').html(`<b>${cgst}</b> `);
                            $('#sgst').val(sgst);
                            $('#cgst').val(cgst);
                            $('#sgst').attr('data-id', data['id']);

                            if (gst == 1) {
                                $('#gstswitch').prop('checked', true); // Check the checkbox
                                $('#gstdisabledswitch').prop('checked', true); // Check the checkbox
                            } else {
                                $('#gstswitch').prop('checked', false); // Uncheck the checkbox
                                $('#gstdisabledswitch').prop('checked', false); // Uncheck the checkbox
                            }
                            if (god_name == 1) {
                                $('#godnameswith').prop('checked', true); // Check the checkbox
                                $('#god_namedisabledswitch').prop('checked', true); // Check the checkbox
                            } else {
                                $('#godnameswith').prop('checked', false); // Uncheck the checkbox
                                $('#god_namedisabledswitch').prop('checked', false); // Uncheck the checkbox
                            }
                            if (invoice_number == 1) {
                                $('#manualinvnumberswitch').prop('checked', true); // Check the checkbox
                            } else {
                                $('#manualinvnumberswitch').prop('checked',
                                    false); // Uncheck the checkbox
                            }

                            if (invoice_date == 1) {
                                $('#manualinvdateswitch').prop('checked', true); // Check the checkbox
                            } else {
                                $('#manualinvdateswitch').prop('checked',
                                    false); // Uncheck the checkbox
                            }
                            $('#startcid').html(`<b>${data.customer_id}</b>`);

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#overduedays').html(`<b>Not set</b> `);
                            $('#yearstartdate').html(`<b>Not set</b>`);
                            $('#viewsgst').html(`<b>Not set</b>`);
                            $('#viewcgst').html(`<b>Not set</b>`);
                            $('#gstdisabledswitch').prop('checked', false); // Uncheck the checkbox
                            $('#god_namedisabledswitch').prop('checked', false); // Uncheck the checkbox

                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            getoverduedays();

            // show overdue day and year start from  settings form
            $('#editoverdueday').on('click', function () {
                $(this).hide();
                getoverduedays();
                if (overdueday != '') {
                    $('#overduedaysform').show();
                }
            });

            // hide overdue day and year start from settings form on click othersetting-cancelbtn
            $('#othersetting-cancelbtn').on('click', function () {
                $('#editoverdueday').show();
                $('#overduedaysform').hide();
                $('#overduedaysform')[0].reset();
            });

            // submit over due day and year start from setting form
            $('#overduedaysform').submit(function (event) {
                event.preventDefault();
                loadershow();
                editid = $('#overdue_day').data('id');
                let getOverDueDaysUpdateUrl = "{{ route('getoverduedays.update', '__editId__') }}".replace('__editId__', editid);
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: getOverDueDaysUpdateUrl,
                    data: formdata,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#editoverdueday').show();
                            $('#overduedaysform').hide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#overduedaysform')[0].reset();
                            getoverduedays();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            });

            /*
             * overdue and year start settings code end
             */

            /*
             * gst settings code start
             */

            // show gst settings form
            $('#editgstsettings').on('click', function () {
                getoverduedays();
                $('#gstsettingsform').show();
            });

            //hide gst settings form
            $('#gst-cancelbtn').on('click', function () {
                $('#gstsettingsform').hide();
                $('#gstsettingsform')[0].reset();
            });

            //submit gst settings form
            $('#gstsettingsform').submit(function (event) {
                event.preventDefault();
                loadershow();
                editid = $('#sgst').data('id');
                let gstSettingsUpdateUrl = "{{ route('gstsettingsupdate.update', '__editId__') }}".replace('__editId__', editid);
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: gstSettingsUpdateUrl,
                    data: formdata,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#gstsettingsform').hide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#gstsettingsform')[0].reset();
                            getoverduedays();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });

            });

            /*
             * gst settings code end
             */

            /*
             *terms and conditions settings code start
             */

            // get terms and conditions
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('termsandconditions.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.termsandconditions != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.termsandconditions, function (key, value) {
                                $('#tabledata').append(` <tr>
                                                                                                                                            <td>${id}</td>
                                                                                                                                            <td class='text-left' style="white-space: pre-line;"><div id="tcdiv">${value.t_and_c}</div></td>
                                                                                                                                            <td>
                                                                                                                                                @if (session('user_permissions.invoicemodule.invoicesetting.edit') == '1')
                                                                                                                                                    ${value.is_active == 1 ? '<div id=status_' + value.id + '> <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Inactive" data-status=' + value.id + ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0">active</button></div>' : '<div data-toggle="tooltip" data-placement="bottom" data-original-title="Active" id=status_' + value.id + '><button data-status= ' + value.id + ' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button></div>'}
                                                                                                                                                @else
                                                                                                                                                      -
                                                                                                                                                @endif
                                                                                                                                            </td>
                                                                                                                                            <td>
                                                                                                                                                <span>
                                                                                                                                                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" data-id= '${value.id}'
                                                                                                                                                        class="del-btn btn iq-bg-danger btn-rounded btn-sm my-0">
                                                                                                                                                        <i class="ri-delete-bin-fill"></i>
                                                                                                                                                    </button>
                                                                                                                                                </span>
                                                                                                                                            </td>
                                                                                                                                        </tr>`);
                                id++;
                            });
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#tabledata').append(`<tr><td colspan='3' >No Data Found</td></tr>`)
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            //call function for loaddata (terms and condition)
            loaddata();

            //  t & cstatus update(active to inactive)            
            $(document).on("click", ".status-active", function () {
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to change status to inactive?', // Text
                    'Yes, change',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        loadershow();
                        var statusid = element.data('status');
                        changetcstatus(statusid, 0);
                    }
                );
            });

            //  t & c status update (inactive to active)            
            $(document).on("click", ".status-deactive", function () {
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to change status to active?', // Text
                    'Yes, change',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        loadershow();
                        var statusid = element.data('status');
                        changetcstatus(statusid, 1);
                    }
                );
            });

            //chagne t&c status update function (active/inactive)
            function changetcstatus(tcid, statusvalue) {
                let termsAndConditionsStatusUpdateUrl = "{{ route('termsandconditions.statusupdate', '__tcId__') }}".replace('__tcId__', tcid);
                $.ajax({
                    type: 'PUT',
                    url: termsAndConditionsStatusUpdateUrl,
                    data: {
                        status: statusvalue,
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function (response) {
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            // delete terms and conditions              
            $(document).on("click", ".del-btn", function () {
                var deleteid = $(this).data('id');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to delete this record?', // Text
                    'Yes, delete',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let termsAndConditionsDeleteUrl = "{{ route('termsandconditions.delete', '__deleteId__') }}".replace('__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: termsAndConditionsDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function (response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    $(row).closest("tr").fadeOut();
                                } else if (response.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: "something went wrong!"
                                    });
                                }
                                loaderhide();
                            },
                            error: function (xhr, status, error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText); // Log the full error response for debugging
                                handleAjaxError(xhr);
                            }
                        });
                    }
                );
            });

            // show add new terms and conditions form on click add new terms and condition btn
            $('#newtcBtn').on('click', function (e) {
                e.preventDefault();
                $('#tcform').removeClass('d-none');
                $('#newtcBtn').addClass('d-none');
            })

            // hide add new terms and conditions form on click cancel tc button
            $('#canceltcBtn').on('click', function (e) {
                e.preventDefault();
                $('#tcform')[0].reset();
                $('#t_and_c').summernote('code', '');
                $('#tcform').addClass('d-none');
                $('#newtcBtn').removeClass('d-none');
            })

            // reset add new terms and conditions form on  click reset tc button
            $('#resettcBtn').on('click', function (e) {
                $('#tcform')[0].reset();
                $('#t_and_c').summernote('code', '');
            })

            // submit terms and conditions form 
            $('#tcform').submit(function (event) {
                event.preventDefault();
                loadershow();
                url = "{{ route('termsandconditions.store') }}";
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#tcform')[0].reset();
                            $('#t_and_c').summernote('code', '');
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            });

            /*
             *terms and conditions settings code end
             */

            /*
             * invoice number code start
             */

            // invoice number settings  user can add or not enter manual invoice number during create invoice
            $('#manualinvnumberswitch').on('change', function () {
                var val = $(this).prop('checked') ? 'yes' : 'no';
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to update this setting?', // Text
                    'Yes, update',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        loadershow();
                        // Success callback
                        var invnumberstatus = 0;
                        if (val == 'yes') {
                            invnumberstatus = 1;
                        }
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('othersettings.updateinvoicenumberstatus') }}",
                            data: {
                                user_id: "{{ session()->get('user_id') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                token: "{{ session()->get('api_token') }}",
                                status: invnumberstatus
                            },
                            success: function (response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                }
                                loaderhide();
                                // You can update your HTML with the data here if needed
                            },
                            error: function (xhr, status, error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText); // Log the full error response for debugging
                                handleAjaxError(xhr);
                            }
                        });
                    },
                    () => {
                        if (val == 'yes') {
                            element.prop('checked', false);
                        } else {
                            element.prop('checked', true);
                        }
                    }
                );


            });

            // invoice number settings  user can add or not enter manual invoice date during create invoice
            $('#manualinvdateswitch').on('change', function () {
                var val = $(this).prop('checked') ? 'yes' : 'no';
                element = $(this);

                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to update this setting?', // Text
                    'Yes, update',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        var invdatestatus = 0;
                        if (val == 'yes') {
                            invdatestatus = 1
                        }
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('othersettings.updateinvoicedatestatus') }}",
                            data: {
                                user_id: "{{ session()->get('user_id') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                token: "{{ session()->get('api_token') }}",
                                status: invdatestatus
                            },
                            success: function (response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                }
                                loaderhide();
                                // You can update your HTML with the data here if needed
                            },
                            error: function (xhr, status, error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText); // Log the full error response for debugging
                                handleAjaxError(xhr);
                            }
                        });
                    },
                    () => {
                        // Error callback
                        if (val == 'yes') {
                            element.prop('checked', false);
                        } else {
                            element.prop('checked', true);
                        }
                    }
                );
            });

            //get invoice pattern and set it 
            function getinvoicepatterns() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('invoicenumberpatterns.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.pattern != '') {
                            var data = response.pattern[0];
                            // Update the HTML content
                            $.each(data, function (key, value) {
                                var pattern = value.invoice_pattern;
                                var modifiedfiedpattern =
                                    pattern.replace('nextyear',
                                        "<span style='color:tomato'>nextyear</span>").replace('year',
                                            "<span style='color:goldenrod'>year</span>")
                                        .replace('month',
                                            "<span style='color:lawngreen'>month</span>")
                                        .replace('date',
                                            "<span style='color:darkmagenta'>date</span>")
                                        .replace('customerid',
                                            "<span style='color:lightseagreen'>customerid</span>")
                                        .replace('cidai', "<span style='color:silver'>" + response
                                            .pattern[1].customer_id +
                                            "(Auto increment as per customer)</span>")
                                        .replace('ai', "<span style='color:red'>" + value
                                            .start_increment_number +
                                            "(Auto increment as per invoice)");
                                if (value.pattern_type == "global") {
                                    $('#globalinvoicepattern').html(modifiedfiedpattern);
                                } else {
                                    $('#domesticinvoicepattern').html(modifiedfiedpattern);
                                }
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#domesticinvoicepattern').text('Yet not set');
                            $('#globalinvoicepattern').text('Yet not set');

                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            getinvoicepatterns();

            // show add or edit invoice number pattern form on click add/edit invoice number btn
            $('#editinvoicenumberBtn').on('click', function () {
                $(this).addClass('d-none');
                $('#invoicenumberpatternform').removeClass('d-none');
            });

            // hide add/edit invoice number pattern form
            $('#cancelnumberpattern').on('click', function () {
                $('#invoicenumberinputs').html(' ');
                $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop('disabled',
                    false);
                $('#editinvoicenumberBtn').removeClass('d-none');
                $('#invoicenumberpatternform').addClass('d-none');
            });

            // append dynamic input on select type during create invoice number pattern
            $('#invoicepattern').on('change', function () {

                var value = $(this).val();
                if (value == 'text') {
                    $('#invoicenumberinputs').append(`
                                                                                                    <div class="col-sm-2 text-center invoicepatterninput">
                                                                                                        <input type="text" data-type="text" required Placeholder="text" class="form-control">
                                                                                                        <span class="text-center"><i data-type="text" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                    </div>
                                                                                                    `);
                } else if (value == 'date') {
                    $('#invoicenumberinputs').append(`
                                                                                                        <div class="col-sm-2 text-center invoicepatterninput">
                                                                                                            <input type="text" data-type="date" readonly value="date"  class="form-control">
                                                                                                            <span class="text-center"><i data-type="date" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                        </div>
                                                                                                    `);
                } else if (value == 'month') {
                    $('#invoicenumberinputs').append(`
                                                                                                        <div class="col-sm-2 text-center invoicepatterninput">
                                                                                                            <input type="text" data-type="month" readonly value="month"  class="form-control">
                                                                                                            <span class="text-center"><i data-type="month" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                        </div>
                                                                                                    `);
                } else if (value == 'nextyear') {
                    $('#invoicenumberinputs').append(`
                                                                                                            <div class="col-sm-2 text-center invoicepatterninput">
                                                                                                                <input type="text" data-type="nextyear"  readonly value="nextyear"  class="form-control">
                                                                                                                <span class="text-center"><i data-type="nextyear" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                            </div>
                                                                                                        `);
                } else if (value == 'year') {
                    $('#invoicenumberinputs').append(`
                                                                                                            <div class="col-sm-2 text-center invoicepatterninput">
                                                                                                                <input type="text" data-type="year"  readonly value="year"  class="form-control">
                                                                                                                <span class="text-center"><i data-type="year" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                            </div>
                                                                                                        `);
                } else if (value == 'customerid') {
                    $('#invoicenumberinputs').append(`
                                                                                                        <div class="col-sm-2 text-center invoicepatterninput">
                                                                                                            <input type="text" data-type="customerid"  readonly value="customerid"  class="form-control">
                                                                                                            <span class="text-center"><i data-type="customerid" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                        </div>
                                                                                                    `);
                } else if (value == 'ai') {
                    $('#invoicenumberinputs').append(`
                                                                                                            <div class="col-sm-3 text-center invoicepatterninput">
                                                                                                                <input type="number" required data-type="ai" min="1" placeholder="Where To Start? Increment" class="form-control">
                                                                                                                <span class="text-center"><i data-type="ai" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                            </div>
                                                                                                        `);
                    $(this).find('option:selected,option[value="cidai"]').prop('disabled', true);
                } else if (value == 'cidai') {
                    $('#invoicenumberinputs').append(`
                                                                                                            <div class="col-sm-3 text-center invoicepatterninput">
                                                                                                                <input type="number" required data-type="cidai" min="1" placeholder="Where To Start? Increment" class="form-control">
                                                                                                                <span class="text-center"><i data-type="cidai" class="ri-delete-bin-line btn btn-primary iq-bg-danger invoicepatterninputdlt"></i></span>
                                                                                                            </div>
                                                                                                        `);
                    $(this).find('option:selected,option[value="ai"]').prop('disabled', true);
                }
                $(this).val($(this).find('option:first').val());
            });

            //delete dynamic input on click delete btn  during create invoice number pattern
            $(document).on('click', '.invoicepatterninputdlt', function () {
                var inputtype = $(this).data('type');

                if (inputtype === 'ai' || inputtype === 'cidai') {
                    $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop('disabled',
                        false);
                }
                $(this).closest('div').remove();
            });

            // remove all dynamic appended input type on click reset pattern btn
            $('#resetpatterninput').on('click', function () {
                $('#invoicenumberinputs').html(' ');
                $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop('disabled',
                    false);
            });

            // submit invoice number pattern form
            $('#invoicenumberpatternform').on('submit', function (e) {
                e.preventDefault();

                var inputs = [];
                $('div.invoicepatterninput > input').each(function () {
                    inputs.push({
                        type: $(this).data('type'),
                        value: $(this).val()
                    });
                });

                var hasAnyAiInput = inputs.some(function (input) {
                    return input.type === 'ai' || input.type === 'cidai';
                });

                if (hasAnyAiInput) {
                    pattern_type = $('#patterntype').val();
                    var data = {
                        inputs,
                        pattern_type,
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    }

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('invoicepattern.store') }}",
                        data: data,
                        success: function (response) {
                            // Handle the response from the server
                            if (response.status == 200) {
                                // You can perform additional actions, such as showing a success message or redirecting the user
                                Toast.fire({
                                    icon: "success",
                                    title: response.message
                                });
                                $('#invoicenumberpatternform')[0].reset();
                                $('#invoicenumberinputs').html(' ');
                                $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop('disabled', false);
                                $('#invoicenumberpatternform').addClass('d-none');
                                $('#editinvoicenumberBtn').removeClass('d-none');
                                getinvoicepatterns();
                            } else if (response.status == 1) {
                                showConfirmationDialog(
                                    'Are you sure?',  // Title
                                    response.message, // Text
                                    'Yes, ',  // Confirm button text
                                    'No, cancel', // Cancel button text
                                    'question', // Icon type (question icon)
                                    () => {
                                        // Success callback
                                        startpatternfromoldnumber(data);
                                    },
                                    () => {
                                        // Error callback
                                        $('#invoicenumberpatternform')[0].reset();
                                        $('#invoicenumberinputs').html(' ');
                                        $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop('disabled', false);
                                    }
                                );
                            } else if (response.status == 500) {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message
                                });
                            } else {
                                Toast.fire({
                                    icon: "error",
                                    title: "something went wrong!"
                                });
                            }
                            loaderhide();
                        },
                        error: function (xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                        }
                    });
                } else {
                    Toast.fire({
                        icon: "error",
                        title: "Please select atleast one auto increment input"
                    });
                }
            });
            /*
             * invoice number code end
             */


            /*
             * customer id number code start
             */

            // increment will start from last paused number 
            function startpatternfromoldnumber(data) {
                data.onconfirm = 'yes';
                $.ajax({
                    type: 'POST',
                    url: "{{ route('invoicepattern.store') }}",
                    data: data,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#invoicenumberpatternform')[0].reset();
                            $('#invoicenumberinputs').html(' ');
                            $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop(
                                'disabled',
                                false);
                            getinvoicepatterns();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            // show edit customer id form
            $('#editcustomeridBtn').on('click', function (e) {
                e.preventDefault();
                $('#cidform').removeClass('d-none');
                $('#formsettingform').removeClass('d-none');
                $('#editcustomeridBtn').addClass('d-none');
            })


            // hide edit customer id form
            $('#cancelcustomeridBtn').on('click', function (e) {
                e.preventDefault();
                $('#cidform')[0].reset();
                $('#cidform').addClass('d-none');
                $('#editcustomeridBtn').removeClass('d-none');
            })

            // submit edit customer id form
            $('#cidform').on('submit', function (e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customerid.store') }}",
                    data: formdata,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#cidform')[0].reset();
                            $('#cidform').addClass('d-none');
                            $('#editcustomeridBtn').removeClass('d-none');
                            getoverduedays();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });

            });

            /*
             * customer id number code end
             */
            // show edit customer form settings
            $('#editcustomerformsettingsBtn').on('click', function (e) {
                e.preventDefault();
                $('#customerformsettingform').removeClass('d-none');
                $('#editcustomerformsettingsBtn').addClass('d-none');
            })
            // hide edit customer form settings
            $('#cancelcustomerformsettingBtn').on('click', function (e) {
                e.preventDefault();
                $('#customerformsettingform')[0].reset();
                $('#customerformsettingform').addClass('d-none');
                $('#editcustomerformsettingsBtn').removeClass('d-none');
            })

            $('#customerformsettingform').on('submit', function (e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('invoicecustomerdropdown.store') }}",
                    data: formdata,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#customerformsettingform')[0].reset();
                            $('#customerformsettingform').addClass('d-none');
                            $('#editcustomerformsettingsBtn').removeClass('d-none');
                            getoverduedays();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });

            });



        });
    </script>
@endpush