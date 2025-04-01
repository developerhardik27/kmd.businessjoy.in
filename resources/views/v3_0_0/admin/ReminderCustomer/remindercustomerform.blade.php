@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Reminder Customer
@endsection
@section('title')
    New Customer
@endsection


@section('form-content')
    <form id="remindercustomerform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ $company_id }}" class="form-control" name="company_id">
                    <label class="form-label" for="name">Name</label><span style="color:red;">*</span>
                    <input type="text" id="name" class="form-control" name='name' placeholder="Customer Name"
                        required>
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="customer_type">Customer Type:</label><span style="color:red;">*</span>
                    <select name="customer_type" class="form-control" id="customer_type">
                        <option disabled selected>Select Customer Type</option>
                        <option value="Own" selected>Own</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="error-msg" id="error-customer_type" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="email">Email</label><span style="color:red;">*</span>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email"
                        required>
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="contact_number">Contact Number</label><span style="color:red;">*</span>
                    <input type="tel" class="form-control" name='contact_number' id="contact_number"
                        placeholder="0123456789" required>
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="country">Select Country</label><span style="color:red;">*</span>
                    <select class="form-control" name='country' id="country" required>
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="state">Select State</label><span style="color:red;">*</span>
                    <select class="form-control" name='state' id="state" required>
                        <option selected="" disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="city">Select City</label><span style="color:red;">*</span>
                    <select class="form-control" name='city' id="city" required>
                        <option selected="" disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="invoiceid">Invoice Id</label>
                    <input type="text" id="invoiceid" name='invoiceid' class="form-control"
                        placeholder="Invoice Id">
                    <span class="error-msg" id="error-invoiceid" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="area">Area</label><span style="color:red;">*</span>
                    <input type="text" id="area" name='area' class="form-control" placeholder="Area">
                    <span class="error-msg" id="error-area" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="pincode">Pincode</label><span style="color:red;">*</span>
                    <input type="text" id="pincode" name='pincode' class="form-control" placeholder="Pin Code">
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
                <div class="col-sm-12 mb-2">
                    <label class="form-label" for="address">Address</label><span style="color:red;">*</span>
                    <textarea class="form-control" required name='address' id="address" rows="2"></textarea>
                    <span class="error-msg" id="error-address" style="color: red"></span>
                </div>
                <div class="col-sm-12">
                    <input type="checkbox" name="createreminder" id="createreminder" checked>
                    <label class="form-label" for="createreminder"> Create Reminder</label>
                    <span class="error-msg" id="error-createreminder" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="container" id="remindercustomerformcontainer">
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6 mb-2">
                        <label class="form-label" for="service_type">Service Type:</label>
                        <select name="service_type" class="form-control" id="service_type">
                            <option disabled selected>Select Service Type</option>
                            <option value="paid">Paid</option>
                            <option value="free" selected>Free</option>
                        </select>
                        <span class="error-msg" id="error-service_type" style="color: red"></span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="form-label" for="product_name">Product Name:</label><span
                            style="color:red;">*</span>
                        <input type="text" class="form-control" name="product_name" id="product_name"
                            placeholder="Product Name" />
                        <span class="error-msg" id="error-product_name" style="color: red"></span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="form-label" for="product_unique_id">Product Unique Id:</label>
                        <input type="text" class="form-control" name="product_unique_id" id="product_unique_id"
                            placeholder="Product Unique Id" />
                        <span class="error-msg" id="error-product_unique_id" style="color: red"></span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="form-label" for="amount"> Amount :</label><span style="color:red;">*</span>
                        <input type="text" class="form-control" name="amount" id="amount"
                            placeholder="Amount" />
                        <span class="error-msg" id="error-amount" style="color: red"></span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="form-label" for="next_reminder">Next Reminder Date:</label><span
                            style="color:red;">*</span>
                        <input type="datetime-local" class="form-control" name="next_reminder" id="next_reminder" />
                        <span class="m-1 text-info btn" id="after_sixmonths_date">After 6 Months</span> <span
                            id="after_ninemonths_date" class="m-1 text-info btn">After 9 Months</span>
                        <span class="error-msg" id="error-next_reminder" style="color: red"></span>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <label class="form-label" for="before_services_notes">Before Services Notes:</label>
                        <textarea name="before_services_notes" placeholder="Before Services Notes" class="form-control"
                            id="before_services_notes" cols="" rows="2"></textarea>
                        <span class="error-msg" id="error-before_services_notes" style="color: red"></span>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <label class="form-label" for="after_services_notes">After Services Notes:</label>
                        <textarea name="after_services_notes" placeholder="After Services Notes" class="form-control"
                            id="after_services_notes" cols="" rows="2"></textarea>
                        <span class="error-msg" id="error-after_services_notes" style="color: red"></span>
                    </div>
                    <div class="col-sm-12">
                        <button type="reset" class="btn iq-bg-danger float-right" data-toggle="tooltip"
                            data-placement="bottom" data-original-title="Reset">Reset</button>
                        <button type="submit" class="btn btn-primary float-right my-0" data-toggle="tooltip"
                            data-placement="bottom" data-original-title="Save">Save</button>
                    </div>
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

            var now = new Date();


            var nextReminderDate = new Date(now.getFullYear(), now.getMonth() + 6, now.getDate(), now
                .getHours(), now.getMinutes());
            var nextReminderFormatted = nextReminderDate.getFullYear() + '-' +
                ('0' + (nextReminderDate.getMonth() + 1)).slice(-2) + '-' +
                ('0' + nextReminderDate.getDate()).slice(-2) + 'T' +
                ('0' + nextReminderDate.getHours()).slice(-2) + ':' +
                ('0' + nextReminderDate.getMinutes()).slice(-2);
            $("#next_reminder").val(nextReminderFormatted);

            $("#after_sixmonths_date").click(function() {
                var nextReminderDate = new Date(now.getFullYear(), now.getMonth() + 6, now.getDate(), now
                    .getHours(), now.getMinutes());
                var nextReminderFormatted = nextReminderDate.getFullYear() + '-' +
                    ('0' + (nextReminderDate.getMonth() + 1)).slice(-2) + '-' +
                    ('0' + nextReminderDate.getDate()).slice(-2) + 'T' +
                    ('0' + nextReminderDate.getHours()).slice(-2) + ':' +
                    ('0' + nextReminderDate.getMinutes()).slice(-2);
                $("#next_reminder").val(nextReminderFormatted);
            });

            $("#after_ninemonths_date").click(function() {
                var nextReminderDate = new Date(now.getFullYear(), now.getMonth() + 9, now.getDate(), now
                    .getHours(), now.getMinutes());
                var nextReminderFormatted = nextReminderDate.getFullYear() + '-' +
                    ('0' + (nextReminderDate.getMonth() + 1)).slice(-2) + '-' +
                    ('0' + nextReminderDate.getDate()).slice(-2) + 'T' +
                    ('0' + nextReminderDate.getHours()).slice(-2) + ':' +
                    ('0' + nextReminderDate.getMinutes()).slice(-2);
                $("#next_reminder").val(nextReminderFormatted);
            });


            $("#createreminder").change(function() {
                if (this.checked) {
                    $('#remindercustomerformcontainer').css('display', 'block');
                } else {
                    $('#remindercustomerformcontainer').css('display', 'none');
                    $('#remindercustomerformcontainer input').val('');
                    $('#remindercustomerformcontainer textarea').val('');
                    $('#remindercustomerformcontainer select').each(function() {
                        $(this).val($(this).find('option:first').val());
                    });

                }
            });
            // get and set country data in country dropdown
            $.ajax({
                type: 'GET',
                url: "{{ route('country.index') }}",
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
                        $('#country').val(1);
                        loadstate(1, 7);
                        loadcity(7);
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#country').append(`<option disabled> No Data Found</option>`);
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
                    Toast.fire({
                        icon: "error",
                        title: errorMessage
                    });
                }
            });


            //show state data in dropdown
            function loadstate(country, state) {
                let stateSearchUrl = "{{ route('state.search', '__countryId__') }}".replace('__countryId__',
                    country);
                $.ajax({
                    type: 'GET',
                    url: stateSearchUrl,
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
                let citySearchUrl = "{{ route('city.search', '__stateId__') }}".replace('__stateId__', state);
                $.ajax({
                    type: 'GET',
                    url: citySearchUrl,
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
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }



            // get and set state data when country select
            $('#country').on('change', function() {
                loadershow();
                var country_id = $(this).val();
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                stateSearchUrl = "{{ route('state.search', '__countryId__') }}".replace('__countryId__',
                    country_id);
                $.ajax({
                    type: 'GET',
                    url: stateSearchUrl,
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
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            });

            // get and set city data when select/change state
            $('#state').on('change', function() {
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
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#city').append(`<option disabled> No Data Found</option>`);
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            });


            // submit form 
            $('#remindercustomerform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('remindercustomer.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location =
                                "{{ route('admin.remindercustomer') }}"; // redirect on customer list page

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
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            });




        });
    </script>
@endpush
