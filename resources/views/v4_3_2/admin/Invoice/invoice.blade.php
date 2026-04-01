@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Invoicelist
@endsection
@section('table_title')
    Invoice
@endsection

@section('style')
    <style>
        .ui-widget-header { background: #1518b117 !important; border: 1px solid #e1ded9 !important; }
.btn-info { background-color: #253566 !important; border-color: #253566 !important; color: white; }
.btn-info:hover { background-color: #39519b !important; color: rgb(255,255,255); }
.btn-success { background-color: #67d5a5d9 !important; border-color: var(--iq-success) !important; color: black !important; }
.btn-success:hover { background-color: #16d07ffa !important; border-color: var(--iq-success) !important; color: rgb(250,250,250) !important; }
.select2-results__options { max-height: 150px !important; overflow-y: auto !important; }

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
    flex: 0 0 180px; min-width: 180px;
    padding: 10px 12px; box-sizing: border-box;
    border-right: 1px solid #eaecf2;
}
.filter-field:first-child { border-radius: 8px 0 0 8px; }
.filter-field:last-child  { border-radius: 0 8px 8px 0; border-right: none; }

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
.action-bar > div { flex: 1; }
.action-bar > div:nth-child(2) { text-align: center; }
.action-bar > div:nth-child(3) { text-align: right; }

/* ───────── MOBILE ───────── */
@media (max-width: 576px) {
    .filter-bar-inner { flex-direction: column; align-items: stretch; }
    .filter-scroll-area { flex-direction: column; overflow-x: visible; gap: 6px; }
    .filter-field {
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
        .action-bar { flex-direction: column; }
        .action-bar > div { text-align: left !important; }
    }
    </style>
@endsection


@section('table-content')
 <div class="filter-bar">
    <div class="filter-bar-header">
        <i class="ri-filter-3-line"></i> Filters
    </div>
    <div class="filter-bar-inner">

        <div class="filter-scroll-area">

            <div class="filter-field">
                <label>Buyer</label>
                <select name="filter_buyer" class="filter form-control select2" id="filter_buyer">
                    <option value=""></option>
                </select>
            </div>

            <div class="filter-field">
                <label>Company</label>
                <select name="filter_company" class="filter form-control select2" id="filter_company">
                    <option value=""></option>
                </select>
            </div>

            <div class="filter-field">
                <label>Payment Status</label>
                <select name="filter_payment_status" class="filter form-control select2" id="filter_payment_status">
                    <option value=""></option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="part_payment">Part Payment</option>
                    <option value="cancel">Cancel</option>
                    <option value="due">Over Due</option>
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
<div class="action-bar d-flex justify-content-between align-items-center w-100 mb-3">

    <div>
        <button class="btn btn-primary" id="pdfBtn">
            <i class="ri-file-chart-line"></i> Generate Report
        </button>
    </div>

    <div>
        <button id="bulkGeneratePdfBtn"
            class="btn btn-warning d-none"
            data-toggle="tooltip" data-placement="bottom"
            data-original-title="Generate Commission Bill for selected invoices">
            <i class="ri-file-add-line"></i> Commission Bill
            <span id="bulkSelectionCount" class="badge badge-light ml-1">0</span>
        </button>
    </div>

    <div>
        @if (session('user_permissions.invoicemodule.invoice.add') == '1')
            <button class="btn btn-success generate-invoice"
                data-toggle="tooltip" data-placement="bottom"
                data-original-title="Create New Invoice">
                <i class="ri-add-line"></i> New Invoice
            </button>
        @endif
    </div>

</div>
    {{-- ── Top bar: Generate Report + Bulk Commission Bill button ─────────────── --}}
    <!-- <div class="d-flex align-items-center mb-2" style="gap:8px;">
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Create Report"
            class="btn btn-sm btn-primary" id="pdfBtn">
            <span id="pdf-data">Generate Report</span>
        </button>

        {{-- Hidden until 1+ checkbox is ticked and all from same company --}}
        <button id="bulkGeneratePdfBtn"
            class="btn btn-sm btn-warning d-none"
            data-toggle="tooltip" data-placement="bottom"
            data-original-title="Generate Commission Bill for selected invoices">
            <i class="ri-file-add-line"></i> Generate Commission Bill
            <span id="bulkSelectionCount" class="badge badge-light ml-1">0</span>
        </button>
    </div> -->

    {{-- ── Generate Invoice Modal ──────────────────────────────────────────────── --}}
    <div class="modal fade" id="generateinvoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="generateinvoiceForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Invoice</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="token"      value="{{ session('api_token') }}" required />
                            <input type="hidden" name="user_id"    value="{{ session('user_id') }}">
                            <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                        </div>
                        <div class="col-sm-11 mb-3">
                            <label for="companymaster_id">Company</label><span style="color:red;">*</span>
                            <select class="form-control select2" id="companymaster_id" name="companymaster_id" required>
                                <option selected disabled>Select Company</option>
                            </select>
                            <span class="error-msg" id="error-companymaster_id" style="color:red;"></span>
                        </div>
                        <div class="col-sm-11 mb-3">
                            <label for="buyer_id">Buyer</label><span style="color:red;">*</span>
                            <select class="form-control select2" id="buyer_id" name="buyer_id" required>
                                <option selected disabled>Select buyer</option>
                            </select>
                            <span class="error-msg" id="error-buyer" style="color:red;"></span>
                        </div>
                        <div class="col-sm-11 mb-3">
                            <label for="invoice_no">Invoice / Lot No</label><span style="color:red;">*</span>
                            <select class="form-control select2" id="invoice_no" name="invoice_no" multiple></select>
                            <span class="error-msg" id="error-invoice_no" style="color:red;"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Generate</button>
                        <button type="button" class="btn btn-secondary" id="modalcancelbtn" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Commission Bill / PDF Modal ─────────────────────────────────────────── --}}
    <div class="modal fade" id="pdfDateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="pdfDateForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Commission Bill PDF</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        {{-- Hidden system fields --}}
                        <input type="hidden" name="token"              value="{{ session('api_token') }}" required />
                        <input type="hidden" name="user_id"            value="{{ session('user_id') }}">
                        <input type="hidden" name="company_id"         value="{{ session('company_id') }}">
                        <input type="hidden" name="garden_id"          id="garden_id"          value="">
                        <input type="hidden" name="invoice_id"         id="invoice_id"         value="">
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

    {{-- ── DataTable ────────────────────────────────────────────────────────────── --}}
    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAllCheckbox" disabled></th>
                <td>ID</td>
                <th>Invoice ID</th>
                <th>Invoice Date</th>
                <th>Company Name</th>
                <th>Buyer Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Invoice</th>
                <th>Payment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata"></tbody>
    </table>

    {{-- ── Payment Modal ────────────────────────────────────────────────────────── --}}
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
                            <input type="hidden" name="user_id"    value="{{ session('user_id') }}"   required />
                            <input type="hidden" name="company_id" value="{{ session('company_id') }}" required />
                            <input type="hidden" name="token"      value="{{ session('api_token') }}"  required />
                            <input type="hidden" name="inv_id"     id="inv_id">

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

@endsection

@push('ajax')
<script>
    let isEventBound = false;

    $('document').ready(function () {

        let message = "{{ session('message') }}";
        if (message) { Toast.fire({ icon: 'error', title: message }); }

        let getbuyername    = [];
        let getcompanyname  = [];
        var global_response = '';

        const API_TOKEN  = "{{ session()->get('api_token') }}";
        const COMPANY_ID = "{{ session()->get('company_id') }}";
        const USER_ID    = "{{ session()->get('user_id') }}";

        // ────────────────────────────────────────────────────────────────────────────
        // Sidebar filter selects
        // ────────────────────────────────────────────────────────────────────────────
        $('#filter_payment_status').select2({ placeholder: "Select Payment Status", allowClear: true, width: '100%' });

        function getCompanyData() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET', url: "{{ route('companymaster.index') }}",
                    data: { user_id: USER_ID, company_id: COMPANY_ID, token: API_TOKEN },
                    success: function (r) { loaderhide(); resolve(r); },
                    error  : function (xhr) { loaderhide(); handleAjaxError(xhr); reject(xhr); }
                });
            });
        }

        function getBuyerData() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET', url: "{{ route('buyer.index') }}",
                    data: { user_id: USER_ID, company_id: COMPANY_ID, token: API_TOKEN },
                    success: function (r) { loaderhide(); resolve(r); },
                    error  : function (xhr) { loaderhide(); handleAjaxError(xhr); reject(xhr); }
                });
            });
        }

        function loadFilters() {
            return new Promise(resolve => {
                var fd = JSON.parse(sessionStorage.getItem('filterData'));
                if (fd) {
                    $.each(fd, function (k, v) { if (v != ' ') { $('#' + k).val(v); } });
                    $('#filter_company, #filter_buyer').trigger('change');
                    loaddata();
                    sessionStorage.removeItem('filterData');
                    loaderhide();
                    resolve();
                } else { resolve(); loaddata(); }
            });
        }

        async function initialize() {
            try {
                const [companyRes, buyerRes] = await Promise.all([getCompanyData(), getBuyerData()]);

                if (companyRes.status == 200 && companyRes.data != '') {
                    $.each(companyRes.data, function (k, v) {
                        getcompanyname.push(v.company_name);
                        $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`);
                    });
                    $('#filter_company').val('').select2({ search: true, placeholder: 'Select Company', allowClear: true });
                } else {
                    $('#filter_company').val('').select2({ search: true, placeholder: 'No company found', allowClear: true });
                }

                if (buyerRes.status == 200 && buyerRes.data != '') {
                    $.each(buyerRes.data, function (k, v) {
                        getbuyername.push(v.name);
                        $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`);
                    });
                    $('#filter_buyer').val('').select2({ search: true, placeholder: 'Select Buyer', allowClear: true });
                } else {
                    $('#filter_buyer').val('').select2({ search: true, placeholder: 'No buyer found', allowClear: true });
                }

                loaderhide();
                await loadFilters();
            } catch (err) {
                console.error('Init error:', err);
                Toast.fire({ icon: 'error', title: 'An error occurred while initializing' });
                loaderhide();
            }
        }

        initialize();

        // ────────────────────────────────────────────────────────────────────────────
        // Generate Invoice modal
        // ────────────────────────────────────────────────────────────────────────────
        $("#modalcancelbtn").on('click', function () {
            $('#generateinvoiceForm')[0].reset();
            $('#companymaster_id, #buyer_id, #invoice_no').val(null).trigger('change');
            $('#generateinvoiceModal').modal('hide');
            $('#companymaster_id').empty().append('<option selected disabled>Select Company</option>');
        });

        $('#generateinvoiceForm').on('submit', function (e) {
            e.preventDefault();
            let selectedOptions = $('#invoice_no option:selected');

            if (selectedOptions.length == 0) {
                Toast.fire({ icon: 'error', title: 'Please select at least one Invoice' });
                return false;
            }

            let firstTransportId = $(selectedOptions[0]).data('tranport_id');
            let sameTransport = true;
            selectedOptions.each(function () {
                if ($(this).data('tranport_id') != firstTransportId) { sameTransport = false; return false; }
            });
            if (!sameTransport) {
                Toast.fire({ icon: 'error', title: 'Selected invoices must have the same Transport' });
                return false;
            }

            loadershow();
            var formData = {
                company_ids  : $('#companymaster_id').val(),
                buyer_parties: $('#buyer_id').val(),
                invoice_no   : $('#invoice_no').val(),
                order_detail_ids: $('#invoice_no option:selected').map(function() {
                    return $(this).data('order_detail_id');
                }).get(),
                user_id: USER_ID, company_id: COMPANY_ID, token: API_TOKEN,
                _token: "{{ csrf_token() }}"
            };
            for (var key in formData) {
                if (!formData[key]) {
                    Toast.fire({ icon: 'error', title: key.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase()) + ' is required' });
                    loaderhide(); return false;
                }
            }

            $.ajax({
                url: "{{ route('brokerpurchase.lot_no_createInvoice') }}", type: 'GET', data: formData,
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
                error: function (xhr) { loaderhide(); Toast.fire({ icon: 'error', title: 'AJAX request failed' }); }
            });
        });

        function companymaster() {
            loadershow();
            $('#companymaster_id').empty().append('<option selected disabled>Select company</option>');
            $('#buyer_id').empty().append('<option selected disabled>Select buyer</option>');
            ajaxRequest('GET', "{{ route('companymaster.index') }}", { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID })
                .done(function (r) {
                    if (r.status == 200 && r.data.length > 0) {
                        $.each(r.data, function (k, v) {
                            const d = [v.company_name, v.mobile_1, v.email].filter(Boolean).join(' - ');
                            $('#companymaster_id').append(`<option data-gstno='${v.gst_no}' value='${v.id}'>${d}</option>`);
                        });
                    } else { $('#companymaster_id').append('<option disabled>No Data found</option>'); }
                }).fail(xhr => handleAjaxError(xhr)).always(() => loaderhide());
        }

        function buyer_ids() {
            loadershow();
            $('#buyer_id').empty().append('<option selected disabled>Select buyer</option>');
            ajaxRequest('GET', "{{ route('company_buyer.index') }}", {
                token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID,
                companymaster_id: $('#companymaster_id').val()
            }).done(function (r) {
                if (r.status == 200 && r.data.length > 0) {
                    $.each(r.data, function (k, v) {
                        const d = [v.name, v.mobile_1, v.email].filter(Boolean).join(' - ');
                        $('#buyer_id').append(`<option data-gstno='${v.gst_no}' value='${v.id}'>${d}</option>`);
                    });
                } else { $('#buyer_id').append('<option disabled>No Data found</option>'); }
            }).fail(xhr => handleAjaxError(xhr)).always(() => loaderhide());
        }

        function invoice_no(buyer_id, pre = []) {
            loadershow();
            $('#invoice_no').empty();
            ajaxRequest('GET', "{{ route('invoice_no.index') }}", {
                token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID,
                buyer_id: buyer_id, company_ids: $('#companymaster_id').val()
            }).done(function (r) {
                if (r.status == 200 && r.data.length > 0) {
                    $.each(r.data, function (k, v) {
                        $('#invoice_no').append(`
                            <option value='${v.invoice_no}' data-order-id='${v.order_id}' data-tranport_id='${v.tranport_id}' data-order_detail_id ='${v.id}'>
                                invoice/lot no: ${v.invoice_no + (v.garden_name ? ' - ' + v.garden_name : '')} - Order No: ${v.order_id} - Transport: ${v.tranport_name}
                            </option>`);
                    });
                    if (pre.length > 0) { $('#invoice_no').val(pre).trigger('change'); }
                } else { $('#invoice_no').append('<option disabled>No Data found</option>'); }
            }).fail(xhr => handleAjaxError(xhr)).always(() => loaderhide());
        }

        $('#companymaster_id').on('change', function () {
            $('#buyer_id').empty().append('<option selected disabled>Select buyer</option>');
            if ($(this).val() == 'add_companymaster_id') { $('#exampleModalScrollable').modal('show'); return; }
            buyer_ids();
        });
        $('#buyer_id').on('change', function () {
            let pre = $('#hidden_invoice_ids').val();
            invoice_no($(this).val(), pre ? pre.split(',') : []);
        });

        $('#companymaster_id, #buyer_id, #invoice_no').select2({ placeholder: "Select", width: '100%', search: true });

        $(document).on("click", ".generate-invoice", function () { companymaster(); $('#generateinvoiceModal').modal('show'); });

        // ════════════════════════════════════════════════════════════════════════════
        // CHECKBOX SELECTION — same-company validation + bulk button
        // ════════════════════════════════════════════════════════════════════════════

        $(document).on('change', '.invoice-checkbox', function () {
            updateBulkGenerateBtn($(this));
        });

        $('#data').on('change', '#selectAllCheckbox', function () {
            $('.invoice-checkbox').prop('checked', $(this).is(':checked'));
            updateBulkGenerateBtn();
        });

        /**
         * Validates same-company constraint and toggles the bulk button.
         * @param {jQuery|undefined} changedCb  – the checkbox just toggled (to uncheck on mismatch)
         */
        function updateBulkGenerateBtn(changedCb) {
            let checked = $('.invoice-checkbox:checked');

            if (checked.length === 0) {
                $('#bulkGeneratePdfBtn').addClass('d-none').removeData('selected-rows');
                $('#bulkSelectionCount').text('0');
                return;
            }

            // All selected rows must share the same company_details_id
            let firstCdId  = checked.first().data('company-details-id');
            let sameCompany = true;
            checked.each(function () {
                if ($(this).data('company-details-id') != firstCdId) { sameCompany = false; return false; }
            });

            if (!sameCompany) {
                Toast.fire({ icon: 'error', title: 'Please select invoices from the same company only' });
                if (changedCb) { changedCb.prop('checked', false); }
                updateBulkGenerateBtn(); // re-evaluate
                return;
            }

            let selectedRows = [];
            checked.each(function () {
                selectedRows.push({
                    garden_ids        : $(this).data('garden-ids'),
                    company_details_id: $(this).data('company-details-id'),
                    line_total        : parseFloat($(this).data('line-total'))  || 0,
                    brokerage         : parseFloat($(this).data('brokerage'))   || 0,
                    invoice_id        : $(this).data('invoice-id'),
                });
            });

            $('#bulkSelectionCount').text(selectedRows.length);
            $('#bulkGeneratePdfBtn').removeClass('d-none').data('selected-rows', selectedRows);
        }

        // Bulk button → open pdfDateModal with combined totals
        $(document).on('click', '#bulkGeneratePdfBtn', function () {
            let selectedRows = $(this).data('selected-rows');
            if (!selectedRows || selectedRows.length === 0) {
                Toast.fire({ icon: 'error', title: 'No rows selected' });
                return;
            }

            let allGardenIds       = selectedRows.map(r => r.garden_ids).join(',');
            let allInvoiceIds      = selectedRows.map(r => r.invoice_id).join(',');
            let totalLineTotal     = selectedRows.reduce((s, r) => s + r.line_total, 0);
            let company_details_id = selectedRows[0].company_details_id;
            let brokerage          = selectedRows[0].brokerage;

            // Populate modal
            $('#garden_id').val(allGardenIds);
            $('#invoice_id').val(allInvoiceIds);
            $('#company_details_id').val(company_details_id);
            $('#line_total').val(totalLineTotal.toFixed(2));
            $('#brokerage').val(brokerage);
            $('#brokrageAmount').val((totalLineTotal * brokerage / 100).toFixed(2));

            // Show summary info banner
            $('#summaryInvoiceIds').text(allInvoiceIds);
            $('#summaryCount').text(selectedRows.length);
            $('#pdfModalSummary').show();

            $('#pdfDateModal').modal('show');
        });

        // ════════════════════════════════════════════════════════════════════════════
        // SINGLE ROW: .generate-pdf button → open pdfDateModal
        // ════════════════════════════════════════════════════════════════════════════
        $(document).on("click", ".generate-pdf", function () {
            let gardenId           = $(this).data('id');
            let company_details_id = $(this).data('company_details_id');
            let line_total         = parseFloat($(this).data('line_total'))  || 0;
            let brokerage          = parseFloat($(this).data('brokerage'))   || 0;
            let invoice_id         = $(this).data('invoice_id');

            $('#garden_id').val(gardenId);
            $('#company_details_id').val(company_details_id);
            $('#invoice_id').val(invoice_id);
            $('#line_total').val(line_total);
            $('#brokerage').val(brokerage);
            $('#brokrageAmount').val((line_total * brokerage / 100).toFixed(2));

            // Hide the multi-invoice summary banner for single-row opens
            $('#pdfModalSummary').hide();

            $('#pdfDateModal').modal('show');
        });

        // Live brokerage recalculate
        $('#brokerage').on('input', calculateBrokerage);
        calculateBrokerage();
        function calculateBrokerage() {
            let pct   = parseFloat($('#brokerage').val())   || 0;
            let total = parseFloat($('#line_total').val())  || 0;
            $('#brokrageAmount').val((total * pct / 100).toFixed(2));
        }

        // pdfDateForm submit
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

        // ════════════════════════════════════════════════════════════════════════════
        // DATATABLE
        // ════════════════════════════════════════════════════════════════════════════
        let table  = '';
        var search = {!! json_encode($search) !!};

        function loaddata() {
            loadershow();
            table = $('#data').DataTable({
                language  : { lengthMenu: '_MENU_ &nbsp;Entries per page' },
                destroy   : true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url : "{{ route('invoice.inv_list') }}",
                    data: function (d) {
                        d.user_id               = USER_ID;
                        d.company_id            = COMPANY_ID;
                        d.token                 = API_TOKEN;
                        d.filter_payment_status = $('#filter_payment_status').val();
                        d.filter_company        = $('#filter_company').val();
                        d.filter_buyer          = $('#filter_buyer').val();
                    },
                    dataSrc: function (json) {
                        $('#pdfBtn').removeClass('d-none');
                        if (json.message) {
                            Toast.fire({ icon: 'error', title: json.message || 'Something went wrong!' });
                            $('#pdfBtn').addClass('d-none');
                        }
                        global_response = json;
                        return json.data;
                    },
                    complete: function () { companymaster(); loaderhide(); },
                    error   : function (xhr) {
                        global_response = '';
                        console.log(xhr.responseText);
                        Toast.fire({ icon: 'error', title: 'Error loading data' });
                    }
                },
                order : [[0, 'desc']],
                search: { search: search },
                columns: [

                    // ── Col 0: Checkbox ────────────────────────────────────────────
                    {
                        data: null, orderable: false, searchable: false, defaultContent: '',
                        render: function (data, type, row) {
                            // Show checkbox only if no commission bill has been generated yet
                            if (!row.brokerbill_no) {
                                return `
                                    <input type="checkbox"
                                        class="invoice-checkbox"
                                        data-invoice-id="${row.id}"
                                        data-garden-company-id="${row.garden_company_id}"
                                        data-garden-ids="${row.garden_ids}"
                                        data-company-details-id="${row.company_details_id}"
                                        data-line-total="${row.line_total}"
                                        data-brokerage="${row.brokerage}">`;
                            }
                            return ''; // commission bill already exists — no checkbox
                        }
                    },

                    // ── Col 1: ID ──────────────────────────────────────────────────
                    { data: 'id',                  name: 'id',                  orderable: true,  searchable: true,  defaultContent: '-' },
                    // ── Col 2: Invoice No ──────────────────────────────────────────
                    { data: 'inv_no',              name: 'inv_no',              orderable: true,  searchable: true,  defaultContent: '-' },
                    // ── Col 3: Invoice Date ────────────────────────────────────────
                    { data: 'inv_date_formatted',  name: 'inv_date_formatted',  orderable: false, searchable: true,  defaultContent: '-' },
                    // ── Col 4: Company ─────────────────────────────────────────────
                    { data: 'garden_company_name', name: 'garden_company_name', orderable: true,  searchable: true,  defaultContent: '-' },
                    // ── Col 5: Buyer ───────────────────────────────────────────────
                    { data: 'customer',            name: 'customer',            orderable: true,  searchable: true,  defaultContent: '-' },
                    // ── Col 6: Amount ──────────────────────────────────────────────
                    {
                        data: 'grand_total', name: 'grand_total', orderable: true, searchable: true, defaultContent: '-',
                        render: function (data, type, row) { return `${row.currency_symbol} ${row.grand_total}`; }
                    },

                    // ── Col 7: Status ──────────────────────────────────────────────
                    {
                        data: 'status', name: 'status', orderable: true, searchable: true, defaultContent: '-',
                        render: function (data, type, row) {
                            let actions = '-';
                            @if (session('user_permissions.invoicemodule.invoice.edit') == '1')
                                let options = '';
                                if (row.part_payment == 1 && row.pending_amount != 0) {
                                    options = `
                                        <option value='part_payment' ${row.status=="part_payment"?'selected':''}>Part Payment</option>
                                        <option value='paid'         ${row.status=="paid"        ?'selected':''} disabled>Paid</option>
                                        <option value='pending'      ${row.status=="pending"     ?'selected':''} disabled>Pending</option>`;
                                }
                                if (row.pending_amount == 0) {
                                    options = `
                                        <option value='part_payment' ${row.status=="part_payment"?'selected':''} disabled>Part Payment</option>
                                        <option value='paid'         ${row.status=="paid"        ?'selected':''}>Paid</option>
                                        <option value='pending'      ${row.status=="pending"     ?'selected':''} disabled>Pending</option>`;
                                }
                                if (row.part_payment != 1 && row.part_payment != 0) {
                                    options = `
                                        <option value='part_payment' ${row.status=="part_payment"?'selected':''} disabled>Part Payment</option>
                                        <option value='paid'         ${row.status=="paid"        ?'selected':''} disabled>Paid</option>
                                        <option value='pending'      ${row.status=="pending"     ?'selected':''}>Pending</option>`;
                                }
                                actions = `
                                    <select data-status='${row.id}' data-original-value="${row.status}"
                                        class="status" id="status_${row.id}" name="" required>
                                        ${options}
                                        <option value='cancel' ${row.status=="cancel"?'selected':''}>Cancel</option>
                                        <option value='due'    ${row.status=="due"   ?'selected':''}>Over Due</option>
                                    </select>`;
                            @endif
                            return actions;
                        }
                    },

                    // ── Col 8: Invoice PDF Download ────────────────────────────────
                    {
                        data: 'id', name: 'id', orderable: false, searchable: false, defaultContent: '-',
                        render: function (data, type, row) {
                            let actions = '-';
                            @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                let url = "{{ route('invoice.generatepdf', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                                actions = `
                                    <span data-toggle="tooltip" data-placement="left" data-original-title="Download Invoice Pdf">
                                        <a href="${url}" target="_blank">
                                            <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0">
                                                <i class="ri-download-line"></i>
                                            </button>
                                        </a>
                                    </span>`;
                            @endif
                            return actions;
                        }
                    },

                    // ── Col 9: Payment ─────────────────────────────────────────────
                    {
                        data: 'id', name: 'id', orderable: false, searchable: false, defaultContent: '-',
                        render: function (data, type, row) {
                            let receiptAllUrl = "{{ route('invoice.generaterecieptll', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                            let actions = '';

                            if (row.status != 'paid') {
                                actions += `
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Pay">
                                        <button data-toggle="modal" data-target="#paymentmodal"
                                            data-amount="${row.grand_total}" data-id="${row.id}"
                                            class="btn btn-sm btn-primary m-0 paymentformmodal">
                                            <i class="ri-paypal-fill"></i>
                                        </button>
                                    </span>`;
                            }
                            if (row.part_payment == 1 && row.status == 'paid' && row.pending_amount == 0) {
                                actions += `
                                    <span>
                                        <a href="${receiptAllUrl}" target="_blank">
                                            <button data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Download Combined Receipt"
                                                class="reciept-btn btn btn-primary btn-rounded btn-sm m-0">
                                                <i class="ri-download-line"></i>
                                            </button>
                                        </a>
                                    </span>`;
                            }
                            if (row.part_payment == 1) {
                                actions += `
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="View All Reciept">
                                        <button data-id="${row.id}" data-toggle="modal"
                                            data-target="#exampleModalScrollable"
                                            class="btn btn-sm btn-info my-0 viewpayment">
                                            <i class="ri-eye-fill"></i>
                                        </button>
                                    </span>`;
                            }
                            if (row.part_payment == 0 && row.status == 'paid') {
                                actions += `
                                    <span>
                                        <a href="${receiptAllUrl}" target="_blank">
                                            <button class="btn-info reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0"
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Download Single Receipt">
                                                <i class="ri-download-line"></i>
                                            </button>
                                        </a>
                                    </span>
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="Delete Payment Entry">
                                        <button data-id="${row.paymentid}" data-inv-id="${row.id}"
                                            class="btn btn-sm btn-outline-danger pay-del-btn">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </span>`;
                            }
                            return actions;
                        }
                    },

                    // ── Col 10: Action ─────────────────────────────────────────────
                    // NOTE: brokerbill_no block REMOVED from here.
                    // Commission bill is now handled ONLY via checkboxes (bulk) or
                    // the per-row .generate-pdf button below.
                    {
                        data: 'id', name: 'id', orderable: false, searchable: false,
                        render: function (data, type, row) {
                            let actionBtns = '';

                            // Edit button
                            @if (session('user_permissions.invoicemodule.invoice.edit') == '1')
                                if (row.is_editable == 1) {
                                    let editUrl = "{{ route('admin.editinvoice', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                                    actionBtns += `
                                        <span>
                                            <a href="${editUrl}">
                                                <button type="button" data-id="${row.id}"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Edit Invoice"
                                                    class="edit-btn btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>`;
                                }
                            @endif
                            if(!row.brokerbill_no){
                            // Delete button
                            @if (session('user_permissions.invoicemodule.invoice.delete') == '1')
                                actionBtns += `
                                    <span>
                                        <button type="button" data-id="${row.id}"
                                            data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Delete Invoice"
                                            class="del-btn btn btn-danger btn-rounded btn-sm my-0">
                                            <i class="ri-delete-bin-fill"></i>
                                        </button>
                                    </span>`;
                            @endif
                            }
                            // Commission bill: Download if already generated; create button if not
                            @if (session('user_permissions.teamodule.brokeragebill.view') == '1')
                                if (row.brokerbill_no) {
                                    // ★ Bill already exists → DOWNLOAD ONLY (no add/create btn)
                                    let dlUrl = "{{ route('brokragbill.generatebrokragebillpdf', '__gardenId__') }}"
                                                 .replace('__gardenId__', row.brokerbill_no);
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Download Commission Bill PDF">
                                            <a href="${dlUrl}" target="_blank">
                                                <button type="button"
                                                    class="download-btn btn btn-info btn-rounded btn-sm my-0">
                                                    <i class="ri-download-line"></i>
                                                </button>
                                            </a>
                                        </span>`;
                                } else {
                                    // ★ Not generated yet → single-row create button (opens pdfDateModal)
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Create Commission Bill PDF">
                                            <button class="btn btn-info btn-rounded btn-sm my-0 generate-pdf"
                                                data-id="${row.garden_ids}"
                                                data-company_details_id="${row.company_details_id}"
                                                data-line_total="${row.line_total}"
                                                data-brokerage="${row.brokerage}"
                                                data-invoice_id="${row.id}">
                                                <i class="ri-file-add-line"></i>
                                            </button>
                                        </span>`;
                                }
                            @endif

                            return actionBtns;
                        }
                    }
                ],

                pagingType  : "full_numbers",
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
                            let t     = $('#data').DataTable();
                            let page  = parseInt($('#jumpToPage').val());
                            let total = t.page.info().pages;
                            if (!isNaN(page) && page > 0 && page <= total) {
                                t.page(page - 1).draw('page');
                            } else {
                                Toast.fire({ icon: 'error', title: `Enter a page between 1 and ${total}` });
                            }
                        } else {
                            Toast.fire({ icon: 'error', title: 'DataTable not yet initialized.' });
                        }
                    });
                }
            });
        }

        // ── Generate Report ───────────────────────────────────────────────────────
        let params;
        $('#pdfBtn').on('click', function () {
            params = table.ajax.params();
            params.filter_payment_status = $('#filter_payment_status').val();
            params.filter_buyer          = $('#filter_buyer').val();
            params.filter_company        = $('#filter_company').val();
            let url = "{{ route('invoice.leger') }}" + '?' + $.param(params);
            loadershow();
            $.ajax({
                type: 'GET', url: "{{ route('invoice.leger') }}", data: params,
                success: function () { window.open(url, '_blank'); loaderhide(); },
                error  : function (xhr) { loaderhide(); handleAjaxError(xhr); }
            });
        });

        // ── Delete Invoice ────────────────────────────────────────────────────────
        $(document).on("click", ".del-btn", function () {
            let deleteid = $(this).data('id');
            let url = "{{ route('invoice.delete', '__deleteId__') }}".replace('__deleteId__', deleteid);
            showConfirmationDialog('Are you sure?', 'to delete this invoice?', 'Yes, delete it', 'No, cancel', 'question',
                () => {
                    loadershow();
                    $.ajax({
                        type: 'PUT', url: url,
                        data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                        success: function (r) {
                            Toast.fire({ icon: r.status==200?'success':'error', title: r.message || 'Invoice not deleted.' });
                            if (r.status == 200) { loaddata(); }
                            loaderhide();
                        },
                        error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
                    });
                }
            );
        });

        // ── Status Change ─────────────────────────────────────────────────────────
        function statuschange(id, value) {
            loadershow();
            let url = "{{ route('invoice.status', '__id__') }}".replace('__id__', id);
            $.ajax({
                type: 'PUT', url: url,
                data: { status: value, token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                success: function (r) {
                    Toast.fire({ icon: r.status==200?'success':'error', title: r.message || 'Status not updated.' });
                    if (r.status == 200) { loaddata(); }
                    loaderhide();
                },
                error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
            });
        }

        $(document).on("change", ".status", function () {
            let el = $(this), old = el.data('original-value'), sid = el.data('status'), val = el.val();
            showConfirmationDialog('Are you sure?', 'to change this record status?', 'Yes, change it', 'No, cancel', 'question',
                () => { loadershow(); el.data('original-value', val); statuschange(sid, val); loaderhide(); },
                () => { $('#status_' + sid).val(old); }
            );
        });

        // ── Payment Modal: open ───────────────────────────────────────────────────
        $(document).on('click', '.paymentformmodal', function () {
            $('#paymentform')[0].reset();
            $('.tds_inputs').hide();
            let invoiceid = $(this).data('id');
            let amount    = $(this).data('amount');
            $('#inv_id').val(invoiceid);
            loadershow();
            let url = "{{ route('paymentdetails.pendingpayment', '__invoiceId__') }}".replace('__invoiceId__', invoiceid);
            $.ajax({
                type: 'GET', url: url,
                data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                success: function (r) {
                    if (r.status == 200) {
                        let recv = amount - r.payment[0].pending_amount;
                        $('#paidamount').val(r.payment[0].pending_amount).attr('max', r.payment[0].pending_amount);
                        $('#info-total_received_amount').text(recv);
                    } else {
                        $('#paidamount').val(amount).attr('max', amount);
                        $('#info-total_received_amount').text(0);
                    }
                    $('#info-total_amount').text(amount);
                    $('.info-pending_amount').text(0);
                    loaderhide();
                },
                error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
            });
        });

        $("#paymentmodal").on("shown.bs.modal", function () {
            $('#payment_date').val(new Date().toISOString().split('T')[0]);
        });

        // ── View Payment Details ──────────────────────────────────────────────────
        $(document).on('click', '.viewpayment', function () {
            loadershow();
            viewpayment($(this).data('id'));
        });

        function viewpayment(invoiceId) {
            $('#details').html('');
            let url = "{{ route('paymentdetails.search', '__invoiceId__') }}".replace('__invoiceId__', invoiceId);
            $.ajax({
                type: 'GET', url: url,
                data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                success: function (r) {
                    if (r.status == 200) {
                        $.each(r.paymentdetail, function (k, v) {
                            let receiptUrl = "{{ route('invoice.generatereciept', '__invoiceId__') }}".replace('__invoiceId__', v.id);
                            let tds = (v.tds_amount && v.tds_amount > 0)
                                ? `<div><b>TDS Amount:</b> ${v.tds_amount}</div><div><b>Challan No:</b> ${v.challan_no}</div><div><b>TDS Status:</b> ${v.tds_status}</div>`
                                : '';
                            $('#details').append(`
                                <tr><td>
                                    <div class="col-md-10 float-left">
                                        <div><b>Payment date:</b> ${v.datetime}</div>
                                        <div><b>Total Amount:</b> ${v.amount}</div>
                                        <div><b>Paid Amount:</b> ${v.paid_amount}</div>
                                        ${tds}
                                        <div><b>Pending Amount:</b> ${v.pending_amount}</div>
                                        <div><b>Paid By:</b> ${v.paid_by ?? '-'}</div>
                                    </div>
                                    <div class="col-md-2 float-right p-0">
                                        <a href="${receiptUrl}" target="_blank">
                                            <button data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Download Single Receipt"
                                                class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0">
                                                <i class="ri-download-cloud-fill"></i>
                                            </button>
                                        </a>
                                        <button data-id="${v.id}" data-inv-id="${invoiceId}"
                                            class="btn btn-sm btn-danger pay-del-btn float-right">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td></tr>`);
                        });
                    } else {
                        $('#details').html('<tr><td>No data Found</td></tr>');
                        Toast.fire({ icon: 'error', title: r.message });
                    }
                    loaderhide();
                },
                error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
            });
        }

        $("#exampleModalScrollable").on("hidden.bs.modal", function () {
            $('#details').html(''); $('#addfooterbutton').html('');
        });

        $('#paidamount, #tds_amount').on('change keyup', function () {
            let paid  = parseFloat($('#paidamount').val()) || 0;
            let tds   = parseFloat($('#tds_amount').val())  || 0;
            let total = parseInt($('#info-total_amount').text()) || 0;
            let recv  = parseInt($('#info-total_received_amount').text()) || 0;
            $('.info-pending_amount').text(total - recv - paid - tds);
        });

        $('#tds_applicable').on('change', function () {
            $('.tds_inputs').toggle($(this).is(':checked'));
        });

        // ── Payment Form Submit ───────────────────────────────────────────────────
        $('#paymentform').submit(function (e) {
            e.preventDefault();
            loadershow();
            $.ajax({
                type: 'POST', url: "{{ route('paymentdetails.store') }}", data: $(this).serialize(),
                success: function (r) {
                    Toast.fire({ icon: r.status==200?'success':'error', title: r.message });
                    if (r.status == 200) { loaddata(); $('#paymentform')[0].reset(); $('#paymentmodal').modal('hide'); }
                    loaderhide();
                },
                error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
            });
        });

        // ── Update Company Details ────────────────────────────────────────────────
        $(document).on("click", ".update-company-details-btn", function () {
            let invoiceid = $(this).data('id');
            let row = this;
            let url = "{{ route('invoice.updatecompanydetails', '__invoiceid__') }}".replace('__invoiceid__', invoiceid);
            showConfirmationDialog('Are you sure?', 'to update company details?', 'Yes, update', 'No, cancel', 'question',
                () => {
                    loadershow();
                    $.ajax({
                        type: 'PUT', url: url,
                        data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID, invoiceid: invoiceid },
                        success: function (r) {
                            Toast.fire({ icon: r.status==200?'success':'error', title: r.status==200?'Company details successfully updated':r.message });
                            if (r.status == 200) { $(row).hide(); }
                            loaderhide();
                        },
                        error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
                    });
                }
            );
        });

        // ── Delete Payment Record ─────────────────────────────────────────────────
        $(document).on("click", ".pay-del-btn", function () {
            let deleteid = $(this).data('id');
            let invId    = $(this).data('inv-id');
            let url = "{{ route('paymentdetails.deletepayment', '__deleteId__') }}".replace('__deleteId__', deleteid);
            showConfirmationDialog('Are you sure?', 'to delete this payment record?', 'Yes, delete', 'No, cancel', 'question',
                () => {
                    loadershow();
                    $.ajax({
                        type: 'PUT', url: url,
                        data: { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID },
                        success: function (r) {
                            Toast.fire({ icon: r.status==200?'success':'error', title: r.message || (r.status==200?'Successfully deleted':'Something went wrong!') });
                            if (r.status == 200) { viewpayment(invId); table.draw(); }
                            loaderhide();
                        },
                        error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
                    });
                }
            );
        });

        // ── Sidebar filter buttons ────────────────────────────────────────────────
        $('.applyfilters').on('click',  function () { table.draw(); hideOffCanvass(); });
        $('.removefilters').on('click', function () {
            $('#filter_payment_status, #filter_company, #filter_buyer').val(null).trigger('change');
            table.draw();
            hideOffCanvass();
        });

    });
</script>
@endpush