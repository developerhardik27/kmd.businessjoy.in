@extends('admin.masterlayout')

@section('page_title')
    Add New Company
@endsection
@section('title')
    New Company
@endsection


@section('form-content')
    <form id="companyform" name="companyform" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" name="created_by" class="form-control" value="{{ $user_id }}"
                        placeholder="created_by" required />
                    <label for="">Name</label>
                    <input id="name" type="text" name="name" class="form-control" placeholder="company name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="exampleInputEmail3">Email</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail3" value=""
                        placeholder="Enter Email" required />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="exampleInputphone">Contact Number</label>
                    <input type="tel" name="contact_number" class="form-control" id="exampleInputphone" value=""
                        placeholder="0123456789" required />
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="">GST Number</label>
                    <input type="text" name="gst_number" class="form-control" placeholder="GST Number" required />
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
                    <label for="">Pincode</label>
                    <input type="text" name="pincode" class="form-control" placeholder="Pin Code" required />
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="exampleFormControlTextarea1">Address</label>
                    <textarea class="form-control" name="address" id="exampleFormControlTextarea1" rows="2" required></textarea>
                    <span class="error-msg" id="error-address" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <div class="col-sm-6">
                        <label for="">Image</label><br>
                        <input type="file" name="img" id="" width="100%" />
                        <span class="error-msg" id="error-img" style="color: red"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="companysubmit">Submit</button>
            <div id="loader" class="loader"></div>
            <button id="resetbtn" type="reset" class="btn iq-bg-danger">Reset</button>
        </div>
    </form>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            // fetch country data and show in dropdown
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
                    } else {
                        $('#country').append(`<option> No Data Found</option>`)
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

            // load state in dropdown when country select
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
                            $('#state').append(`<option> No Data Found</option>`)
                        }
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            });

            // load city in dropdown when state select
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
                            $('#city').append(`<option> No Data Found</option>`)
                        }
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            });

            // submit form data
            $('#companyform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                $('#companysubmit').hide();
                $('#resetbtn').hide();
                // Show the loader
                $("#loader").show();
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('company.store') }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.company') }}";

                        } else {
                            $('#companysubmit').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
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
                            $('#companysubmit').show();
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
            })
        });
    </script>
@endpush
