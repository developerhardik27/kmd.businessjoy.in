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
    .ui-widget-header { background: #1518b117 !important; border: 1px solid #e1ded9 !important; }
    .btn-info { background-color: #253566 !important; border-color: #253566 !important; color: white; }
    .btn-info:hover { background-color: #39519b !important; color: rgb(255,255,255); }
    .btn-success { background-color: #67d5a5d9 !important; border-color: var(--iq-success) !important; color: black !important; }
    .btn-success:hover { background-color: #16d07ffa !important; border-color: var(--iq-success) !important; color: rgb(250,250,250) !important; }

    /* ───────── FILTER BAR ───────── */
    .filter-bar {
        background: #fff; border: 1px solid #dde3ef;
        border-radius: 10px; padding: 14px 18px; margin-bottom: 14px;
    }
    .filter-bar-header {
        display: flex; align-items: center; gap: 7px;
        font-size: 11px; font-weight: 600; color: #6b7280;
        text-transform: uppercase; letter-spacing: .05em;
        margin-bottom: 12px; padding-bottom: 10px;
        border-bottom: 1px solid #eaecf2;
    }
    .filter-bar-inner { display: flex; align-items: flex-end; gap: 10px; }

    /* ───────── SCROLL AREA ───────── */
    .filter-scroll-area {
        display: flex; align-items: flex-start; gap: 0;
        overflow-x: auto; flex: 1; padding-bottom: 4px;
        scrollbar-width: thin; scrollbar-color: #d1d5db transparent;
    }
    .filter-scroll-area::-webkit-scrollbar { height: 4px; }
    .filter-scroll-area::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

    /* ───────── FILTER FIELD ───────── */
    .filter-field {
        flex: 0 0 160px; min-width: 160px;
        padding: 10px 12px; box-sizing: border-box;
        border-right: 1px solid #eaecf2; border-radius: 0;
    }
    .filter-field:last-child { border-right: none; }
    .filter-field:first-child { border-radius: 8px 0 0 8px; }
    .filter-field:last-child  { border-radius: 0 8px 8px 0; }

    /* Alternating: odd = white, even = grey */
    .filter-scroll-area .filter-field:nth-child(odd)  { background-color: #ffffff; }
    .filter-scroll-area .filter-field:nth-child(even) { background-color: rgba(0,0,0,.04); }

    /* Range fields wider */
    .filter-field.range-field { flex: 0 0 260px; min-width: 260px; }

    /* Labels */
    .filter-field label {
        display: block; font-size: 10.5px; font-weight: 600;
        color: #6b7280; text-transform: uppercase;
        letter-spacing: .05em; margin-bottom: 6px; white-space: nowrap;
    }

    /* Inputs */
    .filter-field .form-control {
        height: 34px; font-size: 13px; border-radius: 6px;
        border: 1px solid #d1d5db; width: 100%;
        box-sizing: border-box; background: transparent !important;
    }
    .filter-field .form-control:focus {
        border-color: #3b5bdb;
        box-shadow: 0 0 0 2px rgba(59,91,219,.1); outline: none;
    }

    /* Select2 */
    .filter-field .select2-container { width: 100% !important; }
    .filter-field .select2-container--default .select2-selection--single {
        height: 34px !important; border-radius: 6px !important;
        border: 1px solid #d1d5db !important; background: transparent !important;
        display: flex !important; align-items: center;
    }
    .filter-field .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important; font-size: 13px !important; padding-left: 10px !important;
    }
    .filter-field .select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px !important; }
    .filter-field .select2-container--default .select2-selection--multiple {
        min-height: 34px !important; height: auto !important;
        border-radius: 6px !important; border: 1px solid #d1d5db !important;
        background: transparent !important; padding: 2px 4px !important;
    }
    .filter-field .select2-container--default.select2-container--focus .select2-selection--single,
    .filter-field .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b5bdb !important; box-shadow: 0 0 0 2px rgba(59,91,219,.1) !important;
    }
    .filter-field .select2-selection__choice {
        background: #3b5bdb !important; border: none !important;
        color: #fff !important; border-radius: 5px !important;
        font-size: 12px !important; padding: 2px 8px !important; margin: 2px !important;
    }
    .filter-field .select2-selection__choice__remove { color: #fff !important; margin-right: 4px !important; }

    /* ───────── RANGE GROUP ───────── */
    .range-group { display: flex; gap: 6px; align-items: center; }
    .range-item { flex: 1; min-width: 0; }
    .range-item input.form-control {
        width: 100% !important; height: 34px; font-size: 12px;
        padding: 4px 6px; box-sizing: border-box; min-width: 0; background: transparent !important;
    }
    .range-sep { font-size: 12px; color: #9ca3af; flex-shrink: 0; }

    /* ───────── ACTION BUTTONS ───────── */
    .filter-actions-fixed {
        flex: 0 0 auto; display: flex; flex-direction: column; gap: 5px;
        padding-left: 12px; border-left: 1px solid #eaecf2;
    }
    .filter-actions-fixed .btn {
        height: 34px; font-size: 12.5px; font-weight: 600;
        border-radius: 7px; display: inline-flex;
        align-items: center; justify-content: center;
        gap: 5px; white-space: nowrap; width: 90px; margin: 0;
    }

    /* ───────── ACTION BAR ───────── */
    .action-bar {
        display: flex; align-items: center;
        gap: 8px; flex-wrap: wrap; margin-bottom: 14px;
    }
    .action-bar .btn {
        height: 32px; font-size: 12.5px; font-weight: 600;
        border-radius: 7px; display: inline-flex;
        align-items: center; gap: 5px; white-space: nowrap;
    }

    /* ───────── MOBILE ───────── */
    @media (max-width: 576px) {
        .filter-bar-inner { flex-direction: column; align-items: stretch; }
        .filter-scroll-area { flex-direction: column; overflow-x: visible; gap: 6px; }
        .filter-field{
            flex: 1 1 100%; min-width: 100%;
            border-right: none; border-radius: 8px !important;
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
        .filter-actions-fixed {
            flex-direction: row; border-left: none;
            border-top: 1px solid #eaecf2; padding-left: 0; padding-top: 10px;
        }
        .filter-actions-fixed .btn { flex: 1; width: auto; }
        .action-bar { flex-direction: column; align-items: stretch; }
        .action-bar .btn { justify-content: center; }
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
                <select name="filter_company" class="filter form-control select2" id="filter_company" multiple></select>
            </div>

            <div class="filter-field">
                <label>Buyer</label>
                <select name="filter_buyer" class="filter form-control select2" id="filter_buyer" multiple></select>
            </div>

            <div class="filter-field">
                <label>Garden</label>
                <select name="filter_garden" class="filter form-control select2" id="filter_garden" multiple></select>
            </div>

            <div class="filter-field">
                <label>Grade</label>
                <select name="filter_grade" class="filter form-control select2" id="filter_grade" multiple></select>
            </div>

            <div class="filter-field range-field">
                <label>Net Kg From <span class="m-4"></span> Net Kg To</label>

                <div class="range-group">
                    <div class="range-item">
                        <input type="number" class="form-control filter" name="filter_net_kg_from" id="filter_net_kg_from" placeholder="From">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="number" class="form-control filter" name="filter_net_kg_to" id="filter_net_kg_to" placeholder="To">
                    </div>
                </div>
            </div>

            <div class="filter-field range-field">
                <label>Bags From <span class="m-4"></span> Bags To</label>

                <div class="range-group">
                    <div class="range-item">
                        <input type="number" class="form-control filter" name="filter_bags_from" id="filter_bags_from" placeholder="From">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="number" class="form-control filter" name="filter_bags_to" id="filter_bags_to" placeholder="To">
                    </div>
                </div>
            </div>

            <div class="filter-field range-field">
                <label>Created Date From <span class="m-1"></span> Created Date To</label>

                <div class="range-group">
                    <div class="range-item">
                        <input type="date" class="form-control filter" id="filter_from_date">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="date" class="form-control filter" id="filter_to_date">
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
    @if (session('user_permissions.invoicemodule.invoice.add') == 1)
        <button id="createInvoiceBtn" class="btn btn-info d-none"
            data-toggle="tooltip" data-placement="bottom" data-original-title="Create Invoice">
            <i class="ri-bill-line"></i> Create Invoice
        </button>
    @endif
</div>
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
            <th>Company Name</th>
            <th>Buyer</th>
            <th>Transport</th>
            <th>Garden</th>
            <th>Invoice No</th>
            <th>Grade</th>
            <th>Bags</th>
            <th>Net kg</th>
            <th>Sample Purchase Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="tabledata"></tbody>
</table>
<table id="totals_table" class="table table-bordered table-striped w-100 mt-3">
    <thead>
        <tr>
            <th>Total Sample</th>
            <th>Total Bags</th>
            <th>Total Net Kg</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td id="total_samples">-</td>
            <td id="total_bags">-</td>
            <td id="total_net_kg">-</td>
            <td id="total_amount">-</td>
        </tr>
    </tbody>
</table>

@endsection

@push('ajax')
<script>
$('document').ready(function () {

    const API_TOKEN  = "{{ session()->get('api_token') }}";
    const COMPANY_ID = "{{ session()->get('company_id') }}";
    const USER_ID    = "{{ session()->get('user_id') }}";

    var global_response = '';

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

    /* ── Shared fetch helper ── */
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

    /* ── Init Select2 for multiple selects ── */
    function initMultiSelect2(selector, placeholder) {
        $(selector).select2({
            placeholder, allowClear: true, width: '100%',
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
        // Reset number/date inputs
        $('#filter_net_kg_from, #filter_net_kg_to').val('');
        $('#filter_bags_from, #filter_bags_to').val('');
        $('#filter_from_date, #filter_to_date').val('');

        // Reset Select2 dropdowns
        $('#filter_company').val(null).trigger('change');
        $('#filter_buyer').val(null).trigger('change');
        $('#filter_garden').val(null).trigger('change');
        $('#filter_grade').val(null).trigger('change');
    }

    /* ── Save current filters to sessionStorage ── */
    function saveFilters() {
        const filterData = {
            filter_company: $('#filter_company').val(),
            filter_buyer: $('#filter_buyer').val(),
            filter_garden: $('#filter_garden').val(),
            filter_grade: $('#filter_grade').val(),
            filter_net_kg_from: $('#filter_net_kg_from').val(),
            filter_net_kg_to: $('#filter_net_kg_to').val(),
            filter_bags_from: $('#filter_bags_from').val(),
            filter_bags_to: $('#filter_bags_to').val(),
            filter_from_date: $('#filter_from_date').val(),
            filter_to_date: $('#filter_to_date').val()
        };
        sessionStorage.setItem('brokerPurchaseFilterData', JSON.stringify(filterData));
    }

    /* ── Load saved session filters ── */
    function loadFilters() {
        return new Promise(resolve => {
            var fd = JSON.parse(sessionStorage.getItem('brokerPurchaseFilterData'));
            if (fd) {
                $.each(fd, function (k, v) { if (v != ' ' && v != null && v != '') $('#' + k).val(v); });
                $('#filter_company, #filter_buyer, #filter_garden, #filter_grade').trigger('change');
                loaddata();
                sessionStorage.removeItem('brokerPurchaseFilterData');
            } else {
                resetFilters();
                loaddata();
            }
            resolve();
        });
    }

    /* ── Initialize all filters ── */
    async function initialize() {
        loadershow();
        try {
            const [companyRes, buyerRes, gardenRes, gradeRes] = await Promise.all([
                fetchData("{{ route('companymaster.index') }}"),
                fetchData("{{ route('buyer.index') }}"),
                fetchData("{{ route('garden.index') }}"),
                fetchData("{{ route('grade.index') }}")
            ]);

            // Company
            if (companyRes.status == 200 && companyRes.data.length) {
                companyRes.data.forEach(v => $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`));
            }
            initMultiSelect2('#filter_company', 'Select Company');

            // Buyer
            if (buyerRes.status == 200 && buyerRes.data.length) {
                buyerRes.data.forEach(v => $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initMultiSelect2('#filter_buyer', 'Select Buyer');

            // Garden
            if (gardenRes.status == 200 && gardenRes.data.length) {
                gardenRes.data.forEach(v => $('#filter_garden').append(`<option value="${v.id}">${v.garden_name}</option>`));
            }
            initMultiSelect2('#filter_garden', 'Select Garden');

            // Grade
            if (gradeRes.status == 200 && gradeRes.data.length) {
                gradeRes.data.forEach(v => $('#filter_grade').append(`<option value="${v.id}">${v.grade}</option>`));
            }
            initMultiSelect2('#filter_grade', 'Select Grade');
            if (isPageReload()) {
                sessionStorage.removeItem('brokerPurchaseFilterData');
                resetFilters();
                loaddata();
            } else {
                // normal navigation (e.g. redirect back from Edit page) — filters restore karo
                await loadFilters();
            }

        } catch (e) {
            loaderhide();
            console.error(e);
            Toast.fire({ icon: 'error', title: 'An error occurred while initializing' });
            loaderhide();
        }
    }

    initialize();

    /* ── Checkbox logic ── */
   $(document).on('change', '.purchase-checkbox', function () {
    let companyId  = $(this).data('company-id');
    let buyerParty = $(this).data('buyer-party');
     let orderDetailIds = $(this).data('order_detail_ids');
    let isChecked  = $(this).is(':checked');

    if (isChecked) {
        // Uncheck different buyer parties in same company
        $('.purchase-checkbox').each(function () {
            if ($(this).data('company-id') == companyId && $(this).data('buyer-party') != buyerParty) {
                $(this).prop('checked', false);
            }
        });
        // Auto-check all same company + same buyer party
        $('.purchase-checkbox').each(function () {
            if ($(this).data('company-id') == companyId && $(this).data('buyer-party') == buyerParty) {
                $(this).prop('checked', true);
            }
        });
    }
    // ✅ No else — uncheck only fires naturally for that one checkbox

    toggleCreateInvoiceBtn();
});
    function toggleCreateInvoiceBtn() {
    let checkedBoxes = $('.purchase-checkbox:checked');
    if (checkedBoxes.length === 0) {
        $('#createInvoiceBtn').addClass('d-none').removeAttr('data-company-id data-buyer-party data-sample-ids data-order-detail-ids');
        return;
    }

    let companyIds = [], buyerParties = [], sampleIds = [], orderDetailIds = [];

    checkedBoxes.each(function () {
        companyIds.push($(this).data('company-id'));
        buyerParties.push($(this).data('buyer-party'));
        sampleIds.push($(this).data('sample-id'));          // ✅ no Set — keep all
        orderDetailIds.push($(this).data('order_detail_ids')); // ✅ no Set — keep all
    });

    $('#createInvoiceBtn')
        .removeClass('d-none')
        .attr('data-company-id',       [...new Set(companyIds)].join(','))   // unique only
        .attr('data-buyer-party',      [...new Set(buyerParties)].join(',')) // unique only
        .attr('data-sample-ids',       sampleIds.join(','))        // ✅ all ids
        .attr('data-order-detail-ids', orderDetailIds.join(','));  // ✅ all ids
}

    /* ── Create Invoice ── */
    $('#createInvoiceBtn').on('click', function () {
    loadershow();

    // ✅ Use .attr() not .data() — .data() returns stale cached value
    let companyIds     = ($('#createInvoiceBtn').attr('data-company-id')       || '').toString();
    let buyerParties   = ($('#createInvoiceBtn').attr('data-buyer-party')      || '').toString();
    let sampleIds      = ($('#createInvoiceBtn').attr('data-sample-ids')       || '').toString();
    let orderDetailIds = ($('#createInvoiceBtn').attr('data-order-detail-ids') || '').toString();

    if (!companyIds || !buyerParties) {
        Toast.fire({ icon: 'error', title: 'No company or buyer selected' });
        loaderhide(); return;
    }

    $.ajax({
        url: "{{ route('brokerpurchase.createInvoice') }}", type: 'GET',
        data: { 
            company_ids: companyIds, 
            buyer_parties: buyerParties, 
            sampleIds, 
            orderDetailIds, 
            user_id: USER_ID, 
            company_id: COMPANY_ID, 
            token: API_TOKEN, 
            _token: "{{ csrf_token() }}" 
        },
        success: function (r) {
            if (r.status === 200) {
                $.post("{{ route('admin.storeInvoiceSession') }}", { _token: "{{ csrf_token() }}", data: r.data }, function () {
                    window.location.href = "{{ route('admin.addinvoice') }}";
                });
                $('.purchase-checkbox').prop('checked', false);
                toggleCreateInvoiceBtn();
                loaddata();
            } else {
                loaderhide();
                Toast.fire({ icon: 'error', title: r.message || 'Something went wrong' });
            }
        },
        error: xhr => { loaderhide(); handleAjaxError(xhr); }
    });
});
    /* ── DataTable ── */
    let table = '';

    function loaddata() {
        table = $('#data').DataTable({
            language: { lengthMenu: '_MENU_ &nbsp;Entries per page' },
            pageLength: 25,
            destroy: true, responsive: true, processing: true, serverSide: true,
            ajax: {
                type: 'GET', url: "{{ route('brokerpurchase.index') }}",
                data: function (d) {
                    d.user_id          = USER_ID;
                    d.company_id       = COMPANY_ID;
                    d.token            = API_TOKEN;
                    d.filter_net_kg_from = $('#filter_net_kg_from').val();
                    d.filter_net_kg_to   = $('#filter_net_kg_to').val();
                    d.filter_bags_from   = $('#filter_bags_from').val();
                    d.filter_bags_to     = $('#filter_bags_to').val();
                    d.filter_company     = $('#filter_company').val();
                    d.filter_buyer       = $('#filter_buyer').val();
                    d.filter_garden      = $('#filter_garden').val();
                    d.filter_grade       = $('#filter_grade').val();
                    d.filter_from_date   = $('#filter_from_date').val();
                    d.filter_to_date     = $('#filter_to_date').val();
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
                        $('#total_samples').text(json.totals.total_samples || 0);
                        $('#total_bags').text(json.totals.total_bags || 0);
                        $('#total_net_kg').text(json.totals.total_net_kg || 0);
                        $('#total_amount').text(parseFloat(json.totals.total_amount || 0).toFixed(2));
                    }

                    return json.data;
                },
                complete: () => loaderhide(),
                error: xhr => { global_response = ''; console.log(xhr.responseText); Toast.fire({ icon: 'error', title: 'Error loading data' }); }
            },
            order: [[0, 'desc']],
            columns: [
                {
                    data: 'id', name: 'id', orderable: true, searchable: true, defaultContent: '',
                    render: (data, type, row) => {
                        if (row.invoice_id == null) {
                            return `<input type="checkbox" class="purchase-checkbox" value="${row.company_id}"
                                data-company-id="${row.company_id}" data-buyer-party="${row.buyer_party}" data-sample-id="${row.id}" data-order_detail_ids="${row.order_detail_id}">`;
                        }
                        return `<b>${row.id}</b>`;
                    }
                },
                { data: 'company_name',   name: 'company_name',   orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'buyer_name',     name: 'buyer_name',     orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'transport_name', name: 'transport_name', orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'garden_name',    name: 'garden_name',    orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'invoice_no',     name: 'invoice_no',     orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'grade_name',     name: 'grade_name',     orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'bags',           name: 'bags',           orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'net_kg',         name: 'net_kg',         orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'sample_purchase_date', name: 'sample_purchase_date', orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'id', name: 'id', orderable: false, searchable: false, defaultContent: '-',
                    render: (data, type, row) => {
                        let btns = '';
                        @if (session('user_permissions.teamodule.brokerpurchase.view') == '1')
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                <button type="button" data-view="${data}" data-toggle="modal" data-target="#exampleModalScrollable"
                                    class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                    View</button></span>`;
                        @endif
                        @if (session('user_permissions.teamodule.brokerpurchase.edit') == '1')
                        if(row.brokerbill_no == null){
                            let editUrl = `{{ route('admin.brokerpurchaseupdateform', '__id__') }}`.replace('__id__', data);
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Sample Purchase">
                                <a href="${editUrl}" onclick="saveFilters();"><button class="btn btn-success btn-rounded btn-sm my-0">Edit</button></a></span>`;
                        }
                        @endif
                        @if (session('user_permissions.teamodule.brokerpurchase.delete') == '1')
                        if(row.brokerbill_no == null){
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                <button type="button" data-id="${data}" class="del-btn btn btn-danger btn-rounded btn-sm my-0 mr-2">
                                    Delete</span>`;
                        }
                        @endif
                        @if (session('user_permissions.teamodule.brokerpurchase.view') == '1')
                            let sampleurl = `{{ route('admin.samplepurchase', '__id__') }}`.replace('__id__', data);
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Download Sample Purchase Pdf">
                                <a href="${sampleurl}"><button class="btn btn-success btn-rounded btn-sm my-0"><i class="ri-download-line"></i></button></a></span>`;
                        @endif
                        if (row.invoice_id) {
                            @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                let pdfUrl = "{{ route('invoice.generatepdf', '__invoiceId__') }}".replace('__invoiceId__', row.invoice_id);
                                btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Download Invoice Pdf">
                                    <a href="${pdfUrl}" target="_blank">
                                        <button class="download-btn btn btn-info btn-rounded btn-sm my-0"><i class="ri-download-line"></i></button>
                                    </a></span>`;
                            @endif
                        }
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
                        if (!isNaN(page) && page > 0 && page <= total) { t.page(page - 1).draw('page'); }
                        else { Toast.fire({ icon: 'error', title: `Enter a page between 1 and ${total}` }); }
                    }
                });
            }
        });
    }

    /* ── View Details ── */
    $(document).on('click', '.view-btn', function () {
        $('#details').html('');
        var data = $(this).data('view');
        $.each(global_response.data, function (key, bp) {
            if (bp.id == data) {
                $('#details').append(`
                    <tr><th>Id</th><td>${bp.id||'-'}</td></tr>
                    <tr><th>Company</th><td>${bp.company_name||'-'}</td></tr>
                    <tr><th>Garden Name</th><td>${bp.garden_name||'-'}</td></tr>
                    <tr><th>Invoice No</th><td>${bp.invoice_no||'-'}</td></tr>
                    <tr><th>Grade</th><td>${bp.grade_name||'-'}</td></tr>
                    <tr><th>Bags</th><td>${bp.bags||'-'}</td></tr>
                    <tr><th>Net kg</th><td>${bp.net_kg||'-'}</td></tr>`);
            }
        });
    });
    function exportReport(type) {
        let params = table.ajax.params();
        params.filter_net_kg_from = $('#filter_net_kg_from').val();
        params.filter_net_kg_to   = $('#filter_net_kg_to').val();
        params.filter_bags_from   = $('#filter_bags_from').val();
        params.filter_bags_to     = $('#filter_bags_to').val();
        params.filter_company     = $('#filter_company').val();
        params.filter_buyer       = $('#filter_buyer').val();
        params.filter_garden      = $('#filter_garden').val();
        params.filter_grade       = $('#filter_grade').val();
        params.filter_from_date   = $('#filter_from_date').val();
        params.filter_to_date     = $('#filter_to_date').val();
        params.type               = type;   // <-- key addition

        let url = "{{ route('brokerpurchase.samplereport') }}" + '?' + $.param(params);

        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('brokerpurchase.samplereport') }}",
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
    /* ── Delete ── */
    $(document).on('click', '.del-btn', function () {
        var deleteid = $(this).data('id');
        showConfirmationDialog('Are you sure?', 'to delete this record?', 'Yes, delete', 'No, cancel', 'question',
            () => {
                loadershow();
                $.ajax({
                    type: 'PUT',
                    url: "{{ route('brokerpurchase.delete', '__deleteId__') }}".replace('__deleteId__', deleteid),
                    data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                    success: r => {
                        loaderhide();
                        Toast.fire({ icon: r.status==200?'success':'error', title: r.message });
                        if (r.status == 200) loaddata();
                    },
                    error: xhr => { loaderhide(); handleAjaxError(xhr); }
                });
            }
        );
    });

    /* ── Apply / Clear ── */
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