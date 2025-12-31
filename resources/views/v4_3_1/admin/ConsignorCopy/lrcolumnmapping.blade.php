@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')
@section('page_title')
    {{ config('app.name') }} - Lr Column Mapping
@endsection

@section('page-content')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title float-left col-auto">
                                <h4 class="card-title">Column Mapping</h4>
                            </div>
                            <div class="float-right row">
                                <div class="float-right">
                                    <button type="btn" id="newColBtn" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Add New Link" class="btn btn-primary float-right">
                                        + Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form id="columnform" name="columnform">
                                @csrf
                                <div class="form-group">
                                    <div id="newColForm" class="form-row d-none">
                                        <div class="col-md-6 mb-2">
                                            <input type="hidden" name="token" id="token"
                                                value="{{ session('api_token') }}">
                                            <input type="hidden" name="company_id" id="company_id"
                                                value="{{ session('company_id') }}">
                                            <input type="hidden" name="user_id" id="user_id"
                                                value="{{ session('user_id') }}">
                                            <input type="hidden" name="edit_id" id="edit_id" value="">    
                                            <select name="lr_column" class="form-control " id="lr_column">
                                                <option selected value="">Select Lr  Column</option>
                                                <option value="loading_date">Loading Date</option> 
                                                <option value="stuffing_date">Stuffing Date</option> 
                                                <option value="truck_number">Truck Number</option>
                                                <option value="driver_name">Driver Name</option>
                                                <option value="weight_type">Weight</option>
                                                <option value="actual">Actual</option>
                                                <option value="charged">Charged</option>
                                                <option value="value">Value</option>
                                                <option value="paid">Paid</option>
                                                <option value="to_pay">To Pay</option>
                                                <option value="container_no">Container No</option>
                                                <option value="seal_no">Seal No</option>
                                            </select>
                                            <span class="error-msg" id="error-lr_column" style="color: red"></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <select name="invoice_column" class="form-control " id="invoice_column">
                                                <option selected value="">Select Invoice Column</option>
                                            </select>
                                            <span class="error-msg" id="error-invoice_column" style="color: red"></span>
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Cancel" id="cancelbtn"
                                                class="btn btn-secondary float-right">Cancel</button>
                                            <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Reset Details"
                                                class="btn iq-bg-danger float-right mr-2">Reset</button>
                                            <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="Save Details"
                                                class="btn btn-primary float-right my-0">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <table id="data"
                                class="table  table-bordered display table-responsive-sm table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th>lr Column</th>
                                        <th>Invoice Column</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tabledata">
                                </tbody>
                            </table>
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
            loaderhide();
            // show add new column form on click add new column (initally its hide)
            $('#newColBtn').on('click', function(e) {
                e.preventDefault();
                $('#newColForm').removeClass('d-none');
                $(this).addClass('d-none');
            })

            // hide and reset add new column form on click cancel btn
            $('#cancelbtn').on('click', function() {
                $('#newColForm').addClass('d-none');
                $('#newColBtn').removeClass('d-none');
                $('#columnform')[0].reset();
                $('#edit_id').val('');
            });

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
                        $.each(response.invoicecolumn, function(key, value) {
                            $('#invoice_column').append(`
                                <option value="${value.column_name}">${value.column_name}</option>
                            `);
                        });

                    } else if (response.status == 500) {
                        $('#invoice_column').append(`
                            <option>No Data Found</option>
                        `);
                    } else {
                        $('#invoice_column').append(`
                           <option>No Data Found</option>
                        `);
                    }
                    loaderhide();
                    // You can update your HTML with the data here if needed
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });

            // fetch column name and append into column list table
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $('.error-msg').text('');
                $.ajax({
                    type: 'GET',
                    url: "{{ route('Lrcolumnmapping.index') }}",
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.Lrcolumnmapping != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.Lrcolumnmapping, function(key, value) {
                                $('#tabledata').append(` 
                                    <tr>
                                        <td>${id}</td>
                                        <td>${value.lr_column.replace(/_/g, ' ')}</td>
                                        <td>${value.invoice_column}</td> 
                                        <td>  
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

             // edit column 
             $(document).on("click", ".edit-btn", function() {
                var element = $(this);
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'Do you want to edit this?', // Text
                    'Yes, edit',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        var editid = element.data('id'); 
                        $('#newColForm').removeClass('d-none');
                        $('#newColBtn').addClass('d-none');
                        let lrColumnMappingEditUrl = "{{ route('Lrcolumnmapping.edit','__editId__') }}".replace('__editId__',editid);
                        $.ajax({
                            type: 'GET',
                            url: lrColumnMappingEditUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(response) {
                                if (response.status == 200 && response.Lrcolumnmapping != '') {
                                    var Lrcolumnmapping = response.Lrcolumnmapping; 
                                    $('#edit_id').val(editid); 
                                    $('#lr_column').val(Lrcolumnmapping.lr_column);
                                    $('#invoice_column').val(Lrcolumnmapping.invoice_column); 
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
                                $('#column_type').prop('disabled', false);
                                loaderhide();
                                console.error('Error:', error);
                            }
                        });
                    } 
                );  
            });


             // delete column  link
             $(document).on("click", ".del-btn", function() {
                var element = $(this);
                var deleteid = element.data('id');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to delete this column?', // Text
                    'Yes, delete',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let lrColumnMapponigDeleteUrl = "{{ route('Lrcolumnmapping.delete','__deleteId__') }}".replace('__deleteId__',deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: lrColumnMapponigDeleteUrl,
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


            $('#columnform').on('submit', function(e) {
                e.preventDefault();
                loadershow();
                var element = $(this);
                var fomrdata = element.serializeArray();
                var editid = $('#edit_id').val();
                if(editid != '' && editid != null){
                    url = "{{route('Lrcolumnmapping.update','__editid__')}}".replace('__editid__',editid);
                    type = 'put';
                }else{
                    url = "{{ route('Lrcolumnmapping.store') }}";
                    type = 'post';
                }

                $.ajax({
                    type: type ,
                    url: url,
                    data: fomrdata,
                    success: function(response) {
                        if (response.status == 200) {
                            $('#newColForm').addClass('d-none');
                            $('#newColBtn').removeClass('d-none');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#columnform')[0].reset();
                            if(editid != null){
                                $('#edit_id').val('');
                            }
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else if (response.status == 422) {
                            $('.error-msg').text('');
                            $.each(response.errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
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
                        handleAjaxError(xhr);
                    }

                });

            });

        });
    </script>
@endpush
