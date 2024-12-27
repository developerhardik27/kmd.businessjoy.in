@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Reminder
@endsection
@section('title')
    New Reminder
@endsection

@section('form-content')
<form id="newreminderform" name="newreminderform">
    @csrf
    <div class="form-group">
        <div class="form-row">
            <div class="col-sm-6">
                <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                    placeholder="user_id" required />
                <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                    placeholder="company_id" required />
                <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                    placeholder="token" required />
                <label for="customer">Customer</label><span style="color:red;">*</span><br>
                <select class="form-control" id="customer" name="customer_id" required>
                    <option selected="" disabled=""> Select Customer</option>
                </select>
                <span class="error-msg" id="error-customer_id" style="color: red"></span>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="service_type">Service Type:</label><span style="color:red;">*</span>
                <select name="service_type" class="form-control" id="service_type">
                    <option disabled selected>Select Customer Type</option>
                    <option value="paid">Paid</option>
                    <option value="free">Free</option>
                </select>
                <span class="error-msg" id="error-service_type" style="color: red"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="form-row">
            <div class="col-sm-6">
                <label class="form-label" for="product_name">Product Name:</label><span style="color:red;">*</span>
                <input type="text" class="form-control" name="product_name" id="product_name"
                    placeholder="Product Name" />
                <span class="error-msg" id="error-product_name" style="color: red"></span>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="product_unique_id">Product Unique Id:</label>
                <input type="text" class="form-control" name="product_unique_id" id="product_unique_id"
                    placeholder="Product Unique Id" />
                <span class="error-msg" id="error-product_unique_id" style="color: red"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="form-row">
            <div class="col-sm-6">
                <label class="form-label" for="amount"> Amount :</label><span style="color:red;">*</span>
                <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount" />
                <span class="error-msg" id="error-amount" style="color: red"></span>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="next_reminder">Next Reminder Date:</label><span style="color:red;">*</span>
                <input type="datetime-local" class="form-control" name="next_reminder" id="next_reminder" />
                <span class="m-1 text-info btn dynamic-date" data-months="6">After 6 Months</span>
                <span class="m-1 text-info btn dynamic-date" data-months="9">After 9 Months</span>
                <span class="error-msg" id="error-next_reminder" style="color: red"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="form-row">
            <div class="col-sm-12">
                <label class="form-label" for="before_services_notes">Before Services Notes:</label>
                <textarea name="before_services_notes" placeholder="Before Services Notes" class="form-control"
                    id="before_services_notes" cols="" rows="2"></textarea>
                <span class="error-msg" id="error-before_services_notes" style="color: red"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="form-row">
            <div class="col-sm-12">
                <label class="form-label" for="after_services_notes">After Services Notes:</label>
                <textarea name="after_services_notes" placeholder="After Services Notes" class="form-control"
                    id="after_services_notes" cols="" rows="2"></textarea>
                <span class="error-msg" id="error-after_services_notes" style="color: red"></span>
            </div>
        </div>
    </div>
    <div class="button-container">
        <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
        <div id="loader" class="loader"></div>
        <button id="resetbtn" type="reset" class="btn iq-bg-danger">Cancel</button>
    </div>
</form>
@endsection

@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        // mobile number validation
        function isNumberKey(e) {
            var evt = e || window.event;

            if (evt) {
                var charCode = evt.keyCode || evt.which;
            } else {
                return true;
            }

            // Allow numeric characters (0-9), plus sign (+), tab (9), backspace (8), delete (46), left arrow (37), right arrow (39)
            if ((charCode > 47 && charCode < 58) || charCode == 9 || charCode == 8 || charCode == 46 ||
                charCode == 37 || charCode == 39 || charCode == 43) {
                return true;
            }

            return false;
        }

        function numberMobile(e) {
            e.target.value = e.target.value.replace(/[^+\d]/g, ''); // Allow + and digits
            return false;
        }

        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data


            var now = new Date();
            var formattedDateTime = now.getFullYear() + '-' +
                ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
                ('0' + now.getDate()).slice(-2) + 'T' +
                ('0' + now.getHours()).slice(-2) + ':' +
                ('0' + now.getMinutes()).slice(-2);
            $('#service_completed_date').val(formattedDateTime);

            $(".dynamic-date").click(function() {
                var monthsToAdd = parseInt($(this).data("months"));
                var now = new Date();
                var nextReminderDate = new Date(now.getFullYear(), now.getMonth() + monthsToAdd, now
                    .getDate(), now
                    .getHours(), now.getMinutes());
                var nextReminderFormatted = nextReminderDate.getFullYear() + '-' +
                    ('0' + (nextReminderDate.getMonth() + 1)).slice(-2) + '-' +
                    ('0' + nextReminderDate.getDate()).slice(-2) + 'T' +
                    ('0' + nextReminderDate.getHours()).slice(-2) + ':' +
                    ('0' + nextReminderDate.getMinutes()).slice(-2);
                $("#next_reminder").val(nextReminderFormatted);
            });

            // redirect on lead list page on click cancel button
            $('#resetbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.reminder') }}";
            });


            function getCustomerData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('remindercustomer.customers') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }



            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [customerDataResponse] = await Promise.all(
                        [
                            getCustomerData()
                        ]);

                    // Check if user data is successfully fetched
                    if (customerDataResponse.status == 200 && customerDataResponse.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(customerDataResponse.customer, function(key, value) {
                            var optionValue = value.name;
                            $('#customer').append(
                                `<option value="${value.id}">${optionValue}</option>`);
                        });

                    } else if (customerDataResponse.status == 500) {
                        toastr.error(customerDataResponse.message);
                    } else {
                        $('#customer').append(`<option> No Custoer Found </option>`);
                    }

                    loaderhide();
                    // Load data
                    await loaddata();

                    // Further code execution after successful AJAX calls and HTML appending
                    // Your existing logic here

                } catch (error) {
                    console.error('Error:', error);
                    toastr.error("An error occurred while initializing");
                    loaderhide();
                }
            }

            initialize();

            function loaddata() {
                var edit_id = @json($edit_id);
                // show old data in fields
                let reminderSearchUrl = "{{ route('reminder.search','__editId__') }}".replace('__editId__',edit_id);
                $.ajax({
                    type: 'GET',
                    url: reminderSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        user_id: " {{ session()->get('user_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            data = response.reminder[0]
                            // You can update your HTML with the data here if needed
                            $('#service_type').val(data.service_type);
                            $('#product_name').val(data.product_name);
                            $('#product_unique_id').val(data.product_unique_id);
                            $('#amount').val(data.amount);
                            $('#next_reminder').val(data.next_reminder_date);
                            $('#before_services_notes').val(data.before_service_note)
                            $('#after_services_notes').val(data.after_service_note);
                            $('#customer').find('option:disabled').remove(); // remove disabled option
                            $('#customer').val(data.customer_id);

                        } else if (response.status == 500) {
                            toastr.error(response.message);
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


            //submit form
            $('#newreminderform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serializeArray();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('reminder.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.reminder') }}";
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
                })
            });
        });
    </script>
@endpush
