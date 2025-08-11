@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Customer
@endsection
@section('title')
    New Customer
@endsection


@section('form-content')
    <form id="customerform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ $company_id }}" class="form-control" name="company_id">
                    <label for="firstname">FirstName</label><span class="withoutgstspan" style="color:red;">*</span>
                    <input type="text" id="firstname" class="form-control withoutgstinput" name='firstname'
                        placeholder="First Name" required>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="lastname">LastName</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <input type="text" id="lastname" class="form-control requiredinput" name='lastname'
                        placeholder="Last Name">
                    <span class="error-msg" id="error-lastname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="company_name">Company Name</label>
                    <span class="withgstspan" style="color:red;">*</span>
                    <input type="text" id="company_name" class="form-control withgstinput" name='company_name'
                        id="" placeholder="Company Name">
                    <span class="error-msg" id="error-company_name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="gst_number">GST Number</label>
                    {{-- <span class="withgstspan" style="color:red;">*</span> --}}
                    <input type="text" id="gst_number" class="form-control" name='gst_number' id=""
                        placeholder="GST Number">
                    <span class="error-msg" id="error-gst_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="email">Email</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <input type="email" class="form-control requiredinput" name="email" id="email"
                        placeholder="Enter Email">
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="contact_number">Contact Number</label>
                    {{-- <span class="requiredinputspan"  style="color:red;">*</span> --}}
                    <input type="tel" class="form-control requiredinput" name='contact_number' id="contact_number"
                        placeholder="0123456789">
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="country">Select Country</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <select class="form-control requiredinput" name='country' id="country">
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="state">Select State</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <select class="form-control requiredinput" name='state' id="state">
                        <option selected="" disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="city">Select City</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <select class="form-control requiredinput" name='city' id="city">
                        <option selected="" disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="pincode">Pincode</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <input type="text" id="pincode" name='pincode' class="form-control requiredinput"
                        placeholder="Pin Code">
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="house_no_building_name">House no./ Building Name</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <textarea class="form-control requiredinput" name='house_no_building_name' id="house_no_building_name"
                        rows="2" placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                    <span class="error-msg" id="error-house_no_building_name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="road_name_area_colony">Road Name/Area/Colony</label>
                    {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                    <textarea class="form-control requiredinput" name='road_name_area_colony' id="road_name_area_colony" rows="2"
                        placeholder="e.g. sardar patel road, jagatpur"></textarea>
                    <span class="error-msg" id="error-road_name_area_colony" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right"><i class="ri-close-line"></i></button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset Customer Details" class="btn iq-bg-danger float-right mr-2"><i
                            class="ri-refresh-line"></i></button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save Customer Details" class="btn btn-primary float-right my-0"><i
                            class="ri-check-line"></i></button>
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

            // customer form  -> dynamic required attribute (if enter company name then only company name required otherwise only firstname)
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

            // show country data in dropdown and set defautl value according to logged in user
            $.ajax({
                type: 'GET',
                url: '{{ route('country.index') }}',
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
                var url = "/api/state/search/" + id;
                if (id == 0) {
                    url = "/api/state/search/" + "{{ session('user')['country_id'] }}";
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

            // load city in dropdown when state select/change
            $('#state').on('change', function() {
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id);
            });

            // load city in dropdown and set defautl value according to logged in user if not manualy select
            function loadcity(id = 0) {
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                url = "/api/city/search/" + id;
                if (id == 0) {
                    url = "/api/city/search/" + "{{ session('user')['state_id'] }}";
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

            // redirect on customer list page on click cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.invoicecustomer') }}";
            });

            // submit customer form 
            $('#customerform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customer.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location =
                                "{{ route('admin.invoicecustomer') }}"; // redirect on customer list page

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
            });
        });
    </script>
@endpush
