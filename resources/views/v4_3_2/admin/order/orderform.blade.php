@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Order
@endsection
@section('title')
    New Order
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

/* ── Compact top header bar ── */
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
.order-header-bar .col-sm-4 { padding: 0 6px; }
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
.li-topbar {
    padding: 8px 14px; display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 8px;
    border-bottom: 1px solid var(--c-border);
    background: linear-gradient(90deg, #eef1fd 0%, var(--c-white) 100%);
}
.li-topbar-left { display: flex; align-items: center; gap: 8px; }
.li-topbar-left .ico {
    width: 26px; height: 26px; border-radius: 6px;
    background: var(--c-primary); color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 13px;
}
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

.li-list { display: flex; flex-direction: column; gap: 8px; }
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
    padding: 10px 16px; display: flex; align-items: flex-start;
    justify-content: space-between; gap: 16px;
}
.li-totals-left { flex: 1; }
.li-totals-center { flex: 1; display: flex; justify-content: center; }
.totals-inner { max-width: 420px; min-width: 320px; }
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
.inv-footer {
    display: flex; justify-content: flex-end; gap: 7px; flex-wrap: wrap;
    padding: 10px 16px; background: var(--c-white);
    border: 1px solid var(--c-border); box-shadow: var(--shadow-sm);
}
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
    .li-totals-left  { display: none; }
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
    /* .order-header-bar [class*="col-sm"]:last-child {
        width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important;
    } */
    .order-field-grid { grid-template-columns: 1fr; gap: 6px; }
    .order-field-grid .amount-cell { grid-column: unset; }
    .order-card-head { padding: 5px 10px; }
    .order-card-body { padding: 8px 10px; }
    .btn-f { flex: 0 0 100%; }
}
.order-date {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    position: relative;
    top:-5px;
}
#orderform
{
    margin-top: -60px;
}
@media (max-width: 768px) {
    .order-date {
        flex-direction: column;
        align-items: stretch;
        position: unset;
    }
    #orderform
    {
        margin-top: unset;
    }
    .order-date > div {
        width: 100%;
    }
}
/* Base button */
.btn-f {
    border: 1px solid #ccc;
    background: #f8f9fa;
    color: #333;
    padding: 8px 14px;
    border-radius: 6px;
    transition: all 0.2s ease;
    outline: none;
}

/* Hover */
.btn-f:hover {
    background: #e9ecef;
}

/* Focus (when clicked or tabbed) */
.btn-f:focus {
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    border-color: #007bff;
}

/* Active (when pressed) */
.btn-f:active {
    transform: scale(0.97);
}

/* Primary button */
.btn-f-primary {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
}

.btn-f-primary:focus {
    box-shadow: 0 0 0 3px rgba(0,123,255,0.4);
}

/* Secondary button */
.btn-f-secondary {
    background: #6c757d;
    color: #fff;
    border-color: #6c757d;
}

