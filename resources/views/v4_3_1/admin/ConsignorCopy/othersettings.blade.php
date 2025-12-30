@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')

@section('page_title')
    {{ config('app.name') }} - Consignor Copy Other Settings
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

        .select2 {
            min-width: 100% !important;
        }
    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            @if (session('user_permissions.logisticmodule.consignorcopytandcsettings.edit') == 1)
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                        <button type="button" id="newtcBtn" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Add New Terms and Conditions"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-add-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">LR Copy Terms & Conditions Settings </h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="tcform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-6">
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
                                                <button type="button" id="canceltcBtn" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Cancel"
                                                    class="btn btn-secondary">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <hr>
                                <div class="table-wrapper-scroll-y my-custom-scrollbar">
                                    <div class="table-responsive">
                                        <table id="data" class="table table-bordered table-striped w-100 text-center">
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
                </div>
            @endif
        </div>
        <div class="container-fluid">
            <div class="row">
                @if (
                        session('user_permissions.logisticmodule.watermark.edit') == 1 ||
                        session('user_permissions.logisticmodule.watermark.add') == 1
                    )
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Watermark Settings"
                            type="button" id="editWatermarkSettingsBtn"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Watermark Image Settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="watermarkform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-12">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="watermark_image">Watermark Image</label><br>
                                                <input type="file" class="form-control-file w-100" accept=".jpg, .jpeg, .png"
                                                    name="watermark_image" id="watermark_image" />
                                                <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that
                                                    is smaller than 1 MB.</p>
                                                <span class="error-msg" id="error-watermark_image" style="color: red"></span>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="button" id="cancelWatermarkSettingsBtn" data-toggle="tooltip"
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
                                Current Watermark : <img id="current_watermark_img" src="" width="100px" alt="Not Set Yet"><br>

                            </div>
                        </div>
                    </div>
                @endif
                @if (session('user_permissions.logisticmodule.consignmentnotenumbersettings.edit') == 1)
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Where To Start Consignment Note Number" type="button"
                            id="editconsignmentnotenumberBtn"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Consignment Note Number Settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="cidform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-12">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="consignment_note_number">Consignment Note Number</label>
                                                <input type="number" class="form-control" min="1" name="consignment_note_number"
                                                    id="consignment_note_number">
                                                <p id="info-consignment_note_no" class="text-info m-0"></p>
                                                <span class="error-msg" id="error-consignment_note_number"
                                                    style="color: red"></span>

                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="button" id="cancelconsignmentnotenumberBtn" data-toggle="tooltip"
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
                                Consignment Note Number : <span id="startcnn"> </span><br>
                                Current Consignment Note Number : <span id="currentcnn"> </span>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('user_permissions.logisticmodule.logisticothersettings.edit') == 1)
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Default Values"
                            type="button" id="editOtherSettingsBtn"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Default Value</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="othersettingsform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-12 mb-2">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="gst_tax_payable_by">GST Tax Payable By</label>
                                                <select class="form-control" name="gst_tax_payable_by" id="gst_tax_payable_by">
                                                    <option selected value="">Select</option>
                                                    <option value="consignee/consignor">Consignee/Consignor</option>
                                                    <option value="consignee">Consignee</option>
                                                    <option value="consignor">Consignor</option>
                                                </select>
                                                <span class="error-msg" id="error-gst_tax_payable_by" style="color: red"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <label for="weight">Weight</label>
                                                <select class="form-control" name="weight" id="weight">
                                                    <option selected value="">Select</option>
                                                    <option value="kg">KG</option>
                                                    <option value="ton">TON</option>
                                                </select>
                                                <span class="error-msg" id="error-weight" style="color: red"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <label for="authorized_signatory">Authorized Signatory</label>
                                                <select class="form-control" name="authorized_signatory"
                                                    id="authorized_signatory">
                                                    <option value="">Select</option>
                                                    <option value="blank" selected>Blank</option>
                                                    <option value="company_signature">Company Signature</option>
                                                </select>
                                                <span class="error-msg" id="error-authorized_signatory"
                                                    style="color: red"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                Gods Name :
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
                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="button" id="cancelOtherSettingsBtn" data-toggle="tooltip"
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
                                <p><strong>GST tax payable by : </strong> <span id="gst_tax_payable_by_span"> </span></p>
                                <p><strong>Weight : </strong> <span id="weight_span"> </span></p>
                                <p><strong>Authorized Signatory : </strong> <span id="authorized_signatory_span"> </span>
                                <p>
                                    <strong>God Name Show / Hide :</strong>

                                    <span class="custom-control custom-switch custom-control-inline align-middle ms-2">
                                        <input type="checkbox" class="custom-control-input" disabled
                                            id="god_namedisabledswitch">

                                        <label class="custom-control-label" for="god_namedisabledswitch" id="godnamestatus">
                                        </label>
                                    </span>
                                </p>

                            </div>
                        </div>
                    </div>
                @endif
                @if (session('user_permissions.logisticmodule.downloadcopysetting.edit') == 1)
                    <div class="col-md-6">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Download Copy Setting"
                            type="button" id="editDownloadCopySettingBtn"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Download Copy</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="downloadcopysettingform" class="d-none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-12 mb-2">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ session('user_id') }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ session('company_id') }}" name="company_id"
                                                    class="form-control">
                                                <label for="download_copy">Copies</label>
                                                <select class="select2 form-control w-100" name="download_copy[]"
                                                    id="download_copy" multiple>
                                                    <option value="consignor">Consignor</option>
                                                    <option value="consignee">Consignee</option>
                                                    <option value="driver">Driver</option>
                                                    <option value="always ask">Always ask</option>
                                                </select>
                                                <span class="error-msg" id="error-download_copy" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="button" id="cancelDownloadCopySettingBtn" data-toggle="tooltip"
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
                                <p><strong>Copies : </strong> <span id="download_copy_span"> </span></p>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('user_permissions.logisticmodule.logisticothersettings.edit') == 1)
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
                                                        <input type="checkbox" name="customer_type[]" value="invoice">
                                                        invoice
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" name="customer_type[]" value="quotation">
                                                        Quotation
                                                    </label>
                                                </div>

                                                <span class="error-msg" id="error-customer_id" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="btn" id="cancelcustomerformsettingBtn" data-toggle="tooltip"
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
    <script>

        $('document').ready(function () {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data


            /*
             * overview  ----------------
             * initialize summernote editor  
             * terms and conditions settings 
             * consignment note number settings
             */

            let othersettings = null; //declare default setting variable
            let watermarkImg = null;

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
                placeholder: 'Add New T&C',
                tabsize: 2,
                height: 100,
            });


            loaderhide();

            /*
             *settings code start
             */
            function getlogisticothersettings() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('getlogisticothersettings') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.logisticsettings != '') {
                            othersettings = response.logisticsettings;

                            //this code for get god name 
                            customer_dropdown = response.logisticsettings['customer_dropdown'];

                            document.querySelectorAll('input[name="customer_type[]"]').forEach((checkbox) => {
                                if (customer_dropdown.includes(checkbox.value)) {
                                    checkbox.checked = true;
                                }
                            });
                            let names = JSON.parse(customer_dropdown);
                            $('#startformsetting').html(`<b>${names.join(', ')}</b> `);
                            god_name = othersettings['god_name_show/hide'];
                            $('#godnameswith').prop('checked', data.god_name == 1);
                            $('#god_namedisabledswitch').prop('checked', data.god_name == 1);
                            $('#godnamestatus').text(god_name == 1 ? 'active' : 'inactive');
                            if (god_name == 1) {
                                $('#godnameswith').prop('checked', true); // Check the checkbox
                                $('#god_namedisabledswitch').prop('checked', true); // Check the checkbox
                            } else {
                                $('#godnameswith').prop('checked', false); // Uncheck the checkbox
                                $('#god_namedisabledswitch').prop('checked', false); // Uncheck the checkbox
                            }

                            //this code for get other details 
                            if (response.lrcount > 0) {
                                $('#info-consignment_note_no').text(`Consignment note number not allow to smaller than ${othersettings.current_consignment_note_no}`);
                            }

                            $('#startcnn').html(
                                `${othersettings.start_consignment_note_no || 'Not Set Yet'}`);
                            $('#currentcnn').html(
                                `${othersettings.current_consignment_note_no || 'Not Set Yet'}`);
                            $('#gst_tax_payable_by_span').html(
                                `${othersettings.gst_tax_payable_by || 'Not Set Yet'}`);
                            $('#weight_span').html(`${othersettings.weight || 'Not Set Yet'}`);
                            $('#authorized_signatory_span').html(
                                `${othersettings.authorized_signatory ? `${othersettings.authorized_signatory}`.replace('_', ' ') : 'Not Set Yet'}`
                            );

                            $('#download_copy_span').html(
                                `${othersettings.download_copy || 'Not Set Yet'}`);
                        } else {
                            othersettings = null;
                            Toast.fire({
                                icon: "error",
                                title: response.message || 'something went wrong'
                            });
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        othersettings = null;
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            getlogisticothersettings();

            /*
             *terms and conditions settings code start
             */

            // get terms and conditions
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('consignorcopytermsandconditions.index') }}",
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
                                $('#tabledata').append(` 
                                                                                                                                    <tr>
                                                                                                                                        <td>${id}</td>
                                                                                                                                        <td class='text-left'><div id="tcdiv">${value.t_and_c}</div></td>
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
                                                                                                                                    </tr>
                                                                                                                                `);
                                id++;
                            });
                            $('[data-toggle="tooltip"]').tooltip({
                                boundary: 'window',
                                offset: '0, 10' // Push tooltip slightly away from the button
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#tabledata').append(`<tr><td colspan='4' >No Data Found</td></tr>`)
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


            // get watermark function
            function getwatermarksettings() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('watermark.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status === 200 && response.watermarksettings) {

                            let data = response.watermarksettings;

                            if (data.watermark_img) {
                                let imagePath = "{{ asset('uploads') }}/" + data.watermark_img;
                                $('#current_watermark_img').attr('src', imagePath);
                            }

                        } else {
                            console.log('No watermark settings found');
                        }

                        loaderhide();
                    },
                    error: function (xhr) {
                        loaderhide();
                        console.log(xhr.responseText);
                        handleAjaxError(xhr);
                    }
                });
            }
            getwatermarksettings();


            //  t & cstatus update(active to inactive)            
            $(document).on("click", ".status-active", function () {
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change status to inactive?', // Text
                    'Yes, change', // Confirm button text
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
                    'Are you sure?', // Title
                    'to change status to active?', // Text
                    'Yes, change', // Confirm button text
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
                let termsAndConditionsStatusUpdateUrl =
                    "{{ route('consignorcopytermsandconditions.statusupdate', '__tcId__') }}"
                        .replace('__tcId__', tcid);
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
                        console.log(xhr.responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            // delete terms and conditions              
            $(document).on("click", ".del-btn", function () {
                var deleteid = $(this).data('id');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let termsAndConditionsDeleteUrl =
                            "{{ route('consignorcopytermsandconditions.delete', '__deleteId__') }}"
                                .replace(
                                    '__deleteId__', deleteid);
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
                            error: function (xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
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
                url = "{{ route('consignorcopytermsandconditions.store') }}";
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
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#error-' + key).text(value[0].replace('t and c',
                                    'T&C'));
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
                });
            });

            /*
             *terms and conditions settings code end
             */

            // watermark settings  start

            // show edit customer id form
            $('#editWatermarkSettingsBtn').on('click', function (e) {
                e.preventDefault();
                $('#watermarkform').removeClass('d-none');
                $('#editWatermarkSettingsBtn').addClass('d-none');
            })

            // hide edit customer id form
            $('#cancelWatermarkSettingsBtn').on('click', function (e) {
                e.preventDefault();
                $('#watermarkform')[0].reset();
                $('#watermarkform').addClass('d-none');
                $('#editWatermarkSettingsBtn').removeClass('d-none');
            })

            $('#watermarkform').submit(function (event) {
                event.preventDefault();
                loadershow();
                url = "{{ route('watermark.update') }}";
                $('.error-msg').text('');
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            imagePath = "{{asset('uploads/')}}" + '/' + response.watermarksettings;
                            $('#current_watermark_img').attr('src', `${imagePath}`);
                            $('#watermarkform')[0].reset();
                            $('#watermarkform').addClass('d-none');
                            $('#editWatermarkSettingsBtn').removeClass('d-none');
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            });

            // end watermark settings


            /*
             * customer id number code start
             */

            // show edit customer id form
            $('#editconsignmentnotenumberBtn').on('click', function (e) {
                e.preventDefault();
                $('#consignment_note_number').val(othersettings.start_consignment_note_no);
                $('#cidform').removeClass('d-none');
                $('#editconsignmentnotenumberBtn').addClass('d-none');
            })

            // hide edit customer id form
            $('#cancelconsignmentnotenumberBtn').on('click', function (e) {
                e.preventDefault();
                $('#cidform')[0].reset();
                $('#cidform').addClass('d-none');
                $('#editconsignmentnotenumberBtn').removeClass('d-none');
            })

            // submit edit customer id form

            $('#cidform').on('submit', function (e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('consignmentnotenumber.store') }}",
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
                            $('#editconsignmentnotenumberBtn').removeClass('d-none');
                            getlogisticothersettings();
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


            /*
             * other settings 
             */

            // show other settings form
            $('#editOtherSettingsBtn').on('click', function (e) {
                e.preventDefault();
                $('.error-msg').text('');
                $('#gst_tax_payable_by').val(othersettings.gst_tax_payable_by);
                $('#weight').val(othersettings.weight);
                $('#authorized_signatory').val(othersettings.authorized_signatory);
                $('#othersettingsform').removeClass('d-none');
                $(this).addClass('d-none');
            })

            // hide other settings form
            $('#cancelOtherSettingsBtn').on('click', function (e) {
                e.preventDefault();
                $('#othersettingsform')[0].reset();
                $('#othersettingsform').addClass('d-none');
                $('#editOtherSettingsBtn').removeClass('d-none');
            })

            // submit edit customer id form
            $('#othersettingsform').on('submit', function (e) {
                e.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('logisticothersettings.store') }}",
                    data: formdata,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#othersettingsform')[0].reset();
                            $('#othersettingsform').addClass('d-none');
                            $('#editOtherSettingsBtn').removeClass('d-none');
                            getlogisticothersettings();
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
             * Download Copy settings 
             */

            // Detect when an option is selected
            $('#download_copy').on('select2:select', function (e) {
                let selectedValue = e.params.data.id;  // Get the value of the selected option

                // If "Always ask" is selected, handle accordingly
                if (selectedValue === 'always ask') {
                    let values = $(this).val();  // Get the current values
                    $(this).val('always ask').trigger('change');
                }
            });

            $('#download_copy').on('change', function (ele) {
                let values = $(this).val();
                if (values.includes('always ask') && values.length > 1) {
                    let allwVal = values.filter(val => val !== 'always ask');
                    $(this).val(allwVal).trigger('change');
                }
                if (values.length == 0) {
                    $(this).val('always ask').trigger('change');
                }
            });

            // show other settings form
            $('#editDownloadCopySettingBtn').on('click', function (e) {
                e.preventDefault();
                $('.error-msg').text('');
                $('#download_copy').val(othersettings.download_copy.split(','))
                    .select2({
                        placeholder: 'Select Options',
                        allowClear: true,
                    });
                $('#downloadcopysettingform').removeClass('d-none');
                $(this).addClass('d-none');
            })

            // hide other settings form
            $('#cancelDownloadCopySettingBtn').on('click', function (e) {
                e.preventDefault();
                $('#downloadcopysettingform')[0].reset();
                $('#downloadcopysettingform').addClass('d-none');
                $('#editDownloadCopySettingBtn').removeClass('d-none');
            })

            // submit edit customer id form
            $('#downloadcopysettingform').on('submit', function (e) {
                e.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('downloadcopysetting.store') }}",
                    data: formdata,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#downloadcopysettingform')[0].reset();
                            $('#downloadcopysettingform').addClass('d-none');
                            $('#editDownloadCopySettingBtn').removeClass('d-none');
                            getlogisticothersettings();
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
                    url: "{{ route('customerdropdowninlogistic.store') }}",
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
                            getlogisticothersettings();
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