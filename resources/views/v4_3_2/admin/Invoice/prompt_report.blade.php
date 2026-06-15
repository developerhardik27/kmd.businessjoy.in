@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Prompt Report
@endsection
@section('title')
    Prompt Report
@endsection

@section('style')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

:root {
    --c-bg:        #f0f2f7;
    --c-white:     #ffffff;
    --c-border:    #dde3ef;
    --c-primary:   #3b5bdb;
    --c-primary-h: #2f4ac3;
    --c-primary-s: rgba(59,91,219,.08);
    --c-danger:    #e03131;
    --c-danger-s:  #fff0f0;
    --c-warn:      #e67700;
    --c-warn-s:    #fff8e7;
    --c-success:   #2f9e44;
    --c-success-s: #ebfbee;
    --c-text:      #1a1d2e;
    --c-muted:     #6b7280;
    --c-light:     #f8f9fc;
    --radius:      8px;
    --radius-sm:   6px;
    --shadow-sm:   0 1px 3px rgba(0,0,0,.07);
    --font:        'Inter', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--font) !important; background: var(--c-bg) !important; }

/* ── Field labels & controls ── */
.f-label {
    display: block; font-size: 10.5px; font-weight: 600;
    color: var(--c-muted); text-transform: uppercase;
    letter-spacing: .05em; margin-bottom: 3px;
}
.f-ctrl {
    width: 100%; background: var(--c-light);
    border: 1.5px solid var(--c-border); border-radius: var(--radius-sm);
    padding: 5px 10px; font-size: 12.5px; color: var(--c-text);
    font-family: var(--font); transition: border .15s, box-shadow .15s;
    outline: none; appearance: none; height: 32px;
}
.f-ctrl:focus { border-color: var(--c-primary); background: #fff; box-shadow: 0 0 0 3px var(--c-primary-s); }

/* ── Select2 compact ── */
.select2-container { width: 100% !important; }
.select2-container--default .select2-selection--single {
    height: 32px !important; border: 1.5px solid var(--c-border) !important;
    border-radius: var(--radius-sm) !important; background: var(--c-light) !important;
    display: flex; align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 30px !important; font-size: 12.5px; color: var(--c-text) !important; padding-left: 10px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 30px !important; }
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--c-primary) !important; box-shadow: 0 0 0 3px var(--c-primary-s) !important;
}

/* ── Button group ── */
.btn-group {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--c-border);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.btn-group .btn {
    height: 36px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 500;
    border-radius: var(--radius-sm);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0px !important;
}
</style>
@endsection

@section('form-content')

<form id="reportForm">
    @csrf
    <input type="hidden" name="token" value="{{ session('api_token') }}">
    <input type="hidden" name="user_id" value="{{ session('user_id') }}">
    <input type="hidden" name="company_id" value="{{ session('company_id') }}">

    <div class="row" style="margin: 0 -6px;">
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Company</label>
            <select name="filter_company" class="f-ctrl select2" id="filter_company">
                <option value="">Select Company</option>
            </select>
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Buyer</label>
            <select name="filter_buyer" class="f-ctrl select2" id="filter_buyer">
                <option value="">Select Buyer</option>
            </select>
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Payment Status</label>
            <select name="filter_payment_status" class="f-ctrl select2" id="filter_payment_status">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="part_payment">Part Payment</option>
                <option value="cancel">Cancel</option>
                <option value="due">Over Due</option>
            </select>
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Credit Days</label>
            <select name="filter_credit_days" class="f-ctrl select2" id="filter_credit_days">
                <option value="">All</option>
                <option value="CD">CD</option>
                <option value="15">15 Days</option>
                <option value="30">30 Days</option>
                <option value="45">45 Days</option>
                <option value="60">60 Days</option>
                <option value="90">90 Days</option>
            </select>
        </div>
    </div>

    <div class="btn-group">
        <button type="button" class="btn btn-primary" id="pdfBtn">Generate PDF</button>
        <button type="button" class="btn btn-success" id="excelBtn">Generate Excel</button>
        <button type="button" class="btn btn-info" id="sendMailBtn">Send Mail</button>
        <button type="button" class="btn btn-secondary" id="clearBtn">Clear</button>
    </div>
