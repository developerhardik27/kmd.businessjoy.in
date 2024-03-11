@extends('admin.masterlayout')
@section('page_title')
{{ config('app.name') }} - Update Lead
@endsection
@section('title')
    Update Lead
@endsection


@section('form-content')
    <form id="leadupdateform">
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
                    <label class="form-label" for="name">Name:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" required />
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
                    <label class="form-label" for="contact_no">Mobile Number:</label>
                    <input type="text" class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" maxlength="13" required />
                    <span class="error-msg" id="error-contact_no" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="title">Job Title:</label>
                    <select name="title" class="form-control" id="title">
                        <option disabled selected>Select Title</option>
                        <option value="Student">Student</option>
                        <option value="Employee">Employee</option>
                        <option value="Manager">Manager</option>
                        <option value="Business Owner">Business Owner</option>
                        <option value="Self Employeed">Self Employeed</option>
                        <option value="Other"> Other</option>
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
                        <option value="" disabled selected>Select Your Budget</option>
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
                    <label class="form-label" for="audience_type">Audience Type:</label>
                    <input type="text" class="form-control" name="audience_type" id="audience_type"
                        placeholder="Audience Type" />
                    <span class="error-msg" id="error-audience_type" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="customer_type">Customer Type:</label>
                    <select class="form-control" name="customer_type" id="customer_type" placeholder="Customer Type">
                        <option value="Global">Global</option>
                        <option value="local">Local</option>
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
                    <label class="form-label" for="no_of_attempt">Number Of Attempt:</label>
                    <input type="number" class="form-control" value="0" name="number_of_attempt" min="0"
                     id="no_of_attempt">
                    <span class="error-msg" id="error-no_of_attempt" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="created_at">Created At:</label>
                    <input type="datetime" readonly class="form-control" name="created_at" id="created_at"
                        placeholder="Created at" />
                    <span class="error-msg" id="error-created_at" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="updated_at">Updated At:</label>
                    <input type="datetime" readonly class="form-control" name="updated_at" id="updated_at"
                        placeholder="null" />
                    <span class="error-msg" id="error-updated_at" style="color: red"></span>
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
                    <label class="form-label" for="assignedto">Assigned To:</label><span
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
                <div class="col-sm-6">
                    <label class="form-label" for="company">Company Name:</label>
                    <input type="text" class="form-control" name="company" id="company"
                        placeholder="Company Name" />
                    <span class="error-msg" id="error-company" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="source">Source:</label>
                    <input type="text" class="form-control" name="source" id="source" placeholder="source" />
                    <span class="error-msg" id="error-source" style="color: red"></span>
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
            <button id="resetbtn" type="reset" class="btn iq-bg-danger">Cancel</button>
        </div>
    </form>
@endsection

@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            // redirect on lead list page on click cancel button
            $('#resetbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.lead') }}";
            })
           

            // last follow up and next follow up date validation
            $("#last_follow_up").on("change", function() {
                var lastDate = new Date($(this).val());
                var nextDateInput = $("#next_follow_up");
                var nextDate = new Date(nextDateInput.val());

                if (nextDate < lastDate) {
                    nextDateInput.val($(this).val());
                }

                nextDateInput.attr("min", $(this).val());
            });

            $("#next_follow_up").on("change", function() {
                var lastDate = new Date($("#last_follow_up").val());
                var nextDate = new Date($(this).val());

                if (nextDate < lastDate) {
                    $(this).val(lastDate.toISOString().slice(0, 10));
                }
            });
           
            //get user data for assinged  to field
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

            var edit_id = @json($edit_id);
            // show old data in fields
            $.ajax({
                type: 'GET',
                url: '/api/lead/search/' + edit_id,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: " {{ session()->get('company_id') }} ",
                    user_id: " {{ session()->get('user_id') }} "
                },
                success: function(response) {
                    if (response.status == 200) {
                        data = response.lead[0]
                        // You can update your HTML with the data here if needed
                        $('#name').val(data.name);
                        $('#email').val(data.email);
                        $('#contact_no').val(data.contact_no);
                        $('#title').val(data.title);
                        $('#budget').val(data.budget);
                        $('#audience_type').val(data.audience_type);
                        $('#customer_type').val(data.customer_type)
                        $('#status').val(data.status);
                        $('#last_follow_up').val(data.last_follow_up);
                        $('#next_follow_up').val(data.next_follow_up);
                        $('#no_of_follow_up').val(data.number_of_follow_up);
                        $('#no_of_attempt').val(data.attempt_lead);
                        $('#notes').val(data.notes);
                        $('#leadstage').val(data.lead_stage);
                        $('#created_at').val(data.created_at_formatted);
                        $('#updated_at').val(data.updated_at_formatted);
                        $('#source').val(data.source);
                        $('#company').val(data.company);
                        assignedto = data.assigned_to;
                        assignedtoarray = assignedto.split(',');
                        assignedtoarray.forEach(function(value) {
                            $('#assignedto').multiselect('select', value);
                        });
                        $('#assignedto').multiselect();
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    }
                    loaderhide();
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });
            //submit form
            $('#leadupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'Post',
                    url: "{{ route('lead.update', $edit_id) }}",
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
                        } else if (response.status == 422) {
                            toastr.error(response.errors);
                        } else {
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
