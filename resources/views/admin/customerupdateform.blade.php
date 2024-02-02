@extends('admin.masterlayout')
@section('page_title')
    Update Customer
@endsection
@section('title')
    Update Customer
@endsection


@section('form-content')
    <form id="customerupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="user_id"
                        placeholder="updated_by">
                    <input type="hidden" value="{{ $company_id }}" class="form-control" name="company_id"
                        placeholder="company_id">
                    <label for="firstname">FirstName</label>
                    <input type="text" id="firstname" class="form-control" name='firstname' placeholder="First name"
                        required>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="lastname">LastName</label>
                    <input type="text" id="lastname" class="form-control" name='lastname' placeholder="Last name"
                        required>
                    <span class="error-msg" id="error-lastname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="company_name">Company Name</label>
                    <input type="text" class="form-control" name='company_name' id="company_name" value=""
                        placeholder="Company name" required>
                    <span class="error-msg" id="error-company_name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="gst_no">GST Number</label>
                    <input type="text" class="form-control" name='gst_number' id="gst_no" value=""
                        placeholder="GST Number">
                    <span class="error-msg" id="error-gst_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" value=""
                        placeholder="Enter Email" required>
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="contact_no">Contact Number</label>
                    <input type="tel" class="form-control" name='contact_number' id="contact_no" value=""
                        placeholder="0123456789" required>
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <label for="country">Select Country</label>
                        <select class="form-control" name='country' id="country" required>
                            <option selected="" disabled="">Select your Country</option>
                        </select>
                        <span class="error-msg" id="error-country" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="state">Select State</label>
                        <select class="form-control" name='state' id="state" required>
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
                        <select class="form-control" name='city' id="city" required>
                            <option selected="" disabled="">Select your City</option>
                        </select>
                        <span class="error-msg" id="error-city" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="pincode">Pincode</label>
                        <input type="text" id="pincode" name='pincode' class="form-control" placeholder="Pin Code"
                            required>
                        <span class="error-msg" id="error-pincode" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-12">
                        <label for="address">Address</label>
                        <textarea class="form-control" name='address' id="address" rows="2" required></textarea>
                        <span class="error-msg" id="error-address" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="button-container">
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                <div id="loader" class="loader"></div>
                <button id="resetbtn" type="reset" class="btn iq-bg-danger">Reset</button>
            </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // get selected customer data and show it into fields
            var edit_id = @json($edit_id);
            $.ajax({
                type: 'GET',
                url: '/api/customer/search/' + edit_id,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}"
                },
                success: function(response) {
                    if (response.status == 200) {
                        // You can update your HTML with the data here if needed
                        $('#firstname').val(response.customer.firstname);
                        $('#lastname').val(response.customer.lastname);
                        $('#company_name').val(response.customer.company_name);
                        $('#gst_no').val(response.customer.gst_no);
                        $('#email').val(response.customer.email);
                        $('#contact_no').val(response.customer.contact_no);
                        $('#pincode').val(response.customer.pincode);
                        $('#address').val(response.customer.address);
                        country = response.customer.country_id;
                        state = response.customer.state_id;
                        city = response.customer.city_id;
                        loadstate(country, state);
                        loadcity(state, city);
                        loaderhide();
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    }

                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });

            // show country data in dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('country.index') }}',
                data: {
                    token: "{{ session()->get('api_token') }}",
                },
                success: function(response) {
                    if (response.status == 200 && response.country != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.country, function(key, value) {
                            $('#country').append(
                                `<option value='${value.id}'> ${value.country_name}</option>`
                            )
                        });
                        $('#country').val(country);
                    } else {
                        $('#country').append(`<option disabled> No Data Found</option>`);
                    }


                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
            //show state data in dropdown
            function loadstate(country, state) {
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option  value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`);
                        }
                        $('#state').val(state);


                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }

            // show city data in dropdown
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
                                // You can update your HTML with the data here if needed
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                        } else {
                            $('#city').append(`<option disabled> No Data Found</option>`);
                        }
                        $('#city').val(city);
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }


            // load state data of selected country when country change
            $('#country').on('change', function() {
                loadershow();
                // var country = $(this).val();
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
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            })
                        } else {
                            loaderhide();
                            $('#state').append(`<option disabled> No Data Found</option>`);
                        }


                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });

            // load city data of selected state when state change
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
                            loaderhide();
                            $('#city').append(`<option disabled> No Data Found</option>`);
                        }


                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });

            // subimt form
            $('#customerupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'put',
                    url: "{{ route('customer.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.customer') }}";

                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            })
        });
    </script>
@endpush
