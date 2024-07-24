@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterpage')

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
    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    {{-- <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Invoice Number Settings"
                        type="button" id="editinvoicenumber"
                        class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                        <i class="ri-edit-fill"></i>
                    </button> --}}
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Invoice Number Settings</h4>
                            </div>
                        </div>
                        <div class="iq-card-body" id="">
                            <form id="invoicenumberpatternform">
                                @csrf
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-4">
                                            <label for="invoicepattern"> Invoice Pattern : </label>
                                            <select name="invoicepattern" class="form-control" id="invoicepattern">
                                                <option value="" selected disabled>Select Input Type</option>
                                                <option value="text">Text</option>
                                                <option value="year">Year</option>
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
                                                    data-original-title="Reset Inputs" id="resetpatterninput"
                                                    class="btn iq-bg-danger float-right">Reset</button>
                                                <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Save"
                                                    class="btn btn-primary float-right my-0">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            Domestic Invoice Pattern : <span id="domesticinvoicepattern"></span><br>
                            Global Invoice Pattern : <span id="globalinvoicepattern"></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-12">
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
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <input type="hidden" name="token" class="form-control"
                                                value="{{ session('api_token') }}" placeholder="token" required />
                                            <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                class="form-control">
                                            <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                class="form-control">
                                            Invoice Overdue Days : <input type="number" id="overdue_day" name='overdue_day'
                                                class="form-control" placeholder="overdue days" min="1" required />
                                            <span class="error-msg" id="error-overdue_day" style="color: red"></span><br>
                                        </div>
                                        <div class="col-sm-6">
                                            Year Starting Date : <input type="date" id="year_start_date"
                                                name='year_start_date' class="form-control"
                                                placeholder="Year starting date" required />
                                            <span class="error-msg" id="error-year_start_date" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-12">
                                                <button type="reset" data-toggle="tooltip" id="overduereset"
                                                    data-placement="bottom" data-original-title="Reset Settings"
                                                    class="btn iq-bg-danger float-right">Reset</button>
                                                <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Update Settings"
                                                    class="btn btn-primary float-right my-0">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </form>
                            Current Invoice Overdue Days : <span id="overduedays"></span> <br>
                            Year Starting Date : <span id="yearstartdate"></span> <br>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-12">
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
                                            <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                class="form-control">
                                            <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                class="form-control">
                                            SGST : <input type="number" id="sgst" name='sgst'
                                                class="form-control" placeholder="SGST" min="1" step="0.01" required />
                                            <span class="error-msg" id="error-sgst" style="color: red"></span><br>
                                        </div>
                                        <div class="col-sm-6">
                                            CGST : <input type="number" id="cgst" name='cgst'
                                                class="form-control" placeholder="CGST" step="0.01" required />
                                            <span class="error-msg" id="error-cgst" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="form-row mt-1">
                                        <div class="col-sm-6">
                                            GST : <div
                                                class="custom-control custom-switch custom-switch-text custom-control-inline">
                                                <div class="custom-switch-inner">
                                                    <p class="mb-0">
                                                        <input type="checkbox" name="gst"
                                                            class="custom-control-input" id="gstswitch" value="1">
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
                                            <div class="col-sm-12">
                                                <button type="reset" id="gstsettingreset" data-toggle="tooltip"
                                                    data-placement="bottom" data-original-title="Reset GSt Settings"
                                                    class="btn iq-bg-danger float-right">Reset</button>
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
                            GST : <span id="viewgst">
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" disabled="" checked=""
                                        id="gstdisabledswitch">
                                    <label class="custom-control-label" for="gstdisabledswitch">active</label>
                                </div>
                            </span> <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Invoice Terms & conditions Settings </h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form id="tcform">
                                @csrf
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <input type="hidden" name="edit_id" class="form-control" id="edit_id"
                                                required />
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
                                        <div class="col-sm-6">
                                            <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Submit Terms & Conditions"
                                                class="btn btn-primary">Submit</button>
                                            <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Terms & Conditions"
                                                class="btn iq-bg-danger">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="table-wrapper-scroll-y my-custom-scrollbar">
                                <table
                                    id="data"class="table  table-bordered display table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped text-center">
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
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Customer ID settings</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form id="cidform">
                                @csrf
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-3">
                                            <input type="hidden" name="edit_id" class="form-control" id="edit_id"
                                                required />
                                            <input type="hidden" name="token" class="form-control"
                                                value="{{ session('api_token') }}" required />
                                            <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                class="form-control">
                                            <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                class="form-control">
                                            <label for="customer_id">Customer Id</label>
                                            <input type="number" class="form-control" min="0" name="customer_id"
                                                id="customer_id">
                                            <span class="error-msg" id="error-customer_id" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset"
                                                class="btn iq-bg-danger float-right">Reset</button>
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

            @isset($message)
                alert('You have not invoice number pattern. Please first make invoice number pattern!');
            @endisset


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

            var overdueday = '';

            function getoverduedays() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('getoverduedays.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.overdueday != '') {
                            var data = response.overdueday[0];
                            overdueday = data['overdue_day'];
                            year_start = data['year_start'];
                            sgst = data['sgst'];
                            cgst = data['cgst'];
                            gst = data['gst'];
                            $('#overduedays').html(`<b>${overdueday}</b> `);
                            $('#yearstartdate').html(`<b>${year_start}</b> `);
                            $('#overdue_day').val(overdueday);
                            $('#year_start_date').val(year_start);
                            $('#overdue_day').attr('data-id', data['id']);

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
                            $('#startcid').html(`<b>${data.customer_id}</b>`);

                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#overduedays').html(`<b>Not set</b> `);
                            $('#yearstartdate').html(`<b>Not set</b>`);
                            $('#viewsgst').html(`<b>Not set</b>`);
                            $('#viewcgst').html(`<b>Not set</b>`);
                            $('#gstdisabledswitch').prop('checked', false); // Uncheck the checkbox
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
                        toastr.error(errorMessage);
                    }
                });
            }
            getoverduedays();
            $('#editoverdueday').on('click', function() {
                getoverduedays();
                if (overdueday != '') {
                    $('#overduedaysform').show();
                }
            });

            $('#editgstsettings').on('click', function() {
                getoverduedays();
                $('#gstsettingsform').show();
            });

            $('#overduedaysform').submit(function(event) {
                event.preventDefault();
                loadershow();
                editid = $('#overdue_day').data('id');
                url = "/api/getoverduedays/update/" + editid;
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#overduedaysform').hide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#overduedaysform')[0].reset();
                            getoverduedays();
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
            $('#gstsettingsform').submit(function(event) {
                event.preventDefault();
                loadershow();
                editid = $('#sgst').data('id');
                url = "/api/gstsettings/update/" + editid;
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#gstsettingsform').hide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#gstsettingsform')[0].reset();
                            getoverduedays();
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

            // get terms and conditions
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('termsandconditions.index') }}',
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
                                $('#tabledata').append(` <tr>
                                                        <td>${id}</td>
                                                        <td class='text-left' style="white-space: pre-line;">${value.t_and_c}</td>
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
                                                    </tr>`);
                                id++;
                            });
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
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
                        toastr.error(errorMessage);
                    }
                });
            }

            //call function for loaddata
            loaddata();

            //  t & cstatus update deactive              
            $(document).on("click", ".status-active", function() {
                if (confirm('Are you really want to change status to inactive ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/termsandconditions/statusupdate/' + statusid,
                        data: {
                            status: '0',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                // $('#status_' + statusid).html('<button data-status= ' +
                                //     statusid +
                                //     ' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button>'
                                // );
                                loaddata();
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
                }
            });

            //  t & c status update  active            
            $(document).on("click", ".status-deactive", function() {
                if (confirm('Are you really want to change status to active ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/termsandconditions/statusupdate/' + statusid,
                        data: {
                            status: '1',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                // $('#status_' + statusid).html('<button data-status= ' +
                                //     statusid +
                                //     ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >Active</button>'
                                // );
                                loaddata();
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
                }
            });

            // delete terms and conditions              
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/termsandconditions/delete/' + deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $(row).closest("tr").fadeOut();
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
                }
            });


            // terms and conditions form submit
            $('#tcform').submit(function(event) {
                event.preventDefault();
                loadershow();
                url = "{{ route('termsandconditions.store') }}";
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
                            toastr.success(response.message);
                            $('#tcform')[0].reset();
                            $('#t_and_c').summernote('code', '');
                            loaddata();
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



            // invoice number settings 


            function getinvoicepatterns() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('invoicenumberpatterns.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.pattern != '') {
                            var data = response.pattern;

                            // Update the HTML content
                            $.each(data, function(key, value) {
                                var pattern = value.invoice_pattern;
                                var modifiedfiedpattern = pattern.replace('cidai',
                                        "(Auto increment according to customer)")
                                    .replace('ai', "(Auto increment according to invoice)");
                                if (value.pattern_type == "global") {
                                    $('#globalinvoicepattern').text(modifiedfiedpattern);
                                } else {
                                    $('#domesticinvoicepattern').text(modifiedfiedpattern);
                                }
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#domesticinvoicepattern').text('Yet not set');
                            $('#globalinvoicepattern').text('Yet not set');

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
                        toastr.error(errorMessage);
                    }
                });
            }
            getinvoicepatterns();


            $('#invoicepattern').on('change', function() {

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

            $(document).on('click', '.invoicepatterninputdlt', function() {
                var inputtype = $(this).data('type');

                if (inputtype === 'ai' || inputtype === 'cidai') {
                    $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop('disabled',
                        false);
                }
                $(this).closest('div').remove();
            });

            $('#resetpatterninput').on('click', function() {
                $('#invoicenumberinputs').html(' ');
                $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop('disabled',
                    false);
            });

            $('#invoicenumberpatternform').on('submit', function(e) {
                e.preventDefault();

                var inputs = [];
                $('div.invoicepatterninput > input').each(function() {
                    inputs.push({
                        type: $(this).data('type'),
                        value: $(this).val()
                    });
                });

                var hasAnyAiInput = inputs.some(function(input) {
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
                        success: function(response) {
                            // Handle the response from the server
                            if (response.status == 200) {
                                // You can perform additional actions, such as showing a success message or redirecting the user
                                toastr.success(response.message);
                                $('#invoicenumberpatternform')[0].reset();
                                $('#invoicenumberinputs').html(' ');
                                $('#invoicepattern').find(
                                    'option[value="ai"],option[value="cidai"]').prop(
                                    'disabled',
                                    false);
                                getinvoicepatterns();
                            } else if (response.status == 1) {
                                if (confirm(response.message)) {
                                    startpatternfromoldnumber(data);
                                } else {
                                    $('#invoicenumberpatternform')[0].reset();
                                    $('#invoicenumberinputs').html(' ');
                                    $('#invoicepattern').find(
                                        'option[value="ai"],option[value="cidai"]').prop(
                                        'disabled',
                                        false);
                                }
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
                } else {
                    toastr.error('Please select atleast one auto increment input');
                }
            });

            // increment will start from last paused number 
            function startpatternfromoldnumber(data) {
                data.onconfirm = 'yes';
                $.ajax({
                    type: 'POST',
                    url: "{{ route('invoicepattern.store') }}",
                    data: data,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#invoicenumberpatternform')[0].reset();
                            $('#invoicenumberinputs').html(' ');
                            $('#invoicepattern').find('option[value="ai"],option[value="cidai"]').prop(
                                'disabled',
                                false);
                            getinvoicepatterns();
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
            }

            $('#cidform').on('submit', function(e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customerid.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#cidform')[0].reset();
                            getoverduedays();
                        }else if (response.status == 500) {
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

        });
    </script>
@endpush
