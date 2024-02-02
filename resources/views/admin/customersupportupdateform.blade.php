@extends('admin.masterlayout')
@section('page_title')
    Update Lead
@endsection
@section('title')
    Update Lead
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
                    <label class="form-label" for="name">Name:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" required />
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
                    <label class="form-label" for="contact_no">Mobile Number:</label>
                    <input type="text" class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" maxlength="13" required />
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
                    <label class="form-label" for="assignedto">Assigned To:</label><br />
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
                    <label class="form-label" for="last_call">Last Call:</label>
                    <input type="date" class="form-control" name="last_call" id="last_call" placeholder="last_call" />
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

            $('#resetbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.customersupport') }}";
            })

            $.ajax({
                type: 'GET',
                url: '{{ route('user.index') }}',
                data: {
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
                url: '/api/customersupport/search/' + edit_id,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: " {{ session()->get('company_id') }} "
                },
                success: function(response) {
                    data = response.customersupport[0]
                    if (response.status == 200) {
                        // You can update your HTML with the data here if needed
                        $('#name').val(data.name);
                        $('#email').val(data.email);
                        $('#contact_no').val(data.contact_no);
                        $('#status').val(data.status);
                        $('#last_call').val(data.last_call);
                        $('#next_call').val(data.next_call);
                        $('#number_of_call').val(data.number_of_call);
                        $('#ticket').val(data.ticket);
                        $('#notes').val(data.notes);
                        $('#created_at').val(data.created_at_formatted);
                        $('#updated_at').val(data.updated_at_formatted);
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
            $('#ticketupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'Post',
                    url: "{{ route('customersupport.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.customersupport') }}";

                        } else if (response.status == 422) {
                            toastr.error(response.errors);
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
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
