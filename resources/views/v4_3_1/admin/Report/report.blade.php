@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')

@section('page_title')
    {{ config('app.name') }} - Invoice Report
@endsection
@section('title')
    Invoice Reports
@endsection
@section('style')
    <style>
        .my-custom-scrollbar {
            position: relative;
            height: 200px;
            overflow: auto;
        }

        .table-wrapper-scroll-y {
            display: block;
        }
    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Generate Zip File</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            @if (session('user_permissions.reportmodule.report.add') == '1')
                                <form id="invoicezipform" method="POST" action="{{ route('invoice.generatepdfzip') }}">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-3 mb-2">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" required />
                                                <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                    class="form-control">
                                                <label for="fromdate">From Date</label>
                                                <input type="date" name="fromdate" id="fromdate" class="form-control"
                                                    required>
                                                <span class="error-msg" id="error-fromdate" style="color: red"></span>
                                            </div>
                                            <div class="col-sm-3 mb-2">
                                                <label for="todate">To Date</label>
                                                <input type="date" name="todate" id="todate" class="form-control"
                                                    required>
                                                <span class="error-msg" id="error-todate" style="color: red"></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <br>
                                                <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Generate Zip File" class="btn btn-primary"
                                                    id="generateBtn">Generate</button>
                                                <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Reset" class="btn iq-bg-danger">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif
                            <hr>
                            @if (session('user_permissions.reportmodule.report.log') == '1')
                                <div class="table-wrapper-scroll-y my-custom-scrollbar">
                                    <table id="data"
                                        class="table table-bordered display table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>Module Name</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Created By</th>
                                                <th>Generated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabledata">

                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            $('#invoicezipform').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                loadershow(); // Show the loader

                // Serialize form data
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('invoice.generatepdfzip') }}", // Your API endpoint
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#invoicezipform')[0].reset();
                            // Redirect to the URL to start the file download
                            window.location.href = response.zipFileName;
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: "error",
                            title: 'An error occurred while generating the ZIP file.'
                        });

                    },
                    complete: function() {
                        loaderhide(); // Hide the loader
                    }
                });
            });


            @if (Session::has('success'))
                Toast.fire({
                    icon: "success",
                    title: "{{ Session::get('success') }}"
                });
            @endif
            @if (Session::has('error'))
                Toast.fire({
                    icon: "error",
                    title: "{{ Session::get('error') }}"
                });
            @endif


            loaderhide();


            // get report log
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('report.logs') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.reports != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.reports, function(key, value) {
                                $('#tabledata').append(` 
                                    <tr>
                                        <td>${id}</td>
                                        <td class='text-center'">${value.module_name}</td> 
                                        <td class='text-center'">${value.from_date_formatted}</td> 
                                        <td class='text-center'">${value.to_date_formatted}</td> 
                                        <td class='text-center'">${value.firstname} ${value.lastname}</td> 
                                        <td class='text-center'">${value.created_at_formatted}</td> 
                                        <td>
                                            @if (session('user_permissions.reportmodule.report.delete') == '1')
                                                <span>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" data-id= '${value.id}'
                                                        class="del-btn btn iq-bg-danger btn-rounded btn-sm my-0">
                                                        <i class="ri-delete-bin-fill"></i>
                                                    </button>
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                `);
                                id++;
                            });
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip({
                                boundary: 'window',
                                offset: '0, 10' // Push tooltip slightly away from the button
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#tabledata').append(`<tr><td colspan='7' >No Data Found</td></tr>`)
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            @if (session('user_permissions.reportmodule.report.log') == '1')
                //call function for loaddata if  user has permission
                loaddata();
            @endif

            // delete report log             
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let reportLogDeleteUrl = "{{ route('report.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: reportLogDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    $(row).closest("tr").fadeOut();
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
                                handleAjaxError(xhr);
                            }
                        });
                    }
                );

            });


        });
    </script>
@endpush
