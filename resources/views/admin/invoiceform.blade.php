@extends('admin.masterlayout')
@section('page_title')
    Create New Invoice
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
                    <input type="hidden" name="created_by" id="created_by" class="form-control"
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
        <div id="table" class="table-editable">
            <span class="add_div float-right mb-3 mr-2">
                <button type="button" class="btn btn-sm iq-bg-success"><i class="ri-add-fill"><span class="pl-1">Add
                            New Item</span></i>
                </button>
            </span>
            <table id="add_new_div" class="table table-bordered table-responsive-md table-striped text-center">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Sort</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="iteam_row">

                        <td>
                            <input type="hidden" name="product_id_1" id="product_id_1" class="iteam_id" value="1" required>
                            <input type="text" class="form-control iteam_name" placeholder="Name" name="product_1"
                                id="product_1" required>
                        </td>
                        <td>
                            <textarea class="form-control iteam_description" rows="1" id="item_description_1" name="item_description_1"
                                required></textarea>
                        </td>
                        <td><input type="number" class="form-control iteam_quantity counttotal" placeholder="quantity"
                                id="quantity_1" value="1" name='quantity_1' required></td>
                        <td><input type="number" class="form-control iteam_price changeprice" id="price_1"
                                placeholder="Amount" name='price_1' required>
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
                </tbody>
                <tr>
                    <th colspan="3">total amount</th>
                    <td id=""><input type="number" name="total_amount" id="totalamount" readonly required>
                    </td>
                </tr>
                <tr id="gstline">
                    <th colspan="3">GST</th>
                    <td><input type="number" name="gst" id="gst" readonly required></td>
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
                                            name="created_by">
                                        <input type="hidden" value="{{ $company_id }}" class="form-control"
                                            name="company_id">
                                        <label for="firstname">FirstName</label>
                                        <input type="text" class="form-control" id="firstname" name='firstname'
                                            placeholder="First name" required>
                                        <span class="modal-error-msg" id="modal-error-firstname" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="lastname">LastName</label>
                                        <input type="text" class="form-control" id="lastname" name='lastname'
                                            placeholder="Last name" required>
                                        <span class="modal-error-msg" id="modal-error-lastname" style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" class="form-control" id="company_name" name='company_name' id=""
                                            placeholder="Company name" required>
                                        <span class="modal-error-msg" id="modal-error-company_name" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="gst_number">GST Number</label>
                                        <input type="text" class="form-control" name='gst_number' id="gst_number"
                                            placeholder="GST Number">
                                        <span class="modal-error-msg" id="modal-error-gst_number" style="color: red"></span>
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
                                        <span class="modal-error-msg" id="modal-error-contact_number" style="color: red"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <label for="modal_country">Select Country</label>
                                            <select class="form-control" name='country' id="modal_country" required>
                                                <option selected="" disabled="">Select your Country</option>
                                            </select>
                                            <span class="modal-error-msg" id="modal-error-country" style="color: red"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="modal_state">Select State</label>
                                            <select class="form-control" name='state' id="modal_state" required>
                                                <option selected="" disabled="">Select your State</option>
                                            </select>
                                            <span class="modal-error-msg" id="modal-error-state" style="color: red"></span>
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
                                            <span class="modal-error-msg" id="modal-error-city" style="color: red"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="modal_pincode">Pincode</label>
                                            <input type="text" id="modal_pincode" name='pincode' class="form-control"
                                                placeholder="Pin Code">
                                            <span class="modal-error-msg" id="modal-error-pincode" style="color: red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-12">
                                            <label for="modal_address">Address</label>
                                            <textarea class="form-control" required name='address' id="modal_address" rows="2"></textarea>
                                            <span class="modal-error-msg" id="modal-error-address" style="color: red"></span>
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
            // account data fetch and set account detials dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('invoice.bankacc') }}',
                data: {
                    user_id: {{ session()->get('company_id') }},
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
                    } else {
                        $('#acc_details').append(
                            `<option disabled '>No Data found </option>`);
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

            // currency data fetch and set currendy dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('invoice.currency') }}',
                data: {
                    token: "{{ session()->get('api_token') }}"
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
                } else {
                    $('#type').val(2);
                    $('#gstline').hide();


                }
                $.ajax({
                    type: 'GET',
                    url: "/api/customer/search/" + id,
                    data: {
                        token: "{{ session()->get('api_token') }}"
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
                $('#add_new_div').append(`<tr class="iteam_row">
                                        <td>
                                          <input type="hidden" name="product_id_${addname}" id="product_id_${addname}" class="iteam_id" value="${addname}">
                                          <input type="text" name="product_${addname}" id="product_${addname}" class="form-control iteam_name" placeholder="Name" required>
                                         </td>
                                      <td><textarea name="item_description_${addname}" id="item_description_${addname}" class="form-control iteam_description" id="notes" rows="1" required></textarea></td>
                                      <td><input name="quantity_${addname}" id="quantity_${addname}" type="number"  class="form-control iteam_quantity counttotal" placeholder="Quantity" value='1' required></td>
                                      <td><input type="number" id="price_${addname}" name="price_${addname}" class="form-control iteam_price changeprice" placeholder="Amount" required></td>   
                                      <td>
                                        <span class="table-up"><a href="#!" class="indigo-text"><i class="fa fa-long-arrow-up" aria-hidden="true"></i></a></span>
                                        <span class="table-down"><a href="#!" class="indigo-text"><i class="fa fa-long-arrow-down" aria-hidden="true"></i></a></span>
                                      </td>
                                      <td>
                                        <span class='remove-row'><button type="button" class="btn iq-bg-danger btn-rounded btn-sm my-0"><i class="ri-delete-bin-2-line"></i></button></span>
                                      </td>
                                     </tr>`);                    
            }

            // function for fill value on select product 
            function fillvalue(product) {
                $.ajax({
                    type: 'GET',
                    url: '/api/product/search/' + product,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                         
                            var poldname =  $(`#product_${addname}`).val(); 
                            var polddesc =  $(`#item_description_${addname}`).val(); 
                            var poldamount =  $(`#price_${addname}`).val(); 
                           if(poldname != ''|| polddesc != ''|| poldamount != '' ){
                            addname++;
                            adddiv();
                            $(`#product_id_${addname}`).val(response.product.id);
                            $(`#product_${addname}`).val(response.product.name);
                            $(`#item_description_${addname}`).val(response.product
                                .description);
                            $(`#price_${addname}`).val(parseFloat(response.product
                                .price_per_unit));
                            total();
                            $('#products').val('selected');
                           }else{
                            $(`#product_id_${addname}`).val(response.product.id);
                            $(`#product_${addname}`).val(response.product.name);
                            $(`#item_description_${addname}`).val(response.product
                                .description);
                            $(`#price_${addname}`).val(parseFloat(response.product
                                .price_per_unit));
                            total();
                            $('#products').val('selected');
                           }
                        }

                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }


            // call function on products select and  filldata 
            var changeid = 0; // this variable is use for check conditon when products select
            $('#products').on('change', function() {
                loadershow();
                var product_id = $(this).val();
                if (changeid < addname) {
                    fillvalue(product_id);
                    changeid++;
                } else {
                    addname++;
                    adddiv();
                    fillvalue(product_id);
                }
               loaderhide();
            });

            // total amount counting with quantity functions start
            $(document).on('keyup', '.counttotal', function() {
                total();
            });
            $(document).on('change', '.counttotal', function() {
                total();
            });
            $(document).on('keyup', '.changeprice', function() {
                total();
            });
            $(document).on('change', '.changeprice', function() {
                total();
            });
            // total amount counting with quantity functions end



            // delete row 
            $(document).on('click', '.remove-row', function() {
                var $deleteid = $(this).data('id');
               if(confirm('Are you really want to delete this ?')) {
                if ($deleteid == 1) {
                    $(this).parents("tr").detach();
                    total();
                } else {
                    // changeid--;
                    // addname--;
                    $(this).parents("tr").detach();
                    total();
                }
               }
            });

            // call function for gst or without gst counting
            $('#type').on('change', function() {
                if ($(this).val() == 2) {
                    $('#gstline').hide();
                } else {
                    $('#gstline').show();
                }
                total();
            })

            //function for count gst and total of all products
            function total() {
                var totalamount = 0;

                for (var i = addname; i >= 1; i--) {
                    if (isNaN($(`#price_${i}`).val())) {
                        totalamount = totalamount + 0;
                    } else {

                        totalamount = totalamount + parseFloat(($(`#quantity_${i}`).val() * $(`#price_${i}`)
                            .val()));
                        // alert($(`#quantity_${i}`).val() * $(`#price_${i}`).val() );
                    }
                }

                if ($('#type').val() != 2) {
                    var gst = (totalamount * 18) / 100;
                    $('#gst').val(gst);
                } else {
                    $('#gst').val(0);

                }

                $('#totalamount').val(totalamount);
            }

            // submit form 
            var iteam_data = new Array();
            $('#invoiceform').submit(function(event) {
                event.preventDefault();
                loadershow();
              $('.error-msg').text('');

                var i = 0;
                $('table tr.iteam_row').each(function() {
                    iteam_data[i] = new Array();
                    iteam_data[i][0] = $(this).find('td').find('.iteam_id').val();
                    iteam_data[i][1] = $(this).find('td').find('.iteam_name').val();
                    iteam_data[i][2] = $(this).find('td').find('.iteam_description').val();
                    iteam_data[i][3] = $(this).find('td').find('.iteam_quantity').val();
                    iteam_data[i][4] = $(this).find('td').find('.iteam_price').val();
                    i++;
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
                var notes = $('#notes').val();
                var data = {
                    country_id: country,
                    created_by: created,
                    company_id: company_id,
                    payment_mode: payment_type,
                    acc_details: account,
                    currency_id: currency,
                    customer_id: customer,
                    total_amount: total,
                    gst: gst,
                    notes: notes
                };

                $.ajax({
                    type: 'POST',
                    url: "{{ route('invoice.store') }}",
                    data: {
                        data,
                        iteam_data,
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.invoice') }}";

                        }  else {
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


            // for add new customer
            // set country data in country dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('country.index') }}',
                data: {
                    token: "{{ session()->get('api_token') }}"
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
                        token: "{{ session()->get('api_token') }}"
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
                            $('#modal_state').append(`<option disabled> No Data Found</option>`);
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
                        token: "{{ session()->get('api_token') }}"
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
                            $('#exampleModalScrollable').toggle();
                            $('.modal-backdrop').toggle();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            customers(response.customerid);
                            loaderhide();
                            toastr.success(response.message);

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
