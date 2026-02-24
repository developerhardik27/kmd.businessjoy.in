@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Commission Bill
@endsection
@section('title')
    Commission Bill
@endsection


@section('form-content')
    <form id="brokerbillform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">
                    <input type="hidden" class="form-control requiredinput" name="grade" id="grade">
                    <input type="hidden" name="form_type" id="form_type" value="">
                    <label for="garden_id">Select Garden</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="garden_id" id="garden_id">
                        <option selected disabled>Select your garden</option>
                    </select>
                    <span class="error-msg" id="error-garden_id" style="color: red"></span>

                </div>
                <div class="col-sm-12 mt-3 table-responsive">
                    <table id="broker_purchases_table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Invoive No</th>
                                <th>Invoive Date</th>
                                <th>Invoive Amount</th>
                                <th>Brokerage Date</th>
                                <th>Brokerage (%)</th>
                                <th>Brokerage Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No records found
                                </td>
                            </tr>
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
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            // redirect on brokerpurchase list page on click cancel btn
            loadGardens();
            let edit_id = @json($edit_id);


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
                            if (edit_id) {
                                $('#garden_id').val(edit_id).trigger('change');
                            }
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
                if (edit_id) {
                    editotherdata(garden_id);
                } else {
                    addotherdata(garden_id);
                }


            });

            function formatDateForInput(dateStr) {
                if (!dateStr) return '';
                return dateStr.split(' ')[0]; // YYYY-MM-DD
            }
            //this for edit time load data
            function editotherdata(garden_id) {
                loadershow();
                let token = "{{ session('api_token') }}";
                let company_id = "{{ session('company_id') }}";
                let user_id = "{{ session('user_id') }}";
                $('#form_type').val('edit');
                $('#broker_purchases_table tbody').empty(); // Clear previous rows

                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokeragebill.getOtherDatanotnull') }}",
                    data: {
                        garden_id: garden_id,
                        company_id: company_id,
                        user_id: user_id,
                        token: token
                    },
                    success: function(response) {
                        console.log(response.data);
                        $('#broker_purchases_table tbody').empty();
                        if (!response.data || response.data.length === 0) {
                            $('#broker_purchases_table tbody').append(`
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No records found
                                    </td>
                                </tr>
                            `);
                            loaderhide();
                            return;
                        }
                        response.data.forEach(function(item, index) {
                            let row = `
                                <tr>
                                    <input type="hidden" name="rows[${index}][id]" value="${item.id}">

                                    <td>
                                        <input type="text"
                                            name="rows[${index}][inv_no]"
                                            value="${item.inv_no ?? ''}"
                                            class="form-control" ${item.inv_no ?? 'disabled'} disabled>
                                    </td>

                                    <td>
                                        <input type="date"
                                            name="rows[${index}][inv_date]"
                                            value="${formatDateForInput(item.inv_date)}"
                                            class="form-control" disabled>
                                    </td>

                                    <td>
                                        <input type="text"
                                            name="rows[${index}][amount]"
                                            value="${item.amount ?? ''}"
                                            class="form-control amount" ${item.amount ?? 'disabled'} disabled>
                                    </td>

                                   <td>
                                        <input type="date"
                                            name="rows[${index}][brokerage_date]"
                                            value="${item.brokerage_date ? item.brokerage_date : new Date().toISOString().split('T')[0]}"
                                            class="form-control">
                                             <span class="error-msg" id="error-brokerage_date_${index}" style="color:red"></span>
                                    </td>

                                    <td>
                                        <input type="decimal"
                                            name="rows[${index}][brokerage]"
                                            value="${item.brokerage ?? ''}"
                                            class="form-control brokerage">
                                             <span class="error-msg" id="error-brokerage_${index}" style="color:red"></span>
                                    </td>

                                    <td>
                                        <input type="number"
                                            name="rows[${index}][brokerage_amount]"
                                            value="${item.borkerage_amount ?? ''}"
                                            class="form-control brokerage_amount" step="0.01">
                                    </td>
                                </tr>
                            `;
                            let $row = $(row);
                            $('#broker_purchases_table tbody').append($row);

                            calculateBrokerageAmount($row);
                        });


                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                });
            }
            // this for add time load data
            function addotherdata(garden_id) {
                loadershow();
                let token = "{{ session('api_token') }}";
                let company_id = "{{ session('company_id') }}";
                let user_id = "{{ session('user_id') }}";
                $('#form_type').val('add');
                $('#broker_purchases_table tbody').empty(); // Clear previous rows
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokeragebill.getOtherDatanull') }}",
                    data: {
                        garden_id: garden_id,
                        company_id: company_id,
                        user_id: user_id,
                        token: token
                    },
                    success: function(response) {
                        console.log(response.data);
                        $('#broker_purchases_table tbody').empty();
                        if (!response.data || response.data.length === 0) {
                            $('#broker_purchases_table tbody').append(`
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No records found
                                    </td>
                                </tr>
                            `);
                            loaderhide();
                            return;
                        }
                        response.data.forEach(function(item, index) {
                            let row = `
                                <tr>
                                    <input type="hidden" name="rows[${index}][id]" value="${item.id}">

                                    <td>
                                        <input type="text"
                                            name="rows[${index}][inv_no]"
                                            value="${item.inv_no ?? ''}"
                                            class="form-control" ${item.inv_no ?? 'disabled'}>
                                    </td>

                                    <td>
                                        <input type="date"
                                            name="rows[${index}][inv_date]"
                                            value="${formatDateForInput(item.inv_date)}"
                                            class="form-control">
                                    </td>

                                    <td>
                                        <input type="text"
                                            name="rows[${index}][amount]"
                                            value="${item.amount ?? ''}"
                                            class="form-control amount" ${item.amount ?? 'disabled'}>
                                    </td>

                                    <td>
                                        <input type="date"
                                            name="rows[${index}][brokerage_date]"
                                            value="${item.brokerage_date ? item.brokerage_date : new Date().toISOString().split('T')[0]}"
                                            class="form-control">
                                             <span class="error-msg" id="error-brokerage_date_${index}" style="color:red"></span>
                                    </td>

                                    <td>
                                        <input type="decimal"
                                            name="rows[${index}][brokerage]"
                                            value="${item.brokerage ?? ''}"
                                            class="form-control brokerage">
                                          <span class="error-msg" id="error-brokerage_${index}" style="color:red"></span>
                                    </td>

                                    <td>
                                        <input type="number"
                                            name="rows[${index}][brokerage_amount]"
                                            value="${item.borkerage_amount ?? ''}"
                                            class="form-control brokerage_amount">
                                    </td>
                                </tr>
                            `;
                            let $row = $(row);
                            $('#broker_purchases_table tbody').append($row);

                            calculateBrokerageAmount($row);
                        });


                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                });
            }

            function calculateBrokerageAmount(row) {
                let amount = parseFloat(row.find('.amount').val()) || 0;
                let brokerage = parseFloat(row.find('.brokerage').val()) || 0;

                let brokerageAmount = amount * brokerage / 100;
                brokerageAmount = Math.round(brokerageAmount);
                row.find('.brokerage_amount').val(brokerageAmount.toFixed(2));
            }
            $(document).on('input', '.amount, .brokerage', function() {
                let row = $(this).closest('tr');
                calculateBrokerageAmount(row);
            });


            // Remove row dynamically
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });

            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.brokeragebill') }}";
            });

            // submit brokeragebill form 
            $('#brokerbillform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('brokeragebill.store') }}",
                    data: formdata,
                    success: function(response) {
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location.href =
                                "{{ route('admin.brokeragebill') }}"; // redirect on brokeragebill list page

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
                        $('.error-msg').text('');

                        if (xhr.status === 422) {

                            let response = xhr.responseJSON;

                            if (!response || !response.errors) {
                                return;
                            }

                            let errors = response.errors;
                            let firstErrorElement = null;

                            $.each(errors, function(key, value) {

                                let errorElement = null;
                                let inputElement = null;

                                // For dynamic rows (rows.0.brokerage)
                                if (key.startsWith('rows.')) {

                                    let parts = key.split('.');
                                    let rowIndex = parts[1]; // 0
                                    let field = parts[2]; // brokerage

                                    errorElement = $('#error-' + field + '_' +
                                        rowIndex);
                                    inputElement = $('[name="rows[' + rowIndex + '][' +
                                        field + ']"]');

                                } else {
                                    errorElement = $('#error-' + key);
                                    inputElement = $('[name="' + key + '"]');
                                }

                                if (errorElement.length) {
                                    errorElement.text(value[0]);
                                }

                                if (inputElement.length) {
                                    // inputElement.addClass('is-invalid');
                                }

                                if (!firstErrorElement && errorElement.length) {
                                    firstErrorElement = errorElement;
                                }
                            });

                            // Scroll to first error
                            if (firstErrorElement) {
                                $('html, body').animate({
                                    scrollTop: firstErrorElement.offset().top - 120
                                }, 600);
                            }

                            return;
                        }

                        // For other server errors (500, 401, etc.)
                        let errorMessage = "Something went wrong";

                        try {
                            let responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || errorMessage;
                        } catch (e) {}

                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            });
        });
    </script>
@endpush