.btn-f-secondary:focus {
    box-shadow: 0 0 0 3px rgba(108,117,125,0.4);
}
.btn-add-row {
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-add-row:focus {
    outline: none; /* remove default */
    border: 1px solid #4c6fff; /* nice blue border */
    box-shadow: 0 0 0 2px rgba(0, 10, 52, 0.2); /* soft glow */
    border:1px solid black;
}

/* Optional: active click */
.btn-add-row:active {
    transform: scale(0.97);
}
</style>
<link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endsection

@section('form-content')

{{-- ── Garden Modal ── --}}
<div class="modal fade" id="gardenModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-plant-line mr-2"></i> Add New Garden</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form id="gardenform">
                    @csrf
                    <input type="hidden" name="token"      value="{{ session('api_token') }}">
                    <input type="hidden" name="user_id"    value="{{ session('user_id') }}">
                    <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Garden Name <span class="req">*</span></label>
                            <input type="text" id="garden_name" class="f-ctrl" name="garden_name" placeholder="Garden Name">
                            <span class="modal-error-msg f-err" id="modal-error-garden_name"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Email</label>
                            <input type="email" class="f-ctrl" name="email" id="g_email" placeholder="Email">
                            <span class="modal-error-msg f-err" id="modal-error-email"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Contact Person Name</label>
                            <input type="text" class="f-ctrl" name="contact_person_name" id="g_contact_person_name" placeholder="Contact Person">
                            <span class="modal-error-msg f-err" id="modal-error-contact_person_name"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Mobile 1</label>
                            <input type="tel" class="f-ctrl" name="mobile_1" id="g_mobile_1" placeholder="0123456789">
                            <span class="modal-error-msg f-err" id="modal-error-mobile_1"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Mobile 2</label>
                            <input type="tel" class="f-ctrl" name="mobile_2" id="g_mobile_2" placeholder="0123456789">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Country</label>
                            <select class="f-ctrl" name="country" id="country"><option selected disabled>Select Country</option></select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">State</label>
                            <select class="f-ctrl" name="state" id="state"><option selected disabled>Select State</option></select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">City</label>
                            <select class="f-ctrl" name="city" id="city"><option selected disabled>Select City</option></select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Pincode</label>
                            <input type="text" class="f-ctrl" id="pincode" name="pincode" placeholder="Pin Code">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Address</label>
                            <textarea class="f-ctrl" name="address" id="g_address" rows="2" placeholder="Address" style="height:auto;"></textarea>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">GST Number</label>
                            <input type="text" class="f-ctrl" name="gst_no" id="g_gst_no" placeholder="GST Number">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">PAN Number</label>
                            <input type="text" class="f-ctrl" name="pan" id="g_pan" placeholder="PAN Number">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2" style="gap:8px">
                        <button type="submit" class="btn-f btn-f-primary"><i class="ri-save-line"></i> Save</button>
                        <button type="reset"  class="btn-f btn-f-reset"><i class="ri-refresh-line"></i> Reset</button>
                        <button type="button" id="modal_cancelBtn" class="btn-f btn-f-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── Party Modal ── --}}
<div class="modal fade" id="partyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partyModalLabel"><i class="ri-user-add-line mr-2"></i> Add New Party</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form id="partyform">
                    @csrf
                    <input type="hidden" name="token"      value="{{ session('api_token') }}">
                    <input type="hidden" name="user_id"    value="{{ session('user_id') }}">
                    <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                    <input type="hidden" name="party_type" id="party_type">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Name <span class="req">*</span></label>
                            <input type="text" id="p_name" class="f-ctrl" name="name" placeholder="Name">
                            <span class="f-err" id="error-name"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Email</label>
                            <input type="email" class="f-ctrl" name="email" id="p_email" placeholder="Email">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Contact Person</label>
                            <input type="text" class="f-ctrl" name="contact_person_name" id="p_contact" placeholder="Contact Person Name">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Mobile 1</label>
                            <input type="tel" class="f-ctrl" name="mobile_1" id="p_mobile_1" placeholder="0123456789">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Mobile 2</label>
                            <input type="tel" class="f-ctrl" name="mobile_2" id="p_mobile_2" placeholder="0123456789">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Country <span class="req">*</span></label>
                            <select class="f-ctrl" name="country" id="p_country"><option selected disabled>Select Country</option></select>
                            <span class="f-err" id="error-country"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">State <span class="req">*</span></label>
                            <select class="f-ctrl" name="state" id="p_state"><option selected disabled>Select State</option></select>
                            <span class="f-err" id="error-state"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">City</label>
                            <select class="f-ctrl" name="city" id="p_city"><option selected disabled>Select City</option></select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Pincode</label>
                            <input type="text" class="f-ctrl" name="pincode" id="p_pincode" placeholder="Pin Code">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Address</label>
                            <textarea class="f-ctrl" name="address" id="p_address" rows="2" placeholder="Address" style="height:auto;"></textarea>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">GST Number</label>
                            <input type="text" class="f-ctrl" name="gst_no" id="p_gst_no" placeholder="GST Number">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">PAN Number</label>
                            <input type="text" class="f-ctrl" name="pan" id="p_pan" placeholder="PAN Number">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2" style="gap:8px">
                        <button type="submit" class="btn-f btn-f-primary"><i class="ri-save-line"></i> Save</button>
                        <button type="reset"  class="btn-f btn-f-reset"><i class="ri-refresh-line"></i> Reset</button>
                        <button type="button" id="modalcancelbtn" class="btn-f btn-f-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ MAIN ORDER FORM ══════════════ --}}
<form id="orderform">
    @csrf
    <input type="hidden" name="token"      value="{{ session('api_token') }}">
    <input type="hidden" name="user_id"    value="{{ session('user_id') }}">
    <input type="hidden" name="company_id" value="{{ session('company_id') }}">

    {{-- ── 1. Line Items ── --}}
   <div class="d-flex justify-content-between align-items-center flex-wrap order-date">
    
        <!-- LEFT SIDE TITLE -->
        <div>
            <h6 style="margin:0;font-weight:700;color:#1a1d2e;">
            
            </h6>
        </div>

        <!-- RIGHT SIDE DATE -->
        <div style="min-width:220px;">
            <label class="f-label">Order Date <span class="req">*</span></label>
            <input type="date" class="f-ctrl" name="order_date" id="order_date" max="">
            <span class="f-err" id="error-order_date"></span>
        </div>

    </div>
    <div class="li-wrap" style="margin-bottom: 0; border-bottom: none;">
        <div class="li-list" id="purchaseBody">
            <div id="li-empty" class="li-empty">
                <div class="li-empty-icon"><i class="ri-inbox-line"></i></div>
                <p><strong>No items yet.</strong> Click "Add New Row" to get started.</p>
            </div>
        </div>
    </div>

    {{-- ── 2. Add New Row Button ── --}}
    <div style="
        display: flex;
        justify-content: center;
        padding: 8px 14px;
        background: var(--c-light);
        border: 1px solid var(--c-border);
        border-top: none;
        margin-bottom: 10px;
    ">
        <button type="button" id="addRowBtn" class="btn-add-row">
            <i class="ri-add-circle-line"></i> Add New Row
        </button>
    </div>

    {{-- ── 3. Order Header Bar ── --}}
    <div class="order-header-bar">
        <div class="row align-items-end" style="margin:0 -6px;">
            <div class="col-sm-3 mb-0" style="padding:0 6px;">
                <label class="f-label">Buyer Party</label>
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

            <div class="col-sm-3 mb-0" style="padding:0 6px;">
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

            <div class="col-sm-3 mb-0" style="padding:0 6px;">
                <label class="f-label">Discount (%)</label>
                <input type="number" step="0.01" class="f-ctrl" name="discount" id="discount"
                       placeholder="0.00" value="0" min="0">
                <span class="f-err" id="error-discount"></span>
            </div>
            <div class="col-sm-3 mb-0" style="padding:0 6px;">
                <label class="f-label">Reference</label>
                <select class="form-control" name="reference" id="reference">
                    <option value="" selected disabled>Select Reference</option>
                </select>
                <span class="f-err" id="error-reference"></span>
            </div>
            <div class="col-sm-3 mb-0" style="padding:0 6px;">
               <label class="f-label">Expected Dispatch Date</label>
                <input type="date" class="f-ctrl" name="expected_dispatch_date" id="expected_dispatch_date">
                <span class="f-err" id="error-expected_dispatch_date"></span>
            </div>
        </div>
    </div>

    {{-- ── 4. Totals ── --}}
    <div class="li-totals" style="border: 1px solid var(--c-border); border-top: none; margin-bottom: 10px;">
        <div class="li-totals-left"></div>
        <div class="li-totals-center"></div>
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

    {{-- ── 5. Footer Buttons ── --}}
    <div class="inv-footer">

    <button id="cancelbtn" type="button" class="btn-f btn-f-cancel">
        <i class="ri-close-line"></i> Cancel
    </button>

    <button type="reset" class="btn-f btn-f-reset">
        <i class="ri-refresh-line"></i> Reset
    </button>
    <button type="submit" name="save_type" value="continue" class="btn-f btn-f-secondary">
        <i class="ri-save-3-line"></i> Save & Continue
    </button>

    <!-- ✅ Save Order -->
    <button type="submit" name="save_type" value="redirect" class="btn-f btn-f-primary">
        <i class="ri-save-line"></i> Save Order
    </button>
</div>
</form>
@endsection

@push('ajax')
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<script>
/* ════════════════════════════════════════════════════════
   ORDER DATE — localStorage logic
   • Same day  → restore saved date
   • New day   → clear storage, leave field blank
   • On change → save to localStorage
════════════════════════════════════════════════════════ */
(function () {
    const $dateField = document.getElementById('order_date');
    if (!$dateField) return;

    // ✅ Get today's LOCAL date
    const now = new Date();
    const today = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0');

    // Set max attribute to today's date to prevent future dates
    $dateField.setAttribute('max', today);

    let storedDay  = localStorage.getItem('order_default_day');
    let storedDate = localStorage.getItem('order_default_date');

    // ✅ If NEW DAY → reset everything
    if (storedDay !== today) {
        localStorage.removeItem('order_default_date');
        localStorage.setItem('order_default_day', today);
        storedDate = null;
    }

    // ✅ Always apply default if exists
    if (storedDate) {
        $dateField.value = storedDate;
    } else {
        $dateField.value = today; // first time default
    }

    // ✅ Save ONLY FIRST TIME (very important)
    $dateField.addEventListener('change', function () {
        const alreadySaved = localStorage.getItem('order_default_date');
            localStorage.setItem('order_default_date', this.value);
            localStorage.setItem('order_default_day', today);
    });

})();

$(document).ready(function () {

    let message = "{{ session('message') }}";
    if (message) Toast.fire({ icon: 'error', title: message });

    let gardens = '', grades = '';
    let rowCount = 0;

    /* ════════════════════════════════════════════════════════
       CALCULATION ENGINE
    ════════════════════════════════════════════════════════ */
    function safeFloat(val) {
        if (val === '' || val === null || val === undefined) return null;
        const n = parseFloat(val);
        return isNaN(n) ? null : n;
    }

    function validateField($el) {
        const raw = $el.val().trim();
        if (raw === '' || raw === '0') { $el.removeClass('field-error'); return true; }
        const n = parseFloat(raw);
        if (isNaN(n)) {
            $el.addClass('field-error');
            Toast.fire({ icon: 'warning', title: `${$el.closest('div').find('.f-label').text().trim()} must be a number` });
            $el.val('0');
            return false;
        }
        if (n < 0) {
            $el.addClass('field-error');
            Toast.fire({ icon: 'warning', title: `${$el.closest('div').find('.f-label').text().trim()} cannot be negative` });
            $el.val('0');
            return false;
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

        if (!validateField(row.find('.' + changedField))) return;

        let bags = safeFloat($bags.val());
        let kg   = safeFloat($kg.val());
        let net  = safeFloat($net.val());
        const rate = safeFloat($rate.val()) || 0;

        if (bags === null && kg === null && net === null) {
            $amt.val('0.00');
            calculateTotals();
            return;
        }

        const filledCount = [bags, kg, net].filter(v => v !== null && v > 0).length;
        if (filledCount < 2 && changedField !== 'rate') {
            if (net !== null && net > 0 && rate > 0) {
                $amt.val((net * rate).toFixed(2));
            }
            calculateTotals();
            return;
        }

        bags = bags || 0;
        kg   = kg   || 0;
        net  = net  || 0;

        if (bags < 0 || kg < 0 || net < 0) {
            $bags.val('0'); $kg.val('0'); $net.val('0'); $amt.val('0.00');
            calculateTotals();
            return;
        }

        if (changedField === 'bags') {
            if (kg > 0)              net = bags * kg;
            else if (net > 0 && bags > 0) kg = net / bags;
        }
        else if (changedField === 'kg') {
            if (bags > 0)            net = bags * kg;
            else if (net > 0 && kg > 0)  bags = net / kg;
            if (kg === 0 && bags === 0)  net = 0;
        }
        else if (changedField === 'net-kg') {
            if (net === 0) { $amt.val('0.00'); calculateTotals(); return; }
            const lastTyped = row.data('lastTyped') || '';
            if (lastTyped === 'bags' && bags > 0 && kg > 0)      kg   = net / bags;
            else if (lastTyped === 'kg' && kg > 0)                bags = net / kg;
            else {
                if (bags > 0 && kg === 0)      kg   = net / bags;
                else if (kg > 0 && bags === 0) bags = net / kg;
                else if (bags > 0 && kg > 0)   kg   = net / bags;
            }
        }
        // changedField === 'rate' → amount only, no bags/kg/net change

        if (!isFinite(bags) || isNaN(bags) || bags < 0) bags = 0;
        if (!isFinite(kg)   || isNaN(kg)   || kg   < 0) kg   = 0;
        if (!isFinite(net)  || isNaN(net)  || net  < 0) net  = 0;

        const fmtNum = n => n > 0 ? parseFloat(n.toFixed(4)) : '';
        if (changedField !== 'bags')   $bags.val(fmtNum(bags));
        if (changedField !== 'kg')     $kg.val(fmtNum(kg));
        if (changedField !== 'net-kg') $net.val(fmtNum(net));

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
        $('.amount').each(function ()  { totalAmount += parseFloat($(this).val()) || 0; });
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
       EVENT BINDING
    ════════════════════════════════════════════════════════ */
    $(document).on('focus', '.bags, .kg, .net-kg, .rate, #discount', function () {
        if ($(this).val() == '0') $(this).val('');
    });

    $(document).on('blur', '.bags, .kg, .net-kg, .rate, #discount', function () {
        const val = parseFloat($(this).val());
        if (isNaN(val) || $(this).val().trim() === '') $(this).val('0');
    });

    $(document).on('focusout', '.bags, .kg', function () {
        const row = $(this).closest('.order-row-card');
        if (!row.length) return;
        if ($(this).hasClass('bags'))                                    row.data('lastTyped', 'bags');
        else if ($(this).hasClass('kg') && !$(this).hasClass('net-kg')) row.data('lastTyped', 'kg');
    });

    let calcTimer = null;
    $(document).on('focusout', '.bags, .kg, .net-kg, .rate', function () {
        const row = $(this).closest('.order-row-card');
        if (!row.length) return;
        const changedField = ['bags','kg','net-kg'].find(c => $(this).hasClass(c)) || 'rate';
        clearTimeout(calcTimer);
        calcTimer = setTimeout(() => { recalcRow(row, changedField); }, 80);
    });

    $('#discount').on('input', function () { calculateTotals(); });

    /* ════════════════════════════════════════════════════════
       ROW COUNT
    ════════════════════════════════════════════════════════ */
    function updateRowCount() {
        const n = $('#purchaseBody .order-row-card').length;
        $('#li-count').text(n);
        n === 0 ? $('#li-empty').show() : $('#li-empty').hide();
    }

    /* ════════════════════════════════════════════════════════
       BUILD A ROW CARD
    ════════════════════════════════════════════════════════ */
    function addNewRow() {
        rowCount++;
        let lastSelectedGarden = null;
        const $last = $('.garden-select').filter(function () {
            return $(this).val() && $(this).val() !== 'add_new';
        }).last();
        if ($last.length) lastSelectedGarden = $last.val();

        const card = `
        <div class="order-row-card" id="row_${rowCount}">
            <div class="order-card-head">
                <div class="order-card-num">
                    <span class="num-badge">${rowCount}</span>
                    <span class="lbl">Line Item ${rowCount}</span>
                </div>
                <button type="button" class="btn-card-del remove-row" data-toggle="tooltip" data-original-title="Delete Row">
                    <i class="ri-delete-bin-2-line"></i>
                </button>
            </div>
            <div class="order-card-body">
                <div class="order-field-grid">
                    <div>
                        <label class="f-label">Garden <span style="color:var(--c-danger)">*</span></label>
                        <select class="f-ctrl garden-select" name="garden_id[]">${gardens}</select>
                        <span class="f-err row-f-err err-garden_id"></span>
                    </div>
                    <div>
                        <label class="f-label">Invoice / Lot No</label>
                        <input type="text" class="f-ctrl" name="invoice_no[]" placeholder="Invoice No">
                        <span class="f-err row-f-err err-invoice_no"></span>
                    </div>
                    <div>
                        <label class="f-label">Grade</label>
                        <select class="f-ctrl grade-select" name="grade[]">${grades}</select>
                    </div>
                    <div>
                        <label class="f-label">Bags</label>
                        <input type="number" class="f-ctrl bags" name="bags[]" placeholder="0" value="0" min="0" step="0.01">
                        <span class="f-err row-f-err err-bags"></span>
                    </div>
                    <div>
                        <label class="f-label">Kg</label>
                        <input type="number" step="0.01" class="f-ctrl kg" name="kg[]" placeholder="0.00" value="0" min="0">
                        <span class="f-err row-f-err err-kg"></span>
                    </div>
                    <div>
                        <label class="f-label">Net Kg</label>
                        <input type="number" step="0.001" class="f-ctrl net-kg" name="net_kg[]" placeholder="0.00" value="0" min="0">
                    </div>
                    <div>
                        <label class="f-label">Rate / Kg <span style="color:var(--c-danger)">*</span></label>
                        <input type="number" step="0.01" class="f-ctrl rate" name="rate[]" placeholder="0.00" value="0" min="0">
                        <span class="f-err row-f-err err-rate"></span>
                    </div>
                    <div class="amount-cell">
                        <label class="f-label">Amount</label>
                        <input type="number" class="f-ctrl amount" name="amt[]" disabled placeholder="0.00">
                        <span class="f-err row-f-err err-amount"></span>
                    </div>
                </div>
            </div>
        </div>`;

        $('#li-empty').before(card);
        updateRowCount();
        $('[data-toggle="tooltip"]').tooltip('dispose').tooltip();

        $(`#row_${rowCount} .garden-select`).select2({ placeholder: 'Select Garden', width: '100%', allowClear: true });
        $(`#row_${rowCount} .grade-select`).select2({ placeholder: 'Select Grade',  width: '100%', allowClear: true });
        
        if (rowCount === 1) {
            setTimeout(() => {
                $(`#row_${rowCount} .garden-select`).select2('open');
            }, 100);
        }
        if (lastSelectedGarden) {
            $(`#row_${rowCount} .garden-select`).val(lastSelectedGarden).trigger('change');
        }
    }

    /* ADD ROW */
    $('#addRowBtn').on('click', function () { addNewRow(); });

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
       GARDEN MODAL
    ════════════════════════════════════════════════════════ */
    $('#gardenModal').on('shown.bs.modal', loadcountry);
    $('#modal_cancelBtn').on('click', () => { $('#gardenform')[0].reset(); $('#gardenModal').modal('hide'); });

    $(document).on('change', '.garden-select', function () {
        if ($(this).val() === 'add_new') { $(this).val(''); $('#gardenModal').modal('show'); }
    });

    $('#gardenform').submit(function (e) {
        e.preventDefault(); loadershow(); $('.modal-error-msg').text('');
        $.ajax({ type: 'POST', url: "{{ route('garden.store') }}", data: $(this).serialize(),
            success: function (r) {
                if (r.status == 200) {
                    Toast.fire({ icon: 'success', title: r.message });
                    $('#gardenform')[0].reset(); $('#gardenModal').modal('hide');
                    fetchGardens(String(r.garden_id));
                } else Toast.fire({ icon: 'error', title: r.message });
                loaderhide();
            },
            error: xhr => { loaderhide(); handleModalAjaxError(xhr); }
        });
    });

    /* ════════════════════════════════════════════════════════
       PARTY MODAL
    ════════════════════════════════════════════════════════ */
    $(document).on('change', '#buyer_party, #transport,#reference', function () {
        const opt = $(this).find(':selected');
        if (opt.val() === 'add_new') {
            let pType = opt.data('type');
            if (pType === 'Buyer') $('#buyer_party').val(''); else if (pType === 'Transport') $('#transport').val(''); else $('#reference').val('');
            $('#partyModalLabel').text(`Add New Party - ${pType}`);
            $('#party_type').val(pType);
            loadPartyCountry();
            $('#partyModal').modal('show');
            $(this).val('');
            if (pType === 'Transport') {
                $('#p_state').prev('label').find('span.req').remove();
                $('#error-state').text('');
                $('#p_state, #p_city').val('').prop('selectedIndex', 0);
            } else if (pType === 'Buyer') {
                if ($('#p_state').prev('label').find('span.req').length === 0) {
                    $('#p_state').prev('label').append('<span class="req">*</span>');
                }
            }
        }
    });

    $('#modalcancelbtn').on('click', () => { $('#partyform')[0].reset(); $('#partyModal').modal('hide'); $('#error-state, #error-name').text(''); });

    $('#partyform').submit(function (e) {
        e.preventDefault(); loadershow(); $('.f-err').text('');
        $.ajax({ type: 'POST', url: "{{ route('party.store') }}", data: $(this).serialize(),
            success: function (r) {
                if (r.status == 200) {
                    Toast.fire({ icon: 'success', title: r.message });
                    if (r.data['party_type'] === 'Buyer') buyer_party(r.data['party_id']); else if (r.data['party_type'] === 'Transport') transport(r.data['party_id']); else reference(r.data['party_id']);
                    $('#partyform')[0].reset(); $('#partyModal').modal('hide');
                } else Toast.fire({ icon: 'error', title: r.message });
                loaderhide();
            },
            error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    });

    /* ════════════════════════════════════════════════════════
       LOCATION — GARDEN MODAL
    ════════════════════════════════════════════════════════ */
    function loadcountry() {
        $.ajax({ type: 'GET', url: "{{ route('country.index') }}", data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.country) {
                    $('#country').html('<option value="">Select Country</option>');
                    $.each(r.country, function (k, v) {
                        $('#country').append(`<option value="${v.id}" ${v.id==101?'selected':''}>${v.country_name}</option>`);
                    });
                    if ($('#country').hasClass('select2-hidden-accessible')) $('#country').select2('destroy');
                    $('#country').select2({ placeholder: 'Select Country', allowClear: true, width: '100%', dropdownParent: $('#gardenModal') });
                    loadstate();
                }
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#country').on('change', function () {
        loadershow();
        if ($('#city').hasClass('select2-hidden-accessible')) $('#city').select2('destroy');
        $('#city').html('<option value="">Select City</option>');
        $('#city').select2({ placeholder: 'Select City', allowClear: true, width: '100%', dropdownParent: $('#gardenModal') });
        loadstate($(this).val());
    });
    function loadstate(id = 0) {
        $('#state').html('<option value="">Select State</option>');
        const url = id == 0
            ? "{{ route('state.search', session('user')['country_id']) }}"
            : "{{ route('state.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.state) $.each(r.state, (k,v) => $('#state').append(`<option value="${v.id}">${v.state_name}</option>`));
                if (id == 0) loadcity();
                if ($('#state').hasClass('select2-hidden-accessible')) $('#state').select2('destroy');
                $('#state').select2({ placeholder: 'Select State', allowClear: true, width: '100%', dropdownParent: $('#gardenModal') });
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#state').on('change', function () { loadershow(); loadcity($(this).val()); });
    function loadcity(id = 0) {
        $('#city').html('<option value="">Select City</option>');
        const url = id == 0
            ? "{{ route('city.search', session('user')['state_id']) }}"
            : "{{ route('city.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.city) $.each(r.city, (k,v) => $('#city').append(`<option value="${v.id}">${v.city_name}</option>`));
                if ($('#city').hasClass('select2-hidden-accessible')) $('#city').select2('destroy');
                $('#city').select2({ placeholder: 'Select City', allowClear: true, width: '100%', dropdownParent: $('#gardenModal') });
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#gardenModal').on('hidden.bs.modal', function () {
        ['#country','#state','#city'].forEach(s => { if ($(s).hasClass('select2-hidden-accessible')) $(s).select2('destroy'); });
    });

    /* ════════════════════════════════════════════════════════
       LOCATION — PARTY MODAL
    ════════════════════════════════════════════════════════ */
    function loadPartyCountry() {
        $.ajax({ type: 'GET', url: "{{ route('country.index') }}", data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.country) {
                    $('#p_country').html('<option value="">Select Country</option>');
                    $.each(r.country, (k,v) => $('#p_country').append(`<option value="${v.id}" ${v.id==101?'selected':''}>${v.country_name}</option>`));
                    if ($('#p_country').hasClass('select2-hidden-accessible')) $('#p_country').select2('destroy');
                    $('#p_country').select2({ placeholder: 'Select Country', allowClear: true, width: '100%', dropdownParent: $('#partyModal') });
                    loadPartyState();
                }
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#p_country').on('change', function () { loadershow(); loadPartyState($(this).val()); });
    function loadPartyState(id = 0) {
        $('#p_state').html('<option value="">Select State</option>');
        const url = id == 0
            ? "{{ route('state.search', session('user')['country_id']) }}"
            : "{{ route('state.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.state) $.each(r.state, (k,v) => $('#p_state').append(`<option value="${v.id}">${v.state_name}</option>`));
                if ($('#p_state').hasClass('select2-hidden-accessible')) $('#p_state').select2('destroy');
                $('#p_state').select2({ placeholder: 'Select State', allowClear: true, width: '100%', dropdownParent: $('#partyModal') });
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#p_state').on('change', function () { loadershow(); loadPartyCity($(this).val()); });
    function loadPartyCity(id = 0) {
        $('#p_city').html('<option value="">Select City</option>');
        const url = "{{ route('city.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.city) $.each(r.city, (k,v) => $('#p_city').append(`<option value="${v.id}">${v.city_name}</option>`));
                if ($('#p_city').hasClass('select2-hidden-accessible')) $('#p_city').select2('destroy');
                $('#p_city').select2({ placeholder: 'Select City', allowClear: true, width: '100%', dropdownParent: $('#partyModal') });
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#partyModal').on('hidden.bs.modal', function () {
        ['#p_country','#p_state','#p_city'].forEach(s => { if ($(s).hasClass('select2-hidden-accessible')) $(s).select2('destroy'); });
    });

    /* ════════════════════════════════════════════════════════
       FETCH GARDENS
    ════════════════════════════════════════════════════════ */
    async function fetchGardens(selectId = null) {
        try {
            const r = await ajaxRequest('GET', "{{ route('garden.index') }}", {
                user_id: "{{ session()->get('user_id') }}",
                company_id: "{{ session()->get('company_id') }}",
                token: "{{ session()->get('api_token') }}"
            });
            gardens = '<option value="" selected disabled>Select Garden</option>';
            if (r.data && r.data.length) r.data.forEach(g => { gardens += `<option value="${g.id}">${g.garden_name}</option>`; });
            gardens += `<option value="add_new">+ Add New Garden</option>`;
            if (selectId) {
                $('.garden-select').each(function () {
                    let cur = $(this).val();
                    $(this).html(gardens);
                    if (cur && cur !== 'add_new') $(this).val(cur);
                });
                $('.garden-select').filter(function () { return !$(this).val(); }).last().val(selectId);
            }
            $('.garden-select').select2({ placeholder: 'Select Garden', width: '100%', allowClear: true });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    /* ════════════════════════════════════════════════════════
       FETCH GRADE
    ════════════════════════════════════════════════════════ */
    async function fetchGrade() {
        try {
            const r = await ajaxRequest('GET', "{{ route('grade.index') }}", {
                user_id: "{{ session()->get('user_id') }}",
                company_id: "{{ session()->get('company_id') }}",
                token: "{{ session()->get('api_token') }}"
            });
            grades = '<option value="" selected disabled>Select Grade</option>';
            if (r.data && r.data.length) r.data.forEach(g => { grades += `<option value="${g.id}">${g.grade}</option>`; });
            $('.grade-select').select2({ placeholder: 'Select Grade', width: '100%', allowClear: true });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    /* ════════════════════════════════════════════════════════
       FETCH BUYERS / TRANSPORT
    ════════════════════════════════════════════════════════ */
    async function buyer_party(party_id = null) {
        try {
            const r = await ajaxRequest('GET', "{{ route('buyer.index') }}", {
                user_id: "{{ session()->get('user_id') }}",
                company_id: "{{ session()->get('company_id') }}",
                token: "{{ session()->get('api_token') }}"
            });
            const $sel = $('#buyer_party');
            $sel.empty().append('<option value="" selected disabled>Select Buyer</option><option value="add_new" data-type="Buyer">+ Add New Buyer</option>');
            if (r.data && r.data.length) r.data.forEach(p => $sel.append(`<option value="${p.id}">${p.name}</option>`));
            if (party_id) $sel.val(party_id);
            $sel.select2({ placeholder: 'Select Buyer', width: '100%' });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }
    async function reference(party_id = null) {
        try {
            const r = await ajaxRequest('GET', "{{ route('reference.index') }}", {
                user_id: "{{ session()->get('user_id') }}",
                company_id: "{{ session()->get('company_id') }}",
                token: "{{ session()->get('api_token') }}"
            });
            const $sel = $('#reference');
            $sel.empty().append('<option value="" selected disabled>Select Reference</option><option value="add_new" data-type="reference">+ Add New Reference</option>');
            if (r.data && r.data.length) r.data.forEach(p => $sel.append(`<option value="${p.id}">${p.name}</option>`));
            if (party_id) $sel.val(party_id);
            $sel.select2({ placeholder: 'Select Reference', width: '100%' });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    async function transport(party_id = null) {
        try {
            const r = await ajaxRequest('GET', "{{ route('transport.index') }}", {
                user_id: "{{ session()->get('user_id') }}",
                company_id: "{{ session()->get('company_id') }}",
                token: "{{ session()->get('api_token') }}"
            });
            const $sel = $('#transport');
            $sel.empty().append('<option value="" selected disabled>Select Transport</option><option value="add_new" data-type="Transport">+ Add New Transport</option>');
            if (r.data && r.data.length) r.data.forEach(p => $sel.append(`<option value="${p.id}">${p.name}</option>`));
            if (party_id) $sel.val(party_id);
            $sel.select2({ placeholder: 'Select Transport', width: '100%' });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    $(document).on('focus', '.select2-selection', function () {
        $(this).closest('.select2-container').prev('select').select2('open');
    });

    /* ════════════════════════════════════════════════════════
       INIT
    ════════════════════════════════════════════════════════ */
    async function initOrderForm() {
        loadershow();
        try {
            await Promise.all([ fetchGardens(), fetchGrade(), buyer_party(), transport(),reference() ]);
            $('#credit_days').select2({ placeholder: 'Select Credit Days', allowClear: true, width: '100%' });
            
            // Reset form on page load
            // $('#orderform')[0].reset();
            $('#buyer_party').val(null).trigger('change');
            $('#reference').val(null).trigger('change');
            $('#transport').val(null).trigger('change');
            $('#credit_days').val(null).trigger('change');
            
            addNewRow();
        } catch (e) { handleAjaxError(e); }
        finally { loaderhide(); }
    }
    initOrderForm();

    /* ════════════════════════════════════════════════════════
       RESET BUTTON
    ════════════════════════════════════════════════════════ */
    $('button[type="reset"]').on('click', function() {
        setTimeout(function() {
            // Reset main form Select2 dropdowns
            $('#buyer_party').val(null).trigger('change');
            $('#reference').val(null).trigger('change');
            $('#transport').val(null).trigger('change');
            $('#credit_days').val(null).trigger('change');
            
            // Reset line item Select2 dropdowns
            $('.garden-select').val(null).trigger('change');
            $('.grade-select').val(null).trigger('change');
        }, 10);
    });

    /* ════════════════════════════════════════════════════════
       CANCEL
    ════════════════════════════════════════════════════════ */
    $('#cancelbtn').on('click', () => { loadershow(); window.location.href = "{{ route('admin.order') }}"; });

    /* ════════════════════════════════════════════════════════
       SUBMIT
    ════════════════════════════════════════════════════════ */
    let saveType = 'redirect'; // default

    $('#orderform button[type="submit"]').click(function () {
        saveType = $(this).val();
    });
    $('#orderform').submit(function (e) {
        e.preventDefault();
        $('.row-f-err').text('');
        $('.order-row-card').removeClass('card-error').css('border-color', '');

        const rows = [], cardIds = [];

        $('#purchaseBody .order-row-card').each(function () {
            const bags   = parseFloat($(this).find('input[name="bags[]"]').val())   || 0;
            const kg     = parseFloat($(this).find('input[name="kg[]"]').val())     || 0;
            const net_kg = parseFloat($(this).find('input[name="net_kg[]"]').val()) || 0;
            const rate   = parseFloat($(this).find('input[name="rate[]"]').val())   || 0;
            cardIds.push($(this).attr('id'));
            rows.push({
                garden_id:  $(this).find('select[name="garden_id[]"]').val(),
                invoice_no: $(this).find('input[name="invoice_no[]"]').val(),
                grade:      $(this).find('select[name="grade[]"]').val(),
                bags, kg, net_kg, rate, amount: net_kg * rate
            });
        });

        if (rows.length < 1) { Toast.fire({ icon: 'error', title: 'Enter at least one record' }); return; }

        const payload = {
            _token:         $('input[name="_token"]').val(),
            token:          $('input[name="token"]').val(),
            user_id:        $('input[name="user_id"]').val(),
            company_id:     $('input[name="company_id"]').val(),
            buyer_party:    $('#buyer_party').val(),
            reference:      $('#reference').val(),
            expected_dispatch_date: $('#expected_dispatch_date').val(),
            transport:      $('#transport').val(),
            credit_days:    $('#credit_days').val(),
            discount:       $('#discount').val(),
            order_date:     $('#order_date').val(),
            totalNetKg:     $('#totalNetKg').text(),
            totalAmount:    $('#totalAmount').text(),
            discountAmount: $('#discountAmount').text(),
            finalAmount:    $('#finalAmount').text(),
            save_type: saveType, 
            rows
        };

        loadershow();
        $.ajax({
            type: 'POST', url: "{{ route('order.store') }}", data: payload,
            success: function (r) {
                if (r.status == 200) {
                    Toast.fire({ icon: 'success', title: r.message });
                      if (saveType === 'continue') {
                        loaderhide();
                        window.location.href = "{{route('admin.orderform')}}";

                    } else {
                        // ✅ Redirect (Save Order)
                        window.location.href = "{{ route('admin.order') }}";
                    }
                } else { Toast.fire({ icon: 'error', title: r.message }); loaderhide(); }
            },
            error: function (xhr) {
                loaderhide();
                if (xhr.status !== 422) { handleAjaxError(xhr); return; }
                let errors = {};
                try { errors = xhr.responseJSON.errors || {}; } catch(e) {}
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