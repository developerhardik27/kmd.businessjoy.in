@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Pending Invoice Report
@endsection
@section('title')
    Pending Invoice Report
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
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Company</label>
            <select name="filter_company" class="f-ctrl select2" id="filter_company">
                <option value="">Select Company</option>
            </select>
        </div>
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Buyer</label>
            <select name="filter_buyer" class="f-ctrl select2" id="filter_buyer">
                <option value="">Select Buyer</option>
            </select>
        </div>
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Order Date From</label>
            <input type="date" class="f-ctrl" id="filter_order_date_from" name="filter_order_date_from">
        </div>
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Order Date To</label>
            <input type="date" class="f-ctrl" id="filter_order_date_to" name="filter_order_date_to">
        </div>
        
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Sample Date From</label>
            <input type="date" class="f-ctrl" id="filter_sample_date_from" name="filter_sample_date_from">
        </div>
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Sample Date To</label>
            <input type="date" class="f-ctrl" id="filter_sample_date_to" name="filter_sample_date_to">
        </div>
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Invoice Status</label>
            <select name="filter_invoice_status" class="f-ctrl select2" id="filter_invoice_status">
                <option value="Pending" selected>Pending</option>
                <option value="Half Invoice">Half Invoice</option>
                <option value="Invoices Created">Invoices Created</option>
            </select>
        </div>
     <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <button type="button" class="btn btn-secondary" id="clearBtn" style="margin-top:20px; width:100%; display: none;">Clear</button>
        </div>
    </div>
    <div class="form-group">
        <div class="form-row">
            <div class="col-sm-12" id="buttonContainer1" style="text-align:center">
                <button type="button" class="btn btn-secondary clearbtn" style="width: 120px;">Clear</button>
                <button type="button" class="btn btn-primary" id="generateBtn" style="width: 120px;">Generate</button>
            </div>
            <div class="col-sm-12" id="buttonContainer2">
                <button type="button" class="btn btn-info mt-2" id="sendMailBtn" style="display: none;">Send Mail <span id="selectedCount" style="display: none;">(0)</span></button>
                <button type="button" class="btn btn-primary mt-2" id="pdfBtn" style="display: none; float: right;">Generate PDF</button>
                <button type="button" class="btn btn-success mt-2" id="excelBtn" style="display: none; float: right;">Generate Excel</button>
            </div>
        </div>
    </div>
</form>

<!-- Email Input Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Email for Company: <span id="modalCompanyName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    <input type="hidden" id="modalCompanyId" name="modalCompanyId">
                    <div class="form-group">
                        <label for="companyEmail">Company Email</label>
                        <input type="email" class="form-control" id="companyEmail" name="companyEmail" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEmailBtn">Save & Send Mail</button>
            </div>
        </div>
    </div>
</div>

