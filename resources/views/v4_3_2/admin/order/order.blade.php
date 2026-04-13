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

    /* ───────── FILTER BAR ───────── */
    .filter-bar {
        background: #fff;
        border: 1px solid #dde3ef;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 14px;
    }
    .filter-bar-header {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eaecf2;
    }
    .filter-bar-inner {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    /* ───────── SCROLL AREA ───────── */
    .filter-scroll-area {
        display: flex;
        align-items: flex-start;
        gap: 0;
        overflow-x: auto;
        flex: 1;
        padding-bottom: 4px;
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
    }
    .filter-scroll-area::-webkit-scrollbar { height: 4px; }
    .filter-scroll-area::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

    /* ───────── FILTER FIELD ───────── */
    .filter-field {
        flex: 0 0 160px;
        min-width: 160px;
        padding: 10px 12px;
        border-radius: 0;
        box-sizing: border-box;
        border-right: 1px solid #eaecf2;
    }
    .filter-field:last-child { border-right: none; }

    /* Range fields wider */
    .filter-field.range-field {
        flex: 0 0 260px;
        min-width: 260px;
    }

    /* Alternating background — odd WHITE, even GREY */
    .filter-scroll-area .filter-field:nth-child(odd) {
        background-color: #ffffff;
    }
    .filter-scroll-area .filter-field:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.04);
    }

    /* First and last get rounded corners */
    .filter-scroll-area .filter-field:first-child { border-radius: 8px 0 0 8px; }
    .filter-scroll-area .filter-field:last-child  { border-radius: 0 8px 8px 0; }
    .filter-scroll-area .filter-field:only-child  { border-radius: 8px; }

    /* Labels */
    .filter-field label {
        display: block;
        font-size: 10.5px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 6px;
        white-space: nowrap;
    }

    /* Inputs */
    .filter-field .form-control {
        height: 34px;
        font-size: 13px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        width: 100%;
        box-sizing: border-box;
        background: transparent !important;
    }
    .filter-field .form-control:focus {
        border-color: #3b5bdb;
        box-shadow: 0 0 0 2px rgba(59,91,219,.1);
        outline: none;
    }

    /* Select2 */
    .filter-field .select2-container {
        width: 100% !important;
    }
    .filter-field .select2-selection--single,
    .filter-field .select2-selection--multiple {
        border-radius: 6px !important;
        border: 1px solid #d1d5db !important;
        background-color: transparent !important;
        min-height: 34px !important;
        box-sizing: border-box;
    }
    .filter-field .select2-container--default .select2-selection--single {
        height: 34px !important;
        display: flex !important;
        align-items: center;
    }
    .filter-field .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        font-size: 13px !important;
        padding-left: 10px !important;
    }
    .filter-field .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .filter-field .select2-container--default.select2-container--focus .select2-selection--single,
    .filter-field .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b5bdb !important;
        box-shadow: 0 0 0 2px rgba(59,91,219,.1) !important;
    }
    /* Tags */
    .filter-field .select2-selection__choice {
        background: #3b5bdb !important;
        border: none !important;
        color: #fff !important;
        border-radius: 5px !important;
        font-size: 12px !important;
        padding: 2px 8px !important;
        margin: 2px !important;
    }
    .filter-field .select2-selection__choice__remove {
        color: #fff !important;
        margin-right: 4px !important;
    }

    /* ───────── RANGE GROUP ───────── */
    .range-group {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .range-item {
        flex: 1;
        min-width: 0;
    }
    .range-item input.form-control {
        width: 100% !important;
        height: 34px;
        font-size: 12px;
        padding: 4px 6px;
        box-sizing: border-box;
        min-width: 0;
        background: transparent !important;
    }
    .range-sep {
        font-size: 12px;
        color: #9ca3af;
        flex-shrink: 0;
    }

    /* ───────── ACTION BUTTONS ───────── */
    .filter-actions-fixed {
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding-left: 12px;
        border-left: 1px solid #eaecf2;
    }
    .filter-actions-fixed .btn {
        height: 34px;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        white-space: nowrap;
        width: 90px;
        margin: 0;
    }

    /* ───────── ACTION BAR ───────── */
    .action-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .action-bar .btn {
        height: 32px;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    /* ───────── MOBILE ───────── */
    @media (max-width: 576px) {
        .filter-bar-inner { flex-direction: column; align-items: stretch; }
        .filter-scroll-area { flex-direction: column; overflow-x: visible; gap: 6px; }
        .filter-field {
            flex: 1 1 100%;
            min-width: 100%;
            border-right: none;
            border-radius: 8px !important;
            border-bottom: 1px solid #eaecf2;
        }
        .filter-field .select2-container { width: 100% !important; }
        .range-group {
            gap: 6px;
        }

        .range-sep {
            display: none; /* hide the dash on mobile */
        }

        .range-item input.form-control {
            width: 100%; /* full width */
        }

        /* Adjust filter-field for mobile */
        .filter-field.range-field {
            flex: 1 1 100%;
            min-width: 100%;
            max-width: 100%;
            border-radius: 8px !important;
            border-bottom: 1px solid #eaecf2;
        }
        .filter-actions-fixed .btn { flex: 1; width: auto; }
        .action-bar { flex-direction: column; align-items: stretch; }
        .action-bar .btn { justify-content: center; }
        .filter-field .range-field { max-width:350px;}
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

@section('table-content')

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <div class="filter-bar-header">
        <i class="ri-filter-3-line"></i> Filters
    </div>
    <div class="filter-bar-inner">

        <div class="filter-scroll-area">

            <div class="filter-field">
                <label>Company</label>
                <select name="filter_company" class="filter form-control select2" id="filter_company">
                     <option value="">Select Company</option>
                </select>
            </div>

            <div class="filter-field">
                <label>Invoice Status</label>
                <select name="filter_invoice_status" class="filter form-control select2" id="filter_invoice_status">
                    <option value="">select  Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Half Invoice">Half Invoice</option>
                    <option value="Invoices Created">Invoices Created</option>
                </select>
            </div>

            <div class="filter-field">
                <label>Buyer</label>
                <select name="filter_buyer" class="filter form-control select2" id="filter_buyer">
                    <option value="">Select Buyer</option>
                </select>
            </div>

            <div class="filter-field">
                <label>Garden</label>
                <select name="filter_garden" class="filter form-control select2" id="filter_garden" multiple></select>
            </div>

            <div class="filter-field">
                <label>Transport</label>
                <select name="filter_transport" class="filter form-control select2" id="filter_transport" multiple></select>
            </div>

            <div class="filter-field">
                <label>Grade</label>
                <select name="filter_grade" class="filter form-control select2" id="filter_grade" multiple></select>
            </div>

            <div class="filter-field range-field">
                <label>Order Date From <span class="m-2"></span> Order Date To</label>
                <div class="range-group">
                    <div class="range-item">
                        <input type="date" class="form-control filter" id="filter_date_from" name="filter_date_from" placeholder="From">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="date" class="form-control filter" id="filter_date_to" name="filter_date_to" placeholder="To">
                    </div>
                </div>
            </div>

            <div class="filter-field range-field">
                <label>Amount From <span class="m-3"></span> Amount To</label>
                <div class="range-group">
                    <div class="range-item">
                        <input type="number" class="form-control filter" id="filter_final_amount_from" name="filter_final_amount_from" placeholder="From">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="number" class="form-control filter" id="filter_final_amount_to" name="filter_final_amount_to" placeholder="To">
                    </div>
                </div>
            </div>

            <div class="filter-field range-field">
                <label>Credit Days From <span class="m-2"></span> Credit Days To</label>
                <div class="range-group">
                    <div class="range-item">
                        <input type="number" class="form-control filter" id="filter_credit_days_from" name="filter_credit_days_from" placeholder="From">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="number" class="form-control filter" id="filter_credit_days_to" name="filter_credit_days_to" placeholder="To">
                    </div>
                </div>
            </div>

        </div>

        <div class="filter-actions-fixed">
            <button class="btn btn-primary applyfilters">
                <i class="ri-filter-line"></i> Apply
            </button>
            <button class="btn btn-outline-secondary removefilters">
                <i class="ri-close-line"></i> Clear
            </button>
        </div>

    </div>
</div>

{{-- ── Action Bar ── --}}
<div class="action-bar">
    <button class="btn btn-primary" id="pdfBtn">
        <i class="ri-file-chart-line"></i> Generate Report
    </button>
    <button class="btn btn-success" id="excelBtn">
        <i class="ri-file-excel-2-line"></i> Export Excel
    </button>
</div>

<table id="data" class="table display table-bordered table-striped w-100">
    <thead>
        <tr>
            <th>Id</th>
            <th>Order Date</th>
            <th>Company Name</th>
            <th>Gardens</th>
            <th>Buyer</th>
            <th>Invoice/Lot No</th>
            <th>Grades</th>
            <th>Invoice Status</th>
            <th>Total Net Kg</th>
            <th>Order Pdf</th>
            <th>Rate </th>
            <th>Final Amount</th>
            <th>Credit Days</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="tabledata"></tbody>
</table>

@endsection

@push('ajax')
<script>
$('document').ready(function () {

    let gettransportname = [], getgardenname = [], getbuyername = [], getgradename = [], getcompanyname = [];
    var global_response = '';

    const API_TOKEN  = "{{ session()->get('api_token') }}";
    const COMPANY_ID = "{{ session()->get('company_id') }}";
    const USER_ID    = "{{ session()->get('user_id') }}";

    /* ── Data fetchers ── */
    function fetchData(url) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET', url,
                data: { user_id: USER_ID, company_id: COMPANY_ID, token: API_TOKEN },
                success: r => { loaderhide(); resolve(r); },
                error: xhr => { loaderhide(); handleAjaxError(xhr); reject(xhr); }
            });
        });
    }

    /* ── Init Select2 helper ── */
    function initSelect2(selector, placeholder, multiple = false) {
        $(selector).select2({
            placeholder,
            allowClear: true,
            width: '100%',
            ...(multiple ? {} : {})
        });
    }

    /* ── Load saved filters from sessionStorage ── */
    function loadFilters() {
        return new Promise(resolve => {
            var fd = JSON.parse(sessionStorage.getItem('filterData'));
            if (fd) {
                $.each(fd, function (k, v) { if (v != ' ') $('#' + k).val(v); });
                $('#filter_company,#filter_transport,#filter_buyer,#filter_garden,#filter_grade,#filter_invoice_status').trigger('change');
                loaddata();
                sessionStorage.removeItem('filterData');
                loaderhide();
            } else {
                loaddata();
            }
            resolve();
        });
    }

    /* ── Initialize all filters ── */
    async function initialize() {
        try {
            const [companyRes, transportRes, buyerRes, gardenRes, gradeRes] = await Promise.all([
                fetchData("{{ route('companymaster.index') }}"),
                fetchData("{{ route('transport.index') }}"),
                fetchData("{{ route('buyer.index') }}"),
                fetchData("{{ route('garden.index') }}"),
                fetchData("{{ route('grade.index') }}")
            ]);

            // Company
            if (companyRes.status == 200 && companyRes.data.length) {
                companyRes.data.forEach(v => $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`));
            }
            initSelect2('#filter_company', 'Select Company');

            // Invoice Status
            initSelect2('#filter_invoice_status', 'Select Invoice Status');

            // Transport
            if (transportRes.status == 200 && transportRes.data.length) {
                transportRes.data.forEach(v => $('#filter_transport').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initSelect2('#filter_transport', 'Select Transport', true);

            // Buyer
            if (buyerRes.status == 200 && buyerRes.data.length) {
                buyerRes.data.forEach(v => $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initSelect2('#filter_buyer', 'Select Buyer');

            // Garden
            if (gardenRes.status == 200 && gardenRes.data.length) {
                gardenRes.data.forEach(v => $('#filter_garden').append(`<option value="${v.id}">${v.garden_name}</option>`));
            }
            initSelect2('#filter_garden', 'Select Garden', true);

            // Grade
            if (gradeRes.status == 200 && gradeRes.data.length) {
                gradeRes.data.forEach(v => $('#filter_grade').append(`<option value="${v.id}">${v.grade}</option>`));
            }
            initSelect2('#filter_grade', 'Select Grade', true);

            loaderhide();
            await loadFilters();

        } catch (e) {
            console.error(e);
            Toast.fire({ icon: 'error', title: 'An error occurred while initializing' });
            loaderhide();
        }
    }

    initialize();

    /* ── DataTable ── */
    let table = '';

    function loaddata() {
        loadershow();
        table = $('#data').DataTable({
            language: { lengthMenu: '_MENU_ &nbsp;Entries per page' },
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                type: 'GET',
                url: "{{ route('order.index') }}",
                data: function (d) {
                    d.user_id               = USER_ID;
                    d.company_id            = COMPANY_ID;
                    d.token                 = API_TOKEN;
                    d.filter_credit_days_from  = $('#filter_credit_days_from').val();
                    d.filter_credit_days_to    = $('#filter_credit_days_to').val();
                    d.filter_final_amount_from = $('#filter_final_amount_from').val();
                    d.filter_final_amount_to   = $('#filter_final_amount_to').val();
                    d.filter_date_from         = $('#filter_date_from').val();
                    d.filter_date_to           = $('#filter_date_to').val();
                    d.filter_transport         = $('#filter_transport').val();
                    d.filter_buyer             = $('#filter_buyer').val();
                    d.filter_garden            = $('#filter_garden').val();
                    d.filter_grade             = $('#filter_grade').val();
                    d.filter_company           = $('#filter_company').val();
                    d.filter_invoice_status    = $('#filter_invoice_status').val();
                },
                dataSrc: function (json) {
                    $('#pdfBtn').removeClass('d-none');
                    $('#excelBtn').removeClass('d-none');
                    if (json.message) {
                        Toast.fire({ icon: 'error', title: json.message || 'Something went wrong!' });
                        $('#pdfBtn').addClass('d-none');
                        $('#excelBtn').addClass('d-none');
                    }
                    global_response = json;
                    return json.data;
                },
                complete: () => loaderhide(),
                error: xhr => {
                    global_response = '';
                    console.log(xhr.responseText);
                    Toast.fire({ icon: 'error', title: 'Error loading data' });
                }
            },
            order: [[0, 'desc']],
            columns: [
                { data: 'id',             name: 'id',             orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'order_date',     name: 'order_date',     orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'company_names', name: 'company_names', orderable: true, searchable: true, defaultContent: '-',
                    render: (data) => {
                        if (!data) return '-';
                        let s = data.length > 20 ? data.substring(0,20)+'...' : data;
                        return `<span data-toggle="tooltip" data-original-title="${data}">${s}</span>`;
                    }
                },
                {
                    data: 'garden_names',
                    name: 'garden_names',
                    orderable: true,
                    searchable: true,
                    defaultContent: '-',

                    render: (data) => {
                        if (!data) return '-';
                        let s = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `<span data-toggle="tooltip" data-original-title="${data}">${s}</span>`;
                    },

                    createdCell: function (td, cellData, rowData) {

                        let hasInvalidCompany = false;

                        if (rowData.company_names) {
                            let companies = rowData.company_names.split(',');

                            hasInvalidCompany = companies.some(c => {
                                return !c || c.trim() === '-' || c.trim() === '';
                            });
                        } else {
                            hasInvalidCompany = true;
                        }

                        if (hasInvalidCompany) {
                            $(td).css({
                                'background-color': 'red',
                                'color': 'white'
                            });
                        }
                    }
                },
                { data: 'buyer_name',     name: 'buyer_name',     orderable: true,  searchable: true,  defaultContent: '-' 
                    ,render:(data) =>{
                        if(!data) return '<span style = "color:red"> Buyer Not Selected </span>';
                        else return data;
                    }
                },
                {
                    data: 'invoice_nos', name: 'invoice_nos', orderable: true, searchable: true, defaultContent: '-',
                    render: (data) => {
                        if (!data) return '-';
                        let s = data.length > 15 ? data.substring(0,15)+'...' : data;
                        return `<span data-toggle="tooltip" data-original-title="${data}">${s}</span>`;
                    }
                },
                { data: 'grades',         name: 'grades',         orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'invoice_status', name: 'invoice_status', orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'totalNetKg',     name: 'totalNetKg',     orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'id', name: 'id', orderable: false, searchable: false,
                    render: (data) => {
                        let btns = '';
                        @if (session('user_permissions.teamodule.teadashboard.view') == '1')
                            let pdfUrl = `{{ route('admin.orderpdf', '__id__') }}`.replace('__id__', data);
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Download Pdf">
                                <a href="${pdfUrl}" target="_blank">
                                    <button class="download-btn btn btn-info btn-rounded btn-sm my-0"><i class="ri-download-line"></i></button>
                                </a></span>`;
                        @endif
                        return btns;
                    }
                },
                { data: 'rate',   name: 'rate',   orderable: true,  searchable: true,  defaultContent: '-' , render: (data) => {
                        if (!data) return '-';
                        let s = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `<span data-toggle="tooltip" data-original-title="${data}">${s}</span>`;
                    },},
                { data: 'final_amount',   name: 'final_amount',   orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'credit_days',    name: 'credit_days',    orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'id', name: 'id', orderable: false, searchable: false,
                    render: (data, type, row) => {
                        let btns = '';
                        @if (session('user_permissions.teamodule.teadashboard.view') == '1')
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                <button type="button" data-view="${data}" data-toggle="modal" data-target="#exampleModalScrollable"
                                    class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                    <i class="ri-indent-decrease"></i>
                                </button></span>`;
                        @endif
                        @if (session('user_permissions.teamodule.teadashboard.edit') == '1')
                            let editUrl = `{{ route('admin.orderupdateform', '__id__') }}`.replace('__id__', data);
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Order">
                                <a href="${editUrl}">
                                    <button class="btn btn-success btn-rounded btn-sm my-0"><i class="ri-edit-fill"></i></button>
                                </a></span>`;
                        @endif
                        @if (session('user_permissions.teamodule.teadashboard.delete') == '1')
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Order">
                                <button type="button" data-id="${data}" class="del-btn btn btn-danger btn-rounded btn-sm my-0">
                                    <i class="ri-delete-bin-fill"></i>
                                </button></span>`;
                        @endif
                        return btns;
                    }
                }
            ],
            pagingType: 'full_numbers',
            drawCallback: function () {
                $('[data-toggle="tooltip"]').tooltip({ boundary: 'window', offset: '0, 10' });
                if ($('#jumpToPageWrapper').length === 0) {
                    $(".dt-paging").after(`
                        <div id="jumpToPageWrapper" class="d-flex align-items-center ml-3" style="gap:5px;">
                            <label for="jumpToPage" class="mb-0">Jump to page:</label>
                            <input type="number" id="jumpToPage" min="1" class="dt-input" style="width:80px;" />
                            <button id="jumpToPageBtn" class="btn btn-sm btn-primary">Go</button>
                        </div>`);
                }
                $(document).off('click', '#jumpToPageBtn').on('click', '#jumpToPageBtn', function () {
                    if ($.fn.DataTable.isDataTable('#data')) {
                        let t = $('#data').DataTable();
                        let page = parseInt($('#jumpToPage').val());
                        let total = t.page.info().pages;
                        if (!isNaN(page) && page > 0 && page <= total) {
                            t.page(page - 1).draw('page');
                        } else {
                            Toast.fire({ icon: 'error', title: `Enter a page between 1 and ${total}` });
                        }
                    }
                });
            }
        });
    }

    /* ── Generate Report ── */
    let params;
    /* ── Generate Report (shared helper) ── */
    function exportReport(type) {
        let params = table.ajax.params();
        params.filter_buyer             = $('#filter_buyer').val();
        params.filter_transport         = $('#filter_transport').val();
        params.filter_garden            = $('#filter_garden').val();
        params.filter_company           = $('#filter_company').val();
        params.filter_invoice_status    = $('#filter_invoice_status').val();
        params.filter_grade             = $('#filter_grade').val();
        params.filter_credit_days_from  = $('#filter_credit_days_from').val();
        params.filter_credit_days_to    = $('#filter_credit_days_to').val();
        params.filter_final_amount_from = $('#filter_final_amount_from').val();
        params.filter_final_amount_to   = $('#filter_final_amount_to').val();
        params.filter_date_from         = $('#filter_date_from').val();
        params.filter_date_to           = $('#filter_date_to').val();
        params.type                     = type;   // <-- key addition

        let url = "{{ route('brokragbill.orderreport') }}" + '?' + $.param(params);

        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('brokragbill.orderreport') }}",
            data: params,
            success: function () {
                if (type === 'pdf') {
                    window.open(url, '_blank');   // stream in new tab
                } else {
                    window.location.href = url;   // direct download for excel
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    }

    $('#pdfBtn').on('click',   function () { exportReport('pdf');   });
    $('#excelBtn').on('click', function () { exportReport('excel'); });

    /* ── View Order Details ── */
    $(document).on('click', '.view-btn', function () {
        $("#exampleModalScrollable .modal-dialog").addClass('modal-xl');
        var orderId = $(this).data('view');
        var order = global_response.data.find(o => o.id == orderId);
        if (!order) return;

        var html = `
            <table class="table table-bordered">
                <thead><tr>
                    <th colspan="2">Buyer</th><th>Transport</th><th>Credit Days</th>
                    <th>Discount</th><th>Final Amount</th><th colspan="2">Total Net KG</th>
                </tr></thead>
                <tbody><tr>
                    <td colspan="2">${order.buyer_name||'-'}</td>
                    <td>${order.transport_name||'-'}</td>
                    <td>${order.credit_days||'-'}</td>
                    <td>${order.discount||'-'}</td> 
                    <td>${order.final_amount||'-'}</td>
                    <td colspan="2">${order.totalNetKg||'-'}</td>
                </tr></tbody>
            </table>
            <table class="table" style="border-top: none;">
                <tr>
                    <td colspan="8" class="p-0" style="background-color: white;"> <div class="p-2" style="text-align: center;"><h5>Order Details</h5></div></td>
                </tr>
               
                </table>
            <table class="table table-striped table-bordered">
                <thead><tr>
                    <th>Garden</th><th>Grade</th><th>Invoice/Lot No</th>
                    <th>Bags</th><th>KG</th><th>Net KG</th><th>Rate</th><th>Amount</th>
                </tr></thead>
                <tbody>`;

        $.each(order.details, function (k, d) {
            html += `<tr>
                <td>${d.garden_name||'-'}</td><td>${d.grade_name||'-'}</td><td>${d.invoice_no||'-'}</td>
                <td>${d.bags||'-'}</td><td>${d.kg||'-'}</td><td>${d.net_kg||'-'}</td>
                <td>${d.rate||'-'}</td><td>${d.amount||'-'}</td>
            </tr>`;
        });
        html += `</tbody></table>`;
        $('#details').html(html);
    });

    /* ── Delete ── */
    $(document).on('click', '.del-btn', function () {
        var deleteid = $(this).data('id');
        showConfirmationDialog('Are you sure?', 'to delete this record?', 'Yes, delete', 'No, cancel', 'question',
            () => {
                loadershow();
                $.ajax({
                    type: 'PUT',
                    url: "{{ route('order.delete', '__deleteId__') }}".replace('__deleteId__', deleteid),
                    data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                    success: r => {
                        loaderhide();
                        Toast.fire({ icon: r.status==200?'success':'error', title: r.message });
                        if (r.status == 200) { loaddata(); }
                    },
                    error: xhr => { loaderhide(); handleAjaxError(xhr); }
                });
            }
        );
    });

    /* ── Apply / Clear filters ── */
    $('.applyfilters').on('click', function () { table.draw(); });

    $('.removefilters').on('click', function () {
        $('#filter_transport').val(null).trigger('change');
        $('#filter_company').val(null).trigger('change');
        $('#filter_invoice_status').val(null).trigger('change');
        $('#filter_buyer').val(null).trigger('change');
        $('#filter_garden').val(null).trigger('change');
        $('#filter_grade').val(null).trigger('change');
        $('#filter_credit_days_from, #filter_credit_days_to').val('');
        $('#filter_final_amount_from, #filter_final_amount_to').val('');
        $('#filter_date_from, #filter_date_to').val('');
        table.draw();
    });

});
</script>
@endpush