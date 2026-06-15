@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Pending Sample Purchase Report
@endsection
@section('title')
    Pending Sample Purchase Report
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
        <div class="col-sm-3" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Company</label>
            <select name="filter_company" class="f-ctrl select2" id="filter_company">
                <option value="">Select Company</option>
            </select>
        </div>
        <div class="col-sm-3" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Buyer</label>
            <select name="filter_buyer" class="f-ctrl select2" id="filter_buyer">
                <option value="">Select Buyer</option>
            </select>
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Order Date From</label>
            <input type="date" class="f-ctrl" id="filter_order_date_from" name="filter_order_date_from">
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Order Date To</label>
            <input type="date" class="f-ctrl" id="filter_order_date_to" name="filter_order_date_to">
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Sample Status</label>
            <select name="filter_sample_status" class="f-ctrl select2" id="filter_sample_status">
                <option value="Pending" selected>Pending</option>
                <option value="Half Sample">Half Sample</option>
                <option value="Sample Created">Sample Created</option>
            </select>
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Sample Date From</label>
            <input type="date" class="f-ctrl" id="filter_sample_date_from" name="filter_sample_date_from">
        </div>
        <div class="col-sm-2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Sample Date To</label>
            <input type="date" class="f-ctrl" id="filter_sample_date_to" name="filter_sample_date_to">
        </div>
    </div>

    <div class="btn-group">
        <button type="button" class="btn btn-primary" id="pdfBtn">Generate PDF</button>
        <button type="button" class="btn btn-success" id="excelBtn">Generate Excel</button>
        <button type="button" class="btn btn-info" id="sendMailBtn">Send Mail</button>
        <button type="button" class="btn btn-secondary" id="clearBtn">Clear</button>
    </div>
</form>

<!-- Email Input Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Email for Company</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    <input type="hidden" id="main_company_id" name="main_company_id">
                    <div class="form-group">
                        <label for="company_email">Company Email</label>
                        <input type="email" class="form-control" id="company_email" name="company_email" required>
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

