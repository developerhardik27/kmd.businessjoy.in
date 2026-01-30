@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Sample Purchase
@endsection
@section('title')
    New Sample Purchase
@endsection


@section('form-content')
    <form id="brokerpurchaseform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">
                    <input type="hidden" class="form-control requiredinput" name="grade" id="grade">

                    <label for="garden_id">Select Garden</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="garden_id" id="garden_id">
                        <option selected disabled>Select your garden</option>
                    </select>
                    <span class="error-msg" id="error-garden_id" style="color: red"></span>

                </div>
                <div class="col-sm-6 mb-2">
                    <label for="invoice_no">Select Invoice No</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="invoice_no" id="invoice_no">
                        <option selected disabled>Select your Invoice No</option>
                    </select>
                    <span class="error-msg" id="error-invoice_no" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="grade">Grade</label>

                    <input type="text" class="form-control requiredinput" name="grade_name" id="grade_name"
                        placeholder="Enter grade" readonly>
                    <span class="error-msg" id="error-grade" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="bags">No Of Bages</label>
                    <input type="text" class="form-control requiredinput" name="bags" id="bags"
                        placeholder="Enter bags" readonly>
                    <span class="error-msg" id="error-bags" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="net_kg">Net Weight</label>
                    <input type="text" class="form-control requiredinput" name="net_kg" id="net_kg"
                        placeholder="Enter Net Weight" readonly>
                    <span class="error-msg" id="error-net_kg" style="color: red"></span>
                </div>
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset brokerpurchase Details"
                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save brokerpurchase Details"
                        class="btn btn-primary float-right my-0">Save</button>
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

            // redirect on brokerpurchase list page on click cancel btn
            loadGardens();

            function loadGardens() {
                loadershow();
                let token = "{{ session('api_token') }}";
                let company_id = "{{ session('company_id') }}";
                let user_id = "{{ session('user_id') }}";
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.getGardens') }}",
                    data: {
                        company_id: company_id,
                        user_id: user_id,
                        token: token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            $('#garden_id').empty();
                            $('#garden_id').append(
                                '<option disabled selected>Select your garden</option>');

                            $.each(response.data, function(index, item) {
                                $('#garden_id').append(
                                    `<option value="${item.garden_id}">${item.garden_name}</option>`
                                );
                            });
                            $('#garden_id').select2({
                                placeholder: "Select Gardens",
                                width: '100%',
                                search: true,
                            });
                            loaderhide();
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
            $('#garden_id').on('change', function() {
                let garden_id = $(this).val();
                loadInvoices(garden_id);
            });

            function loadInvoices(garden_id) {
                loadershow();
                let token = "{{ session('api_token') }}";
                let company_id = "{{ session('company_id') }}";
                let user_id = "{{ session('user_id') }}";

                $('#invoice_no').empty();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.getInvoices') }}",
                    data: {
                        garden_id: garden_id,
                        company_id: company_id,
                        user_id: user_id,
                        token: token
                    },
                    success: function(response) {
                        $('#invoice_no').empty();
                        $('#invoice_no').append(
                            '<option disabled selected>Select your invoice no</option>'
                        );

                        if (response.status === 200 && response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                $('#invoice_no').append(
                                    `<option value="${item.invoice_no}">${item.invoice_no}</option>`
                                );
                            });
                            $('#invoice_no').select2({
                                placeholder: "Select invoice no",
                                width: '100%',
                                search: true,
                            });
                        } else {
                            $('#invoice_no').append(
                                '<option disabled>No invoices found</option>'
                            );
                        }

                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            $('#invoice_no').on('change', function() {
                let invoice_no = $(this).val();
                let garden_id = $('#garden_id').val();
                loadOrderDetails(garden_id, invoice_no);
            });



            function loadOrderDetails(garden_id, invoice_no) {
                let token = "{{ session('api_token') }}";
                let company_id = "{{ session('company_id') }}";
                let user_id = "{{ session('user_id') }}";

                loadershow();

                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.getOtherDetails') }}",
                    data: {
                        garden_id: garden_id,
                        invoice_no: invoice_no,
                        company_id: company_id,
                        user_id: user_id,
                        token: token
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            let data = response.data[0];
                            $('#grade').val(data.grade_id);
                            $('#grade_name').val(data.grade_name);
                            $('#bags').val(data.bags);
                            $('#net_kg').val(data.net_kg);
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                });
            }



            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.brokerpurchase') }}";
            });

            // submit brokerpurchase form 
            $('#brokerpurchaseform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('brokerpurchase.store') }}",
                    data: formdata,
                    success: function(response) {
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location.href =
                                "{{ route('admin.brokerpurchase') }}"; // redirect on brokerpurchase list page

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
