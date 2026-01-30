@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Company
@endsection
@section('title')
    New Company
@endsection


@section('form-content')
    <form id="Companyform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">

                    <label for="company_name">Company name</label><span class="" style="color:red;">*</span>
                    <input type="text" id="company_name" class="form-control " name='company_name'
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
                    <label for="country">Select Country</label>
                    <select class="form-control requiredinput country" name='country' id="country" data-target="state">
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="state">Select State</label>
                    <select class="form-control requiredinput state" name='state' id="state" data-target="city">
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
                <div class="col-sm-6 mb-2">
                    <label for="garden_id">Select Garden</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="garden_id[]" id="garden_id" multiple>
                    </select>
                    <span class="error-msg" id="error-garden_id" style="color:red"></span>
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

                                    <label for="garden_name">garden name</label><span class="withoutgstspan"
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
                                    <select class="form-control requiredinput country" name='country' id="modal_country"
                                        data-target="modal_state">
                                        <option selected="" disabled="">Select your Country</option>
                                    </select>
                                    <span class="modal-error-msg" id="modal-error-country" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="state">Select State</label>
                                    <select class="form-control requiredinput state" name='state' id="modal_state"
                                        data-target="modal_city">
                                        <option selected="" disabled="">Select your State</option>
                                    </select>
                                    <span class="modal-error-msg" id="modal-error-state" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="city">Select City</label>
                                    <select class="form-control requiredinput" name='city' id="modal_city">
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
                                    <label for="address">address</label>
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
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            // companymaster form  -> dynamic required attribute (if enter company name then only company name required otherwise only firstname)
            $('.withgstspan').hide();
            $('#company_name').on('change keyup', function() {
                var val = $(this).val();
                if (val != '') {
                    $('.withgstspan').show();
                    $('.withoutgstspan').hide();
                    $('.withgstinput').attr('required', true);
                    $('.withoutgstinput').removeAttr('required');
                } else {
                    $('.withgstspan').hide();
                    $('.withoutgstspan').show();
                    $('.withoutgstinput').attr('required', true);
                    $('.withgstinput').removeAttr('required');
                }
            });
            let country;

            function renderSelectBox(data, type, subtype = null) {
                $(`#${subtype || type}`).html(
                    `<option selected="" disabled="">Select your ${type}</option>`
                );
                $.each(data, function(key, value) {
                    let fill_name = value[`${type}_name`];

                    $(`#${subtype || type}`).append(
                        `<option value="${value.id}">${fill_name}</option>`
                    );
                });

            }

            // show country data in dropdown and set defautl value according to logged in user
            function loadcountry(type = null) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('country.index') }}",
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.country != '') {
                            country = response.country;
                            // You can update your HTML with the data here if needed
                            renderSelectBox(country, 'country', type);
                            country_id = "{{ session('user')['country_id'] }}";

                            $(`#${type|| 'country'}`).val(country_id);
                            loadstate(0, type ? 'modal_state' : null);
                        } else {
                            $('#country').append(`<option> No Data Found</option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }
            loadcountry();
            // load state in dropdown when country change
            $('.country').on('change', function() {
                let statefieldId = $(this).data('target');
                let cityfieldId = statefieldId == 'modal_state' ? 'modal_city' : 'city';
                loadershow();
                $(`#${cityfieldId}`).html(`<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id, statefieldId);
            });


            // load state in dropdown and set defautl value according to logged in user if not manualy select
            function loadstate(id = 0, type = null) {
                $(`#${type|| 'state'}`).html(`<option selected="" disabled="">Select your State</option>`);
                var url = "{{ route('state.search', '__id__') }}".replace('__id__', id);
                if (id == 0) {
                    url = "{{ route('state.search', session('user')['country_id']) }}";
                }
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            // You can update your HTML with the data here if needed
                            let state = response.state;

                            renderSelectBox(state, 'state', type);
                            if (id == 0) {
                                state_id = "{{ session('user')['state_id'] }}";
                                $(`#${type|| 'state'}`).val(state_id);
                                loadcity(0, type ? 'modal_city' : null);
                            }
                        } else {
                            $(`#${type|| 'state'}`).append(`<option> No Data Found</option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            // load city in dropdown when state select/change
            $('.state').on('change', function() {
                let statefieldId = $(this).data('target');
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id, statefieldId);
            });

            // load city in dropdown and set defautl value according to logged in user if not manualy select
            function loadcity(id = 0, type = null) {
                $(`#${type|| 'city'}`).html(`<option selected="" disabled="">Select your City</option>`);
                url = "{{ route('city.search', '__id__') }}".replace('__id__', id);
                if (id == 0) {
                    url = "{{ route('city.search', session('user')['state_id']) }}";
                }
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            // You can update your HTML with the data here if needed

                            let city = response.city;
                            renderSelectBox(city, 'city', type);
                            if (id == 0) {
                                $(`#${type|| 'city'}`).val("{{ session('user')['city_id'] }}");
                            }
                        } else {
                            $(`#${type|| 'city'}`).append(`<option> No Data Found</option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            // redirect on companymaster list page on click cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.companymaster') }}";
            });
            $('#garden_id').select2({
                placeholder: "Select Gardens",
                width: '100%',
                search: true,
            });
            fetchGardens();

            let oldSelectedGardenId = [];

            function fetchGardens(selectedGardenIds = []) {

                if (!Array.isArray(selectedGardenIds)) {
                    selectedGardenIds = [selectedGardenIds];
                }

                // convert to string (Select2 requirement)
                selectedGardenIds = selectedGardenIds.map(String);

                $.ajax({
                    url: "{{ route('garden.index') }}",
                    type: "GET",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        let gardenSelect = $('#garden_id');
                        gardenSelect.empty();
                        gardenSelect.append('<option value="add_new"> Add New Garden</option>');

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, garden) {
                                gardenSelect.append(
                                    `<option value="${garden.id}">${garden.garden_name}</option>`
                                );
                            });
                        }
                        let finalSelection = oldSelectedGardenId.slice();

                        selectedGardenIds.forEach(function(id) {
                            if (!finalSelection.includes(id)) {
                                finalSelection.push(id);
                            }
                        });

                        console.log(finalSelection);
                        gardenSelect.val(finalSelection).trigger('change');
                    }
                });
            }

            $(document).on('change', '#garden_id', function() {
                oldSelectedGardenId = $(this).val() || [];
                oldSelectedGardenId = oldSelectedGardenId.filter(id => id !== 'add_new');
                if ($(this).val()?.includes("add_new")) {
                    $('#gardenModal').modal('show');

                    $(this).val(oldSelectedGardenId).trigger('change');
                    return;
                }
            });
            $('#gardenModal').on('shown.bs.modal', function(e) {
                loadcountry('modal_country');

            });
            $('#gardenform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.modal-error-msg').text('');
                let formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('garden.store') }}",
                    data: formdata,
                    success: function(response) {
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#gardenform')[0].reset();
                            $('#gardenModal').modal('hide');
                            let latestGardenId = String(response.garden_id);
                            fetchGardens([latestGardenId]);
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();

                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleModalAjaxError(xhr);
                    }

                });
            });
            $('#modal_cancelBtn').on('click', function() {
                $('#gardenform')[0].reset();
                $('#gardenModal').modal('hide');

            })
            // submit companymaster form 
            $('#Companyform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('companymaster.store') }}",
                    data: formdata,
                    success: function(response) {
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location.href =
                                "{{ route('admin.companymaster') }}"; // redirect on companymaster list page

                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();

                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            });
        });
    </script>
@endpush
