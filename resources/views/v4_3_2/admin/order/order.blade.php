@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Orders
@endsection
@section('table_title')
    Orders
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
@if (session('user_permissions.teamodule.order.add') == '1')
    @section('addnew')
        {{ route('admin.orderform') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Order"
            class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif
@section('advancefilter')
    <div class="col-sm-12 text-right px-4">
        <button class="btn btn-sm btn-primary m-0" data-toggle="tooltip" data-placement="bottom" data-original-title="Filters"
            onclick="showOffCannvas()">
            <i class="ri-filter-line"></i>
        </button>
    </div>
@endsection
@section('sidebar-filters')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6>Final Amount</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1">
                        <label for="filter_final_amount_from">From</label>
                        <input type="number" class="form-control filter" name="filter_final_amount_from"
                            id="filter_final_amount_from" placeholder="From Final Amount">
                    </div>
                    <div class="col-6 mb-1">
                        <label for="filter_final_amount_to">To</label>
                        <input type="number" class="form-control filter" name="filter_final_amount_to"
                            id="filter_final_amount_to" placeholder="To Final Amount">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6>Credit Days</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1">
                        <label for="filter_credit_days_from">From</label>
                        <input type="number" class="form-control filter" name="filter_credit_days_from"
                            id="filter_credit_days_from" placeholder="From Credit Days">
                    </div>
                    <div class="col-6 mb-1">
                        <label for="filter_credit_days_to">To</label>
                        <input type="number" class="form-control filter" name="filter_credit_days_to"
                            id="filter_credit_days_to" placeholder="To Credit Days">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6>Buyer</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_buyer" class="form-label mt-1">Buyer</label>
                        <select name="filter_buyer" class="filter form-control w-100 select2" id="filter_buyer" multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6>Transport</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_transport" class="form-label mt-1">Transport</label>
                        <select name="filter_transport" class="filter form-control w-100 select2" id="filter_transport"
                            multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6>Garden</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_garden" class="form-label mt-1">Garden</label>
                        <select name="filter_garden" class="filter form-control w-100 select2" id="filter_garden" multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Grade</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_grade" class="form-label mt-1">Grade</label>
                        <select name="filter_grade" class="filter form-control w-100 select2" id="filter_grade" multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('table-content')
    <button data-toggle="tooltip" data-placement="bottom" data-original-title="Create Report" class="btn btn-sm btn-primary"
        id="pdfBtn">
        <span id="pdf-data">Generate Report</span>
    </button>
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Buyer party</th>
                <th>transport</th>
                <th>credit days</th>
                <th>discount (%)</th>
                <th>totalNetKg</th>
                <th>finalAmount</th>
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
            let gettransportname = [];
            let getgardenname = [];
            let getbuyername = [];
            let getgradename = [];
            var global_response = '';

            function getBuyerData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('buyer.index') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            loaderhide();
                            resolve(response);
                        },
                        error: function(xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                            reject(xhr);
                        }
                    });
                });
            }

            function gettransportData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('transport.index') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            loaderhide();
                            resolve(response);
                        },
                        error: function(xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                            reject(xhr);
                        }
                    });
                });
            }

            function getGardenData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('garden.index') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            loaderhide();
                            resolve(response);
                        },
                        error: function(xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                            reject(xhr);
                        }
                    });
                });
            }

            function getGradeData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('grade.index') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            loaderhide();
                            resolve(response);
                        },
                        error: function(xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                            reject(xhr);
                        }
                    });
                });
            }

            function loadFilters() {
                return new Promise((resolve, reject) => {
                    var filterData = JSON.parse(sessionStorage.getItem('filterData'));
                    if (filterData) {
                        $.each(filterData, function(key, value) {
                            if (value != ' ') {
                                $('#' + key).val(value); // Removed `, true`
                            }
                        });

                        // Trigger change event to ensure multiselect UI updates
                        $('#filter_transport, #filter_buyer, #filter_garden, #filter_grade')
                            .trigger('change');

                        loaddata();


                        sessionStorage.removeItem('filterData');
                        loaderhide();
                        resolve(); // Resolve the promise here after all actions
                    } else {
                        // If no filter data, resolve immediately
                        resolve();
                        loaddata();
                    }
                });
            }
            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [gettransportDataresponse, getBuyerDataresponse, getGardenDataresponse,
                        getGradeDataresponse
                    ] = await Promise.all([
                        gettransportData(),
                        getBuyerData(),
                        getGardenData(),
                        getGradeData()
                    ]);
                    // this getCompanyData response 
                    if (gettransportDataresponse.status == 200 && gettransportDataresponse.data != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(gettransportDataresponse.data, function(key, value) {
                            var transportId = value.id;
                            var optionValue = value.name;
                            gettransportname.push(optionValue);
                            $('#filter_transport').append(
                                `<option value="${transportId}">${optionValue}</option>`
                            );
                        });
                        $('#filter_transport').val('');
                        $('#filter_transport').select2({
                            search: true,
                            placeholder: 'Select transport',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_transport').val('');
                        $('#filter_transport').select2({
                            search: true,
                            placeholder: 'No transport found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }
                    // this getBuyerData response
                    if (getBuyerDataresponse.status == 200 && getBuyerDataresponse.data != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(getBuyerDataresponse.data, function(key, value) {
                            var buyerId = value.id;
                            var optionValue = value.name;
                            getbuyername.push(optionValue);
                            $('#filter_buyer').append(
                                `<option value="${buyerId}">${optionValue}</option>`
                            );
                        });
                        $('#filter_buyer').val('');
                        $('#filter_buyer').select2({
                            search: true,
                            placeholder: 'Select Buyer',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_buyer').val('');
                        $('#filter_buyer').select2({
                            search: true,
                            placeholder: 'No buyer found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }

                    // this getGardenData response
                    if (getGardenDataresponse.status == 200 && getGardenDataresponse.data != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(getGardenDataresponse.data, function(key, value) {
                            var garderId = value.id;
                            var optionValue = value.garden_name;
                            getgardenname.push(optionValue);
                            $('#filter_garden').append(
                                `<option value="${garderId}">${optionValue}</option>`
                            );
                        });
                        $('#filter_garden').val('');
                        $('#filter_garden').select2({
                            search: true,
                            placeholder: 'Select Garden',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_garden').val('');
                        $('#filter_garden').select2({
                            search: true,
                            placeholder: 'No garden found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }

                    //   // this getGradeData response
                    if (getGradeDataresponse.status == 200 && getGradeDataresponse.data != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(getGradeDataresponse.data, function(key, value) {
                            var gradeId = value.id;
                            var optionValue = value.grade;
                            getgradename.push(optionValue);
                            $('#filter_grade').append(
                                `<option value="${gradeId}">${optionValue}</option>`
                            );
                        });
                        $('#filter_grade').val('');
                        $('#filter_grade').select2({
                            search: true,
                            placeholder: 'Select Grade',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_grade').val('');
                        $('#filter_grade').select2({
                            search: true,
                            placeholder: 'No grade found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }

                    loaderhide();
                    await loadFilters();

                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: "error",
                        title: "An error occurred while initializing"
                    });
                    loaderhide();
                }
            }

            initialize();
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
                        url: "{{ route('order.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_credit_days_from = $('#filter_credit_days_from').val();
                            d.filter_credit_days_to = $('#filter_credit_days_to').val();
                            d.filter_final_amount_from = $('#filter_final_amount_from').val();
                            d.filter_final_amount_to = $('#filter_final_amount_to').val();
                            d.filter_transport = $('#filter_transport').val();
                            d.filter_buyer = $('#filter_buyer').val();
                            d.filter_garden = $('#filter_garden').val();
                            d.filter_grade = $('#filter_grade').val();
                        },
                        dataSrc: function(json) {
                            $("#pdfBtn").removeClass('d-none');
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Something went wrong!'
                                })
                                $("#pdfBtn").addClass('d-none');
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
                            data: 'buyer_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'buyer_name'
                        },
                        {
                            data: 'transport_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'transport_name'
                        },

                        {
                            data: 'credit_days',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'credit_days'
                        },
                        {
                            data: 'discount',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'discount'
                        },
                        {
                            data: 'totalNetKg',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'totalNetKg'
                        },
                        {
                            data: 'final_amount',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'final_amount'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.teamodule.teadashboard.view') == '1')
                                    actionBtns += `   
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button"  data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0 ">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.teamodule.teadashboard.edit') == '1')
                                    let editUrl =
                                        `{{ route('admin.orderupdateform', '__id__') }}`
                                        .replace(
                                            '__id__', data);
                                    actionBtns += `
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Order">
                                            <a href=${editUrl}>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.teamodule.teadashboard.delete') == '1')
                                    actionBtns += `
                                         <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Order">
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
            let params;
            $('#pdfBtn').on('click', function() {

                params = table.ajax.params();
                params.filter_buyer = $('#filter_buyer').val();
                params.filter_transport = $('#filter_transport').val();
                params.filter_garden = $('#filter_garden').val();
                params.filter_grade = $('#filter_grade').val();
                params.filter_credit_days_from = $('#filter_credit_days_from').val();
                params.filter_credit_days_to = $('#filter_credit_days_to').val();
                params.filter_final_amount_from = $('#filter_final_amount_from').val();
                params.filter_final_amount_to = $('#filter_final_amount_to').val();
                let queryString = $.param(params);


                let url = "{{ route('brokragbill.orderreport') }}" + "?" + queryString;
                loadershow();

                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokragbill.orderreport') }}",
                    data: params,
                    success: function(response) {
                        window.open(url, '_blank');
                        loaderhide();
                    },
                    error: function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            });

            $(document).on("click", ".view-btn", function() {
                $("#exampleModalScrollable .modal-dialog").addClass('modal-xl');

                var orderId = $(this).data('view'); // get order id
                var order = global_response.data.find(o => o.id == orderId); // find the order
                if (!order) return;


                var tableHtml = `
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2">Buyer Name</th>
                            <th>Transport Name</th>
                            <th>Credit Days</th>
                            <th>Discount</th>
                            <th>Final Amount</th>
                            <th colspan="2">Total Net KG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2">${order.buyer_name || '-'}</td>
                            <td>${order.transport_name || '-'}</td>
                            <td>${order.credit_days || '-'}</td>
                            <td>${order.discount || '-'}</td>
                            <td>${order.final_amount || '-'}</td>
                            <td colspan="2">${order.totalNetKg || '-'}</td>
                        </tr>
                    </tbody>
                </table>

                <h5>Order Details</h5>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Garden Name</th>
                            <th>Grade Name</th>
                            <th>Invoice No</th>
                            <th>Bags</th>
                            <th>KG</th>
                            <th>Net KG</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
            `;


                $.each(order.details, function(key, detail) {
                    tableHtml += `
                    <tr>
                        <td>${detail.garden_name || '-'}</td>
                        <td>${detail.grade_name || '-'}</td>
                        <td>${detail.invoice_no || '-'}</td>
                        <td>${detail.bags || '-'}</td>
                        <td>${detail.kg || '-'}</td>
                        <td>${detail.net_kg || '-'}</td>
                        <td>${detail.rate || '-'}</td>
                        <td>${detail.amount || '-'}</td>
                    </tr>
                `;
                });

                tableHtml += `
                    </tbody>
                </table>
            `;

                // Inject into modal
                $('#details').html(tableHtml);
            });


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
                            "{{ route('order.delete', '__deleteId__') }}"
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
            $('.applyfilters').on('click', function() {
                table.draw();
                hideOffCanvass(); // close OffCanvass
            });

            //remove filtres
            $('.removefilters').on('click', function() {
                $('#filter_transport').val(null).trigger('change');
                $('#filter_buyer').val(null).trigger('change');
                $('#filter_garden').val(null).trigger('change');
                $('#filter_grade').val(null).trigger('change');
                $('#filter_credit_days_from').val('');
                $('#filter_credit_days_to').val('');
                $('#filter_final_amount_from').val('');
                $('#filter_final_amount_to').val('');
                table.draw();
                hideOffCanvass(); // close OffCanvass
            });



        });
    </script>
@endpush
