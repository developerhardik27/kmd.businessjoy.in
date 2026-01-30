@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Sample Purchase List
@endsection
@section('table_title')
    Sample Purchase List
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
@if (session('user_permissions.teamodule.brokerpurchase.add') == '1')
    @section('addnew')
        {{ route('admin.brokerpurchaseform') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Sample Purchase"
            class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif
@section('advancefilter')
    <div class="col-sm-12 text-right px-4">
        @if (session('user_permissions.invoicemodule.invoice.add') == 1)
            <button id="createInvoiceBtn" class="btn btn-sm btn-info m-0 mr-1 mb-md-0 mb-1 float-left d-none"
                data-toggle="tooltip" data-placement="bottom" data-original-title="Create Invoice">
                <i class="ri-bill-line"></i> Create Invoice
            </button>
        @endif
        <button class="btn btn-sm btn-primary m-0" data-toggle="tooltip" data-placement="bottom"
            data-original-title="Filters" onclick="showOffCannvas()">
            <i class="ri-filter-line"></i>
        </button>
    </div>
@endsection
@section('sidebar-filters')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6>Net Kg</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1">
                        <label for="filter_net_kg_from">From</label>
                        <input type="number" class="form-control filter" name="filter_net_kg_from" id="filter_net_kg_from"
                            placeholder="From Net Kg">
                    </div>
                    <div class="col-6 mb-1">
                        <label for="filter_net_kg_to">To</label>
                        <input type="number" class="form-control filter" name="filter_net_kg_to" id="filter_net_kg_to"
                            placeholder="To Net Kg">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6>Bags</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-1">
                        <label for="filter_bags_from">From</label>
                        <input type="number" class="form-control filter" name="filter_bags_from" id="filter_bags_from"
                            placeholder="From Bags">
                    </div>
                    <div class="col-6 mb-1">
                        <label for="filter_bags_to">To</label>
                        <input type="number" class="form-control filter" name="filter_bags_to" id="filter_bags_to"
                            placeholder="To Bags">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6>Company</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_company" class="form-label mt-1">Company</label>
                        <select name="filter_company" class="filter form-control w-100 select2" id="filter_company"
                            multiple>
                        </select>
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
        <div class="card">
            <div class="card-header">
                <h6>Created Date</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <label for="filter_from_date" class="form-label">From:</label>
                        <input type="date" id="filter_from_date" class="filter form-input form-control">
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="filter_to_date" class="form-label">To:</label>
                        <input type="date" id="filter_to_date" class="filter form-input form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('table-content')
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                {{-- <th>#</th> --}}
                <th>Id</th>
                <th>Order id</th>
                <th>Company Name</th>
                <th>Buyer</th>
                <th>Transport</th>
                <th>Garden</th>
                <th>Invoice No</th>
                <th>Grade</th>
                <th>Bags</th>
                <th>Net kg</th>
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
            let getcompanyname = [];
            let getgardenname = [];
            let getbuyername = [];
            let getgradename = [];


            function getCompanyData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('companymaster.index') }}",
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
                        $('#filter_company, #filter_buyer, #filter_garden, #filter_grade')
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
                    const [getCompanyDataresponse, getBuyerDataresponse, getGardenDataresponse,
                        getGradeDataresponse
                    ] = await Promise.all([
                        getCompanyData(),
                        getBuyerData(),
                        getGardenData(),
                        getGradeData()
                    ]);
                    // this getCompanyData response 
                    if (getCompanyDataresponse.status == 200 && getCompanyDataresponse.data != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(getCompanyDataresponse.data, function(key, value) {
                            var companyId = value.id;
                            var optionValue = value.company_name;
                            getcompanyname.push(optionValue);
                            $('#filter_company').append(
                                `<option value="${companyId}">${optionValue}</option>`
                            );
                        });
                        $('#filter_company').val('');
                        $('#filter_company').select2({
                            search: true,
                            placeholder: 'Select Company',
                            allowClear: true 
                        });
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_company').val('');
                        $('#filter_company').select2({
                            search: true,
                            placeholder: 'No company found',
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
            var global_response = '';

            $(document).on('change', '.purchase-checkbox', function() {

                let companyId = $(this).data('company-id');
                let buyerParty = $(this).data('buyer-party');
                let isChecked = $(this).is(':checked');


                $('.purchase-checkbox').prop('checked', false);


                if (isChecked) {
                    $('.purchase-checkbox').each(function() {
                        if (
                            $(this).data('company-id') == companyId &&
                            $(this).data('buyer-party') == buyerParty
                        ) {
                            $(this).prop('checked', true);
                        }
                    });
                }

                toggleCreateInvoiceBtn();
            });

            function toggleCreateInvoiceBtn() {
                let checkedBoxes = $('.purchase-checkbox:checked');


                if (checkedBoxes.length === 0) {
                    $('#createInvoiceBtn').addClass('d-none');

                    $('#createInvoiceBtn').removeAttr('data-company-id data-buyer-party');
                    return;
                }


                $('#createInvoiceBtn').removeClass('d-none');


                let companyIds = [];
                let buyerParties = [];

                checkedBoxes.each(function() {
                    companyIds.push($(this).data('company-id'));
                    buyerParties.push($(this).data('buyer-party'));
                });


                companyIds = [...new Set(companyIds)];
                buyerParties = [...new Set(buyerParties)];


                $('#createInvoiceBtn')
                    .attr('data-company-id', companyIds.join(','))
                    .attr('data-buyer-party', buyerParties.join(','));


                console.log('Button company_id:', $('#createInvoiceBtn').data('company-id'));
                console.log('Button buyer_party:', $('#createInvoiceBtn').data('buyer-party'));
            }

            $("#createInvoiceBtn").click(function() {
                loadershow();
                let companyIds = ($('#createInvoiceBtn').data('company-id') || '').toString();
                let buyerParties = ($('#createInvoiceBtn').data('buyer-party') || '')
                    .toString();

                if (!companyIds || !buyerParties) {
                    Toast.fire({
                        icon: 'error',
                        title: 'No company or buyer selected'
                    });
                    loaderhide();
                    return;
                }
                $.ajax({
                    url: "{{ route('brokerpurchase.createInvoice') }}", // your route
                    type: "GET", // use POST for creating invoice
                    data: {
                        company_ids: companyIds, // "1,2,3"
                        buyer_parties: buyerParties, // "ABC,XYZ"
                        user_id: "{{ session()->get('user_id') }}", // optional extra data
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        _token: "{{ csrf_token() }}" // CSRF token required for POST
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            data = response.data;
                            $.post("{{ route('admin.storeInvoiceSession') }}", {
                                _token: "{{ csrf_token() }}",
                                data: response.data
                            }, function() {
                                window.location.href =
                                    "{{ route('admin.addinvoice') }}";
                            });

                            $('.purchase-checkbox').prop('checked', false);
                            toggleCreateInvoiceBtn();
                            loaddata();
                        } else {
                            loaderhide();
                            Toast.fire({
                                icon: 'error',
                                title: response.message ||
                                    'Something went wrong'
                            });
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        console.log(xhr.responseText);
                        Toast.fire({
                            icon: 'error',
                            title: 'AJAX request failed'
                        });
                    }
                });
            });


            // function for  get brokerpurchases data and set it into datatable
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
                        url: "{{ route('brokerpurchase.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_net_kg_from = $('#filter_net_kg_from').val();
                            d.filter_net_kg_to = $('#filter_net_kg_to').val();
                            d.filter_bags_from = $('#filter_bags_from').val();
                            d.filter_bags_to = $('#filter_bags_to').val();
                            d.filter_company = $('#filter_company').val();
                            d.filter_buyer = $('#filter_buyer').val();
                            d.filter_garden = $('#filter_garden').val();
                            d.filter_grade = $('#filter_grade').val();
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
                            defaultContent: '',
                            name: 'id',
                            render: function(data, type, row) {
                                if (row.invoice_id == null) {
                                    // If invoice_id is null, show unchecked checkbox
                                    return `
                                        <input type="checkbox"
                                            class="purchase-checkbox"
                                            value="${row.company_id}"
                                            data-company-id="${row.company_id}"
                                            data-buyer-party="${row.buyer_party}">
                                    `;
                                }

                            }
                        },
                        // {
                        //     data: 'id',
                        //     orderable: true,
                        //     searchable: true,
                        //     defaultContent: '-',
                        //     name: 'id',
                        // },
                        {
                            data: 'order_id',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'order_id'
                        },
                        {
                            data: 'company_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'company_name'
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
                            data: 'garden_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'garden_name'
                        },
                        {
                            data: 'invoice_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'invoice_no'
                        },
                        {
                            data: 'grade_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'grade_name'
                        },
                        {
                            data: 'bags',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'bags'
                        },
                        {
                            data: 'net_kg',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'net_kg'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.teamodule.brokerpurchase.view') == '1')
                                    actionBtns += `   
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button"  data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0 ">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.teamodule.brokerpurchase.edit') == '1')
                                    let editUrl =
                                        `{{ route('admin.brokerpurchaseupdateform', '__id__') }}`
                                        .replace(
                                            '__id__', data);
                                    actionBtns += `
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Sample Purchase">
                                            <a href=${editUrl}>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.teamodule.brokerpurchase.delete') == '1')
                                    actionBtns += `
                                         <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete brokerpurchase Details">
                                                <button type="button" data-id= '${data}' class="del-btn btn btn-danger btn-rounded btn-sm my-0">
                                                    <i class="ri-delete-bin-fill"></i>
                                                </button>
                                            </span>
                                    `;
                                @endif
                                if (row.invoice_id) {
                                    @if (session('user_permissions.invoicemodule.invoice.view') == '1')

                                        let generateInvoicePdfUrl =
                                            "{{ route('invoice.generatepdf', '__invoiceId__') }}"
                                            .replace('__invoiceId__', row.invoice_id);
                                        actionBtns += `                                             
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Download Invoice Pdf">
                                            <a href=${generateInvoicePdfUrl} target='_blank' id='pdf'>
                                                <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0" ><i class="ri-download-line"></i></button>
                                            </a>
                                        </span>
                                    `;
                                    @endif
                                }
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


                        $(document).off('click', '#jumpToPageBtn').on('click',
                            '#jumpToPageBtn',
                            function() {
                                let table = $('#data').DataTable();
                                // Check if table is initialized
                                if ($.fn.DataTable.isDataTable('#data')) {
                                    let page = parseInt($('#jumpToPage').val());
                                    let totalPages = table.page.info().pages;

                                    if (!isNaN(page) && page > 0 && page <=
                                        totalPages) {
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
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, brokerpurchase) {
                    if (brokerpurchase.id == data) {

                        $('#details').append(`
                        <tr>
                            <th>Id</th>
                            <td>${brokerpurchase.id || '-'}</td>
                        </tr>
                        <tr>
                            <th>Company</th>
                            <td>${brokerpurchase.company_name|| '-'}</td>
                        <tr>
                            <th>Garden Name</th>
                            <td>${brokerpurchase.garden_name|| '-'}</td>
                        </tr>
                         <tr>
                            <th>Invoice No</th>
                            <td>${brokerpurchase.invoice_no|| '-'}</td>
                        </tr>
                         <tr>
                            <th>Grade</th>
                            <td>${brokerpurchase.grade_name|| '-'}</td>
                        </tr>
                         <tr>
                            <th>Bags</th>
                            <td>${brokerpurchase.bags|| '-'}</td>
                        </tr>
                         <tr>
                            <th>Net kg</th>
                            <td>${brokerpurchase.net_kg|| '-'}</td>
                        </tr>
                        
                    `);
                    }
                });
            });
            //call data function for load brokerpurchase data
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
                        let brokerpurchaseDeleteUrl =
                            "{{ route('brokerpurchase.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: brokerpurchaseDeleteUrl,
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
                                    var responseJSON = JSON.parse(xhr
                                        .responseText);
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
                $('#filter_company').val(null).trigger('change');
                $('#filter_buyer').val(null).trigger('change');
                $('#filter_garden').val(null).trigger('change');
                $('#filter_grade').val(null).trigger('change');
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $('#filter_net_kg_from').val('');
                $('#filter_net_kg_to').val('');
                $('#filter_bags_from').val('');
                $('#filter_bags_to').val('');

                table.draw();
                hideOffCanvass(); // close OffCanvass
            });


        });
    </script>
@endpush
