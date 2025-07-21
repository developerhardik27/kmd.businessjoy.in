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
    <form id="leadupdateform" name="leadupdateform" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" value="{{ session('user_id') }}">
        <input type="hidden" name="company_id" value="{{ session('company_id') }}">
        <input type="hidden" name="token" value="{{ session('api_token') }}">

        <div class="row">

            <!-- Lead Info Card (col-8) -->
            <div class="col-md-8">
                <div class="iq-card mb-4">
                    <div class="iq-card-body">
                        <h6 class="mb-3 font-weight-bold">Lead Information</h6>
                        <div class="form-row">
                            <div class="col-sm-6 mb-3">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    placeholder="First Name" required>
                                <span class="error-msg" id="error-first_name" style="color:red;"></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    placeholder="Last Name" required>
                                <span class="error-msg" id="error-last_name" style="color:red;"></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Email"
                                    name="email">
                                <span class="error-msg" id="error-email" style="color:red;"></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="contact_no">Mobile Number</label>
                                <input type="text" class="form-control" id="contact_no" placeholder="Mobile Number"
                                    name="contact_no" minlength="10" maxlength="15" onkeypress="return isNumberKey(event);"
                                    onkeyup="numberMobile(event);">
                                <span class="error-msg" id="error-contact_no" style="color:red;"></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="lead_title">Lead Title</label>
                                <input type="text" class="form-control" placeholder="Lead Title" id="lead_title"
                                    name="lead_title">
                                <span class="error-msg" id="error-lead_title" style="color:red;"></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="title">Job Title</label>
                                <select class="form-control" id="title" name="title">
                                    <option selected disabled>Select Title</option>
                                    <option value="Student">Student</option>
                                    <option value="Employee">Employee</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Business Owner">Business Owner</option>
                                    <option value="Self Employeed">Self Employeed</option>
                                    <option value="Other">Other</option>
                                </select>
                                <span class="error-msg" id="error-title" style="color:red;"></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="budget">Budget</label>
                                <select class="form-control" id="budget" name="budget">
                                    <option selected disabled>Select Budget</option>
                                    <option value="10,000 to 50,000">10,000 to 50,000</option>
                                    <option value="More than 50,000">More than 50,000</option>
                                    <option value="More than 1,00,000">More than 1,00,000</option>
                                    <option value="Less than $1000">Less than $1000</option>
                                    <option value="$1,000 - $5,000">$1,000 - $5,000</option>
                                    <option value="$5,000 - $10,000">$5,000 - $10,000</option>
                                    <option value="More than $10,000">More than $10,000</option>
                                </select>
                                <span class="error-msg" id="error-budget" style="color:red;"></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="company">Company Name</label>
                                <input type="text" class="form-control" placeholder="Company Name" id="company"
                                    name="company">
                                <span class="error-msg" id="error-company" style="color:red;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Follow-up (col-4) -->
            <div class="col-md-4">
                <div class="iq-card mb-4 p-3 shadow-sm match-height">
                    <h6 class="mb-4 font-weight-bold">Follow-up</h6>
                    <div class="form-group mb-3">
                        <label for="last_follow_up">Last Follow Up</label>
                        <input type="datetime-local" class="form-control form-control-sm" id="last_follow_up"
                            name="last_follow_up">
                        <small class="text-danger" id="error-last_follow_up"></small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="next_follow_up">Next Follow Up</label>
                        <input type="datetime-local" class="form-control form-control-sm" id="next_follow_up"
                            name="next_follow_up">
                        <small class="text-danger" id="error-next_follow_up"></small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="no_of_follow_up">Number of Follow Ups</label>
                        <input type="number" min="0" max="10" class="form-control form-control-sm"
                            id="no_of_follow_up" name="number_of_follow_up" value="0">
                        <small class="text-danger" id="error-number_of_follow_up"></small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="assignedto">Assigned To</label>
                        <select class="form-control form-control-sm" id="assignedto" name="assignedto[]" multiple>
                            <!-- options -->
                        </select>
                        <small class="text-danger" id="error-assignedto"></small>
                    </div>
                </div>
            </div>

            <!-- Status (full width) -->
            <div class="col-md-12">
                <div class="iq-card mb-4 p-3 shadow-sm">
                    <h6 class="mb-4 font-weight-bold">Status</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status">Status</label>
                            <select class="form-control form-control-sm" id="status" name="status">
                                <option selected disabled>Select Lead Status</option>
                                <!-- options -->
                            </select>
                            <small class="text-danger" id="error-status"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="leadstage">Lead Stage</label>
                            <select class="form-control form-control-sm" id="leadstage" name="leadstage">
                                <!-- options -->
                            </select>
                            <small class="text-danger" id="error-leadstage"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="customer_type">Customer Type</label>
                            <select class="form-control form-control-sm" id="customer_type" name="customer_type">
                                <option selected disabled>Select Customer Type</option>
                                <option value="local">Local</option>
                                <option value="Global">Global</option>
                            </select>
                            <small class="text-danger" id="error-customer_type"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="source">Source</label>
                            <input list="source_list" class="form-control form-control-sm" id="source" name="source"
                                placeholder="Source">
                            <datalist id="source_list"></datalist>
                            <small class="text-danger" id="error-source"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="web_url">Website URL</label>
                            <input type="text" class="form-control form-control-sm" id="web_url" name="web_url"
                                placeholder="Website URL">
                            <small class="text-danger" id="error-web_url"></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="col-12 mb-2">
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Attachments</h6>

                        <!-- Existing attachments (populated dynamically via AJAX) -->
                        <div id="existing-attachments"></div>

                        <!-- New attachments -->
                        <div id="attachment-wrapper">
                            <div class="input-group mb-2">
                                <input type="file" name="attachment[]"/>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger remove-attachment">Remove</button>
                                </div>
                            </div>
                        </div>

                        <!-- Add More Button -->
                        <button type="button" class="btn btn-primary btn-sm" id="add-attachment">Add More</button>

                        <!-- Help Text -->
                        <p class="text-primary mt-2 mb-0">
                            Please select image files (JPG, JPEG, PNG), video files (MP4, WebM), or PDF files under 10 MB.
                        </p>
                        <span class="error-msg" id="error-attachment" style="color: red"></span>
                    </div>
                </div>
            </div>

            <!-- Notes full width (col-12) -->
            <div class="col-12">
                <div class="iq-card mb-4">
                    <div class="iq-card-body">
                        <h6 class="mb-3">Notes</h6>
                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Notes"></textarea>
                        <span class="error-msg" id="error-notes" style="color:red;"></span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <button type="reset" id="resetbtn" data-toggle=tooltip data-placement="bottom"
                    data-original-title="Cancel" class="btn iq-bg-danger float-right">Cancel</button>
                <button type="submit" data-toggle=tooltip data-placement="bottom" data-original-title="Update"
                    class="btn btn-primary float-right my-0">Save</button>
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

            // Array to keep removed existing files
            var removedFiles = [];

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

            $('#add-attachment').click(function() {
                const inputHtml = `
                    <div class="input-group mb-2">
                        <input type="file" name="attachment[]" />
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-attachment">Remove</button>
                        </div>
                    </div>
                `;
                $('#attachment-wrapper').append(inputHtml);
            });

            // 🔒 Confirm before removing new (unsaved) attachment
            $('#attachment-wrapper').on('click', '.remove-attachment', function() {
                var element = $(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "you want to remove this attachment?.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        element.closest('.input-group').remove();
                    }
                })
            });

            // 🔒 Confirm before removing existing (already uploaded) attachment
            $('#existing-attachments').on('click', '.remove-existing-attachment', function() {
                var row = $(this).closest('.existing-attachment-row');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This file is already uploaded. Do you want to remove it?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var filePath = row.find('input[name="existing_attachments[]"]').val();
                        // Add removed file path to array
                        removedFiles.push(filePath);

                        // Remove from DOM
                        row.remove();

                        // Update or create hidden input to send removed files to server
                        if ($('#removed_attachments').length === 0) {
                            $('<input>').attr({
                                type: 'hidden',
                                id: 'removed_attachments',
                                name: 'removed_attachments'
                            }).appendTo('#leadupdateform');
                        }

                        // Set removed files as JSON string
                        $('#removed_attachments').val(JSON.stringify(removedFiles));

                    }
                })
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
                            $('#lead_title').val(data.lead_title);
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

                            // Prefill attachments
                            if (data.attachment) {
                                try {
                                    const attachments = JSON.parse(data.attachment);
                                    $('#existing-attachments').empty();

                                    attachments.forEach((filePath, index) => {
                                        const fileUrl = `/uploads/${filePath}`;
                                        const fileName = filePath.split('/').pop();

                                        const html = `
                                            <div class="d-flex align-items-center mb-2 existing-attachment-row">
                                                <a href="${fileUrl}" target="_blank" class="mr-2">${fileName}</a>
                                                <button type="button" class="btn btn-danger btn-sm remove-existing-attachment">Remove</button>
                                                <input type="hidden" name="existing_attachments[]" value="${filePath}">
                                            </div>
                                        `;
                                        $('#existing-attachments').append(html);
                                    });
                                } catch (e) {
                                    console.error('Invalid attachment data');
                                }
                            }

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
                // Create FormData object from the form, which includes files
                let formdata = new FormData(this);

                // Append Summernote content manually
                formdata.set('notes', $('#notes').summernote('code'));
                $.ajax({
                    type: 'POST',
                    url: "{{ route('lead.update', $edit_id) }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
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
                            var firstErrorElement = null;

                            $.each(errors, function(key, value) {
                                var errorElement = $('#error-' + key);

                                // Set the error text
                                errorElement.text(value[0]);

                                // Store the first element to scroll to
                                if (!firstErrorElement) {
                                    firstErrorElement = errorElement;
                                }
                            });

                            // Scroll to the first error if exists
                            if (firstErrorElement) {
                                $('html, body').animate({
                                    scrollTop: firstErrorElement.offset().top -
                                        100 // Offset for better visibility
                                }, 1000);
                            }
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
