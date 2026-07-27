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
            display: none;
        }
        .range-item input.form-control {
            width: 100%;
        }
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
                     <option value="blank_company">No Company</option>
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
                <label>Sample Status</label>
                <select name="filter_sample_status" class="filter form-control select2" id="filter_sample_status">
                    <option value="">select  Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Half Sample">Half Sample</option>
                    <option value="Sample Created">Sample Created</option>
                </select>
            </div>
            <div class="filter-field">
                <label>Buyer</label>
                <select name="filter_buyer" class="filter form-control select2" id="filter_buyer">
                    <option value="">Select Buyer</option>
                    <option value="blank_buyer">No Buyer</option>
                </select>
            </div>
            <div class="filter-field">
                <label>Reference</label>
                <select name="filter_reference" class="filter form-control select2" id="filter_reference">
                    <option value="">Select Reference</option>
                </select>
            </div>
            <div class="filter-field">
                <label>Contact Person</label>
                <select name="filter_contact_person" class="filter form-control select2" id="filter_contact_person">
                    <option value="">Select Contact Person</option>
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
                <label>Expected Dispatch Date From <span class="m-2"></span> Expected Dispatch Date To</label>
                <div class="range-group">
                    <div class="range-item">
                        <input type="date" class="form-control filter" id="filter_expected_dispatch_date_from" name="filter_expected_dispatch_date_from" placeholder="From">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="date" class="form-control filter" id="filter_expected_dispatch_date_to" name="filter_expected_dispatch_date_to" placeholder="To">
                    </div>
                </div>
            </div>
            <div class="filter-field">
                <label>Dispatch Status</label>
                <select name="filter_dispatch_status" class="filter form-control select2" id="filter_dispatch_status">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                </select>
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
            <th>Reference</th>
            <th>Invoice/Lot No</th>
            <th>Grades</th>
            <th>Invoice Status</th>
            <th>Sample Status</th>
            <th>Dispatch Status</th>
            <th>Expected Dispatch Date</th>
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
<table id="totals_table" class="table table-bordered table-striped w-100 mt-3">
    <thead>
        <tr>
            <th>Total Order</th>
            <th>Total Bags</th>
            <th>Total Net Kg</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td id="total_orders">-</td>
            <td id="total_bags">-</td>
            <td id="total_net_kg">-</td>
            <td id="total_amount">-</td>
        </tr>
    </tbody>
</table>
{{-- ── Dispatch Status Modal ── --}}
<div class="modal fade" id="dispatchStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Dispatch Status</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form id="dispatchStatusForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ session('api_token') }}">
                    <input type="hidden" name="user_id" value="{{ session('user_id') }}">
                    <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                    <input type="hidden" id="dispatch_order_id" name="order_id">
                    <div class="form-group">
                        <label>Dispatch Status</label>
                        <select class="form-control" name="dispatch_status" id="modal_dispatch_status">
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group" id="expected_date_group" style="display: none;">
                        <label>Expected Dispatch Date <span style="color:red;">*</span></label>
                        <input type="date" class="form-control" name="expected_dispatch_date" id="modal_expected_dispatch_date">
                        <span style="color:red;" id="error-expected_dispatch_date"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDispatchStatus">Update</button>
            </div>
        </div>
    </div>
</div>
{{-- ── Sample Status Garden Selection Modal ── --}}
<div class="modal fade" id="sampleGardenModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Garden for Sample Purchase</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="sample_order_id">
                <div class="form-group">
                    <label>Select Garden</label>
                    <select class="form-control" id="sample_garden_select">
                        <option value="">Select a garden</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="goToSamplePurchase">Go to Sample Purchase</button>
            </div>
        </div>
    </div>
</div>
{{-- ── Invoice Status Company Selection Modal ── --}}
<div class="modal fade" id="invoiceCompanyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Company for Invoice Creation</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="invoice_order_id">
                <input type="hidden" id="invoice_buyer_id">
                <div class="form-group">
                    <label>Select Company</label>
                    <select class="form-control" id="invoice_company_select">
                        <option value="">Select a company</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="invoice_company_confirm">Create Invoice</button>
            </div>
        </div>
    </div>
