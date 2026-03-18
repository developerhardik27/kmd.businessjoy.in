@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Party
@endsection
@section('title')
    New Party
@endsection


@section('form-content')
    <form id="partyform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <label for="party_type">Select type</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name='party_type' id="party_type">
                        <option selected="" disabled="">Select your party type</option>
                        <option value = "Transport">Transport</option>
                        <option value = "Buyer">Buyer </option>
                    </select>
                    <span class="error-msg" id="error-party_type" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">

                    <label for="name">Name</label><span style="color:red;">*</span>
                    <input type="text" id="name" class="form-control" name='name' placeholder=" Name">
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="email">Email</label>
                    <input type="email" class="form-control requiredinput" name="email" id="email"
                        placeholder="Enter Email">
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="contact_person_name">Contact Person Name</label>
                    <input type="text" class="form-control requiredinput" name="contact_person_name"
                        id="contact_person_name" placeholder="Enter Contact Person Name">
                    <span class="error-msg" id="error-contact_person_name" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="mobile_1">Mobile 1</label>
                    <input type="tel" class="form-control requiredinput" name='mobile_1' id="mobile_1"
                        placeholder="0123456789">
                    <span class="error-msg" id="error-mobile_1" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="mobile_2">Mobile 2</label>
                    <input type="tel" class="form-control requiredinput" name='mobile_2' id="mobile_2"
                        placeholder="0123456789">
                    <span class="error-msg" id="error-mobile_2" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="country">Select Country</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name='country' id="country">
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="state">Select State</label>
                    <select class="form-control requiredinput" name='state' id="state">
                        <option selected="" disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="city">Select City</label>
                    <select class="form-control requiredinput" name='city' id="city">
                        <option selected="" disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="pincode">Pincode</label>
                    <input type="text" id="pincode" name='pincode' class="form-control requiredinput"
                        placeholder="Pin Code">
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="address">Address</label>
                    <textarea class="form-control requiredinput" name='address' id="address" rows="2"
                        placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                    <span class="error-msg" id="error-address" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="gst_no">GST Number</label>
                    <input type="text" id="gst_no" class="form-control" name='gst_no' id=""
                        placeholder="GST Number">
                    <span class="error-msg" id="error-gst_no" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="pan">PAN Number</label>
                    <input type="text" id="pan" class="form-control" name='pan' id=""
                        placeholder="PAN Number">
                    <span class="error-msg" id="error-pan" style="color: red"></span>
                </div>
                
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset party Details" class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save party Details" class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('ajax')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('document').ready(function () {

    // ── Initialize Select2 ──────────────────────────────────────────
    function initSelect2(selector, placeholder) {
        $(selector).select2({
            placeholder: placeholder,
            allowClear: true,
            width: '100%'
        });
    }
    initSelect2('#party_type', 'Select your Party Type');
    initSelect2('#country',    'Select your Country');
    initSelect2('#state',      'Select your State');
    initSelect2('#city',       'Select your City');

    // ── Session message ─────────────────────────────────────────────
    let message = "{{ session('message') }}";
    if (message) {
        Toast.fire({ icon: 'error', title: message });
    }

    // ── Party type change (asterisk toggle) ─────────────────────────
    $('#party_type').on('change', function () {
        var type = $(this).val();
        if (type === 'Transport') {
            $('#state').closest('.col-sm-6').find('label span.required-star').remove();
            $('#error-state').text('');
        } else if (type === 'Buyer') {
            if ($('#state').closest('.col-sm-6').find('label span.required-star').length === 0) {
                $('#state').closest('.col-sm-6').find('label').append('<span class="required-star" style="color:red;">*</span>');
                $('#error-state').text('');
            }
        }
    });

    // ── Load Countries (India only, pre-selected) ───────────────────
    $.ajax({
        type: 'GET',
        url: "{{ route('country.index') }}",
        data: { token: "{{ session()->get('api_token') }}" },
        success: function (response) {
            if (response.status == 200 && response.country != '') {
                $.each(response.country, function (key, value) {
                    if (value.id == 101) {
                        var option = new Option(value.country_name, value.id, true, true);
                        $('#country').append(option).trigger('change'); // pre-select + refresh Select2
                    }
                });
                loadstate(); // load states for India
            } else {
                $('#country').append('<option disabled>No Data Found</option>').trigger('change');
            }
            loaderhide();
        },
        error: function (xhr) {
            loaderhide();
            handleAjaxError(xhr);
        }
    });

    // ── Country change → reload states ─────────────────────────────
    $('#country').on('change', function () {
        var country_id = $(this).val();
        if (!country_id) return;
        loadershow();
        $('#state').html('<option selected disabled>Select your State</option>').trigger('change');
        $('#city').html('<option selected disabled>Select your City</option>').trigger('change');
        loadstate(country_id);
    });

    // ── Load States ─────────────────────────────────────────────────
    function loadstate(id = 0) {
        $('#state').html('<option selected disabled>Select your State</option>').trigger('change');
        $('#city').html('<option selected disabled>Select your City</option>').trigger('change');

        var url = (id == 0)
            ? "{{ route('state.search', session('user')['country_id']) }}"
            : "{{ route('state.search', '__id__') }}".replace('__id__', id);

        $.ajax({
            type: 'GET',
            url: url,
            data: { token: "{{ session()->get('api_token') }}" },
            success: function (response) {
                if (response.status == 200 && response.state != '') {
                    $.each(response.state, function (key, value) {
                        var option = new Option(value.state_name, value.id, false, false);
                        $('#state').append(option);
                    });
                    $('#state').trigger('change'); // refresh Select2 after appending
                    if (id == 0) {
                        loadcity(); // auto-load cities for default state
                    }
                } else {
                    $('#state').append('<option disabled>No Data Found</option>').trigger('change');
                }
                loaderhide();
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    }

    // ── State change → reload cities ────────────────────────────────
    $('#state').on('change', function () {
        var state_id = $(this).val();
        if (!state_id) return;
        loadershow();
        loadcity(state_id);
    });

    // ── Load Cities ─────────────────────────────────────────────────
    function loadcity(id = 0) {
        $('#city').html('<option selected disabled>Select your City</option>').trigger('change');

        var url = (id == 0)
            ? "{{ route('city.search', session('user')['state_id']) }}"
            : "{{ route('city.search', '__id__') }}".replace('__id__', id);

        $.ajax({
            type: 'GET',
            url: url,
            data: { token: "{{ session()->get('api_token') }}" },
            success: function (response) {
                if (response.status == 200 && response.city != '') {
                    $.each(response.city, function (key, value) {
                        var option = new Option(value.city_name, value.id, false, false);
                        $('#city').append(option);
                    });
                    $('#city').trigger('change'); // refresh Select2 after appending
                } else {
                    $('#city').append('<option disabled>No Data Found</option>').trigger('change');
                }
                loaderhide();
            },
            error: function (xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }
        });
    }

    // ── Cancel button ────────────────────────────────────────────────
    $('#cancelbtn').on('click', function () {
        loadershow();
        window.location.href = "{{ route('admin.party') }}";
    });

    // ── Form submit ──────────────────────────────────────────────────
    $('#partyform').submit(function (event) {
        event.preventDefault();
        loadershow();
        $('.error-msg').text('');
        $.ajax({
            type: 'POST',
            url: "{{ route('party.store') }}",
            data: $(this).serialize(),
            success: function (response) {
                if (response.status == 200) {
                    Toast.fire({ icon: 'success', title: response.message });
                    window.location.href = "{{ route('admin.party') }}";
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

});
</script>
@endpush
