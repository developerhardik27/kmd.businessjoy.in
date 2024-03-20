@php
    $folder = session('folder_name');
@endphp
@extends($folder.'.admin.masterlayout')

@section('page_title')
{{ config('app.name') }} - Add New Lead
@endsection
@section('title')
    New Lead
@endsection

@section('style')
    <style>
        .multiselect {
            border: 0.5px solid #00000073;
        }
    </style>
@endsection

@section('form-content')
    <form id="leadform" name="leadform">
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
                    <label class="form-label" for="name">Full Name:</label> <span style="color:red;">*</span>
                    <input type="text" class="form-control" name="leadname" id="name" placeholder="Name" required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email"
                        placeholder="Professional Email" />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="contact_no">Mobile Number:</label> <span style="color:red;">*</span>
                    <input type="text" class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" maxlength="13" onkeypress="return isNumberKey(event);"
                        onkeyup="numberMobile(event);" required />
                    <span class="error-msg" id="error-contact_no" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="title">Job Title:</label>
                    <select name="title" class="form-control" id="title">
                        <option value="" disabled selected>Select Title</option>
                        <option value=" Student">Student</option>
                        <option value="Employee">Employee</option>
                        <option value="Manager">Manager</option>
                        <option value="Business Owner">Business Owner</option>
                        <option value="Self Employeed">Self Employeed</option>
                        <option value=" Other"> Other</option>
                    </select>
                    <span class="error-msg" id="error-title" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="budget">Budget:</label>
                    <select name="budget" class="form-control" id="budget">
                        <option value="" disabled selected>Select budget</option>
                        <option value="10,000 to 50,000">₹10,000 to 50,000</option>
                        <option value="More tan 50,000">More tan ₹50,000</option>
                        <option value="More than 1,00,000">More than ₹ 1,00,000</option>
                    </select>
                    <span class="error-msg" id="error-budget" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="status">Status:</label>
                    <select name="status" class="form-control" id="status">
                        <option value="" disabled selected>Status</option>
                        <option value='Not Interested'>Not Interested</option>
                        <option value='Not Receiving'>Not Receiving</option>
                        <option value='New Lead'>New Lead</option>
                        <option value='Interested'>Interested</option>
                        <option value='Switch Off'>Switch Off</option>
                        <option value='Does Not Exist'>Does Not Exist</option>
                        <option value='Email Sent'>Email Sent</option>
                        <option value='Wrong Number'>Wrong Number</option>
                        <option value='By Mistake'>By Mistake</option>
                        <option value='Positive'>Positive</option>
                        <option value='Busy'>Busy</option>
                        <option value='Call Back'>Call Back</option>
                    </select>
                    <span class="error-msg" id="error-status" style="color: red"></span>
                </div>

            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="company">Company Name:</label>
                    <input type="text" class="form-control" name="company" id="company"
                        placeholder="Company Name" />
                    <span class="error-msg" id="error-company" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="customer_type">Customer Type:</label>
                    <select name="customer_type" class="form-control" id="customer_type">
                        <option disabled selected>Select Customer Type</option>
                        <option value="local">Local</option>
                        <option value="Global">Global</option>
                    </select>
                    <span class="error-msg" id="error-customer_type" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="last_follow_up">Last Follow Up:</label>
                    <input type="date" class="form-control" name="last_follow_up" id="last_follow_up"
                        placeholder="Last_follow_up" />
                    <span class="error-msg" id="error-last_follow_up" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="next_follow_up">Next Follow Up:</label>
                    <input type="date" class="form-control" name="next_follow_up" id="next_follow_up"
                        placeholder="Next_follow_up" />
                    <span class="error-msg" id="error-next_follow_up" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="no_of_follow_up">Number Of Follow Up:</label>
                    <input type="number" class="form-control" value="0" name="number_of_follow_up" min="0"
                        max="10" id="no_of_follow_up">
                    <span class="error-msg" id="error-number_of_follow_up" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="source">Source:</label>
                    <input type="text" class="form-control" name="source" id="source" placeholder="source"
                        value='Manual' />
                    <span class="error-msg" id="error-source" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="leadstage">Lead Stage:</label>
                    <select name="leadstage" class="form-control" id="leadstage">
                        <option value="" disabled selected>LeadStage</option>
                        <option value='New Lead'>New Lead</option>
                        <option value='Requirement Ghathering'>Requirement Ghathering</option>
                        <option value='Quotation'>Quotation</option>
                        <option value="In Followup">In Followup</option>
                        <option value='Sale'>Sale</option>
                        <option value='Cancelled'>Cancelled</option>
                        <option value='Disqualified'>Disqualified</option>
                    </select>
                    <span class="error-msg" id="error-leadstage" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="assignedto">Assigned To:</label> <span
                        style="color:red;">*</span><br />
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
                    <label class="form-label" for="notes">Notes:</label>
                    <textarea name="notes" placeholder="notes" class="form-control" id="notes" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-notes" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
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


            $('#notes').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['table']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']]
                ],
                placeholder: 'Add Notes',
                tabsize: 2,
                height: 100
            });
            // get user data for assigned to field
            $.ajax({
                type: 'GET',
                url: '{{ route('user.index') }}',
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
                        $('#assignedto').multiselect(
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

           // redirect on lead list page if click on cancel button
            $('#resetbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.lead') }}";
            })
            // submit form data
            $('#leadform').submit(function(event) {
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
                    url: "{{ route('lead.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.lead') }}";

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
