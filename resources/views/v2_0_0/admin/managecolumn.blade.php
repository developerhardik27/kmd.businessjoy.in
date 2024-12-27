@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Manage Columns
@endsection
@section('title')
    Manage Columns
@endsection

@section('form-content')
    <form id="columnform" name="columnform">
        @csrf
        <div class="form-group">
            <div id="newColForm" class="form-row d-none">
                <div class="col-sm-4 mb-2">
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
                <div class="col-sm-4 mb-2">
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
                <div class="col-sm-2 mb-2">
                    <input type="number" class="form-control" name="column_width" id="column_width" placeholder="Column Width(%)"
                        required />
                    <span class="error-msg" id="error-column_width" style="color: red"></span>
                </div>
                <div class="col-sm-2 mt--2">
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Submit Column Details" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn iq-bg-danger mr-2" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset Column Details">Reset</button>
                    <button type="button" class="btn btn-secondary" id="cancelbtn" data-toggle="tooltip"
                        data-placement="bottom" data-original-title="Cancel">Cancel</button>
                </div>
            </div>
            <div id="newColBtnDiv" class="form-row ">
                <div class="col-sm-12">
                    <button type="btn" id="newColBtn" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Add New Column" class="btn btn-primary float-right">+ Add New Column</button>
                </div>
            </div>
        </div>
    </form>
    <hr>
    <table id="data"
        class="table  table-bordered display table-responsive-lg table-striped text-center">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Column Name</th>
                <th>Column Type</th>
                <th>Column Width(%)</th>
                <th>Sequence</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">
        </tbody>
        <tr>
            <td colspan="4" style="border:none;">
            </td>
            <td class="text-center" style="border-left: none">
                <button data-toggle="tooltip" data-placement="bottom" data-original-title="Save Columns Sequence"
                    class="btn btn-sm btn-primary savecolumnorder">
                    <i class="ri-check-line"></i>
                </button>
            </td>
        </tr>
    </table>
@endsection