<div id="reportTableContainer" style="margin-top: 20px; display: none;">
    <div class="card" style="border: 1px solid var(--c-border); border-radius: var(--radius); background: var(--c-white);">
        <div class="card-body" style="padding: 15px;">
            <table id="reportTable" class="table table-bordered table-striped" style="width: 100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAllRows"></th>
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
                        <th>Rate</th>
                        <th>Final Amount</th>
                        <th>Credit Days</th>
                    </tr>
                </thead>
            </table>
            <table id="totals_table" class="table table-bordered table-striped mt-3" style="width: 100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th>Total Records</th>
                        <th>Total Net Kg</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="total_records">-</td>
                        <td id="total_net_kg">-</td>
                        <td id="total_amount">-</td>
                    </tr>
                </tbody>
            </table>
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
                url: "{{ route('order.pendingInvoiceReportData') }}",
                data: function (d) {
                    d.token = API_TOKEN;
                    d.user_id = USER_ID;
                    d.company_id = COMPANY_ID;
                    d.filter_order_date_from = $('#filter_order_date_from').val();
                    d.filter_order_date_to = $('#filter_order_date_to').val();
                    d.filter_invoice_status = $('#filter_invoice_status').val();
                    d.filter_company = $('#filter_company').val();
                    d.filter_buyer = $('#filter_buyer').val();
                    d.filter_sample_date_from = $('#filter_sample_date_from').val();
                    d.filter_sample_date_to = $('#filter_sample_date_to').val();
                },

                dataSrc: function (json) {

                    if (json.message) {
                        Toast.fire({ icon: 'error', title: json.message || 'Something went wrong!' });
                    }
                    global_response = json;

                    // Update totals
                    if (json.totals) {
                        $('#total_records').text(json.totals.total_records || 0);
                        $('#total_net_kg').text(json.totals.total_net_kg || 0);
                        $('#total_amount').text(parseFloat(json.totals.total_amount || 0).toFixed(2));
                    }

                    return json.data;
                },
                complete: () => {
                    loaderhide();
                    attachTableDrawHandler();
                },
                error: xhr => {
                    global_response = '';
                    console.log(xhr.responseText);
                    Toast.fire({ icon: 'error', title: 'Error loading data' });
                }

            },

            order: [[1, 'desc']],
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    defaultContent: '',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="row-checkbox" data-row-id="${row.id}">`;
                    }
                },
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
                { data: 'invoice_status', name: 'invoice_status', orderable: true,  searchable: true,  defaultContent: '-' },
                {
                    data: 'sample_status',
                    name: 'sample_status',
                    orderable: true,
                    searchable: true,
                    defaultContent: '-',
                    render: (data, type, row) => {
                        if (!data) return '-';
                        let orderId = row.id;
                        return `<button class="sample-status-btn btn btn-sm" data-order-id="${orderId}">
                            ${data}
                        </button>`;
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
                        return `${data}`;
                    }
                },
                { data: 'expected_dispatch_date',  name: 'expected_dispatch_date',  orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'net_kg',     name: 'net_kg',     orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'rate',   name: 'rate',   orderable: true,  searchable: true,  defaultContent: '-' , render: (data) => {
                        if (!data) return '-';
                        let s = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `<span data-toggle="tooltip" data-original-title="${data}">${s}</span>`;
                    },},
                { data: 'amount',   name: 'amount',   orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'credit_days',    name: 'credit_days',    orderable: true,  searchable: true,  defaultContent: '-' },
            ],
            pagingType: 'full_numbers',
            drawCallback: function () {
                // Restore checkbox state for selected rows
                let table = $('#reportTable').DataTable();
                table.rows().every(function() {
                    let row = this.data();
                    if (row && selectedRowIds.has(row.id)) {
                        let checkbox = $(this.node()).find('.row-checkbox');
                        checkbox.prop('checked', true);
                    }
                });

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

    /* ── Initialize filters ── */
    async function initialize() {
        try {
            const [companyRes, buyerRes] = await Promise.all([
                fetchData("{{ route('companymaster.index') }}"),
                fetchData("{{ route('buyer.index') }}")
            ]);

            console.log('Company response:', companyRes);
            console.log('Buyer response:', buyerRes);

            // Company
            if (companyRes.status == 200 && companyRes.data && companyRes.data.length) {
                companyRes.data.forEach(v => $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`));
            }
            initSelect2('#filter_company', 'All');

            // Buyer
            if (buyerRes.status == 200 && buyerRes.data && buyerRes.data.length) {
                buyerRes.data.forEach(v => $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`));
            }
            initSelect2('#filter_buyer', 'All');

            // Initialize Select2 for other fields
            initSelect2('#filter_invoice_status', 'Select Status');

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

        } catch (e) {
            console.error('Initialization error:', e);
        }
    }

    /* ── Fetch Data Helper ── */
    function fetchData(url) {
        return $.ajax({
            type: 'GET',
            url: url,
            data: { token: API_TOKEN, user_id: USER_ID, company_id: COMPANY_ID }
        });
    }

    /* ── Initialize Select2 ── */
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

    /* ── Reload DataTable on filter change ── */
    $('#filter_order_date_from, #filter_order_date_to, #filter_invoice_status, #filter_company, #filter_buyer, #filter_sample_date_from, #filter_sample_date_to').on('change', function() {
        if (table) {
            table.ajax.reload(function() {
                // After reload, clear selections that are no longer visible
                let visibleRowIds = new Set();
                table.rows({ search: 'applied' }).every(function() {
                    let row = this.data();
                    if (row) {
                        visibleRowIds.add(row.id);
                    }
                });

                // Keep only selected rows that are still visible
                let newSelectedRowIds = new Set();
                selectedRowIds.forEach(id => {
                    if (visibleRowIds.has(id)) {
                        newSelectedRowIds.add(id);
                    }
                });
                selectedRowIds = newSelectedRowIds;

                // Update checkboxes and count
                table.rows({ search: 'applied' }).every(function() {
                    let row = this.data();
                    let checkbox = $(this.node()).find('.row-checkbox');
                    if (row && selectedRowIds.has(row.id)) {
                        checkbox.prop('checked', true);
                    } else {
                        checkbox.prop('checked', false);
                    }
                });
                $('#selectAllRows').prop('checked', false);
                updateSelectedCount();
            });
        }
    });

    $('#generateBtn').on('click', function() {
        $('#reportTableContainer').show();
        $('.btn2').removeClass('col-sm-3');
        $('.btn2').addClass('col-sm-2');
        $('#generateBtn').hide();
        $('.clearbtn').hide();
        $('#sendMailBtn, #pdfBtn, #excelBtn, #clearBtn').show();
        if (table) {
            table.ajax.reload();
        } else {
            loaddata();
        }
    });

    // Store selected row IDs
    let selectedRowIds = new Set();

    // Handle row checkbox clicks
    $(document).on('change', '.row-checkbox', function() {
        let rowId = $(this).data('row-id');
        let table = $('#reportTable').DataTable();
        let rowData = table.row($(this).closest('tr')).data();
        
        if ($(this).is(':checked')) {
            // Check if row has valid company name
            let companyNames = rowData.company_names ? rowData.company_names.trim() : '';
            console.log(companyNames);
            if (!companyNames || companyNames === '' || companyNames === '-') {
                $(this).prop('checked', false);
                Toast.fire({ icon: 'error', title: 'This row does not have a company' });
                return;
            }
            selectedRowIds.add(rowId);
        } else {
            selectedRowIds.delete(rowId);
        }
        updateSelectedCount();
    });

    // Handle select all checkbox (only selects visible rows)
    $('#selectAllRows').on('change', function() {
        let isChecked = $(this).is(':checked');
        let table = $('#reportTable').DataTable();
        let skippedRows = [];

        // Only process visible rows (respecting filters)
        table.rows({ search: 'applied' }).every(function() {
            let row = this.data();
            let rowId = row.id;
            let companyNames = row.company_names ? row.company_names.trim() : '';

            // Only select rows with valid company names
            if (companyNames && companyNames !== '' && companyNames !== '-') {
                if (isChecked) {
                    selectedRowIds.add(rowId);
                } else {
                    selectedRowIds.delete(rowId);
                }
            } else {
                // Always uncheck rows without valid company
                selectedRowIds.delete(rowId);
                if (isChecked) {
                    skippedRows.push(rowId);
                }
            }
        });

        // Update checkboxes for visible rows
        table.rows({ search: 'applied' }).every(function() {
            let row = this.data();
            let checkbox = $(this.node()).find('.row-checkbox');
            let companyNames = row.company_names ? row.company_names.trim() : '';

            if (companyNames && companyNames !== '' && companyNames !== '-') {
                checkbox.prop('checked', isChecked);
            } else {
                checkbox.prop('checked', false);
            }
        });

        updateSelectedCount();

        // Show message if any rows were skipped
        if (isChecked && skippedRows.length > 0) {
            let skippedIds = skippedRows.join(', ');
            Toast.fire({ icon: 'warning', title: 'Row(s) skipped: ' + skippedIds + ' - no company assigned' });
        }
    });

    // Update selected count display based on visible rows only
    function updateSelectedCount() {
        let table = $('#reportTable').DataTable();
        let visibleSelectedCount = 0;

        table.rows({ search: 'applied' }).every(function() {
            let row = this.data();
            if (row && selectedRowIds.has(row.id)) {
                visibleSelectedCount++;
            }
        });

        $('#selectedCount').text('(' + visibleSelectedCount + ')');
        if (visibleSelectedCount > 0) {
            $('#selectedCount').show();
        } else {
            $('#selectedCount').hide();
        }
    }

    // Restore checkbox states after table reload
    function attachTableDrawHandler() {
        if (table) {
            table.on('draw', function() {
                $('.row-checkbox').each(function() {
                    let rowId = $(this).data('row-id');
                    if (selectedRowIds.has(rowId)) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
                updateSelectedCount();
            });
        }
    }

    /* ── Generate Report ── */
    function generateReport(type) {
        let params = {
            _token: $('input[name="_token"]').val(),
            token: API_TOKEN,
            user_id: USER_ID,
            company_id: COMPANY_ID,
            filter_order_date_from: $('#filter_order_date_from').val(),
            filter_order_date_to: $('#filter_order_date_to').val(),
            filter_invoice_status: $('#filter_invoice_status').val(),
            filter_company: $('#filter_company').val(),
            filter_buyer: $('#filter_buyer').val(),
            filter_sample_date_from: $('#filter_sample_date_from').val(),
            filter_sample_date_to: $('#filter_sample_date_to').val(),
            type: type
        };

        let url = type === 'pdf'
            ? "{{ route('admin.pending_invoice_report_pdf') }}"
            : "{{ route('admin.pending_invoice_report_excel') }}";

        loadershow();

        // Create form and submit
        let form = $('<form>', {
            'method': 'POST',
            'action': url,
            'target': '_blank'
        });

        $.each(params, function(key, value) {
            if (value) {
                $('<input>', {
                    'type': 'hidden',
                    'name': key,
                    'value': value
                }).appendTo(form);
            }
        });

        form.appendTo('body').submit();
        loaderhide();
    }

    $('#pdfBtn').on('click', function () { generateReport('pdf'); });
    $('#excelBtn').on('click', function () { generateReport('excel'); });

    /* ── Send Mail Functionality ── */
    $('#sendMailBtn').on('click', function () {
        console.log('Selected Row IDs:', Array.from(selectedRowIds));

        let table = $('#reportTable').DataTable();

        // Get only visible selected rows (respecting current filters)
        let selectedRows = [];
        table.rows({ search: 'applied' }).every(function() {
            let row = this.data();
            if (row && selectedRowIds.has(row.id)) {
                selectedRows.push(row);
            }
        });

        console.log('Visible selected rows:', selectedRows.length);

        if (selectedRows.length === 0) {
            Toast.fire({ icon: 'error', title: 'Please select at least one visible row' });
            return;
        }

        // Group selected rows by company only
        let groupedData = {};
        selectedRows.forEach(row => {
            let companyNames = row.company_names ? row.company_names.split(',').map(name => name.trim()) : [];

            companyNames.forEach(companyName => {
                if (companyName && companyName !== '  -  ') {
                    let companyKey = companyName.toLowerCase().replace(/\s+/g, '_');

                    if (!groupedData[companyKey]) {
                        groupedData[companyKey] = {
                            companyName: companyName,
                            companyEmail: row.company_email,
                            rows: []
                        };
                    }
                    groupedData[companyKey].rows.push(row);
                }
            });
        });

        console.log('Grouped Data:', groupedData);

        // Get unique companies from grouped data
        let companies = {};
        Object.values(groupedData).forEach(group => {
            let companyKey = group.companyName.toLowerCase().replace(/\s+/g, '_');
            if (!companies[companyKey]) {
                companies[companyKey] = {
                    id: companyKey,
                    name: group.companyName,
                    email: group.companyEmail,
                    rows: group.rows
                };
            }
        });

        console.log('Companies Object:', companies);

        // Check if any company is missing email directly from grouped data
        let missingEmailCompanies = Object.values(companies).filter(c => !c.email || c.email.trim() === '');

        if (missingEmailCompanies.length > 0) {
            // Show modal for first company with missing email
            let company = missingEmailCompanies[0];
            $('#modalCompanyName').text(company.name);
            $('#modalCompanyId').val(company.id);
            $('#companyEmail').val('');
            $('#emailModal').modal('show');
        } else {
            // All companies have emails, proceed to send mail
            sendMailToCompanies(companies);
        }
    });

    /* ── Save Email and Send Mail ── */
    $('#saveEmailBtn').on('click', function () {
        let email = $('#companyEmail').val().trim();
        let companyId = $('#modalCompanyId').val();

        if (!email) {
            Toast.fire({ icon: 'error', title: 'Please enter an email address' });
            return;
        }

        loadershow();
        $.ajax({
            type: 'POST',
            url: "{{ route('companymaster.update', '__id__') }}".replace('__id__', companyId),
            data: {
                _token: $('input[name="_token"]').val(),
                email: email,
                token: API_TOKEN,
                user_id: USER_ID,
                company_id: COMPANY_ID
            },
            success: function(response) {
                loaderhide();
                if (response.status == 200) {
                    $('#emailModal').modal('hide');
                    Toast.fire({ icon: 'success', title: 'Email saved successfully' });
                    // Retry sending mail
                    $('#sendMailBtn').click();
                } else {
                    Toast.fire({ icon: 'error', title: response.message || 'Failed to save email' });
                }
            },
            error: function(xhr) {
                loaderhide();
                Toast.fire({ icon: 'error', title: 'Failed to save email' });
            }
        });
    });

    /* ── Send Mail to Companies ── */
    function sendMailToCompanies(companies) {
        const requestData = {
            _token: $('input[name="_token"]').val(),
            token: API_TOKEN,
            user_id: USER_ID,
            company_id: COMPANY_ID,
            companies: JSON.stringify(Object.values(companies))
        };

        loadershow();

        $.ajax({
            type: 'POST',
            url: "{{ route('admin.send_pending_invoice_mail') }}",
            data: requestData,
            success: function(response) {
                loaderhide();
                Toast.fire({ icon: 'success', title: response.message || 'Mail sent successfully' });

                // Clear all selections after successful mail send
                selectedRowIds.clear();
                let table = $('#reportTable').DataTable();
                table.rows().every(function() {
                    let checkbox = $(this.node()).find('.row-checkbox');
                    checkbox.prop('checked', false);
                });
                $('#selectAllRows').prop('checked', false);
                updateSelectedCount();
            },
            error: function(xhr) {
                loaderhide();
                Toast.fire({ icon: 'error', title: 'Failed to send mail' });
            }
        });
    }

    /* ── Clear filters ── */
    $('#clearBtn, .clearbtn').on('click', function () {
        $('#filter_order_date_from, #filter_order_date_to, #filter_sample_date_from, #filter_sample_date_to').val('');
        $('#filter_invoice_status').val('Pending').trigger('change');
        $('#filter_company').val(null).trigger('change');
        $('#filter_buyer').val(null).trigger('change');
        $('#filter_company').select2('val', '');
        $('#filter_buyer').select2('val', '');
        $('#reportTableContainer').hide();
        $('#generateBtn').show();
        $('#sendMailBtn, #pdfBtn, #excelBtn, #clearBtn').hide();
        $('.clearbtn').show();
        $('.btn2').addClass('col-sm-3');
        $('.btn2').removeClass('col-sm-2');
        selectedRowIds.clear();
        $('#selectAllRows').prop('checked', false);
        updateSelectedCount();
        if (table) {
            table.clear().draw();
        }
    });

    initialize();
    loaderhide();
});
</script>
@endpush
