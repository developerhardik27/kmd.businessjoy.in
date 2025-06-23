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
                                    <h4 class="card-title">Consignor Copy Terms & Conditions Settings </h4>
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
                                                <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                    class="form-control">
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
                                        <table id="data"class="table table-bordered table-striped w-100 text-center">
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
                                                <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="consignment_note_number">Consignment Note Number</label>
                                                <input type="number" class="form-control" min="1"
                                                    name="consignment_note_number" id="consignment_note_number">
                                                <span class="error-msg" id="error-consignment_note_number"
                                                    style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-sm-12 mt-2">
                                                <button type="button" id="cancelconsignmentnotenumberBtn"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Cancel"
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
                                Consignment Note Number : <span id="startcnn"> </span>
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
                                                <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="gst_tax_payable_by">GST Tax Payable By</label>
                                                <select class="form-control" name="gst_tax_payable_by"
                                                    id="gst_tax_payable_by">
                                                    <option disabled selected value="">Select</option>
                                                    <option value="consignee/consignor">Consignee/Consignor</option>
                                                    <option value="consignee">Consignee</option>
                                                    <option value="consignor">Consignor</option>
                                                </select>
                                                <span class="error-msg" id="error-gst_tax_payable_by"
                                                    style="color: red"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <label for="weight">Weight</label>
                                                <select class="form-control" name="weight" id="weight">
                                                    <option disabled selected value="">Select</option>
                                                    <option value="kg">KG</option>
                                                    <option value="ton">TON</option>
                                                </select>
                                                <span class="error-msg" id="error-weight" style="color: red"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <label for="authorized_signatory">Authorized Signatory</label>
                                                <select class="form-control" name="authorized_signatory"
                                                    id="authorized_signatory">
                                                    <option disabled selected value="">Select</option>
                                                    <option value="blank">Blank</option>
                                                    <option value="company_signature">Company Signature</option>
                                                </select>
                                                <span class="error-msg" id="error-authorized_signatory"
                                                    style="color: red"></span>
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
                                </p>
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
        $('document').ready(function() {

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
            function getlogisticothersettings() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('getlogisticothersettings') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.logisticsettings != '') {
                            othersettings = response.logisticsettings;
                            $('#startcnn').html(
                                `${othersettings.start_consignment_note_no || 'Not Set Yet'}`);
                            $('#gst_tax_payable_by_span').html(
                                `${othersettings.gst_tax_payable_by || 'Not Set Yet'}`);
                            $('#weight_span').html(`${othersettings.weight || 'Not Set Yet'}`);
                            $('#authorized_signatory_span').html(
                                `${othersettings.authorized_signatory ?  `${othersettings.authorized_signatory}`.replace('_',' ')  : 'Not Set Yet'}`);
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message || 'something went wrong'
                            });
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
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
                    success: function(response) {
                        if (response.status == 200 && response.termsandconditions != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.termsandconditions, function(key, value) {
                                $('#tabledata').append(` 
                                    <tr>
                                        <td>${id}</td>
                                        <td class='text-left'><div id="tcdiv">${value.t_and_c}</div></td>
                                        <td>
                                            @if (session('user_permissions.invoicemodule.invoicesetting.edit') == '1')
                                                ${value.is_active == 1 ? '<div id=status_'+value.id+ '> <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Inactive" data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0">active</button></div>'  : '<div data-toggle="tooltip" data-placement="bottom" data-original-title="Active" id=status_'+value.id+ '><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button></div>'}
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            }

            //call function for loaddata (terms and condition)
            loaddata();

            //  t & cstatus update(active to inactive)            
            $(document).on("click", ".status-active", function() {
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
            $(document).on("click", ".status-deactive", function() {
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
                    success: function(response) {
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            }

            // delete terms and conditions              
            $(document).on("click", ".del-btn", function() {
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
                            success: function(response) {
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
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                var errorMessage = "";
                                try {
                                    var responseJSON = JSON.parse(xhr.responseText);
                                    errorMessage = responseJSON.message ||
                                        "An error occurred";
                                } catch (e) {
                                    errorMessage = "An error occurred";
                                }
                                Toast.fire({
                                    icon: "error",
                                    title: errorMessage
                                });
                            }
                        });
                    }
                );
            });

            // show add new terms and conditions form on click add new terms and condition btn
            $('#newtcBtn').on('click', function(e) {
                e.preventDefault();
                $('#tcform').removeClass('d-none');
                $('#newtcBtn').addClass('d-none');
            })

            // hide add new terms and conditions form on click cancel tc button
            $('#canceltcBtn').on('click', function(e) {
                e.preventDefault();
                $('#tcform')[0].reset();
                $('#t_and_c').summernote('code', '');
                $('#tcform').addClass('d-none');
                $('#newtcBtn').removeClass('d-none');
            })

            // reset add new terms and conditions form on  click reset tc button
            $('#resettcBtn').on('click', function(e) {
                $('#tcform')[0].reset();
                $('#t_and_c').summernote('code', '');
            })

            // submit terms and conditions form 
            $('#tcform').submit(function(event) {
                event.preventDefault();
                loadershow();
                url = "{{ route('consignorcopytermsandconditions.store') }}";
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    success: function(response) {
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
                });
            });

            /*
             *terms and conditions settings code end
             */


            /*
             * customer id number code start
             */

            // show edit customer id form
            $('#editconsignmentnotenumberBtn').on('click', function(e) {
                e.preventDefault();
                $('#cidform').removeClass('d-none');
                $('#editconsignmentnotenumberBtn').addClass('d-none');
            })

            // hide edit customer id form
            $('#cancelconsignmentnotenumberBtn').on('click', function(e) {
                e.preventDefault();
                $('#cidform')[0].reset();
                $('#cidform').addClass('d-none');
                $('#editconsignmentnotenumberBtn').removeClass('d-none');
            })

            // submit edit customer id form
            $('#cidform').on('submit', function(e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('consignmentnotenumber.store') }}",
                    data: formdata,
                    success: function(response) {
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
                });

            });

            /*
             * customer id number code end
             */


            /*
             * other settings 
             */

            // show other settings form
            $('#editOtherSettingsBtn').on('click', function(e) {
                e.preventDefault();
                $('#othersettingsform').removeClass('d-none');
                $(this).addClass('d-none');
            })

            // hide other settings form
            $('#cancelOtherSettingsBtn').on('click', function(e) {
                e.preventDefault();
                $('#othersettingsform')[0].reset();
                $('#othersettingsform').addClass('d-none');
                $('#editOtherSettingsBtn').removeClass('d-none');
            })

            // submit edit customer id form
            $('#othersettingsform').on('submit', function(e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('logisticothersettings.store') }}",
                    data: formdata,
                    success: function(response) {
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
                });
            });
        });
    </script>
@endpush
