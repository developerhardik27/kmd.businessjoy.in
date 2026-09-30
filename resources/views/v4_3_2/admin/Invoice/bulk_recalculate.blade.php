@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Bulk Invoice Recalculate
@endsection
@section('title')
    Bulk Invoice Recalculate
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
    --radius:      10px;
    --radius-sm:   7px;
    --shadow-sm:   0 1px 3px rgba(0,0,0,.07), 0 2px 8px rgba(0,0,0,.05);
    --font:        'Inter', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--font) !important; background: var(--c-bg) !important; }

.inv-section {
    background: var(--c-white); border: 1px solid var(--c-border);
    border-radius: var(--radius); box-shadow: var(--shadow-sm);
    margin-bottom: 18px; overflow: hidden;
}
.inv-section-head {
    padding: 14px 20px; display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid var(--c-border);
    background: linear-gradient(90deg, #f5f7ff 0%, var(--c-white) 100%);
}
.inv-section-head .ico {
    width: 32px; height: 32px; border-radius: 8px;
    background: var(--c-primary-s); color: var(--c-primary);
    display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0;
}
.inv-section-head h6 { margin: 0; font-size: 13px; font-weight: 700; color: var(--c-text); }
.inv-section-head .head-right { margin-left: auto; }
.inv-section-body { padding: 20px; }

.f-label {
    display: block; font-size: 11.5px; font-weight: 600; color: var(--c-muted);
    text-transform: uppercase; letter-spacing: .055em; margin-bottom: 5px;
}
.f-ctrl {
    width: 100%; background: var(--c-light); border: 1.5px solid var(--c-border);
    border-radius: var(--radius-sm); padding: 8px 12px; font-size: 13px; color: var(--c-text);
    font-family: var(--font); outline: none; transition: border .15s, box-shadow .15s, background .15s;
}
.f-ctrl:focus { border-color: var(--c-primary); background: #fff; box-shadow: 0 0 0 3px var(--c-primary-s); }
.f-ctrl:disabled { background: #f4f5f8; opacity: .8; cursor: not-allowed; }
.f-hint { font-size: 11px; color: var(--c-muted); margin-top: 4px; display: block; }

/* buttons */
.btn-f {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px;
    border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; border: none;
    cursor: pointer; font-family: var(--font); transition: background .15s, transform .1s, opacity .15s;
}
.btn-f:hover:not(:disabled) { transform: translateY(-1px); }
.btn-f:disabled { opacity: .5; cursor: not-allowed; }
.btn-f-primary { background: var(--c-primary); color: #fff; box-shadow: 0 2px 10px rgba(59,91,219,.25); }
.btn-f-primary:hover:not(:disabled) { background: var(--c-primary-h); }
.btn-f-success { background: var(--c-success); color: #fff; box-shadow: 0 2px 10px rgba(47,158,68,.25); }
.btn-f-success:hover:not(:disabled) { background: #267a37; }
.btn-f-danger  { background: var(--c-danger); color: #fff; }
.btn-f-danger:hover:not(:disabled) { background: #c92a2a; }
.btn-f-light   { background: #edf0f7; color: var(--c-muted); }
.btn-f-light:hover:not(:disabled) { background: #e2e6ef; }
.btn-f-sm { padding: 4px 10px; font-size: 11px; }
.btn-row { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

/* warning box */
.warn-box {
    background: var(--c-warn-s); border: 1px solid #ffe3a3; border-left: 4px solid var(--c-warn);
    border-radius: var(--radius-sm); padding: 12px 16px; font-size: 12.5px; color: #7a4a00; margin-bottom: 18px;
}
.warn-box ul { margin: 6px 0 0 18px; padding: 0; }

/* change details modal */
.modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); z-index: 1050; display: none;
    align-items: center; justify-content: center;
}
.modal-overlay.active { display: flex; }
.modal-overlay .modal-content {
    background: var(--c-white); border-radius: var(--radius); border: none;
    max-width: 760px; width: 92%; max-height: 85vh;
    display: flex; flex-direction: column; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.modal-overlay .modal-header {
    padding: 16px 20px; border-bottom: 1px solid var(--c-border);
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(90deg, #f5f7ff 0%, var(--c-white) 100%);
}
.modal-overlay .modal-header h6 { margin: 0; font-size: 14px; font-weight: 700; color: var(--c-text); }
.modal-close {
    background: none; border: none; font-size: 22px; color: var(--c-muted);
    cursor: pointer; padding: 2px 8px; border-radius: 4px; line-height: 1;
}
.modal-close:hover { background: var(--c-border); color: var(--c-text); }
.modal-overlay .modal-body { padding: 20px; overflow-y: auto; flex: 1; }
.modal-overlay .modal-footer {
    padding: 14px 20px; border-top: 1px solid var(--c-border);
    display: flex; justify-content: flex-end; gap: 10px;
}

.info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px; }
.info-card { background: var(--c-light); padding: 12px; border-radius: var(--radius-sm); }
.info-card .k { font-size: 10.5px; color: var(--c-muted); text-transform: uppercase; margin-bottom: 4px; }
.info-card .v { font-size: 14px; font-weight: 600; color: var(--c-text); }

.gt-box {
    background: var(--c-success-s); border: 1px solid var(--c-success); border-left: 4px solid var(--c-success);
    border-radius: var(--radius-sm); padding: 12px 16px; margin-bottom: 16px;
}
.gt-box .t { font-size: 11px; color: var(--c-muted); text-transform: uppercase; margin-bottom: 8px; }
.gt-box .row-flex { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.gt-box .lbl { font-size: 11px; color: var(--c-muted); }
.gt-box .val { font-size: 16px; font-weight: 700; color: var(--c-text); margin-left: 8px; }

.reason-box {
    background: #eef3ff; border: 1px solid #c9d6ff; border-left: 4px solid var(--c-primary);
    border-radius: var(--radius-sm); padding: 12px 16px; margin-bottom: 16px; font-size: 12.5px; color: var(--c-text);
}
.reason-box .t { font-size: 11px; color: var(--c-primary); text-transform: uppercase; font-weight: 700; margin-bottom: 6px; }
.reason-box ul { margin: 0; padding-left: 18px; }
.reason-box li { margin-bottom: 3px; }

.sec-title { font-size: 12px; font-weight: 600; color: var(--c-text); margin: 18px 0 10px; display: flex; align-items: center; gap: 8px; }

.change-details-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.change-details-table th {
    background: #f5f7ff; text-align: left; padding: 10px 12px;
    font-size: 11px; text-transform: uppercase; letter-spacing: .05em;
    color: var(--c-muted); border-bottom: 1px solid var(--c-border);
}
.change-details-table td { padding: 10px 12px; border-bottom: 1px solid #eef0f6; color: var(--c-text); vertical-align: top; }
.change-details-table tr:last-child td { border-bottom: none; }
.change-badge {
    display: inline-block; padding: 3px 8px; border-radius: 12px;
    font-size: 10px; font-weight: 700; text-transform: uppercase;
}
.change-badge.mng-col { background: var(--c-primary-s); color: var(--c-primary); }
.change-badge.invoice { background: var(--c-success-s); color: var(--c-success); }
.col-diff { font-size: 11px; color: var(--c-muted); margin-top: 3px; }
.disc-tag { display: inline-block; font-size: 10px; font-weight: 700; background: var(--c-warn-s); color: var(--c-warn); border-radius: 4px; padding: 1px 6px; margin-left: 6px; }

/* status + progress */
.status-pill {
    display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 700;
    padding: 4px 12px; border-radius: 20px; background: #edf0f7; color: var(--c-muted);
}
.status-pill.running { background: var(--c-primary-s); color: var(--c-primary); }
.status-pill.done    { background: var(--c-success-s); color: var(--c-success); }
.status-pill.stopped { background: var(--c-warn-s); color: var(--c-warn); }
.status-pill.error   { background: var(--c-danger-s); color: var(--c-danger); }

.prog-wrap { height: 10px; background: #e6eaf5; border-radius: 20px; overflow: hidden; margin: 14px 0 6px; }
.prog-bar { height: 100%; width: 0; background: var(--c-primary); border-radius: 20px; transition: width .3s; }
.prog-bar.running {
    width: 100%;
    background: repeating-linear-gradient(45deg, #3b5bdb, #3b5bdb 10px, #5c7cfa 10px, #5c7cfa 20px);
    background-size: 28px 28px; animation: stripes .8s linear infinite;
}
.prog-bar.done    { width: 100%; background: var(--c-success); }
.prog-bar.stopped { width: 100%; background: var(--c-warn); }
.prog-bar.error   { width: 100%; background: var(--c-danger); }
@keyframes stripes { from { background-position: 0 0; } to { background-position: 28px 0; } }
.prog-text { font-size: 12px; color: var(--c-muted); }

.stat-grid { display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 12px; margin-top: 14px; }
.stat {
    background: var(--c-light); border: 1px solid var(--c-border); border-radius: var(--radius-sm);
    padding: 12px 14px;
}
.stat .n { font-size: 22px; font-weight: 700; color: var(--c-text); line-height: 1.1; }
.stat .l { font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--c-muted); margin-top: 3px; }
.stat.ok .n   { color: var(--c-success); }
.stat.warn .n { color: var(--c-warn); }
.stat.bad .n  { color: var(--c-danger); }
.stat.pri .n  { color: var(--c-primary); }
.stat-grid-2 { grid-template-columns: repeat(4, minmax(0,1fr)); }

/* tabs + tables */
.nav-tabs { border-bottom: 1px solid var(--c-border); padding: 0 12px; background: #fafbff; }
.nav-tabs .nav-link { font-size: 12.5px; font-weight: 600; color: var(--c-muted); border: none; padding: 12px 16px; }
.nav-tabs .nav-link.active { color: var(--c-primary); border-bottom: 2px solid var(--c-primary); background: transparent; }
.tab-badge {
    font-size: 10.5px; font-weight: 700; background: #e6eaf5; color: var(--c-muted);
    border-radius: 20px; padding: 1px 8px; margin-left: 6px;
}
.res-scroll { max-height: 420px; overflow: auto; }
table.res { width: 100%; border-collapse: collapse; font-size: 12.5px; }
table.res th {
    position: sticky; top: 0; background: #f5f7ff; text-align: left; padding: 10px 14px;
    font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--c-muted);
    border-bottom: 1px solid var(--c-border);
}
table.res td { padding: 9px 14px; border-bottom: 1px solid #eef0f6; color: var(--c-text); vertical-align: top; }
table.res tr:hover td { background: #fafbff; }
.num-up { color: var(--c-success); font-weight: 700; }
.num-down { color: var(--c-danger); font-weight: 700; }
.reason-short { font-size: 11.5px; color: var(--c-muted); max-width: 320px; }
.empty-row td { text-align: center; color: var(--c-muted); padding: 30px; }

/* log */
.log-box {
    background: #10131f; color: #cfd6f6; border-radius: var(--radius-sm);
    padding: 12px 14px; font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 12px;
    max-height: 200px; overflow: auto; line-height: 1.6;
}
.log-box .ok   { color: #69db7c; }
.log-box .warn { color: #ffd43b; }
.log-box .err  { color: #ff8787; }
.log-box .time { color: #6b7394; margin-right: 8px; }

@media (max-width: 900px) { .stat-grid { grid-template-columns: repeat(3, minmax(0,1fr)); } }
@media (max-width: 600px) {
    .stat-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .inv-section-body { padding: 14px; }
    .btn-row .btn-f { flex: 1; justify-content: center; }
    .info-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('form-content')

{{-- ═══════════ Info ═══════════ --}}
<div class="warn-box">
    <strong><i class="ri-alert-line"></i> Read before running</strong>
    <ul>
        <li>This does the same as opening every invoice in <b>Edit</b> and pressing <b>Update Invoice</b> (formulas, amounts, discount, broker purchase, order totals, GST, roundoff, grand total, payment status).</li>
        <li><b>Dry Run never saves anything.</b> Data changes only after you press <b>Run &amp; Save</b>.</li>
        <li>Invoices that already have a <b>brokerage bill</b> are not processed.</li>
        <li><b>New:</b> Use "Invoice Range" to process only specific invoices (e.g., enter 55 in "From ID" and 60 in "To ID" to process invoices 55-60). This overrides the Start ID setting.</li>
        <li><b>Update Mode &rarr; Only where amount differs</b> (recommended): compares <code>mng_col.amount</code> and <code>broker_purchases.invoice_grand_total</code> with <b>Rate &times; Net Kg &minus; discount %</b>. Only invoices with a difference are updated; correct invoices are not touched.</li>
        <li>Click <b>View details</b> on a changed invoice to see <b>why</b> it changed (order discount, roundoff, formula, tax) with before/after values. Check big differences before saving.</li>
        <li>Take a <b>database backup</b> first. If it stops, use the <b>Resume ID</b> to continue.</li>
    </ul>
</div>

{{-- ═══════════ Settings ═══════════ --}}
<div class="inv-section">
    <div class="inv-section-head">
        <div class="ico"><i class="ri-settings-3-line"></i></div>
        <h6>Settings</h6>
    </div>
    <div class="inv-section-body">
        <div class="row">
            <div class="col-sm-3 mb-3">
                <label class="f-label">Update Mode</label>
                <select id="mode" class="f-ctrl">
                    <option value="mismatch" selected>Only where amount differs</option>
                    <option value="all">All invoices (full recalculate)</option>
                </select>
                <span class="f-hint">Differs = mng_col amount or broker invoice_grand_total &ne; Rate &times; Net Kg &minus; discount %.</span>
            </div>
            <div class="col-sm-3 mb-3">
                <label class="f-label">Batch Size</label>
                <select id="batch_size" class="f-ctrl">
                    <option value="25">25 invoices / request</option>
                    <option value="50" selected>50 invoices / request</option>
                    <option value="100">100 invoices / request</option>
                    <option value="200">200 invoices / request</option>
                </select>
                <span class="f-hint">Lower it if you get timeouts.</span>
            </div>
            <div class="col-sm-3 mb-3">
                <label class="f-label">Start / Resume From Invoice ID</label>
                <input type="number" min="0" id="start_id" class="f-ctrl" value="0">
                <span class="f-hint">0 = from the beginning. Updates automatically while running.</span>
            </div>
            <div class="col-sm-3 mb-3">
                <label class="f-label">Invoice Range (optional)</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="number" min="1" id="from_id" class="f-ctrl" placeholder="From ID">
                    <span style="color: var(--c-muted);">to</span>
                    <input type="number" min="1" id="to_id" class="f-ctrl" placeholder="To ID">
                </div>
                <span class="f-hint">Process only invoices in this range (e.g., 55 to 60). Overrides Start ID.</span>
            </div>
            <div class="col-sm-3 mb-3">
                <label class="f-label">Single Invoice ID (optional)</label>
                <input type="number" min="1" id="single_id" class="f-ctrl" placeholder="Leave empty for all invoices">
                <span class="f-hint">Use this to test on one invoice first.</span>
            </div>
        </div>

        <div class="btn-row mt-2">
            <button type="button" id="btnDry" class="btn-f btn-f-primary"><i class="ri-eye-line"></i> Dry Run (no save)</button>
            <button type="button" id="btnRun" class="btn-f btn-f-success"><i class="ri-play-circle-line"></i> Run &amp; Save</button>
            <button type="button" id="btnStop" class="btn-f btn-f-danger" disabled><i class="ri-stop-circle-line"></i> Stop</button>
            <button type="button" id="btnClear" class="btn-f btn-f-light"><i class="ri-refresh-line"></i> Clear Results</button>
            <button type="button" id="btnCsv" class="btn-f btn-f-light" disabled><i class="ri-download-2-line"></i> Download CSV</button>
        </div>
    </div>
</div>

{{-- ═══════════ Progress ═══════════ --}}
<div class="inv-section">
    <div class="inv-section-head">
        <div class="ico"><i class="ri-loader-4-line"></i></div>
        <h6>Progress</h6>
        <div class="head-right"><span id="statusPill" class="status-pill">Idle</span></div>
    </div>
    <div class="inv-section-body">
        <div class="prog-wrap"><div id="progBar" class="prog-bar"></div></div>
        <div class="prog-text" id="progText">Not started.</div>

        <div class="stat-grid">
            <div class="stat pri"><div class="n" id="stProcessed">0</div><div class="l">Processed</div></div>
            <div class="stat ok"><div class="n" id="stUpdated">0</div><div class="l">Updated</div></div>
            <div class="stat pri"><div class="n" id="stChanged">0</div><div class="l">Invoices changed</div></div>
            <div class="stat warn"><div class="n" id="stSkipped">0</div><div class="l">Skipped</div></div>
            <div class="stat bad"><div class="n" id="stFailed">0</div><div class="l">Failed</div></div>
        </div>

        <div class="stat-grid stat-grid-2">
            <div class="stat ok"><div class="n" id="stMatched">0</div><div class="l">Already correct (not touched)</div></div>
            <div class="stat warn"><div class="n" id="stMngDiff">0</div><div class="l">mng_col amount different</div></div>
            <div class="stat warn"><div class="n" id="stBrokerDiff">0</div><div class="l">broker invoice_grand_total different</div></div>
            <div class="stat ok"><div class="n" id="stSaved">0</div><div class="l">Saved &amp; verified in DB</div></div>
        </div>

        <div class="log-box mt-3" id="logBox"></div>
    </div>
</div>

{{-- ═══════════ Results ═══════════ --}}
<div class="inv-section">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tabChanged" role="tab">Changed<span class="tab-badge" id="bdChanged">0</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tabSkipped" role="tab">Skipped<span class="tab-badge" id="bdSkipped">0</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tabFailed" role="tab">Failed<span class="tab-badge" id="bdFailed">0</span></a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="tabChanged" role="tabpanel">
            <div class="res-scroll">
                <table class="res">
                    <thead><tr><th>Invoice ID</th><th>Invoice No</th><th>Old Grand Total</th><th>New Grand Total</th><th>Difference</th><th>Why</th><th>Details</th></tr></thead>
                    <tbody id="tbChanged"><tr class="empty-row"><td colspan="7">No results yet.</td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="tabSkipped" role="tabpanel">
            <div class="res-scroll">
                <table class="res">
                    <thead><tr><th>Invoice ID</th><th>Invoice No</th><th>Reason</th></tr></thead>
                    <tbody id="tbSkipped"><tr class="empty-row"><td colspan="3">No results yet.</td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="tabFailed" role="tabpanel">
            <div class="res-scroll">
                <table class="res">
                    <thead><tr><th>Invoice ID</th><th>Invoice No</th><th>Error</th></tr></thead>
                    <tbody id="tbFailed"><tr class="empty-row"><td colspan="3">No results yet.</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ Change Details Modal ═══════════ --}}
<div class="modal-overlay" id="changeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h6 id="modalTitle">Invoice Changes</h6>
            <button type="button" class="modal-close" onclick="closeChangeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <button type="button" class="btn-f btn-f-light" onclick="closeChangeModal()">Close</button>
        </div>
    </div>
</div>

@endsection

@push('ajax')
<script>
loaderhide();

const API_TOKEN  = "{{ session()->get('api_token') }}";
const COMPANY_ID = "{{ session()->get('company_id') }}";
const USER_ID    = "{{ session()->get('user_id') }}";
const BULK_URL   = "{{ route('invoice.bulkrecalculate') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });

let running       = false;
let stopRequested = false;
let startedAt     = null;
let timer         = null;
let lastIdShown   = 0;

let totals = { processed: 0, updated: 0, skipped: 0, failed: 0, matched: 0, mngDiff: 0, brokerDiff: 0, saved: 0 };
let changedList = [], skippedList = [], failedList = [];
let changeMap = {};
let lastRunDry = true;

const FIELD_LABELS = {
    total: 'Subtotal', sgst: 'SGST', cgst: 'CGST', igst: 'IGST',
    gst: 'GST', grand_total: 'Grand Total', status: 'Status'
};

/* ───────── helpers ───────── */
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const sleep = ms => new Promise(r => setTimeout(r, ms));
const money = n => (parseFloat(n) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const signed = n => (n >= 0 ? '+' : '') + money(n);

function ajaxPromise(method, url, data = {}) {
    return new Promise((res, rej) => ajaxRequest(method, url, data).done(res).fail(rej));
}

function log(msg, type = '') {
    const t = new Date().toLocaleTimeString();
    const $b = $('#logBox');
    $b.append(`<div class="${type}"><span class="time">[${t}]</span>${esc(msg)}</div>`);
    $b.scrollTop($b[0].scrollHeight);
}

function setStatus(state, label) {
    $('#statusPill').attr('class', 'status-pill ' + state).text(label);
    $('#progBar').attr('class', 'prog-bar ' + (['running','done','stopped','error'].includes(state) ? state : ''));
    if (state === 'idle') $('#progBar').css('width', '0');
}

function elapsed() {
    if (!startedAt) return '00:00';
    const s = Math.floor((Date.now() - startedAt) / 1000);
    return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
}

function renderStats() {
    $('#stProcessed').text(totals.processed);
    $('#stUpdated').text(totals.updated);
    $('#stChanged').text(changedList.length);
    $('#stSkipped').text(totals.skipped);
    $('#stFailed').text(totals.failed);
    $('#stMatched').text(totals.matched);
    $('#stMngDiff').text(totals.mngDiff);
    $('#stBrokerDiff').text(totals.brokerDiff);
    $('#stSaved').text(totals.saved);
    $('#bdChanged').text(changedList.length);
    $('#bdSkipped').text(skippedList.length);
    $('#bdFailed').text(failedList.length);
    $('#progText').text(`${lastRunDry ? 'DRY RUN' : 'LIVE'} · processed ${totals.processed} invoices · last ID ${lastIdShown} · elapsed ${elapsed()}`);
    $('#btnCsv').prop('disabled', !(changedList.length || skippedList.length || failedList.length));
}

function appendRows(list, $tb, render) {
    if (!list.length) return;
    $tb.find('.empty-row').remove();
    $tb.append(list.map(render).join(''));
}

function resetResults() {
    totals = { processed: 0, updated: 0, skipped: 0, failed: 0, matched: 0, mngDiff: 0, brokerDiff: 0, saved: 0 };
    changedList = []; skippedList = []; failedList = []; changeMap = {};
    $('#tbChanged').html('<tr class="empty-row"><td colspan="7">No results yet.</td></tr>');
    $('#tbSkipped').html('<tr class="empty-row"><td colspan="3">No results yet.</td></tr>');
    $('#tbFailed').html('<tr class="empty-row"><td colspan="3">No results yet.</td></tr>');
    $('#logBox').empty();
    setStatus('idle', 'Idle');
    $('#progText').text('Not started.');
    renderStats();
    closeChangeModal();
}

function setRunningUi(on) {
    running = on;
    $('#btnDry, #btnRun, #btnClear').prop('disabled', on);
    $('#btnStop').prop('disabled', !on);
    $('#mode, #batch_size, #start_id, #from_id, #to_id, #single_id').prop('disabled', on);
}

/* one API call, retried up to 3 times (safe: recalculation is repeatable) */
async function callBatch(lastId, dry, single, batch, mode, fromId, toId) {
    const payload = {
        token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID,
        last_id: lastId, batch_size: batch, dry_run: dry ? 1 : 0, mode: mode
    };
    if (single) payload.invoice_id = single;
    if (fromId && toId) {
        payload.from_id = fromId;
        payload.to_id = toId;
    }

    log('Sending request: dry_run=' + payload.dry_run + ' (dry=' + dry + ')' + (fromId && toId ? ', range=' + fromId + '-' + toId : ''), 'ok');

    let lastErr;
    for (let i = 1; i <= 3; i++) {
        try {
            const response = await ajaxPromise('POST', BULK_URL, payload);
            log('Response received: dry_run=' + response.dry_run + ', saved_verified=' + (response.saved_verified || 0), 'ok');
            return response;
        } catch (e) {
            lastErr = e;
            if (i < 3) {
                log(`Request failed (attempt ${i}/3), retrying…`, 'warn');
                await sleep(2000 * i);
            }
        }
    }
    throw lastErr;
}

/* one row of the "Changed" table */
function changedRow(c) {
    const diff = parseFloat(c.new_grand) - parseFloat(c.old_grand);
    const firstReason = (c.reasons && c.reasons.length) ? c.reasons[0] : '';
    const more = (c.reasons && c.reasons.length > 1) ? ` (+${c.reasons.length - 1} more)` : '';
    return `<tr>
        <td>${esc(c.id)}</td>
        <td>${esc(c.inv_no)}</td>
        <td>${money(c.old_grand)}</td>
        <td>${money(c.new_grand)}</td>
        <td class="${diff >= 0 ? 'num-up' : 'num-down'}">${signed(diff)}</td>
        <td class="reason-short">${esc(firstReason)}${more}</td>
        <td><button type="button" class="btn-f btn-f-light btn-f-sm" onclick="showChangeDetails(${Number(c.id)})">View details</button></td>
    </tr>`;
}

/* ───────── main loop ───────── */
async function startRun(dry) {
    if (running) return;

    resetResults();
    lastRunDry = dry;
    stopRequested = false;
    startedAt = Date.now();
    timer = setInterval(renderStats, 1000);

    const batch  = parseInt($('#batch_size').val()) || 50;
    const mode   = $('#mode').val() || 'mismatch';
    const single = ($('#single_id').val() || '').trim();
    const fromId = parseInt($('#from_id').val()) || 0;
    const toId   = parseInt($('#to_id').val()) || 0;
    let last     = parseInt($('#start_id').val()) || 0;

    setRunningUi(true);
    setStatus('running', dry ? 'Dry run…' : 'Running…');

    let runInfo = 'mode ' + mode + ' · batch ' + batch;
    if (single) {
        runInfo += ' · single invoice ' + single;
    } else if (fromId && toId) {
        runInfo += ' · range ' + fromId + ' to ' + toId;
    } else {
        runInfo += ' · from ID ' + last;
    }

    log(`${dry ? 'DRY RUN (nothing is saved)' : 'LIVE RUN'} started · ${runInfo}`);

    let finalState = 'done', finalLabel = 'Completed';

    try {
        while (true) {
            if (stopRequested) { finalState = 'stopped'; finalLabel = 'Stopped'; log(`Stopped by user. Resume from ID ${last}.`, 'warn'); break; }

            const r = await callBatch(last, dry, single, batch, mode, fromId, toId);

            if (r.status != 200) {
                finalState = 'error'; finalLabel = 'Error';
                log(r.message || 'Server returned an error.', 'err');
                Toast.fire({ icon: 'error', title: r.message || 'Server error' });
                break;
            }

            if (!dry && r.dry_run) {
                finalState = 'error'; finalLabel = 'Error';
                log('Server treated this request as DRY RUN, nothing was saved. Check that dry_run=0 reaches the server.', 'err');
                Toast.fire({ icon: 'error', title: 'Server ran in DRY RUN mode' });
                break;
            }
            (r.warnings || []).forEach(w => log('Warning: ' + w, 'warn'));

            totals.processed += r.processed || 0;
            totals.updated   += r.updated   || 0;
            totals.skipped   += (r.skipped || []).length;
            totals.failed    += (r.failed  || []).length;
            totals.matched   += r.matched || 0;
            totals.mngDiff   += r.mismatch_mng_col || 0;
            totals.brokerDiff += r.mismatch_broker || 0;
            totals.saved     += r.saved_verified || 0;

            const ch = r.changed || [], sk = r.skipped || [], fl = r.failed || [];
            ch.forEach(c => { changeMap[c.id] = c; });
            changedList.push(...ch); skippedList.push(...sk); failedList.push(...fl);

            appendRows(ch, $('#tbChanged'), changedRow);
            appendRows(sk, $('#tbSkipped'), s => `<tr><td>${esc(s.id)}</td><td>${esc(s.inv_no)}</td><td>${esc(s.reason)}</td></tr>`);
            appendRows(fl, $('#tbFailed'),  f => `<tr><td>${esc(f.id)}</td><td>${esc(f.inv_no)}</td><td>${esc(f.error)}</td></tr>`);

            lastIdShown = r.next_last_id;
            if (!dry) $('#start_id').val(r.next_last_id); 
            renderStats();

            log(`Batch done → ${r.processed} processed, ${r.updated} ok, ${r.matched || 0} already correct, ${r.saved_verified || 0} saved & verified, ${ch.length} changed, ${sk.length} skipped, ${fl.length} failed (up to ID ${r.next_last_id})`,
                fl.length ? 'err' : (sk.length ? 'warn' : 'ok'));

            if (single || r.done) { log('All invoices finished.', 'ok'); break; }
            last = r.next_last_id;
        }
    } catch (e) {
        finalState = 'error'; finalLabel = 'Failed';
        log(`Request failed after 3 attempts. You can resume from ID ${last}.`, 'err');
        if (typeof handleAjaxError === 'function') handleAjaxError(e);
    }
    if (dry || finalState === 'done') $('#start_id').val(0);
    clearInterval(timer);
    setRunningUi(false);
    setStatus(finalState, finalLabel);
    renderStats();

    if (finalState === 'done') {
        Toast.fire({
            icon: 'success',
            title: dry ? 'Dry run finished — nothing was saved' : `Done — ${totals.saved} invoice(s) saved & verified in DB`
        });
    }
}

/* ───────── CSV ───────── */
function downloadCsv() {
    const q = v => `"${String(v ?? '').replace(/"/g, '""')}"`;
    const lines = [];
    lines.push(['TYPE', 'INVOICE_ID', 'INVOICE_NO', 'OLD_GRAND', 'NEW_GRAND', 'DIFFERENCE', 'MESSAGE'].map(q).join(','));
    changedList.forEach(c => lines.push(['CHANGED', c.id, c.inv_no, c.old_grand, c.new_grand,
        (parseFloat(c.new_grand) - parseFloat(c.old_grand)).toFixed(2), (c.reasons || []).join(' | ')].map(q).join(',')));
    skippedList.forEach(s => lines.push(['SKIPPED', s.id, s.inv_no, '', '', '', s.reason].map(q).join(',')));
    failedList.forEach(f => lines.push(['FAILED', f.id, f.inv_no, '', '', '', f.error].map(q).join(',')));

    const blob = new Blob(['\ufeff' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `invoice_bulk_recalculate_${lastRunDry ? 'dryrun' : 'live'}_${Date.now()}.csv`;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
}

/* ───────── Change Details Modal ───────── */
function showChangeDetails(invoiceId) {
    const c = changeMap[invoiceId];
    if (!c) return;

    const diff = parseFloat(c.new_grand) - parseFloat(c.old_grand);
    let html = '';

    html += `<div class="info-grid">
        <div class="info-card"><div class="k">Invoice No</div><div class="v">${esc(c.inv_no)}</div></div>
        <div class="info-card"><div class="k">Invoice ID</div><div class="v">${esc(c.id)}</div></div>
    </div>`;

    html += `<div class="gt-box">
        <div class="t">Invoice grand total change</div>
        <div class="row-flex">
            <div><span class="lbl">Before:</span><span class="val">${money(c.old_grand)}</span></div>
            <div style="font-size:20px;color:var(--c-muted)">→</div>
            <div><span class="lbl">After:</span><span class="val">${money(c.new_grand)}</span></div>
            <div style="margin-left:auto"><span class="lbl">Difference:</span>
                <span class="val" style="color:${diff >= 0 ? 'var(--c-success)' : 'var(--c-danger)'}">${signed(diff)}</span></div>
        </div>
    </div>`;
    if (c.lines_total) {
        html += `<div class="reason-box"><div class="t">Line check</div>
            ${c.lines_total - c.lines_untouched} line(s) changed, ${c.lines_untouched} line(s) were already correct and not touched.</div>`;
    }
    if (c.reasons && c.reasons.length) {
        html += `<div class="reason-box"><div class="t">Why this invoice changes</div><ul>` +
            c.reasons.map(r => `<li>${esc(r)}</li>`).join('') + `</ul></div>`;
    }

    // invoices table fields
    const ic = c.invoice_changes || [];
    if (ic.length) {
        html += `<div class="sec-title"><span class="change-badge invoice">INVOICE</span><span>Invoice fields updated</span></div>
        <table class="change-details-table"><thead><tr><th>Field</th><th>Old</th><th>New</th><th>Difference</th></tr></thead><tbody>`;
        ic.forEach(f => {
            if (f.field === 'status') {
                html += `<tr><td>${FIELD_LABELS.status}</td><td>${esc(f.old)}</td><td>${esc(f.new)}</td><td>-</td></tr>`;
            } else {
                const d = f.new - f.old;
                html += `<tr><td>${esc(FIELD_LABELS[f.field] || f.field)}</td><td>${money(f.old)}</td><td>${money(f.new)}</td>
                         <td class="${d >= 0 ? 'num-up' : 'num-down'}">${signed(d)}</td></tr>`;
            }
        });
        html += `</tbody></table>`;
    }

    // mng_col lines
    const mc = c.mng_col_changes || [];
    if (mc.length) {
        html += `<div class="sec-title"><span class="change-badge mng-col">MNG_COL</span><span>Line item changes (${mc.length})</span></div>
        <table class="change-details-table"><thead><tr><th>Line ID</th><th>Old Amount</th><th>New Amount</th><th>Difference</th></tr></thead><tbody>`;
        mc.forEach(m => {
            const d = m.new_amount - m.old_amount;
            const amountChanged = Math.abs(d) > 1;
            let extra = '';
            if (amountChanged && m.discount_pct > 0) extra += `<span class="disc-tag">order discount ${esc(m.discount_pct)}%</span>`;
            if (!amountChanged) extra += `<span class="disc-tag">amount same, formula columns only</span>`;
            if (m.columns && m.columns.length) {
                extra += '<div class="col-diff">' + m.columns.map(k => `${esc(k.column)}: ${esc(k.old)} → ${esc(k.new)}`).join('<br>') + '</div>';
            }
            html += `<tr><td>${esc(m.mng_col_id)}${extra}</td><td>${money(m.old_amount)}</td><td>${money(m.new_amount)}</td>
                     <td class="${amountChanged ? (d >= 0 ? 'num-up' : 'num-down') : ''}">${amountChanged ? signed(d) : '-'}</td></tr>`;
        });
        html += `</tbody></table>`;
    } else {
        html += `<div style="margin-top:16px;padding:14px;background:var(--c-light);border-radius:var(--radius-sm);text-align:center;color:var(--c-muted);font-size:12px;">
            No line item (mng_col) changes. Only invoice level values change.</div>`;
    }

    // broker_purchases.invoice_grand_total
    const bc = c.broker_changes || [];
    if (bc.length) {
        html += `<div class="sec-title"><span class="change-badge invoice">BROKER PURCHASE</span><span>invoice_grand_total (${bc.length})</span></div>
        <table class="change-details-table"><thead><tr><th>Broker Purchase ID</th><th>Order Detail ID</th><th>Old</th><th>New</th><th>Difference</th></tr></thead><tbody>`;
        bc.forEach(b => {
            const oldV = (b.old === null || b.old === undefined) ? 0 : b.old;
            const d = b.new - oldV;
            html += `<tr><td>${esc(b.broker_purchase_id)}${b.created ? ' <span class="disc-tag">new row</span>' : ''}</td>
                     <td>${esc(b.order_detail_id)}</td>
                     <td>${(b.old === null || b.old === undefined) ? '-' : money(b.old)}</td>
                     <td>${money(b.new)}</td>
                     <td class="${d >= 0 ? 'num-up' : 'num-down'}">${signed(d)}</td></tr>`;
        });
        html += `</tbody></table>`;
    }

    $('#modalTitle').text('Invoice Changes · ' + c.inv_no);
    $('#modalBody').html(html);
    $('#changeModal').addClass('active');
}

function closeChangeModal() {
    $('#changeModal').removeClass('active');
}

/* ───────── events ───────── */
$(function () {
    renderStats();

    $('#btnDry').on('click', () => startRun(true));

    $('#btnRun').on('click', function () {
        showConfirmationDialog(
            'Update ALL invoices?',
            'This will SAVE the changes for the selected Update Mode. Have you taken a backup and checked the dry run details?',
            'Yes, run & save',
            'No, cancel',
            'warning',
            function () { startRun(false); }
        );
    });

    $('#btnStop').on('click', function () {
        stopRequested = true;
        $(this).prop('disabled', true);
        log('Stop requested — finishing current batch…', 'warn');
    });

    $('#btnClear').on('click', resetResults);
    $('#btnCsv').on('click', downloadCsv);

    // close modal: click outside or ESC
    $('#changeModal').on('click', function (e) { if (e.target === this) closeChangeModal(); });
    $(document).on('keydown', function (e) { if (e.key === 'Escape') closeChangeModal(); });

    window.addEventListener('beforeunload', function (e) {
        if (running) { e.preventDefault(); e.returnValue = ''; }
    });
});
</script>
@endpush