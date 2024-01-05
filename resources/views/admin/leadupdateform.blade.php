@extends('admin.masterlayout')
@section('page_title')
    Update Lead
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
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <label class="form-label" for="name">Name:</label>
                    <input type="text"  class="form-control" name="name" id="name" placeholder="Name"
                        required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="email">Email:</label>
                    <input type="email"  class="form-control" name="email" id="email"
                        placeholder="Professional Email"  />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="contact_no">Mobile Number:</label>
                    <input type="text"  class="form-control" name="contact_no" id="contact_no"
                        placeholder="Whatsapp Mobile Number" maxlength="13" required />
                    <span class="error-msg" id="error-contact_no" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="title">Job Title:</label>
                    <select name="title" class="form-control" id="modal_title" required>
                        <option disabled selected>Select Title</option>
                        <option value="Student">Student</option>
                        <option value="Employee">Employee</option>
                        <option value="Manager">Manager</option>
                        <option value="Business Owner">Business Owner</option>
                        <option value="Self Employeed">Self Employeed</option>
                        <option value=" Other"> Other</option>
                    </select>
                    <span class="error-title" id="error-price_per_unit" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="budget">Budget:</label>
                    <select name="budget" class="form-control" id="budget" required>
                        <option value="" disabled selected>Select Your Budget</option>
                        <option value="10000-20000">10000 To 20000</option>
                        <option value="20000-30000">20000 To 30000</option>
                        <option value="greater than 30000">Greater Than 30000</option>
                    </select>
                    <span class="error-budget" id="error-description" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="status">Status:</label>
                    <select name="status" class="form-control" id="status" >
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
                        <option value='Rejected'>Rejected</option>
                        <option value='Sale'>Sale</option>
                        <option value='Busy'>Busy</option>
                        <option value='Call Back'>Call Back</option>
                    </select>
                    <span class="error-status" id="error-description" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="audience_type">Audience Type:</label>
                    <input type="text"  class="form-control" name="audience_type" id="audience_type"
                        placeholder="Audience Type" />
                    <span class="error-msg" id="error-audience_type" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="customer_type">Customer Type:</label>
                    <input type="text"  class="form-control" name="customer_type" id="customer_type"
                        placeholder="Customer Type" />
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
                    <input type="number" class="form-control" value="0" name="number_of_follow_up" min="0" max="10" id="no_of_follow_up">
                    <span class="error-number_of_follow_up" id="error-description" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="source">Source:</label>
                    <input type="text" class="form-control" name="source" id="source" placeholder="source"
                         />
                    <span class="error-source" id="error-description" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="created_at">Created At:</label>
                    <input type="datetime" readonly class="form-control" name="created_at" id="created_at"
                        placeholder="Created at"  />
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
                <div class="col-sm-6 mt-4">
                    <label class="form-label" for="is_active">Qualified:</label>
                    <input type="checkbox" value="1" class="form-control-checkbox" name="is_active" id="is_active" />
                    <span class="error-msg" id="error-is_active" style="color: red"></span>
                </div>
                
                <div class="col-sm-6">
                    <label class="form-label" for="ip">Ip:</label>
                    <input type="text" readonly class="form-control" name="ip" id="ip"
                        placeholder="Ip " />
                    <span class="error-msg" id="error-ip" style="color: red"></span>
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
            <button id="resetbtn" type="reset" class="btn iq-bg-danger">Reset</button>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            var edit_id = @json($edit_id);
            // show old data in fields
            $.ajax({
                type: 'GET',
                url: '/api/lead/search/' + edit_id,
                data: {
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                     data = response.lead[0]
                    if (response.status == 200) {
                        // You can update your HTML with the data here if needed
                        $('#name').val(data.name);
                        $('#email').val(data.email);
                        $('#contact_no').val(data.contact_no);
                        $('#modal_title').val(data.title);
                        $('#budget').val(data.budget);
                        $('#audience_type').val(data.audience_type);
                        $('#customer_type').val(data.customer_type)
                        $('#status').val(data.status);
                        $('#last_follow_up').val(data.last_follow_up);
                        $('#next_follow_up').val(data.next_follow_up);
                        $('#no_of_follow_up').val(data.number_of_follow_up);
                        $('#notes').val(data.notes);
                        $('#created_at').val(data.created_at_formatted);
                        $('#updated_at').val(data.updated_at_formatted);
                        $('#source').val(data.source);
                        $('#ip').val(data.ip);
                        (data.is_active == 1) ?  $('#is_active').attr('checked',true) : $('#is_active').attr('checked',false) ;
                    }

                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
            //submit form
            $('#leadupdateform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                $('#submitBtn').hide();
                $('#resetbtn').hide();
                // Show the loader
                $("#loader").show();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'Post',
                    url: "{{ route('lead.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.lead') }}";

                        } else if (response.status == 422) {
                            toastr.error(response.errors);
                            $('#submitBtn').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
                        } else {
                            toastr.error(response.message);
                            $('#submitBtn').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            $('#submitBtn').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
                        } else {
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
