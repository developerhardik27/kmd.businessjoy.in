@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Company
@endsection
@section('title')
    Update Company
@endsection


@section('form-content')
    <form id="companyupdateform" name="companyupdateform" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}" required />
                    <input type="hidden" name="user_id" class="form-control" value="{{ $user_id }}" required />
                    <label for="name">Name</label><span style="color:red;">*</span>
                    <input id="name" type="text" name="name" class="form-control" placeholder="company name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="email">Email</label><span style="color:red;">*</span>
                    <input type="email" name="email" class="form-control" id="email" value=""
                        placeholder="Enter Email" required />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="contact_no">Contact Number</label><span style="color:red;">*</span>
                    <input type="tel" name="contact_number" class="form-control" id="contact_no"
                        placeholder="0123456789" required />
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="gst_no">GST Number</label>
                    <input type="text" value="" id="gst_no" name="gst_number" class="form-control"
                        placeholder="GST Number" />
                    <span class="error-msg" id="error-gst_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="country">Select Country</label><span style="color:red;">*</span>
                    <select class="form-control" name="country" id="country" required>
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="state" required>Select State</label><span style="color:red;">*</span>
                    <select class="form-control" name="state" id="state">
                        <option selected disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="city">Select City</label><span style="color:red;">*</span>
                    <select class="form-control" name="city" id="city" required>
                        <option selected disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="pincode">Pincode</label><span style="color:red;">*</span>
                    <input type="text" id="pincode" value="" name="pincode" class="form-control"
                        placeholder="Pin Code" required />
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="address">Address</label><span style="color:red;">*</span>
                    <textarea class="form-control" name="address" id="address" rows="2" required></textarea>
                    <span class="error-msg" id="error-address" style="color: red"></span>
                </div>
                @if (Session::has('user_permissions.adminmodule.company.max') &&
                                                    session('user_permissions.adminmodule.company.max') == '1')
                    <div class="col-sm-6">
                        <label for="pincode">Max Users</label><span style="color:red;">*</span>
                        <input type="text" name="maxuser" id='maxuser' class="form-control" placeholder="Max Users"
                            value="5" required />
                        <span class="error-msg" id="error-maxuser" style="color: red"></span>
                    </div>
                @endif
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="img">Company Logo Image</label><br>
                    <input type="file" class="form-control-file" name="img" id="img" width="100%" />
                    <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that is smaller than 2 MB and has a maximum width of 120 pixels.</p>
                    <span class="error-msg" id="error-img" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="sign_img">Company Signature Image</label><br>
                    <input type="file" class="form-control-file" name="sign_img" id="sign_img" width="100%" />
                    <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that is smaller than 2 MB and has a maximum width of 120 pixels.</p>
                    <span class="error-msg" id="error-sign_img" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="reset" data-toggle="tooltip" data-placement="bottom" data-original-title="Reset"
                        class="btn iq-bg-danger float-right"><i class='ri-refresh-line'></i></button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Update"
                        class="btn btn-primary float-right my-0"><i class='ri-check-line'></i></button>
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





            //get country data and set into dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('country.index') }}',
                data: {
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.country != '') {
                        $.each(response.country, function(key, value) {
                            $('#country').append(
                                `<option value='${value.id}'> ${value.country_name}</option>`
                            )
                        });
                    } else {
                        $('#country').append(`<option disabled> No Data Found</option>`);
                    }

                    // You can update your HTML with the data here if needed
                },
                error: function(xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr.responseText); // Log the full error response for debugging
                    var errorMessage = "";
                    try {
                        var responseJSON = JSON.parse(xhr.responseText);
                        errorMessage = responseJSON.message || "An error occurred";
                    } catch (e) {
                        errorMessage = "An error occurred";
                    }
                    toastr.error(errorMessage);
                }
            });

            // get company old data from api and set it into form input
            var edit_id = @json($edit_id);
            $.ajax({
                type: 'GET',
                url: '/api/company/search/' + edit_id,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    user_id: " {{ session()->get('user_id') }}",
                    company_id: " {{ session()->get('company_id') }}",
                },
                success: function(response) {

                    if (response.status == 200 && response.company != '') {
                        var company = response.company[0];
                        $('#name').val(company.name);
                        $('#email').val(company.email);
                        $('#contact_no').val(company.contact_no);
                        $('#gst_no').val(company.gst_no);
                        $('#pincode').val(company.pincode);
                        $('#address').val(company.address);
                        $('#maxuser').val(company.max_users);
                        country = company.country_id;
                        state = company.state_id;
                        city = company.city_id;
                        $('#country').val(country);
                        loadstate(country, state);
                        loadcity(state, city);
                        loaderhide();
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    } else {
                        loaderhide();
                        toastr.error('something went wrong !');
                    }

                    // You can update your HTML with the data here if needed
                },
                error: function(xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr.responseText); // Log the full error response for debugging
                    var errorMessage = "";
                    try {
                        var responseJSON = JSON.parse(xhr.responseText);
                        errorMessage = responseJSON.message || "An error occurred";
                    } catch (e) {
                        errorMessage = "An error occurred";
                    }
                    toastr.error(errorMessage);
                }
            });



            // set state data in dropdown 
            function loadstate(country, state) {
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            $.each(response.state, function(key, value) {
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
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            }

            // set city data in dropdown      
            function loadcity(state, city) {
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            $.each(response.city, function(key, value) {
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
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            }


            // set state data for selected country in dropdown 
            $('#country').on('change', function() {
                loadershow();
                var country = $(this).val();
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            loaderhide();
                            $.each(response.state, function(key, value) {
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
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // set city data for selected state in dropdown      
            $('#state').on('change', function() {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                var state = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            loaderhide();
                            $.each(response.city, function(key, value) {
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
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            //submit form
            $('#companyupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'post',
                    url: "{{ route('company.update', $edit_id) }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.company') }}";

                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            toastr.error(errorMessage);
                        }
                    }
                });
            })
        });
    </script>
@endpush
