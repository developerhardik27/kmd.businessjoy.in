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

    <form id="brokerpurchaseform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-4 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">
                    {{-- <input type="hidden" class="form-control requiredinput" name="grade" id="grade"> --}}

                    <label for="garden_id">Select Garden</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="garden_id" id="garden_id" style="height: 38px;">
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
                    <label for="invoice_no">Select Invoice/Lot No</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="invoice_no[]" id="invoice_no" multiple style="height: 38px;">

                    </select>
                    <span class="error-msg" id="error-invoice_no" style="color: red"></span>
                </div>
                <div class="col-sm-12 mt-4">
                    <table class="table table-bordered" id="invoice-details-table">
                        <thead>
                            <tr>
                                <th>Buyer name</th>
                                <th>Invoice No</th>
                                <th>Grade</th>
                                <th>No of Bags</th>
                                <th>Net Weight (kg)</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX will populate rows here -->
                        </tbody>
                    </table>
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
            // Handle Cancel button in confirmation modal
            $('#cancelConfirmBtn').on('click', function() {
                $('#confirmationModal').modal('hide');
            });

            // Handle OK button in confirmation modal
            $('#okConfirmBtn').on('click', function() {
                $('#confirmationModal').modal('hide');
                // Proceed with actual form submission
                submitBrokerPurchaseForm();
            });

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            
            /* ── Init Select2 helper ── */
            function initSelect2(selector, placeholder, multiple = false) {
                $(selector).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    ...(multiple ? {} : {}),
                    templateResult: function (option) {
                        if (!option.id) return option.text;
                        const selected = $(selector).val();
                        if (Array.isArray(selected) && selected.includes(option.id)) return null;
                        if (selected == option.id) return null;
                        return option.text;
                    }
                });
            }

            let message = "{{ session('message') }}";
            if (message) {
                Toast.fire({
                    icon: 'error',
                    title: message
                });
            }
            // redirect on brokerpurchase list page on click cancel btn
            // Check if coming from order view with order_id and garden_id (from sessionStorage)
            const orderId = sessionStorage.getItem('sample_purchase_order_id');
            const gardenId = sessionStorage.getItem('sample_purchase_garden_id');
            const invoiceNos = sessionStorage.getItem('sample_purchase_invoice_nos');

            if (orderId && gardenId) {
                // Load gardens first, then select the specific garden
                loadGardens(gardenId, invoiceNos ? JSON.parse(invoiceNos) : null);
                // Clear sessionStorage after use
                sessionStorage.removeItem('sample_purchase_order_id');
                sessionStorage.removeItem('sample_purchase_garden_id');
                sessionStorage.removeItem('sample_purchase_invoice_nos');
            } else {
                loadGardens();
            }

            function loadGardens(selectedGardenId = null, preSelectInvoices = null) {
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
                            initSelect2('#garden_id', 'Select Gardens');

                            // Pre-select garden if provided
                            if (selectedGardenId) {
                                $('#garden_id').val(selectedGardenId).trigger('change');
                                // Store pre-select invoices for use after invoices are loaded
                                if (preSelectInvoices) {
                                    window.preSelectInvoices = preSelectInvoices;
                                }
                            }

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
                        // $('#invoice_no').append(
                        //     '<option disabled selected>Select your invoice no</option>'
                        // );

                        if (response.status === 200 && response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                $('#invoice_no').append(
                                    `<option value="${item.invoice_no}">${item.invoice_no}</option>`
                                );
                            });
                            initSelect2('#invoice_no', 'Select invoice(s)', true);

                            // Pre-select invoices if available
                            if (window.preSelectInvoices && window.preSelectInvoices.length > 0) {
                                $('#invoice_no').val(window.preSelectInvoices).trigger('change');
                                window.preSelectInvoices = null; // Clear after use
                            }
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
            initSelect2('#invoice_no', 'Select invoice(s)', true);
            $('#invoice_no').on('change', function() {
                let garden_id = $('#garden_id').val();
                let invoice_nos = $(this).val(); // this is now an array
                if (invoice_nos && invoice_nos.length > 0) {
                    loadMultipleOrderDetails(garden_id, invoice_nos);
                } else {
                    $('#invoice-details-table tbody').empty(); // clear table
                }
            });

            function loadMultipleOrderDetails(garden_id, invoice_nos) {
                let token = "{{ session('api_token') }}";
                let company_id = "{{ session('company_id') }}";
                let user_id = "{{ session('user_id') }}";

                loadershow();

                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokerpurchase.getOtherDetails') }}",
                    data: {
                        garden_id: garden_id,
                        invoice_nos: invoice_nos, // send array
                        company_id: company_id,
                        user_id: user_id,
                        token: token
                    },
                    success: function(response) {
                        $('#invoice-details-table tbody').empty();

                        if (response.status === 200 && response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                console.log(item);
                                $buyername = item.buyer_name;
                                $color = 'black';
                                if (!$buyername) {
                                    $buyername = 'Order Not Select Buyer';
                                    $color = 'red';
                                }
                                $('#invoice-details-table tbody').append(`
                        <tr data-index="${index}">
                            <td style="color: ${$color};">${$buyername}</td>
                            <td>${item.invoice_no}</td>
                            <td>${item.grade_name}</td>
                            <td><input type="number" class="form-control form-control-sm editable-bags" data-index="${index}" value="${item.bags}" min="0" style="width: 80px;"></td>
                            <td><input type="number" class="form-control form-control-sm editable-net_kg" data-index="${index}" value="${item.net_kg}" min="0" step="0.01" style="width: 100px;"></td>
                            <td><input type="number" class="form-control form-control-sm editable-rate" data-index="${index}" value="${item.rate}" min="0" step="0.01" style="width: 100px;"></td>
                        </tr>
                    `);
                                $('#brokerpurchaseform').append(`
                            <input type="hidden" class="invoice-details-inputs" name="details[${index}][order_detail_id]" value="${item.order_detail_id}">
                            <input type="hidden" class="invoice-details-inputs" name="details[${index}][invoice_no]" value="${item.invoice_no}">
                            <input type="hidden" class="invoice-details-inputs" name="details[${index}][grade_name]" value="${item.grade_name}">
                            <input type="hidden" class="invoice-details-inputs" name="details[${index}][garde]" value="${item.grade_id}">
                            <input type="hidden" class="invoice-details-inputs hidden-bags" data-index="${index}" name="details[${index}][bags]" value="${item.bags}">
                            <input type="hidden" class="invoice-details-inputs hidden-net_kg" data-index="${index}" name="details[${index}][net_kg]" value="${item.net_kg}">
                            <input type="hidden" class="invoice-details-inputs hidden-rate" data-index="${index}" name="details[${index}][rate]" value="${item.rate}">
                        `);
                            });
                        } else {
                            $('#invoice-details-table tbody').append(
                                '<tr><td colspan="5" class="text-center">No data found</td></tr>');
                        }

                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                });
            }

            /* ── Update hidden inputs when editable fields change ── */
            $(document).on('change', '.editable-bags', function() {
                const index = $(this).data('index');
                const value = $(this).val();
                $(`.hidden-bags[data-index="${index}"]`).val(value);
            });

            $(document).on('change', '.editable-net_kg', function() {
                const index = $(this).data('index');
                const value = $(this).val();
                $(`.hidden-net_kg[data-index="${index}"]`).val(value);
            });

            $(document).on('change', '.editable-rate', function() {
                const index = $(this).data('index');
                const value = $(this).val();
                $(`.hidden-rate[data-index="${index}"]`).val(value);
            });


            /* ── Reset button handler ── */
            $('button[type="reset"]').on('click', function() {
                setTimeout(function() {
                    // Reset Select2 dropdowns
                    $('#garden_id').val(null).trigger('change');
                    $('#invoice_no').val(null).trigger('change');
                    // Clear the invoice details table
                    $('#invoice-details-table tbody').empty();
                    // Clear hidden inputs
                    $('.invoice-details-inputs').remove();
                }, 10);
            });

            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.brokerpurchase') }}";
            });

            // Form submit handler - show confirmation modal
            $('#brokerpurchaseform').submit(function(event) {
                event.preventDefault();
                // Show confirmation modal
                $('#confirmationModal').modal('show');
            });

            // Function to handle actual form submission
            function submitBrokerPurchaseForm() {
                loadershow();
                $('.error-msg').text('');

                // Check if any invoice details have missing buyer names
                let hasMissingBuyer = false;
                $('#invoice-details-table tbody tr').each(function() {
                    const buyerCell = $(this).find('td:first');
                    if (buyerCell.css('color') === 'rgb(255, 0, 0)' || buyerCell.text().includes('Order Not Select Buyer')) {
                        hasMissingBuyer = true;
                    }
                });

                if (hasMissingBuyer) {
                    loaderhide();
                    Toast.fire({ icon: 'error', title: 'Cannot submit: Order has no buyer selected. Please select a buyer for the order first.' });
                    return;
                }

                let formdata = $('#brokerpurchaseform').serialize();
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
            }
        });
    </script>
@endpush
