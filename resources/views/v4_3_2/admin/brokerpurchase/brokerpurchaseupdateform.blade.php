@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Sample Purchase
@endsection
@section('title')
    Update Sample Purchase
@endsection
@section('form-content')
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Updating this order will also update the bag details and any other related information. If an invoice has already been created, the invoice will also be updated with these changes. Do you want to continue?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelConfirmBtn">Cancel</button>
                    <button type="button" class="btn btn-primary" id="okConfirmBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <form id="brokerpurchaseupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-4 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">
                    <input type="hidden" class="form-control requiredinput" name="grade" id="grade">
                    <input type="hidden" id="broker_purchase_id" value="{{ $brokerpurchase->id ?? '' }}">
                    <label for="garden_id">Select Garden</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="garden_id" id="garden_id" style="height: 38px;" disabled>
                        <option selected disabled>Select your garden</option>
                    </select>
                    <span class="error-msg" id="error-garden_id" style="color: red"></span>

                </div>
                <div class="col-sm-4 mb-2">
                    <label for="sample_purchase_date">Sample Purchase Date</label>
                    <input type="date" class="form-control" name="sample_purchase_date" id="sample_purchase_date" style="height: 38px;">
                    <span class="error-msg" id="error-sample_purchase_date" style="color: red"></span>
                </div>
                <div class="col-sm-4 mb-2">
                    <label for="invoice_no">Select Invoice No</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="invoice_no" id="invoice_no" style="height: 38px;" disabled>
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
                        placeholder="Enter bags" >
                    <span class="error-msg" id="error-bags" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="net_kg">Net Weight</label>
                    <input type="text" class="form-control requiredinput" name="net_kg" id="net_kg"
                        placeholder="Enter Net Weight" >
                    <span class="error-msg" id="error-net_kg" style="color: red"></span>
                </div>
                <input type="hidden" class="form-control requiredinput" name="rate" id="rate"
                    placeholder="Enter Rate" readonly>
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
        $(document).ready(function() {
            // Handle Cancel button in confirmation modal
            $('#cancelConfirmBtn').on('click', function() {
                window.location.href = "{{ route('admin.brokerpurchase') }}";
            });

            // Handle OK button in confirmation modal
            $('#okConfirmBtn').on('click', function() {
                $('#confirmationModal').modal('hide');
            });

            let edit_id = @json($edit_id);
            let oldData = null;

            function commonData() {
                return {
                    token: "{{ session('api_token') }}",
                    company_id: "{{ session('company_id') }}",
                    user_id: "{{ session('user_id') }}"
                };
            }


            function clearDetails() {
                $('#grade, #grade_name, #bags, #net_kg').val('');
            }


            function loadGardens(selectedGarden = null, callback = null) {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.getGardens') }}",
                    data: commonData(),
                    success: function(response) {

                        $('#garden_id').empty().append('<option disabled>Select your garden</option>');
                        if (response.status === 200) {
                            $.each(response.data, function(i, item) {
                                $('#garden_id').append(
                                    `<option value="${item.garden_id}">${item.garden_name}</option>`
                                );
                            });
                            if (selectedGarden) {
                                $('#garden_id').val(selectedGarden);
                            }
                            if (callback) callback();
                            $('#garden_id').select2({
                                placeholder: "Select Gardens",
                                width: '100%',
                                search: true,
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            }


            function loadInvoices(garden_id, selectedInvoice = null, callback = null) {
                selectedInvoice = parseInt(selectedInvoice);
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.getupdateInvoices') }}",
                    data: {
                        ...commonData(),
                        garden_id: garden_id
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            $.each(response.data, function(i, item) {
                                $('#invoice_no').append(
                                    `<option value="${item.invoice_no}">${item.invoice_no}</option>`
                                );
                            });
                            if (selectedInvoice) {
                                $('#invoice_no').val(selectedInvoice);
                            } else {
                                $('#invoice_no').val('');
                            }
                            $('#invoice_no').select2({
                                placeholder: "Select Invoice no",
                                width: '100%',
                                search: true,
                            });
                            if (callback) callback();
                        }
                        loaderhide();

                    },
                    error: function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            }


            function loadOrderDetails(garden_id, invoice_no) {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.getOtherDetails') }}",
                    data: {
                        ...commonData(),
                        garden_id: garden_id,
                        invoice_nos: invoice_no
                    },
                    success: function(response) {
                        if (response.status === 200 && response.data.length > 0) {
                            let data = response.data[0];
                            $('#grade').val(data.grade_id);
                            $('#grade_name').val(data.grade_name);
                            $('#bags').val(data.bags);
                            $('#rate').val(data.rate);
                            $('#net_kg').val(data.net_kg);
                        } else {
                            clearDetails();
                        }
                        loaderhide();
                    },
                    error: function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            }


            $('#garden_id').on('change', function() {
                let garden_id = $(this).val();
                clearDetails();
                $("#error-invoice_no").text('');
                $('#invoice_no').empty().append(
                    '<option disabled selected>Select your Invoice No</option>');

                if (garden_id) {
                    loadInvoices(garden_id);
                }
            });


            $('#invoice_no').on('change', function() {
                let invoice_no = $(this).val();
                let garden_id = $('#garden_id').val();
                $("#error-invoice_no").text('');

                if (!invoice_no || !garden_id) return;


                $.ajax({
                    url: "{{ route('brokerpurchase.checkInvoice') }}",
                    type: 'GET',
                    data: {
                        ...commonData(),
                        invoice_no: invoice_no,
                        id: $('#broker_purchase_id').val()
                    },
                    success: function(response) {
                        loadOrderDetails(garden_id, invoice_no);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            loaderhide();
                            handleAjaxError(xhr);
                            clearDetails();
                            $('#invoice_no').val(invoice_no);
                        }
                    }
                });
            });


            if (edit_id) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.edit', $edit_id) }}",
                    data: commonData(),
                    success: function(response) {
                        if (response.status === 200) {
                            oldData = response.brokerpurchase;
                            const warningMessage = oldData.warning_message || '';

                            $("#broker_purchase_id").val(oldData.id);
                            $("#sample_purchase_date").val(oldData.sample_purchase_date);
                            // Sequentially load gardens → invoices → order details
                            loadGardens(oldData.garden_id, function() {
                                loadInvoices(oldData.garden_id, oldData.invoice_no, function() {
                                    loadOrderDetails(oldData.garden_id, oldData
                                        .invoice_no);
                                });
                            });

                            // Update modal message if warning exists
                            if (warningMessage) {
                                $('#confirmationModal .modal-body p').text(warningMessage);
                                $('#confirmationModal').modal('show');
                            }
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            }


            $('#brokerpurchaseupdateform').submit(function(e) {
                e.preventDefault();
                loadershow();
                $.ajax({
                    type: 'PUT',
                    url: "{{ route('brokerpurchase.update', $edit_id) }}",
                    data: $(this).serialize(),
                    success: function(response) {
                        loaderhide();
                        if (response.status === 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location.href = "{{ route('admin.brokerpurchase') }}";
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            });


            $('#cancelbtn').click(function() {
                window.location.href = "{{ route('admin.brokerpurchase') }}";
            });
        });
    </script>
@endpush