@push('ajax')
    @isset($message)
        <script>
            $('document').ready(function() {
                alert('Required minimum one column to create invoice. Kindly, Create your columns');
            });
        </script>
    @endisset
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            // show add new column form on click add new column (initally its hide)
            $('#newColBtn').on('click', function(e) {
                e.preventDefault();
                $('#newColForm').removeClass('d-none');
                $('#newColBtnDiv').addClass('d-none');
            })

            // validation for column name
            $('#column_name').on('input', function() {
                var name = $(this).val();
                var filteredName = name.replace(/[^A-Za-z_ ]/g,
                    ''); // Remove any characters not in the allowed set
                $(this).val(filteredName); // Update the input value with the filtered name
            });


            // fetch column name and append into column list table
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $('.error-msg').text('');
                $.ajax({
                    type: 'GET',
                    url: "{{ route('invoicecolumn.index') }}",
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.invoicecolumn != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.invoicecolumn, function(key, value) {
                                $('#tabledata').append(` <tr>
                                                        <td>${id}</td>
                                                        <td>${value.column_name}</td>
                                                        <td>${value.column_type}</td>
                                                        <td>${value.column_width}</td>
                                                        <td><input type='number' min=1 oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder='Set Coumn Sequence' data-id='${value.id}' value="${value.column_order}" class='columnorder'></td>
                                                        <td>
                                                            <span>
                                                                <button type="button"data-toggle="tooltip" data-placement="bottom" data-original-title="${(value.is_hide == 0 )? 'Hide Column' : 'Show Column' }" value=${(value.is_hide == 0 )? 1 : 0} data-id='${value.id}'
                                                                     class="btn hide-btn btn-outline-${(value.is_hide == 0 )? "info" : "danger"} btn-rounded btn-sm my-1">
                                                                    ${(value.is_hide == 0 )? "Show" : "Hide"}
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Column" data-id='${value.id}'
                                                                     class="btn edit-btn iq-bg-success btn-rounded btn-sm my-1">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Column" data-id= '${value.id}'
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
                            toastr.error(response.message);
                        } else {
                            $('#tabledata').append(`<tr><td colspan='5' >No Data Found</td></tr>`)
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

            // hide column will be not show into invoice its use for only calculation
            $(document).on("click", '.hide-btn', function() {
                hidevalue = $(this).val();
                var columnid = $(this).data('id');
                if (confirm(
                        'Old invoice will not edit after apply this changes. Are you sure still you want to Update this Column?'
                    )) {
                    var row = this;
                    let invoiceColumnHideUrl = "{{ route('invoicecolumn.hide','__columnId__') }}".replace('__columnId__',columnid);
                    loadershow();
                    $.ajax({
                        type: 'PUT',
                        url: invoiceColumnHideUrl,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: {{ session()->get('company_id') }},
                            user_id: {{ session()->get('user_id') }},
                            hidevalue
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                loaddata();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                            loaderhide();
                        },
                        error: function(error) {
                            loaderhide();
                            toastr.error('Something Went Wrong !');
                        }
                    });
                }
            });


            // edit column 
            $(document).on("click", ".edit-btn", function() {
                if (confirm("You want edit this Column ?")) {
                    loadershow();
                    $('#column_type').prop('disabled', true);
                    var editid = $(this).data('id');
                    $('#newColForm').removeClass('d-none');
                    $('#newColBtnDiv').addClass('d-none');
                    let invoiceColumnEditUrl = "{{ route('invoicecolumn.edit','__editId__') }}".replace('__editId__',editid);
                    $.ajax({
                        type: 'GET',
                        url: invoiceColumnEditUrl,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200 && response.invoicecolumn != '') {
                                var invoicecolumndata = response.invoicecolumn;
                                $('#updated_by').val("{{ session()->get('user_id') }}");
                                $('#edit_id').val(editid);
                                $('#column_name').val(invoicecolumndata.column_name);
                                $('#column_type').val(invoicecolumndata.column_type);
                                $('#column_width').val(invoicecolumndata.column_width);
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                            loaderhide();
                        },
                        error: function(error) {
                            $('#column_type').prop('disabled', false);
                            loaderhide();
                            console.error('Error:', error);
                        }
                    });
                }
            });


            // delete column 
            $(document).on("click", ".del-btn", function() {
                if (confirm(
                        'This column will remove from old invoices and receipts. Are you sure you want to remove this column?'
                    )) {
                    var deleteid = $(this).data('id');
                    var row = this;
                    loadershow();
                    let invoiceColumnDeleteUrl = "{{ route('invoicecolumn.delete','__deleteId__') }}".replace('__deleteId__',deleteid);
                    $.ajax({
                        type: 'PUT',
                        url: invoiceColumnDeleteUrl,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: {{ session()->get('company_id') }},
                            user_id: {{ session()->get('user_id') }},
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                            loaderhide();
                        },
                        error: function(error) {
                            loaderhide();
                            toastr.error('Something Went Wrong !');
                        }
                    });
                }
            });

            // manage column order  (it will show order by in invoice)
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
                    type: 'POST',
                    url: "{{ route('invoicecolumn.columnorder') }}",
                    data: {
                        columnorders,
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}",
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            toastr.success(response.message);
                            loaddata();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        loaderhide();
                    },
                    error: function(error) {
                        loaderhide();
                        toastr.error('Something Went Wrong !');
                    }
                });
            });

            // hide and reset add new column form on click cancel btn
            $('#cancelbtn').on('click', function() {
                $('#newColForm').addClass('d-none');
                $('#newColBtnDiv').removeClass('d-none');
                $('#columnform')[0].reset();
            });

            // add or edit column form submit
            $('#columnform').submit(function(e) {
                e.preventDefault();
                var editid = $('#edit_id').val()
                if (editid != '') {
                    if (confirm(
                            "Edited column name will reflect in relevant invoices and receipts. Are you sure still you want to apply this?"
                        )) {
                        loadershow();
                        $('#column_type').prop('disabled', false);
                        var columndata = $(this).serialize();
                        $('#column_type').prop('disabled', true);
                        let invoiceColumnUpdateUrl = "{{ route('invoicecolumn.update','__editId__') }}".replace('__editId__',editid);
                        $.ajax({
                            type: "POST",
                            url: invoiceColumnUpdateUrl,
                            data: columndata,
                            success: function(response) {
                                $('#column_type').prop('disabled', false);
                                if (response.status == 200) {
                                    $('#edit_id').val('');
                                    $('#newColForm').addClass('d-none');
                                    $('#newColBtnDiv').removeClass('d-none');
                                    // You can perform additional actions, such as showing a success message or redirecting the user
                                    toastr.success(response.message);
                                    $('#columnform')[0].reset();
                                    loaddata();
                                } else if (response.status == 500) {
                                    toastr.error(response.message);
                                } else {
                                    toastr.error(response.message);
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
                                        errorMessage = responseJSON.message ||
                                            "An error occurred";
                                    } catch (e) {
                                        errorMessage = "An error occurred";
                                    }
                                    toastr.error(errorMessage);
                                }
                            }
                        });
                    }
                } else {
                    if (confirm(
                            "Old invoice will not edit after apply this. Are you sure still you want to apply this?"
                        )) {
                        loadershow();
                        var columndata = $(this).serialize();
                        $.ajax({
                            type: "POST",
                            url: "{{ route('invoicecolumn.store') }}",
                            data: columndata,
                            success: function(response) {
                                if (response.status == 200) {
                                    $('#newColForm').addClass('d-none');
                                    $('#newColBtnDiv').removeClass('d-none');
                                    // You can perform additional actions, such as showing a success message or redirecting the user
                                    toastr.success(response.message);
                                    $('#columnform')[0].reset();
                                    loaddata();
                                } else if (response.status == 500) {
                                    toastr.error(response.message);
                                } else if (response.status == 422) {
                                    $('.error-msg').text('');
                                    $.each(response.errors, function(key, value) {
                                        $('#error-' + key).text(value[0]);
                                    });
                                } else {
                                    toastr.error(response.message);
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
                                        errorMessage = responseJSON.message ||
                                            "An error occurred";
                                    } catch (e) {
                                        errorMessage = "An error occurred";
                                    }
                                    toastr.error(errorMessage);
                                }
                            }

                        });
                    }

                }
            });
        });
    </script>
@endpush