</form>

<div id="reportTableContainer" style="margin-top: 20px;">
    <div class="card" style="border: 1px solid var(--c-border); border-radius: var(--radius); background: var(--c-white);">
        <div class="card-body" style="padding: 15px;">
            <table id="reportTable" class="table table-bordered table-striped" style="width: 100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Invoice No</th>
                        <th>Invoice Date</th>
                        <th>Company Name</th>
                        <th>Buyer Name</th>
                        <th>Amount</th>
                        <th>Credit Days</th>
                        <th>Expected Payment Date</th>
                        <th>Status</th>
                        <th>Invoice</th>
                        <th>Payment</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Payment Modal -->
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
                        <input type="hidden" name="inv_id"     id="inv_id" required />

                        <label for="transid">Transaction id</label>
                        <input type="text" name="transid" class="form-control" id="transid" placeholder="Transaction id" />
                        <p class="modal_error-msg mb-1" id="error-transid" style="color:red;"></p>

                        <label for="payment_date">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" id="payment_date" required />
                        <p class="modal_error-msg mb-1" id="error-payment_date" style="color:red;"></p>

                        Total Amount :-&nbsp;<span class="mb-1 text-info" id="info-total_amount">0</span>,
                        &nbsp;Received Amount :-&nbsp;<span class="mb-1 text-info" id="info-total_received_amount">0</span><br>

                        <label for="paidamount">Paid Amount</label>
                        <input type="number" name="paidamount" class="form-control" id="paidamount" placeholder="Paid Amount" required />
                        <p class="modal_error-msg mb-1" id="error-paidamount" style="color:red;"></p>

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

                        <div class="tds_details">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="tdsCheck" name="tdsCheck">
                                <label class="form-check-label" for="tdsCheck">TDS Applicable</label>
                            </div>
                            <div class="tds_inputs" style="display:none;">
                                <label for="tdsamount">TDS Amount</label>
                                <input type="number" name="tdsamount" class="form-control" id="tdsamount" placeholder="TDS Amount" />
                                <p class="modal_error-msg mb-1" id="error-tdsamount" style="color:red;"></p>

                                <label for="challan_no">Challan No</label>
                                <input type="text" name="challan_no" class="form-control" id="challan_no" placeholder="Challan No" />
                                <p class="modal_error-msg mb-1" id="error-challan_no" style="color:red;"></p>

                                <label for="tds_status">TDS Status</label>
                                <select class="form-control" name="tds_status" id="tds_status">
                                    <option selected disabled>Select TDS Status</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                </select>
                                <p class="modal_error-msg mb-1" id="error-tds_status" style="color:red;"></p>
                            </div>
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

<!-- Email Input Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter Buyer Email</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Buyer Name: <span id="modalBuyerName"></span></label>
                </div>
                <div class="form-group">
                    <label for="buyerEmail">Email Address *</label>
                    <input type="email" class="form-control" id="buyerEmail" placeholder="Enter email address" required>
                    <input type="hidden" id="modalBuyerId">
                    <input type="hidden" id="modalInvNo">
                    <input type="hidden" id="modalAmount">
                    <input type="hidden" id="modalExpectedDate">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEmailBtn">Save & Send Mail</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle"><span id="viewmodaltitle"><b>Payment Details</b></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="details" width='100%' class="table table-bordered table-responsive-md table-striped">
                </table>
            </div>
            <div class="modal-footer">
                <span id="addfooterbutton"></span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('ajax')
