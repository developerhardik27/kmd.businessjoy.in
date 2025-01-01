@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')

@section('page_title')
    {{ config('app.name') }} - Quotationlist
@endsection
@section('table_title')
    Quotation
@endsection

@section('style')
    <style>
        .ui-widget-header {
            background: #1518b117 !important;
            border: 1px solid #e1ded9 !important;
        }

        .btn-info {
            background-color: #253566 !important;
            border-color: #253566 !important;
            color: white;
        }

        .btn-info:hover {
            background-color: #39519b !important;
            color: rgb(255, 255, 255);
        }

        .btn-success {
            background-color: #67d5a5d9 !important;
            border-color: var(--iq-success) !important;
            color: black !important;
        }

        .btn-success:hover {
            background-color: #16d07ffa !important;
            border-color: var(--iq-success) !important;
            color: rgb(250, 250, 250) !important;
        }
    </style>
@endsection

@if (session('user_permissions.quotationmodule.quotation.add') == '1')
    @section('addnew')
        {{ route('admin.addquotation') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Create New Quotation">+
                Create
                New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data"
        class="table display table-bordered table-responsive-lg table-responsive-xl table-striped text-center">
        <thead>
            <tr>
                <th>Quotation ID</th>
                <th>Quotation Date</th>
                <th>Customer/Company Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>

    <div class="modal fade" id="remarksmodal" tabindex="-1" role="dialog" aria-labelledby="viewremarksmodalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewremarksmodalTitle"><b>Remarks</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="remarksform">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                            required />
                        <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                            required />
                        <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                            placeholder="token" required />
                        <input type="hidden" name="quotation_id" id="quotation_id">
                        <label for="remarks">Remarks</label>
                        <div id="remarksdiv">

                        </div>
                        <textarea readonly name="remarks" class="form-control d-none" id="remarks" placeholder="Remarks"></textarea>
                        <span class="modal_error-msg" id="error-remarks" style="color: red"></span><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="remarkseditbtn" class="btn btn-success"><i
                                class="ri-edit-fill"></i></button>
                        <button type="submit" id="remarkssubmitbtn" class="btn btn-primary d-none">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        let isEventBound = false;
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            @if (Session::has('message'))
                Toast.fire({
                    icon: "error",
                    title: 'You have not any column for download this quotation.'
                });
            @endif

            var global_response = '';
            // function for  get customers data and set it table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('quotation.quotation_list') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200 && response.quotation != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            // You can update your HTML with the data here if needed
                            $.each(response.quotation, function(key, value) {
                                var customer = '';
                                if (value.firstname != null) {
                                    customer += value.firstname;
                                }

                                if (value.lastname != null) {
                                    if (customer.length > 0) {
                                        customer +=
                                            ' '; // Add space between firstname and lastname if both are present
                                    }
                                    customer += value.lastname;
                                }
                                if (value.company_name != null) {
                                    if (customer.length > 0) {
                                        customer += ' / '; // 
                                    }
                                    customer += value.company_name;
                                }

                                let quotationEditUrl =
                                    "{{ route('admin.editquotation', '__quotationId__') }}"
                                    .replace('__quotationId__', value.id);
                                let generateQuotationPdfUrl =
                                    "{{ route('quotation.generatepdf', '__quotationId__') }}"
                                    .replace('__quotationId__', value.id);
                                $('#data').append(`<tr>
                                                        <td>${value.quotation_number != null  ? value.quotation_number : '-' }</td>
                                                        <td>${value.quotation_date_formatted}</td>
                                                        <td>${customer}</td>
                                                        <td>${value.currency_symbol} ${value.grand_total}</td>
                                                        <td> 
                                                            @if (session('user_permissions.quotationmodule.quotation.edit') == '1')
                                                                <select data-status='${value.id}' data-original-value="${value.status}" class="status w-100 form-control" id="status_${value.id}" name="" required >
                                                                    <option value='pending'>Pending Approval</option>
                                                                    <option value='accepted'>Accepted</option>
                                                                    <option value='rejected'>Rejected</option>
                                                                    <option value='expired'>Expired</option>
                                                                    <option value='revised'>Revised</option>
                                                                </select>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td> 
                                                        <td>
                                                             @if (session('user_permissions.quotationmodule.quotation.view') == '1')
                                                                <span data-toggle="tooltip" data-placement="left" data-original-title="Download Quotation Pdf">
                                                                    <a href=${generateQuotationPdfUrl} target='_blank' id='pdf'>
                                                                        <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0 mb-2" ><i class="ri-download-line"></i></button>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.quotationmodule.quotation.edit') == '1') 
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="Remarks"> 
                                                                    <button type="button" data-toggle="modal" data-target="#remarksmodal" data-id='${value.id}'  class="remarks-btn btn btn-warning btn-rounded btn-sm my-0 mb-2">
                                                                        <i class="ri-chat-smile-3-line"></i>
                                                                    </button>
                                                                </span>
                                                                ${(value.is_editable == 1)?  
                                                                        `   
                                                                                                        <span>
                                                                                                            <a href=${quotationEditUrl}>
                                                                                                                <button type="button" data-id='${value.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Quotation" class="edit-btn btn btn-success btn-rounded btn-sm my-0 mb-2">
                                                                                                                    <i class="ri-edit-fill"></i>
                                                                                                                </button>
                                                                                                            </a>
                                                                                                        </span>
                                                                                                    `
                                                                    : ''
                                                                }
                                                            @endif
                                                            @if (session('user_permissions.quotationmodule.quotation.delete') == '1')
                                                                <span>
                                                                    <button type="button" data-id='${value.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Quotation" class="del-btn btn btn-danger btn-rounded btn-sm my-0 mb-2">
                                                                        <i class="ri-delete-bin-fill"></i>
                                                                    </button>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                      
                                                    </tr>`);
                                $(`#status_${value.id}`).val(value.status);
                            });

                            var search = {!! json_encode($search) !!}
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                            $('#data').DataTable({
                                'order': [],
                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#data').append(`<tr><td colspan='7'>No Data Found</td></tr>`);
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            }
            //call data function for load customer data
            loaddata();

            // record delete 
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this quotation?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let quotationDeleteUrl = "{{ route('quotation.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: quotationDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: " {{ session()->get('company_id') }} ",
                                user_id: " {{ session()->get('user_id') }} "
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
                                        title: 'Quotation not delete'
                                    });

                                    loaderhide();
                                }
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

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.quotation, function(key, quotation) {
                    if (quotation.id == data) {
                        $.each(quotation, function(fields, value) {
                            $('#details').append(`<tr>
                                    <th>${fields}</th>
                                    <td>${value}</td>
                                    </tr>`)
                        })
                    }
                });
            });

            //status change function
            function statuschange(id, value) {
                loadershow();
                let quotationStatusUrl = "{{ route('quotation.status', '__id__') }}".replace('__id__', id);
                $.ajax({
                    type: 'PUT',
                    url: quotationStatusUrl,
                    data: {
                        status: value,
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            })
                            loaddata();
                            if (value == 'rejected') {
                                $('#remarksmodal').modal('show');
                                showRemarksModal(id);
                            }
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: 'Status not updated'
                            });
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            }

            //call status change function
            $(document).on("change", ".status", function() {
                var oldstatus = $(this).data('original-value'); //get original quotation status value
                var statusid = $(this).data('status'); // get quotation id
                var status = $(this).val(); //get current value
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change this record?', // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        element.data('original-value',
                            status); // set current value to original value
                        statuschange(statusid, status);
                        loaderhide();
                    },
                    () => {
                        // Error callback
                        $('#status_' + statusid).val(
                            oldstatus
                        ); // if user will cancelled to change status then set original value as it is
                    }
                );
            });


            function showRemarksModal(quotationId) {
                $('#remarksform')[0].reset();
                $('#remarksdiv').html('');
                $('#quotation_id').val(quotationId);
                loadershow();
                let quotationUrl = "{{ route('quotation.getquotationremarks', '__quotationId__') }}"
                    .replace('__quotationId__', quotationId);
                $.ajax({
                    type: 'GET',
                    url: quotationUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#remarksdiv').html(response.remarks);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                    }
                });
            }

            // form reset every time when on click make payment button
            $(document).on('click', '.remarks-btn', function() {
                var quotationid = $(this).data('id');
                showRemarksModal(quotationid);
            });

            $('#remarkseditbtn').on('click', function() {
                $(this).addClass('d-none');
                $('#remarksdiv').addClass('d-none');
                $('#remarks').removeClass('d-none').attr('readonly', false);
                $('#remarkssubmitbtn').removeClass('d-none');
                // Initialize Summernote
                $('#remarks').summernote({
                    height: 300, // Adjust the height as needed
                    focus: true, // Set focus to the editor
                });
                $('#remarks').summernote('code', $('#remarksdiv').html());
            });


            // Open modal: remove aria-hidden and manage focus
            $('#remarksmodal').on('shown.bs.modal', function() {
                $(this).removeAttr('aria-hidden'); // Remove aria-hidden when modal is shown
            });

            // reset payment details in modal when modal will close
            $("#remarksmodal").on("hidden.bs.modal", function() {
                // Set aria-hidden to true when the modal is hidden
                $(this).attr('aria-hidden', 'true');

                // Toggle visibility of the edit button and submit button
                $('#remarksdiv').removeClass('d-none');
                $('#remarkseditbtn').removeClass('d-none');
                $('#remarks').attr('readonly', true);
                $('#remarkssubmitbtn').addClass('d-none');
                $('#remarks').addClass('d-none');

                // Destroy Summernote editor when modal is closed
                // This ensures that Summernote is properly destroyed when modal is hidden
                $('#remarks').summernote('code', '');
                $('#remarks').summernote('destroy');

                // Reset the textarea to plain text (optional: if you want to reset it)
                $('#remarks').val($('#remarks').val());
            });

            // payment form submit 
            $('#remarksform').submit(function(event) {
                $('#modal_error-msg').text('');
                event.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('quotation.updatequotationremarks') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            })
                            $('#remarksform')[0].reset(); // Reset form
                            // Destroy Summernote when the modal is closed
                            $('#remarks').summernote('code', '');
                            $('#remarks').summernote('destroy');
                            $('#remarksmodal').modal('hide');

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
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
                });
            });

        });
    </script>
@endpush
