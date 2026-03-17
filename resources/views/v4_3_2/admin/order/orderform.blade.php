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
    --radius:      10px;
    --radius-sm:   7px;
    --shadow-sm:   0 1px 3px rgba(0,0,0,.07), 0 2px 8px rgba(0,0,0,.05);
    --font:        'Inter', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--font) !important; background: var(--c-bg) !important; }

/* ── Section Cards ── */
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
.inv-section-body { padding: 20px; }

/* ── Form controls ── */
.f-label {
    display: block; font-size: 11.5px; font-weight: 600;
    color: var(--c-muted); text-transform: uppercase;
    letter-spacing: .055em; margin-bottom: 5px;
}
.f-label .req { color: var(--c-danger); margin-left: 2px; }
.f-ctrl {
    width: 100%; background: var(--c-light);
    border: 1.5px solid var(--c-border); border-radius: var(--radius-sm);
    padding: 8px 12px; font-size: 13px; color: var(--c-text);
    font-family: var(--font); transition: border .15s, box-shadow .15s, background .15s;
    outline: none; appearance: none;
}
.f-ctrl:focus { border-color: var(--c-primary); background: #fff; box-shadow: 0 0 0 3px var(--c-primary-s); }
.f-ctrl[disabled],.f-ctrl:disabled { background: #f4f5f8 !important; cursor: default; color: var(--c-text); opacity: .85; }
textarea.f-ctrl { resize: vertical; min-height: 70px; }
.f-err { font-size: 11px; color: var(--c-danger); margin-top: 3px; display: block; }

/* Select2 */
.select2-container { width: 100% !important; }
.select2-container--default .select2-selection--single {
    height: 37px !important; border: 1.5px solid var(--c-border) !important;
    border-radius: var(--radius-sm) !important; background: var(--c-light) !important;
    display: flex; align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 35px !important; font-size: 13px; color: var(--c-text) !important; padding-left: 12px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 35px !important; }
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--c-primary) !important; box-shadow: 0 0 0 3px var(--c-primary-s) !important;
}
.select2-results__option[aria-disabled="true"] { color: var(--c-danger) !important; }

