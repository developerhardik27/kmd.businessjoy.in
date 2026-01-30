@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Order Form
@endsection

@section('style')
    <style>
        .disableinput {
            border: none;
        }

        /* For select2 dropdown, override the default select2 styles for disabled items */
        .select2-results__option[aria-disabled="true"] {
            color: red !important;
        }

        table input.form-control {
            width: auto;
            min-width: 100%;
        }

        .requiredinput {
            width: auto;
            min-width: 100%;
        }

        table textarea.form-control {
            width: auto;
            min-width: 100%;
        }

        .select2-container {
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
@endsection
@section('title')
    Update Order Form
@endsection

@section('form-content')
    <form id="orderupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}" placeholder="token"
                    required />
                <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id">
                <div class="col-sm-6 mb-2">
                    <label for="buyer_party">Buyer Party</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="buyer_party" id="buyer_party">
                        <option value="" selected disabled>Select Buyer</option>
                    </select>
                    <span class="error-msg" id="error-buyer_party" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="transport">Transport</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="transport" id="transport">
                        <option value="" selected disabled>Select Transport</option>
                    </select>
                    <span class="error-msg" id="error-transport" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="credit_days">Credit Days</label><span style="color:red;">*</span>
                    <select class="form-control requiredinput" name="credit_days" id="credit_days">
                        <option value="" disabled selected>Select Day</option>
                        <option value="CD">CD</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="45">45</option>
                        <option value="60">60</option>
                        <option value="90">90</option>
                    </select>
                    <span class="error-msg" id="error-credit_days" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="discount">Discount (%)</label>
                    <input type="number" step="0.01" class="form-control calculationfield" name="discount"
                        id="discount" placeholder="Discount">
                    <span class="error-msg" id="error-discount" style="color:red"></span>
                </div>

                <div id="table" class="table-editable w-100" style="overflow-x:auto">
                    <table class="table table-bordered  table-striped text-center" id="purchaseTable">
                        <thead>
                            <tr>
                                <th>Garden</th>
                                <th>Invoice No / Lot No</th>
                                <th>Grade</th>
                                <th>Bags</th>
                                <th>Kg</th>
                                <th>Net Kg</th>
                                <th>Rate / Kg</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseBody">
                            <tr id ="addnewbtnrow">
                                <td colspan="8" class="text-center">
                                    <button type="button" id="addRowBtn" class="btn btn-success">
                                        + Add New Row
                                    </button>
                                </td>
                            </tr>
                            <tr id="totalRow">
                                <td colspan="5" class="text-center"><strong>Total</strong></td>
                                <td class="text-center"><strong id="totalNetKg">0.00</strong></td>
                                <td></td>
                                <td class="text-center"><strong id="totalAmount">0.00</strong></td>
                            </tr>

                            <tr>
                                <td colspan="7" class="text-right"><strong>Discount Amount</strong></td>
                                <td class="text-center"><strong id="discountAmount">0.00</strong></td>
                            </tr>

                            <tr>
                                <td colspan="7" class="text-right"><strong>Final Amount</strong></td>
                                <td class="text-center"><strong id="finalAmount">0.00</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-sm-12 mt-3">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset order" class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save order" class="btn btn-primary float-right my-0">Save</button>
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
            // get selected companymaster data and show it into fields

            // redirect on companymaster list page on click cancel btn

            let gardens;
            let grades;
            let row = -1;

            async function fetchGardens() {
                try {
                    const response = await ajaxRequest("GET", "{{ route('garden.index') }}", {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    });

                    gardens = '<option value="" selected disabled>Select Garden</option>';

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(garden => {
                            gardens += `<option value="${garden.id}">${garden.garden_name}</option>`;
                        });
                    }
                } catch (xhr) {
                    handleAjaxError(xhr);
                } finally {
                    loaderhide();
                }
            }

            async function fetchGrade() {
                try {
                    const response = await ajaxRequest("GET", "{{ route('grade.index') }}", {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    });

                    grades = '<option value="" selected disabled>Select Grade</option>';

                    if (response.data?.length) {
                        response.data.forEach(grade => {
                            grades += `<option value="${grade.id}">${grade.grade}</option>`;
                        });
                    }
                } catch (xhr) {
                    handleAjaxError(xhr);
                } finally {
                    loaderhide();
                }
            }

            async function buyer_party() {
                try {
                    const response = await ajaxRequest("GET", "{{ route('buyer.index') }}", {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    });

                    let partySelect = $('#buyer_party');
                    partySelect.empty().append(
                        '<option value="" selected disabled>Select Buyer</option>'
                    );

                    if (response.data?.length) {
                        response.data.forEach(party => {
                            partySelect.append(
                                `<option value="${party.id}">${party.name}</option>`
                            );
                        });
                    } else {
                        partySelect.append('<option disabled>No party Found</option>');
                    }
                } catch (xhr) {
                    handleAjaxError(xhr);
                } finally {
                    loaderhide();
                }
            }

            async function transport() {
                try {
                    const response = await ajaxRequest("GET", "{{ route('transport.index') }}", {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    });

                    let transportSelect = $('#transport');
                    transportSelect.empty().append(
                        '<option value="" selected disabled>Select Transport</option>'
                    );

                    if (response.data?.length) {
                        response.data.forEach(item => {
                            transportSelect.append(
                                `<option value="${item.id}">${item.name}</option>`
                            );
                        });
                    } else {
                        transportSelect.append('<option disabled>No transport Found</option>');
                    }
                } catch (xhr) {
                    handleAjaxError(xhr);
                } finally {
                    loaderhide();
                }
            }

            async function initPurchaseForm() {
                loadershow();

                await Promise.all([
                    fetchGardens(),
                    fetchGrade(),
                    buyer_party(),
                    transport()
                ]);

                loaddata();
            }

            initPurchaseForm();

            function loaddata() {
                loadershow();
                var edit_id = @json($edit_id);
                let orderSearchUrl = "{{ route('order.edit', '__editId__') }}".replace('__editId__',
                    edit_id);

                $.ajax({
                    type: 'GET',
                    url: orderSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            let orders = response.orders;
                            let order = orders.order;
                            let order_details = orders.order_details;
                            $('#buyer_party').val(order.buyer_party);
                            $('#transport').val(order.transport);
                            $('#credit_days').val(order.credit_days);
                            $('#discount').val(order.discount);
                            order_details.forEach(function(detail) {
                                row++;
                                let newRow = `
                                <tr id="row_${row}" class='recordRow'>
                                    <td>
                                        <select id="garden_${row}" class="form-control garden-select requiredinput" name="garden_id[]">
                                            ${gardens}
                                        </select>
                                    </td>
    
                                    <td>
                                        <input type="text" class="form-control requiredinput" name="invoice_no[]"
                                            value="${detail.invoice_no ?? ''}">
                                         <span class="error-msg" id="error-invoice_no_${row}" style="color:red"></span>

                                    </td>
    
                                    <td>
                                        <select id="grade_${row}" class="form-control grade-select requiredinput" name="grade[]">
                                            ${grades}
                                        </select>
                                    </td>
    
                                    <td>
                                        <input type="number" class="form-control bags calculationfield" name="bags[]"
                                            value="${detail.bags ?? ''}">
                                         <span class="error-msg" id="error-bags_${row}" style="color:red"></span>

                                    </td>
    
                                    <td>
                                        <input type="number" step="0.01" class="form-control kg calculationfield" name="kg[]"
                                            value="${detail.kg ?? ''}">
                                    <span class="error-msg" id="error-kg_${row}" style="color:red"></span>

                                    </td>
    
                                    <td>
                                        <input type="number" step="0.01" class="form-control net-kg" name="net_kg[]" disabled
                                            value="${detail.net_kg ?? ''}">
                                    </td>
    
                                    <td>
                                        <input type="number" step="0.01" class="form-control rate calculationfield" name="rate[]"
                                            value="${detail.rate ?? ''}">
                                  <span class="error-msg" id="error-rate_${row}" style="color:red"></span>

                                    </td>
    
                                    <td>
                                        <input type="number" class="form-control amount" name="amt[]" disabled
                                            value="${detail.amount ?? ''}">
                                    </td>
    
                                    <td>
                                        <button type="button" class="btn btn-danger remove-row">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>`;

                                $('#purchaseBody #addnewbtnrow').before(newRow);

                                $(`#garden_${row}`).val(detail.garden_id);
                                $(`#grade_${row}`).val(detail.grade);
                            });

                            calculateTotals();

                        } else if (response.status == 500) {
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


            function calculateTotals() {

                let totalNetKg = 0;
                let totalAmount = 0;

                $('.net-kg').each(function() {
                    totalNetKg += parseFloat($(this).val()) || 0;
                });

                $('.amount').each(function() {
                    totalAmount += parseFloat($(this).val()) || 0;
                });

                // Discount percentage from your input
                let discountPercent = parseFloat($('#discount').val()) || 0;

                // Discount amount
                let discountAmount = (totalAmount * discountPercent) / 100;

                // Final amount
                let finalAmount = totalAmount - discountAmount;

                $('#totalNetKg').text(totalNetKg.toFixed(2));
                $('#totalAmount').text(totalAmount.toFixed(2));
                $('#discountAmount').text(discountAmount.toFixed(2));
                $('#finalAmount').text(finalAmount.toFixed(2));
            }

            $(document).on('keyup change', '.calculationfield', function() {
                let row = $(this).closest('tr');
                let bags = parseFloat(row.find('.bags').val()) || 0;
                let kg = parseFloat(row.find('.kg').val()) || 0;
                let rate = parseFloat(row.find('.rate').val()) || 0;
                let netKg = bags * kg;
                let amount = netKg * rate;
                row.find('.net-kg').val(netKg.toFixed(2));
                row.find('.amount').val(amount.toFixed(2));
                calculateTotals();
            });

            function addNewRow() {
                row++
                let newRow = `
                    <tr id='row_${row}' class='recordRow'>
                        <td>
                            <select class="form-control garden-select requiredinput" name="garden_id[]">
                                ${gardens}
                            </select>
                            <span class="error-msg" id="error-garden_id_${row}" style="color:red"></span>

                        </td>
        
                        <td>
                            <input type="text" class="form-control requiredinput" name="invoice_no[]"
                                placeholder="Enter Invoice No">
                                <span class="error-msg" id="error-invoice_no_${row}" style="color:red"></span>
                        </td>
        
                        <td>
                            <select class="form-control grade-select requiredinput" name="grade[]">
                                ${grades}}
                            </select>
                        </td>
        
                        <td>
                            <input type="number" class="form-control bags calculationfield" name="bags[]"
                                placeholder="Enter No of Bags">
                            <span class="error-msg" id="error-bags_${row}" style="color:red"></span>

                        </td>
        
                        <td>
                            <input type="number" step="0.01" class="form-control kg calculationfield" name="kg[]"
                                placeholder="Enter no of Kg">
                            <span class="error-msg" id="error-kg_${row}" style="color:red"></span>

                        </td>
        
                        <td>
                            <input type="number" step="0.01" class="form-control net-kg" name="net_kg[]"disabled
                                placeholder="Enter no of net kg">
                        </td>
        
                        <td>
                            <input type="number" step="0.01" class="form-control rate calculationfield" name="rate[]"
                                placeholder="Enter per kg rate">
                             <span class="error-msg" id="error-rate_${row}" style="color:red"></span>

                        </td>
        
                        <td>
                            <input type="number" class="form-control amount" name="amt[]" disabled
                                placeholder="0">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-row"><i class="ri-delete-bin-line"></i></button>
                        </td>
                    </tr>
                `;

                $('#purchaseBody #addnewbtnrow').before(newRow);

                calculateTotals();
            }

            $('#addRowBtn').on('click', function() {
                addNewRow();
            });

            $(document).on('click', '.remove-row', function() {
                let row = $(this).closest('tr');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This row will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        calculateTotals();
                    }
                })
            });

            // redirect on companymaster list page on click cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.order') }}";
            });

            // subimt form
            $('#orderupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let rows = [];
                $('#purchaseBody tr.recordRow').each(function() {

                    let garden_id = $(this).find('select[name="garden_id[]"]').val();


                    let invoice_no = $(this).find('input[name="invoice_no[]"]').val();
                    let grade = $(this).find('select[name="grade[]"]').val();
                    let bags = parseFloat($(this).find('input[name="bags[]"]').val()) || 0;
                    let kg = parseFloat($(this).find('input[name="kg[]"]').val()) || 0;
                    let net_kg = bags * kg;
                    let rate = parseFloat($(this).find('input[name="rate[]"]').val()) || 0;
                    let amount = net_kg * rate;

                    rows.push({
                        garden_id: garden_id,
                        invoice_no: invoice_no,
                        grade: grade,
                        bags: bags,
                        kg: kg,
                        net_kg: net_kg,
                        rate: rate,
                        amount: amount
                    });
                });
                if (rows.length < 1) {
                    Toast.fire({
                        icon: "error",
                        title: "Enter At list One Recode"
                    });
                }
                let dataToSend = {
                    _token: $('input[name="_token"]').val(),
                    token: $('input[name="token"]').val(),
                    user_id: $('input[name="user_id"]').val(),
                    company_id: $('input[name="company_id"]').val(),
                    buyer_party: $('#buyer_party').val(),
                    transport: $('#transport').val(),
                    credit_days: $('#credit_days').val(),
                    discount: $("#discount").val(),
                    totalNetKg: $("#totalNetKg").text(),
                    totalAmount: $("#totalAmount").text(),
                    discountAmount: $("#discountAmount").text(),
                    finalAmount: $("#finalAmount").text(),
                    rows: rows
                };

                $.ajax({
                    type: 'PUT',
                    url: "{{ route('order.update', $edit_id) }}",
                    data: dataToSend,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });;
                            window.location.href =
                                "{{ route('admin.order') }}";

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
                        handleAjaxError(xhr);
                    }
                });
            })

        });
    </script>
@endpush
