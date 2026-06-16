@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Turnover Report
@endsection
@section('title')
    Turnover Report
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
            <label class="f-label">Company*</label>
            <select name="filter_company" class="f-ctrl select2" id="filter_company" required>
                <option value="">Select Company</option>
            </select>
        </div>
        <div class="col-sm-3 btn2" style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Buyer*</label>
            <select name="filter_buyer" class="f-ctrl select2" id="filter_buyer" required>
                <option value="">Select Buyer</option>
            </select>
        </div>
        <div class="col-sm-3 " style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Order Date From</label>
            <input type="date" class="f-ctrl" id="filter_order_date_from" name="filter_order_date_from">
        </div>
        <div class="col-sm-3 " style="padding: 0 6px; margin-bottom: 12px;">
            <label class="f-label">Order Date To</label>
            <input type="date" class="f-ctrl" id="filter_order_date_to" name="filter_order_date_to">
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
                <button type="button" class="btn btn-primary mt-2" id="pdfBtn" style="display: none; float: right;">Generate PDF</button>
                <button type="button" class="btn btn-success mt-2" id="excelBtn" style="display: none; float: right;">Generate Excel</button>
            </div>
        </div>
    </div>
</form>

<div id="reportTableContainer" style="margin-top: 20px; display: none; ">
    <div class="card" style="border: 1px solid var(--c-border); border-radius: var(--radius); background: var(--c-white);">
        <div class="card-body" style="padding: 15px;">
            <table id="reportTable" class="table table-bordered table-striped" style="width: 100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Buyer Name</th>
                        <th>Total Net Kg</th>
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
    const API_TOKEN  = "{{ session()->get('api_token') }}";
    const USER_ID    = "{{ session()->get('user_id') }}";
    const COMPANY_ID = "{{ session()->get('company_id') }}";

    let table;
loaderhide();
    /* ─────────────────────────────────────────
     *  DATATABLE
     * ───────────────────────────────────────── */
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
                url: "{{ route('order.turnoverReportData') }}",
                data: function (d) {
                    d.token                  = API_TOKEN;
                    d.user_id                = USER_ID;
                    d.company_id             = COMPANY_ID;
                    d.filter_order_date_from = $('#filter_order_date_from').val();
                    d.filter_order_date_to   = $('#filter_order_date_to').val();
                    d.filter_company         = $('#filter_company').val();
                    d.filter_buyer           = $('#filter_buyer').val();
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
                { data: 'company_name', name: 'company_name', orderable: true, searchable: true, defaultContent: '-' },
                { data: 'buyer_name',   name: 'buyer_name',   orderable: true, searchable: true, defaultContent: '-' },
                { data: 'total_net_kg', name: 'total_net_kg', orderable: true, searchable: true, defaultContent: '-' },
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
                        let t     = $('#reportTable').DataTable();
                        let page  = parseInt($('#jumpToPage').val());
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

    /* ─────────────────────────────────────────
     *  SELECT2 INIT
     *
     *  templateResult fires for every <option>
     *  when the dropdown list opens.
     *  Returning null hides that item from the list.
     *  So the currently-selected value simply won't
     *  appear in the open dropdown.
     * ───────────────────────────────────────── */
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

    /* ─────────────────────────────────────────
     *  INITIALIZE — populate dropdowns, then table
     * ───────────────────────────────────────── */
    async function initialize() {
        try {
            const [companyRes, buyerRes] = await Promise.all([
                fetchData("{{ route('companymaster.index') }}"),
                fetchData("{{ route('buyer.index') }}")
            ]);

            // Populate Company dropdown
            if (companyRes.status == 200 && companyRes.data && companyRes.data.length) {
                companyRes.data.forEach(v =>
                    $('#filter_company').append(`<option value="${v.id}">${v.company_name}</option>`)
                );
            }
            initSelect2('#filter_company', 'All');

            // Populate Buyer dropdown
            if (buyerRes.status == 200 && buyerRes.data && buyerRes.data.length) {
                buyerRes.data.forEach(v =>
                    $('#filter_buyer').append(`<option value="${v.id}">${v.name}</option>`)
                );
            }
            initSelect2('#filter_buyer', 'All');


        } catch (e) {
            console.error('Initialization error:', e);
        }
    }

    /* ─────────────────────────────────────────
     *  FETCH HELPER
     * ───────────────────────────────────────── */
    function fetchData(url) {
        return $.ajax({
            type: 'GET',
            url: url,
            data: { token: API_TOKEN, user_id: USER_ID, company_id: COMPANY_ID }
        });
    }

    /* ─────────────────────────────────────────
     *  FILTER CHANGE HANDLERS
     * ───────────────────────────────────────── */
    $('#filter_company, #filter_buyer').on('change', function () {
        if (table) table.ajax.reload();
    });

    $('#filter_order_date_from, #filter_order_date_to').on('change', function () {
        if (table) table.ajax.reload();
    });

    $('#generateBtn').on('click', function() {
        $('#reportTableContainer').show();
        $('.btn2').removeClass('col-sm-3');
        $('.btn2').addClass('col-sm-2'); 
        $('#generateBtn').hide();
        $('.clearbtn').hide();
        $('#pdfBtn, #excelBtn, #clearBtn').show();
        if (table) {
            table.ajax.reload();
        } else {
            loaddata();
        }
    });

    /* ─────────────────────────────────────────
     *  GENERATE REPORT — PDF or Excel
     *  Creates a hidden form, submits to _blank,
     *  then removes itself from the DOM.
     * ───────────────────────────────────────── */
    function generateReport(type) {
        let params = {
            _token:                 $('input[name="_token"]').val(),
            token:                  API_TOKEN,
            user_id:                USER_ID,
            company_id:             COMPANY_ID,
            filter_order_date_from: $('#filter_order_date_from').val(),
            filter_order_date_to:   $('#filter_order_date_to').val(),
            filter_company:         $('#filter_company').val(),
            filter_buyer:           $('#filter_buyer').val(),
            type:                   type
        };

        let url = type === 'pdf'
            ? "{{ route('admin.turnover_report_pdf') }}"
            : "{{ route('admin.turnover_report_excel') }}";

        loadershow();

        let form = $('<form>', { method: 'POST', action: url, target: '_blank' });
        $.each(params, function (key, value) {
            if (value) {
                $('<input>', { type: 'hidden', name: key, value: value }).appendTo(form);
            }
        });
        form.appendTo('body').submit();
        form.remove();
        loaderhide();
    }

    $('#pdfBtn').on('click',   function () { generateReport('pdf');   });
    $('#excelBtn').on('click', function () { generateReport('excel'); });

    /* ─────────────────────────────────────────
     *  CLEAR FILTERS
     * ───────────────────────────────────────── */
    $('#clearBtn, .clearbtn').on('click', function () {
        $('#filter_order_date_from, #filter_order_date_to').val('');
        $('#filter_company').val(null).trigger('change');
        $('#filter_buyer').val(null).trigger('change');
        $('#filter_company').select2('val', '');
        $('#filter_buyer').select2('val', '');
        $('#reportTableContainer').hide();
        $('#generateBtn').show();
        $('#pdfBtn, #excelBtn, #clearBtn').hide();
        $('.clearbtn').show();
        $('.btn2').addClass('col-sm-3');
        $('.btn2').removeClass('col-sm-2'); 
        if (table) {
            table.clear().draw();
        }
    });

    /* ─────────────────────────────────────────
     *  BOOT
     * ───────────────────────────────────────── */
    initialize();
});
</script>
@endpush