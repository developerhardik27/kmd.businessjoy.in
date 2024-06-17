@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Update user details
@endsection
@section('title')
    Update User details
@endsection


@section('form-content')
    <div class="iq-card-body">
        <form method="POST" id="usereditform" enctype="multipart/form-data">
            @csrf
            <div class="form-group row align-items-center">
                <div class="col-md-12">
                    <div class="profile-img-edit" id="userprofile">
                        <div class="p-image">
                            <i class="ri-pencil-line upload-button"></i>
                            <input type="file" class="file-upload" id="img" name="img">
                            <span class="error-msg" id="error-img" style="color: red"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <input type="hidden" name="editrole" class="form-control" value=1 placeholder="token" required />
                        <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                            placeholder="token" required />
                        <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                        <input type="hidden" value="{{ session('company_id') }}" name="company_id" class="form-control">
                        <label for="firstname">FirstName</label><span style="color:red;">*</span>
                        <input type="text" id="firstname" name='firstname' class="form-control" placeholder="First name"
                            required>
                        <span class="error-msg" id="error-firstname" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="lastname">LastName</label><span style="color:red;">*</span>
                        <input type="text" id="lastname" name='lastname' class="form-control" placeholder="Last name"
                            required>
                        <span class="error-msg" id="error-lastname" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <label for="email">Email</label><span style="color:red;">*</span>
                        <input type="email" name='email' class="form-control" id="email" value=""
                            placeholder="Enter Email" required>
                        <span class="error-msg" id="error-email" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="password">Password</label>
                        <input type="text" id="password" name='password' class="form-control" id="exampleInputPassword3"
                            value="" placeholder="update Password (not mandatory)">
                        <span class="error-msg" id="error-password" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <label for="contact_no">Contact Number</label><span style="color:red;">*</span>
                        <input type="tel" name='contact_number' class="form-control" id="contact_no" value=""
                            placeholder="0123456789" required>
                        <span class="error-msg" id="error-contact_number" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="country">Select Country</label><span style="color:red;">*</span>
                        <select id="country" class="form-control" name='country' required>
                            <option selected="" disabled="">Select your Country</option>
                        </select>
                        <span class="error-msg" id="error-country" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <label for="state">Select State</label><span style="color:red;">*</span>
                        <select class="form-control" name='state' id="state" required>
                            <option selected="" disabled="">Select your State</option>
                        </select>
                        <span class="error-msg" id="error-state" style="color: red"></span>
                    </div>
                    <div class="col-sm-6">
                        <label for="city">Select City</label><span style="color:red;">*</span>
                        <select class="form-control" name='city' id="city" required>
                            <option selected="" disabled="">Select your City</option>
                        </select>
                        <span class="error-msg" id="error-city" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6">
                        <label for="pincode">Pincode</label><span style="color:red;">*</span>
                        <input type="text" id="pincode" name='pincode' class="form-control" placeholder="Pin Code"
                            required>
                        <span class="error-msg" id="error-pincode" style="color: red"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-12">
                        <button type="reset" class="btn iq-bg-danger float-right">Reset</button>
                        <button type="submit" class="btn btn-primary float-right my-0">Submit</button>
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

                var edit_id = @json($edit_id);
                // show user old data in the form input fields
                $.ajax({
                    type: 'GET',
                    url: '/api/user/search/' + edit_id,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.user != '') {
                            user = response.user[0];
                            // You can update your HTML with the data here if needed
                            $('#firstname').val(user.firstname);
                            $('#lastname').val(user.lastname);
                            $('#email').val(user.email);
                            $('#contact_no').val(user.contact_no);
                            $('#pincode').val(user.pincode);
                            var imgElement = $('<img>').attr('src', '/uploads/' + user.img).attr(
                                'alt', 'profile-pic').attr('class', 'profile-pic rounded ');
                            $('#userprofile').prepend(imgElement);
                            country = user.country_id;
                            state = user.state_id;
                            city = user.city_id;
                            company = user.company_id;
                            loadcountry(country);
                            loadstate(country, state);
                            loadcity(state, city);
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error('something went wrong !');
                        }
                        loaderhide();
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

                // show country data in dropdown and old country selected
                function loadcountry(country) {
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
                                $('#country').val(country);
                            } else {
                                $('#country').append(`<option disabled> No Data Found</option>`)
                            }
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                }

                // show country data in dropdown and old country selected
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
                                $('#state').append(`<option disabled> No Data Found</option>`)
                            }
                            $('#state').val(state);
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                }

                // show state data in dropdown and old state selected
                function loadcity(state, city) {
                    $.ajax({
                        type: 'GET',
                        url: "/api/city/search/" + state,
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
                            $('#city').val(city);
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                }

                // show state of selected country
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
                                });
                            } else {
                                $('#state').append(`<option disabled> No Data Found</option>`);
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
                });

                // show city of selected state
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
                            if (response.status == 200) {
                                // You can update your HTML with the data here if needed
                                $.each(response.city, function(key, value) {
                                    $('#city').append(
                                        `<option value='${value.id}'> ${value.city_name}</option>`
                                    );
                                });
                            } else {
                                $('#city').append(`<option disabled>No Data Found</option>`);
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
                });
                
                //submit form
                $('#usereditform').submit(function(event) {
                    event.preventDefault();
                    loaderhide();
                    $('.error-msg').text('');
                    var formData = new FormData($(this)[0]);
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('user.update', $edit_id) }}",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            // Handle the response from the server
                            if (response.status == 200) {
                                // You can perform additional actions, such as showing a success message or redirecting the user
                                toastr.success(response.message);
                                window.location =
                                    "{{ route('admin.userprofile', ['id' => Session::get('user_id')]) }}";
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
