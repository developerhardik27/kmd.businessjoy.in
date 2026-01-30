@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Ticket
@endsection
@section('title')
    Update Ticket
@endsection


@section('form-content')
    <form id="ticketupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="ticket">Ticket Number:</label>
                    <input type="number" readonly class="form-control" name="ticket" id="ticket">
                    <span class="error-msg" id="error-ticket" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                        placeholder="user_id" required />
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                        placeholder="company_id" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                </div> 
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="first_name">First Name:</label><span style="color:red;"> *</span>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="last_name">Last Name:</label><span style="color:red;"> *</span>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div> 
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email"
                        placeholder="Professional Email" />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="contact_no">Mobile Number:</label><span style="color:red;">*</span>
                    <input type="text" class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" onkeyup="numberMobile(event);" minlength="10" maxlength="15"
                        required />
                    <span class="error-msg" id="error-contact_no" style="color: red"></span>
                </div>      
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="issuetype">Issue Type:</label><span style="color:red;"> *</span>
                    <select name="issuetype" class="form-control" id="issuetype">
                        <option value="" disabled>Select Issue Type</option>
                        <option value='technical' selected>Technical</option>
                        <option value='other'>Other</option>
                    </select>
                    <span class="error-msg" id="error-issuetype" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="modulename">Module Name:</label><span style="color:red;"> *</span>
                    <select name="modulename" class="form-control" id="modulename">
                        <option value="" disabled selected>Select Module</option>
                        @if ((Session::has('invoice') && Session::get('invoice') == 'yes') || Session::get('admin_role') == 1)
                            <option value='invoice'>Invoice</option>
                        @endif
                        @if ((Session::has('lead') && Session::get('lead') == 'yes') || Session::get('admin_role') == 1)
                            <option value='lead'>Lead</option>
                        @endif
                        @if ((Session::has('customersupport') && Session::get('customersupport') == 'yes') || Session::get('admin_role') == 1)
                            <option value='customersupport'>Customer Support</option>
                        @endif
                        @if ((Session::has('admin') && Session::get('admin') == 'yes') || Session::get('admin_role') == 1)
                            <option value='admin'>Admin</option>
                        @endif
                        @if ((Session::has('account') && Session::get('account') == 'yes') || Session::get('admin_role') == 1)
                            <option value='account'>Account</option>
                        @endif
                        @if ((Session::has('inventory') && Session::get('inventory') == 'yes') || Session::get('admin_role') == 1)
                            <option value='inventory'>Inventory</option>
                        @endif
                        @if ((Session::has('reminder') && Session::get('reminder') == 'yes') || Session::get('admin_role') == 1)
                            <option value='reminder'>Reminder</option>
                        @endif
                    </select>
                    <span class="error-msg" id="error-modulename" style="color: red"></span>
                </div> 
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="status">Status:</label>
                    <select name="status" class="form-control" id="status">
                        <option value="" disabled selected>Status</option>
                        <option value='pending'>Pending</option>
                        <option value='in_progress'>In Progress</option>
                        <option value='resolved'>Resolved</option>
                        <option value='cancelled'>Cancelled</option>
                    </select>
                    <span class="error-status" id="error-description" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label class="form-label" for="assignedto">Assigned To:</label><br />
                    <select name="assignedto[]" class="form-control multiple" id="assignedto" multiple>
                        <option value="" disabled selected>Select User</option>
                    </select>
                    <span class="error-msg" id="error-assignedto" style="color: red"></span>
                </div> 
                <div class="col-sm-12 mb-2">
                    <label class="form-label" for="description">Description:</label><span style="color:red;"> *</span>
                    <textarea name="description" placeholder="description" class="form-control" id="description" cols=""
                        rows="2"></textarea>
                    <span class="error-msg" id="error-description" style="color: red"></span>
                </div> 
                <div class="col-sm-12 mb-2">
                    <label class="form-label" for="remarks">Remarks:</label>
                    <textarea name="remarks" placeholder="remarks" class="form-control" id="remarks" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-remarks" style="color: red"></span>
                </div> 
                <div class="col-sm-12">
                    <button type="button" id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" class="btn btn-primary float-right my-0">Submit</button>
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


            // redirect to techsupport list page on click cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.techsupport') }}";
            });
 
            // intialize summernote editor for description
            $('#description , #remarks').summernote({
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

            // get & set user data into form input for assing to ticket 
            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.techsupportindex') }}",
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
                    const [userDataResponse] = await Promise.all([
                        getUserData()
                    ]);

                    // Check if user data is successfully fetched
                    if (userDataResponse.status == 200 && userDataResponse.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(userDataResponse.user, function(key, value) {
                            var optionValue = value.firstname + ' ' + value.lastname;
                            $('#assignedto').append(
                                `<option value="${value.id}">${optionValue}</option>`);
                        });
                        $('#assignedto').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options
                        loaderhide();
                    } else if (userDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: userDataResponse.message
                        });
                        loaderhide();
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                        loaderhide();
                    }

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
                nonSelectedText : 'Select Assigned To',
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });

            // function for get old techsupport data
            function loaddata() {
                var edit_id = @json($edit_id);
                // show old customer support data in fields
                let techSupportSearchUrl = "{{ route('techsupport.search', '__editId__') }}".replace('__editId__',
                    edit_id);
                $.ajax({
                    type: 'GET',
                    url: techSupportSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        user_id: " {{ session()->get('user_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            data = response.techsupport[0]
                            // You can update your HTML with the data here if needed
                            $('#first_name').val(data.first_name);
                            $('#last_name').val(data.last_name);
                            $('#email').val(data.email);
                            $('#contact_no').val(data.contact_no);
                            $('#status').val(data.status);
                            $('#issuetype').val(data.issue_type);
                            $('#modulename').val(data.module_name);
                            $('#number_of_call').val(data.number_of_call);
                            $('#ticket').val(data.ticket);
                            $('#description').summernote('code', data.description);
                            $('#remarks').summernote('code', data.remarks);
                            $('#assignedto').find('option:disabled').remove();
                            assignedto = data.assigned_to;
                            if (assignedto !== null) {
                                var assignedtoarray = assignedto.split(',');
                                assignedtoarray.forEach(function(value) {
                                    $('#assignedto').multiselect('select', value);
                                });
                            }
                            $('#assignedto').multiselect('rebuild');
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                            loaderhide();
                        }
                        loaderhide();
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }

            //submit form
            $('#ticketupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serializeArray();
                formdata.push({
                    name: "description",
                    value: $('#description').summernote('code')
                }, {
                    name: "remarks",
                    value: $('#remarks').summernote('code')
                });
                $.ajax({
                    type: 'POST',
                    url: "{{ route('techsupport.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.techsupport') }}";

                        } else if (response.status == 422) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                            loaderhide();
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        handleAjaxError(xhr);
                    }
                });
            })
        });
    </script>
@endpush
