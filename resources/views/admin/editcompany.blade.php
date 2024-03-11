@extends('admin.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Update company details
@endsection
@section('title')
    Update compnay details
@endsection


@section('form-content')
    <form id="editcompanyform" name="editcompanyform" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" name="user_id" class="form-control" value="{{ $user_id }}"
                        placeholder="updated_by" required />
                    <label for="">Name</label>
                    <input id="name" type="text" name="name" value="" class="form-control"
                        placeholder="company name" required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" id="email" value=""
                        placeholder="Enter Email" required />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="contact_no">Contact Number</label>
                    <input type="tel" name="contact_number" class="form-control" id="contact_no" value=""
                        placeholder="0123456789" required />
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="gst_no">GST Number</label>
                    <input type="text" value="" id="gst_no" name="gst_number" class="form-control"
                        placeholder="GST Number" required />
                    <span class="error-msg" id="error-gst_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="country">Select Country</label>
                    <select class="form-control" name="country" id="country" required>
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="state" required>Select State</label>
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
                    <label for="city">Select City</label>
                    <select class="form-control" name="city" id="city" required>
                        <option selected disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="pincode">Pincode</label>
                    <input type="text" id="pincode" value="" name="pincode" class="form-control"
                        placeholder="Pin Code" required />
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="address">Address</label>
                    <textarea class="form-control" name="address" id="address" rows="2" required></textarea>
                    <span class="error-msg" id="error-address" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="">Image</label><br>
                    <input type="file" name="img" id="" width="100%" />
                    <span class="error-msg" id="error-img" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="companysubmit">Save</button>
            <div id="loader" class="loader"></div>
            <button id="resetbtn" type="reset" class="btn iq-bg-danger">Reset</button>
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

            // getcompany  data from api and set in the form inputs 
            var edit_id = @json($edit_id);
            $.ajax({
                type: 'GET',
                url: '/api/company/search/' + edit_id,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    user_id: "{{ session()->get('user_id') }}"
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == 200 && response.company != '') {
                        var company = response.company[0];
                        $('#name').val(company.name);
                        $('#email').val(company.email);
                        $('#contact_no').val(company.contact_no);
                        $('#gst_no').val(company.gst_no);
                        $('#pincode').val(company.pincode);
                        $('#address').val(company.address);
                        country = company.country_id;
                        state = company.state_id;
                        city = company.city_id;
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
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });

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
                        $('#country').val(country);
                        loaderhide();
                    } else {
                        $('#country').append(`<option disabled> No Data Found</option>`);
                        loaderhide();
                    }


                    // You can update your HTML with the data here if needed
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
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
                            $('#state').append(`<option disabled> No Data Found</option>`)
                        }
                        $('#state').val(state);
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        console.error('Error:', error);
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
                            $('#city').append(`<option disabled> No Data Found</option>`)
                        }
                        $('#city').val(city);


                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        console.error('Error:', error);
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
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                            loaderhide();
                        } else {
                            $('#state').append(`<option> No Data Found</option>`);
                            loaderhide();
                        }


                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        loaderhide();
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
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#city').append(`<option disabled> No Data Found</option>`)
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        loaderhide();
                    }
                });
            });

            //submit form
            $('#editcompanyform').submit(function(event) {
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
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location =
                                "{{ route('admin.companyprofile', ['id' => Session::get('company_id')]) }}";

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
