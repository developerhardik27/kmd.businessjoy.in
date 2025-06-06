@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Api Authorization
@endsection
@section('title')
    Api Authorization
@endsection

@section('form-content')
    <form id="apiauthform" name="apiauthform">
        @csrf
        <div class="form-group">
            <div class="form-row d-none newapiauthform">
                <div class="col-sm-6">
                    <input type="hidden" name="token" id="token" value="{{ session('api_token') }}">
                    <input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <label for="company">Select Company</label><span style="color:red;">*</span>
                    <select id="company" class="form-control" name='company' required>
                        <option selected="" disabled="">Select Company</option>
                    </select>
                    <span class="error-msg" id="error-company" style="color: red"></span>
                </div>
                <div class="col-sm-5">
                    <label for="domain_name">Domain Names</label><span style="color:red;">*</span>
                    <input type="text" name='domain_name' class="form-control" id="domain_name" value=""
                        placeholder="eg. abc.com , xyz.com ... " required />
                    <span class="error-msg" id="error-domain_name" style="color: red"></span>
                </div>
            </div>
            <div class="form-row d-none newapiauthform mt-2">
                <div class="col-sm-12">
                    <button type="reset" class="btn iq-bg-danger float-right" data-toggle="tooltip"
                        data-placement="bottom" data-original-title="Reset"><i class="ri-refresh-line"></i></button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Submit"
                        class="btn btn-primary my-0 float-right"><i class="ri-check-line"></i></button>
                </div>
            </div>
            <div id="newapiauthBtnDiv" class="form-row">
                <div class="col-sm-12">
                    <button type="btn" id="newapiauthBtn" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Add New Company Authorization" class="btn btn-primary float-right">+ Add New
                    </button>
                </div>
            </div>
        </div>
    </form>
    <hr>
    <table id="data"
        class="table  table-bordered display table-responsive-sm table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Company Name</th>
                <th>Domain Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">
        </tbody>
    </table>


    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle"><b>Details</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="details" width='100%' class="table table-bordered table-responsive-md table-striped">
                    </table>
                </div>
                <div class="modal-footer">
                    <span id="addfooterbutton"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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


            $('#newapiauthBtn').on('click', function(e) {
                e.preventDefault();
                $('.newapiauthform').removeClass('d-none');
                $('#newapiauthBtnDiv').addClass('d-none');
            })



            function companydata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('company.index') }}",
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // You can update your HTML with the data here if needed
                        if (response.status == 200 && response.company != '') {
                            var id = 1;
                            $.each(response.company, function(key, value) {
                                $('#company').append(
                                    `<option value=${value.id}>${value.name != null ? value.name : '-'}</option>`
                                );
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });

                        } else {
                            $('#company').append(`<option>No Data Found<option>`);
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
            } // companylist fetch when click new authorization button

            companydata();

            // fetch column name and append into column list table
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $('.error-msg').text('');
                $.ajax({
                    type: 'GET',
                    url: "{{ route('apiauthorization.index') }}",
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.apiauth != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.apiauth, function(key, value) {
                                $('#tabledata').append(` <tr>
                                                        <td>${id}</td>
                                                        <td>${value.name}</td>
                                                        <td>${value.domain_name}</td>
                                                        <td>  
                                                             <span class="">
                                                                <button type="button"  data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-1">
                                                                   <i class="ri-indent-decrease"></i>
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Authorization" data-id='${value.id}'
                                                                     class="btn edit-btn iq-bg-success btn-rounded btn-sm my-1">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Authorization" data-id= '${value.id}'
                                                                    class=" del-btn btn iq-bg-danger btn-rounded btn-sm my-1">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        </td>
                                                    </tr>`)
                                id++;
                            });
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });

                        } else {
                            $('#tabledata').append(`<tr><td colspan='4' >No Data Found</td></tr>`)
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }

            //call function for loaddata
            loaddata();


            // edit column if it is not used for any invoice
            $(document).on("click", ".edit-btn", function() {
                var editid = $(this).data('id');
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'edit this record?', // Text
                    'Yes, edit', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $('.newapiauthform').removeClass('d-none');
                        $('#newapiauthBtnDiv').addClass('d-none');
                        apiAuthorizationEditUrl = "{{ route('apiauthorization.edit', '__editId__') }}"
                            .replace(
                                '__editId__', editid);
                        $.ajax({
                            type: 'GET',
                            url: apiAuthorizationEditUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(response) {
                                if (response.status == 200 && response.apiauth != '') {
                                    var apiauthdata = response.apiauth;
                                    $('#edit_id').val(editid);
                                    $('#domain_name').val(apiauthdata.domain_name);
                                    $('#company').val(apiauthdata.company_id);
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
                            error: function(error) {
                                loaderhide();
                                console.error('Error:', error);
                            }
                        });
                    }
                );
            });


            // view bank data in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.apiauth, function(key, apiauth) {
                    if (apiauth.id == data) {
                        $('#details').append(`
                                <tr>
                                    <th>Company Name</th>
                                    <td>${(apiauth.name != null)? apiauth.name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Domain Number</th>
                                    <td>${(apiauth.domain_name != null)? apiauth.domain_name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Site Key</th>
                                    <td>${(apiauth.site_key != null)? apiauth.site_key : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Server Key</th>
                                    <td>${(apiauth.server_key != null)? apiauth.server_key : '-'}</td>
                                </tr> 
                        `);
                    }
                });
            });


            // delete column if it is not has data of any invoice
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'delete this record?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        apiAuthorizationDeleteUrl =
                            "{{ route('apiauthorization.delete', '__deleteId__') }}"
                            .replace('__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: apiAuthorizationDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: {{ session()->get('company_id') }},
                                user_id: {{ session()->get('user_id') }},
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
                                        title: response.message
                                    });

                                }
                                loaderhide();
                            },
                            error: function(error) {
                                loaderhide();
                                Toast.fire({
                                    icon: "error",
                                    title: "something went wrong!"
                                });
                            }
                        });
                    }
                );

            });


            // add or edit column form submit
            $('#apiauthform').submit(function(e) {
                e.preventDefault();
                loadershow();
                var editid = $('#edit_id').val()
                var columndata = $(this).serialize();
                if (editid != '') {
                    url = "{{ route('apiauthorization.update', '__editId__') }}".replace('__editId__',
                        editid);
                } else {
                    url = "{{ route('apiauthorization.store') }}"
                }
                $.ajax({
                    type: "POST",
                    url: url,
                    data: columndata,
                    success: function(response) {
                        if (response.status == 200) {
                            $('#edit_id').val('');
                            $('.newapiauthform').addClass('d-none');
                            $('#newapiauthBtnDiv').removeClass('d-none');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

                            $('#domain_name').val('');
                            $('#company').prop('selectedIndex', 0);
                            loaddata();
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
            });
        });
    </script>
@endpush
