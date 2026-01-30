@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New garden
@endsection
@section('title')
    New garden
@endsection


@section('form-content')
    <form id="gardenform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">

                    <label for="garden_name">garden name</label><span class="" style="color:red;">*</span>
                    <input type="text" id="garden_name" class="form-control " name='garden_name'
                        placeholder="garden  Name">
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
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            let message = "{{ session('message') }}";
            if (message) {
                Toast.fire({
                    icon: 'error',
                    title: message
                });
            }

            // show country data in dropdown and set defautl value according to logged in user
            $.ajax({
                type: 'GET',
                url: "{{ route('country.index') }}",
                data: {
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {

                    if (response.status == 200 && response.country != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.country, function(key, value) {
                            $('#country').append(
                                `<option value='${value.id}'> ${value.country_name}</option>`
                            )
                        });
                        country_id = "{{ session('user')['country_id'] }}";
                        $('#country').val(country_id);
                        loadstate();
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

            // load state in dropdown when country change
            $('#country').on('change', function() {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id);
            });

            // load state in dropdown and set defautl value according to logged in user if not manualy select
            function loadstate(id = 0) {
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
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
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                            if (id == 0) {
                                state_id = "{{ session('user')['state_id'] }}";
                                $('#state').val(state_id);
                                loadcity();
                            }
                        } else {
                            $('#state').append(`<option> No Data Found</option>`);
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
            $('#state').on('change', function() {
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id);
            });

            // load city in dropdown and set defautl value according to logged in user if not manualy select
            function loadcity(id = 0) {
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
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
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                            if (id == 0) {
                                $('#city').val("{{ session('user')['city_id'] }}");
                            }
                        } else {
                            $('#city').append(`<option> No Data Found</option>`);
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

            // redirect on garden list page on click cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.garden') }}";
            });

            // submit garden form 
            $('#gardenform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
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
                            window.location.href =
                                "{{ route('admin.garden') }}"; // redirect on garden list page

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
