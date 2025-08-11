@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

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
    <table id="data" class="table display table-bordered table-striped w-100">
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
            let table = '';
            // function for  get customers data and set it table
            function loaddata() {
                loadershow();
                table = $('#data').DataTable({
                    language: {
                        lengthMenu: '_MENU_ &nbsp;Entries per page'
                    },
                    destroy: true, // allows re-initialization
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('quotation.quotation_list') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Somethint went wrong!'
                                })
                            }

                            global_response = json;

                            return json.data;
                        },
                        complete: function() {
                            loaderhide();
                        },
                        error: function(xhr) {
                            global_response = '';
                            console.log(xhr.responseText);
                            Toast.fire({
                                icon: "error",
                                title: "Error loading data"
                            });
                        }
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [

                        {
                            data: 'quotation_number',
                            name: 'quotation_number',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'quotation_date_formatted',
                            name: 'quotation_date_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'grand_total',
                            name: 'grand_total',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                return `${row.currency_symbol} ${row.grand_total}`;
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function (data, type, row) {
                                actions = '';
                                @if (session('user_permissions.quotationmodule.quotation.edit') == '1')
                                    actions = ` 
                                        <select data-status='${row.id}' data-original-value="${data}" class="status w-100 form-control" id="status_${row.id}">
                                            <option value='pending' ${data == "pending" ? 'selected' : ''}>Pending Approval</option>
                                            <option value='accepted' ${data == "accepted" ? 'selected' : ''}>Accepted</option>
                                            <option value='rejected' ${data == "rejected" ? 'selected' : ''}>Rejected</option>
                                            <option value='expired' ${data == "expired" ? 'selected' : ''}>Expired</option>
                                            <option value='revised' ${data == "revised" ? 'selected' : ''}>Revised</option>
                                        </select> 
                                    `;
                                @endif
                                return actions || '-';
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';
                                 @if (session('user_permissions.quotationmodule.quotation.view') == '1')
                                    let generateQuotationPdfUrl = "{{ route('quotation.generatepdf', '__quotationId__') }}".replace('__quotationId__', data);
                                
                                    actionBtns += `
                                            <span data-toggle="tooltip" data-placement="left" data-original-title="Download Quotation Pdf">
                                                <a href=${generateQuotationPdfUrl} target='_blank' id='pdf'>
                                                    <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0 mb-2" >
                                                        <i class="ri-download-line"></i>
                                                    </button>
                                                </a>
                                            </span>
                                        `;
                                @endif

                                @if (session('user_permissions.quotationmodule.quotation.edit') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Remarks"> 
                                            <button type="button" data-toggle="modal" data-target="#remarksmodal" data-id='${data}'  class="remarks-btn btn btn-warning btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-chat-smile-3-line"></i>
                                            </button>
                                        </span>
                                    `;
                                    let quotationEditUrl =
                                        "{{ route('admin.editquotation', '__quotationId__') }}".replace('__quotationId__', data);
                                    if (row.is_editable == 1) {
                                        actionBtns += `   
                                            <span>
                                                <a href=${quotationEditUrl}>
                                                    <button type="button" data-id='${data}' data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Quotation" class="edit-btn btn btn-success btn-rounded btn-sm my-0 mb-2">
                                                        <i class="ri-edit-fill"></i>
                                                    </button>
                                                </a>
                                            </span>
                                        `;
                                    }
                                @endif 
                                @if (session('user_permissions.quotationmodule.quotation.delete') == '1')
                                    actionBtns += `
                                        <span>
                                            <button type="button" data-id='${data}' data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Quotation" class="del-btn btn btn-danger btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif

                                return actionBtns;
                            }
                        }
                    ],

                    pagingType: "full_numbers",
                    drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip({
                            boundary: 'window',
                            offset: '0, 10' // Push tooltip slightly away from the button
                        });

                        // 👇 Jump to Page input injection
                        if ($('#jumpToPageWrapper').length === 0) {
                            let jumpHtml = `
                                    <div id="jumpToPageWrapper" class="d-flex align-items-center ml-3" style="gap: 5px;">
                                        <label for="jumpToPage" class="mb-0">Jump to page:</label>
                                        <input type="number" id="jumpToPage" min="1" class="dt-input" style="width: 80px;" />
                                        <button id="jumpToPageBtn" class="btn btn-sm btn-primary">Go</button>
                                    </div>
                                `;
                            $(".dt-paging").after(jumpHtml);
                        }


                        $(document).off('click', '#jumpToPageBtn').on('click', '#jumpToPageBtn',
                            function() {
                                let table = $('#data').DataTable();
                                // Check if table is initialized
                                if ($.fn.DataTable.isDataTable('#data')) {
                                    let page = parseInt($('#jumpToPage').val());
                                    let totalPages = table.page.info().pages;

                                    if (!isNaN(page) && page > 0 && page <= totalPages) {
                                        table.page(page - 1).draw('page');
                                    } else {
                                        Toast.fire({
                                            icon: "error",
                                            title: `Please enter a page number between 1 and ${totalPages}`
                                        });
                                    }
                                } else {

                                    Toast.fire({
                                        icon: "error",
                                        title: `DataTable not yet initialized.`
                                    });
                                }
                            }
                        );
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
                                    table.draw();
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
                            table.draw();
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
