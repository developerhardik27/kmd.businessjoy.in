@extends('admin.masterlayout')
@section('page_title')
    Add New Customer
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
                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="created_by">
                    <input type="hidden" value="{{ $company_id }}" class="form-control" name="company_id">
                    <label for="">FirstName</label>
                    <input type="text" class="form-control" name='firstname' placeholder="First Name" required>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="">LastName</label>
                    <input type="text" class="form-control" name='lastname' placeholder="Last Name" required>
                    <span class="error-msg" id="error-lastname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="">Company Name</label>
                    <input type="text" class="form-control" name='company_name' id="" placeholder="Company Name"
                        required>
                        <span class="error-msg" id="error-company_name" style="color: red"></span>    
                </div>
                <div class="col-sm-6">
                    <label for="">GST Number</label>
                    <input type="text" class="form-control" name='gst_number' id="" placeholder="GST Number">
                    <span class="error-msg" id="error-gst_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email"
                        required>
                        <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="exampleInputphone">Contact Number</label>
                    <input type="tel" class="form-control" name='contact_number' id="exampleInputphone"
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
                        <label for="">Pincode</label>
                        <input type="text" name='pincode' class="form-control" placeholder="Pin Code">
                        <span class="error-msg" id="error-pincode" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-12">
                        <label for="address">Address</label>
                        <textarea class="form-control" required name='address' id="address" rows="2"></textarea>
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

            // set country data in country dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('country.index') }}',
                data: {
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {

                    if (response.status == 200 && response.country != '') {
                        $.each(response.country, function(key, value) {
                            // You can update your HTML with the data here if needed
                            $('#country').append(
                                `<option value='${value.id}'> ${value.country_name}</option>`
                            )
                        });
                    } else {
                        $('#country').append(`<option disabled> No Data Found</option>`)
                    }


                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

            // set state data when country select
            $('#country').on('change', function() {
                var country_id = $(this).val();
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country_id,
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
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`)
                        }


                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            });

            // set city data when state select
            $('#state').on('change', function() {
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                var state_id = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state_id,
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

                        } else {
                            $('#city').append(`<option disabled> No Data Found</option>`)
                        }


                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            });

            $('#customerform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                $('.error-msg').text('');
                $('#submitBtn').hide();
                $('#resetbtn').hide();
                // Show the loader
                $("#loader").show();
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
                            window.location = "{{ route('admin.customer') }}";

                        } else {
                            toastr.error(response.message);
                            $('#submitBtn').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            $('#submitBtn').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
                        } else {
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }

                });
            });


        });
    </script>
@endpush
