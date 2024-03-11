@extends('admin.masterlayout')
@section('page_title')
{{ config('app.name') }} - Create New Invoice
@endsection
@section('title')
    Create Invoice
@endsection

@section('form-content')
    <form id="invoiceform" name="invoiceform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-4">
                    <span class=" float-right mb-3 mr-2">
                        <button type="button" data-toggle="modal" data-target="#exampleModalScrollable"
                            class="btn btn-sm bg-primary "><i class="ri-add-fill"><span class="pl-1">Add
                                    customer</span></i>
                        </button>
                    </span>
                    <label for="customer">Customer</label>
                    <select class="form-control" id="customer" name="customer_id" required>
                        <option selected="" disabled=""> Select Customer</option>
                    </select>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-4">
                    <input type="hidden" name="country_id" id="country" class="form-control" value="" />
                    <input type="hidden" name="user_id" id="created_by" class="form-control"
                        value="{{ $user_id }}" />
                    <input type="hidden" name="company_id" id="company_id" class="form-control"
                        value="{{ $company_id }}" />
                    <label for="payment">Payment Mode</label>
                    <select class="form-control" id="payment" name="payment_mode">
                        <option selected="" disabled="">Select your Payment Way</option>
                        <option value="Online Payment">Online Payment</option>
                        <option value="Cash">Cash</option>
                        <option value="Net-Banking">Net-Banking</option>
                        <option value="Check">Check</option>
                    </select>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-4">
                    <label for="type">Tax-Type</label>
                    <select class="form-control" id="type" name="tax_type">
                        <option selected="" disabled="">Select Type</option>
                        <option value="1">GST</option>
                        <option value="2">Without GST</option>
                    </select>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-4">
                    <label for="currency">Currency</label>
                    <select class="form-control" id="currency" name="currency_id">
                        <option selected="" disabled=""> Select Currency</option>
                    </select>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-4">
                    <label for="acc_details">Bank Account </label>
                    <select class="form-control" id="acc_details" name="acc_details">
                        <option selected="" disabled="">Select Account</option>
                    </select>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-4">
                    <label for="products">Add From Existing Items</label>
                    <select class="form-control iq-bg-success" id="products" name="products">
                        <option value="selected" selected="" disabled=""> Select Item</option>
                    </select>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div id="table" class="table-editable" style="overflow-x:auto">
            <span class="add_div float-right mb-3 mr-2">
                <button type="button" class="btn btn-sm iq-bg-success"><i class="ri-add-fill"><span class="pl-1">Add
                            New Item</span></i>
                </button>
            </span>
            <table id="data" class="table table-bordered  table-striped text-center">
                <thead>
                    <tr id="columnname" style="text-transform: uppercase">

                       
                    </tr>
                </thead>
                <tbody  id="add_new_div">
                    
                </tbody>
                <tr class="text-right">
                    <th class="automaticcolspan">Sub total</th>
                    <td id=""><input type="number" name="total_amount" id="totalamount" readonly required>
                    </td>
                </tr>
                <tr id="gstline" class="text-right">
                    <th class="automaticcolspan">Total GST</th>
                    <td><input type="number" name="gst" id="gst" readonly required></td>
                </tr>
                <tr id="grandtotalline" class="text-right">
                    <th class="automaticcolspan font-weight-bold">Total</th>
                    <td><input type="number" name="grandtotal" id="grandtotal" readonly required></td>
                </tr>
            </table>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
            </div>
        </div>


        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
            <div id="loader" class="loader"></div>
            <button id="resetbtn" type="reset" class="btn iq-bg-danger">Reset</button>
        </div>

    </form>


    {{-- for add new customer direct from invoiceform --}}
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="details" width='100%' class="table table-bordered table-responsive-md table-striped">
                        <form id="customerform">
                            @csrf
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <input type="hidden" name="token" class="form-control"
                                            value="{{ session('api_token') }}" placeholder="token" required />
                                        <input type="hidden" value="{{ $user_id }}" class="form-control"
                                            name="user_id">
                                        <input type="hidden" value="{{ $company_id }}" class="form-control"
                                            name="company_id">
                                        <label for="firstname">FirstName</label>
                                        <input type="text" class="form-control" id="firstname" name='firstname'
                                            placeholder="First name" required>
                                        <span class="modal-error-msg" id="modal-error-firstname"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="lastname">LastName</label>
                                        <input type="text" class="form-control" id="lastname" name='lastname'
                                            placeholder="Last name" required>
                                        <span class="modal-error-msg" id="modal-error-lastname"
                                            style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" class="form-control" id="company_name" name='company_name'
                                            id="" placeholder="Company name" required>
                                        <span class="modal-error-msg" id="modal-error-company_name"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="gst_number">GST Number</label>
                                        <input type="text" class="form-control" name='gst_number' id="gst_number"
                                            placeholder="GST Number">
                                        <span class="modal-error-msg" id="modal-error-gst_number"
                                            style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <label for="modal_email">Email</label>
                                        <input type="email" class="form-control" name="email" id="modal_email"
                                            placeholder="Enter Email" required />
                                        <span class="modal-error-msg" id="modal-error-email" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="modal_exampleInputphone">Contact Number</label>
                                        <input type="tel" class="form-control" name='contact_number'
                                            id="modal_exampleInputphone" placeholder="0123456789" required>
                                        <span class="modal-error-msg" id="modal-error-contact_number"
                                            style="color: red"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <label for="modal_country">Select Country</label>
                                            <select class="form-control" name='country' id="modal_country" required>
                                                <option selected="" disabled="">Select your Country</option>
                                            </select>
                                            <span class="modal-error-msg" id="modal-error-country"
                                                style="color: red"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="modal_state">Select State</label>
                                            <select class="form-control" name='state' id="modal_state" required>
                                                <option selected="" disabled="">Select your State</option>
                                            </select>
                                            <span class="modal-error-msg" id="modal-error-state"
                                                style="color: red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <label for="modal_city">Select City</label>
                                            <select class="form-control" name='city' id="modal_city" required>
                                                <option selected="" disabled="">Select your City</option>
                                            </select>
                                            <span class="modal-error-msg" id="modal-error-city"
                                                style="color: red"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="modal_pincode">Pincode</label>
                                            <input type="text" id="modal_pincode" name='pincode' class="form-control"
                                                placeholder="Pin Code">
                                            <span class="modal-error-msg" id="modal-error-pincode"
                                                style="color: red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <label for="modal_address">Address</label>
                                            <textarea class="form-control" required name='address' id="modal_address" rows="2"></textarea>
                                            <span class="modal-error-msg" id="modal-error-address"
                                                style="color: red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="button-container">
                                    <button type="submit" class="btn btn-primary" id="modal_submitBtn">Submit</button>
                                    <div id="modal_loader" class="loader"></div>
                                    <button id="modal_resetbtn" type="reset" class="btn iq-bg-danger">Reset</button>
                                </div>
                        </form>
                    </table>
                </div>
                <div class="modal-footer">
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

            let allColumnData = [];
            let allColumnNames = [];
            let formula = [];

            // fetch invoice formula for calculation 
            $.ajax({
                    type: 'GET',
                    url: '{{ route('invoiceformula.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.invoiceformula != '') {
                            formula = response.invoiceformula;
                        }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            
            // fetch users own columnname and set it into table 
            $.ajax({
                type: 'GET',
                url: '{{ route('invoice.columnname') }}',
                data: {
                    user_id: {{ session()->get('user_id') }},
                    company_id: {{ session()->get('company_id') }},
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                     allColumnData = response.columnname;
                    if (response.status == 200 && response.columnname != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.columnname, function(key, value) {
                            $.each(value, function(innerKey, innerValue) {
                                if (innerKey === 'column_name') {
                                    allColumnNames.push(innerValue);
                                }
                            });
                        });
                        
                        $('#columnname').prepend(
                            `${allColumnNames.map(columnName => `<td>${columnName}</td>`).join('')} 
                                <th>Amount</th>
                                <th>Sort</th>
                                <th>Remove</th>
                            `
                            );


                        const targetRow = $('#add_new_div');

                        // Append input elements dynamically to the target row
                        targetRow.append(`
                             <tr class="iteam_row_1">
                                 ${allColumnData.map(columnData => {
                                    var columnName = columnData.column_name.replace(/\s+/g, '_');
                                            var inputcontent = null ;
                                            if (columnData.column_type === 'time') {
                                                return `<td class='invoicesubmit'><input type="time" name="${columnName}_1" id="${columnName}_1" class="form-control iteam_${columnName} ${(columnData.is_hide === 1)?'border-danger':''}"></td>`;
                                            } else if (columnData.column_type === 'number' || columnData.column_type === 'percentage' ||columnData.column_type === 'decimal') {
                                                return `<td class='invoicesubmit'><input type="number" name="${columnName}_1" id="${columnName}_1" data-id="1" class="form-control iteam_${columnName} counttotal calculation ${(columnData.is_hide === 1)?'border-danger':''}" value=1 min=0></td>`;
                                            } else if (columnData.column_type === 'longtext') {
                                                return `<td class='invoicesubmit'><textarea name="${columnName}_1" id="${columnName}_1" class="form-control iteam_${columnName} ${(columnData.is_hide === 1)?'border-danger':''}" rows="1"></textarea></td>`;
                                            } else {
                                                return `<td class='invoicesubmit'><input type="text" name="${columnName}_1" id="${columnName}_1" class="form-control iteam_${columnName} ${(columnData.is_hide === 1)?'border-danger':''}" placeholder="${columnData.column_name}"></td>`;
                                            }
                                        }).join('')
                                 }
                                <td><input type="number" data-id="1" class="form-control iteam_Amount changeprice calculation" id="Amount_1"
                                        placeholder="Amount" name='Amount_1' min=0 required>
                                </td>
                                <td>
                                    <span class="table-up"><a href="#!" class="indigo-text"><i class="fa fa-long-arrow-up"
                                                aria-hidden="true"></i></a></span>
                                    <span class="table-down"><a href="#!" class="indigo-text"><i
                                                class="fa fa-long-arrow-down" aria-hidden="true"></i></a></span>
                                </td>
                                <td>
                                    <span class="remove-row" data-id="1"><button data-id="1" type="button"
                                            class="btn iq-bg-danger btn-rounded btn-sm my-0"><i
                                                class="ri-delete-bin-2-line"></i></button></span>
                                </td>
                             </tr>
                        `);

                    } else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                    }else {
                        $('#columnname').append(` <th>Name</th>
                        <th>Description</th>
                        <th>Quantity</th>`);
                    }
                       $('.automaticcolspan').attr('colspan',allColumnNames.length);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
          

            // account data fetch and set account detials dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('invoice.bankacc') }}',
                data: {
                    user_id: {{ session()->get('user_id') }},
                    company_id: {{ session()->get('company_id') }},
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.bank != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.bank, function(key, value) {
                            $('#acc_details').append(
                                `<option ${response.bank.length === 1 ? 'selected' : ''} value='${value.id}'>${value.account_no} - ${value.branch_name}</option>`
                            );
                        });
                    }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                    } else {
                        $('#acc_details').append(
                            `<option disabled '>No Data found </option>`);
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

            // currency data fetch and set currensy dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('invoice.currency') }}',
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id : " {{session()->get('company_id')}} ",
                    user_id : " {{session()->get('user_id')}} ",
                },
                success: function(response) {
                    if (response.status == 200 && response.currency != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.currency, function(key, value) {
                            $('#currency').append(
                                `<option value='${value.id}'>${value.country} -${value.currency} - ${value.code} - ${value.symbol} </option>`
                            );
                        });
                    } else {
                        $('#currency').append(
                            `<option disabled '>No Data found </option>`);
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

            // product data fetch and set product dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('product.index') }}',
                data: {
                    user_id: {{ session()->get('user_id') }},
                    company_id: {{ session()->get('company_id') }},
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.product != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.product, function(key, value) {
                            $('#products').append(
                                `<option value='${value.id}'>${value.name} </option>`);
                        });
                    } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                    } else {
                        $('#products').append(
                            `<option disabled '>No Data found </option>`);
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
            loaderhide();
            // customer data fetch and set customer dropdown
            function customers(customerid = 0) {
                loadershow();
                $('#customer').html(`<option selected="" value=0 disabled=""> Select Customer</option>`);
                $.ajax({
                    type: 'GET',
                    url: '{{ route('customer.invoicecustomer') }}',
                    data: {
                        company_id: {{ session()->get('company_id') }},
                        user_id: {{ session()->get('user_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.customer != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.customer, function(key, value) {
                                $('#customer').append(
                                    `<option  data-gstno='${value.gst_no}' value='${value.id}'>${value.firstname}  ${value.lastname}</option>`
                                )
                            });
                            $('#customer').val(customerid);
                            loaderhide();
                        }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            $('#customer').append(`<option disabled '>No Data found </option>`);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }

            customers();


            // fetch contry id from selected customer and set input value for hidden file
            $('#customer').on('change', function() {
                loadershow();
                var selectedOption = $(this).find('option:selected');
                var id = $(this).val();
                var gstno = selectedOption.data('gstno');
                if (gstno != null) {
                    $('#type').val(1);
                    $('#gstline').show();
                    dynamiccalculaton();
                } else {
                    $('#type').val(2);
                    $('#gstline').hide();
                    dynamiccalculaton();
                }
                $.ajax({
                    type: 'GET',
                    url: "/api/customer/search/" + id,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id')}}",
                        user_id: " {{ session()->get('user_id')}}"
                    },
                    success: function(response) {
                        // You can update your HTML with the data here if needed
                        if (response.status == 200 && response.customers != '') {
                            var countryid = response.customer.country_id
                            $('#country').val(countryid);
                            if (countryid == 1) {
                                $('#currency').val(53);
                            } else {
                                $('#currency').val('');
                            }
                        }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                        }
                        loaderhide();
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });

            // call function to append row in table  on click add new button 
            var addname = 1; // for use to this variable is give to dynamic name and id to input type
            $('.add_div').on('click', function() {
                addname++;
                adddiv();
            });

            // function for add new row in table 
            function adddiv() {
                $('#add_new_div').append(`
                    <tr class="iteam_row_${addname}">
                        ${allColumnData.map(columnData => {
                            var columnName = columnData.column_name.replace(/\s+/g, '_');
                                var inputcontent = null ;
                                if (columnData.column_type === 'time') {
                                    return `<td class='invoicesubmit'><input type="time" name="${columnName}_${addname}" id='${columnName}_${addname}' class="form-control iteam_${columnName} ${(columnData.is_hide === 1)?'border-danger':''}"></td>`;
                                } else if (columnData.column_type === 'number' || columnData.column_type === 'percentage' ||columnData.column_type === 'decimal') {
                                    return `<td class='invoicesubmit'><input type="number" name="${columnName}_${addname}" id='${columnName}_${addname}' data-id = ${addname} class="form-control iteam_${columnName} counttotal calculation ${(columnData.is_hide === 1)?'border-danger':''}" value=1 min=0></td>`;
                                } else if (columnData.column_type === 'longtext') {
                                    return `<td class='invoicesubmit'><textarea name="${columnName}_${addname}" id='${columnName}_${addname}' class="form-control iteam_${columnName} ${(columnData.is_hide === 1)?'border-danger':''}" rows="1"></textarea></td>`;
                                } else {
                                    return `<td class='invoicesubmit'><input type="text" name="${columnName}_${addname}" id='${columnName}_${addname}' class="form-control iteam_${columnName} ${(columnData.is_hide === 1)?'border-danger':''}" placeholder="${columnData.column_name}"></td>`;
                                }
                            }).join('')
                        }
                        <td>
                            <input type="number"  data-id = ${addname} id="Amount_${addname}" min=0 name="Amount_${addname}" class="form-control iteam_Amount changeprice calculation" placeholder="Amount" required>
                        </td>   
                        <td>
                            <span class="table-up">
                                <a href="#!" class="indigo-text">
                                    <i class="fa fa-long-arrow-up" aria-hidden="true"></i>
                                </a>
                            </span>
                            <span class="table-down">
                                <a href="#!" class="indigo-text">
                                    <i class="fa fa-long-arrow-down" aria-hidden="true"></i>
                                </a>
                            </span>
                        </td>
                        <td>
                            <span class='remove-row'>
                                <button type="button" class="btn iq-bg-danger btn-rounded btn-sm my-0">
                                <i class="ri-delete-bin-2-line"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                `);
            }


            // delete row 
            $(document).on('click', '.remove-row', function() {
                if (confirm('Are you really want to delete this ?')) {
                        $(this).parents("tr").detach();
                        dynamiccalculaton();
                }
            });

            // call function for gst or without gst counting
            $('#type').on('change', function() {
                if ($(this).val() == 2) {
                    $('#gstline').hide();
                    $('#grandtotal').val($('#totalamount').val());
                    $('#gst').val(0);
                } else {
                    $('#gstline').show();
                    dynamiccalculaton();
                }
            })

            // submit form 
            $('#invoiceform').submit(function(event) {
                iteam_data = new Array();
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');

                $('tbody#add_new_div tr').each(function(index, row) {
                    var rowData = {};

                    // Extract the row number from the class of the current row element
                    var rowNumber = $(row).attr('class').match(/\d+/)[0];

                    // Iterate over each column name
                    $.each(allColumnNames, function (key, columnName) {
                        var columnNameWithUnderscores = columnName.replace(/\s+/g, '_');
                        // Find the input within the current row by using the row number
                        var inputValue = $(row).find(`#${columnNameWithUnderscores}_${rowNumber}`).val();
                        rowData[columnNameWithUnderscores] = inputValue;
                    });
                      rowData['amount'] = $(row).find('#Amount_'+ rowNumber).val();
                    iteam_data.push(rowData);
                });
                var country = $('#country').val();
                var created = $('#created_by').val();
                var company_id = $('#company_id').val();
                var payment_type = $('#payment').val();
                var account = $('#acc_details').val();
                var currency = $('#currency').val();
                var customer = $('#customer').val();
                var total = $('#totalamount').val();
                var gst = $('#gst').val();
                var grandtotal = $('#grandtotal').val();
                var notes = $('#notes').val();
                var data = {
                    country_id: country,
                    user_id: created,
                    company_id: company_id,
                    payment_mode: payment_type,
                    acc_details: account,
                    currency_id: currency,
                    customer_id: customer,
                    total_amount: total,
                    gst: gst,
                    grandtotal : grandtotal,
                    notes: notes
                };
                
                
                $.ajax({
                    type: 'POST',
                    url: "{{ route('invoice.store') }}",
                    data: {
                        data,
                        iteam_data,
                        token: "{{ session()->get('api_token') }}",
                        company_id : " {{ session()->get('company_id')}}",
                        user_id : " {{ session()->get('user_id')}}",
                    },
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.invoice') }}";

                        }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(response.message);
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            });
           


            // dynamic calculation 
            function dynamiccalculaton(targetdata){
                var editid = $(targetdata).data('id');
                var rowData = {};
                $.each(allColumnNames, function (key, value) {
                    $(`table tr.iteam_row_${editid} td`).find(`input[type="number"]#${value}_${editid}`).each(function () {
                        rowData[value] = $(this).val();
                    });
                });
                // rowData['Amount'] = $('#Amount_'+editid).val();
                var iteam_data = new Array();
                iteam_data.push(rowData);
                // console.log(iteam_data);
                 

                function performCalculation(operation, value1, value2) {
                        switch (operation) {
                            case '*':
                                return value1 * value2;
                             break;
                            case '/':
                                return value1 / value2;
                              break;
                            case '+':
                                return value1 + value2;
                             break;
                            case '-':
                                return value1 - value2;
                              break;
                            default:
                                return 0;
                        }
                }
                   
                    var results = {};

                        formula.forEach(function (formula) {
                            var value1 = parseFloat(iteam_data[0][formula.first_column]) || 0;
                            var value2 = parseFloat(iteam_data[0][formula.second_column]) || 0;
                             outputvalue =  performCalculation(formula.operation, value1, value2)
                            iteam_data[0][formula.output_column] = outputvalue
                            results[formula.output_column] = outputvalue;
                            $(`#${formula.output_column}_${editid}`).val(outputvalue);
                        });
                         var total = 0;
                         $('input.changeprice').each(function(){
                              total += parseFloat($(this).val());
                         });
                         $('#totalamount').val(total);
                         if($('#type').val()==1){
                             var gst = (total * 18) / 100;
                             $('#gst').val(Math.round(gst));
                             var grandtotal = total + gst ;
                             $('#grandtotal').val(Math.round(grandtotal));
                         }else{
                            $('#grandtotal').val(Math.round(total));
                         }
                         
            }
                $(document).on('change','.calculation',function(){
                
                        dynamiccalculaton(this);
                });


            // for add new customer 

            // set country data in country dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('country.index') }}',
                data: {
                    token: "{{ session()->get('api_token') }}",
                },
                success: function(response) {

                    if (response.status == 200 && response.country != '') {
                        $.each(response.country, function(key, value) {
                            // You can update your HTML with the data here if needed
                            $('#modal_country').append(
                                `<option value='${value.id}'> ${value.country_name}</option>`
                            )
                        });
                    } else {
                        $('#modal_country').append(`<option disabled> No Data Found</option>`)
                    }


                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

            // set state data when country select
            $('#modal_country').on('change', function() {
                loadershow();
                var country_id = $(this).val();
                $('#modal_state').html(`<option selected="" disabled="">Select your State</option>`);
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country_id,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#modal_state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#modal_state').append(
                            `<option disabled> No Data Found</option>`);
                        }


                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });

            // set city data when state select
            $('#modal_state').on('change', function() {
                loadershow();
                $('#modal_city').html(`<option selected="" disabled="">Select your City</option>`);
                var state_id = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state_id,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                    },
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#modal_city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                            loaderhide();
                        } else {
                            $('#modal_city').append(`<option disabled> No Data Found</option>`);
                            loaderhide();
                        }


                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });


            // submit new customer  form
            $('#customerform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.modal-error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customer.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#customerform')[0].reset();
                            $('#exampleModalScrollable').modal('hide');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            customers(response.customerid);
                            loaderhide();
                            toastr.success(response.message);

                        }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            toastr.error(response.message);
                            loaderhide();
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#modal-error-' + key).text(value[0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            });


        });
    </script>
@endpush
