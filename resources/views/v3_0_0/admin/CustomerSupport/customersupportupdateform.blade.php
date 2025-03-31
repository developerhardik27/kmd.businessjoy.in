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
                <div class="col-sm-6">
                    <label class="form-label" for="ticket">Ticket Number:</label>
                    <input type="number" readonly class="form-control" name="ticket" id="ticket">
                    <span class="error-msg" id="error-ticket" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                        placeholder="user_id" required />
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                        placeholder="company_id" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <label class="form-label" for="last_call">Website Url:</label>
                    <input type="text" class="form-control" name="web_url" id="web_url" placeholder="Website Url" />
                    <span class="error-msg" id="error-web_url" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="first_name">First Name:</label><span style="color:red;"> *</span>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
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
                    <label class="form-label" for="contact_no">Mobile Number:</label><span style="color:red;">*</span>
                    <input type="text" class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" onkeyup="numberMobile(event);" minlength="10" maxlength="15"
                        required />
                    <span class="error-msg" id="error-contact_no" style="color: red"></span>
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
                    <span class="error-status" id="error-description" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="assignedto">Assigned To:</label><span
                        style="color:red;">*</span><br />
                    <select name="assignedto[]" class="form-control multiple" id="assignedto" multiple>
                    </select>
                    <span class="error-msg" id="error-assignedto" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="last_call">Last Call:</label>
                    <input type="datetime-local" class="form-control" name="last_call" id="last_call"
                        placeholder="last_call" />
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
                <div class="col-sm-12">
                    <label class="form-label" for="notes">Notes:</label>
                    <textarea name="notes" style="white-space: pre-line;" placeholder="notes" class="form-control" id="notes"
                        cols="" rows="2"></textarea>
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

            // redirect on customersupport page
            $('#resetbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.customersupport') }}";
            })

            // get & set user data into form input for assing to ticket 
            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.customersupportindex') }}",
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
                                `<option value="${optionValue}">${optionValue}</option>`);
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
                nonSelectedText: '-- Select User --', // Acts as a placeholder
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });

            function loaddata() {
                var edit_id = @json($edit_id);
                // show old customer support data in fields
                let customerSupportSearchUrl = "{{ route('customersupport.search', '__editId__') }}".replace(
                    '__editId__', edit_id);
                $.ajax({
                    type: 'GET',
                    url: customerSupportSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        user_id: " {{ session()->get('user_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            data = response.customersupport[0]
                            // You can update your HTML with the data here if needed
                            $('#web_url').val(data.web_url);
                            $('#first_name').val(data.first_name);
                            $('#last_name').val(data.last_name);
                            $('#email').val(data.email);
                            $('#contact_no').val(data.contact_no);
                            $('#status').val(data.status);
                            $('#last_call').val(data.last_call);
                            $('#next_call').val(data.next_call);
                            $('#number_of_call').val(data.number_of_call);
                            $('#ticket').val(data.ticket);
                            $('#notes').summernote('code', data.notes);
                            $('#created_at').val(data.created_at_formatted);
                            $('#updated_at').val(data.updated_at_formatted);
                            
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
                    name: "notes",
                    value: $('#notes').summernote('code')
                });
                $.ajax({
                    type: 'Post',
                    url: "{{ route('customersupport.update', $edit_id) }}",
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
                            window.location = "{{ route('admin.customersupport') }}";
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                            loaderhide();
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
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
                            Toast.fire({
                                icon: "error",
                                title: 'An error occurred while processing your request. Please try again later.'
                            });

                        }
                    }
                });
            })
        });
    </script>
@endpush
