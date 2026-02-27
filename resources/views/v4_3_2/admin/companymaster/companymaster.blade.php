@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Companymaster
@endsection
@section('table_title')
    Companymaster
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
@if (session('user_permissions.teamodule.companymaster.add') == '1')
    @section('addnew')
        {{ route('admin.companymasterform') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New companymaster"
            class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <div class="modal fade" id="bankDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-ls" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Bank Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="bankdetailform" name="bankdetailform">
                        @csrf
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <input type="hidden" name="user_id" class="form-control"
                                        value="{{ session('user_id') }}" placeholder="user_id" required />
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" placeholder="token" required />
                                    <input type="hidden" name="company_id" class="form-control"
                                        value="{{ session('company_id') }}" placeholder="company_id" required />
                                    <input type="hidden" name="bank_companymaster_id" id="bank_companymaster_id"
                                        class="form-control" value="" placeholder="bank_companymaster_id" required />
                                    <label for="name">Holder Name</label><span style="color:red;">*</span>
                                    <input id="name" type="text" name="holder_name" class="form-control"
                                        placeholder="Holder Name"  />
                                    <span class="modal-error-msg" id="modal-error-holder_name" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="account_number">Account Number</label><span style="color:red;">*</span>
                                    <input type="text" name="account_number" class="form-control" id="account_number"
                                        value="" placeholder="Account Number"  />
                                    <span class="modal-error-msg" id="modal-error-account_number"
                                        style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="swift_code">Swift Code</label>
                                    <input type="text" name="swift_code" class="form-control" id="swift_code"
                                        value="" placeholder="Swift Code" />
                                    <span class="modal-error-msg" id="modal-error-swift_code" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="ifsc_code">IFSC Code</label><span style="color:red;">*</span>
                                    <input type="text" id="ifsc_code" name="ifsc_code" class="form-control"
                                        placeholder="IFSC Code"  />
                                    <span class="modal-error-msg" id="modal-error-ifsc_code" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="bank_name">Bank Name</label><span style="color:red;">*</span>
                                    <input type="text" id="bank_name" name="bank_name" class="form-control"
                                        placeholder="Bank Name"  />
                                    <span class="modal-error-msg" id="modal-error-bank_name" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="branch_name">Branch Name</label>
                                    <input type="text" id="branch_name" name="branch_name" class="form-control"
                                        placeholder="Branch Name" />
                                    <span class="modal-error-msg" id="modal-error-branch_name"
                                        style="color: red"></span>
                                </div>
                                <div class="col-sm-12">
                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Cancel" id="modalcancelbtn"
                                        class="btn btn-secondary float-right">Cancel</button>
                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Reset Details"
                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Save Details"
                                        class="btn btn-primary float-right my-0">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>company name</th>
                <th>email</th>
                <th>Gardens</th>
                <th>contact person name</th>
                <th>mobile 1</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';

            // function for  get companymasters data and set it into datatable
            function loaddata() {
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
                        url: "{{ route('companymaster.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Something went wrong!'
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
                    columns: [{
                            data: 'id',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'id'
                        },
                        {
                            data: 'company_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'company_name'
                        },
                        {
                            data: 'email',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'email'
                        },
                        {
                            data: 'garden_names',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'garden_names'
                        },
                        {
                            data: 'contact_person_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'contact_person_name'
                        },
                        {
                            data: 'mobile_1',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'mobile_1'
                        },

                        {
                            data: 'address',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'address'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.teamodule.companymaster.view') == '1')
                                    actionBtns += `   
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button"  data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0 ">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                actionBtns += `   
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Add Bank">
                                        <button type="button" 
                                            class="btn btn-info btn-rounded btn-sm my-0 addBankBtn"
                                            data-id="${data}"
                                            data-toggle="modal"
                                            data-target="#bankDetailModal">
                                            <i class="ri-bank-line"></i>
                                        </button>
                                    </span>
                                `;
                                @if (session('user_permissions.teamodule.companymaster.edit') == '1')
                                    let editUrl =
                                        `{{ route('admin.companymasterupdateform', '__id__') }}`
                                        .replace(
                                            '__id__', data);
                                    actionBtns += `
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit companymaster">
                                            <a href=${editUrl}>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.teamodule.companymaster.delete') == '1')
                                    actionBtns += `
                                         <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete companymaster Details">
                                                <button type="button" data-id= '${data}' class="del-btn btn btn-danger btn-rounded btn-sm my-0">
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
            $(document).on('click', '.addBankBtn', function() {
                let companyId = $(this).data('id');

                $('#bankdetailform #bank_companymaster_id').val(companyId);

                // Optional: Reset form
                $('#bankdetailform')[0].reset();
            });
            $('#modalcancelbtn').on('click', function() {
                $('#bankdetailform')[0].reset();
                $('#bank_companymaster_id').val('');
                $('#bankDetailModal').modal('hide');
            });
            $('#bankdetailform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text(''); // clear previous errors

                const formdata = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('bank_detail.store') }}",
                    data: formdata,
                    success: function(response) {

                        if (response.status == 200) {

                            // Reset form
                            $('#bankdetailform')[0].reset();

                            // Hide modal
                            $('#bankDetailModal').modal('hide');

                            table.draw();

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
                    },

                    error: function(xhr) {
                        loaderhide();
                        handleModalAjaxError(xhr);
                    }
                });
            });
            $(document).on("click", ".view-btn", function() {
                // Make modal extra large
                $("#exampleModalScrollable .modal-dialog").addClass('modal-xl');

                // Clear previous content
                $('#details').html('');

                var data = $(this).data('view');

                $.each(global_response.data, function(key, company) {
                    if (company.id == data) {

                        // Build bank details table
                        let bankHtml = '';
                        if (company.bank_details && company.bank_details.length > 0) {
                            bankHtml += `
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr><th colspan="4" class="text-center bg-white"><b>Bank Details</b></th></tr>
                            <tr>
                                <th>Bank Name</th>
                                <th>Holder Name</th>
                                <th>Account No</th>
                                <th>IFSC Code</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                            $.each(company.bank_details, function(i, bank) {
                                bankHtml += `
                        <tr>
                            <td>${bank.bank_name || '-'}</td>
                            <td>${bank.holder_name || '-'}</td>
                            <td>${bank.account_no || '-'}</td>
                            <td>${bank.ifsc_code || '-'}</td>
                        </tr>
                    `;
                            });
                            bankHtml += `</tbody></table>`;
                        } else {
                            bankHtml = `
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr><th colspan="4" class="text-center bg-white"><b>Bank Details</b></th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No Bank Details Available</td>
                            </tr>
                        </tbody>
                    </table>
                `;
                        }

                        // Build company details table
                        let companyHtml = `
                <table class="table table-bordered table-striped mb-0">
                    <tbody>
                        <tr>
                            <th width="30%">Id</th>
                            <td>${company.id || '-'}</td>
                        </tr>
                        <tr>
                            <th>Company Name</th>
                            <td>${company.company_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>${company.email || '-'}</td>
                        </tr>
                        <tr>
                            <th>Contact Person Name</th>
                            <td>${company.contact_person_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>Gardens</th>
                            <td>${company.garden_names || '-'}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>
                                ${company.mobile_1 || ''}
                                ${company.mobile_1 && company.mobile_2 ? ' / ' : ''}
                                ${company.mobile_2 || '-'}
                            </td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>
                                 ${
                                    [
                                    company.address,
                                    company.pincode,
                                    company.city_name,
                                    company.state_name,
                                    company.country_name
                                    ]
                                    .filter(Boolean) // removes null, undefined, empty string
                                    .join(', ')
                                }
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0;">${bankHtml}</td>
                        </tr>
                    </tbody>
                </table>
            `;

                        $('#details').html(companyHtml);
                    }
                });
            });
            //call data function for load companymaster data
            loaddata();

            // record delete 
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
                        let companymasterDeleteUrl =
                            "{{ route('companymaster.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: companymasterDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                loaderhide();
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    loaddata();
                                    table.drow();
                                } else if (response.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
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



        });
    </script>
@endpush
