@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - ConsignorCopy
@endsection
@section('table_title')
    Consignor Copy
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
@if (session('user_permissions.logisticmodule.consignorcopy.add') == '1')
    @section('addnew')
        {{ route('admin.addconsignorcopy') }}
    @endsection
    @section('addnewbutton')
        <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Consignorcopy"
            class="btn btn-sm btn-primary">
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif

@section('advancefilter')
    <div class="col-sm-12 text-right">
        <button class="btn btn-sm btn-primary m-0 mr-3" data-toggle="tooltip" data-placement="bottom"
            data-original-title="Filters" onclick="showOffCannvas()">
            <i class="ri-filter-line"></i>
        </button>
    </div>
@endsection

@section('sidebar-filters')
    <div class="col-12 p-0">
        <div class="card">
            <div class="card-header">
                <h6>Consignment Number</h6>
            </div>
            <div class="card-body">
                <input type="text" class="form-control filter" name="filter_consignment_no" id="filter_consignment_no"
                    placeholder="Consignment No">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Container Number</h6>
            </div>
            <div class="card-body">
                <input type="text" class="form-control filter" name="filter_container_no" id="filter_container_no"
                    placeholder="Container No">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Loading Date</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="filter_loading_date_from">From</label>
                        <input type="date" class="form-control filter" name="filter_loading_date_from"
                            id="filter_loading_date_from">
                    </div>
                    <div class="col-6">
                        <label for="filter_loading_date_to">To</label>
                        <input type="date" class="form-control filter" name="filter_loading_date_to"
                            id="filter_loading_date_to">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Stuffing Date</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="filter_stuffing_date_from">From</label>
                        <input type="date" class="form-control filter" name="filter_stuffing_date_from"
                            id="filter_stuffing_date_from">
                    </div>
                    <div class="col-6">
                        <label for="filter_stuffing_date_to">To</label>
                        <input type="date" class="form-control filter" name="filter_stuffing_date_to"
                            id="filter_stuffing_date_to">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Truck Number</h6>
            </div>
            <div class="card-body">
                <input type="text" class="form-control filter" name="filter_truck_no" id="filter_truck_no"
                    placeholder="Truck No">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Location</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1">
                        <label for="filter_location_from">From</label>
                        <input type="text" class="form-control filter" name="filter_location_from"
                            id="filter_location_from">
                    </div>
                    <div class="col-6 mb-1">
                        <label for="filter_location_to">To</label>
                        <input type="text" class="form-control filter" name="filter_location_to" id="filter_location_to">
                    </div>
                    <div class="col-6 mb-1">
                        <label for="filter_location_to_2">To</label>
                        <input type="text" class="form-control filter" name="filter_location_to_2"
                            id="filter_location_to_2">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Consignee</h6>
            </div>
            <div class="card-body">
                <select class="form-control filter" name="filter_consignee" id="filter_consignee">
                </select>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Consignor</h6>
            </div>
            <div class="card-body">
                <select class="form-control filter" name="filter_consignor" id="filter_consignor">
                </select>
            </div>
        </div>

    </div>
@endsection