<div id="reportTableContainer" style="margin-top: 20px;">
    <div class="card" style="border: 1px solid var(--c-border); border-radius: var(--radius); background: var(--c-white);">
        <div class="card-body" style="padding: 15px;">
            <table id="reportTable" class="table table-bordered table-striped" style="width: 100%; font-size: 12px;">
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
                        <th>Rate</th>
                        <th>Final Amount</th>
                        <th>Credit Days</th>
                    </tr>
                </thead>
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
                url: "{{ route('order.pendingSamplePurchaseReportData') }}",
                data: function (d) {
                    d.token = API_TOKEN;
                    d.user_id = USER_ID;
                    d.company_id = COMPANY_ID;
                    d.filter_order_date_from = $('#filter_order_date_from').val();
                    d.filter_order_date_to = $('#filter_order_date_to').val();
                    d.filter_sample_status = $('#filter_sample_status').val();
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
                { data: 'totalNetKg',     name: 'totalNetKg',     orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'rate',   name: 'rate',   orderable: true,  searchable: true,  defaultContent: '-' , render: (data) => {
                        if (!data) return '-';
                        let s = data.length > 20 ? data.substring(0, 20) + '...' : data;
                        return `<span data-toggle="tooltip" data-original-title="${data}">${s}</span>`;
                    },},
                { data: 'final_amount',   name: 'final_amount',   orderable: true,  searchable: true,  defaultContent: '-' },
                { data: 'credit_days',    name: 'credit_days',    orderable: true,  searchable: true,  defaultContent: '-' },
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
            initSelect2('#filter_sample_status', 'Select Status');

            // Initialize DataTable
            loaddata();

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
    $('#filter_order_date_from, #filter_order_date_to, #filter_sample_status, #filter_company, #filter_buyer, #filter_sample_date_from, #filter_sample_date_to').on('change', function() {
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
            filter_order_date_from: $('#filter_order_date_from').val(),
            filter_order_date_to: $('#filter_order_date_to').val(),
            filter_sample_status: $('#filter_sample_status').val(),
            filter_company: $('#filter_company').val(),
            filter_buyer: $('#filter_buyer').val(),
            filter_sample_date_from: $('#filter_sample_date_from').val(),
            filter_sample_date_to: $('#filter_sample_date_to').val(),
            type: type
        };

        let url = type === 'pdf'
            ? "{{ route('admin.pending_sample_purchase_report_pdf') }}"
            : "{{ route('admin.pending_sample_purchase_report_excel') }}";

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
        if (!global_response || !global_response.data || global_response.data.length === 0) {
            Toast.fire({ icon: 'warning', title: 'No data available to send mail' });
            return;
        }

        const companies = {};
        let companyNames = new Set();

        // Extract company names from aggregated data
        global_response.data.forEach(row => {
            if (row.company_names) {
                const names = row.company_names.split(',').map(n => n.trim()).filter(n => n);
                names.forEach(name => companyNames.add(name));
            }
        });

        if (companyNames.size === 0) {
            Toast.fire({ icon: 'warning', title: 'No companies found in data' });
            return;
        }

        // Fetch company details for each company name
        const companyPromises = Array.from(companyNames).map(companyName => {
            return fetchData("{{ route('companymaster.index') }}")
                .then(response => {
                    const company = response.data.find(c => c.company_name === companyName);
                    if (company) {
                        companies[companyName] = {
                            id: company.id,
                            name: company.company_name,
                            email: company.email
                        };
                    }
                });
        });

        Promise.all(companyPromises).then(() => {
            const companiesWithEmail = [];
            const companiesWithoutEmail = [];

            Object.values(companies).forEach(company => {
                if (company.email && company.email.trim() !== '') {
                    companiesWithEmail.push(company);
                } else {
                    companiesWithoutEmail.push(company);
                }
            });

            if (companiesWithoutEmail.length > 0) {
                // Show modal for first company without email
                const company = companiesWithoutEmail[0];
                $('#main_company_id').val(company.id);
                $('#company_email').val('');
                $('#emailModal').modal('show');
            } else {
                // All companies have emails, proceed to send mail
                sendMailToCompanies(companiesWithEmail);
            }
        });
    });

    /* ── Save Email and Send Mail ── */
    $('#saveEmailBtn').on('click', function () {
        const companyId = $('#main_company_id').val();
        const email = $('#company_email').val();

        if (!email || email.trim() === '') {
            Toast.fire({ icon: 'warning', title: 'Please enter an email address' });
            return;
        }

        // Fetch full company data first
        fetchData("{{ route('companymaster.edit', ['id' => ':id']) }}".replace(':id', companyId))
            .then(response => {
                const companyData = response.data;
                companyData.email = email;

                // Update company using main update API
                return $.ajax({
                    type: 'PUT',
                    url: "{{ route('companymaster.update', ['id' => ':id']) }}".replace(':id', companyId),
                    data: companyData,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN
                    }
                });
            })
            .then(response => {
                $('#emailModal').modal('hide');
                Toast.fire({ icon: 'success', title: 'Email saved successfully' });

                // Reload and retry sending mail
                $('#sendMailBtn').click();
            })
            .catch(error => {
                console.error('Error saving email:', error);
                Toast.fire({ icon: 'error', title: 'Failed to save email' });
            });
    });

    /* ── Send Mail to Companies ── */
    function sendMailToCompanies(companies) {
        const requestData = {
            companies: companies,
            dateFrom: $('#filter_order_date_from').val(),
            dateTo: $('#filter_order_date_to').val(),
            sampleStatus: $('#filter_sample_status').val(),
            _token: $('input[name="_token"]').val()
        };

        loadershow();

        $.ajax({
            type: 'POST',
            url: "{{ route('admin.send_pending_sample_mail') }}",
            data: requestData,
            success: function(response) {
                loaderhide();
                Toast.fire({ icon: 'success', title: response.message || 'Mail sent successfully' });
            },
            error: function(xhr) {
                loaderhide();
                const response = xhr.responseJSON || {};
                Toast.fire({ icon: 'error', title: response.message || 'Failed to send mail' });
            }
        });
    }

    /* ── Clear filters ── */
    $('#clearBtn').on('click', function () {
        $('#filter_order_date_from, #filter_order_date_to, #filter_sample_date_from, #filter_sample_date_to').val('');
        $('#filter_sample_status').val('Pending').trigger('change');
        $('#filter_company').val(null).trigger('change');
        $('#filter_buyer').val(null).trigger('change');
        if (table) {
            table.ajax.reload();
        }
    });

    initialize();
    loaddata();
    loaderhide();
});
</script>
@endpush
