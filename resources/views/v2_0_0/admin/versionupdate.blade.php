@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterpage')
@section('page_title')
    {{ config('app.name') }} - Version Control
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endsection

@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title"> Version Control</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form id="versioncontrolform" name="versioncontrolform">
                                @csrf
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-4">
                                            <input type="hidden" name="company_id" class="form-control"
                                                value="{{ session('company_id') }}" placeholder="company id" required />
                                            <input type="hidden" name="user_id" class="form-control"
                                                value="{{ session('user_id') }}" placeholder="user id" required />
                                            <input type="hidden" name="token" class="form-control"
                                                value="{{ session('api_token') }}" placeholder="token" required />
                                            <label for="company">Company</label><span style="color:red;">*</span>
                                            <select class="form-control select2" id="company" name="company" required>
                                                <option selected disabled> Select Company</option>
                                            </select>
                                            <span class="error-msg" id="error-company" style="color: red"></span>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="version">Version</label><span style="color:red;">*</span>
                                            <select class="form-control" id="version" name="version" required>
                                                <option selected disabled> Select Version</option>
                                                <option value="v1_1_0">V1.1.0</option>
                                                <option value="v1_1_1">V1.1.1</option>
                                                <option value="v1_2_1">V1.2.1</option>
                                                <option value="v2_0_0">V2.0.0</option>
                                                <option value="v3_0_0">V3.0.0</option>
                                            </select>
                                            <span class="error-msg" id="error-version" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset" class="btn iq-bg-danger float-right mr-2"
                                                id="resetBtn">Reset</button>
                                            <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Save"
                                                class="btn btn-primary float-right my-0">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Version Features</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div class="col md-4">
                                <a href="{{ asset('versionchange/v1_1_1/v1_1_1.rtf') }}">V1.1.1</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script src="{{ asset('admin/js/select2.min.js') }}"></script>
    <script>
        $('document').ready(function() {

            var global_response = ''; // declare global variable for using company detail globally
            // function for get company data and load into table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('company.companylist') }}",
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // You can update your HTML with the data here if needed
                        if (response.status == 200 && response.company != '') {
                            global_response = response;
                            $('#company').html(
                                `<option selected=""> Select Company</option>`);
                            $.each(response.company, function(key, company) {
                                var companydetails = '';
                                if (company.name != null) {
                                    companydetails += company.name;
                                }
                                if (company.contact_no != null) {
                                    if (companydetails.length > 0) {
                                        companydetails += ' - '; // 
                                    }
                                    companydetails += company.contact_no;
                                }
                                if (company.email != null) {
                                    if (companydetails.length > 0) {
                                        companydetails += ' - '; // 
                                    }
                                    companydetails += company.email;
                                }
                                if (company.app_version != null) {
                                    if (companydetails.length > 0) {
                                        companydetails += ' - '; // 
                                    }
                                    companydetails += company.app_version.replace(/_/g, '.');
                                }
                                $('#company').append(`
                                     <option value="${company.id}">${companydetails}</option>
                                `);
                            });
                            $('.select2').select2();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#company').html(`<option>No Data Found<option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
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
            //call loaddata function for make ajax call
            loaddata(); // this function is for get company details


            $('#resetBtn').on('click', function(e) {
                e.preventDefault();
                $('#versioncontrolform')[0].reset();
                $('.select2').select2();
            })

            $('#versioncontrolform').submit(function(event) {
                event.preventDefault();
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change this company version?', // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $('.error-msg').text('');
                        const formdata = $(this).serialize();
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('company.versionupdate') }}",
                            data: formdata,
                            success: function(response) {
                                // Handle the response from the server
                                if (response.status == 200) {
                                    // You can perform additional actions, such as showing a success message or redirecting the user
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    $('#versioncontrolform')[0].reset();
                                    loaddata();
                                } else if (response.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: "something went wrong!"
                                    });
                                }
                                loaderhide();
                            },
                            error: function(xhr, status,
                            error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                    ); // Log the full error response for debugging
                                if (xhr.status === 422) {
                                    var errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, value) {
                                        $('#error-' + key).text(value[0]);
                                    });
                                } else {
                                    var errorMessage = "";
                                    try {
                                        var responseJSON = JSON.parse(xhr.responseText);
                                        errorMessage = responseJSON.message ||
                                            "An error occurred";
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
                    }
                );
            });

        });
    </script>
@endpush
