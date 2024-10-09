@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Add New Customer Support
@endsection
@section('title')
    New Customer Support
@endsection

@section('style')
    <style>
        .multiselect {
            border: 0.5px solid #00000073;
        }
    </style>
@endsection

@section('form-content')
    <form id="ticketform" name="ticketform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                        placeholder="company_id" required />
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                        placeholder="user_id" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <label class="form-label" for="first_name">First Name:</label><span style="color:red;"> *</span>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="last_name">Last Name:</label><span style="color:red;"> *</span>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email"
                        placeholder="Professional Email" />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="contact_no">Mobile Number:</label><span style="color:red;"> *</span>
                    <input type="text" class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" minlength="10" maxlength="15" onkeypress="return isNumberKey(event);"
                        onkeyup="numberMobile(event);" required />
                    <span class="error-msg" id="error-contact_no" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="last_call">Last Call:</label>
                    <input type="datetime-local" class="form-control" name="last_call" id="last_call"
                        placeholder="Last Call" />
                    <span class="error-msg" id="error-last_call" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="number_of_call">Number Of Call:</label>
                    <input type="number" class="form-control" value="0" name="number_of_call" min="0"
                        max="10" id="number_of_call">
                    <span class="error-msg" id="error-number_of_call" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="status">Status:</label>
                    <select name="status" class="form-control" id="status">
                        <option value="" disabled selected>Status</option>
                        <option value='Open'>Open</option>
                        <option value='In Progress'>In Progress</option>
                        <option value='Resolved'>Resolved</option>
                        <option value='Cancelled'>Cancelled</option>
                    </select>
                    <span class="error-msg" id="error-status" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="assignedto">Assigned To:</label><span style="color:red;">
                        *</span><br />
                    <select name="assignedto[]" class="form-control multiple" id="assignedto" multiple>
                        <option value="" disabled selected>Select User</option>
                    </select>
                    <span class="error-msg" id="error-assignedto" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label class="form-label" for="last_call">Website Url:</label>
                    <input type="text" class="form-control" name="web_url" id="web_url"
                        placeholder="Website Url" />
                    <span class="error-msg" id="error-web_url" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label class="form-label" for="notes">Notes:</label>
                    <textarea name="notes" placeholder="notes" class="form-control" id="notes" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-notes" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                 <div class="col-sm-12">
                    <button type="reset" id="resetbtn" data-toggle=tooltip  data-placement="bottom" data-original-title="Cancel" class="btn iq-bg-danger float-right"><i class="ri-close-line"></i></button>
                    <button type="submit" data-toggle=tooltip  data-placement="bottom" data-original-title="Submit" class="btn btn-primary float-right my-0"><i class="ri-check-line"></i></button>
                 </div>
            </div>
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

            $('#assignedto').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                    '<option selected disabled>-- Select User --</option>'); // prepend "Please choose an option"
                }
                $('#assignedto').multiselect('rebuild');
            });

            $('#notes').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['table']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                placeholder: 'Add Notes',
                tabsize: 2,
                height: 100
            });

            // get & set user data into form input for assign to ticket 
            $.ajax({
                type: 'GET',
                url: "{{ route('user.customersupportindex') }}",
                data: {
                    user_id: "{{ session()->get('user_id') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.user != '') {
                        global_response = response;
                        // You can update your HTML with the data here if needed     
                        $.each(response.user, function(key, value) {
                            var optionValue = value.firstname + ' ' + value.lastname;
                            $('#assignedto').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#assignedto').multiselect({
                                enableFiltering: true,
                                includeSelectAllOption: true,
                                enableCaseInsensitiveFiltering: true
                            },
                            'rebuild'); // Rebuild multiselect after appending options
                        loaderhide();
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                        loaderhide();
                    }
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });


            // user will be redirect if he is click on form cancel button
            $('#resetbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.customersupport') }}";
            });
            // submit form data
            $('#ticketform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serializeArray();
                formdata.push({
                    name: "notes",
                    value: $('#notes').summernote('code')
                });
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customersupport.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.customersupport') }}";

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
                })
            });
        });
    </script>
@endpush
