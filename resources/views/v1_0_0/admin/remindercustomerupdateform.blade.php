@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Customer
@endsection
@section('title')
    Update Customer
@endsection


@section('form-content')
    <form id="remindercustomerupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ $company_id }}" class="form-control" name="company_id">
                    <label class="form-label" for="name">Name</label><span style="color:red;">*</span>
                    <input type="text" id="name" class="form-control" name='name' placeholder="Customer Name"
                        required>
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="customer_type">Customer Type:</label><span style="color:red;">*</span>
                    <select name="customer_type" class="form-control" id="customer_type">
                        <option disabled selected>Select Customer Type</option>
                        <option value="Own">Own</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="error-msg" id="error-customer_type" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="email">Email</label><span style="color:red;">*</span>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email"
                        required>
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="contact_number">Contact Number</label><span style="color:red;">*</span>
                    <input type="tel" class="form-control" name='contact_number' id="contact_number"
                        placeholder="0123456789" required>
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="country">Select Country</label><span style="color:red;">*</span>
                    <select class="form-control" name='country' id="country" required>
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="state">Select State</label><span style="color:red;">*</span>
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
                    <label class="form-label" for="city">Select City</label><span style="color:red;">*</span>
                    <select class="form-control" name='city' id="city" required>
                        <option selected="" disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="invoiceid">Invoice Id</label>
                    <input type="text" id="invoiceid" name='invoiceid' class="form-control"
                        placeholder="Invoice Id">
                    <span class="error-msg" id="error-invoiceid" style="color: red"></span>
                </div>

            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="area">Area</label><span style="color:red;">*</span>
                    <input type="text" id="area" name='area' class="form-control" placeholder="Area">
                    <span class="error-msg" id="error-area" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="pincode">Pincode</label><span style="color:red;">*</span>
                    <input type="text" id="pincode" name='pincode' class="form-control" placeholder="Pin Code">
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label class="form-label" for="address">Address</label><span style="color:red;">*</span>
                    <textarea class="form-control" required name='address' id="address" rows="2"></textarea>
                    <span class="error-msg" id="error-address" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                 <div class="col-sm-12">
                     <button type="reset" class="btn iq-bg-danger float-right">Reset</button>
                     <button type="submit" class="btn btn-primary float-right my-0" >Submit</button>
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
            // get selected customer data and show it into fields

            // get & set customer old data in the form input
            var edit_id = @json($edit_id);
            $.ajax({
                type: 'GET',
                url: '/api/remindercustomer/search/' + edit_id,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    user_id: " {{ session()->get('user_id') }} "
                },
                success: function(response) {
                    if (response.status == 200) {
                        // You can update your HTML with the data here if needed
                        $('#name').val(response.customer.name);
                        $('#customer_type').val(response.customer.customer_type);
                        $('#email').val(response.customer.email);
                        $('#contact_number').val(response.customer.contact_no);
                        $('#pincode').val(response.customer.pincode);
                        $('#address').val(response.customer.address);
                        $('#invoiceid').val(response.customer.invoice_id);
                        $('#area').val(response.customer.area);
                        country = response.customer.country_id;
                        state = response.customer.state_id;
                        city = response.customer.city_id;
                        loadstate(country, state);
                        loadcity(state, city);
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                    }
                    loaderhide();
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
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            })
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`);
                        }
                        loaderhide();
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
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                        } else {
                            $('#city').append(`<option disabled> No Data Found</option>`);
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });

            // subimt form
            $('#remindercustomerupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'put',
                    url: "{{ route('remindercustomer.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.remindercustomer') }}";

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