</div>
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
    /* ── Detect actual browser reload (F5 / Ctrl+R / reload button) ── */
    function isPageReload() {
        try {
            const navEntries = performance.getEntriesByType('navigation');
            if (navEntries.length > 0) {
                return navEntries[0].type === 'reload';
            }
            // fallback for old browsers
            if (performance.navigation) {
                return performance.navigation.type === 1;
            }
        } catch (e) {}
        return false;
    }
    function fetchData(url) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET', url,
                data: { user_id: USER_ID, company_id: COMPANY_ID, token: API_TOKEN },
                success: r => { resolve(r); },
                error: xhr => { handleAjaxError(xhr); reject(xhr); }
            });
        });
    }
    /* ── Init Select2 helper ── */
    function initSelect2(selector, placeholder, multiple = false) {
        $(selector).select2({
            placeholder,
            allowClear: true,
            width: '100%',
            ...(multiple ? {} : {}),
            templateResult: function (option) {
                if (!option.id) return option.text;
                const selected = $(selector).val();
                if (Array.isArray(selected) && selected.includes(option.id)) return null;
                if (selected == option.id) return null;
                return option.text;
            }
        });
    }
    /* ── Reset all filters ── */
    function resetFilters() {
        // Reset text/number/date inputs
        $('#filter_final_amount_from, #filter_final_amount_to, #filter_credit_days_from, #filter_credit_days_to, #filter_date_from, #filter_date_to, #filter_expected_dispatch_date_from, #filter_expected_dispatch_date_to').val('');

        // Reset Select2 dropdowns
        $('#filter_company').val(null).trigger('change');
        $('#filter_invoice_status').val(null).trigger('change');
        $('#filter_sample_status').val(null).trigger('change');
        $('#filter_buyer').val(null).trigger('change');
        $('#filter_reference').val(null).trigger('change');
        $('#filter_contact_person').val(null).trigger('change');
        $('#filter_garden').val(null).trigger('change');
        $('#filter_transport').val(null).trigger('change');
        $('#filter_grade').val(null).trigger('change');
        $('#filter_dispatch_status').val(null).trigger('change');
    }

    /* ── Save current filters to sessionStorage ── */
    function saveFilters() {
        const filterData = {
            filter_company: $('#filter_company').val(),
            filter_invoice_status: $('#filter_invoice_status').val(),
            filter_sample_status: $('#filter_sample_status').val(),
            filter_buyer: $('#filter_buyer').val(),
            filter_reference: $('#filter_reference').val(),
            filter_contact_person: $('#filter_contact_person').val(),
            filter_garden: $('#filter_garden').val(),
            filter_transport: $('#filter_transport').val(),
            filter_grade: $('#filter_grade').val(),
            filter_dispatch_status: $('#filter_dispatch_status').val(),
            filter_date_from: $('#filter_date_from').val(),
            filter_date_to: $('#filter_date_to').val(),
            filter_expected_dispatch_date_from: $('#filter_expected_dispatch_date_from').val(),
            filter_expected_dispatch_date_to: $('#filter_expected_dispatch_date_to').val(),
            filter_credit_days_from: $('#filter_credit_days_from').val(),
            filter_credit_days_to: $('#filter_credit_days_to').val(),
            filter_final_amount_from: $('#filter_final_amount_from').val(),
            filter_final_amount_to: $('#filter_final_amount_to').val()
        };
        sessionStorage.setItem('orderFilterData', JSON.stringify(filterData));
    }

    /* ── Load saved filters from sessionStorage ── */
    function loadFilters() {
        return new Promise(resolve => {
            var fd = JSON.parse(sessionStorage.getItem('orderFilterData'));
            if (fd) {
                $.each(fd, function (k, v) { if (v != ' ' && v != null && v != '') $('#' + k).val(v); });
                $('#filter_company,#filter_transport,#filter_buyer,#filter_reference,#filter_contact_person,#filter_garden,#filter_grade,#filter_invoice_status,#filter_sample_status,#filter_dispatch_status').trigger('change');
                loaddata();
                sessionStorage.removeItem('orderFilterData');
            } else {
                resetFilters();
                loaddata();
            }
            resolve();
        });
    }
    /* ── Initialize all filters ── */
    async function initialize() {
        loadershow(); // ⭐ single show — poore page load (filters + table) ke liye
        try {
            const [companyRes, transportRes, buyerRes, referenceRes, gardenRes, gradeRes] = await Promise.all([
                fetchData("{{ route('companymaster.index') }}"),
                fetchData("{{ route('transport.index') }}"),
                fetchData("{{ route('buyer.index') }}"),
                fetchData("{{ route('reference.index') }}"),
                fetchData("{{ route('garden.index') }}"),
                fetchData("{{ route('grade.index') }}")
            ]);

            if (companyRes.status == 200 && companyRes.data.length) {
                companyRes.data.forEach(v => $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`));
            }
            initSelect2('#filter_company', 'Select Company');
            initSelect2('#filter_invoice_status', 'Select Invoice Status');
            initSelect2('#filter_sample_status', 'Select Sample Status');
            initSelect2('#filter_dispatch_status', 'Select Status');

            if (transportRes.status == 200 && transportRes.data.length) {
                transportRes.data.forEach(v => $('#filter_transport').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initSelect2('#filter_transport', 'Select Transport', true);

            if (buyerRes.status == 200 && buyerRes.data.length) {
                buyerRes.data.forEach(v => $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initSelect2('#filter_buyer', 'Select Buyer');

            if (referenceRes.status == 200 && referenceRes.data.length) {
                referenceRes.data.forEach(v => $('#filter_reference').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initSelect2('#filter_reference', 'Select Reference');

            // Populate contact person filter from company data
            if (companyRes.status == 200 && companyRes.data.length) {
                const contactPersons = {};
                companyRes.data.forEach(v => {
                    if (v.contact_person_name && v.contact_person_name.trim() !== '') {
                        if (!contactPersons[v.contact_person_name]) {
                            contactPersons[v.contact_person_name] = [];
                        }
                        contactPersons[v.contact_person_name].push(v.id);
                    }
                });

                Object.keys(contactPersons).forEach(name => {
                    const companyIds = contactPersons[name].join(',');
                    $('#filter_contact_person').append(`<option value="${companyIds}">${name}</option>`);
                });
            }
            initSelect2('#filter_contact_person', 'Select Contact Person');

            if (gardenRes.status == 200 && gardenRes.data.length) {
                gardenRes.data.forEach(v => $('#filter_garden').append(`<option value="${v.id}">${v.garden_name}</option>`));
            }
            initSelect2('#filter_garden', 'Select Garden', true);

            if (gradeRes.status == 200 && gradeRes.data.length) {
                gradeRes.data.forEach(v => $('#filter_grade').append(`<option value="${v.id}">${v.grade}</option>`));
            }
            initSelect2('#filter_grade', 'Select Grade', true);

            // Add event listeners to save filters when changed
            // $('.filter').on('change', function() {
            //     saveFilters();
            // });
            // $('#filter_date_from, #filter_date_to, #filter_expected_dispatch_date_from, #filter_expected_dispatch_date_to, #filter_credit_days_from, #filter_credit_days_to, #filter_final_amount_from, #filter_final_amount_to').on('change', function() {
            //     saveFilters();
            // });

            // ⭐ loaderhide() yahan se hata diya — table data load hone tak loader chalu rahega
            if (isPageReload()) {
                sessionStorage.removeItem('orderFilterData');
                resetFilters();
                loaddata();
            } else {
                // normal navigation (e.g. redirect back from Edit page) — filters restore karo
                await loadFilters();
            }
        } catch (e) {
            console.error(e);
            Toast.fire({ icon: 'error', title: 'An error occurred while initializing' });
            loaderhide(); // ⭐ sirf error case mein hide, warna table load complete hone tak wait
        }
    }
    initialize();
    let table = '';
    function loaddata() {
        table = $('#data').DataTable({
            language: { lengthMenu: '_MENU_ &nbsp;Entries per page' },
            pageLength: 25,
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
                    d.filter_reference  = $('#filter_reference').val();
                    d.filter_contact_person  = $('#filter_contact_person').val();
                    d.filter_dispatch_status    = $('#filter_dispatch_status').val();
                    d.filter_final_amount_from = $('#filter_final_amount_from').val();
                    d.filter_final_amount_to   = $('#filter_final_amount_to').val();
                    d.filter_expected_dispatch_date_from = $('#filter_expected_dispatch_date_from').val();
                    d.filter_expected_dispatch_date_to   = $('#filter_expected_dispatch_date_to').val();
                    d.filter_date_from         = $('#filter_date_from').val();
                    d.filter_date_to           = $('#filter_date_to').val();
                    d.filter_transport         = $('#filter_transport').val();
                    d.filter_buyer             = $('#filter_buyer').val();
                    d.filter_garden            = $('#filter_garden').val();
                    d.filter_grade             = $('#filter_grade').val();
                    d.filter_company           = $('#filter_company').val();
                    d.filter_invoice_status    = $('#filter_invoice_status').val();
                    d.filter_sample_status     = $('#filter_sample_status').val();
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

                    // Update totals
                    if (json.totals) {
                        $('#total_orders').text(json.totals.total_orders || 0);
                        $('#total_bags').text(json.totals.total_bags || 0);
                        $('#total_net_kg').text(json.totals.total_net_kg || 0);
                        $('#total_amount').text(parseFloat(json.totals.total_amount || 0).toFixed(2));
                    }

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
                { data: 'reference_name',     name: 'reference_name',     orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'invoice_nos', name: 'invoice_nos', orderable: true, searchable: true, defaultContent: '-',
                    render: (data) => {
                        if (!data) return '-';
                        let s = data.length > 15 ? data.substring(0,15)+'...' : data;
                        return `<span data-toggle="tooltip" data-original-title="${data}">${s}</span>`;
                    }
                },
                { data: 'grades',         name: 'grades',         orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'invoice_status',
                    name: 'invoice_status',
                    orderable: true,
                    searchable: true,
                    defaultContent: '-',
                    render: (data, type, row) => {
                        if (!data) return '-';
                        let orderId = row.id;
                        if (data != 'Invoices Created') {
                            return `<button class="invoice-status-btn btn btn-sm" style="background-color: #28a745; color: #fff; border: 1px solid #28a745; cursor: pointer;" data-order-id="${orderId}">
                                ${data}
                            </button>`;
                        }
                        return data;
                    }
                },

                {
                    data: 'sample_status',
                    name: 'sample_status',
                    orderable: true,
                    searchable: true,
                    defaultContent: '-',
                    render: (data, type, row) => {
                        if (!data) return '-';
                        let orderId = row.id;
                        if (data === 'Pending' || data === 'Half Sample') {
                            return `<button class="sample-status-btn btn btn-sm" style="background-color: #17a2b8; color: #fff; border: 1px solid #17a2b8; cursor: pointer;" data-order-id="${orderId}">
                                ${data}
                            </button>`;
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'dispatch_status',
                    name: 'dispatch_status',
                    orderable: true,
                    searchable: true,
                    defaultContent: '-',
                    render: (data, type, row) => {
                        if (!data) return '-';
                        let bgColor = data === 'Completed' ? '#28a745' : '#ffc107';
                        let textColor = data === 'Completed' ? '#fff' : '#000';
                        let orderId = row.id;
                        return `<button class="dispatch-status-btn btn btn-sm" style="background-color: ${bgColor}; color: ${textColor}; border: 1px solid ${bgColor}; cursor: pointer;" data-order-id="${orderId}" data-current-status="${data}" data-expected-date="${row.expected_dispatch_date || ''}">
                            ${data} <i class="ri-edit-line" style="font-size: 12px; margin-left: 4px;"></i>
                        </button>`;
                    }
                },
                { data: 'expected_dispatch_date',  name: 'expected_dispatch_date',  orderable: true,  searchable: true,  defaultContent: '-' },
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
                                    View
                                </button></span>`;
                        @endif
                        @if (session('user_permissions.teamodule.teadashboard.edit') == '1')
                        if(row.brokerbill_no == null){
                            let editUrl = `{{ route('admin.orderupdateform', '__id__') }}`.replace('__id__', data);
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Order">
                                <a href="${editUrl}" onclick="saveFilters();">
                                    <button class="btn btn-success btn-rounded btn-sm my-0">Edit</button>
                                </a></span>`;
                        }
                        @endif
                        @if (session('user_permissions.teamodule.teadashboard.delete') == '1')
                        if(row.brokerbill_no == null){
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Order">
                                <button type="button" data-id="${data}" class="del-btn btn btn-danger btn-rounded btn-sm my-0">
                                    Delete
                                </button></span>`;
                        }
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
        params.filter_sample_status    = $('#filter_sample_status').val();
        params.filter_grade             = $('#filter_grade').val();
        params.filter_reference         = $('#filter_reference').val();
        params.filter_contact_person   = $('#filter_contact_person').val();
        params.filter_credit_days_from  = $('#filter_credit_days_from').val();
        params.filter_credit_days_to    = $('#filter_credit_days_to').val();
        params.filter_final_amount_from = $('#filter_final_amount_from').val();
        params.filter_final_amount_to   = $('#filter_final_amount_to').val();
        params.filter_date_from         = $('#filter_date_from').val();
        params.filter_date_to           = $('#filter_date_to').val();
        params.type                     = type;   // <-- key addition
        let url = "{{ route('order.orderreport') }}" + '?' + $.param(params);
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('order.orderreport') }}",
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
    /* ── Dispatch Status Modal ── */
    $(document).on('click', '.dispatch-status-btn', function () {
        saveFilters();
        const orderId = $(this).data('order-id');
        const currentStatus = $(this).data('current-status');
        const expectedDate = $(this).data('expected-date');
        $('#dispatch_order_id').val(orderId);
        $('#modal_dispatch_status').val(currentStatus);
        $('#modal_expected_dispatch_date').val(expectedDate);
        if (currentStatus === 'Completed') {
            $('#expected_date_group').show();
        } else {
            $('#expected_date_group').hide();
        }
        $('#error-expected_dispatch_date').text('');
        $('#dispatchStatusModal').modal('show');
    });

    /* ── Sample Status Garden Selection Modal ── */

    function fetchInvoicesForOrder(orderId, gardenId, callback) {
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('order.getInvoices') }}",
            data: {
                order_id: orderId,
                garden_id: gardenId,
                company_id: COMPANY_ID,
                user_id: USER_ID,
                token: API_TOKEN
            },
            success: function(response) {
                loaderhide();
                if (response.status === 200 && response.data && response.data.length > 0) {
                    // Filter invoices that belong to this order (you may need to adjust this logic based on your data structure)
                    // For now, return all invoices for the garden
                    const invoiceNos = response.data.map(item => item.invoice_no);
                    callback(invoiceNos);
                } else {
                    callback([]);
                }
            },
            error: function(xhr) {
                loaderhide();
                console.log(xhr.responseText);
                callback([]);
            }
        });
    }

    $(document).on('click', '.sample-status-btn', function () {
        const orderId = $(this).data('order-id');
        $('#sample_order_id').val(orderId);
        $('#sample_garden_select').empty().append('<option value="">Select a garden</option>');
        saveFilters();
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('order.getGardensByOrder') }}",
            data: {
                order_id: orderId,
                user_id: USER_ID,
                company_id: COMPANY_ID,
                token: API_TOKEN
            },
            success: function (response) {
                loaderhide();
                // Handle different response structures
                let gardensData = response.data;
                // If data is not in response.data, check if it's under the message key
                if (!gardensData && response['Gardens retrieved successfully']) {
                    gardensData = response['Gardens retrieved successfully'];
                }
                if (response.status === 200 && gardensData && gardensData.length > 0) {
                    // If only one garden, redirect directly without showing modal
                    if (gardensData.length === 1) {
                        const garden = gardensData[0];
                        // Store in sessionStorage to avoid URL parameters
                        sessionStorage.setItem('sample_purchase_order_id', orderId);
                        sessionStorage.setItem('sample_purchase_garden_id', garden.garden_id);
                        // Also fetch invoice numbers for this order and garden
                        fetchInvoicesForOrder(orderId, garden.garden_id, function(invoiceNos) {
                            sessionStorage.setItem('sample_purchase_invoice_nos', JSON.stringify(invoiceNos));
                            window.location.href = "{{ route('admin.brokerpurchaseform') }}";
                        });
                    } else {
                        // Multiple gardens, show modal for selection
                        gardensData.forEach(garden => {
                            $('#sample_garden_select').append(`<option value="${garden.garden_id}">${garden.garden_name}</option>`);
                        });
                        $('#sampleGardenModal').modal('show');
                    }
                } else {
                    Toast.fire({ icon: 'info', title: 'No gardens found for this order' });
                }
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    });
    $('#goToSamplePurchase').on('click', function () {
        const orderId = $('#sample_order_id').val();
        const gardenId = $('#sample_garden_select').val();
        if (!gardenId) {
            Toast.fire({ icon: 'error', title: 'Please select a garden' });
            return;
        }
        sessionStorage.setItem('sample_purchase_order_id', orderId);
        sessionStorage.setItem('sample_purchase_garden_id', gardenId);
        // Also fetch invoice numbers for this order and garden
        fetchInvoicesForOrder(orderId, gardenId, function(invoiceNos) {
            sessionStorage.setItem('sample_purchase_invoice_nos', JSON.stringify(invoiceNos));
            window.location.href = "{{ route('admin.brokerpurchaseform') }}";
        });
    });
    // Invoice status button click handler
    $(document).on('click', '.invoice-status-btn', function () {
        const orderId = $(this).data('order-id');
        saveFilters();
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('order.getOrderDetailsForInvoice') }}",
            data: {
                order_id: orderId,
                user_id: USER_ID,
                company_id: COMPANY_ID,
                token: API_TOKEN
            },
            success: function (response) {
                loaderhide();
                // Handle different response structures
                let data = response.data;
                if (!data && response['Order details retrieved successfully']) {
                    data = response['Order details retrieved successfully'];
                } else if (!data && response['Multiple companies found']) {
                    data = response['Multiple companies found'];
                }
                if (response.status === 200 && data) {
                    if (data.has_multiple_companies) {
                        // Show company selection modal
                        $('#invoice_company_select').empty().append('<option value="">Select a company</option>');
                        data.companies.forEach(company => {
                            $('#invoice_company_select').append(`<option value="${company.company_id}">${company.company_name}</option>`);
                        });
                        $('#invoice_order_id').val(orderId);
                        $('#invoice_buyer_id').val(data.buyer_id);
                        $('#invoiceCompanyModal').modal('show');
                    } else {
                        // Single company - call API directly
                        callLotNoCreateInvoice(data.company_id, data.buyer_id, data.garden_ids, data.invoice_nos, data.order_detail_ids);
                    }
                } else {
                    Toast.fire({ icon: 'error', title: response.message || 'Error fetching order details' });
                }
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    });

    // Function to call lot_no_createInvoice API
    function callLotNoCreateInvoice(companyId, buyerId, gardenIds, invoiceNos, orderDetailIds) {
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('brokerpurchase.lot_no_createInvoice') }}",
            data: {
                company_ids: companyId,
                buyer_parties: buyerId,
                garden_ids: gardenIds,
                invoice_no: invoiceNos,
                order_detail_ids: orderDetailIds,
                user_id: USER_ID,
                company_id: COMPANY_ID,
                token: API_TOKEN,
                type:'order'
            },
            success: function (response) {
                    if (response.status === 200) {
                        $.post("{{ route('admin.lot_no_storeInvoiceSession') }}", {
                            _token: "{{ csrf_token() }}", data: response.data
                        }, function () { window.location.href = "{{ route('admin.addinvoice') }}"; });
                    } else {
                        loaderhide();
                        Toast.fire({ icon: 'error', title: response.message || 'Something went wrong' });
                    }
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    }
    // Company selection confirm button
    $('#invoice_company_confirm').on('click', function () {
        const orderId = $('#invoice_order_id').val();
        const buyerId = $('#invoice_buyer_id').val();
        const companyId = $('#invoice_company_select').val();
        if (!companyId) {
            Toast.fire({ icon: 'error', title: 'Please select a company' });
            return;
        }
        // Fetch order details for selected company
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('order.getOrderDetailsForInvoice') }}",
            data: {
                order_id: orderId,
                filter_company_id: companyId,
                user_id: USER_ID,
                company_id: COMPANY_ID,
                token: API_TOKEN
            },
            success: function (response) {
                loaderhide();
                // Handle different response structures
                let data = response.data;
                if (!data && response['Order details retrieved successfully']) {
                    data = response['Order details retrieved successfully'];
                }

                if (response.status === 200 && data) {
                    callLotNoCreateInvoice(data.company_id, buyerId, data.garden_ids, data.invoice_nos, data.order_detail_ids);
                } else {
                    Toast.fire({ icon: 'error', title: response.message || 'Error fetching order details' });
                }
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
        $('#invoiceCompanyModal').modal('hide');
    });
    $('#modal_dispatch_status').on('change', function () {
        if ($(this).val() === 'Completed') {
            $('#expected_date_group').show();
        } else {
            $('#expected_date_group').hide();
            $('#modal_expected_dispatch_date').val('');
        }
    });
    $('#saveDispatchStatus').on('click', function () {
        const orderId = $('#dispatch_order_id').val();
        const dispatchStatus = $('#modal_dispatch_status').val();
        const expectedDispatchDate = $('#modal_expected_dispatch_date').val();
        if (dispatchStatus === 'Completed' && !expectedDispatchDate) {
            $('#error-expected_dispatch_date').text('Expected dispatch date is required');
            return;
        }
        $('#error-expected_dispatch_date').text('');
        loadershow();
        $.ajax({
            type: 'POST',
            url: "{{ route('order.updateDispatchStatus') }}",
            data: {
                _token: $('input[name="_token"]').val(),
                token: API_TOKEN,
                user_id: USER_ID,
                company_id: COMPANY_ID,
                order_id: orderId,
                dispatch_status: dispatchStatus,
                expected_dispatch_date: expectedDispatchDate
            },
            success: function (r) {
                loaderhide();
                if (r.status == 200) {
                    Toast.fire({ icon: 'success', title: r.message });
                    $('#dispatchStatusModal').modal('hide');
                    table.draw();
                } else {
                    Toast.fire({ icon: 'error', title: r.message });
                }
            },
            error: function (xhr) {
                loaderhide();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.expected_dispatch_date) {
                        $('#error-expected_dispatch_date').text(errors.expected_dispatch_date[0]);
                    }
                } else {
                    handleAjaxError(xhr);
                }
            }
        });
    });
    /* ── Apply / Clear filters ── */
    $('.applyfilters').on('click', function () {
        saveFilters();
        loadershow();
        table.draw();
    });
    $('.removefilters').on('click', function () {
        resetFilters();
        loadershow();
        table.draw();
    });
});
</script>
@endpush