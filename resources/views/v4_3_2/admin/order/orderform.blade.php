@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Add New Order
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
    New Order
@endsection


@section('form-content')
    <form id="orderform">
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
                <div id="table" class="table-editable w-100 " style="overflow-x:auto">
                    <table class="table table-bordered table-striped text-center" id="purchaseTable">
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

                            <tr id="addnewbtnrow">
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
    <div class="modal fade" id="partyModal" tabindex="-1" role="dialog" aria-labelledby="partyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="partyModalLabel">
                        Add New Party
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="partyform">
                        @csrf
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" placeholder="token" required />
                                    <input type="hidden" value="{{ session('user_id') }}" class="form-control"
                                        name="user_id">
                                    <input type="hidden" value="{{ session('company_id') }}" class="form-control"
                                        name="company_id">
                                    <input type="hidden" class="form-control" name="party_type" id="party_type">
                                    <label for="name">Name</label><span style="color:red;">*</span>
                                    <input type="text" id="name" class="form-control" name='name'
                                        placeholder=" Name">
                                    <span class="error-msg" id="error-name" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control requiredinput" name="email"
                                        id="email" placeholder="Enter Email">
                                    <span class="error-msg" id="error-email" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="contact_person_name">Contact Person Name</label>
                                    <input type="text" class="form-control requiredinput" name="contact_person_name"
                                        id="contact_person_name" placeholder="Enter Contact Person Name">
                                    <span class="error-msg" id="error-contact_person_name" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="mobile_1">Mobile 1</label>
                                    <input type="tel" class="form-control requiredinput" name='mobile_1'
                                        id="mobile_1" placeholder="0123456789">
                                    <span class="error-msg" id="error-mobile_1" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="mobile_2">Mobile 2</label>
                                    <input type="tel" class="form-control requiredinput" name='mobile_2'
                                        id="mobile_2" placeholder="0123456789">
                                    <span class="error-msg" id="error-mobile_2" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="country">Select Country</label>
                                    <select class="form-control requiredinput" name='country' id="country">
                                        <option selected="" disabled="">Select your Country</option>
                                    </select>
                                    <span class="error-msg" id="error-country" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="state">Select State</label>
                                    <select class="form-control requiredinput" name='state' id="state">
                                        <option selected="" disabled="">Select your State</option>
                                    </select>
                                    <span class="error-msg" id="error-state" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="city">Select City</label>
                                    <select class="form-control requiredinput" name='city' id="city">
                                        <option selected="" disabled="">Select your City</option>
                                    </select>
                                    <span class="error-msg" id="error-city" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" id="pincode" name='pincode'
                                        class="form-control requiredinput" placeholder="Pin Code">
                                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="address">Address</label>
                                    <textarea class="form-control requiredinput" name='address' id="address" rows="2"
                                        placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                    <span class="error-msg" id="error-address" style="color: red"></span>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label for="gst_no">GST Number</label>
                                    <input type="text" id="gst_no" class="form-control" name='gst_no'
                                        id="" placeholder="GST Number">
                                    <span class="error-msg" id="error-gst_no" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="pan">PAN Number</label>
                                    <input type="text" id="pan" class="form-control" name='pan'
                                        id="" placeholder="PAN Number">
                                    <span class="error-msg" id="error-pan" style="color: red"></span>
                                </div>

                                <div class="col-sm-12">
                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Cancel" id="modalcancelbtn"
                                        class="btn btn-secondary float-right">Cancel</button>
                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Reset party Details"
                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Save party Details"
                                        class="btn btn-primary float-right my-0">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
            let gardens;
            let grades;
            let partyType;
            let row = -1;
            // show country data in dropdown and set defautl value according to logged in user
            function loadcountry() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('country.index') }}",
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {

                        if (response.status == 200 && response.country != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.country, function(key, value) {
                                $('#country').append(
                                    `<option value='${value.id}'> ${value.country_name}</option>`
                                )
                            });
                            country_id = "{{ session('user')['country_id'] }}";
                            // console.log("this country_id", country_id);
                            $('#country').val(country_id);
                            // console.log($('#country').val());
                            loadstate();
                        } else {
                            $('#country').append(`<option> No Data Found</option>`);
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

            // load state in dropdown when country change
            $('#country').on('change', function() {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id);
            });

            // load state in dropdown and set defautl value according to logged in user if not manualy select
            function loadstate(id = 0) {
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                var url = "{{ route('state.search', '__id__') }}".replace('__id__', id);
                if (id == 0) {
                    url = "{{ route('state.search', session('user')['country_id']) }}";
                }
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                            if (id == 0) {
                                state_id = "{{ session('user')['state_id'] }}";
                                $('#state').val(state_id);
                                loadcity();
                            }
                        } else {
                            $('#state').append(`<option> No Data Found</option>`);
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

            // load city in dropdown when state select/change
            $('#state').on('change', function() {
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id);
            });

            // load city in dropdown and set defautl value according to logged in user if not manualy select
            function loadcity(id = 0) {
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                url = "{{ route('city.search', '__id__') }}".replace('__id__', id);
                if (id == 0) {
                    url = "{{ route('city.search', session('user')['state_id']) }}";
                }
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                            if (id == 0) {
                                $('#city').val("{{ session('user')['city_id'] }}");
                            }
                        } else {
                            $('#city').append(`<option> No Data Found</option>`);
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


            $(document).on('change', '#buyer_party, #transport', function() {
                const selectedOption = $(this).find(':selected');

                if (selectedOption.val() === 'add_new') {
                    loadcountry();
                    partyType = selectedOption.data('type'); // Buyer / Transport
                    if (partyType == "Buyer") {
                        $("#buyer_party").val('');
                    } else {
                        $("#transport").val('');
                    }
                    $("#partyModalLabel").text(`Add New - ${partyType}`)
                    $("#party_type").val(partyType);
                    $('#partyModal').modal('show'); // if using modal

                    $(this).val('');
                }
            });

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

            async function buyer_party(party_id = null) {
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
                    partySelect.append(
                        '<option value="add_new" data-type = "Buyer"> Add New Buyer Party</option>');

                    if (response.data?.length) {
                        response.data.forEach(party => {
                            partySelect.append(
                                `<option value="${party.id}">${party.name}</option>`
                            );
                        });
                    } else {
                        partySelect.append('<option disabled>No party Found</option>');
                    }
                    if (party_id) {
                        $("#buyer_party").val(party_id);
                    }
                    $('#buyer_party').select2({
                        placeholder: "Select Buyer",
                        width: '100%',
                        search: true,
                    });
                } catch (xhr) {
                    handleAjaxError(xhr);
                } finally {
                    loaderhide();
                }
            }

            async function transport(party_id = null) {
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
                    transportSelect.append(
                        '<option value="add_new" data-type="Transport"> Add New Transport</option>');

                    if (response.data?.length) {
                        response.data.forEach(item => {
                            transportSelect.append(
                                `<option value="${item.id}">${item.name}</option>`
                            );
                        });
                    } else {
                        transportSelect.append('<option disabled>No transport Found</option>');
                    }
                    if (party_id) {
                        $("#transport").val(party_id);
                    }
                    $('#transport').select2({
                        placeholder: "Select Transport",
                        width: '100%',
                        search: true,
                    });
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

                loaderhide();

                addNewRow();
            }

            initPurchaseForm();


            function addNewRow() {
                row++;
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
                                ${grades}
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

            // redirect on order list page on click cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.order') }}";
            });
            $("#modalcancelbtn").on('click', function() {
                $('#partyform')[0].reset();
                $('#partyModal').modal('hide');
            });
            $('#partyform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let formdata = $(this).serialize();
                console.log(partyType);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('party.store') }}",
                    data: formdata,
                    success: function(response) {
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            if (partyType === "Buyer") {
                                buyer_party(response.party_id);
                            } else {
                                transport(response.party_id);
                            }
                            $('#partyform')[0].reset();
                            $('#partyModal').modal('hide');

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
            $('#orderform').submit(function(event) {
                event.preventDefault();
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
                    type: 'POST',
                    url: "{{ route('order.store') }}",
                    data: dataToSend,
                    success: function(response) {
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location.href = "{{ route('admin.order') }}";
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        console.log(xhr.responseText);
                        handleAjaxError(xhr); // only for real server errors
                    }


                });
            });
        });
    </script>
@endpush
