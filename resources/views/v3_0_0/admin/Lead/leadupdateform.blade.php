@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Lead
@endsection
@section('title')
    Update Lead
@endsection
@section('style')
    <style>
        .modal-backdrop.show {
            display: none;
            z-index: 999999999;
        }
    </style>
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
                    <label class="form-label" for="first_name">First Name:</label> <span style="color:red;">*</span>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name"
                        required />
                    <span class="error-msg" id="error-first_name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="last_name">last Name:</label> <span style="color:red;">*</span>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name"
                        required />
                    <span class="error-msg" id="error-last_name" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-md-6">
                    <label class="form-label" for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email"
                        placeholder="Professional Email" />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="contact_no">Mobile Number:</label> <span style="color:red;">*</span>
                    <input type="text" class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" minlength="10" maxlength="15"
                        onkeypress="return isNumberKey(event);" onkeyup="numberMobile(event);" required />
                    <span class="error-msg" id="error-contact_no" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="title">Job Title:</label>
                    <select name="title" class="form-control" id="title">
                        <option value="" disabled selected>Select Title</option>
                        <option value="Student">Student</option>
                        <option value="Employee">Employee</option>
                        <option value="Manager">Manager</option>
                        <option value="Business Owner">Business Owner</option>
                        <option value="Self Employeed">Self Employeed</option>
                        <option value="Other"> Other</option>
                    </select>
                    <span class="error-msg" id="error-title" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="budget">Budget:</label>
                    <select name="budget" class="form-control" id="budget">
                        <option value="" disabled selected>Select budget</option>
                        <option value="10,000 to 50,000">₹10,000 to 50,000</option>
                        <option value="More tan 50,000">More tan ₹50,000</option>
                        <option value="More than 1,00,000">More than ₹ 1,00,000</option>
                        <option value="Less than $1000">Less than $1000</option>
                        <option value="$1,000 - $5,000">$1,000 - $5,000</option>
                        <option value="$5,000 - $10,000">$5,000 - $10,000</option>
                        <option value="More than $10,000">More than $10,000</option>
                    </select>
                    <span class="error-msg" id="error-budget" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="status">Status:</label>
                    <select name="status" class="form-control" id="status">
                        <option value="" disabled selected>Select Status</option>

                    </select>
                    <span class="error-msg" id="error-status" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="company">Company Name:</label>
                    <input type="text" class="form-control" name="company" id="company"
                        placeholder="Company Name" />
                    <span class="error-msg" id="error-company" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="last_follow_up">Last Follow Up:</label>
                    <input type="datetime-local" class="form-control" name="last_follow_up" id="last_follow_up"
                        placeholder="Last_follow_up" />
                    <span class="error-msg" id="error-last_follow_up" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="next_follow_up">Next Follow Up:</label>
                    <input type="datetime-local" class="form-control" name="next_follow_up" id="next_follow_up"
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
                        <option value="" disabled selected>Select LeadStage</option>

                    </select>
                    <span class="error-msg" id="error-leadstage" style="color: red"></span>
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
                    <label class="form-label" for="last_call">Website Url:</label>
                    <input type="text" class="form-control" name="web_url" id="web_url"
                        placeholder="Website Url" />
                    <span class="error-msg" id="error-web_url" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="assignedto">Assigned To:</label> <span
                        style="color:red;">*</span><br />
                    <select name="assignedto[]" class="form-control multiple" id="assignedto" multiple>
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
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="reset" id="resetbtn" data-toggle=tooltip data-placement="bottom"
                        data-original-title="Cancel" class="btn iq-bg-danger float-right">Cancel</button>
                    <button type="submit" data-toggle=tooltip data-placement="bottom" data-original-title="Update"
                        class="btn btn-primary float-right my-0">Save</button>
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


            function getLeadStatusData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('lead.leadstatusname') }}",
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


            function getLeadStageData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('lead.leadstagename') }}",
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

            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.leaduserindex') }}",
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
                    const [userDataResponse, leadStageDataResponse, leadStatusDataResponse] = await Promise.all(
                        [
                            getUserData(),
                            getLeadStageData(),
                            getLeadStatusData()
                        ]);

                    // Check if user data is successfully fetched
                    if (userDataResponse.status == 200 && userDataResponse.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(userDataResponse.user, function(key, value) {
                            var optionValue = value.firstname + ' ' + value.lastname;
                            $('#assignedto').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#assignedto').multiselect('rebuild'); // Rebuild multiselect after appending options 
                    } else if (userDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: userDataResponse.message
                        });
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                    }
                    // Check if lead stage data is successfully fetched
                    if (leadStageDataResponse.status == 200 && leadStageDataResponse.lead != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(leadStageDataResponse.lead, function(key, value) {
                            var optionValue = value.leadstage_name;
                            $('#leadstage').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                    } else if (leadStageDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: leadStageDataResponse.message
                        });
                    } else {
                        $('#leadstage').append(`<option> No Lead Stage Found </option>`);
                    }

                    // Check if lead status data is successfully fetched
                    if (leadStatusDataResponse.status == 200 && leadStatusDataResponse.leadstatus != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(leadStatusDataResponse.leadstatus, function(key, value) {
                            var optionValue = value.leadstatus_name;
                            $('#status').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                    } else if (leadStatusDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: leadStatusDataResponse.message
                        });
                    } else {
                        $('#status').append(`<option> No Lead Stage Found </option>`);
                    }
                    loaderhide();
                    // Load data
                    await loaddata();

                    // Further code execution after successful AJAX calls and HTML appending
                    // Your existing logic here

                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: "error",
                        title: "An error occurred while initializing"
                    });
                    loaderhide();
                }
            }

            initialize();
            $('#assignedto').multiselect({
                nonSelectedText: '-- Select User --', // Acts as a placeholder
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });

            function loaddata() {
                var edit_id = @json($edit_id);
                let leadSearchUrl = "{{ route('lead.search', '__editId__') }}".replace('__editId__', edit_id);
                // show old data in fields
                $.ajax({
                    type: 'GET',
                    url: leadSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        user_id: " {{ session()->get('user_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            data = response.lead[0]
                            // You can update your HTML with the data here if needed
                            $('#first_name').val(data.first_name);
                            $('#last_name').val(data.last_name);
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
                            $('#notes').summernote('code', data.notes);
                            $('#leadstage').val(data.lead_stage);
                            $('#created_at').val(data.created_at_formatted);
                            $('#updated_at').val(data.updated_at_formatted);
                            $('#source').val(data.source);
                            $('#company').val(data.company);
                            $('#web_url').val(data.web_url); 
                            
                            assignedto = data.assigned_to;
                            assignedtoarray = assignedto.split(',');
                            assignedtoarray.forEach(function(value) {
                                $('#assignedto').multiselect('select', value);
                            });
                            $('#assignedto').multiselect('rebuild');
                        } else if (response.status == 500) {
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
            }


            //submit form
            $('#leadupdateform').submit(function(event) {
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
                    url: "{{ route('lead.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.lead') }}";
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });;
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
            })
        });
    </script>
@endpush
