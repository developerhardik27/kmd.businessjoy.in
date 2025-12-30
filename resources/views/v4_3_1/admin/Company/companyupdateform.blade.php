@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Company
@endsection
@section('title')
    Update Company
@endsection


@section('form-content')
    <form id="companyupdateform" name="companyupdateform" enctype="multipart/form-data">
        @csrf
        <div class="accordion">
            <!-- Basic Details -->
            <div class="card mb-2">
                <div class="card-header" id="basicdetailsheading">
                    <h5 class="mb-0">
                        <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#basicdetails"
                            aria-expanded="true" aria-controls="basicdetails">
                            Basic Details
                        </button>
                    </h5>
                </div>

                <div id="basicdetails" class="collapse show" aria-labelledby="basicdetailsheading">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <input type="hidden" name="company_id" class="form-control"
                                        value="{{ session('company_id') }}" required />
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" required />
                                    <input type="hidden" name="user_id" class="form-control" value="{{ $user_id }}"
                                        required>
                                    <label for="name">Name</label><span style="color:red;">*</span>
                                    <input id="name" type="text" name="name" class="form-control" placeholder="company name"
                                        required>
                                    <span class="error-msg" id="error-name" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="email">Email (Company Email)</label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        placeholder="Enter Company Email" autocomplete="off" />
                                    <span class="error-msg" id="error-email" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="contact_no">Contact Number</label><span style="color:red;">*</span>
                                    <input type="tel" name="contact_number" class="form-control" id="contact_no"
                                        placeholder="0123456789" required />
                                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                                </div>  
                                <div class="col-sm-6 mb-2">
                                    <label for="alternative_number">Alternative Number</label>
                                    <input type="tel" name="alternative_number" class="form-control" id="alternative_number"
                                        placeholder="0123456789"  />
                                    <span class="error-msg" id="error-alternative_number" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="gst_number">GST Number</label>
                                    <input type="text" id='gst_number' name="gst_number" class="form-control"
                                        placeholder="GST Number" />
                                    <span class="error-msg" id="error-gst_number" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="pan_number">PAN Number</label>
                                    <input type="text" id='pan_number' name="pan_number" class="form-control"
                                        placeholder="PAN Number" />
                                    <span class="error-msg" id="error-gst_number" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="country">Select Country</label><span style="color:red;">*</span>
                                    <select class="form-control" name="country" id="country" required>
                                        <option selected="" disabled="">Select your Country</option>
                                    </select>
                                    <span class="error-msg" id="error-country" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="state" required>Select State</label><span style="color:red;">*</span>
                                    <select class="form-control" name="state" id="state">
                                        <option selected disabled="">Select your State</option>
                                    </select>
                                    <span class="error-msg" id="error-state" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="city">Select City</label><span style="color:red;">*</span>
                                    <select class="form-control" name="city" id="city" required>
                                        <option selected disabled="">Select your City</option>
                                    </select>
                                    <span class="error-msg" id="error-city" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="pincode">Pincode</label><span style="color:red;">*</span>
                                    <input type="text" name="pincode" id='pincode' class="form-control"
                                        placeholder="Pin Code" required />
                                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="house_no_building_name">House no./ Building Name</label><span
                                        class="requiredinputspan" style="color:red;">*</span>
                                    <textarea class="form-control requiredinput" name='house_no_building_name'
                                        id="house_no_building_name" rows="2"
                                        placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                    <span class="error-msg" id="error-house_no_building_name" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="road_name_area_colony">Road Name/Area/Colony</label><span
                                        class="requiredinputspan" style="color:red;">*</span>
                                    <textarea class="form-control requiredinput" name='road_name_area_colony'
                                        id="road_name_area_colony" rows="2"
                                        placeholder="e.g. sardar patel road, jagatpur"></textarea>
                                    <span class="error-msg" id="error-road_name_area_colony" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="company_website_url">Company Website URL</label>
                                    <input type="text" name="company_website_url" id='company_website_url'
                                        class="form-control" placeholder="Company Website URL" />
                                    <span class="error-msg" id="error-company_website_url" style="color: red"></span>
                                </div>
                                @if (
                                        Session::has('user_permissions.adminmodule.company.max') &&
                                        session('user_permissions.adminmodule.company.max') == '1'
                                    )
                                    <div class="col-sm-6 mb-2">
                                        <label for="maxuser">Max Users</label><span style="color:red;">*</span>
                                        <input type="text" name="maxuser" id='maxuser' class="form-control"
                                            placeholder="Max Users" value="5" required />
                                        <span class="error-msg" id="error-maxuser" style="color: red"></span>
                                    </div>
                                @endif
                                <div class="col-sm-6 mb-2">
                                    <label for="img">Company Logo Image</label><br>
                                    <input type="file" class="form-control-file" name="img" id="img" width="100%" />
                                    <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that is smaller
                                        than 1 MB</p>
                                    <span class="error-msg" id="error-img" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="sign_img">Company Signature Image</label><br>
                                    <input type="file" class="form-control-file" name="sign_img" id="sign_img"
                                        width="100%" />
                                    <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that is smaller
                                        than 1 MB.</p>
                                    <span class="error-msg" id="error-sign_img" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <div class="row g-2">

                                        <div class="col-4">
                                            <label for="god_name_1" class="form-label">God Name (1)</label>
                                            <input type="text" name="god_name[]" id="god_name_1" class="form-control"
                                                placeholder="Enter God Name 1" />
                                            <span class="error-msg text-danger" id="error-god_name_1"></span>
                                        </div>

                                        <div class="col-4">
                                            <label for="god_name_2" class="form-label">God Name (2)</label>

                                            <input type="text" name="god_name[]" id="god_name_2" class="form-control"
                                                placeholder="Enter God Name 2"  />

                                            <span class="error-msg text-danger" id="error-god_name_2"></span>
                                        </div>

                                        <div class="col-4">
                                            <label for="god_name_3" class="form-label">God Name (3)</label>
                                            <input type="text" name="god_name[]" id="god_name_3" class="form-control"
                                                placeholder="Enter God Name 3"  />
                                            <span class="error-msg text-danger" id="error-god_name_3"></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logistic -->
            <div class="card mb-2">
                <div class="card-header" id="logsticsectionheading">
                    <h5 class="mb-0">
                        <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#logsticsection"
                            aria-expanded="false" aria-controls="logsticsection">
                            Logistic
                        </button>
                    </h5>
                </div>

                <div id="logsticsection" class="collapse" aria-labelledby="logsticsectionheading">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <label for="transporter_id">Transporter ID</label>
                                    <input type="text" name="transporter_id" id='transporter_id' class="form-control"
                                        placeholder="Transporter ID" />
                                    <span class="error-msg" id="error-transporter_id" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">
                        Cancel
                    </button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom" data-original-title="Reset"
                        class="btn iq-bg-danger float-right mr-2">
                        Reset
                    </button>
                    <button type="submit" id="save-btn" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Update" class="btn btn-primary float-right my-0">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function () {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            // remove collapsed class (collapsed card creating issue if any required field not filled and user do submit)
            $('#save-btn').on('click', function (e) {
                var cardBody = $('.card-body');
                var collapseSection = cardBody.closest('.collapse');

                if (collapseSection.length && !collapseSection.hasClass(
                    'show')) {
                    collapseSection.addClass('show');
                    // Also expand the toggle button if needed
                    var toggleButton = collapseSection.prev(
                        '.card-header').find('button');
                    if (toggleButton.length) {
                        toggleButton.removeClass('collapsed');
                    }
                }
            });

            //get country data and set into dropdown
            $.ajax({
                type: 'GET',
                url: "{{ route('country.index') }}",
                data: {
                    token: "{{ session()->get('api_token') }}"
                },
                success: function (response) {
                    if (response.status == 200 && response.country != '') {
                        $.each(response.country, function (key, value) {
                            $('#country').append(
                                `<option value='${value.id}'> ${value.country_name}</option>`
                            )
                        });
                    } else {
                        $('#country').append(`<option disabled> No Data Found</option>`);
                    }

                    // You can update your HTML with the data here if needed
                },
                error: function (xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr.responseText); // Log the full error response for debugging
                    handleAjaxError(xhr);
                }
            });

            // get company old data from api and set it into form input
            var edit_id = @json($edit_id);
            let companySearchUrl = "{{ route('company.search', '__editId__') }}".replace('__editId__', edit_id);
            $.ajax({
                type: 'GET',
                url: companySearchUrl,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    user_id: " {{ session()->get('user_id') }}",
                    company_id: " {{ session()->get('company_id') }}",
                },
                success: function (response) {

                    if (response.status == 200 && response.company != '') {
                        var company = response.company[0];
                        $('#name').val(company.name);
                        $('#email').val(company.email);
                        $('#contact_no').val(company.contact_no);
                        $('#alternative_number').val(company.alternative_number);
                        $('#gst_number').val(company.gst_no);
                        $('#pan_number').val(company.pan_number);
                        $('#pincode').val(company.pincode);
                        $('#house_no_building_name').val(company.house_no_building_name);
                        $('#road_name_area_colony').val(company.road_name_area_colony);
                        $('#maxuser').val(company.max_users);
                        $('#transporter_id').val(company.transporter_id);
                        $('#company_website_url').val(company.website_url);
                        let godNames = company.god_names;
                        if (typeof godNames === 'string') {
                            godNames = JSON.parse(godNames);
                        }
                        if(godNames && godNames.length > 0){
                            godNames.forEach((value, index) => {
                                $('#god_name_' + (index + 1)).val(value);
                            });
                        }
                        country = company.country_id;
                        state = company.state_id;
                        city = company.city_id;
                        $('#country').val(country);
                        loadstate(country, state);
                        loadcity(state, city);
                        loaderhide();
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                        loaderhide();
                    } else {
                        loaderhide();
                        Toast.fire({
                            icon: "error",
                            title: "something went wrong!"
                        });
                    }

                    // You can update your HTML with the data here if needed
                },
                error: function (xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr.responseText); // Log the full error response for debugging
                    handleAjaxError(xhr);
                }
            });



            // set state data in dropdown 
            function loadstate(country, state) {
                let stateSearchUrl = "{{ route('state.search', '__countryId__') }}".replace('__countryId__',
                    country);
                $.ajax({
                    type: 'GET',
                    url: stateSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.state != '') {
                            $.each(response.state, function (key, value) {
                                $('#state').append(
                                    `<option  value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`);
                        }
                        $('#state').val(state);


                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            // set city data in dropdown      
            function loadcity(state, city) {
                let citySearchUrl = "{{ route('city.search', '__stateId__') }}".replace('__stateId__', state);
                $.ajax({
                    type: 'GET',
                    url: citySearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.city != '') {
                            $.each(response.city, function (key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                        } else {
                            $('#city').append(`<option disabled> No Data Found</option>`);
                        }
                        $('#city').val(city);


                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }


            // set state data for selected country in dropdown 
            $('#country').on('change', function () {
                loadershow();
                var country = $(this).val();
                stateSearchUrl = "{{ route('state.search', '__countryId__') }}".replace('__countryId__',
                    country);
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                $.ajax({
                    type: 'GET',
                    url: stateSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.state != '') {
                            loaderhide();
                            $.each(response.state, function (key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                        } else {
                            $('#state').append(`<option> No Data Found</option>`);
                            loaderhide();
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            });

            // set city data for selected state in dropdown      
            $('#state').on('change', function () {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                var state = $(this).val();
                citySearchUrl = "{{ route('city.search', '__stateId__') }}".replace('__stateId__', state);
                $.ajax({
                    type: 'GET',
                    url: citySearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function (response) {
                        if (response.status == 200 && response.city != '') {
                            loaderhide();
                            $.each(response.city, function (key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                        } else {
                            $('#city').append(`<option disabled> No Data Found</option>`);
                            loaderhide();
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            });

            // redirect on company list page on click cancel btn
            $('#cancelbtn').on('click', function () {
                loadershow();
                window.location.href = "{{ route('admin.company') }}";
            });

            //submit company update form
            $('#companyupdateform').submit(function (event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('company.update', $edit_id) }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.company') }}";

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function (xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var firstErrorElement = null;

                            $.each(errors, function (key, value) {
                                var errorSpan = $('#error-' + key);
                                errorSpan.text(value[0]);

                                // Save the first error container
                                if (!firstErrorElement) {
                                    firstErrorElement = errorSpan;
                                }

                                // Expand the accordion section if it's collapsed
                                var cardBody = errorSpan.closest('.card-body');
                                var collapseSection = cardBody.closest('.collapse');

                                if (collapseSection.length && !collapseSection.hasClass(
                                    'show')) {
                                    collapseSection.addClass('show');
                                    // Also expand the toggle button if needed
                                    var toggleButton = collapseSection.prev(
                                        '.card-header').find('button');
                                    if (toggleButton.length) {
                                        toggleButton.removeClass('collapsed');
                                    }
                                }
                            });

                            // Scroll to first error if found
                            if (firstErrorElement) {
                                $('html, body').animate({
                                    scrollTop: firstErrorElement.offset().top -
                                        100 // Adjust offset as needed
                                }, 500);
                            }
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            })
        });
    </script>
@endpush