/* ── Line Items Wrapper ── */
.li-wrap {
    background: var(--c-white); border: 1px solid var(--c-border);
    border-radius: var(--radius); box-shadow: var(--shadow-sm);
    margin-bottom: 18px; overflow: hidden;
}
.li-topbar {
    padding: 14px 20px; display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 10px;
    border-bottom: 1px solid var(--c-border);
    background: linear-gradient(90deg, #eef1fd 0%, var(--c-white) 100%);
}
.li-topbar-left { display: flex; align-items: center; gap: 10px; }
.li-topbar-left .ico {
    width: 32px; height: 32px; border-radius: 8px;
    background: var(--c-primary); color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 15px;
}
.li-topbar-left h6 { margin: 0; font-size: 13px; font-weight: 700; color: var(--c-text); }
.li-count-badge {
    font-size: 11px; font-weight: 700; padding: 2px 9px;
    background: var(--c-primary); color: #fff; border-radius: 20px;
}
.li-quickbar {
    padding: 12px 20px; display: flex; align-items: center; gap: 10px;
    flex-wrap: wrap; background: var(--c-light); border-bottom: 1px solid var(--c-border);
}
.btn-add-row {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: var(--radius-sm);
    background: var(--c-primary); color: #fff; border: none;
    font-size: 12.5px; font-weight: 600; font-family: var(--font);
    cursor: pointer; white-space: nowrap;
    transition: background .15s, transform .1s;
    box-shadow: 0 2px 8px rgba(59,91,219,.2);
}
.btn-add-row:hover { background: var(--c-primary-h); transform: translateY(-1px); }

/* ── Order row cards ── */
.li-list { padding: 14px 20px; display: flex; flex-direction: column; gap: 12px; }
.li-empty { text-align: center; padding: 48px 20px; color: var(--c-muted); }
.li-empty-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: var(--c-primary-s); color: var(--c-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; margin: 0 auto 12px;
}
.li-empty p { font-size: 13px; margin: 0; }

.order-row-card {
    border: 1.5px solid var(--c-border); border-radius: var(--radius);
    background: var(--c-white); overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
    animation: cardIn .2s ease both;
}
@keyframes cardIn { from { opacity:0; transform:translateY(-5px); } to { opacity:1; transform:translateY(0); } }
.order-row-card:hover { border-color: #b8c4f5; box-shadow: 0 2px 10px rgba(59,91,219,.09); }

.order-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 14px; background: #f7f8fd; border-bottom: 1px solid var(--c-border);
}
.order-card-num { display: flex; align-items: center; gap: 8px; }
.order-card-num .num-badge {
    width: 24px; height: 24px; border-radius: 6px;
    background: var(--c-primary); color: #fff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
}
.order-card-num span.lbl { font-size: 12px; font-weight: 600; color: var(--c-muted); }

.btn-card-del {
    width: 28px; height: 28px; border-radius: 6px; border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; cursor: pointer;
    background: var(--c-danger-s); color: var(--c-danger);
    transition: background .13s, transform .1s;
}
.btn-card-del:hover { background: #ffd5d5; transform: scale(1.1); }

.order-card-body { padding: 14px; }
/* 6-col grid matching invoice */
.order-field-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 10px 14px; align-items: end;
}
/* Amount cell highlight */
.order-field-grid .amount-cell .f-ctrl {
    font-weight: 700 !important; color: var(--c-primary) !important;
    background: #eef1fd !important; border-color: #c5cff5 !important;
}
.order-field-grid .amount-cell .f-ctrl:focus {
    border-color: var(--c-primary) !important; background: #fff !important;
}
.order-card-body .f-label { font-size: 10.5px; margin-bottom: 4px; }
.order-card-body .f-ctrl  { font-size: 12.5px; padding: 7px 10px; }

/* ── Totals ── */
.li-totals { border-top: 2px solid var(--c-border); background: #f8f9fd; padding: 20px; }
.totals-inner { max-width: 380px; margin-left: auto; }
.t-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 0; border-bottom: 1px dashed var(--c-border); gap: 12px;
}
.t-row:last-child { border-bottom: none; }
.t-row.grand { border-top: 2px solid var(--c-primary); border-bottom: none; margin-top: 8px; padding-top: 14px; }
.t-label { flex: 1; font-size: 12.5px; font-weight: 500; color: var(--c-muted); }
.t-row.grand .t-label { font-size: 14px; font-weight: 700; color: var(--c-text); }
.t-val { font-size: 13px; font-weight: 700; color: var(--c-text); min-width: 110px; text-align: right; }
.t-row.grand .t-val { font-size: 18px; color: var(--c-primary); }
.t-row.discount .t-val { color: var(--c-danger); }
.t-badge { font-size: 10px; font-weight: 700; background: var(--c-warn-s); color: var(--c-warn); border-radius: 4px; padding: 1px 6px; margin-left: 6px; }

/* ── Footer Buttons ── */
.inv-footer { display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }
.btn-f {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 20px; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; border: none;
    cursor: pointer; font-family: var(--font); transition: background .15s, transform .1s;
}
.btn-f:hover { transform: translateY(-1px); }
.btn-f-primary { background: var(--c-primary); color: #fff; box-shadow: 0 2px 10px rgba(59,91,219,.25); }
.btn-f-primary:hover { background: var(--c-primary-h); }
.btn-f-reset  { background: var(--c-danger-s); color: var(--c-danger); }
.btn-f-reset:hover { background: #ffd5d5; }
.btn-f-cancel { background: #edf0f7; color: var(--c-muted); }
.btn-f-cancel:hover { background: #e2e6ef; }

/* ── Modal ── */
.modal-content { border-radius: var(--radius) !important; border: none !important; box-shadow: 0 20px 60px rgba(0,0,0,.15) !important; font-family: var(--font); }
.modal-header  { background: linear-gradient(90deg,#f0f3ff,#fff); border-bottom: 1px solid var(--c-border) !important; border-radius: var(--radius) var(--radius) 0 0 !important; padding: 16px 22px !important; }
.modal-title   { font-weight: 700 !important; font-size: 14px !important; color: var(--c-text) !important; }

/* ── Responsive ── */
@media (max-width: 1200px) { .order-field-grid { grid-template-columns: repeat(4, minmax(0,1fr)); } }
@media (max-width:  800px) { .order-field-grid { grid-template-columns: repeat(3, minmax(0,1fr)); } }
@media (max-width:  600px) {
    .li-topbar, .li-quickbar { flex-direction: column; align-items: flex-start; }
    .order-field-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
/* ── Order row card body ── */
.order-card-body .f-label { font-size: 10.5px; margin-bottom: 4px; }
.order-card-body .f-ctrl  { font-size: 12.5px; padding: 7px 10px; }

/* ── Responsive ── */
@media (max-width: 1200px) {
    .order-field-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }
}
@media (max-width: 800px) {
    .order-field-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
}
@media (max-width: 600px) {
    .li-topbar   { flex-direction: column; align-items: flex-start; }
    .li-quickbar { flex-direction: column; align-items: flex-start; }
    .order-field-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .order-card-body  { padding: 10px; }
    .li-list          { padding: 10px; gap: 8px; }
    .inv-section-body { padding: 14px; }
    .li-totals        { padding: 14px; }
    .t-val            { font-size: 12px; }
    .t-row.grand .t-val { font-size: 15px; }
    .btn-add-row      { width: 100%; justify-content: center; }
    .inv-footer       { justify-content: stretch; }
    .btn-f            { flex: 1; justify-content: center; }
}
@media (max-width: 400px) {
    .order-field-grid { grid-template-columns: repeat(1, minmax(0,1fr)); }
    .order-card-head  { padding: 7px 10px; }
}
@media (max-width: 600px) {
    .totals-inner { max-width: 100%; }
    .t-label      { font-size: 11.5px; }
}
/* Desktop: pin Amount to column 6, row 1 */
.order-field-grid .amount-cell {
    grid-column: 6;
    grid-row: 1;
}

/* Tablet: 4-col grid — let it flow normally */
@media (max-width: 1200px) {
    .order-field-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }
    .order-field-grid .amount-cell { grid-column: unset; grid-row: unset; }
}

/* Small tablet: 3-col */
@media (max-width: 800px) {
    .order-field-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
}

/* Mobile: 2-col */
@media (max-width: 600px) {
    .order-field-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}

/* Small mobile: 1-col */
@media (max-width: 400px) {
    .order-field-grid { grid-template-columns: repeat(1, minmax(0,1fr)); }
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
                            <textarea class="f-ctrl" name="address" id="g_address" rows="2" placeholder="Address"></textarea>
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
                <button type="button" id="modalcancelbtn"class="close" data-dismiss="modal"><span>&times;</span></button>
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
                            <label class="f-label">State  <span class="req">*</span></label>
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
                            <textarea class="f-ctrl" name="address" id="p_address" rows="2" placeholder="Address"></textarea>
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

    {{-- Party Details --}}
    <div class="inv-section">
        <div class="inv-section-head">
            <div class="ico"><i class="ri-user-3-line"></i></div>
            <h6>Party Details</h6>
        </div>
        <div class="inv-section-body">
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <label class="f-label">Buyer Party <span class="req">*</span></label>
                    <select class="form-control" name="buyer_party" id="buyer_party">
                        <option value="" selected disabled>Select Buyer</option>
                    </select>
                    <span class="f-err" id="error-buyer_party"></span>
                </div>
                <div class="col-sm-6 mb-3">
                    <label class="f-label">Transport</label>
                    <select class="form-control" name="transport" id="transport">
                        <option value="" selected disabled>Select Transport</option>
                    </select>
                    <span class="f-err" id="error-transport"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Information --}}
    <div class="inv-section">
        <div class="inv-section-head">
            <div class="ico"><i class="ri-file-list-3-line"></i></div>
            <h6>Order Information</h6>
        </div>
        <div class="inv-section-body">
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <label class="f-label">Credit Days <span class="req">*</span></label>
                    <select class="f-ctrl" name="credit_days" id="credit_days">
                        <option value="" disabled selected>Select Day</option>
                        <option value="CD">CD</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="45">45</option>
                        <option value="60">60</option>
                        <option value="90">90</option>
                    </select>
                    <span class="f-err" id="error-credit_days"></span>
                </div>
                <div class="col-sm-6 mb-3">
                    <label class="f-label">Discount (%)</label>
                    <input type="number" step="0.01" class="f-ctrl calculationfield" name="discount" id="discount" placeholder="0.00" value="0" min="0">
                    <span class="f-err" id="error-discount"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Line Items --}}
    <div class="li-wrap">
        <div class="li-topbar">
            <div class="li-topbar-left">
                <div class="ico"><i class="ri-list-check-2"></i></div>
                <h6>Order Items</h6>
                <span class="li-count-badge" id="li-count">0</span>
            </div>
        </div>

        <div class="li-quickbar">
            <button type="button" id="addRowBtn" class="btn-add-row">
                <i class="ri-add-circle-line"></i> Add New Row
            </button>
        </div>

        <div class="li-list" id="purchaseBody">
            <div id="li-empty" class="li-empty">
                <div class="li-empty-icon"><i class="ri-inbox-line"></i></div>
                <p><strong>No items yet.</strong> Click "Add New Row" to get started.</p>
            </div>
        </div>

        {{-- Totals --}}
        <div class="li-totals">
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

    {{-- Footer --}}
    <div class="inv-section">
        <div class="inv-section-body">
            <div class="inv-footer">
                <button id="cancelbtn" type="button" class="btn-f btn-f-cancel"><i class="ri-close-line"></i> Cancel</button>
                <button type="reset"                 class="btn-f btn-f-reset"><i class="ri-refresh-line"></i> Reset</button>
                <button type="submit"                class="btn-f btn-f-primary"><i class="ri-save-line"></i> Save Order</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('ajax')
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<script>
$(document).ready(function () {
    let message = "{{ session('message') }}";
    if (message) Toast.fire({ icon: 'error', title: message });

    let gardens = '', grades = '', partyType;
    let rowCount = 0;

    /* ── focus/blur for number fields ── */
    $(document).on('focus', '.calculationfield', function () { if ($(this).val() == '0') $(this).val(''); });
    $(document).on('blur',  '.calculationfield', function () { if ($(this).val() === '') $(this).val('0'); });

    /* ════════════════════════════════
       ROW COUNT BADGE
    ════════════════════════════════ */
    function updateRowCount() {
        const n = $('#purchaseBody .order-row-card').length;
        $('#li-count').text(n);
        n === 0 ? $('#li-empty').show() : $('#li-empty').hide();
    }

    /* ════════════════════════════════
       BUILD A ROW CARD
    ════════════════════════════════ */
    function addNewRow() {
        rowCount++;
        const card = `
        <div class="order-row-card" id="row_${rowCount}">
            <div class="order-card-head">
                <div class="order-card-num">
                    <span class="num-badge">${rowCount}</span>
                    <span class="lbl">Row ${rowCount}</span>
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
                        <input type="number" class="f-ctrl bags calculationfield" name="bags[]" placeholder="0" value="0" min="0">
                        <span class="f-err row-f-err err-bags"></span>
                    </div>
                    <div>
                        <label class="f-label">Kg</label>
                        <input type="number" step="0.01" class="f-ctrl kg calculationfield" name="kg[]" placeholder="0.00" value="0" min="0">
                        <span class="f-err row-f-err err-kg"></span>
                    </div>
                    <div>
                        <label class="f-label">Net Kg</label>
                        <input type="number" step="0.01" class="f-ctrl net-kg" name="net_kg[]" disabled placeholder="0.00">
                    </div>
                    <div>
                        <label class="f-label">Rate / Kg <span style="color:var(--c-danger)">*</span></label>
                        <input type="number" step="0.01" class="f-ctrl rate calculationfield" name="rate[]" placeholder="0.00" value="0" min="0">
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
        
        // Initialize Select2 for the new garden dropdown
        $(`#row_${rowCount} .garden-select`).select2({
            placeholder: 'Select Garden',
            width: '100%',
            allowClear: true,
            search: true
        });
        
        // Initialize Select2 for the new grade dropdown
        $(`#row_${rowCount} .grade-select`).select2({
            placeholder: 'Select Grade',
            width: '100%',
            allowClear: true,
            search: true
        });
    }

    /* ════════════════════════════════
       TOTALS
    ════════════════════════════════ */
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

    $(document).on('keyup change', '.calculationfield', function () {
        const row = $(this).closest('.order-row-card');
        const bags   = parseFloat(row.find('.bags').val())  || 0;
        const kg     = parseFloat(row.find('.kg').val())    || 0;
        const rate   = parseFloat(row.find('.rate').val())  || 0;
        const netKg  = bags * kg;
        const amount = netKg * rate;
        row.find('.net-kg').val(netKg.toFixed(2));
        row.find('.amount').val(amount.toFixed(2));
        calculateTotals();
    });

    /* ════════════════════════════════
       ADD / REMOVE ROW
    ════════════════════════════════ */
    $('#addRowBtn').on('click', addNewRow);

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

    /* ════════════════════════════════
       GARDEN MODAL
    ════════════════════════════════ */
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

    /* ════════════════════════════════
       PARTY MODAL
    ════════════════════════════════ */
    $(document).on('change', '#buyer_party, #transport', function () {
        const opt = $(this).find(':selected');

        if (opt.val() === 'add_new') {
            // Determine party type
            let partyType = opt.data('type');

            // Clear the dropdown that triggered this to reset
            if (partyType === 'Buyer') {
                $('#buyer_party').val('');
            } else {
                $('#transport').val('');
            }

            // Update modal title and hidden party_type input
            $('#partyModalLabel').text(`Add New  Party - ${partyType}`);
            $('#party_type').val(partyType);

            // Load countries for modal (your existing function)
            loadPartyCountry();

            // Show the modal
            $('#partyModal').modal('show');

            // Clear dropdown selection after modal opens
            $(this).val('');

            // --- NEW: handle Transport special logic ---
            if (partyType === 'Transport') {
                // Remove red asterisk from State
                $('#p_state').prev('label').find('span.req').remove();

                // Clear any previous error
                $('#error-state').text('');

                // Reset state & city dropdowns
                $('#p_state, #p_city').val('').prop('selectedIndex', 0);
            } else if (partyType === 'Buyer') {
                // Add asterisk back for Buyer if missing
                if ($('#p_state').prev('label').find('span.req').length === 0) {
                    $('#p_state').prev('label').append('<span class="req">*</span>');
                }
            }
        }
    });
    $('#modalcancelbtn').on('click', () => { $('#partyform')[0].reset(); $('#partyModal').modal('hide'); $('#error-state').text(''); $('#error-name').text(''); });

    $('#partyform').submit(function (e) {
        e.preventDefault(); loadershow(); $('.f-err').text('');
        $.ajax({ type: 'POST', url: "{{ route('party.store') }}", data: $(this).serialize(),
            success: function (r) {
                if (r.status == 200) {
                    Toast.fire({ icon: 'success', title: r.message });
                    if (partyType === 'Buyer') buyer_party(r.party_id); else transport(r.party_id);
                    $('#partyform')[0].reset(); $('#partyModal').modal('hide');
                } else Toast.fire({ icon: 'error', title: r.message });
                loaderhide();
            },
            error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    });

    /* ════════════════════════════════
       LOCATION HELPERS — GARDEN
    ════════════════════════════════ */
    function loadcountry() {
        $.ajax({ type: 'GET', url: "{{ route('country.index') }}", data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.country != '') {
                    $('#country').html('<option selected disabled>Select Country</option>');
                    $.each(r.country, function (k, v) {
                        if (v.id == 101) $('#country').append(`<option value="${v.id}" selected>${v.country_name}</option>`);
                    });
                    loadstate();
                }
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#country').on('change', function () { loadershow(); $('#city').html('<option selected disabled>Select City</option>'); loadstate($(this).val()); });
    function loadstate(id = 0) {
        $('#state').html('<option selected disabled>Select State</option>');
        const url = id == 0 ? "{{ route('state.search', session('user')['country_id']) }}" : "{{ route('state.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.state != '') { $.each(r.state, function (k, v) { $('#state').append(`<option value="${v.id}">${v.state_name}</option>`); }); if (id == 0) loadcity(); }
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#state').on('change', function () { loadershow(); loadcity($(this).val()); });
    function loadcity(id = 0) {
        $('#city').html('<option selected disabled>Select City</option>');
        const url = id == 0 ? "{{ route('city.search', session('user')['state_id']) }}" : "{{ route('city.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.city != '') $.each(r.city, function (k, v) { $('#city').append(`<option value="${v.id}">${v.city_name}</option>`); });
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }

    /* ════════════════════════════════
       LOCATION HELPERS — PARTY
    ════════════════════════════════ */
    function loadPartyCountry() {
        $.ajax({ type: 'GET', url: "{{ route('country.index') }}", data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.country != '') {
                    $('#p_country').html('<option selected disabled>Select Country</option>');
                    $.each(r.country, function (k, v) { if (v.id == 101) $('#p_country').append(`<option value="${v.id}" selected>${v.country_name}</option>`); });
                    loadPartyState();
                }
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#p_country').on('change', function () { loadershow(); loadPartyState($(this).val()); });
    function loadPartyState(id = 0) {
        $('#p_state').html('<option selected disabled>Select State</option>');
        const url = id == 0 ? "{{ route('state.search', session('user')['country_id']) }}" : "{{ route('state.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.state != '') { $.each(r.state, function (k, v) { $('#p_state').append(`<option value="${v.id}">${v.state_name}</option>`); }); }
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }
    $('#p_state').on('change', function () { loadershow(); loadPartyCity($(this).val()); });
    function loadPartyCity(id = 0) {
        $('#p_city').html('<option selected disabled>Select City</option>');
        const url = "{{ route('city.search','__id__') }}".replace('__id__', id);
        $.ajax({ type: 'GET', url, data: { token: "{{ session()->get('api_token') }}" },
            success: function (r) {
                if (r.status == 200 && r.city != '') $.each(r.city, function (k, v) { $('#p_city').append(`<option value="${v.id}">${v.city_name}</option>`); });
                loaderhide();
            }, error: xhr => { loaderhide(); handleAjaxError(xhr); }
        });
    }

    /* ════════════════════════════════
       FETCH GARDENS
    ════════════════════════════════ */
    async function fetchGardens(selectId = null) {
        try {
            const r = await ajaxRequest('GET', "{{ route('garden.index') }}", { user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}" });
            gardens = '<option value="" selected disabled>Select Garden</option>';
            if (r.data && r.data.length > 0) r.data.forEach(g => { gardens += `<option value="${g.id}">${g.garden_name}</option>`; });
            gardens += `<option value="add_new">+ Add New Garden</option>`;

            if (selectId) {
                $('.garden-select').each(function () {
                    let cur = $(this).val();
                    $(this).html(gardens);
                    if (cur && cur !== 'add_new') $(this).val(cur);
                });
                $('.garden-select').filter(function () { return !$(this).val(); }).last().val(selectId);
            }
            
            // Initialize Select2 for garden dropdowns with search
            $('.garden-select').select2({
                placeholder: 'Select Garden',
                width: '100%',
                allowClear: true,
                search: true
            });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    /* ════════════════════════════════
       FETCH GRADE
    ════════════════════════════════ */
    async function fetchGrade() {
        try {
            const r = await ajaxRequest('GET', "{{ route('grade.index') }}", { user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}" });
            grades = '<option value="" selected disabled>Select Grade</option>';
            if (r.data && r.data.length) r.data.forEach(g => { grades += `<option value="${g.id}">${g.grade}</option>`; });
            
            // Initialize Select2 for grade dropdowns with search
            $('.grade-select').select2({
                placeholder: 'Select Grade',
                width: '100%',
                allowClear: true,
                search: true
            });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    /* ════════════════════════════════
       FETCH BUYERS / TRANSPORT
    ════════════════════════════════ */
    async function buyer_party(party_id = null) {
        try {
            const r = await ajaxRequest('GET', "{{ route('buyer.index') }}", { user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}" });
            const $sel = $('#buyer_party');
            $sel.empty().append('<option value="" selected disabled>Select Buyer</option><option value="add_new" data-type="Buyer">+ Add New Buyer</option>');
            if (r.data && r.data.length) r.data.forEach(p => { $sel.append(`<option value="${p.id}">${p.name}</option>`); });
            if (party_id) $sel.val(party_id);
            $sel.select2({ placeholder: 'Select Buyer', width: '100%', search: true });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    async function transport(party_id = null) {
        try {
            const r = await ajaxRequest('GET', "{{ route('transport.index') }}", { user_id: "{{ session()->get('user_id') }}", company_id: "{{ session()->get('company_id') }}", token: "{{ session()->get('api_token') }}" });
            const $sel = $('#transport');
            $sel.empty().append('<option value="" selected disabled>Select Transport</option><option value="add_new" data-type="Transport">+ Add New Transport</option>');
            if (r.data && r.data.length) r.data.forEach(p => { $sel.append(`<option value="${p.id}">${p.name}</option>`); });
            if (party_id) $sel.val(party_id);
            $sel.select2({ placeholder: 'Select Transport', width: '100%', search: true });
        } catch (xhr) { handleAjaxError(xhr); }
        finally { loaderhide(); }
    }

    /* ════════════════════════════════
       INIT
    ════════════════════════════════ */
    async function initOrderForm() {
        loadershow();
        try {
            await Promise.all([ fetchGardens(), fetchGrade(), buyer_party(), transport() ]);
            addNewRow();
        } catch (e) { handleAjaxError(e); }
        finally { loaderhide(); }
    }
    initOrderForm();

    /* ════════════════════════════════
       CANCEL
    ════════════════════════════════ */
    $('#cancelbtn').on('click', () => { loadershow(); window.location.href = "{{ route('admin.order') }}"; });

    /* ════════════════════════════════
       SUBMIT
    ════════════════════════════════ */
    $('#orderform').submit(function (e) {
        e.preventDefault();

        // Clear all previous row-level errors + card highlights
        $('.row-f-err').text('');
        $('.order-row-card').css('border-color', '');

        const rows    = [];
        const cardIds = []; // maps submission index → card DOM id

        $('#purchaseBody .order-row-card').each(function () {
            const bags   = parseFloat($(this).find('input[name="bags[]"]').val()) || 0;
            const kg     = parseFloat($(this).find('input[name="kg[]"]').val())   || 0;
            const rate   = parseFloat($(this).find('input[name="rate[]"]').val()) || 0;
            const net_kg = bags * kg;

            cardIds.push($(this).attr('id')); // e.g. "row_3"
            rows.push({
                garden_id:  $(this).find('select[name="garden_id[]"]').val(),
                invoice_no: $(this).find('input[name="invoice_no[]"]').val(),
                grade:      $(this).find('select[name="grade[]"]').val(),
                bags, kg, net_kg, rate, amount: net_kg * rate
            });
        });

        if (rows.length < 1) {
            Toast.fire({ icon: 'error', title: 'Enter at least one record' });
            return;
        }

        const payload = {
            _token:         $('input[name="_token"]').val(),
            token:          $('input[name="token"]').val(),
            user_id:        $('input[name="user_id"]').val(),
            company_id:     $('input[name="company_id"]').val(),
            buyer_party:    $('#buyer_party').val(),
            transport:      $('#transport').val(),
            credit_days:    $('#credit_days').val(),
            discount:       $('#discount').val(),
            totalNetKg:     $('#totalNetKg').text(),
            totalAmount:    $('#totalAmount').text(),
            discountAmount: $('#discountAmount').text(),
            finalAmount:    $('#finalAmount').text(),
            rows
        };

        loadershow();
        $.ajax({
            type: 'POST',
            url:  "{{ route('order.store') }}",
            data: payload,
            success: function (r) {
                if (r.status == 200) {
                    Toast.fire({ icon: 'success', title: r.message });
                    window.location.href = "{{ route('admin.order') }}";
                } else {
                    Toast.fire({ icon: 'error', title: r.message });
                    loaderhide();
                }
            },
            error: function (xhr) {
                loaderhide();

                // Only do custom row-error mapping on 422
                if (xhr.status !== 422) { handleAjaxError(xhr); return; }

                let errors = {};
                try { errors = xhr.responseJSON.errors || {}; } catch(e) {}

                let firstErrorCard = null;

                $.each(errors, function (key, messages) {
                    const msg = Array.isArray(messages) ? messages[0] : messages;

                    // Row-level: e.g. "rows.1.invoice_no"
                    const rowMatch = key.match(/^rows\.(\d+)\.(\w+)$/);
                    if (rowMatch) {
                        const idx    = parseInt(rowMatch[1]); // 0-based
                        const field  = rowMatch[2];           // e.g. "invoice_no"
                        const cardId = cardIds[idx];          // e.g. "row_3"

                        if (cardId) {
                            const $card = $('#' + cardId);

                            // Show error message inside that specific card
                            $card.find('.err-' + field).text(msg);

                            // Red border on the card
                            $card.css('border-color', 'var(--c-danger)');

                            // Track first errored card for auto-scroll
                            if (!firstErrorCard) firstErrorCard = $card;
                        }
                        return; // continue $.each
                    }

                    // Top-level fields: buyer_party, credit_days, etc.
                    $('#error-' + key).text(msg);
                });

                // Auto-scroll to first errored card
                if (firstErrorCard) {
                    $('html, body').animate({
                        scrollTop: firstErrorCard.offset().top - 80
                    }, 400);
                }

                Toast.fire({ icon: 'error', title: 'Please fix the errors highlighted below.' });
            }
        });
    });
});
</script>
@endpush
