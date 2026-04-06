@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Edit Invoice
@endsection
@section('title')
    Edit Invoice
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

/* ── Edit badge ── */
.page-edit-badge {
    display: inline-flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #3b5bdb, #5c7cfa);
    color: #fff; border-radius: 20px;
    padding: 4px 14px; font-size: 11.5px; font-weight: 700;
    letter-spacing: .04em; margin-bottom: 14px;
    box-shadow: 0 2px 10px rgba(59,91,219,.3);
}

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
.f-ctrl[disabled],.f-ctrl:disabled { background: #f4f5f8 !important; cursor: default; color: var(--c-text); opacity:.85; }
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
.quickbar-sep { width: 1px; height: 28px; background: var(--c-border); flex-shrink: 0; }
.quickbar-label { font-size: 11.5px; font-weight: 600; color: var(--c-muted); white-space: nowrap; text-transform: uppercase; letter-spacing: .04em; }
.quickbar-sel { min-width: 180px; max-width: 240px; flex: 1; }
.li-list { padding: 14px 20px; display: flex; flex-direction: column; gap: 12px; }
.li-empty { text-align: center; padding: 48px 20px; color: var(--c-muted); }
.li-empty-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: var(--c-primary-s); color: var(--c-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; margin: 0 auto 12px;
}
.li-empty p { font-size: 13px; margin: 0; }

/* ── Item card ── */
.li-item-card {
    border: 1.5px solid var(--c-border); border-radius: var(--radius);
    background: var(--c-white); overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
    animation: cardIn .2s ease both;
}
@keyframes cardIn { from { opacity:0; transform:translateY(-5px); } to { opacity:1; transform:translateY(0); } }
.li-item-card:hover { border-color: #b8c4f5; box-shadow: 0 2px 10px rgba(59,91,219,.09); }
.li-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 14px; background: #f7f8fd; border-bottom: 1px solid var(--c-border);
}
.li-card-num { display: flex; align-items: center; gap: 8px; }
.li-card-num .num-badge {
    width: 24px; height: 24px; border-radius: 6px;
    background: var(--c-primary); color: #fff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
}
.li-card-num p { margin: 0; font-size: 12px; font-weight: 600; color: var(--c-muted); }
.li-card-actions { display: flex; align-items: center; gap: 5px; }
.btn-card {
    width: 28px; height: 28px; border-radius: 6px; border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; cursor: pointer; transition: background .13s, transform .1s;
}
.btn-card:hover { transform: scale(1.1); }
.btn-card-up,.btn-card-dn { background: #f1f3f9; color: var(--c-muted); }
.btn-card-up:hover,.btn-card-dn:hover { background: #e2e6f4; }
.btn-card-dup { background: #eef1fd; color: var(--c-primary); }
.btn-card-dup:hover { background: #d9e0fb; }
.btn-card-del { background: var(--c-danger-s); color: var(--c-danger); }
.btn-card-del:hover { background: #ffd5d5; }
.li-card-body { padding: 14px; }

/* ── 6-col fixed grid — Amount always col 6 row 1 ── */
.li-field-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 10px 14px; align-items: end;
}
.li-field-grid .amount-wrap { width: 100%; position: relative; }
.li-field-grid .amount-wrap .sym {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    font-size: 12px; color: var(--c-primary); font-weight: 700; pointer-events: none;
}
.li-field-grid .amount-wrap input.f-ctrl {
    width: 100% !important; padding-left: 26px !important;
    font-weight: 700 !important; font-size: 13px !important;
    color: var(--c-primary) !important;
    background: #eef1fd !important; border-color: #c5cff5 !important;
}
.li-field-grid .amount-wrap input.f-ctrl:focus {
    border-color: var(--c-primary) !important; background: #fff !important;
}
.li-card-body .f-label { font-size: 10.5px; margin-bottom: 4px; }
.li-card-body .f-ctrl  { font-size: 12.5px; padding: 7px 10px; }
.li-amount-row { display: none !important; }

/* ── Totals ── */
.li-totals { border-top: 2px solid var(--c-border); background: #f8f9fd; padding: 20px; }
.totals-inner { max-width: 380px; margin-left: auto; }
.t-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 0; border-bottom: 1px dashed var(--c-border);
    gap: 12px; flex-wrap: nowrap;
}
.t-row:last-child { border-bottom: none; }
.t-row.grand { border-top: 2px solid var(--c-primary); border-bottom: none; margin-top: 8px; padding-top: 14px; }
.t-label { flex: 1; white-space: nowrap; font-size: 12.5px; font-weight: 500; color: var(--c-muted); display: flex; align-items: center; gap: 7px; }
.t-badge { font-size: 10px; font-weight: 700; background: var(--c-warn-s); color: var(--c-warn); border-radius: 4px; padding: 1px 6px; }
.t-row.grand .t-label { font-size: 14px; font-weight: 700; color: var(--c-text); }
.t-val { flex-shrink: 0; font-size: 13px; font-weight: 700; color: var(--c-text); display: flex; align-items: center; gap: 5px; }
.t-sym { font-size: 11px; font-weight: 400; color: var(--c-muted); }
.t-row.grand .t-val { font-size: 18px; color: var(--c-primary); }
.t-val input.disableinput {
    border: none !important; background: transparent !important;
    font-family: var(--font); font-weight: 700; font-size: 13px;
    color: var(--c-text); text-align: right; width: 120px; padding: 0; outline: none;
}
.t-row.grand .t-val input.disableinput { font-size: 18px; color: var(--c-primary); width: 140px; }
#roundoffline .t-val input.disableinput { color: var(--c-warn) !important; }

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
.btn-f-success { background: var(--c-success); color: #fff; box-shadow: 0 2px 10px rgba(47,158,68,.25); }
.btn-f-success:hover { background: #267a37; }
.btn-f-reset  { background: var(--c-danger-s); color: var(--c-danger); }
.btn-f-reset:hover { background: #ffd5d5; }
.btn-f-cancel { background: #edf0f7; color: var(--c-muted); }
.btn-f-cancel:hover { background: #e2e6ef; }

/* ── Modal ── */
.modal-content { border-radius: var(--radius) !important; border: none !important; box-shadow: 0 20px 60px rgba(0,0,0,.15) !important; font-family: var(--font); }
.modal-header  { background: linear-gradient(90deg,#f0f3ff,#fff); border-bottom: 1px solid var(--c-border) !important; border-radius: var(--radius) var(--radius) 0 0 !important; padding: 16px 22px !important; }
.modal-title   { font-weight: 700 !important; font-size: 14px !important; color: var(--c-text) !important; }

/* ── Responsive ── */
@media (max-width: 1200px) { .li-field-grid { grid-template-columns: repeat(4, minmax(0,1fr)); } }
@media (max-width:  800px) { .li-field-grid { grid-template-columns: repeat(3, minmax(0,1fr)); } }
@media (max-width:  600px) {
    .li-topbar, .li-quickbar { flex-direction: column; align-items: flex-start; }
    .quickbar-sep { display: none; }
    .li-field-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
/* Desktop: pin Amount to column 6, row 1 */
.li-field-grid .amount-cell {
    grid-column: 6;
    grid-row: 1;
}

/* Tablet 4-col: unpin and flow naturally */
@media (max-width: 1200px) {
    .li-field-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }
    .li-field-grid .amount-cell { grid-column: unset; grid-row: unset; }
}

/* Small tablet 3-col */
@media (max-width: 800px) {
    .li-field-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
}

/* Mobile 2-col */
@media (max-width: 600px) {
    .li-topbar, .li-quickbar { flex-direction: column; align-items: flex-start; }
    .quickbar-sep { display: none; }
    .li-field-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .inv-section-body { padding: 14px; }
    .li-list { padding: 10px; }
    .li-card-body { padding: 10px; }
    .li-totals { padding: 14px; }
    .totals-inner { max-width: 100%; }
    .btn-add-row { width: 100%; justify-content: center; }
    .inv-footer { justify-content: stretch; }
    .btn-f { flex: 1; justify-content: center; }
}

/* Small mobile 1-col */
@media (max-width: 400px) {
    .li-field-grid { grid-template-columns: repeat(1, minmax(0,1fr)); }
}
</style>
<link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endsection

@section('form-content')

{{-- Bank Modal --}}
<div class="modal fade" id="bankDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-bank-line mr-2"></i> Add Bank Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form id="bankdetailform">
                    @csrf
                    <input type="hidden" name="user_id"               value="{{ session('user_id') }}">
                    <input type="hidden" name="token"                 value="{{ session('api_token') }}">
                    <input type="hidden" name="company_id"            value="{{ $company_id }}">
                    <input type="hidden" name="bank_companymaster_id" id="bank_companymaster_id" value="">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Holder Name <span class="req">*</span></label>
                            <input type="text" name="holder_name" class="f-ctrl" placeholder="Holder Name" required>
                            <span class="modal-error-msg f-err" id="modal-error-holder_name"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Account Number <span class="req">*</span></label>
                            <input type="text" name="account_number" class="f-ctrl" placeholder="Account Number" required>
                            <span class="modal-error-msg f-err" id="modal-error-account_number"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Swift Code</label>
                            <input type="text" name="swift_code" class="f-ctrl" placeholder="Swift Code">
                            <span class="modal-error-msg f-err" id="modal-error-swift_code"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">IFSC Code <span class="req">*</span></label>
                            <input type="text" name="ifsc_code" class="f-ctrl" placeholder="IFSC Code" required>
                            <span class="modal-error-msg f-err" id="modal-error-ifsc_code"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Bank Name <span class="req">*</span></label>
                            <input type="text" name="bank_name" class="f-ctrl" placeholder="Bank Name" required>
                            <span class="modal-error-msg f-err" id="modal-error-bank_name"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Branch Name</label>
                            <input type="text" name="branch_name" class="f-ctrl" placeholder="Branch Name">
                            <span class="modal-error-msg f-err" id="modal-error-branch_name"></span>
                        </div>
                        <div class="col-12 mt-2 d-flex justify-content-end" style="gap:8px">
                            <button type="submit" class="btn-f btn-f-primary"><i class="ri-save-line"></i> Save</button>
                            <button type="reset"  class="btn-f btn-f-reset"><i class="ri-refresh-line"></i> Reset</button>
                            <button type="button" id="modalcancelbtn" class="btn-f btn-f-cancel">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Add Customer Modal --}}
<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-user-add-line mr-2"></i> Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <form id="customerform">
                    @csrf
                    <input type="hidden" name="token"      value="{{ session('api_token') }}">
                    <input type="hidden" name="user_id"    value="{{ $user_id }}">
                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">First Name <span class="withoutgstspan req">*</span></label>
                            <input type="text" class="f-ctrl withoutgstinput" id="firstname" name="firstname" placeholder="First Name" required>
                            <span class="modal-error-msg f-err" id="modal-error-firstname"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Last Name</label>
                            <input type="text" class="f-ctrl" id="lastname" name="lastname" placeholder="Last Name">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Company Name <span class="withgstspan req" style="display:none">*</span></label>
                            <input type="text" class="f-ctrl withgstiput" id="company_name" name="company_name" placeholder="Company Name">
                            <span class="modal-error-msg f-err" id="modal-error-company_name"></span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">GST Number</label>
                            <input type="text" class="f-ctrl" name="gst_number" id="gst_number" placeholder="GST Number">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Email</label>
                            <input type="email" class="f-ctrl" name="email" id="modal_email" placeholder="Email">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Contact Number</label>
                            <input type="tel" class="f-ctrl" name="contact_number" id="modal_exampleInputphone" placeholder="0123456789">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Country</label>
                            <select class="f-ctrl" name="country" id="modal_country"><option selected disabled>Select Country</option></select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">State</label>
                            <select class="f-ctrl" name="state" id="modal_state"><option selected disabled>Select State</option></select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">City</label>
                            <select class="f-ctrl" name="city" id="modal_city"><option selected disabled>Select City</option></select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Pincode</label>
                            <input type="text" class="f-ctrl" id="modal_pincode" name="pincode" placeholder="Pin Code">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">House No. / Building</label>
                            <textarea class="f-ctrl" name="house_no_building_name" id="house_no_building_name" rows="2" placeholder="e.g. 2nd floor / 04 ABC Apts"></textarea>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="f-label">Road / Area / Colony</label>
                            <textarea class="f-ctrl" name="road_name_area_colony" id="road_name_area_colony" rows="2" placeholder="Sardar Patel Road, Jagatpur"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2" style="gap:8px">
                        <button type="submit"  id="modal_submitBtn" class="btn-f btn-f-primary"><i class="ri-save-line"></i> Save</button>
                        <button id="modal_resetbtn"  type="reset"   class="btn-f btn-f-reset"><i class="ri-refresh-line"></i> Reset</button>
                        <button id="modal_cancelBtn" type="button"  class="btn-f btn-f-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ MAIN EDIT FORM ══════════════ --}}
{{-- <div class="page-edit-badge"><i class="ri-edit-2-line"></i> Editing Invoice</div> --}}

<form id="invoiceform">
    @csrf
    <input type="hidden" name="_method"    value="PUT">
    <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $edit_id }}">
    <input type="hidden" name="country_id" id="country">
    <input type="hidden" name="user_id"    id="created_by" value="{{ $user_id }}">
    <input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}">
    <input type="hidden" name="currency"   id="currency"   value="101">
    @unless(session('company_gst_no') && session('company_gst_no') != '')
        <input type="hidden" id="type" name="type" value="2">
    @endunless

    {{-- Party Details --}}
    <div class="inv-section">
        <div class="inv-section-head">
            <div class="ico"><i class="ri-user-3-line"></i></div>
            <h6>Party Details</h6>
        </div>
        <div class="inv-section-body">
            <div class="row">
                <div class="col-sm-4 mb-3">
                    <label class="f-label">Buyer <span class="req">*</span></label>
                    <select class="form-control select2" id="customer" name="customer" required>
                        <option selected disabled>Select Buyer</option>
                    </select>
                    <span class="f-err" id="error-customer"></span>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="f-label">Companymaster <span class="req">*</span></label>
                    <select class="form-control select2" id="companymaster_id" name="companymaster_id" required>
                        <option selected disabled>Select Companymaster</option>
                    </select>
                    <span class="f-err" id="error-companymaster_id"></span>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="f-label">Transport</label>
                    <select class="form-control select2" id="transport_id" name="transport_id">
                        <option selected disabled>Select Transport</option>
                    </select>
                    <span class="f-err" id="error-transport_id"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice Information --}}
    <div class="inv-section">
        <div class="inv-section-head">
            <div class="ico"><i class="ri-file-text-line"></i></div>
            <h6>Invoice Information</h6>
        </div>
        <div class="inv-section-body">
            <div class="row">
                @if (session('company_gst_no') && session('company_gst_no') != '')
                <div class="col-sm-4 mb-3">
                    <label class="f-label">Tax Type <span class="req">*</span></label>
                    <select class="f-ctrl" id="type" name="type" required>
                        <option disabled>Select Type</option>
                        <option value="1">GST</option>
                        <option value="2">Without GST</option>
                    </select>
                    <span class="f-err" id="error-tax_type"></span>
                </div>
                @endif
                <div class="col-sm-4 mb-3 d-none">
                    <label class="f-label">Bank Account</label>
                    <select class="f-ctrl" id="acc_details" name="acc_details">
                        <option selected disabled>Select Account</option>
                    </select>
                    <span class="f-err" id="error-bank_account"></span>
                </div>
                <div class="col-sm-4 mb-3" id="inv_number_div">
                    <label class="f-label">Invoice Number <span class="req">*</span></label>
                    <input type="text" name="inv_number" id="inv_number" class="f-ctrl" placeholder="e.g. INV-2024-001">
                    <span class="f-err" id="error-inv_number"></span>
                </div>
                <div class="col-sm-4 mb-3" id="inv_date_div">
                    <label class="f-label">Invoice Date <span class="req">*</span></label>
                    <input type="date" class="f-ctrl" id="invoice_date" name="invoice_date">
                    <span class="f-err" id="error-invoice_date"></span>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="f-label">Consignment Number</label>
                    <input type="text" name="consignment_number" id="consignment_number" class="f-ctrl" placeholder="Consignment Number">
                    <span class="f-err" id="error-consignment_number"></span>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="f-label">Consignment Date</label>
                    <input type="date" class="f-ctrl" id="consignment_date" name="consignment_date">
                    <span class="f-err" id="error-consignment_date"></span>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="f-label">HSN Code <span class="req">*</span></label>
                    <input type="text" name="HSN" id="HSN" class="f-ctrl" placeholder="HSN Code">
                    <span class="f-err" id="error-HSN"></span>
                </div>
                <div class="col-sm-4 mb-3">
                    <label class="f-label">Description <span class="req">*</span></label>
                    <input type="text" name="Description" id="Description" class="f-ctrl" placeholder="Description">
                    <span class="f-err" id="error-Description"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="li-wrap">
        <div class="li-topbar">
            <div class="li-topbar-left">
                <div class="ico"><i class="ri-list-check-2"></i></div>
                <h6>Line Items</h6>
                <span class="li-count-badge" id="li-count">0</span>
            </div>
        </div>

        {{-- <div class="li-quickbar">
            <span class="add_div">
                <button type="button" class="btn-add-row">
                    <i class="ri-add-circle-line"></i> Add New Item
                </button>
            </span>
            <div class="quickbar-sep"></div>
            <span class="quickbar-label">Product:</span>
            <div class="quickbar-sel" id="productdiv">
                <select class="form-control select2" id="product" name="product"></select>
            </div>
            <div class="quickbar-sep"></div>
            <span class="quickbar-label">LR:</span>
            <div class="quickbar-sel" id="lrdiv">
                <select class="form-control select2" id="lr" name="lr"></select>
            </div>
        </div> --}}

        <div class="li-list" id="li-list">
            <div id="li-empty" class="li-empty">
                <div class="li-empty-icon"><i class="ri-inbox-line"></i></div>
                <p><strong>No items yet.</strong> Use the buttons above to add items.</p>
            </div>
        </div>

        {{-- Totals --}}
        <div class="li-totals">
            <div class="totals-inner">
                <div class="t-row">
                    <span class="t-label">Subtotal</span>
                    <span class="t-val"><span class="t-sym currentcurrencysymbol"></span><input class="disableinput" type="number" step="any" name="total_amount" id="totalamount" readonly></span>
                </div>
                <div id="igstline" class="t-row" style="display:none">
                    <span class="t-label">IGST <span class="t-badge" id="igstpercentage"></span></span>
                    <span class="t-val"><span class="t-sym currentcurrencysymbol"></span><input class="disableinput" type="number" step="any" name="igst" id="igst" readonly></span>
                </div>
                <div id="sgstline" class="t-row" style="display:none">
                    <span class="t-label">SGST <span class="t-badge" id="sgstpercentage"></span></span>
                    <span class="t-val"><span class="t-sym currentcurrencysymbol"></span><input class="disableinput" type="number" step="any" name="sgst" id="sgst" readonly></span>
                </div>
                <div id="cgstline" class="t-row" style="display:none">
                    <span class="t-label">CGST <span class="t-badge" id="cgstpercentage"></span></span>
                    <span class="t-val"><span class="t-sym currentcurrencysymbol"></span><input class="disableinput" type="number" step="any" name="cgst" id="cgst" readonly></span>
                </div>
                <div id="gstline" class="t-row" style="display:none">
                    <span class="t-label">Total GST <span class="t-badge" id="gstpercentage"></span></span>
                    <span class="t-val"><span class="t-sym currentcurrencysymbol"></span><input class="disableinput" type="number" step="any" name="gst" id="gst" readonly></span>
                </div>
                <div id="roundoffline" class="t-row">
                    <span class="t-label">Roundoff</span>
                    <span class="t-val"><input class="disableinput" type="number" step="any" name="roundoff" id="roundoff" readonly></span>
                </div>
                <div id="grandtotalline" class="t-row grand">
                    <span class="t-label">Grand Total</span>
                    <span class="t-val"><span class="t-sym currentcurrencysymbol"></span><input class="disableinput" type="number" step="any" name="grandtotal" id="grandtotal" readonly></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes & Submit --}}
    <div class="inv-section">
        <div class="inv-section-body">
            <div class="mb-4">
                <label class="f-label">Notes</label>
                <textarea class="f-ctrl" name="notes" id="notes" rows="3" placeholder="Additional notes…"></textarea>
                <span class="f-err" id="error-notes"></span>
            </div>
            <div class="inv-footer">
                <button id="cancelbtn" type="button" class="btn-f btn-f-cancel"><i class="ri-close-line"></i> Cancel</button>
                <button type="reset"                 class="btn-f btn-f-reset"><i class="ri-refresh-line"></i> Reset</button>
                <button type="submit"                class="btn-f btn-f-success"><i class="ri-save-line"></i> Update Invoice</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('ajax')
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<script>
const API_TOKEN  = "{{ session()->get('api_token') }}";
const COMPANY_ID = "{{ session()->get('company_id') }}";
const USER_ID    = "{{ session()->get('user_id') }}";
const EDIT_ID    = @json($edit_id);

let companymaster_id;
let allColumnData      = [];
let allColumnNames     = [];
let hiddencolumn       = 0;
let formula            = [];
let productcolumnlinks = [];
let productdata        = [];
let lrdata             = [];
let lrcolumnlinks      = [];
let sgst, cgst, igst, gst;
let currentcurrency, currentcurrencysymbol;
let buyer_state_id, company_state_id;
let addname = 0;

/* ── helpers ─────────────────────────────── */
function ajaxPromise(m, url, data = {}) {
    return new Promise((res, rej) => ajaxRequest(m, url, data).done(res).fail(rej));
}
function updateCurrencySymbol() {
    currentcurrency       = $('#currency option:selected').data('currency');
    currentcurrencysymbol = $('#currency option:selected').data('symbol');
    $('.currentcurrencysymbol').text(currentcurrencysymbol || '');
}
function updateRowCount() {
    const n = $('#li-list .li-item-card').length;
    $('#li-count').text(n);
    n === 0 ? $('#li-empty').show() : $('#li-empty').hide();
}
function managetooltip() { $('[data-toggle="tooltip"]').tooltip('dispose').tooltip(); }

function applyRoundoff(totalval) {
    totalval = parseFloat(parseFloat(totalval).toFixed(2)) || 0;
    const grand = Math.round(totalval);
    const diff  = parseFloat((grand - totalval).toFixed(2));
    $('#grandtotal').val(grand);
    $('#roundoff').val(diff === 0 ? '0.00' : diff.toFixed(2));
}

/* ── columns & formula ───────────────────── */
async function getcolumn() {
    try {
        const r = await ajaxPromise('GET', "{{ route('invoice.columnname') }}", { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID });
        allColumnData  = r.columnname || [];
        hiddencolumn   = allColumnData.filter(c => c.is_hide === 1).length;
        allColumnNames = allColumnData.map(c => c.column_name);
        await setdata();
    } catch(e) { handleAjaxError(e); }
}
async function getformula() {
    try {
        const r = await ajaxPromise('GET', "{{ route('invoiceformula.index') }}", { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID });
        if (r.status == 200) formula = r.invoiceformula || [];
        await getcolumn();
    } catch(e) { handleAjaxError(e); }
}

/* ── build card ──────────────────────────── */
function buildCard(rowId, values, inventoryId, isLocked, showAction) {
    const orderDetailId = values.order_detail_id;
    let fieldsHtml = allColumnData.map(col => {
        const cn     = col.column_name.replace(/\s+/g, '_');
        const val    = (values[cn] !== undefined && values[cn] !== null) ? values[cn] : (col.default_value || '');
        const hidden = col.is_hide === 1;
         const lock   = (isLocked && cn !== 'shortage') ? 'disabled' : '';
        console.log(isLocked ,cn);
        if (hidden) return `<input type="hidden" name="${cn}_${rowId}" id="${cn}_${rowId}" value="${val}" data-oldproduct-id="${values.id||''}">`;
        const lbl = col.column_name === 'shortage' ? `${col.column_name} <small>(kg)</small>` : col.column_name;
        let ctrl = '';
        if (col.column_type === 'time') {
            ctrl = `<input type="time" name="${cn}_${rowId}" id="${cn}_${rowId}" value="${val}" data-oldproduct-id="${values.id||''}" class="f-ctrl iteam_${cn}" ${lock}>`;
        } else if (['number','percentage','decimal'].includes(col.column_type)) {
            ctrl = `<input type="number" step="any" min="0" name="${cn}_${rowId}" id="${cn}_${rowId}" value="${val}" data-id="${rowId}" data-oldproduct-id="${values.id||''}" class="f-ctrl iteam_${cn} counttotal calculation" ${lock}>`;
        } else if (col.column_type === 'longtext') {
            ctrl = `<textarea name="${cn}_${rowId}" id="${cn}_${rowId}" data-oldproduct-id="${values.id||''}" class="f-ctrl iteam_${cn}" rows="2" ${lock}>${val}</textarea>`;
        } else {
            ctrl = `<input type="text" name="${cn}_${rowId}" id="${cn}_${rowId}" value="${val}" placeholder="${col.column_name}" data-oldproduct-id="${values.id||''}" class="f-ctrl iteam_${cn}" ${lock}>`;
        }
        return `<div><label class="f-label">${lbl}</label>${ctrl}</div>`;
    }).join('');

    const moveBtns = `
        <button type="button" data-toggle="tooltip" data-original-title="Move Up"   class="btn-card btn-card-up table-up"><i class="fa fa-long-arrow-up"></i></button>
        <button type="button" data-toggle="tooltip" data-original-title="Move Down" class="btn-card btn-card-dn table-down"><i class="fa fa-long-arrow-down"></i></button>`;
    const editBtns = showAction ? `
        <button type="button" data-toggle="tooltip" data-original-title="Duplicate" class="btn-card btn-card-dup duplicate-row" data-id="${rowId}"><i class="ri-align-bottom"></i></button>
        <button type="button" data-toggle="tooltip" data-original-title="Delete"    class="btn-card btn-card-del remove-row"    data-id="${rowId}"><i class="ri-delete-bin-2-line"></i></button>` : '';

    const amountVal = values.amount || '';
    const currSym   = currentcurrencysymbol || '';

    const amountField = `
       <div class="amount-cell">
            <label class="f-label">Amount <span style="color:var(--c-danger)">*</span></label>
            <div class="amount-wrap">
                <span class="sym currentcurrencysymbol">${currSym}</span>
                <input type="number" step="any" min="0"
                    name="Amount_${rowId}" id="Amount_${rowId}"
                    value="${amountVal}" data-id="${rowId}"
                    data-oldproduct-id="${values.id||''}"
                    class="f-ctrl iteam_Amount changeprice calculation"
                    placeholder="0.00" required>
            </div>
        </div>`;

    return `<div class="li-item-card iteam_row_${rowId}" data-inventory="${inventoryId}" data-line-item-id="${values.line_item_id||values.id||''}">
        <input type="hidden" 
       name="rows[${rowId}][order_detail_id]" 
       id="order_detail_id_${rowId}" 
       value="${orderDetailId}">
        <div class="li-card-head">
            <div class="li-card-num">
                <span class="num-badge card-row-num"></span>
                <p class="card-row-label">Item</p>
            </div>
            <div class="li-card-actions">${moveBtns}${editBtns}</div>
        </div>
        <div class="li-card-body">
            <div class="li-field-grid">${fieldsHtml}${amountField}</div>
        </div>
    </div>`;
}

function renumberCards() {
    $('#li-list .li-item-card').each(function(i) {
        $(this).find('.card-row-num').text(i + 1);
        $(this).find('.card-row-label').text(`Item ${i + 1}`);
    });
}

/* ── load invoice data ───────────────────── */
async function setdata() {
    const url = "{{ route('invoice.edit', '__id__') }}".replace('__id__', EDIT_ID);
    try {
        const r = await ajaxPromise('GET', url, { token: API_TOKEN, company_id: COMPANY_ID, user_id: USER_ID });
        if (r.status == 200) {
            const inv  = r.data.invdetails;
            const rows = r.data.productdetails;

            // Pre-fill text fields
            $('#inv_number').val(inv.inv_no);
            $('#invoice_date').val(inv.inv_date_formatted);
            $('#consignment_number').val(inv.consignment_number);
            $('#consignment_date').val(inv.consignment_date_formatted);
            $('#HSN').val(inv.HSN);
            $('#Description').val(inv.Description);
            $('#notes').val(inv.notes);
            $('#currency').val(inv.currency_id);

            companymaster_id = inv.company_details_id;
            $('#bank_companymaster_id').val(companymaster_id);

            // Load dropdowns
            await customers(inv.customer_id);
            if (inv.transport_id) {
                await transports(inv.transport_id);
            } else {
                $('#transport_id').html(`<option selected value=0 disabled>Transport not Selected</option>`).prop('disabled', true);
            }
            await companymaster(inv.company_details_id);
            await loadBankDetails(inv.account_id);
            await getoverduedays();

            // Tax type
            const hasTax = (inv.cgst || inv.sgst || inv.igst || inv.gst);
            $('#type').val(hasTax ? 1 : 2).trigger('change');

            // Override GST from saved settings
            try {
                const gs = JSON.parse(inv.gstsettings || '{}');
                if (gs.sgst !== undefined) sgst = parseFloat(gs.sgst);
                if (gs.cgst !== undefined) cgst = parseFloat(gs.cgst);
                if (gs.igst !== undefined) igst = parseFloat(gs.igst);
                if (gs.gst  !== undefined) gst  = parseFloat(gs.gst);
            } catch(e) {}

            updateCurrencySymbol();

            // Build line items
            $.each(rows, function(k, v) {
                addname++;
                $('#li-empty').before(buildCard(addname, v, v.inventory_product_id || null, true, false));
                dynamiccalculaton(`#Amount_${addname}`);
                updateCurrencySymbol();
            });

            renumberCards();
            updateRowCount();
            managetooltip();
        }
    } catch(e) { handleAjaxError(e); }
    loaderhide();
}

/* ── dropdowns ───────────────────────────── */
async function transports(tid = 0) {
    try {
        $('#transport_id').html(`<option selected value=0 disabled>Select transport</option>`);
        const r = await ajaxPromise('GET', "{{ route('transport.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID });
        if (r.status == 200 && r.data != '') {
            $.each(r.data, function(k,v) {
                $('#transport_id').append(`<option data-gstno="${v.gst_no}" value="${v.id}">${[v.name,v.mobile_1,v.email].filter(Boolean).join(' - ')}</option>`);
            });
            $('#transport_id').val(tid).select2();
        }
    } catch(e) { handleAjaxError(e); }
}
async function customers(cid = 0) {
    try {
        $('#customer').html(`<option selected value=0 disabled>Select Buyer</option>`);
        const r = await ajaxPromise('GET', "{{ route('buyer.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID });
        if (r.status == 200 && r.data != '') {
            $.each(r.data, function(k,v) {
                $('#customer').append(`<option data-gstno="${v.gst_no}" value="${v.id}" data-state_id="${v.state_id}">${[v.name,v.mobile_1,v.email].filter(Boolean).join(' - ')}</option>`);
            });
            $('#customer').val(cid);
            buyer_state_id = $('#customer').find('option:selected').data('state_id');
            $('#customer').select2();
        }
    } catch(e) { handleAjaxError(e); }
}
async function companymaster(mid = 0) {
    try {
        $('#companymaster_id').html(`<option selected value=0 disabled>Select Companymaster</option>`);
        const r = await ajaxPromise('GET', "{{ route('companymaster.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID });
        if (r.status == 200 && r.data != '') {
            $.each(r.data, function(k,v) {
                $('#companymaster_id').append(`<option data-gstno="${v.gst_no}" value="${v.id}" data-state_id="${v.state_id}">${[v.company_name,v.mobile_1,v.email].filter(Boolean).join(' - ')}</option>`);
            });
            $('#companymaster_id').val(mid).select2();
            company_state_id = $('#companymaster_id').find('option:selected').data('state_id');
        }
    } catch(e) { handleAjaxError(e); }
}

/* ── calculation ─────────────────────────── */
function dynamiccalculaton(target) {
    let editid = null;
    try { const $t = $(target); editid = ($t && typeof $t.data === 'function') ? $t.data('id') : null; } catch(e) {}

    const rowData = {};
    if (editid) {
        allColumnNames.forEach(colName => {
            const cn  = colName.replace(/\s+/g, '_');
            const val = parseFloat($(`#${cn}_${editid}`).val()) || 0;
            rowData[colName] = val; rowData[cn] = val;
        });
    }
    formula.forEach(f => {
        const v1 = parseFloat(rowData[f.first_column]  ?? rowData[f.first_column.replace(/\s+/g,'_')]  ?? 0) || 0;
        const v2 = parseFloat(rowData[f.second_column] ?? rowData[f.second_column.replace(/\s+/g,'_')] ?? 0) || 0;
        let result = 0;
        switch(f.operation) {
            case '+': result = v1+v2; break; case '-': result = v1-v2; break;
            case '*': result = v1*v2; break; case '/': result = v2!==0 ? v1/v2 : 0; break;
        }
        const out = parseFloat(result.toFixed(3)), outKey = f.output_column.replace(/\s+/g,'_');
        rowData[f.output_column] = out; rowData[outKey] = out;
        if (editid) $(`#${outKey}_${editid}`).val(out);
    });

    let total = 0;
    $('input.changeprice').each(function() { const v = parseFloat($(this).val()); if (!isNaN(v)) total += v; });
    total = parseFloat(total.toFixed(2));
    $('#totalamount').val(total);

    if ($('#type').val() == 1) {
        const sv = parseFloat(((total*(sgst||0))/100).toFixed(2));
        const cv = parseFloat(((total*(cgst||0))/100).toFixed(2));
        const iv = parseFloat(((total*(igst||0))/100).toFixed(2));
        if (gst == 0) { $('#sgst').val(sv); $('#cgst').val(cv); $('#igst').val(iv); }
        else          { $('#gst').val((sv+cv+iv).toFixed(2)); }
        applyRoundoff(total+sv+cv+iv);
    } else {
        $('#sgst,#cgst,#igst,#gst').val(0);
        applyRoundoff(total);
    }
}

/* ── bank ────────────────────────────────── */
async function loadBankDetails(selectedId = null) {
    try {
        const $sel = $('#acc_details');
        $sel.empty().append(`<option value="">Select Bank</option><option value="add_new">+ Add New Bank</option>`);
        const r = await ajaxPromise('GET', "{{ route('bank_detail.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID, companymaster_id });
        if (r.status == 200 && r.bank && r.bank.length > 0) {
            r.bank.forEach(b => {
                const d = [b.account_no, b.branch_name, b.holder_name].filter(Boolean).join(' - ');
                $sel.find('option[value="add_new"]').before(`<option value="${b.id}" ${b.id==selectedId?'selected':''}>${d}</option>`);
            });
        }
        $sel.select2({
            width: '100%',
            placeholder: 'Select Bank',
            allowClear: true
        });
    } catch(e) { handleAjaxError(e); }
}

/* ── GST settings ────────────────────────── */
async function getoverduedays() {
    try {
        const r = await ajaxPromise('GET', "{{ route('getoverduedays.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID });
        if (r.status == 200 && r.overdueday != '') {
            const d = r.overdueday[0];
            sgst=d.sgst; cgst=d.cgst; igst=d.igst; gst=d.gst;

            if (company_state_id === buyer_state_id) {
                igst = 0; $('#igstline').hide(); $('#sgstline,#cgstline').show();
            } else {
                sgst = 0; cgst = 0; $('#sgstline,#cgstline').hide(); $('#igstline').show();
            }
            const tg = sgst+cgst+igst;
            $('#sgstpercentage').text(`${sgst%1===0?sgst+'.00':sgst} %`);
            $('#igstpercentage').text(`${igst%1===0?igst+'.00':igst} %`);
            $('#cgstpercentage').text(`${cgst%1===0?cgst+'.00':cgst} %`);
            $('#gstpercentage').text(`${tg%1===0?tg+'.00':tg} %`);
            if (gst != 0) { $('#sgstline,#cgstline,#igstline').hide(); $('#gstline').show(); }
            else          { $('#gstline').hide(); }
            if (d.invoice_number == 0) $('#inv_number_div').hide();
            if (d.invoice_date   == 0) $('#inv_date_div').hide();
        }
    } catch(e) { handleAjaxError(e); }
}

/* ── init ────────────────────────────────── */
$(document).ready(async function() {
    loadershow();

    ajaxRequest('GET', "{{ route('product.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID })
        .done(function(r) {
            if (r.status==200&&r.product!='') {
                productdata = r.product; let cnt=0;
                $.each(r.product, function(k,v) {
                    if (v.is_active==1) { cnt++;
                        $('#product').append(`<option id="product_option_${v.id}" ${v.track_quantity==0?'disabled':''} value="${v.id}">${v.name}${v.track_quantity==0?' - inventory not tracked':''}</option>`);
                    }
                });
                $('#product').val('').select2({placeholder:"Select Product",search:true});
                if (cnt<1) $('#productdiv').hide();
            } else $('#productdiv').hide();
        });

    ajaxRequest('GET', "{{ route('consignorcopy.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID })
        .done(function(r) {
            if (r.status==200&&r.data!='') {
                lrdata = r.data; let cnt=0;
                $.each(r.data, function(k,v) {
                    cnt++;
                    const d=[v.consignee,v.consignment_note_no,v.consignor,v.container_no,v.to_pay].filter(Boolean).join(' - ');
                    $('#lr').append(`<option value="${v.id}">${d}</option>`);
                });
                $('#lr').val('').select2({placeholder:"Select LR",search:true});
                if (cnt<1) $('#lrdiv').hide();
            } else $('#lrdiv').hide();
        });

    ajaxRequest('GET', "{{ route('productcolumnmapping.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID })
        .done(r => { if(r.status==200&&r.productcolumnmapping!='') productcolumnlinks=r.productcolumnmapping; });
    ajaxRequest('GET', "{{ route('lrcolumnmapping.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID })
        .done(r => { if(r.status==200&&r.lrcolumnmapping!='') lrcolumnlinks=r.lrcolumnmapping; });
    ajaxRequest('GET', "{{ route('country.index') }}", { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID })
        .done(function(r) {
            if (r.status==200&&r.country!='')
                $.each(r.country, function(k,v) {
                    $('#currency').append(`<option data-symbol="${v.currency_symbol}" data-currency="${v.currency}" value="${v.id}">${v.country_name} - ${v.currency_name} - ${v.currency} - ${v.currency_symbol}</option>`);
                });
        });

    await getformula();
});

$(function() {
    $(document).on('focus', '.calculation', function() { if ($(this).val()=='0') $(this).val(''); });
    $(document).on('blur',  '.calculation', function() { if ($(this).val()==='') $(this).val('0'); });

    $('#modalcancelbtn').on('click', () => { $('#bankdetailform')[0].reset(); $('#bankDetailModal').modal('hide'); $('#acc_details').select2('destroy'); });
    $(document).on('change','#acc_details', function() {
        if ($(this).val()==='add_new') { $('#bankDetailModal').modal('show'); $(this).val(''); }
    });

    $('#company_name').on('change keyup', function() {
        const v=$(this).val();
        if (v) { $('.withgstspan').show(); $('.withoutgstspan').hide(); $('.withgstinput').attr('required',true); $('.withoutgstinput').removeAttr('required'); }
        else   { $('.withgstspan').hide(); $('.withoutgstspan').show(); $('.withoutgstinput').attr('required',true); $('.withgstinput').removeAttr('required'); }
    });

    $('#customer').on('change', function() {
        loadershow(); const cid=$(this).val();
        if (cid=='add_customer') $('#exampleModalScrollable').modal('show');
        ajaxRequest('GET', "{{ route('party.search','__id__') }}".replace('__id__',cid), { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID })
            .done(r => { if(r.status==200&&r.party){const c=r.party.country_id;if(c){$('#country').val(c);$('#currency').val(c);updateCurrencySymbol();}} loaderhide(); })
            .fail(xhr => { loaderhide(); handleAjaxError(xhr); });
    });
    $('#companymaster_id').on('change', function() {
        loadershow(); const mid=$(this).val();
        ajaxRequest('GET', "{{ route('companymaster.search','__id__') }}".replace('__id__',mid), { token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID })
            .done(r => { if(r.status===200&&r.companymaster){const c=r.companymaster.country_id;if(c){$('#country').val(c);$('#currency').val(c);updateCurrencySymbol();}} loaderhide(); })
            .fail(xhr => { loaderhide(); handleAjaxError(xhr); });
    });
    $('#currency').on('change', updateCurrencySymbol);

    /* Add row */
    $('.add_div').on('click', function() {
        addname++;
        $('#li-empty').before(buildCard(addname, {}, null, false, true));
        renumberCards(); updateRowCount(); managetooltip();
        dynamiccalculaton(`#Amount_${addname}`);
    });
    /* Move */
    $(document).on('click','.table-up',   function() { const $c=$(this).closest('.li-item-card'),$p=$c.prev('.li-item-card');   if($p.length){$p.before($c);renumberCards();} });
    $(document).on('click','.table-down', function() { const $c=$(this).closest('.li-item-card'),$n=$c.next('.li-item-card');  if($n.length){$n.after($c);renumberCards();} });

    /* Duplicate */
    $(document).on('click','.duplicate-row', function() {
        const elem=$(this),id=elem.data('id');
        showConfirmationDialog('Are you sure?','to add a duplicate row?','Yes, add','No, cancel','question', function() {
            addname++;
            const inv=elem.closest('.li-item-card').data('inventory'), vals={};
            allColumnNames.forEach(cn=>{ const key=cn.replace(/\s+/g,'_'); vals[key]=$(`#${key}_${id}`).val()||''; });
            vals['amount']=$(`#Amount_${id}`).val()||'';
            $(`.iteam_row_${id}`).after(buildCard(addname,vals,inv,false,true));
            renumberCards(); updateRowCount(); managetooltip();
            dynamiccalculaton(`#Amount_${addname}`);
        });
    });

    /* Delete */
    $(document).on('click','.remove-row', function() {
        const elem=$(this);
        showConfirmationDialog('Are you sure?','to delete this item?','Yes, delete','No, cancel','question', function() {
            elem.closest('.li-item-card').remove();
            renumberCards(); updateRowCount(); managetooltip();
            dynamiccalculaton({ data: ()=>null });
        });
    });

    $(document).on('keyup change','.calculation', function() { dynamiccalculaton(this); });
    /* ── Shortage validation: cannot exceed Net_Oty_Per_Pkg * No_Of_Pkags ── */
    $(document).on('keyup change input', '.iteam_shortage', function () {
        const $input  = $(this);
        const rowId   = $input.attr('id').replace('shortage_', '');

        const netOty  = parseFloat($(`#Net_Oty_Per_Pkg_${rowId}`).val()) || 0;
        const noOfPkg = parseFloat($(`#No_Of_Pkags_${rowId}`).val())     || 0;
        const maxVal  = parseFloat((netOty * noOfPkg).toFixed(3));
        const entered = parseFloat($input.val()) || 0;

        if (maxVal > 0 && entered > maxVal) {
             $(`#Net_Weight_Kgs_${rowId}`).val(0);
             $(`#Amount_${rowId}`).val(0);
            // ── Show error only — do NOT cap the value ──
            $input.css('border-color', 'var(--c-danger)');

            let $err = $input.siblings('.shortage-err');
            if (!$err.length) {
                $input.after(`<span class="shortage-err f-err">Max shortage is ${maxVal}</span>`);
            } else {
                $err.text(`Max shortage is ${maxVal}`);
            }

            Toast.fire({
                icon:  'warning',
                title: `Shortage cannot exceed ${maxVal}`
            });
        } else {
            // ── Clear error when value is valid ──
            $input.css('border-color', '');
            $input.siblings('.shortage-err').remove();
        }
    });

    /* ── Re-validate shortage when No_Of_Pkags or Net_Oty_Per_Pkg change ── */
    $(document).on('keyup change input', '.iteam_No_Of_Pkags, .iteam_Net_Oty_Per_Pkg', function () {
        const rowId     = $(this).attr('id').replace(/^(No_Of_Pkags|Net_Oty_Per_Pkg)_/, '');
        const $shortage = $(`#shortage_${rowId}`);
        if ($shortage.length) $shortage.trigger('change');
    });
    /* GST toggle */
    $('#type').on('change', function() {
        if ($(this).val()==2) {
            $('#sgstline,#cgstline,#igstline,#gstline').hide(); $('#gst,#sgst,#cgst,#igst').val(0);
            applyRoundoff(parseFloat($('#totalamount').val())||0);
        } else {
            if (gst!=0){ $('#sgstline,#cgstline,#igstline').hide(); $('#gstline').show(); }
            else       { $('#sgstline,#cgstline,#igstline').show(); $('#gstline').hide(); }
            dynamiccalculaton({ data:()=>null });
        }
    });

    /* Product quick-add */
    $('#product').on('change', function() {
        const sel=$(this).val(); addname++;
        if (productcolumnlinks.length>1) {
            $.each(productdata, function(k,v) {
                if (v.id==sel) {
                    const vals={};
                    productcolumnlinks.forEach(lnk=>{ const key=lnk.invoice_column.replace(/\s+/g,'_'); vals[key]=v[lnk.product_column]||''; });
                    $('#li-empty').before(buildCard(addname,vals,v.id,false,true));
                    renumberCards(); updateRowCount(); managetooltip(); dynamiccalculaton(`#Amount_${addname}`);
                }
            });
        } else { Toast.fire({icon:"info",title:"Product column mapping required"}); }
        $('#product').val('').select2({placeholder:"Select Product",search:true});
    });

    /* LR quick-add */
    $('#lr').on('change', function() {
        const sel=$(this).val(); addname++;
        if (lrcolumnlinks.length>0) {
            $.each(lrdata, function(k,v) {
                if (v.id==sel) {
                    const vals={};
                    lrcolumnlinks.forEach(lnk=>{ const key=lnk.invoice_column.replace(/\s+/g,'_'); vals[key]=v[lnk.lr_column]??''; });
                    $('#li-empty').before(buildCard(addname,vals,v.id,false,true));
                    renumberCards(); updateRowCount(); managetooltip(); dynamiccalculaton(`#Amount_${addname}`);
                }
            });
        } else { Toast.fire({icon:"info",title:"LR column mapping required"}); }
        $('#lr').val('').trigger('change');
    });

    /* Collect row data */
    function collectRowData() {
        const iteam_data=[];
        $('#li-list .li-item-card').each(function() {
            const rowData={}, rowNumber=$(this).attr('class').match(/iteam_row_(\d+)/)[1];
            const inv=$(this).data('inventory'), lineItemId=$(this).data('line-item-id')||null;
            allColumnNames.forEach(cn=>{ const key=cn.replace(/\s+/g,'_'); rowData[key]=$(this).find(`#${key}_${rowNumber}`).val()||''; });
            rowData['amount']=$(this).find(`#Amount_${rowNumber}`).val();
            rowData['order_detail_id'] = $(this).find(`#order_detail_id_${rowNumber}`).val() || '';
            rowData['inventoryproduct']=inv; rowData['line_item_id']=lineItemId;
            iteam_data.push(rowData);
        });
        return iteam_data;
    }
    function collectInvoiceDetails() {
        return {
            invoice_id: EDIT_ID, country_id: $('#country').val(), user_id: $('#created_by').val(),
            company_id: $('#company_id').val(), companymaster_id: $('#companymaster_id').val(),
            transport_id: $('#transport_id').val(), HSN: $('#HSN').val(), Description: $('#Description').val(),
            bank_account: $('#acc_details').val(), invoice_date: $('#invoice_date').val(),
            inv_number: $('#inv_number').val(), consignment_date: $('#consignment_date').val(),
            consignment_number: $('#consignment_number').val(), currency: $('#currency').val(),
            customer: $('#customer').val(), total_amount: $('#totalamount').val(),
            grandtotal: $('#grandtotal').val(), roundoff: $('#roundoff').val(),
            tax_type: $('#type').val(), notes: $('#notes').val(),
            gstsettings: { sgst, cgst, igst, gst },
            ...(gst===0 ? { sgst:$('#sgst').val(), cgst:$('#cgst').val(), igst:$('#igst').val() } : { gst:$('#gst').val() })
        };
    }

    /* Submit */
    $('#invoiceform').submit(function(e) {
        e.preventDefault(); 
        
        if ($('.shortage-err').length > 0) {
            const $firstErr = $('.shortage-err').first();
            $('html, body').animate({
                scrollTop: $firstErr.offset().top - 140
            }, 400);

            // ✅ Highlight all shortage fields with errors
            $('.shortage-err').each(function() {
                $(this).siblings('input').css('border-color', 'var(--c-danger)');
            });

            Toast.fire({ icon: 'error', title: 'Please fix shortage errors before submitting' });
            return;
        }
        loadershow(); 
        $('.f-err').text('');
        
        const url = "{{ route('invoice.update','__id__') }}".replace('__id__', EDIT_ID);
        ajaxRequest('POST', url, {
            _method:'PUT', data:collectInvoiceDetails(), iteam_data:collectRowData(),
            token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID
        }).done(function(r) {
            if (r.status===200) { Toast.fire({icon:"success",title:r.message}); window.location="{{ route('admin.invoice') }}"; }
            else { Toast.fire({icon:"error",title:r.message}); loaderhide(); }
        }).fail(xhr=>{ loaderhide(); handleAjaxError(xhr); });
    });

    $('#inv_number').on('blur', function() {
        ajaxRequest('GET', "{{ route('invoice.checkinvoicenumber') }}", {
            inv_number:$(this).val(), inv_id:EDIT_ID, searchtype:'update',
            token:API_TOKEN, company_id:COMPANY_ID, user_id:USER_ID
        }).fail(xhr=>{ loaderhide(); handleAjaxError(xhr); });
    });

    $('#cancelbtn').on('click', ()=>{ loadershow(); window.location.href="{{ route('admin.invoice') }}"; });

    /* Customer modal — location dropdowns */
    ajaxRequest('GET', "{{ route('country.index') }}", { token:API_TOKEN })
        .done(function(r) {
            if (r.status==200&&r.country!='') {
                $.each(r.country,function(k,v){ $('#modal_country').append(`<option value="${v.id}">${v.country_name}</option>`); });
                $('#modal_country').val("{{ session('user')['country_id'] }}"); loadstate();
            }
            loaderhide();
        }).fail(xhr=>{ loaderhide(); handleAjaxError(xhr); });

    $('#modal_country').on('change', function() { loadershow(); $('#modal_city').html(`<option selected disabled>Select your City</option>`); loadstate($(this).val()); });
    function loadstate(id=0) {
        $('#modal_state').html(`<option selected disabled>Select your State</option>`);
        const url = id==0 ? "{{ route('state.search', session('user')['country_id']) }}" : "{{ route('state.search','id') }}".replace('id',id);
        ajaxRequest('GET',url,{token:API_TOKEN}).done(function(r) {
            if (r.status==200&&r.state!='') { $.each(r.state,function(k,v){ $('#modal_state').append(`<option value="${v.id}">${v.state_name}</option>`); }); if(id==0){ $('#modal_state').val("{{ session('user')['state_id'] }}"); loadcity(); } }
            loaderhide();
        }).fail(xhr=>{ loaderhide(); handleAjaxError(xhr); });
    }
    $('#modal_state').on('change', function() { loadershow(); loadcity($(this).val()); });
    function loadcity(id=0) {
        $('#modal_city').html(`<option selected disabled>Select your City</option>`);
        const url = id==0 ? "{{ route('city.search', session('user')['state_id']) }}" : "{{ route('city.search','id') }}".replace('id',id);
        ajaxRequest('GET',url,{token:API_TOKEN}).done(function(r) {
            if (r.status==200&&r.city!='') { $.each(r.city,function(k,v){ $('#modal_city').append(`<option value="${v.id}">${v.city_name}</option>`); }); if(id==0) $('#modal_city').val("{{ session('user')['city_id'] }}"); }
            loaderhide();
        }).fail(xhr=>{ loaderhide(); handleAjaxError(xhr); });
    }

    /* Bank form */
    $('#bankdetailform').submit(function(e) {
        e.preventDefault(); loadershow(); $('.modal-error-msg').text('');
        $.ajax({ type:'POST', url:"{{ route('bank_detail.store') }}", data:$(this).serialize(),
            success:function(r) {
                if(r.status==200){ $('#bankdetailform')[0].reset(); $('#bankDetailModal').modal('hide'); loadBankDetails(); Toast.fire({icon:"success",title:r.message}); }
                else Toast.fire({icon:"error",title:r.message});
                loaderhide();
            },
            error:xhr=>{ loaderhide(); handleModalAjaxError(xhr); }
        });
    });

    /* Customer form */
    $('#modal_cancelBtn').on('click', ()=>{ $('#customerform')[0].reset(); $('#exampleModalScrollable').modal('hide'); $('#customer option:first').prop('selected',true); });
    $('#customerform').submit(function(e) {
        e.preventDefault(); loadershow(); $('.modal-error-msg').text('');
        $.ajax({ type:'POST', url:"{{ route('customer.store') }}", data:$(this).serialize(),
            success:function(r) {
                if (r.status==200){ $('#customerform')[0].reset(); $('#exampleModalScrollable').modal('hide'); customers(r.customer_id); Toast.fire({icon:"success",title:r.message}); }
                else Toast.fire({icon:"error",title:r.message});
                loaderhide();
            },
            error:function(xhr){
                loaderhide();
                if (xhr.status===422) { const errors=xhr.responseJSON.errors; let ec; $.each(errors,function(k,v){ $('#modal-error-'+k).text(v[0]); ec='#modal-error-'+k; }); $('.modal-body').animate({scrollTop:$(ec).position().top},1000); }
                else Toast.fire({icon:"error",title:(JSON.parse(xhr.responseText).message||"An error occurred")});
            }
        });
    });
});
</script>
@endpush