<script>
$(document).ready(function () {
    const API_TOKEN = "{{ session()->get('api_token') }}";
    const USER_ID = "{{ session()->get('user_id') }}";
    const COMPANY_ID = "{{ session()->get('company_id') }}";

    let table;
    function loaddata() {
        loadershow();
        table = $('#reportTable').DataTable({
            language: { lengthMenu: '_MENU_ &nbsp;Entries per page' },
            pageLength: 25,
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                type: 'GET',
                url: "{{ route('prompt_report.list') }}",
                data: function (d) {
                    d.token = API_TOKEN;
                    d.user_id = USER_ID;
                    d.company_id = COMPANY_ID;
                    d.filter_payment_status = $('#filter_payment_status').val();
                    d.filter_company = $('#filter_company').val();
                    d.filter_buyer = $('#filter_buyer').val();
                    d.filter_credit_days = $('#filter_credit_days').val();
                },
                dataSrc: function (json) {
                    if (json.message) {
                        Toast.fire({ icon: 'error', title: json.message || 'Something went wrong!' });
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
                { data: 'id', name: 'id', orderable: true, searchable: true, defaultContent: '-' },
                { data: 'inv_no', name: 'inv_no', orderable: true, searchable: true, defaultContent: '-' },
                { data: 'inv_date_formatted', name: 'inv_date_formatted', orderable: false, searchable: true, defaultContent: '-' },
                { data: 'garden_company_name', name: 'garden_company_name', orderable: true, searchable: true, defaultContent: '-' },
                { data: 'customer', name: 'customer', orderable: true, searchable: true, defaultContent: '-' },
                {
                    data: 'grand_total', name: 'grand_total', orderable: true, searchable: true, defaultContent: '-',
                    render: function (data, type, row) { return `${row.currency_symbol} ${row.grand_total}`; }
                },
                { data: 'credit_days', name: 'credit_days', orderable: true, searchable: true, defaultContent: '-' },
                { data: 'expected_payment_date', name: 'expected_payment_date', orderable: false, searchable: false, defaultContent: '-' },
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
                {
                    data: 'id', name: 'id', orderable: false, searchable: false, defaultContent: '-',
                    render: function (data, type, row) {
                        let pdfUrl = "{{ route('invoice.generatepdf', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                        return `<a href="${pdfUrl}" target="_blank"><button class="btn btn-sm btn-info" title="Download PDF"><i class="ri-download-line"></i></button></a>`;
                    }
                },
                {
                    data: 'id', name: 'id', orderable: false, searchable: false, defaultContent: '-',
                    render: function (data, type, row) {
                        let receiptAllUrl = "{{ route('invoice.generaterecieptll', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                        let actions = '';
                        if (row.status != 'paid') {
                            actions += `<button data-toggle="modal" data-target="#paymentmodal" data-amount="${row.grand_total}" data-id="${row.id}" class="btn btn-sm btn-primary paymentformmodal" title="Add Payment"><i class="ri-paypal-fill"></i></button>`;
                        }
                        if (row.part_payment == 1 && row.status == 'paid' && row.pending_amount == 0) {
                            actions += `<a href="${receiptAllUrl}" target="_blank"><button class="btn btn-sm btn-primary" title="Download Combined Receipt"><i class="ri-download-line"></i></button></a>`;
                        }
                        if (row.part_payment == 1) {
                            actions += `<button data-id="${row.id}" data-toggle="modal" data-target="#exampleModalScrollable" class="btn btn-sm btn-info viewpayment" title="View All Receipt"><i class="ri-eye-fill"></i></button>`;
                        }
                        if (row.part_payment == 0 && row.status == 'paid') {
                            actions += `<a href="${receiptAllUrl}" target="_blank"><button class="btn btn-sm btn-info" title="Download Single Receipt"><i class="ri-download-line"></i></button></a>`;
                        }
                        if (row.part_payment == 0 && row.status == 'paid' && row.paymentid) {
                            actions += `<button data-id="${row.paymentid}" data-inv-id="${row.id}" class="btn btn-sm btn-outline-danger pay-del-btn" title="Delete Payment"><i class="ri-delete-bin-line"></i></button>`;
                        }
                        return actions || '-';
                    }
                }
            ],
            pagingType: 'full_numbers',
            drawCallback: function () {
                if ($('#jumpToPageWrapper').length === 0) {
                    $(".dt-paging").after(`
                        <div id="jumpToPageWrapper" class="d-flex align-items-center ml-3" style="gap:5px;">
                            <label for="jumpToPage" class="mb-0">Jump to page:</label>
                            <input type="number" id="jumpToPage" min="1" class="dt-input" style="width:80px;" />
                            <button id="jumpToPageBtn" class="btn btn-sm btn-primary">Go</button>
                        </div>`);
                }
                $(document).off('click', '#jumpToPageBtn').on('click', '#jumpToPageBtn', function () {
                    if ($.fn.DataTable.isDataTable('#reportTable')) {
                        let t = $('#reportTable').DataTable();
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

    /* ── Initialize filters ── */
    async function initialize() {
        try {
            const [companyRes, buyerRes] = await Promise.all([
                fetchData("{{ route('companymaster.index') }}"),
                fetchData("{{ route('party.index') }}")
            ]);

            if (companyRes.status == 200 && companyRes.data.length) {
                companyRes.data.forEach(v => $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`));
            }
            initSelect2('#filter_company', 'Select Company');

            if (buyerRes.status == 200 && buyerRes.data.length) {
                buyerRes.data.forEach(v => $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initSelect2('#filter_buyer', 'Select Buyer');

            initSelect2('#filter_payment_status', 'Select Status');
            initSelect2('#filter_credit_days', 'Select Credit Days');

            loaddata();
        } catch (e) {
            console.error('Initialization error:', e);
        }
    }

    function fetchData(url) {
        return $.ajax({
            type: 'GET',
            url: url,
            data: { token: API_TOKEN, user_id: USER_ID, company_id: COMPANY_ID }
        });
    }

    function initSelect2(selector, placeholder) {
        $(selector).select2({
            placeholder: placeholder,
            allowClear: true,
            width: '100%',
            templateResult: function (option) {
                if (!option.id) return option.text;
                const selected = $(selector).val();
                if (Array.isArray(selected) && selected.includes(option.id)) return null;
                if (selected == option.id) return null;
                return option.text;
            }
        });
    }

    $('#filter_company, #filter_buyer, #filter_payment_status, #filter_credit_days').on('change', function() {
        if (table) {
            table.ajax.reload();
        }
    });

    /* ── Generate Report ── */
    function generateReport(type) {
        let params = {
            _token: $('input[name="_token"]').val(),
            token: API_TOKEN,
            user_id: USER_ID,
            company_id: COMPANY_ID,
            filter_payment_status: $('#filter_payment_status').val(),
            filter_company: $('#filter_company').val(),
            filter_buyer: $('#filter_buyer').val(),
            filter_credit_days: $('#filter_credit_days').val(),
            type: type
        };

        let url = type === 'pdf'
            ? "{{ route('prompt_report.export') }}"
            : "{{ route('prompt_report.export') }}";

        loadershow();
        let form = $('<form>', { 'method': 'GET', 'action': url, 'target': '_blank' });
        $.each(params, function(key, value) {
            if (value) {
                $('<input>', { 'type': 'hidden', 'name': key, 'value': value }).appendTo(form);
            }
        });
        form.appendTo('body').submit();
        loaderhide();
    }

    $('#pdfBtn').on('click', function () { generateReport('pdf'); });
    $('#excelBtn').on('click', function () { generateReport('excel'); });

    /* ── Clear filters ── */
    $('#clearBtn').on('click', function () {
        $('#filter_payment_status').val('').trigger('change');
        $('#filter_company').val('').trigger('change');
        $('#filter_buyer').val('').trigger('change');
        $('#filter_credit_days').val('').trigger('change');
        if (table) {
            table.ajax.reload();
        }
    });

    /* ── Payment Modal: open ─────────────────────────────────────────────────── */
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
                loaderhide();
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    });

    $("#paymentmodal").on("shown.bs.modal", function () {
        $('#payment_date').val(new Date().toISOString().split('T')[0]);
    });

    /* ── TDS Checkbox Toggle ────────────────────────────────────────────────── */
    $('#tdsCheck').on('change', function () {
        $('.tds_inputs').toggle($(this).is(':checked'));
    });

    /* ── View Payment Details ────────────────────────────────────────────────── */
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
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    }

    $("#exampleModalScrollable").on("hidden.bs.modal", function () {
        $('#details').html(''); $('#addfooterbutton').html('');
    });

    /* ── Payment Form Submit ───────────────────────────────────────────────── */
    $('#paymentform').submit(function (e) {
        e.preventDefault();
        loadershow();
        $.ajax({
            type: 'POST',
            url: "{{ route('paymentdetails.store') }}",
            data: $(this).serialize(),
            success: function (response) {
                if (response.status == 200) {
                    Toast.fire({ icon: 'success', title: response.message });
                    $('#paymentform')[0].reset();
                    $('#paymentmodal').modal('hide');
                    table.draw();
                } else {
                    Toast.fire({ icon: 'error', title: response.message });
                }
                loaderhide();
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    });

    /* ── Delete Payment ────────────────────────────────────────────────────── */
    $(document).on('click', '.pay-del-btn', function () {
        if (confirm('Are you sure you want to delete this payment?')) {
            let paymentId = $(this).data('id');
            let invId     = $(this).data('inv-id');
            loadershow();
            $.ajax({
                type: 'PUT',
                url: "{{ route('paymentdetails.deletepayment', '__paymentId__') }}".replace('__paymentId__', paymentId),
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: USER_ID,
                    company_id: COMPANY_ID,
                    token: API_TOKEN
                },
                success: function (response) {
                    if (response.status == 200) {
                        Toast.fire({ icon: 'success', title: response.message });
                        table.draw();
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                    }
                    loaderhide();
                },
                error: function (xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                }
            });
        }
    });

    /* ── Status Change ── */
    $(document).on("change", ".status-change", function () {
        let id = $(this).data('id');
        let status = $(this).val();
        let originalValue = $(this).data('original-value') || $(this).find('option:selected').prev().val();
        
        if (confirm('Are you sure you want to change the status?')) {
            loadershow();
            $.ajax({
                type: 'PUT',
                url: "{{ route('invoice.status', '__id__') }}".replace('__id__', id),
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: USER_ID,
                    company_id: COMPANY_ID,
                    token: API_TOKEN,
                    status: status
                },
                success: function (response) {
                    if (response.status == 200) {
                        Toast.fire({ icon: 'success', title: response.message });
                        table.draw();
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                        $(this).val(originalValue);
                    }
                    loaderhide();
                },
                error: function (xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                }
            });
        } else {
            $(this).val(originalValue);
        }
    });

    /* ── Delete Payment ── */
    $(document).on("click", ".pay-del-btn", function () {
        let deleteid = $(this).data('id');
        let inv_id = $(this).data('inv-id');
        if (confirm('Are you sure you want to delete this payment?')) {
            loadershow();
            $.ajax({
                type: 'PUT',
                url: "{{ route('paymentdetails.deletepayment', '__id__') }}".replace('__id__', deleteid),
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: USER_ID,
                    company_id: COMPANY_ID,
                    token: API_TOKEN,
                    inv_id: inv_id
                },
                success: function (response) {
                    if (response.status == 200) {
                        Swal.fire({ icon: 'success', title: response.message, showConfirmButton: false, timer: 1500 });
                        table.draw();
                    } else {
                        Swal.fire({ icon: 'error', title: response.message, showConfirmButton: false, timer: 1500 });
                    }
                    loaderhide();
                },
                error: function (xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                }
            });
        }
    });

    /* ── Send Mail Functionality ── */
    $('#sendMailBtn').on('click', function () {
        if (!global_response || !global_response.data || global_response.data.length === 0) {
            Toast.fire({ icon: 'error', title: 'No data to send' });
            return;
        }

        // Get unique buyers from the data
        let buyers = {};
        global_response.data.forEach(row => {
            if (row.customer_id && row.customer) {
                if (!buyers[row.customer_id]) {
                    buyers[row.customer_id] = {
                        id: row.customer_id,
                        name: row.customer,
                        email: row.customer_email || null,
                        invoices: []
                    };
                }
                buyers[row.customer_id].invoices.push({
                    inv_no: row.inv_no,
                    inv_date: row.inv_date_formatted,
                    amount: row.grand_total,
                    currency_symbol: row.currency_symbol,
                    expected_payment_date: row.expected_payment_date,
                    status: row.status
                });
            }
        });

        // Fetch buyer details to get emails
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('party.index') }}",
            data: {
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            },
            success: function (response) {
                loaderhide();
                if (response.status === 200 && response.data) {
                    // Match buyers by ID and add email
                    let buyersWithEmail = [];
                    Object.values(buyers).forEach(buyer => {
                        let matchedBuyer = response.data.find(b => b.id == buyer.id);
                        if (matchedBuyer) {
                            buyersWithEmail.push({
                                id: matchedBuyer.id,
                                name: buyer.name,
                                email: matchedBuyer.email || null
                            });
                        }
                    });

                    // Check if any buyer is missing email
                    let missingEmailBuyers = buyersWithEmail.filter(b => !b.email);

                    if (missingEmailBuyers.length > 0) {
                        // Show modal for first buyer with missing email
                        let buyer = missingEmailBuyers[0];
                        $('#modalBuyerName').text(buyer.name);
                        $('#modalBuyerId').val(buyer.id);
                        $('#buyerEmail').val('');
                        $('#emailModal').modal('show');
                    } else {
                        // All buyers have emails, send mail directly
                        sendMailToBuyers(buyersWithEmail);
                    }
                } else {
                    Toast.fire({ icon: 'error', title: 'Failed to fetch buyer details' });
                }
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    });

    /* ── Save Email and Send Mail ── */
    $('#saveEmailBtn').on('click', function () {
        let buyerId = $('#modalBuyerId').val();
        let buyerEmail = $('#buyerEmail').val();

        if (!buyerEmail) {
            Toast.fire({ icon: 'error', title: 'Please enter email address' });
            return;
        }

        // Update buyer email
        loadershow();
        $.ajax({
            type: 'PUT',
            url: "{{ route('party.update_email', '__id__') }}".replace('__id__', buyerId),
            data: {
                _token: "{{ csrf_token() }}",
                user_id: USER_ID,
                company_id: COMPANY_ID,
                token: API_TOKEN,
                email: buyerEmail
            },
            success: function (response) {
                loaderhide();
                if (response.status === 200) {
                    $('#emailModal').modal('hide');
                    Toast.fire({ icon: 'success', title: 'Email updated successfully' });
                    // Reload data and trigger send mail again
                    table.ajax.reload(function() {
                        $('#sendMailBtn').click();
                    });
                } else {
                    Toast.fire({ icon: 'error', title: response.message || 'Failed to update email' });
                }
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    });

    /* ── Send Mail to Buyers ── */
    function sendMailToBuyers(buyers) {
        loadershow();
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.prompt_report_send_mail') }}",
            data: {
                _token: "{{ csrf_token() }}",
                buyers: buyers,
                paymentStatus: $('#filter_payment_status').val(),
                company: $('#filter_company').val(),
                creditDays: $('#filter_credit_days').val(),
                message: 'Dear Customer,\n\nThis is a reminder for pending payments. Please find the invoice details below.\n\nPlease arrange for payment at your earliest convenience.\n\nThank you.'
            },
            success: function (response) {
                loaderhide();
                if (response.status == 200) {
                    Toast.fire({ icon: 'success', title: response.message });
                } else {
                    Toast.fire({ icon: 'error', title: response.message || 'Failed to send mail' });
                }
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    }

    initialize();
    loaderhide();
});
</script>
@endpush
