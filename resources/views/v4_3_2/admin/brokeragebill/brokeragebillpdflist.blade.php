@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Commission Bill
@endsection
@section('table_title')
    Commission Bill
@endsection

@section('style')
<style>
    .ui-widget-header { background: #1518b117 !important; border: 1px solid #e1ded9 !important; }
    .btn-info { background-color: #253566 !important; border-color: #253566 !important; color: white; }
    .btn-info:hover { background-color: #39519b !important; color: rgb(255,255,255); }
    .btn-success { background-color: #67d5a5d9 !important; border-color: var(--iq-success) !important; color: black !important; }
    .btn-success:hover { background-color: #16d07ffa !important; border-color: var(--iq-success) !important; color: rgb(250,250,250) !important; }
    .select2-results__options { max-height: 150px !important; overflow-y: auto !important; }

    th, td { white-space: nowrap; }
    .w-id      { width: 4%  !important; }
    .w-date    { width: 8%  !important; }
    .w-company { width: 30% !important; }
    .w-garden  { width: 30% !important; }
    .w-invoice { width: 10% !important; }
    .w-kg      { width: 8%  !important; }
    .w-broker  { width: 6%  !important; }
    .w-amount  { width: 10% !important; }
    .w-status  { width: 6%  !important; }
    .w-invoice-file { width: 6% !important; }
    .w-action  { width: 6%  !important; }
    .w-payment { width: 6%  !important; }
    table.dataTable thead>tr>th.dt-orderable-asc,
    table.dataTable thead>tr>th.dt-orderable-desc { padding-right: 0px !important; }

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
        flex: 0 0 170px; min-width: 170px;
        padding: 10px 12px; box-sizing: border-box;
        border-right: 1px solid #eaecf2;
    }
    .filter-field:first-child { border-radius: 8px 0 0 8px; }
    .filter-field:last-child  { border-radius: 0 8px 8px 0; border-right: none; }

    /* Range fields wider */
    .filter-field.range-field { flex: 0 0 260px; min-width: 260px; }

    /* Alternating: odd = white, even = grey */
    .filter-scroll-area .filter-field:nth-child(odd)  { background-color: #ffffff; }
    .filter-scroll-area .filter-field:nth-child(even) { background-color: rgba(0,0,0,.04); }

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
        background: #3b5bdb !important; border: none !important; color: #fff !important;
        border-radius: 5px !important; font-size: 12px !important;
        padding: 2px 8px !important; margin: 2px !important;
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
        height: 34px; font-size: 12.5px; font-weight: 600; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        gap: 5px; white-space: nowrap; width: 90px; margin: 0;
    }

    /* ───────── ACTION BAR ───────── */
    .action-bar {
        display: flex; align-items: center;
        gap: 8px; flex-wrap: wrap; margin-bottom: 14px;
    }
    .action-bar .btn {
        height: 32px; font-size: 12.5px; font-weight: 600; border-radius: 7px;
        display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
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
        .filter-field .select2-container { width: 100% !important; }
        .filter-actions-fixed {
            flex-direction: row; border-left: none;
            border-top: 1px solid #eaecf2; padding-left: 0; padding-top: 10px;
        }
        .filter-actions-fixed .btn { flex: 1; width: auto; }
        .action-bar { flex-direction: column; align-items: stretch; }
        .action-bar .btn { justify-content: center; font-size: 12px; }
    }
</style>
@endsection

@section('table-content')

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <div class="filter-bar-header">
        <i class="ri-filter-3-line"></i> Filters
    </div>
    <div class="filter-bar-inner">

        <div class="filter-scroll-area">

            <div class="filter-field">
                <label>Garden</label>
                <select name="filter_garden" class="filter form-control select2" id="filter_garden" multiple></select>
            </div>

            <div class="filter-field">
                <label>Company</label>
                <select name="filter_company" class="filter form-control select2" id="filter_company">
                    <option value=""></option>
                </select>
            </div>

            <div class="filter-field">
                <label>Buyer</label>
                <select name="filter_buyer" class="filter form-control select2" id="filter_buyer">
                    <option value=""></option>
                </select>
            </div>

            <div class="filter-field">
                <label>Payment Status</label>
                <select name="filter_payment_status" class="filter form-control select2" id="filter_payment_status" multiple>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="part_payment">Part Payment</option>
                    <option value="cancel">Cancel</option>
                    <option value="due">Over Due</option>
                </select>
            </div>

            <div class="filter-field range-field">
                <label>Created Date From <span class="m-1"></span> Created Date To</label>

                <div class="range-group">
                    <div class="range-item">
                        <input type="date" class="form-control filter" name="filter_date_from" id="filter_date_from">
                    </div>
                    <span class="range-sep">—</span>
                    <div class="range-item">
                        <input type="date" class="form-control filter" name="filter_date_to" id="filter_date_to">
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
<div class="action-bar d-flex justify-content-between align-items-center w-100 mb-3">
    <div>
    <button class="btn btn-primary" id="pdfBtn">
        <i class="ri-file-chart-line"></i> Generate Report
    </button>
    <button class="btn btn-success" id="excelBtn">
        <i class="ri-file-excel-2-line"></i> Export Excel
    </button>
    </div>
    <button class="btn btn-success generate-invoice"
        data-toggle="tooltip" data-placement="bottom"
        data-original-title="Create New Invoice">
        <i class="ri-add-line"></i> New Invoice
    </button>
</div>

<table id="data" class="table display table-bordered table-striped w-100">
    <thead>
        <tr>
            <th class="w-id">Id</th>
            <th class="w-date">Invoice <br> Date</th>
            <th class="w-company">Company Name</th>
            <th class="w-garden">Garden Name</th>
            <th class="w-garden">Buyer <br>Name</th>
            <th class="w-invoice">Invoice No</th>
            <th class="w-kg">Total <br> Net Kg</th>
            <th class="w-broker">Brokr <br>age (%)</th>
            <th class="w-amount">Total <br> Amount</th>
            <th class="w-status">Status</th>
            <th class="w-invoice-file">Inv</th>
            <th class="w-action">Action</th>
            <th class="w-payment">Payment</th>
        </tr>
    </thead>
    <tbody id="tabledata"></tbody>
</table>

{{-- ── Payment Modal ── --}}
<div class="modal fade" id="paymentmodal" tabindex="-1" role="dialog" aria-labelledby="viewpaymentmodalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewpaymentmodalTitle"><b>Payment</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentform">
                <div class="modal-body">
                    @csrf
                    <div class="payment_details">
                        <input type="hidden" name="user_id"    class="form-control" value="{{ session('user_id') }}"    required />
                        <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}" required />
                        <input type="hidden" name="token"      class="form-control" value="{{ session('api_token') }}"  required />
                        <input type="hidden" name="inv_id" id="inv_id">
                        <label for="transid">Transaction ID</label>
                        <input type="text" name="transid" class="form-control" id="transid" placeholder="Transaction id" />
                        <p class="modal_error-msg mb-1" id="error-transid" style="color:red;"></p>
                        <label for="payment_date">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" id="payment_date" required />
                        <p class="modal_error-msg mb-1" id="error-payment_date" style="color:red;"></p>
                        Total Amount :-&nbsp;<span class="mb-1 text-info" id="info-total_amount">0</span>,
                        &nbsp;Received Amount :-&nbsp;<span class="mb-1 text-info" id="info-total_received_amount">0</span><br>
                        <label for="paidamount">New Amount</label>
                        <input type="number" name="paidamount" class="form-control" id="paidamount" placeholder="New Amount" required />
                        <p class="modal_error-msg mb-1" id="error-paidamount" style="color:red;"></p>
                        Pending Amount :-&nbsp;<span class="mb-1 text-info info-pending_amount">0</span><br>
                        <label for="paid_by">Paid By</label>
                        <input type="text" name="paid_by" class="form-control" id="paid_by" placeholder="Who Paid Amount" />
                        <p class="modal_error-msg mb-1" id="error-paid_by" style="color:red;"></p>
                        <label for="payment_type">How They Paid</label>
                        <select class="form-control" name="payment_type" id="payment_type">
                            <option selected disabled>Select Payment Type</option>
                            <option value="Online Payment">Online Payment</option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                        </select>
                        <p class="modal_error-msg mb-1" id="error-payment_type" style="color:red;"></p>
                    </div>
                    <div class="tds_details">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="tds_applicable" id="tds_applicable">
                            <label class="form-check-label" for="tds_applicable">TDS Applicable</label>
                            <p class="modal_error-msg mb-1" id="error-tds_applicable" style="color:red;"></p>
                        </div>
                        <div class="tds_inputs" style="display:none;">
                            <hr>
                            <label for="tds_amount">TDS Amount</label>
                            <input type="number" name="tds_amount" class="form-control" id="tds_amount" placeholder="TDS Amount" />
                            <p class="modal_error-msg mb-1" id="error-tds_amount" style="color:red;"></p>
                            Pending Amount :-&nbsp;<span class="mb-1 text-info info-pending_amount">0</span><br>
                            <label for="challan_no">Challan No</label>
                            <input type="text" name="challan_no" class="form-control" id="challan_no" placeholder="Challan No" />
                            <p class="modal_error-msg mb-1" id="error-challan_no" style="color:red;"></p>
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option selected disabled>Select Status</option>
                                <option value="Recorded">Recorded</option>
                                <option value="Mapped to Challan">Mapped to Challan</option>
                                <option value="Filed in Return">Filed in Return</option>
                                <option value="Reconciled (matches 26AS)">Reconciled (matches 26AS)</option>
                            </select>
                            <p class="modal_error-msg mb-1" id="error-status" style="color:red;"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset"  class="btn iq-bg-danger">Reset</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="pdfDateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="pdfDateForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Commission Bill PDF</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="invoice_id">Select Invoice</label>
                            <select name="invoice_id" id="invoice_id" class="form-control select2" required>
                                <option value="" disabled selected>Select Invoice</option>
                            </select>
                        </div>
                        {{-- Hidden system fields --}}
                        <input type="hidden" name="token"              value="{{ session('api_token') }}" required />
                        <input type="hidden" name="user_id"            value="{{ session('user_id') }}">
                        <input type="hidden" name="company_id"         value="{{ session('company_id') }}">
                        <input type="hidden" name="garden_id"          id="garden_id"          value="">
                        <input type="hidden" name="company_details_id" id="company_details_id" value="">

                        {{-- Summary banner — shown only for bulk selections --}}
                        <div id="pdfModalSummary" class="alert alert-info py-2 px-3 mb-3" style="font-size:13px; display:none;">
                            <!-- <strong>Selected Invoice IDs:</strong> <span id="summaryInvoiceIds">-</span><br> -->
                            <strong>No. of Invoices:</strong> <span id="summaryCount">-</span>
                        </div>

                        <div class="form-group">
                            <label>Invoice Total</label>
                            <input type="number" name="line_total" id="line_total" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Brokerage (%)</label>
                            <input type="number" name="brokerage" id="brokerage" step="0.01" min="0" max="100"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Brokerage Amount</label>
                            <input type="number" name="brokrageAmount" id="brokrageAmount" step="0.01" min="0"
                                class="form-control" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Generate</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('ajax')
<script>
$('document').ready(function () {

    const API_TOKEN  = "{{ session()->get('api_token') }}";
    const COMPANY_ID = "{{ session()->get('company_id') }}";
    const USER_ID    = "{{ session()->get('user_id') }}";

    let getgardenname = [], getcompanyname = [], getbuyername = [];
    var global_response = '';

    /* ── Shared fetch helper ── */
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

    /* ── Init Select2 helpers ── */
    function initSingleSelect2(selector, placeholder) {
        $(selector).select2({ placeholder, allowClear: true, width: '100%',
            templateResult: function (option) {
                if (!option.id) return option.text;
                const selected = $(selector).val();
                if (Array.isArray(selected) && selected.includes(option.id)) return null;
                if (selected == option.id) return null;
                return option.text;
            }
        });
    }
    function initMultiSelect2(selector, placeholder) {
        $(selector).select2({ placeholder, allowClear: true, width: '100%',
            templateResult: function (option) {
                if (!option.id) return option.text;
                const selected = $(selector).val();
                if (Array.isArray(selected) && selected.includes(option.id)) return null;
                if (selected == option.id) return null;
                return option.text;
            }
        });
    }

    /* ── Payment Status pre-init ── */
    initMultiSelect2('#filter_payment_status', 'Select Payment Status');

    /* ── Load saved filters ── */
    function loadFilters() {
        return new Promise(resolve => {
            var fd = JSON.parse(sessionStorage.getItem('filterData'));
            if (fd) {
                $.each(fd, function (k, v) { if (v != ' ') $('#' + k).val(v); });
                $('#filter_company, #filter_buyer, #filter_garden').trigger('change');
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
            const [gardenRes, companyRes, buyerRes] = await Promise.all([
                fetchData("{{ route('garden.index') }}"),
                fetchData("{{ route('companymaster.index') }}"),
                fetchData("{{ route('buyer.index') }}")
            ]);

            // Garden
            if (gardenRes.status == 200 && gardenRes.data.length) {
                gardenRes.data.forEach(v => {
                    getgardenname.push(v.garden_name);
                    $('#filter_garden').append(`<option value="${v.id}">${v.garden_name} - ${v.company_name}</option>`);
                });
            }
            initMultiSelect2('#filter_garden', 'Select Garden');

            // Company
            if (companyRes.status == 200 && companyRes.data.length) {
                companyRes.data.forEach(v => {
                    getcompanyname.push(v.company_name);
                    $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`);
                });
            }
            initSingleSelect2('#filter_company', 'Select Company');

            // Buyer
            if (buyerRes.status == 200 && buyerRes.data.length) {
                buyerRes.data.forEach(v => {
                    getbuyername.push(v.name);
                    $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`);
                });
            }
            initSingleSelect2('#filter_buyer', 'Select Buyer');

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
            pageLength: 25,
            destroy: true, responsive: true, processing: true, serverSide: true,
            ajax: {
                type: 'GET', url: "{{ route('brokeragebill.brokeragebillpdflist') }}",
                data: function (d) {
                    d.user_id               = USER_ID;
                    d.company_id            = COMPANY_ID;
                    d.token                 = API_TOKEN;
                    d.filter_payment_status = $('#filter_payment_status').val();
                    d.filter_garden         = $('#filter_garden').val();
                    d.filter_company        = $('#filter_company').val();
                    d.filter_buyer          = $('#filter_buyer').val();
                    d.filter_date_from      = $('#filter_date_from').val();
                    d.filter_date_to        = $('#filter_date_to').val();
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
                error: xhr => { global_response = ''; console.log(xhr.responseText); Toast.fire({ icon: 'error', title: 'Error loading data' }); }
            },
            order: [[0, 'desc']],
            columns: [
                { data: 'id',           name: 'id',           orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'invoice_date', name: 'invoice_date', orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'company_name', name: 'company_name', orderable: true, searchable: true, defaultContent: '-',
                    render: d => { if (!d) return '-'; let s = d.length > 50 ? d.substring(0,50)+'...' : d; return `<span data-toggle="tooltip" data-original-title="${d}">${s}</span>`; }
                },
                {
                    data: 'garden_names', name: 'garden_names', orderable: true, searchable: true, defaultContent: '-',
                    render: d => { if (!d) return '-'; let s = d.length > 50 ? d.substring(0,50)+'...' : d; return `<span data-toggle="tooltip" data-original-title="${d}">${s}</span>`; }
                },
                {
                    data: 'buyer_names', name: 'buyer_names', orderable: true, searchable: true, defaultContent: '-',
                    render: d => { if (!d) return '-'; let s = d.length > 50 ? d.substring(0,50)+'...' : d; return `<span data-toggle="tooltip" data-original-title="${d}">${s}</span>`; }
                },
                { data: 'invoice_no',  name: 'invoice_no',  orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'net_kg',      name: 'net_kg',      orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'brokerage',   name: 'brokerage',   orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'grand_total', name: 'grand_total', orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'status', name: 'status', orderable: true, searchable: true, defaultContent: '-',
                    render: function (data, type, row) {
                        let actions = '-';
                        @if (session('user_permissions.invoicemodule.invoice.edit') == '1')
                            let options = '';
                            if (row.part_payment == 1 && row.pending_amount != 0) {
                                options = `<option value='part_payment' ${row.status=="part_payment"?'selected':''}>Part Payment</option>
                                    <option value='paid' ${row.status=="paid"?'selected':''} disabled>Paid</option>
                                    <option value='pending' ${row.status=="pending"?'selected':''} disabled>Pending</option>`;
                            }
                            if (row.pending_amount == 0) {
                                options = `<option value='part_payment' ${row.status=="part_payment"?'selected':''} disabled>Part Payment</option>
                                    <option value='paid' ${row.status=="paid"?'selected':''}>Paid</option>
                                    <option value='pending' ${row.status=="pending"?'selected':''} disabled>Pending</option>`;
                            }
                            if (row.part_payment != 1 && row.part_payment != 0) {
                                options = `<option value='part_payment' ${row.status=="part_payment"?'selected':''} disabled>Part Payment</option>
                                    <option value='paid' ${row.status=="paid"?'selected':''} disabled>Paid</option>
                                    <option value='pending' ${row.status=="pending"?'selected':''}>Pending</option>`;
                            }
                            actions = `<select data-status='${row.id}' data-original-value="${row.status}" class="status" id="status_${row.id}" required>
                                ${options}
                                <option value='cancel' ${row.status=="cancel"?'selected':''}>Cancel</option>
                                <option value='due' ${row.status=="due"?'selected':''}>Over Due</option>
                            </select>`;
                        @endif
                        return actions;
                    }
                },
                {
                    data: 'id', name: 'id', orderable: false, searchable: false,
                    render: (data) => {
                        let btns = '';
                        @if (session('user_permissions.teamodule.brokeragebill.view') == '1')
                            let url = "{{ route('brokragbill.generatebrokragebillpdf', '__gardenId__') }}".replace('__gardenId__', data);
                            btns += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Download Pdf">
                                <a href="${url}" target="_blank">
                                    <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0"><i class="ri-download-line"></i></button>
                                </a></span>`;
                        @endif
                        return btns;
                    }
                },
                {
                    data: 'id', name: 'id', orderable: false, searchable: false,
                    render: (data, type, row) => `<span>
                        <button type="button" data-id="${row.id}" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Invoice"
                            class="del-btn btn btn-danger btn-rounded btn-sm my-0">
                            Delete</button></span>`
                },
                {
                    data: 'id', name: 'id', orderable: false, searchable: false, defaultContent: '-',
                    render: function (data, type, row) {
                        let receiptAllUrl = "{{ route('invoice.brokerBillgeneraterecieptall', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                        let actions = '';
                        if (row.status != 'paid') {
                            actions += `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Pay">
                                <button data-toggle="modal" data-target="#paymentmodal" data-amount="${row.grand_total}" data-id="${row.id}"
                                    class="btn btn-sm btn-primary m-0 paymentformmodal"><i class="ri-paypal-fill"></i></button></span>`;
                        }
                        if (row.part_payment == 1 && row.status == 'paid' && row.pending_amount == 0) {
                            actions += `<span><a href="${receiptAllUrl}" target="_blank">
                                <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Combined Receipt"
                                    class="reciept-btn btn btn-primary btn-rounded btn-sm m-0"><i class="ri-download-line"></i></button></a></span>`;
                        }
                        if (row.part_payment == 1) {
                            actions += `<span data-toggle="tooltip" data-placement="right" data-original-title="View All Reciept">
                                <button data-id="${row.id}" data-toggle="modal" data-target="#exampleModalScrollable"
                                    class="btn btn-sm btn-info my-0 viewpayment"><i class="ri-eye-fill"></i></button></span>`;
                        }
                        if (row.part_payment == 0 && row.status == 'paid') {
                            actions += `<span><a href="${receiptAllUrl}" target="_blank">
                                <button class="btn-info reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" data-toggle="tooltip"
                                    data-placement="bottom" data-original-title="Download Single Receipt"><i class="ri-download-line"></i></button></a></span>
                                <span data-toggle="tooltip" data-placement="right" data-original-title="Delete Payment Entry">
                                <button data-id="${row.paymentid}" data-inv-id="${row.id}" class="btn btn-sm btn-outline-danger pay-del-btn">
                                    <i class="ri-delete-bin-line"></i></button></span>`;
                        }
                        return actions;
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
                        let t = $('#data').DataTable(), page = parseInt($('#jumpToPage').val()), total = t.page.info().pages;
                        if (!isNaN(page) && page > 0 && page <= total) { t.page(page - 1).draw('page'); }
                        else { Toast.fire({ icon: 'error', title: `Enter a page between 1 and ${total}` }); }
                    }
                });
            }
        });
    }

    /* ── Payment modal default date ── */
    $('#paymentmodal').on('shown.bs.modal', function () {
        $('#payment_date').val(new Date().toISOString().split('T')[0]);
    });

    /* ── Generate Report ── */
    let params;
    function exportReport(type) {
        params = table.ajax.params();
        params.filter_payment_status = $('#filter_payment_status').val();
        params.filter_garden         = $('#filter_garden').val();
        params.filter_company        = $('#filter_company').val();
        params.filter_buyer          = $('#filter_buyer').val();
        params.filter_date_from      = $('#filter_date_from').val();
        params.filter_date_to        = $('#filter_date_to').val();
        params.type = type;
        let url = "{{ route('brokragbill.outstanding') }}" + '?' + $.param(params);
        loadershow();
        $.ajax({
            type: 'GET', url: "{{ route('brokragbill.outstanding') }}", data: params,
            success: () => { if (type === 'pdf') {
                    window.open(url, '_blank');   // stream in new tab
                } else {
                    window.location.href = url;   // direct download for excel
                } loaderhide(); },
            error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#pdfBtn').on('click', function () { exportReport('pdf'); });
    $('#excelBtn').on('click', function () { exportReport('excel'); });
    /* ── Delete Payment Record ── */
    $(document).on('click', '.pay-del-btn', function () {
        let deleteid = $(this).data('id'), invId = $(this).data('inv-id');
        let url = "{{ route('broker_paymentdetails.deletepayment', '__deleteId__') }}".replace('__deleteId__', deleteid);
        showConfirmationDialog('Are you sure?', 'to delete this payment record?', 'Yes, delete', 'No, cancel', 'question',
            () => {
                loadershow();
                $.ajax({
                    type: 'PUT', url, data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                    success: r => {
                        Toast.fire({ icon: r.status==200?'success':'error', title: r.message || (r.status==200?'Successfully deleted':'Something went wrong!') });
                        if (r.status == 200) { viewpayment(invId); table.draw(); }
                        loaderhide();
                    },
                    error: xhr => { loaderhide(); handleAjaxError(xhr); }
                });
            }
        );
    });

    /* ── Status Change ── */
    function statuschange(id, value) {
        loadershow();
        let url = "{{ route('broker_paymentdetail_.status', '__id__') }}".replace('__id__', id);
        $.ajax({
            type: 'PUT', url,
            data: { status: value, token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
            success: r => {
                Toast.fire({ icon: r.status==200?'success':'error', title: r.message || (r.status==200?'Updated':'Status not updated.') });
                if (r.status == 200) loaddata();
                loaderhide();
            },
            error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }

    $(document).on('change', '.status', function () {
        let el = $(this), old = el.data('original-value'), sid = el.data('status'), val = el.val();
        showConfirmationDialog('Are you sure?', 'to change this record status?', 'Yes, change it', 'No, cancel', 'question',
            () => { loadershow(); el.data('original-value', val); statuschange(sid, val); loaderhide(); },
            () => { $('#status_' + sid).val(old); }
        );
    });

    /* ── View Payment ── */
    $(document).on('click', '.viewpayment', function () { loadershow(); viewpayment($(this).data('id')); });

    function viewpayment(invoiceId) {
        $('#details').html('');
        let url = "{{ route('broker_paymentdetails.search', '__invoiceId__') }}".replace('__invoiceId__', invoiceId);
        $.ajax({
            type: 'GET', url, data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
            success: function (r) {
                if (r.status == 200) {
                    r.paymentdetail.forEach(v => {
                        let receiptUrl = "{{ route('invoice.brokerBillgeneratereciept', '__invoiceId__') }}".replace('__invoiceId__', v.id);
                        let tds = (v.tds_amount && v.tds_amount > 0)
                            ? `<div><b>TDS Amount:</b> ${v.tds_amount}</div><div><b>Challan No:</b> ${v.challan_no}</div><div><b>TDS Status:</b> ${v.tds_status}</div>` : '';
                        $('#details').append(`<tr><td>
                            <div class="col-md-10 float-left">
                                <div><b>Payment date:</b> ${v.datetime}</div><div><b>Total Amount:</b> ${v.amount}</div>
                                <div><b>Paid Amount:</b> ${v.paid_amount}</div>${tds}
                                <div><b>Pending Amount:</b> ${v.pending_amount}</div><div><b>Paid By:</b> ${v.paid_by ?? '-'}</div>
                            </div>
                            <div class="col-md-2 float-right p-0">
                                <a href="${receiptUrl}" target="_blank">
                                    <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Single Receipt"
                                        class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0"><i class="ri-download-cloud-fill"></i></button></a>
                                <button data-id="${v.id}" data-inv-id="${invoiceId}" class="btn btn-sm btn-danger pay-del-btn float-right">
                                    <i class="ri-delete-bin-line"></i></button>
                            </div></td></tr>`);
                    });
                } else {
                    $('#details').html('<tr><td>No data Found</td></tr>');
                    Toast.fire({ icon: 'error', title: r.message });
                }
                loaderhide();
            },
            error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }

    /* ── View Details (modal) ── */
    $(document).on('click', '.view-btn', function () {
        let garden_id = $(this).data('view');
        $("#exampleModalScrollable .modal-dialog").addClass('modal-lg');
        $('#details').html(''); loadershow();
        $.ajax({
            type: 'GET', url: "{{ route('brokeragebill.getOtherData') }}",
            data: { garden_id, company_id: COMPANY_ID, user_id: USER_ID, token: API_TOKEN },
            success: function (r) {
                if (!r.data.length) { $('#details').html('<tr><td colspan="8">No data found</td></tr>'); loaderhide(); return; }
                $('#details').append(`<tr><th>Id</th><th>Invoice No</th><th>Brokerage</th><th>Grade</th><th>Bags</th><th>Total Kg</th><th>Rate Per kg</th><th>Total Broker</th></tr>`);
                let id = 1;
                r.data.forEach(item => {
                    $('#viewmodaltitle').text(item.garden_name + '- Details').addClass('font-weight-bold');
                    $('#details').append(`<tr>
                        <td>${id??'-'}</td><td>${item.invoice_no??'-'}</td><td>${item.brokerage??'0'}(%)</td>
                        <td>${item.grade??'-'}</td><td>${item.bags??'-'}</td><td>${item.net_kg??'-'}</td>
                        <td>${item.rate??'-'}</td><td>${((item.net_kg??0)*(item.rate??0)*(item.brokerage??1))/100}</td>
                    </tr>`);
                    id++;
                });
                loaderhide();
            },
            error: xhr => { console.log(xhr.responseText); loaderhide(); }
        });
    });

    $('#exampleModalScrollable').on('hidden.bs.modal', function () { $('#details').html(''); $('#addfooterbutton').html(''); });

    /* ── Payment form calculations ── */
    $('#paidamount, #tds_amount').on('change keyup', function () {
        let paid = parseFloat($('#paidamount').val()) || 0, tds = parseFloat($('#tds_amount').val()) || 0;
        let total = parseInt($('#info-total_amount').text()) || 0, recv = parseInt($('#info-total_received_amount').text()) || 0;
        $('.info-pending_amount').text(total - recv - paid - tds);
    });
    $('#tds_applicable').on('change', function () { $('.tds_inputs').toggle($(this).is(':checked')); });

    /* ── Payment form submit ── */
    $('#paymentform').submit(function (e) {
        e.preventDefault(); loadershow();
        $.ajax({
            type: 'POST', url: "{{ route('broker_paymentdetails.store') }}", data: $(this).serialize(),
            success: r => { Toast.fire({ icon: r.status==200?'success':'error', title: r.message }); if (r.status == 200) { loaddata(); $('#paymentform')[0].reset(); $('#paymentmodal').modal('hide'); } loaderhide(); },
            error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    });

    /* ── Payment modal open ── */
    $(document).on('click', '.paymentformmodal', function () {
        $('#paymentform')[0].reset(); $('.tds_inputs').hide();
        let invoiceid = $(this).data('id'), amount = $(this).data('amount');
        $('#inv_id').val(invoiceid); loadershow();
        let url = "{{ route('broker_paymentdetails.pendingpayment', '__invoiceId__') }}".replace('__invoiceId__', invoiceid);
        $.ajax({
            type: 'GET', url, data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
            success: r => {
                if (r.status == 200) {
                    let recv = amount - r.payment[0].pending_amount;
                    $('#paidamount').val(r.payment[0].pending_amount).attr('max', r.payment[0].pending_amount);
                    $('#info-total_received_amount').text(recv);
                } else { $('#paidamount').val(amount).attr('max', amount); $('#info-total_received_amount').text(0); }
                $('#info-total_amount').text(amount); $('.info-pending_amount').text(0); loaderhide();
            },
            error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    });

    /* ── Delete ── */
    $(document).on('click', '.del-btn', function () {
        let deleteid = $(this).data('id');
        showConfirmationDialog('Are you sure?', 'to delete this record?', 'Yes, delete', 'No, cancel', 'question',
            () => {
                loadershow();
                $.ajax({
                    type: 'PUT',
                    url: "{{ route('brokeragebill.brokeragebillpdfdelete', '__deleteId__') }}".replace('__deleteId__', deleteid),
                    data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                    success: r => { loaderhide(); Toast.fire({ icon: r.status==200?'success':'error', title: r.message }); if (r.status == 200) loaddata(); },
                    error: xhr => { loaderhide(); handleAjaxError(xhr); }
                });
            }
        );
    });

    /* ── Apply / Clear ── */
    $('.applyfilters').on('click',  function () { table.draw(); });
    $('.removefilters').on('click', function () {
        $('#filter_payment_status').val(null).trigger('change');
        $('#filter_company').val(null).trigger('change');
        $('#filter_buyer').val(null).trigger('change');
        $('#filter_garden').val(null).trigger('change');
        $('#filter_date_to, #filter_date_from').val('');
        table.draw();
    });
    $('#pdfDateForm').on('submit', function (e) {
            e.preventDefault();
            loadershow();
            $.ajax({
                type: 'POST',
                url : "{{ route('brokeragebill.brokeragebillpdf') }}",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.status == 200) {
                        Toast.fire({ icon: 'success', title: response.message });
                        $('#pdfDateForm')[0].reset();
                        $('#pdfDateModal').modal('hide');
                        $('#pdfModalSummary').hide();
                        // Reset checkboxes + hide bulk button
                        $('.invoice-checkbox, #selectAllCheckbox').prop('checked', false);
                        $('#bulkGeneratePdfBtn').addClass('d-none').removeData('selected-rows');
                        $('#bulkSelectionCount').text('0');
                        table.draw();
                    } else if (response.status == 500) {
                        Toast.fire({ icon: 'error', title: response.message });
                        $('#pdfDateForm')[0].reset();
                        $('#pdfDateModal').modal('hide');
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                        $('#pdfDateForm')[0].reset();
                    }
                    loaderhide();
                },
                error: function (xhr) { console.log(xhr.responseText); loaderhide(); }
            });
        });
    $('#pdfDateModal').on('hidden.bs.modal', function () {
        // Reset the form fields
        $('#pdfDateForm')[0].reset();

        // Reset Select2 dropdown (clear selection)
        $('#invoice_id').val(null).trigger('change');

        // Hide summary banner
        $('#pdfModalSummary').hide();

        // Clear hidden inputs manually if needed
        $('#garden_id').val('');
        $('#company_details_id').val('');

        // Clear brokerage amount field
        $('#brokrageAmount').val('');
    });    
    function loadinvoiceids()
    {
        let url = "{{ route('brokeragebill.brokerbillinvoicelist') }}";
        $.ajax({
            type: 'GET',
            url: url,
            data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
            success: r => {
                if (r.status == 200 && r.data.length) {
                    $('#invoice_id').html('<option value="" disabled selected>Select Invoice</option>');
                    r.data.forEach(v => { 
                        $('#invoice_id').append(`
                            <option 
                                value="${v.invoice_id}" 
                                data-line_total="${v.line_total}" 
                                data-brokerage="${v.brokerage}"
                                data-garden_id="${v.garden_id}"
                                data-company_details_id="${v.company_details_id}">
                                [${v.invoice_number}] - [${v.company_name}]
                            </option>`);
                    });
                } else {
                    $('#invoice_id').html('<option value="" disabled selected>No Invoices Found</option>');
                }
            },
            error: xhr => { 
                console.log(xhr.responseText); 
                Toast.fire({ icon: 'error', title: 'Error loading invoices' }); 
            }
        });
    }

    $(document).on("click", ".generate-invoice", function () { 
        loadinvoiceids(); 
        $('#pdfDateModal').modal('show'); 
    });

    $("#invoice_id").on('change', function () {
        let selectedOption = $(this).find('option:selected');
        let lineTotal = parseFloat(selectedOption.data('line_total')) || 0;
        let brokerage = parseFloat(selectedOption.data('brokerage')) || 0;
        let brokerageAmount = ((lineTotal * brokerage) / 100).toFixed(2);
        let gardenId = selectedOption.data('garden_id') || '';
        let companyDetailsId = selectedOption.data('company_details_id') || '';

        $('#line_total').val(lineTotal.toFixed(2));
        $('#brokerage').val(brokerage.toFixed(2));
        $('#brokrageAmount').val(brokerageAmount);
        $('#garden_id').val(gardenId);
        $('#company_details_id').val(companyDetailsId);

        $('#summaryCount').text('1');
        $('#pdfModalSummary').show();
    });

    // Optional: Recalculate brokerage amount if user changes brokerage %
    $('#brokerage').on('input', function() {
        let lineTotal = parseFloat($('#line_total').val()) || 0;
        let brokeragePercent = parseFloat($(this).val()) || 0;
        let brokerageAmount = ((lineTotal * brokeragePercent) / 100).toFixed(2);
        $('#brokrageAmount').val(brokerageAmount);
    });
    $('#invoice_id').select2({
        placeholder: "Select Invoice",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#pdfDateModal'),
        templateResult: function (option) {
            if (!option.id) return option.text;
            const selected = $('#invoice_id').val();
            if (Array.isArray(selected) && selected.includes(option.id)) return null;
            if (selected == option.id) return null;
            return option.text;
        }
    });
});
</script>
@endpush