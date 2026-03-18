@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update garden
@endsection
@section('title')
    Update garden
@endsection


@section('form-content')
    <form id="gardenupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">

                    <label for="garden_name">garden name</label>
                    <input type="text" id="garden_name" class="form-control" name='garden_name'
                        placeholder="company  Name">
                    <span class="error-msg" id="error-garden_name" style="color: red"></span>
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
                    <label for="country">Select Country</label>
                    <select class="form-control requiredinput" name='country' id="country">
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="state">Select State</label>
                    <select class="form-control requiredinput" name='state' id="state">
                        <option selected="" disabled="" value="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="city">Select City</label>
                    <select class="form-control requiredinput" name='city' id="city">
                        <option selected="" disabled="" value="">Select your City</option>
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
                        data-original-title="Reset garden Details"
                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save garden Details" class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('ajax')
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
    initSelect2('#country', 'Select your Country');
    initSelect2('#state',   'Select your State');
    initSelect2('#city',    'Select your City');

    // ── Load Countries ──────────────────────────────────────────────
    $.ajax({
        type: 'GET',
        url: "{{ route('country.index') }}",
        data: { token: "{{ session()->get('api_token') }}" },
        success: function (response) {
            if (response.status == 200 && response.country != '') {
                $.each(response.country, function (key, value) {
                    var option = new Option(value.country_name, value.id, false, false);
                    $('#country').append(option);
                });
                $('#country').trigger('change'); // refresh Select2
            } else {
                $('#country').append('<option disabled>No Data Found</option>');
            }
            loadGardenData(); // load edit data AFTER countries are ready
        },
        error: function (xhr) { handleAjaxError(xhr); }
    });

    // ── Load edit data and pre-fill form ────────────────────────────
    function loadGardenData() {
        var edit_id = @json($edit_id);
        var url = "{{ route('garden.edit', '__id__') }}".replace('__id__', edit_id);

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                token: "{{ session()->get('api_token') }}",
                company_id: "{{ session()->get('company_id') }}",
                user_id: "{{ session()->get('user_id') }}"
            },
            success: function (response) {
                if (response.status == 200) {
                    var data = response.garden;

                    // Fill plain inputs
                    $('#garden_name').val(data.garden_name);
                    $('#email').val(data.email);
                    $('#contact_person_name').val(data.contact_person_name);
                    $('#mobile_1').val(data.mobile_1);
                    $('#mobile_2').val(data.mobile_2);
                    $('#pincode').val(data.pincode);
                    $('#address').val(data.address);
                    $('#gst_no').val(data.gst_no);
                    $('#pan').val(data.pan);

                    // Set country and trigger Select2
                    if (data.country_id) {
                        $('#country').val(data.country_id).trigger('change');
                    }

                    // Load states, then pre-select state; inside that load cities and pre-select city
                    loadstate(data.country_id, data.state_id, data.city_id);

                } else {
                    Toast.fire({ icon: 'error', title: response.message });
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    }

    // ── Load States (with optional pre-select for state & city) ─────
    function loadstate(countryId, selectedStateId = null, selectedCityId = null) {
        $('#state').html('<option selected disabled value="">Select your State</option>').trigger('change');
        $('#city').html('<option selected disabled value="">Select your City</option>').trigger('change');

        if (!countryId) return;

        var url = "{{ route('state.search', '__id__') }}".replace('__id__', countryId);
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

                    if (selectedStateId) {
                        $('#state').val(selectedStateId).trigger('change'); // pre-select + refresh Select2
                        loadcity(selectedStateId, selectedCityId);          // load cities for this state
                    } else {
                        $('#state').trigger('change');
                    }
                } else {
                    $('#state').append('<option disabled>No Data Found</option>').trigger('change');
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    }

    // ── Load Cities (with optional pre-select) ──────────────────────
    function loadcity(stateId, selectedCityId = null) {
        $('#city').html('<option selected disabled value="">Select your City</option>').trigger('change');

        if (!stateId) return;

        var url = "{{ route('city.search', '__id__') }}".replace('__id__', stateId);
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

                    if (selectedCityId) {
                        $('#city').val(selectedCityId).trigger('change'); // pre-select + refresh Select2
                    } else {
                        $('#city').trigger('change');
                    }
                } else {
                    $('#city').append('<option disabled>No Data Found</option>').trigger('change');
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    }

    // ── Country change (manual) ─────────────────────────────────────
    $('#country').on('change', function () {
        var countryId = $(this).val();
        if (!countryId) return;
        loadershow();
        loadstate(countryId);
    });

    // ── State change (manual) ───────────────────────────────────────
    $('#state').on('change', function () {
        var stateId = $(this).val();
        if (!stateId) return;
        loadershow();
        loadcity(stateId);
    });

    // ── Cancel ──────────────────────────────────────────────────────
    $('#cancelbtn').on('click', function () {
        loadershow();
        window.location.href = "{{ route('admin.garden') }}";
    });

    // ── Form Submit ─────────────────────────────────────────────────
    $('#gardenupdateform').submit(function (event) {
        event.preventDefault();
        loadershow();
        $('.error-msg').text('');
        $.ajax({
            type: 'PUT',
            url: "{{ route('garden.update', $edit_id) }}",
            data: $(this).serialize(),
            success: function (response) {
                if (response.status == 200) {
                    Toast.fire({ icon: 'success', title: response.message });
                    window.location.href = "{{ route('admin.garden') }}";
                } else {
                    Toast.fire({ icon: 'error', title: response.message });
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    });

});
</script>
@endpush