@section('table-content')
    <table id="data" class="table  table-bordered display w-100 table-striped">
        <thead>
            <tr>
                <th>Consignment Note No</th>
                <th>Consignor</th>
                <th>Consignee</th>
                <th>Container No</th>
                <th>Type</th>
                <th>To Pay</th>
                <th>PDF</th>
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

            @if (Session::has('message'))
                Toast.fire({
                    icon: "error",
                    title: 'Something went wrong!'
                });
            @endif

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';

            var table = '' ;


            function getConsigneeData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('consignee.getconsigneelist') }}",
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


            function getConsignorData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('consignor.getconsignorlist') }}",
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
                    const [consigneeDataResponse, consignorDataResponse] = await Promise.all(
                        [
                            getConsigneeData(),
                            getConsignorData()
                        ]);

                    // Check if user data is successfully fetched
                    if (consigneeDataResponse.status == 200 && consigneeDataResponse.consignee != '') {
                        // You can update your HTML with the data here if needed
                        $.each(consigneeDataResponse.consignee, function(key, value) {
                            const consigneeDetails = [value.firstname, value.lastname, value
                                .company_name, value.contact_no
                            ].filter(Boolean).join(' - ');

                            if (value.is_active == 1) {
                                $('#filter_consignee').append(
                                    `<option value='${value.id}'>${consigneeDetails}</option>`
                                )
                            }
                        });
                        $('#filter_consignee').val('');
                        $('#filter_consignee').select2({
                            search: true,
                            placeholder: 'Select a Consignee',
                            allowClear: true // Optional: adds "clear" (x) button
                        }); // search bar in consignee list
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_consignee').append(`<option disabled '>No Data found </option>`);
                    }

                    // Check if lead stage data is successfully fetched
                    if (consignorDataResponse.status == 200 && consignorDataResponse.consignor != '') {
                        // You can update your HTML with the data here if needed
                        $.each(consignorDataResponse.consignor, function(key, value) {
                            const consignorDetails = [value.firstname, value.lastname, value
                                .company_name, value
                                .contact_no
                            ].filter(Boolean).join(' - ');

                            if (value.is_active == 1) {
                                $('#filter_consignor').append(
                                    `<option value='${value.id}'>${consignorDetails}</option>`
                                )
                            }
                        });
                        $('#filter_consignor').val('');
                        $('#filter_consignor').select2({
                            search: true,
                            placeholder: 'Select a Consignor',
                            allowClear: true // Optional: adds "clear" (x) button
                        }); // search bar in consignor list
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_consignor').append(`<option disabled '>No Data found </option>`);
                    }

                    loaderhide();

                    // Further code execution after successful AJAX calls and HTML appending

                    await loaddata();

                } catch (error) {
                    console.error('Error:', error);
                    loaderhide();
                    $('#filter_consignee').append(`<option disabled '>No Data found </option>`);
                    $('#filter_consignor').append(`<option disabled '>No Data found </option>`);

                    $('#data').DataTable({
                        responsive: true,
                        "destroy": true, //use for reinitialize datatable
                    });
                }
            }

            initialize();


            // load consignor copy  data in table 
            function loaddata() {
                table = $('#data').DataTable({
                    language: {
                        lengthMenu: '_MENU_ &nbsp;Entries per page'
                    },
                    destroy: true, // allows re-initialization
                    responsive : true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('consignorcopy.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_consignment_no = $('#filter_consignment_no').val();
                            d.filter_container_no = $('#filter_container_no').val();
                            d.filter_loading_date_from = $('#filter_loading_date_from').val();
                            d.filter_loading_date_to = $('#filter_loading_date_to').val();
                            d.filter_stuffing_date_from = $('#filter_stuffing_date_from').val();
                            d.filter_stuffing_date_to = $('#filter_stuffing_date_to').val();
                            d.filter_truck_no = $('#filter_truck_no').val();
                            d.filter_location_from = $('#filter_location_from').val();
                            d.filter_location_to = $('#filter_location_to').val();
                            d.filter_location_to_2 = $('#filter_location_to_2').val();
                            d.filter_consignee = $('#filter_consignee').val();
                            d.filter_consignor = $('#filter_consignor').val();
                        },
                        dataSrc: function(json) { 
                            if (json.status === 200) {
                                tancid = json.latesttcid != null ? json.latesttcid.id : null;
                            }

                            if(json.message){
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Something went wrong!'
                                })
                            }
                            return json.data;
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
                    order: [[0,'desc']],
                    columns: [

                        {
                            data: 'consignment_note_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'consignment_note_no'
                        },
                        {
                            data: 'consignor',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'consignor'
                        },
                        {
                            data: 'consignee',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'consignee'
                        },
                        {
                            data: 'container_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'container_no'
                        },
                        {
                            data: 'type',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'type'
                        },
                        {
                            data: 'to_pay',
                            name: 'to_pay',
                            orderable: true,
                            searchable: false,
                             defaultContent: '-',
                        },
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            name: 'consignor_copy.id',
                            render: function(data, type, row) {
                                let generateConsignorCopyPdfUrl =
                                    "{{ route('consignorcopy.generatepdf', '__consignorcopyId__') }}"
                                    .replace('__consignorcopyId__', data);
                                @if (session('user_permissions.logisticmodule.consignorcopy.view') == '1')
                                    return `
                                        <span data-toggle="tooltip" data-placement="left" title="Download Consignor Copy Pdf">
                                            <a href="${generateConsignorCopyPdfUrl}" target="_blank" id="pdf">
                                                <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0">
                                                    <i class="ri-download-line"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @else
                                    return `-`;
                                @endif
                            }
                        },
                        {
                            data: 'id',
                            name: 'consignor_copy.id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';

                                // T&C Update Button
                                if (tancid != null && row.t_and_c_id != tancid) {
                                    actionBtns += `
                                        <span>
                                            <button type="button" data-id='${row.id}' data-toggle="tooltip" data-placement="bottom" title="Update Terms & Conditions"
                                                class="update-t-and-c-btn btn btn-outline-primary btn-rounded btn-sm my-0">
                                                <i class="ri-file-edit-line"></i>
                                            </button>
                                        </span>
                                    `;
                                }

                                @if (session('user_permissions.logisticmodule.consignorcopy.edit') == '1')
                                    let consignorCopyEditUrl =
                                        `{{ route('admin.editconsignorcopy', '__id__') }}`.replace(
                                            '__id__', row.id);
                                    actionBtns += `
                                        <span>
                                            <a href="${consignorCopyEditUrl}">
                                                <button type="button" data-id='${row.id}' data-toggle="tooltip" data-placement="bottom" title="Edit Consignor Copy"
                                                    class="edit-btn btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.logisticmodule.consignorcopy.delete') == '1')
                                    actionBtns += `
                                        <span>
                                            <button type="button" data-id='${row.id}' data-toggle="tooltip" data-placement="bottom" title="Delete"
                                                class="del-btn btn btn-danger btn-rounded btn-sm my-0">
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
                        $('[data-toggle="tooltip"]').tooltip();

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

            // filters 

            //apply filters

            $('#applyfilters').on('click', function() {
                table.draw();
                hideOffCanvass(); // close OffCanvass

            });




            //remove filters
            $('#removefilters').on('click', function() {
                $('input.filter').val('');
                // Clear Select2 filters properly and trigger change
                $('#filter_consignee').val(null).trigger('change');
                $('#filter_consignor').val(null).trigger('change');

                hideOffCanvass(); // close OffCanvass

                table.draw();

            });


            // delete consignor copy             
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                let consignorCopyDltUrl = "{{ route('consignorcopy.delete', '__deleteId__') }}".replace(
                    '__deleteId__', deleteid);
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record ?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: consignorCopyDltUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: "succesfully deleted"
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

            // update consignor copy terms and conditions           
            $(document).on("click", ".update-t-and-c-btn", function() {
                var tandcid = $(this).data('id');
                let consignorCopyDltUrl = "{{ route('consignorcopy.updatetandc', '__tandcId__') }}"
                    .replace(
                        '__tandcId__', tandcid);
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to update terms and conditions ?', // Text
                    'Yes, update', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: consignorCopyDltUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: "Terms and condition succesfully updated"
                                    });
                                    $(row).hide();
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


        });
    </script>
@endpush
