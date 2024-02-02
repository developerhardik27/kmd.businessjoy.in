@extends('admin.masterlayout')

@section('page_title')
    Manage Columns
@endsection
@section('title')
    Manage Columns
@endsection


@section('form-content')
    <form id="columnform" name="columnform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-5">
                    <input type="hidden" name="token" id="token" value="{{ session('api_token') }}">
                    <input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ session('user_id') }}">
                    <input type="hidden" name="updated_by" id="updated_by">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="hidden" name="created_by" id="created_by" value="{{ $user_id }}">
                    <input type="text" class="form-control form-input" name="column_name" placeholder="Column Name"
                        id="column_name">
                    <span class="error-msg" id="error-column_name" style="color: red"></span>
                </div>
                <div class="col-sm-5 ">
                    <select name="column_type" class="form-control " id="column_type">
                        <option selected disabled>Select Datatype</option>
                        <option value="text">Text</option>
                        <option value="longtext">Long Text</option>
                        <option value="number">Number</option>
                        <option value="decimal">Decimal</option>
                        <option value="percentage">Percentage</option>
                    </select>
                    <span class="error-msg" id="error-column_type" style="color: red"></span>
                </div>
                <div class="col-sm-2 mt--2">
                    <button type="submit" class="btn btn-primary" id="submitBtn"><i class="ri-check-line"></i></button>
                    <div id="loader" class="loader"></div>
                    <button id="resetbtn" type="reset" class="btn iq-bg-danger"><i class="ri-refresh-line"></i></button>
                </div>
            </div>
        </div>

    </form>
    <hr>
    <table id="data" class="table  table-bordered display table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Column Name</th>
                <th>Column Type</th>
                <th>Order</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">
        </tbody>
        <tr>
            <td colspan="5">
                <button class="btn btn-sm btn-primary savecolumnorder float-right" title="Save column order"><i
                        class="ri-check-line"></i></button>
            </td>
        </tr>
    </table>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $('.error-msg').text('');
                $.ajax({
                    type: 'GET',
                    url: '{{ route('invoicecolumn.index') }}',
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.invoicecolumn != '') {
                            loaderhide();
                            global_response = response;
                            var id = 1;
                            $.each(response.invoicecolumn, function(key, value) {
                                $('#tabledata').append(` <tr>
                                                        <td>${id}</td>
                                                        <td>${value.column_name}</td>
                                                        <td>${value.column_type}</td>
                                                        <td><input type='text' placeholder='Set Coumn Order' data-id='${value.id}' value=${value.column_order} class='columnorder'></td>
                                                        <td>
                                                            <span>
                                                                <button type="button" value=${(value.is_hide == 0 )? 1 : 0} data-id='${value.id}'
                                                                     class="btn hide-btn btn-outline-${(value.is_hide == 0 )? "info" : "danger"} btn-rounded btn-sm my-0">
                                                                    ${(value.is_hide == 0 )? "Show" : "Hide"}
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-id='${value.id}'
                                                                     class="btn edit-btn iq-bg-success btn-rounded btn-sm my-0">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-id= '${value.id}'
                                                                    class=" del-btn btn iq-bg-danger btn-rounded btn-sm my-0">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        </td>
                                                    </tr>`)
                                id++;
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#tabledata').append(`<tr><td colspan='5' >No Data Found</td></tr>`)
                        }
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


            $(document).on("click", '.hide-btn', function() {
                hidevalue = $(this).val();
                var columnid = $(this).data('id');
                if (confirm('Are you really want to Update this Column ?')) {
                    var row = this;
                    loadershow();
                    $.ajax({
                        type: 'put',
                        url: '/api/invoicecolumn/hide/' + columnid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: {{ session()->get('company_id') }},
                            hidevalue
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                loaderhide();
                                loaddata();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                toastr.error(response.message);
                                loaderhide();
                            }
                        },
                        error: function(error) {
                            loaderhide();
                            toastr.error('Something Went Wrong !');
                        }
                    });
                }
            });

            $(document).on("click", ".edit-btn", function() {
                if (confirm("You want edit this Column ?")) {
                    loadershow();
                    var editid = $(this).data('id');
                    $.ajax({
                        type: 'get',
                        url: '/api/invoicecolumn/edit/' + editid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200 && response.invoicecolumn != '') {
                                var invoicecolumndata = response.invoicecolumn;
                                loaderhide();
                                $('#updated_by').val("{{ session()->get('user_id') }}");
                                $('#edit_id').val(editid);
                                $('#column_name').val(invoicecolumndata.column_name);
                                $('#column_type').val(invoicecolumndata.column_type);
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(error) {
                            loaderhide();
                            console.error('Error:', error);
                        }
                    });
                }
            });

            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this Column ?')) {
                    var deleteid = $(this).data('id');
                    var row = this;
                    loadershow();
                    $.ajax({
                        type: 'put',
                        url: '/api/invoicecolumn/delete/' + deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: {{ session()->get('company_id') }}
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                loaderhide();
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                toastr.error(response.message);
                                loaderhide();
                            }
                        },
                        error: function(error) {
                            loaderhide();
                            toastr.error('Something Went Wrong !');
                        }
                    });
                }
            });


            $('.savecolumnorder').on('click', function() {
                var columnorders = [];
                $('input.columnorder').each(function() {
                    columnid = $(this).data('id');
                    columnorder = $(this).val();
                    if (columnid != null && columnorder != null) {
                        columnorders[columnid] = columnorder;
                    }
                });
                $.ajax({
                    type: 'Post',
                    url: '{{ route('invoicecolumn.columnorder') }}',
                    data: {
                        columnorders,
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            toastr.success(response.message);
                            loaderhide();
                            loaddata();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            toastr.error(response.message);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        toastr.error('Something Went Wrong !');
                    }
                });
            });


            $('#columnform').submit(function(e) {
                e.preventDefault();
                loadershow();
                var editid = $('#edit_id').val()
                if (editid != '') {
                    var columndata = $(this).serialize();
                    $.ajax({
                        type: "post",
                        url: "/api/invoicecolumn/update/" + editid,
                        data: columndata,
                        success: function(response) {
                            if (response.status == 200) {
                                $('#edit_id').val('');
                                loaderhide();
                                // You can perform additional actions, such as showing a success message or redirecting the user
                                toastr.success(response.message);
                                $('#columnform')[0].reset();

                                loaddata();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else if (response.status == 422) {
                                loaderhide();
                                $('.error-msg').text('');
                                $.each(response.errors, function(key, value) {
                                    $('#error-' + key).text(value[0]);
                                });
                            } else {
                                loaderhide();

                                toastr.error(response.message);
                            }

                        },
                        error: function(xhr, status, error) {
                            loaderhide();
                            // Handle error response and display validation errors
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#error-' + key).text(value[0]);
                                });
                            } else {
                                loaderhide();
                                toastr.error(
                                    'An error occurred while processing your request. Please try again later.'
                                );
                            }
                        }

                    });
                } else {
                    var columndata = $(this).serialize();
                    $.ajax({
                        type: "post",
                        url: "{{ route('invoicecolumn.store') }}",
                        data: columndata,
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                // You can perform additional actions, such as showing a success message or redirecting the user
                                toastr.success(response.message);
                                $('#columnform')[0].reset();
                                loaddata();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else if (response.status == 422) {
                                loaderhide();
                                $('.error-msg').text('');
                                $.each(response.errors, function(key, value) {
                                    $('#error-' + key).text(value[0]);
                                });
                            } else {
                                loaderhide();

                                toastr.error(response.message);
                            }

                        },
                        error: function(xhr, status, error) {
                            loaderhide();
                            // Handle error response and display validation errors
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#error-' + key).text(value[0]);
                                });
                            } else {
                                loaderhide();
                                toastr.error(
                                    'An error occurred while processing your request. Please try again later.'
                                );
                            }
                        }

                    });
                }

            });
        });
    </script>
@endpush
