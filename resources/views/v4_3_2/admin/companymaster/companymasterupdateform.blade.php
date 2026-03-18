@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update companymaster
@endsection
@section('title')
    Update companymaster
@endsection


@section('form-content')
    <form id="companymasterupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">

                    <label for="company_name">Company name</label>
                    <input type="text" id="company_name" class="form-control" name='company_name'
                        placeholder="company  Name">
                    <span class="error-msg" id="error-company_name" style="color: red"></span>
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
                    <label for="country">Select Country</label><span class="" style="color:red;">*</span>
                    <select class="form-control requiredinput country" name="country" id="country" data-target="state">
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="state">Select State</label><span class="" style="color:red;">*</span>
                    <select class="form-control requiredinput state" name="state" id="state" data-target="city">

                        <option selected="" disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="city">Select City</label>
                    <select class="form-control requiredinput" name="city" id="city">

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
                    <label for="address">address</label>
                    <textarea class="form-control requiredinput" name='address' id="address" rows="2"
                        placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                    <span class="error-msg" id="error-address" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="gst_no">GST Number</label>
                    <input type="text" id="gst_no" class="form-control" name='gst_no' id="gst_no"
                        placeholder="GST Number">
                    <span class="error-msg" id="error-gst_no" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="pan">PAN Number</label>
                    <input type="text" id="pan" class="form-control" name='pan'
                        id="pan"placeholder="PAN Number">
                    <span class="error-msg" id="error-pan" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="garden_id">Select Garden</label>
                    <select class="form-control requiredinput" name="garden_id[]" id="garden_id" multiple>
                    </select>
                    <span class="error-msg" id="error-garden_id" style="color:red"></span>
                </div>
                  <div class="col-sm-6 mb-2">
                    <label for="brokerage">Brokerage</label>
                    <input type="text" id="brokerage" class="form-control" name='brokerage' id=""
                        placeholder="Brokerage Number">
                    <span class="error-msg" id="error-brokerage" style="color: red"></span>
                </div>
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset companymaster Details"
                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save companymaster Details"
                        class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade" id="gardenModal" tabindex="-1" role="dialog" aria-labelledby="gardenModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Garden</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="gardenform">
                        @csrf
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" placeholder="token" required />
                                    <input type="hidden" value="{{ session('user_id') }}" class="form-control"
                                        name="user_id">
                                    <input type="hidden" value="{{ session('company_id') }}" class="form-control"
                                        name="company_id">

                                    <label for="garden_name">garden name</label><span   
                                        style="color:red;">*</span>
                                    <input type="text" id="garden_name" class="form-control withoutgstinput"
                                        name='garden_name' placeholder="garden  Name">
                                    <span class="modal-error-msg" id="modal-error-garden_name" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control requiredinput" name="email"
                                        id="email" placeholder="Enter Email">
                                    <span class="modal-error-msg" id="modal-error-email" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="contact_person_name">Contact Person Name</label>
                                    <input type="text" class="form-control requiredinput" name="contact_person_name"
                                        id="contact_person_name" placeholder="Enter Contact Person Name">
                                    <span class="modal-error-msg" id="modal-error-contact_person_name"
                                        style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="mobile_1">Mobile 1</label>
                                    <input type="tel" class="form-control requiredinput" name='mobile_1'
                                        id="mobile_1" placeholder="0123456789">
                                    <span class="modal-error-msg" id="modal-error-mobile_1" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="mobile_2">Mobile 2</label>
                                    <input type="tel" class="form-control requiredinput" name='mobile_2'
                                        id="mobile_2" placeholder="0123456789">
                                    <span class="modal-error-msg" id="modal-error-mobile_2" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="country">Select Country</label>
                                    <select class="form-control requiredinput country" id="modal_country"
                                        data-target="modal_state">

                                        <option selected="" disabled="">Select your Country</option>
                                    </select>
                                    <span class="modal-error-msg" id="modal-error-country" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="state">Select State</label>
                                    <select class="form-control requiredinput state" id="modal_state"
                                        data-target="modal_city">

                                        <option selected="" disabled="">Select your State</option>
                                    </select>
                                    <span class="modal-error-msg" id="modal-error-state" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="city">Select City</label>
                                    <select class="form-control requiredinput state" id="modal_city">

                                        <option selected="" disabled="">Select your City</option>
                                    </select>
                                    <span class="modal-error-msg" id="modal-error-city" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" id="pincode" name='pincode'
                                        class="form-control requiredinput" placeholder="Pin Code">
                                    <span class="modal-error-msg" id="modal-error-pincode" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="address">Address</label>
                                    <textarea class="form-control requiredinput" name='address' id="address" rows="2"
                                        placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                    <span class="modal-error-msg" id="modal-error-address" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="gst_no">GST Number</label>
                                    <input type="text" id="gst_no" class="form-control" name='gst_no'
                                        id="" placeholder="GST Number">
                                    <span class="modal-error-msg" id="modal-error-gst_no" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="pan">PAN Number</label>
                                    <input type="text" id="pan" class="form-control" name='pan'
                                        id="" placeholder="PAN Number">
                                    <span class="modal-error-msg" id="modal-error-pan" style="color: red"></span>
                                </div>
                                <div class="col-sm-12">
                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Cancel" id="modal_cancelBtn"
                                        class="btn btn-secondary float-right">Cancel</button>
                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Reset garden Details"
                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Save garden Details"
                                        class="btn btn-primary float-right my-0">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('document').ready(function () {

    // ── Initialize Select2 ──────────────────────────────────────────
    function initSelect2(selector, placeholder, parent = null) {
        var config = {
            placeholder: placeholder,
            allowClear: true,
            width: '100%'
        };
        if (parent) config.dropdownParent = $(parent); // required for modal
        $(selector).select2(config);
    }

    // Main form dropdowns
    initSelect2('#country', 'Select your Country');
    initSelect2('#state',   'Select your State');
    initSelect2('#city',    'Select your City');

    // Garden multi-select
    $('#garden_id').select2({
        placeholder: 'Select Gardens',
        allowClear: true,
        width: '100%'
    });

    // ── Helper: populate a select with options ──────────────────────
    function populateSelect(selector, data, nameKey, placeholder, selectedId = null) {
        $(selector).html(`<option selected disabled value="">${placeholder}</option>`);
        $.each(data, function (key, value) {
            var option = new Option(value[nameKey], value.id, false, false);
            $(selector).append(option);
        });
        if (selectedId) {
            $(selector).val(selectedId);
        }
        $(selector).trigger('change'); // refresh Select2
    }

    // ── Load Countries ──────────────────────────────────────────────
    function loadcountry(countrySelector, stateSelector, citySelector, selectedCountryId = null, selectedStateId = null, selectedCityId = null) {
        loadershow();
        $.ajax({
            type: 'GET',
            url: "{{ route('country.index') }}",
            data: { token: "{{ session()->get('api_token') }}" },
            success: function (response) {
                if (response.status == 200 && response.country != '') {
                    populateSelect(countrySelector, response.country, 'country_name', 'Select your Country', selectedCountryId);
                    if (selectedCountryId) {
                        loadstate(selectedCountryId, stateSelector, citySelector, selectedStateId, selectedCityId);
                    }
                } else {
                    $(countrySelector).append('<option disabled>No Data Found</option>').trigger('change');
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    }

    // ── Load States ─────────────────────────────────────────────────
    function loadstate(countryId, stateSelector, citySelector, selectedStateId = null, selectedCityId = null) {
        $(stateSelector).html('<option selected disabled value="">Select your State</option>').trigger('change');
        $(citySelector).html('<option selected disabled value="">Select your City</option>').trigger('change');

        if (!countryId) return;

        $.ajax({
            type: 'GET',
            url: "{{ route('state.search', '__id__') }}".replace('__id__', countryId),
            data: { token: "{{ session()->get('api_token') }}" },
            success: function (response) {
                if (response.status == 200 && response.state != '') {
                    populateSelect(stateSelector, response.state, 'state_name', 'Select your State', selectedStateId);
                    if (selectedStateId) {
                        loadcity(selectedStateId, citySelector, selectedCityId);
                    }
                } else {
                    $(stateSelector).append('<option disabled>No Data Found</option>').trigger('change');
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    }

    // ── Load Cities ─────────────────────────────────────────────────
    function loadcity(stateId, citySelector, selectedCityId = null) {
        $(citySelector).html('<option selected disabled value="">Select your City</option>').trigger('change');

        if (!stateId) return;

        $.ajax({
            type: 'GET',
            url: "{{ route('city.search', '__id__') }}".replace('__id__', stateId),
            data: { token: "{{ session()->get('api_token') }}" },
            success: function (response) {
                if (response.status == 200 && response.city != '') {
                    populateSelect(citySelector, response.city, 'city_name', 'Select your City', selectedCityId);
                } else {
                    $(citySelector).append('<option disabled>No Data Found</option>').trigger('change');
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
        });
    }

    // ── Main form: country change ───────────────────────────────────
    $('#country').on('change', function () {
        var countryId = $(this).val();
        if (!countryId) return;
        loadershow();
        loadstate(countryId, '#state', '#city');
    });

    // ── Main form: state change ─────────────────────────────────────
    $('#state').on('change', function () {
        var stateId = $(this).val();
        if (!stateId) return;
        loadershow();
        loadcity(stateId, '#city');
    });

    // ── Load edit data and pre-fill form ────────────────────────────
    var edit_id = @json($edit_id);

    $.ajax({
        type: 'GET',
        url: "{{ route('companymaster.edit', '__id__') }}".replace('__id__', edit_id),
        data: {
            token: "{{ session()->get('api_token') }}",
            company_id: "{{ session()->get('company_id') }}",
            user_id: "{{ session()->get('user_id') }}"
        },
        success: function (response) {
            if (response.status == 200) {
                var data = response.companymaster;
                var gardenId = response.gardenId;

                // Fill plain inputs
                $('#company_name').val(data.company_name);
                $('#email').val(data.email);
                $('#contact_person_name').val(data.contact_person_name);
                $('#mobile_1').val(data.mobile_1);
                $('#mobile_2').val(data.mobile_2);
                $('#pincode').val(data.pincode);
                $('#address').val(data.address);
                $('#gst_no').val(data.gst_no);
                $('#pan').val(data.pan);
                $('#brokerage').val(data.brokerage);

                // Load country → state → city with pre-selection
                loadcountry('#country', '#state', '#city', data.country_id, data.state_id, data.city_id);

                // Load gardens with pre-selection
                fetchGardens(gardenId);

            } else {
                Toast.fire({ icon: 'error', title: response.message });
            }
            loaderhide();
        },
        error: function (xhr) { loaderhide(); handleAjaxError(xhr); }
    });

    // ── Garden select: fetch gardens ────────────────────────────────
    let oldSelectedGardenId = [];

    function fetchGardens(selectedGardenIds = []) {
        if (!Array.isArray(selectedGardenIds)) selectedGardenIds = [selectedGardenIds];
        selectedGardenIds = selectedGardenIds.map(String);

        $.ajax({
            url: "{{ route('garden.editgardenload') }}",
            type: 'GET',
            data: {
                user_id: "{{ session()->get('user_id') }}",
                company_id: "{{ session()->get('company_id') }}",
                token: "{{ session()->get('api_token') }}",
                companymaster_id: edit_id
            },
            success: function (response) {
                var gardenSelect = $('#garden_id');
                gardenSelect.empty();
                gardenSelect.append(new Option('+ Add New Garden', 'add_new', false, false));

                if (response.data && response.data.length > 0) {
                    $.each(response.data, function (index, garden) {
                        gardenSelect.append(new Option(garden.garden_name, garden.id, false, false));
                    });
                }

                var finalSelection = oldSelectedGardenId.slice();
                selectedGardenIds.forEach(function (id) {
                    if (!finalSelection.includes(id)) finalSelection.push(id);
                });

                gardenSelect.val(finalSelection).trigger('change');
            }
        });
    }

    // ── Garden select: intercept "Add New" option ───────────────────
    $(document).on('change', '#garden_id', function () {
        oldSelectedGardenId = ($(this).val() || []).filter(id => id !== 'add_new');
        if (($(this).val() || []).includes('add_new')) {
            $('#gardenModal').modal('show');
            $(this).val(oldSelectedGardenId).trigger('change');
        }
    });

    // ── Modal: init Select2 when modal opens ────────────────────────
    $('#gardenModal').on('shown.bs.modal', function () {
        // Init with dropdownParent so dropdown renders inside modal
        initSelect2('#modal_country', 'Select your Country', '#gardenModal');
        initSelect2('#modal_state',   'Select your State',   '#gardenModal');
        initSelect2('#modal_city',    'Select your City',    '#gardenModal');
         var sessionCountry = "{{ session('user')['country_id'] ?? '' }}";
        // Load countries for modal (no pre-selection needed for add)
       loadcountry('#modal_country', '#modal_state', '#modal_city', sessionCountry);
    });

    // ── Modal: country change ───────────────────────────────────────
    $('#modal_country').on('change', function () {
        var countryId = $(this).val();
        if (!countryId) return;
        loadershow();
        loadstate(countryId, '#modal_state', '#modal_city');
    });

    // ── Modal: state change ─────────────────────────────────────────
    $('#modal_state').on('change', function () {
        var stateId = $(this).val();
        if (!stateId) return;
        loadershow();
        loadcity(stateId, '#modal_city');
    });

    // ── Modal: reset on hide ────────────────────────────────────────
    $('#gardenModal').on('hidden.bs.modal', function () {
        $('#gardenform')[0].reset();
        // Destroy Select2 so they re-initialize fresh on next open
        ['#modal_country', '#modal_state', '#modal_city'].forEach(function (sel) {
            if ($(sel).data('select2')) $(sel).select2('destroy');
            $(sel).html('<option selected disabled>Select your ' + sel.replace('#modal_', '') + '</option>');
        });
    });

    // ── Modal cancel button ─────────────────────────────────────────
    $('#modal_cancelBtn').on('click', function () {
        $('#gardenModal').modal('hide');
    });

    // ── Garden modal form submit ────────────────────────────────────
    $('#gardenform').submit(function (event) {
        event.preventDefault();
        loadershow();
        $('.modal-error-msg').text('');
        $.ajax({
            type: 'POST',
            url: "{{ route('garden.store') }}",
            data: $(this).serialize(),
            success: function (response) {
                if (response.status == 200) {
                    Toast.fire({ icon: 'success', title: response.message });
                    $('#gardenModal').modal('hide');
                    fetchGardens([String(response.garden_id)]);
                } else {
                    Toast.fire({ icon: 'error', title: response.message });
                }
                loaderhide();
            },
            error: function (xhr) { loaderhide(); handleModalAjaxError(xhr); }
        });
    });

    // ── Cancel main form ────────────────────────────────────────────
    $('#cancelbtn').on('click', function () {
        loadershow();
        window.location.href = "{{ route('admin.companymaster') }}";
    });

    // ── Main company update form submit ─────────────────────────────
    $('#companymasterupdateform').submit(function (event) {
        event.preventDefault();
        loadershow();
        $('.error-msg').text('');
        $.ajax({
            type: 'PUT',
            url: "{{ route('companymaster.update', $edit_id) }}",
            data: $(this).serialize(),
            success: function (response) {
                if (response.status == 200) {
                    Toast.fire({ icon: 'success', title: response.message });
                    window.location.href = "{{ route('admin.companymaster') }}";
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
