@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Order
@endsection
@section('title')
    Update Order
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

/* ── Compact top header bar (replaces both sections) ── */
.order-header-bar {
    background: var(--c-white);
    border: 1px solid var(--c-border);
    box-shadow: var(--shadow-sm);
    padding: 10px 16px;
    margin-bottom: 10px;
}
.order-header-bar .row { margin: 0 -6px; }
.order-header-bar .col-sm-2,
.order-header-bar .col-sm-3,
.order-header-bar .col-sm-4 {
    padding: 0 6px;
}
.order-header-bar .mb-0 { margin-bottom: 0 !important; }

/* ── Field labels & controls ── */
.f-label {
    display: block; font-size: 10.5px; font-weight: 600;
    color: var(--c-muted); text-transform: uppercase;
    letter-spacing: .05em; margin-bottom: 3px;
}
.f-label .req { color: var(--c-danger); margin-left: 2px; }
.f-ctrl {
    width: 100%; background: var(--c-light);
    border: 1.5px solid var(--c-border); border-radius: var(--radius-sm);
    padding: 5px 10px; font-size: 12.5px; color: var(--c-text);
    font-family: var(--font); transition: border .15s, box-shadow .15s;
    outline: none; appearance: none; height: 32px;
}
.f-ctrl:focus { border-color: var(--c-primary); background: #fff; box-shadow: 0 0 0 3px var(--c-primary-s); }
.f-ctrl[disabled],.f-ctrl:disabled { background: #f4f5f8 !important; cursor: default; color: var(--c-text); opacity: .85; }
.f-err { font-size: 10px; color: var(--c-danger); margin-top: 2px; display: block; }
.f-ctrl.field-error { border-color: var(--c-danger) !important; background: var(--c-danger-s) !important; }

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
.select2-results__option[aria-disabled="true"] { color: var(--c-danger) !important; }

/* ── Line items wrapper ── */
.li-wrap {
    background: var(--c-white); border: 1px solid var(--c-border);
    box-shadow: var(--shadow-sm);
    margin-bottom: 0px; overflow: hidden;
}

/* ── Totals ── */
.li-totals {
    border-top: 1.5px solid var(--c-border); background: #f8f9fd;
    padding: 10px 16px; display: flex; align-items: flex-start;
    justify-content: space-between; gap: 16px;
}
.li-totals-left { flex: 1; }
.li-totals-center { flex: 1; display: flex; justify-content: center; }
.totals-inner { max-width: 420px; min-width: 320px; }

/* .li-list { display: flex; flex-direction: column; gap: 8px; padding: 10px; } */
.li-topbar-left h6 { margin: 0; font-size: 12.5px; font-weight: 700; color: var(--c-text); }
.li-count-badge {
    font-size: 10px; font-weight: 700; padding: 2px 7px;
    background: var(--c-primary); color: #fff; border-radius: 20px;
}
.li-quickbar {
    padding: 8px 14px; display: flex; align-items: center; gap: 8px;
    background: var(--c-light); border-bottom: 1px solid var(--c-border);
}
.btn-add-row {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 14px; border-radius: var(--radius-sm);
    background: var(--c-primary); color: #fff; border: none;
    font-size: 12px; font-weight: 600; font-family: var(--font);
    cursor: pointer; white-space: nowrap;
    transition: background .15s, transform .1s;
    box-shadow: 0 2px 6px rgba(59,91,219,.2);
}
.btn-add-row:hover { background: var(--c-primary-h); transform: translateY(-1px); }

.li-list {  display: flex; flex-direction: column; gap: 8px; }
.li-empty { text-align: center; padding: 30px 20px; color: var(--c-muted); }
.li-empty-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: var(--c-primary-s); color: var(--c-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; margin: 0 auto 8px;
}
.li-empty p { font-size: 12.5px; margin: 0; }

/* ── Row card ── */
.order-row-card {
    border: 1.5px solid var(--c-border); 
    background: var(--c-white); overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
    animation: cardIn .18s ease both;
}
@keyframes cardIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
.order-row-card:hover { border-color: #b8c4f5; box-shadow: 0 2px 8px rgba(59,91,219,.08); }
.order-row-card.card-error { border-color: var(--c-danger) !important; }

.order-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 6px 12px; background: #f7f8fd; border-bottom: 1px solid var(--c-border);
}
.order-card-num { display: flex; align-items: center; gap: 6px; }
.order-card-num .num-badge {
    width: 20px; height: 20px; border-radius: 5px;
    background: var(--c-primary); color: #fff;
    font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
}
.order-card-num span.lbl { font-size: 11px; font-weight: 600; color: var(--c-muted); }
.btn-card-del {
    width: 24px; height: 24px; border-radius: 5px; border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; cursor: pointer;
    background: var(--c-danger-s); color: var(--c-danger);
    transition: background .13s, transform .1s;
}
.btn-card-del:hover { background: #ffd5d5; transform: scale(1.1); }

.order-card-body { padding: 10px 12px; }
.order-field-grid {
    display: grid;
    grid-template-columns: repeat(8, minmax(0, 1fr));
    gap: 8px 10px; align-items: end;
}
.order-field-grid .amount-cell .f-ctrl {
    font-weight: 700 !important; color: var(--c-primary) !important;
    background: #eef1fd !important; border-color: #c5cff5 !important;
}
.order-card-body .f-label { font-size: 10px; margin-bottom: 3px; }
.order-card-body .f-ctrl  { font-size: 12px; padding: 5px 8px; height: 30px; }

/* ── Totals ── */
.li-totals {
    border-top: 1.5px solid var(--c-border); background: #f8f9fd;
    padding: 10px 16px;
}
/* .totals-inner { max-width: 420px; margin-left: auto; } */
.t-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 5px 0; border-bottom: 1px dashed var(--c-border); gap: 10px;
}
.t-row:last-child { border-bottom: none; }
.t-row.grand { border-top: 1.5px solid var(--c-primary); border-bottom: none; margin-top: 5px; padding-top: 8px; }
.t-label { flex: 1; font-size: 12px; font-weight: 500; color: var(--c-muted); }
.t-row.grand .t-label { font-size: 13px; font-weight: 700; color: var(--c-text); }
.t-val { font-size: 12.5px; font-weight: 700; color: var(--c-text); min-width: 100px; text-align: right; }
.t-row.grand .t-val { font-size: 16px; color: var(--c-primary); }
.t-row.discount .t-val { color: var(--c-danger); }
.t-badge { font-size: 10px; font-weight: 700; background: var(--c-warn-s); color: var(--c-warn); border-radius: 4px; padding: 1px 5px; margin-left: 5px; }

/* ── Footer ── */
.inv-footer { display: flex; justify-content: flex-end; gap: 7px; flex-wrap: wrap; padding: 10px 16px; background: var(--c-white); border: 1px solid var(--c-border);  box-shadow: var(--shadow-sm); }
.btn-f {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 16px; border-radius: var(--radius-sm);
    font-size: 12.5px; font-weight: 600; border: none;
    cursor: pointer; font-family: var(--font); transition: background .15s, transform .1s;
}
.btn-f:hover { transform: translateY(-1px); }
.btn-f-primary { background: var(--c-primary); color: #fff; box-shadow: 0 2px 8px rgba(59,91,219,.22); }
.btn-f-primary:hover { background: var(--c-primary-h); }
.btn-f-reset  { background: var(--c-danger-s); color: var(--c-danger); }
.btn-f-reset:hover { background: #ffd5d5; }
.btn-f-cancel { background: #edf0f7; color: var(--c-muted); }
.btn-f-cancel:hover { background: #e2e6ef; }

/* ── Modals ── */
.modal-content { border-radius: var(--radius) !important; border: none !important; box-shadow: 0 20px 60px rgba(0,0,0,.15) !important; font-family: var(--font); }
.modal-header  { background: linear-gradient(90deg,#f0f3ff,#fff); border-bottom: 1px solid var(--c-border) !important; border-radius: var(--radius) var(--radius) 0 0 !important; padding: 12px 18px !important; }
.modal-title   { font-weight: 700 !important; font-size: 13.5px !important; color: var(--c-text) !important; }

/* ── Responsive ── */
@media (max-width: 1200px) {
    .order-field-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .order-field-grid .amount-cell { grid-column: unset; }
}
@media (max-width: 900px) {
    .order-field-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .order-header-bar .row { flex-wrap: wrap; }
    .order-header-bar [class*="col-sm"] {
        width: 100% !important; max-width: 100% !important;
        flex: 0 0 100% !important; margin-bottom: 8px !important;
    }
    .order-field-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px 8px; }
    .order-field-grid .amount-cell { grid-column: span 2; }
    .li-totals { flex-direction: column; align-items: stretch !important; gap: 12px; padding: 12px; }
    .li-totals-left { display: none; }
    .li-totals-center { justify-content: center; }
    .btn-add-row { width: 100%; justify-content: center; }
    .totals-inner { max-width: 100%; min-width: unset; width: 100%; }
    .inv-footer { justify-content: stretch; flex-wrap: wrap; }
    .btn-f { flex: 1 1 auto; justify-content: center; min-width: 100px; }
}
@media (max-width: 480px) {
    .order-header-bar [class*="col-sm"] {
        width: 50% !important; max-width: 50% !important; flex: 0 0 50% !important;
    }
    .order-header-bar [class*="col-sm"]:last-child {
        width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important;
    }
    .order-field-grid { grid-template-columns: 1fr; gap: 6px; }
    .order-field-grid .amount-cell { grid-column: unset; }
    .order-card-head { padding: 5px 10px; }
    .order-card-body { padding: 8px 10px; }
    .btn-f { flex: 0 0 100%; }
}
</style>
<link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endsection

@section('form-content')

<form id="orderupdateform">
    @csrf
    <input type="hidden" name="_method"    value="PUT">
    <input type="hidden" name="token"      value="{{ session('api_token') }}">
    <input type="hidden" name="user_id"    value="{{ session('user_id') }}">
    <input type="hidden" name="company_id" value="{{ session('company_id') }}">

    {{-- ── Single compact header bar: Party + Order Info merged ── --}}
    <div class="order-header-bar">
        <div class="row align-items-end" style="margin:0 -6px;">
            <div class="col-sm-3 mb-0" style="padding:0 6px;">
                <label class="f-label">Buyer Party </label>
                <select class="form-control" name="buyer_party" id="buyer_party">
                    <option value="" selected disabled>Select Buyer</option>
                </select>
                <span class="f-err" id="error-buyer_party"></span>
            </div>
            <div class="col-sm-3 mb-0" style="padding:0 6px;">
                <label class="f-label">Transport</label>
                <select class="form-control" name="transport" id="transport">
                    <option value="" selected disabled>Select Transport</option>
                </select>
                <span class="f-err" id="error-transport"></span>
            </div>
            <div class="col-sm-2 mb-0" style="padding:0 6px;">
                <label class="f-label">Credit Days <span class="req">*</span></label>
                <select class="f-ctrl" name="credit_days" id="credit_days">
                    <option value="" disabled selected>Select</option>
                    <option value="CD">CD</option>
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="45">45</option>
                    <option value="60">60</option>
                    <option value="90">90</option>
                </select>
                <span class="f-err" id="error-credit_days"></span>
            </div>
            <div class="col-sm-2 mb-0" style="padding:0 6px;">
                <label class="f-label">Discount (%)</label>
                <input type="number" step="0.01" class="f-ctrl" name="discount" id="discount" placeholder="0.00" value="0" min="0">
                <span class="f-err" id="error-discount"></span>
            </div>
            <div class="col-sm-2 mb-0" style="padding:0 6px;">
                <label class="f-label">Order Date <span class="req">*</span></label>
                <input type="date" class="f-ctrl" name="order_date" id="order_date" value="{{ date('Y-m-d') }}">
                <span class="f-err" id="error-order_date"></span>
            </div>
        </div>
    </div>

    {{-- ── Line Items ── --}}
    <div class="li-wrap">
    <div class="li-list" id="purchaseBody">
        <div id="li-empty" class="li-empty">
            <div class="li-empty-icon"><i class="ri-inbox-line"></i></div>
            <p><strong>No items yet.</strong> Click "Add New Row" to get started.</p>
        </div>
    </div>
    <div class="li-totals">
        <div class="li-totals-left"></div>
        <div class="li-totals-center">
            <button type="button" id="addRowBtn" class="btn-add-row">
                <i class="ri-add-circle-line"></i> Add New Row
            </button>
        </div>
        <div class="totals-inner">
            <div class="t-row">
                <span class="t-label">Total Net Kg</span>
                <span class="t-val" id="totalNetKg">0.00</span>
            </div>
            <div class="t-row">
                <span class="t-label">Total Amount</span>
                <span class="t-val" id="totalAmount">0.00</span>
            </div>
            <div class="t-row discount">
                <span class="t-label">Discount <span class="t-badge" id="discountBadge">0%</span></span>
                <span class="t-val" id="discountAmount">0.00</span>
            </div>
            <div class="t-row grand">
                <span class="t-label">Final Amount</span>
                <span class="t-val" id="finalAmount">0.00</span>
            </div>
        </div>
    </div>
</div>
    <div class="inv-section">
        <div class="inv-section-body">
            <div class="inv-footer">
                <button id="cancelbtn" type="button" class="btn-f btn-f-cancel"><i class="ri-close-line"></i> Cancel</button>
                <button type="reset"                 class="btn-f btn-f-reset"><i class="ri-refresh-line"></i> Reset</button>
                <button type="submit"                class="btn-f btn-f-success"><i class="ri-save-line"></i> Update Order</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('ajax')
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<script>
$(document).ready(function () {

    const EDIT_ID = @json($edit_id);
    let gardens = '', grades = '';
    let rowCount = 0;

    /* ════════════════════════════════════════════════════════
       CALCULATION ENGINE — same as add form, all 19 TC
    ════════════════════════════════════════════════════════ */

    // TC-16: safe parse — returns null for empty/non-numeric
    function safeFloat(val) {
        if (val === '' || val === null || val === undefined) return null;
        const n = parseFloat(val);
        return isNaN(n) ? null : n;
    }

    // TC-11 / TC-16: validate field
    function validateField($el) {
        const raw = $el.val().trim();
        if (raw === '' || raw === '0') { $el.removeClass('field-error'); return true; }
        const n = parseFloat(raw);
        if (isNaN(n)) {
            $el.addClass('field-error');
            Toast.fire({ icon: 'warning', title: `${$el.closest('div').find('.f-label').text().trim()} must be a number` });
            $el.val('0'); return false;
        }
        if (n < 0) {
            $el.addClass('field-error');
            Toast.fire({ icon: 'warning', title: `${$el.closest('div').find('.f-label').text().trim()} cannot be negative` });
            $el.val('0'); return false;
        }
        $el.removeClass('field-error');
        return true;
    }

   function recalcRow(row, changedField) {
        const $bags = row.find('.bags');
        const $kg   = row.find('.kg');
        const $net  = row.find('.net-kg');
        const $rate = row.find('.rate');
        const $amt  = row.find('.amount');

        // TC-16 validation on changed field
        if (!validateField(row.find('.' + changedField))) return;

        let bags = safeFloat($bags.val());
        let kg   = safeFloat($kg.val());
        let net  = safeFloat($net.val());
        const rate = safeFloat($rate.val()) || 0;

        // TC-13: all empty → nothing to calculate
        if (bags === null && kg === null && net === null) {
            $amt.val('0.00');
            calculateTotals();
            return;
        }

        // TC-12: only one field filled → no calculation yet
        const filledCount = [bags, kg, net].filter(v => v !== null && v > 0).length;
        if (filledCount < 2 && changedField !== 'rate') {
            // Still update amount if we have net + rate
            if (net !== null && net > 0 && rate > 0) {
                $amt.val((net * rate).toFixed(2));
            }
            calculateTotals();
            return;
        }

        // Normalise nulls to 0 for math
        bags = bags || 0;
        kg   = kg   || 0;
        net  = net  || 0;

        /* ── TC-11: negative → reset ── */
        if (bags < 0 || kg < 0 || net < 0) {
            $bags.val('0'); $kg.val('0'); $net.val('0'); $amt.val('0.00');
            calculateTotals();
            return;
        }

        /* ── LOGIC ── */
        if (changedField === 'bags') {
            // TC-01: Bags + Kg → Net   (bags=10, kg=50 → net=500)
            // TC-04: Change Bags       (bags→12, kg=50 → net=600)
            // TC-08: Bags=0, Kg=50 → Net=0
            // TC-09: Bags=2.5, Kg=40 → Net=100
            // TC-10: Large values
            if (kg > 0) {
                net = bags * kg;
            }
            // TC-02 partial: Bags + Net → Kg
            else if (net > 0 && bags > 0) {
                kg = net / bags;
            }
        }

        else if (changedField === 'kg') {
            // TC-01 variant: Bags + Kg → Net
            // TC-05: Change Kg (bags=10, kg→60 → net=600)
            if (bags > 0) {
                net = bags * kg;
            }
            // TC-03 partial: Kg + Net → Bags
            else if (net > 0 && kg > 0) {
                bags = net / kg;
            }

            // TC-17: Kg cleared → clear Net too
            if (kg === 0 && bags === 0) {
                net = 0;
            }
        }

        else if (changedField === 'net-kg') {
            // TC-17: Net cleared
            if (net === 0) {
                $amt.val('0.00');
                calculateTotals();
                return;
            }

            // lastTyped set on INPUT of bags/kg tells us what user manually typed
            const lastTyped = row.data('lastTyped') || '';
            if (lastTyped === 'bags' && bags > 0 && kg > 0) {
                // TC-02: User typed Bags → derive Kg
                // TC-06: Both filled, lastTyped=bags → Kg = net/bags
                kg = net / bags;
            }
            else if (lastTyped === 'kg' && kg > 0) {
                // TC-03: User typed Kg → derive Bags
                bags = net / kg;
            }
            else {
                // Fallback when no lastTyped (fresh row)
                if (bags > 0 && kg === 0)      kg   = net / bags;
                else if (kg > 0 && bags === 0) bags = net / kg;
                else if (bags > 0 && kg > 0)   kg   = net / bags; // bags anchor
            }
        }

        else if (changedField === 'rate') {
            // Rate change → just recalc amount, no bags/kg/net change
        }

        /* ── TC-11 / safety: no negative, NaN, Infinity ── */
        if (!isFinite(bags) || isNaN(bags) || bags < 0) bags = 0;
        if (!isFinite(kg)   || isNaN(kg)   || kg   < 0) kg   = 0;
        if (!isFinite(net)  || isNaN(net)  || net  < 0) net  = 0;

        /* ── TC-18: precision — use up to 4 decimal places ── */
        const fmtNum = n => n > 0 ? parseFloat(n.toFixed(4)) : '';

        // Update only non-active fields (TC-14: don't rewrite what user is typing)
        if (changedField !== 'bags')   $bags.val(fmtNum(bags));
        if (changedField !== 'kg')     $kg.val(fmtNum(kg));
        if (changedField !== 'net-kg') $net.val(fmtNum(net));

        /* ── Amount ── */
        const amount = (net > 0 && rate > 0) ? net * rate : 0;
        $amt.val(amount > 0 ? amount.toFixed(2) : '0.00');

        calculateTotals();
    }


    /* ════════════════════════════════════════════════════════
       TOTALS
    ════════════════════════════════════════════════════════ */
    function calculateTotals() {
        let totalNetKg = 0, totalAmount = 0;
        $('.net-kg').each(function () { totalNetKg += parseFloat($(this).val()) || 0; });
        $('.amount').each(function () { totalAmount += parseFloat($(this).val()) || 0; });
        const discountPct    = parseFloat($('#discount').val()) || 0;
        const discountAmount = (totalAmount * discountPct) / 100;
        const finalAmount    = totalAmount - discountAmount;
        $('#totalNetKg').text(totalNetKg.toFixed(2));
        $('#totalAmount').text(totalAmount.toFixed(2));
        $('#discountAmount').text(discountAmount.toFixed(2));
        $('#finalAmount').text(finalAmount.toFixed(2));
        $('#discountBadge').text(discountPct + '%');
    }

    /* ════════════════════════════════════════════════════════
       EVENT BINDINGS
    ════════════════════════════════════════════════════════ */

    // TC-14: clear zero on focus
    $(document).on('focus', '.bags, .kg, .net-kg, .rate, #discount', function () {
        if ($(this).val() == '0') $(this).val('');
    });

    // TC-15: restore 0 on blur if empty
    $(document).on('blur', '.bags, .kg, .net-kg, .rate, #discount', function () {
        const val = parseFloat($(this).val());
        if (isNaN(val) || $(this).val().trim() === '') $(this).val('0');
    });

    // Track lastTyped on INPUT of bags/kg ONLY
    // net-kg input must NEVER set lastTyped — this is the key fix for TC-02/03/06
    $(document).on('focusout', '.bags, .kg', function () {
        const row = $(this).closest('.order-row-card');
        if (!row.length) return;
        if ($(this).hasClass('bags'))                                   row.data('lastTyped', 'bags');
        else if ($(this).hasClass('kg') && !$(this).hasClass('net-kg')) row.data('lastTyped', 'kg');
    });

    // Main input handler — debounced 80ms for TC-15 stability
    let calcTimer = null;
    $(document).on('focusout', '.bags, .kg, .net-kg, .rate', function () {
        const row = $(this).closest('.order-row-card');
        if (!row.length) return;
        const changedField = ['bags','kg','net-kg'].find(c => $(this).hasClass(c)) || 'rate';
        clearTimeout(calcTimer);
        calcTimer = setTimeout(() => { recalcRow(row, changedField); }, 80);
    });

    // Discount change → totals only
    $('#discount').on('input', function () { calculateTotals(); });

    /* ════════════════════════════════════════════════════════
       ROW COUNT BADGE
    ════════════════════════════════════════════════════════ */
    function updateRowCount() {
        const n = $('#purchaseBody .order-row-card').length;
        $('#li-count').text(n);
        n === 0 ? $('#li-empty').show() : $('#li-empty').hide();
    }

    /* ════════════════════════════════════════════════════════
       BUILD A ROW CARD
    ════════════════════════════════════════════════════════ */
    function buildCard(rowId, detail) {
        const val = (key, fb) => (detail && detail[key] != null) ? detail[key] : (fb ?? '');
        return `
        <div class="order-row-card" id="row_${rowId}">
            <div class="order-card-head">
                <div class="order-card-num">
                    <span class="num-badge">${rowId}</span>
                    <span class="lbl">Line Item ${rowId}</span>
                </div>
                <button type="button" class="btn-card-del remove-row"
                    data-toggle="tooltip" data-original-title="Delete Row">
                    <i class="ri-delete-bin-2-line"></i>
                </button>
            </div>
            <div class="order-card-body">
                <div class="order-field-grid">
                    <input type="hidden" name="order_detail_id[]" id="order_detail_id_${rowId}" value="${val('id') || ''}">
                    <div>
                        <label class="f-label">Garden <span style="color:var(--c-danger)">*</span></label>
                        <select class="f-ctrl garden-select" name="garden_id[]" id="garden_${rowId}">${gardens}</select>
                        <span class="f-err row-f-err err-garden_id"></span>
                    </div>
                    <div>
                        <label class="f-label">Invoice / Lot No</label>
                        <input type="text" class="f-ctrl" name="invoice_no[]"
                            value="${val('invoice_no')}" placeholder="Invoice No">
                        <span class="f-err row-f-err err-invoice_no"></span>
                    </div>
                    <div>
                        <label class="f-label">Grade</label>
                        <select class="f-ctrl grade-select" name="grade[]" id="grade_${rowId}">${grades}</select>
                    </div>
                    <div>
                        <label class="f-label">Bags</label>
                        <input type="number" class="f-ctrl bags" name="bags[]"
                            value="${val('bags', 0)}" placeholder="0" min="0" step="0.01">
                        <span class="f-err row-f-err err-bags"></span>
                    </div>
                    <div>
                        <label class="f-label">Kg</label>
                        <input type="number" step="0.01" class="f-ctrl kg" name="kg[]"
                            value="${val('kg', 0)}" placeholder="0.00" min="0">
                        <span class="f-err row-f-err err-kg"></span>
                    </div>
                    <div>
                        <label class="f-label">Net Kg</label>
                        <input type="number" step="0.001" class="f-ctrl net-kg" name="net_kg[]"
                            value="${val('net_kg', 0)}" placeholder="0.00" min="0">
                    </div>
                    <div>
                        <label class="f-label">Rate / Kg <span style="color:var(--c-danger)">*</span></label>
                        <input type="number" step="0.01" class="f-ctrl rate" name="rate[]"
                            value="${val('rate', 0)}" placeholder="0.00" min="0">
                        <span class="f-err row-f-err err-rate"></span>
                    </div>
                    <div class="amount-cell">
                        <label class="f-label">Amount</label>
                        <input type="number" class="f-ctrl amount" name="amt[]"
                            value="${val('amount', 0)}" disabled placeholder="0.00">
                        <span class="f-err row-f-err err-amount"></span>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function addNewRow(detail) {
        rowCount++;

        // Auto-select last garden only for blank new rows
        let autoGarden = null;
        if (!detail) {
            const $last = $('.garden-select').filter(function () {
                return $(this).val() && $(this).val() !== 'add_new';
            }).last();
            if ($last.length) autoGarden = $last.val();
        }

        $('#li-empty').before(buildCard(rowCount, detail || null));

        // Pre-select existing data
        if (detail) {
            if (detail.garden_id) $(`#garden_${rowCount}`).val(detail.garden_id);
            if (detail.grade)     $(`#grade_${rowCount}`).val(detail.grade);
        }

        updateRowCount();
        $('[data-toggle="tooltip"]').tooltip('dispose').tooltip();

        $(`#garden_${rowCount}`).select2({ placeholder: 'Select Garden', width: '100%', allowClear: true });
        $(`#grade_${rowCount}`).select2({ placeholder: 'Select Grade',  width: '100%', allowClear: true });

        if (autoGarden) $(`#garden_${rowCount}`).val(autoGarden).trigger('change');
    }

    /* ADD ROW */
   $('#addRowBtn').on('click', function () {
    addNewRow(null);
});

    /* REMOVE ROW */
    $(document).on('click', '.remove-row', function () {
        const card = $(this).closest('.order-row-card');
        Swal.fire({
            title: 'Are you sure?', text: 'This row will be deleted!', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!', cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) { card.remove(); calculateTotals(); updateRowCount(); }
        });
    });

    /* ════════════════════════════════════════════════════════
       FETCH DROPDOWNS
    ════════════════════════════════════════════════════════ */
    async function fetchGardens() {
        try {
            const r = await ajaxRequest('GET', "{{ route('garden.index') }}", {
                user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}"
            });
            gardens = '<option value="" selected disabled>Select Garden</option>';
            if (r.data && r.data.length) r.data.forEach(g => { gardens += `<option value="${g.id}">${g.garden_name}</option>`; });
            $('.garden-select').select2({ placeholder: 'Select Garden', width: '100%', allowClear: true });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    async function fetchGrade() {
        try {
            const r = await ajaxRequest('GET', "{{ route('grade.index') }}", {
                user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}"
            });
            grades = '<option value="" selected disabled>Select Grade</option>';
            if (r.data && r.data.length) r.data.forEach(g => { grades += `<option value="${g.id}">${g.grade}</option>`; });
            $('.grade-select').select2({ placeholder: 'Select Grade', width: '100%', allowClear: true });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    async function fetchBuyers() {
        try {
            const r = await ajaxRequest('GET', "{{ route('buyer.index') }}", {
                user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}"
            });
            const $sel = $('#buyer_party');
            $sel.empty().append('<option value="" selected disabled>Select Buyer</option>');
            if (r.data && r.data.length) r.data.forEach(p => $sel.append(`<option value="${p.id}">${p.name}</option>`));
            $sel.select2({ placeholder: 'Select Buyer', width: '100%' });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    async function fetchTransports() {
        try {
            const r = await ajaxRequest('GET', "{{ route('transport.index') }}", {
                user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}"
            });
            const $sel = $('#transport');
            $sel.empty().append('<option value="" selected disabled>Select Transport</option>');
            if (r.data && r.data.length) r.data.forEach(p => $sel.append(`<option value="${p.id}">${p.name}</option>`));
            $sel.select2({ placeholder: 'Select Transport', width: '100%' });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    /* ════════════════════════════════════════════════════════
       LOAD EXISTING ORDER DATA
    ════════════════════════════════════════════════════════ */
    async function loaddata() {
        try {
            const url = "{{ route('order.edit', '__id__') }}".replace('__id__', EDIT_ID);
            const r   = await ajaxRequest('GET', url, {
                token: "{{ session()->get('api_token') }}", company_id: "{{ session()->get('company_id') }}", user_id: "{{ session()->get('user_id') }}"
            });

            if (r.status == 200) {
                const order         = r.orders.order;
                const order_details = r.orders.order_details;

                $('#buyer_party').val(order.buyer_party).trigger('change');
                $('#transport').val(order.transport).trigger('change');
                $('#credit_days').val(order.credit_days);
                $('#discount').val(order.discount ?? 0);
                $('#order_date').val(order.order_date);
                $('#credit_days').select2({ placeholder: 'Select Credit Days', allowClear: true, width: '100%' });

                order_details.forEach(detail => addNewRow(detail));
                console.log(order_details);
                calculateTotals();
                
            } else {
                Toast.fire({ icon: 'error', title: r.message });
            }
        } catch (e) { handleAjaxError(e); }
        finally { loaderhide(); }
    }
    $(document).on('focus', '.select2-selection', function () {
        $(this).closest('.select2-container').prev('select').select2('open');
    });
    /* ════════════════════════════════════════════════════════
       INIT
    ════════════════════════════════════════════════════════ */
    async function init() {
        loadershow();
        try {
            await Promise.all([ fetchGardens(), fetchGrade(), fetchBuyers(), fetchTransports() ]);
            await loaddata();
        } catch (e) { handleAjaxError(e); }
        finally { loaderhide(); }
    }
    init();

    /* ════════════════════════════════════════════════════════
       CANCEL
    ════════════════════════════════════════════════════════ */
    $('#cancelbtn').on('click', () => { loadershow(); window.location.href = "{{ route('admin.order') }}"; });

    /* ════════════════════════════════════════════════════════
       SUBMIT — UPDATE
    ════════════════════════════════════════════════════════ */
    $('#orderupdateform').submit(function (e) {
        e.preventDefault();
        $('.row-f-err').text('');
        $('.f-err').text('');
        $('.order-row-card').removeClass('card-error').css('border-color', '');

        const rows = [], cardIds = [];

        $('#purchaseBody .order-row-card').each(function () {
            const bags   = parseFloat($(this).find('input[name="bags[]"]').val())   || 0;
            const kg     = parseFloat($(this).find('input[name="kg[]"]').val())     || 0;
            const net_kg = parseFloat($(this).find('input[name="net_kg[]"]').val()) || 0;
            const rate   = parseFloat($(this).find('input[name="rate[]"]').val())   || 0;
            const order_detail_id   = $(this).find('input[name="order_detail_id[]"]').val()   || 0;
            cardIds.push($(this).attr('id'));
            rows.push({
                garden_id:  $(this).find('select[name="garden_id[]"]').val(),
                invoice_no: $(this).find('input[name="invoice_no[]"]').val(),
                grade:      $(this).find('select[name="grade[]"]').val(),
                bags, kg, net_kg, rate, order_detail_id, amount: net_kg * rate
            });
        });

        if (rows.length < 1) { Toast.fire({ icon: 'error', title: 'Enter at least one record' }); return; }

        const payload = {
            _token:         $('input[name="_token"]').val(),
            _method:        'PUT',
            token:          $('input[name="token"]').val(),
            user_id:        $('input[name="user_id"]').val(),
            company_id:     $('input[name="company_id"]').val(),
            buyer_party:    $('#buyer_party').val(),
            transport:      $('#transport').val(),
            credit_days:    $('#credit_days').val(),
            discount:       $('#discount').val(),
            order_date:      $('#order_date').val(),
            totalNetKg:     $('#totalNetKg').text(),
            totalAmount:    $('#totalAmount').text(),
            discountAmount: $('#discountAmount').text(),
            finalAmount:    $('#finalAmount').text(),
            rows
        };

        loadershow();
        $.ajax({
            type: 'PUT', url: "{{ route('order.update', $edit_id) }}", data: payload,
            success: function (r) {
                if (r.status == 200) {
                    Toast.fire({ icon: 'success', title: r.message });
                    window.location.href = "{{ route('admin.order') }}";
                } else { Toast.fire({ icon: 'error', title: r.message }); loaderhide(); }
            },
            error: function (xhr) {
                loaderhide();
                if (xhr.status !== 422) { handleAjaxError(xhr); return; }
                let errors = {};
                try { errors = xhr.responseJSON.errors || {}; } catch (e) {}
                let firstErrorCard = null;
                $.each(errors, function (key, messages) {
                    const msg = Array.isArray(messages) ? messages[0] : messages;
                    const rowMatch = key.match(/^rows\.(\d+)\.(\w+)$/);
                    if (rowMatch) {
                        const cardId = cardIds[parseInt(rowMatch[1])];
                        if (cardId) {
                            const $card = $('#' + cardId);
                            $card.find('.err-' + rowMatch[2]).text(msg);
                            $card.addClass('card-error');
                            if (!firstErrorCard) firstErrorCard = $card;
                        }
                    } else {
                        $('#error-' + key).text(msg);
                    }
                });
                if (firstErrorCard) $('html,body').animate({ scrollTop: firstErrorCard.offset().top - 80 }, 400);
                Toast.fire({ icon: 'error', title: 'Please fix the errors highlighted below.' });
            }
        });
    });
});
</script>
@endpush
