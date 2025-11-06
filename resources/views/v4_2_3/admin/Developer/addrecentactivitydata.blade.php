@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add Activity/Recent Data
@endsection
@section('title')
    Activity/Recent Data
@endsection


@section('form-content')
    <form id="recentactivityform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">
                    <label for="module">Module</label>
                    <span style="color:red;">*</span>
                    <input type="text" id="module" class="form-control" name='module'
                        placeholder="Module Name" required>
                    <span class="error-msg" id="error-module" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="page">Page</label>
                    <span style="color:red;">*</span>
                    <input type="text" id="page" class="form-control" name='page'
                        placeholder="Page Name" required>
                    <span class="error-msg" id="error-page" style="color: red"></span>
                </div> 
                <div class="col-sm-6 mb-2">
                    <label for="limit">Limit</label>
                    <span style="color:red;">*</span>
                    <input type="text" id="limit" class="form-control" name='limit' placeholder="Limit" required>
                    <span class="error-msg" id="error-limit" style="color: red"></span>
                </div> 
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset Customer Details"
                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save Customer Details" class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            loaderhide();
        
            // redirect on activity/recent list page on click cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.recentactivitydata') }}";
            });

            // submit activity/recent form 
            $('#recentactivityform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let formdata = $(this).serialize();
                
                $.ajax({
                    type: 'POST',
                    url: "{{ route('recentactivitydata.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location.href =
                                "{{ route('admin.recentactivitydata') }}"; // redirect on activity/recent list page

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
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
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var firstErrorElement = null;

                            $.each(errors, function (key, value) {
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
                                    scrollTop: firstErrorElement.offset().top - 100 // Offset for better visibility
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
            });
        });
    </script>
@endpush
