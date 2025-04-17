@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Purchase Order
@endsection

@section('style')
    <style>
        table,
        table th,
        table td {
            border-right: transparent !important;
            border-left: transparent !important;
        }

        /* For select2 dropdown, override the default select2 styles for disabled items */
        .select2-results__option[aria-disabled="true"] {
            color: red !important; 
        }

        table input.form-control {
            width: auto;
        }

        table textarea.form-control{
            width: auto;
        }
    </style>
@endsection

@section('title')
    Update Purchase Order
@endsection


@section('form-content')
    <form id="purchaseupdateform">
        @csrf
        <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}" placeholder="token" />
        <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id" placeholder="user_id" />
        <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id"
            placeholder="company_id" />
        <div class="row">
            <div class="col-12">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <h4><span id="purchaseordernumber"></span></h4>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label for="supplier">Supplier</label>
                                    <select class="form-control select2" id="supplier" name="supplier">
                                        <option value="add_supplier"> Add New Supplier </option>
                                    </select>
                                    <span class="error-msg" id="error-supplier" style="color: red"></span>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label for="payment">Payment Mode</label>
                                    <select class="form-control" id="payment" name="payment">
                                        <option selected="" disabled="">Select your Payment Way</option>
                                        <option value="Online Payment">Online Payment</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Check">Check</option>
                                    </select>
                                    <span class="error-msg" id="error-payment_mode" style="color: red"></span>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label for="currency">Currency</label>
                                    <select class="form-control" id="currency" name="currency">
                                        <option selected="" disabled=""> Select Currency</option>
                                    </select>
                                    <span class="error-msg" id="error-currency" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h6 class="mb-2">Shipment Details</h6>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-3 mb-3">
                                    <span class=" float-right mb-3 mr-2"></span>
                                    <label for="estimated_arrival">Estimated arrival</label>
                                    <input type="date" class="form-control" name='estimated_arrival'
                                        id="estimated_arrival" placeholder="Estimated arrival" />
                                    <span class="error-msg" id="error-estimated_arrival" style="color: red"></span>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <label for="shipping_carrier">Shipping carrier</label>
                                    <input type="text" class="form-control" name='shipping_carrier' id="shipping_carrier"
                                        placeholder="Shipping carrier" />
                                    <span class="error-msg" id="error-shipping_carrier" style="color: red"></span>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <label for="tracking_number">Tracking number</label>
                                    <input type="text" class="form-control" name='tracking_number' id="tracking_number"
                                        placeholder="Tracking number" />
                                    <span class="error-msg" id="error-tracking_number" style="color: red"></span>
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <label for="tracking_url">Tracking URL</label>
                                    <input type="text" class="form-control" name='tracking_url' id="tracking_url"
                                        placeholder="Tracking url" />
                                    <span class="error-msg" id="error-tracking_url" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h6 class="mb-3">Add Product <span style="color: red">*</span></h6>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12 mb-3">
                                    <select class="form-control select2" id="products" name="products[]" multiple>
                                    </select>
                                    <span class="error-msg" id="error-products" style="color: red"></span>
                                </div>
                                <div class="col-sm-12 table-responsive" id="datatable" style="display:none">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Products</th>
                                                <th>Supplier SKU</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Tax(%)</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="data">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h6 class="mb-2">Additional details</h6>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12 mb-3">
                                    <span class=" float-right mb-3 mr-2"></span>
                                    <label for="reference_number">Reference Number</label>
                                    <input type="text" class="form-control" name='reference_number'
                                        id="reference_number" placeholder="Reference number" />
                                    <span class="error-msg" id="error-reference_number" style="color: red"></span>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <label for="note_to_supplier">Note to supplier</label>
                                    <textarea type="text" class="form-control" name='note_to_supplier' id="note_to_supplier" rows="2"
                                        placeholder="Note to supplier"></textarea>
                                    <span class="error-msg" id="error-note_to_supplier" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h6 class="mb-2">Cost summary</h6>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12 d-flex justify-content-between">
                                    <label for="taxes">Taxes (included)</label>
                                    <input type="number" value="0" min="0" step="any"
                                        class="form-control w-75" name='taxes' id="taxes" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12 d-flex justify-content-between">
                                    <label for="sub_total">Sub Total</label>
                                    <input type="number" value="0" min="0" step="any"
                                        class="form-control w-75" name='sub_total' id="sub_total" readonly />
                                </div>
                            </div>
                        </div>
                        <p><span id="itemcount">0</span> <span id="itemcounttext">items</span></p>
                        <h6 class="mb-2">Cost adjustment</h6>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12 d-flex justify-content-between">
                                    <label for="shipping">Shipping</label>
                                    <input type="number" placeholder="0" min="0" step="any"
                                        class="form-control w-75 numberinput product_price_count" name='shipping'
                                        id="shipping" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12 d-flex justify-content-between">
                                    <label for="discount">Discount</label>
                                    <input type="number" placeholder="0" max="0" step="any"
                                        class="form-control w-75 product_price_count" name='discount' id="discount" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12 d-flex justify-content-between">
                                    <h5 for="total">Total</h5>
                                    <input type="number" value="0" min="0" step="any"
                                        class="form-control w-75" name='total' id="total" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-sm-12 d-flex flex-column flex-md-row justify-content-end">
                            <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                data-original-title="Update" class="btn btn-primary m-0 my-1 mx-1">Update</button>
                            <button id="cancelbtn" type="button" data-toggle="tooltip" data-placement="bottom"
                                data-original-title="Cancel" class="btn btn-secondary my-1 mx-1">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- for add new supplier direct --}}
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="details" width='100%'
                        class="table table-bordered table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped">
                        <form id="supplierform">
                            @csrf
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <input type="hidden" name="token" class="form-control"
                                            value="{{ session('api_token') }}" placeholder="token" required />
                                        <input type="hidden" value="{{ session('user_id') }}" class="form-control"
                                            name="user_id">
                                        <input type="hidden" value="{{ session('company_id') }}" class="form-control"
                                            name="company_id">
                                        <label for="firstname">FirstName</label><span class="withoutgstspan"
                                            style="color:red;">*</span>
                                        <input type="text" class="form-control withoutgstinput" id="firstname"
                                            name='firstname' placeholder="First name" required>
                                        <span class="modal-error-msg" id="modal-error-firstname"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="lastname">LastName</label>
                                        {{-- <span style="color:red;">*</span> --}}
                                        <input type="text" class="form-control" id="lastname" name='lastname'
                                            placeholder="Last name">
                                        <span class="modal-error-msg" id="modal-error-lastname"
                                            style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <label for="company_name">Company Name</label>
                                        <span class="withgstspan" style="color:red;">*</span>
                                        <input type="text" class="form-control withgstiput" id="company_name"
                                            name='company_name' id="" placeholder="Company name">
                                        <span class="modal-error-msg" id="modal-error-company_name"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="gst_number">GST Number</label>
                                        {{-- <span class="withgstspan" style="color:red;">*</span> --}}
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
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <input type="email" class="form-control requiredinput" name="email"
                                            id="modal_email" placeholder="Enter Email" />
                                        <span class="modal-error-msg" id="modal-error-email" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="modal_exampleInputphone">Contact Number</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <input type="tel" class="form-control requiredinput" name='contact_number'
                                            id="modal_exampleInputphone" placeholder="0123456789">
                                        <span class="modal-error-msg" id="modal-error-contact_number"
                                            style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <label for="modal_country">Select Country</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <select class="form-control requiredinput" name='country' id="modal_country">
                                            <option selected="" disabled="">Select your Country</option>
                                        </select>
                                        <span class="modal-error-msg" id="modal-error-country" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="modal_state">Select State</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <select class="form-control requiredinput" name='state' id="modal_state">
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
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <select class="form-control requiredinput" name='city' id="modal_city">
                                            <option selected="" disabled="">Select your City</option>
                                        </select>
                                        <span class="modal-error-msg" id="modal-error-city" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="modal_pincode">Pincode</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <input type="text" id="modal_pincode" name='pincode'
                                            class="form-control requiredinput" placeholder="Pin Code">
                                        <span class="modal-error-msg" id="modal-error-pincode" style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-sm-6">
                                        <label for="house_no_building_name">House no./ Building Name</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <textarea class="form-control requiredinput" name='house_no_building_name' id="house_no_building_name"
                                            rows="2" placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                        <span class="modal-error-msg" id="modal-error-house_no_building_name"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="road_name_area_colony">Road Name/Area/Colony</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <textarea class="form-control requiredinput" name='road_name_area_colony' id="road_name_area_colony" rows="2"
                                            placeholder="e.g. sardar patel road, jagatpur"></textarea>
                                        <span class="modal-error-msg" id="modal-error-road_name_area_colony"
                                            style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="modal_submitBtn">Save</button>
                                <button id="modal_resetbtn" type="reset" class="btn iq-bg-danger mr-2">Reset</button>
                                <button id="modal_cancelBtn" type="btn" class="btn btn-secondary">Cancel</button>
                            </div>
                        </form>
                    </table>
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
            loaderhide();

            const API_TOKEN = "{{ session()->get('api_token') }}";
            const COMPANY_ID = "{{ session()->get('company_id') }}";
            const USER_ID = "{{ session()->get('user_id') }}";

            var po_id = @json($edit_id);
            let purchaseSearchUrl = "{{ route('purchase.edit', '__editId__') }}".replace('__editId__', po_id);


            function ajaxRequest(type, url, data) {
                return $.ajax({
                    type,
                    url,
                    data
                });
            }

            function handleAjaxError(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#error-' + key).text(value[0]);
                    });
                    $('html, body').animate({
                        scrollTop: 0
                    }, 1000);
                } else {
                    var errorMessage = "An error occurred";
                    try {
                        var responseJSON = JSON.parse(xhr.responseText);
                        errorMessage = responseJSON.message || errorMessage;
                    } catch (e) {}
                    Toast.fire({
                        icon: "error",
                        title: errorMessage
                    });
                }
            }
            // currency data fetch from country table and set currensy dropdown
            ajaxRequest('GET', "{{ route('country.index') }}", {
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.country != '') {
                    // You can update your HTML with the data here if needed
                    $.each(response.country, function(key, value) {
                        $('#currency').append(
                            `<option data-symbol='${value.currency_symbol}' data-currency='${value.currency}' value='${value.id}'>${value.country_name} - ${value.currency_name} - ${value.currency} - ${value.currency_symbol} </option>`
                        );
                    });
                    $('#currency').val(101);
                } else {
                    $('#currency').append(
                        `<option disabled '>No Data found </option>`);
                }
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });


            // supplier data fetch and set supplier dropdown
            function suppliers(supplierid = 0) {
                loadershow();
                $('#supplier').html(`
                   <option selected="" value=0 disabled=""> Select supplier</option>
                   <option value="add_supplier" > Add New supplier </option>
                `);

                ajaxRequest('GET', "{{ route('supplier.index') }}", {
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    if (response.status == 200 && response.supplier != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.supplier, function(key, value) {
                            const supplierDetails = [value.suppliername, value
                                .company_name, value.contact_no, value.email
                            ].filter(Boolean).join(' - ');

                            $('#supplier').append(
                                `<option  data-gstno='${value.gst_no}' value='${value.id}'>${supplierDetails}</option>`
                            )
                        });
                        $('#supplier').val(supplierid);
                        $('.select2').select2(); // search bar in supplier list
                        if (supplierid !=
                            0) { // get supplier data if its new added from add new supplierform
                            loadershow();
                            var selectedOption = $('#supplier').find('option:selected');

                            let supplierSearchUrl = "{{ route('supplier.search', '__supplierId__') }}"
                                .replace('__supplierId__', supplierid);

                            ajaxRequest('GET', supplierSearchUrl, {
                                token: API_TOKEN,
                                company_id: COMPANY_ID,
                                user_id: USER_ID
                            }).done(function(response) {
                                // You can update your HTML with the data here if needed
                                if (response.status == 200 && response.suppliers != '') {
                                    var countryid = response.supplier.country_id
                                    if (countryid != null) {
                                        $('#currency').val(countryid);
                                        currentcurrency = $('#currency option:selected').data(
                                            'currency');
                                        currentcurrencysymbol = $('#currency option:selected').data(
                                            'symbol');
                                        $('.currentcurrencysymbol').text(currentcurrencysymbol);
                                    }

                                } else if (response.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                }
                                loaderhide();
                            }).fail(function(xhr) {
                                loaderhide();
                                handleAjaxError(xhr);
                            });
                        }
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#supplier').append(`<option disabled '>No Data found </option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });


            };

            suppliers();


            // get product data 
            ajaxRequest('GET', "{{ route('product.index') }}", {
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.product != '') {
                    productdata = response.product;
                    // You can update your HTML with the data here if needed
                    $.each(response.product, function(key, value) {
                        $('#products').append(
                            `<option id="product_option_${value.id}" ${value.track_quantity == 0 ? 'disabled' : ''} value='${value.id}'>${value.name} ${value.track_quantity == 0 ? ' - inventory not tracked' : ''}</option>`
                        )
                    });
                    $('#products').val('');
                    $('#products').select2({
                        placeholder: "Select Product",
                        mulitple: true,
                        search: true,
                    }); // search bar in product list 
                } else if (response.status == 500) {
                    Toast.fire({
                        icon: "error",
                        title: response.message
                    });
                } else {
                    $('#products').append(`<option disabled>No Product found </option>`);
                }
                loaderhide();
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });

            // fetch country id from selected supplier and set input value for hidden file
            $('#supplier').on('change', function() {
                loadershow();
                var selectedOption = $(this).find('option:selected');

                var supplierid = $(this).val();
                if (supplierid == 'add_supplier') {
                    $('#exampleModalScrollable').modal('show');
                }

                supplierSearchUrl = "{{ route('supplier.search', '__supplierId__') }}".replace(
                    '__supplierId__', supplierid);

                ajaxRequest('GET', supplierSearchUrl, {
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    // You can update your HTML with the data here if needed
                    if (response.status == 200 && response.suppliers != '') {
                        var countryid = response.supplier.country_id
                        if (countryid != null) {
                            $('#currency').val(countryid);
                        }
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });

            });

            var total_received_product = []; // store old products ids who has received inventory
            // purchase data fetch
            ajaxRequest('GET', purchaseSearchUrl, {
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.purchase != '') {
                    purchase = response.purchase;
                    purchase_order_details = response.purchase_order_details;

                    $('#purchaseordernumber').text(`
                        #PO${purchase.id}                    
                    `);

                    if (purchase.supplier_id) {
                        $('#supplier').val(purchase.supplier_id).select2();
                    }

                    if (purchase.payment_terms) {
                        $('#payment').val(purchase.payment_terms || '');
                    }

                    if (purchase.currency) {
                        $('#currency').val(purchase.currency || '');
                    }

                    if (purchase.estimated_arrival) {
                        $('#estimated_arrival').val(purchase.estimated_arrival || '');
                    }

                    $('#shipping_carrier').val(purchase.shipping_carrier || '');
                    $('#tracking_number').val(purchase.tracking_number || '');
                    $('#tracking_url').val(purchase.tracking_url || '');
                    $('#reference_number').val(purchase.reference_number || '');
                    $('#note_to_supplier').val(purchase.note_to_supplier || '');
                    $('#taxes').val(purchase.taxes) || '';
                    $('#sub_total').val(purchase.sub_total || '');
                    $('#shipping').val(purchase.shipping || '');
                    $('#discount').val(purchase.discount || '');
                    $('#total').val(purchase.total || '');

                    $('#itemcount').text(purchase.total_items || '');
                    (purchase.total_items < 2) ? $('#itemcounttext').text(' item'): $('#itemcounttext')
                        .text(' items');

                    var total_product = [];
                    $.each(purchase_order_details, function(key, product) {
                        total_product.push(String(product.product_id));
                        var received = parseInt(product.accepted || 0) + parseInt(product
                            .rejected || 0);
                        if (received > 0) {
                            total_received_product.push(String(product.product_id));
                        }
                        $('#data').append(`
                            <tr id="product_${product.product_id}">
                                <td>
                                    <input type="text" name="product_name_${product.product_id}" value="${product.product_name || ''}" class="form-control" readonly>
                                    <span class="error-msg" id="error-product_name_${product.product_id}" style="color: red"></span>
                                    ${product.accepted ? `<p class="text-success mb-0">Received quantity: ${product.accepted}</p>` : ''}
                                    ${received > 0 ? `<ul class="text-muted pl-0 mb-0"><li>Received products can't be removed</li></ul>` : ''}
                                    ${product.track_quantity == 0 ? `<span class="text-danger"><i class="ri-information-line text-bold">Inventory not tracked</i></span>` : ''}
                                </td>
                                <td>
                                    <input type="text" name="product_supplier_sku_${product.product_id}" value="${product.supplier_sku || ''}" id="product_supplier_sku_${product.product_id}" class="form-control product_supplier_sku">
                                    <span class="error-msg" id="error-product_supplier_sku_${product.product_id}" style="color: red"></span>
                                </td>
                                <td>
                                    <input type="number" name="product_quantity_${product.product_id}" value="${product.quantity || 0}"  id="product_quantity_${product.product_id}" class="form-control product_quantity product_price_count numberinput" step="any" value="1">
                                    <span class="error-msg" id="error-product_quantity_${product.product_id}" style="color: red"></span>
                                </td>
                                <td>
                                    <input type="number" name="product_price_${product.product_id}" value="${product.price || 0}" id="product_price_${product.product_id}" class="form-control product_price product_price_count numberinput"  step="any" value="0" min="0" >
                                    <span class="error-msg" id="error-product_price_${product.product_id}" style="color: red"></span>
                                </td>
                                <td>
                                    <input type="number" name="product_tax_${product.product_id}" value="${product.tax || 0}" id="product_tax_${product.product_id}" class="form-control product_tax product_price_count numberinput"  step="any" placeholder="0" min="0" >
                                    <span class="error-msg" id="error-product_tax_${product.product_id}" style="color: red"></span>
                                </td>
                                <td>
                                    <input type="number" name="product_total_amount_${product.product_id}" value="${product.total || 0}" id="product_total_amount_${product.product_id}" class="form-control product_total_amount product_price_count numberinput" value="0" min="0" step="any" readonly>
                                    <span class="error-msg" id="error-product_total_amount_${product.product_id}" style="color: red"></span>
                                </td>
                            </tr>
                        `);
                    });



                    $('#products').val(total_product).select2({
                        placeholder: "Select Product",
                        mulitple: true,
                        search: true,
                    });

                    $('#datatable').show();

                }
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });




            // on change product remove and add product details row
            $('#products').on('change', function() {
                var selectedProducts = $(this).val();

                // If no products are selected, keep received products selected
                if (selectedProducts == null && total_received_product.length > 0) {
                    selectedProducts = total_received_product;
                } else {
                    selectedProducts = [...new Set([...selectedProducts, ...total_received_product])]
                }


                $('#products').val(selectedProducts);


                if (selectedProducts == null) {
                    $('#datatable').hide('');
                    $('#data').html('');
                    calculateAmount();
                } else {
                    $('#datatable').show('');
                    // Loop through all selected products
                    $.each(selectedProducts, function(key, product) {
                        // If the product is selected but does not exist in the table, append the row
                        if ($('#data').find(`#product_${product}`).length == 0) {
                            var productName = $(`#product_option_${product}`).text();
                            $('#data').append(`
                                <tr id="product_${product}">
                                    <td>
                                        <input type="text" name="product_name_${product}" class="form-control"  value="${productName}" readonly>
                                        <span class="error-msg" id="error-product_name_${product}" style="color: red"></span>
                                    </td>
                                    <td>
                                        <input type="text" name="product_supplier_sku_${product}" id="product_supplier_sku_${product}" class="form-control product_supplier_sku">
                                        <span class="error-msg" id="error-product_supplier_sku_${product}" style="color: red"></span>
                                    </td>
                                    <td>
                                        <input type="number" name="product_quantity_${product}"  id="product_quantity_${product}" class="form-control product_quantity product_price_count numberinput" step="any" value="1">
                                        <span class="error-msg" id="error-product_quantity_${product}" style="color: red"></span>
                                    </td>
                                    <td>
                                        <input type="number" name="product_price_${product}" id="product_price_${product}" class="form-control product_price product_price_count numberinput"  step="any" value="0" min="0" >
                                        <span class="error-msg" id="error-product_price_${product}" style="color: red"></span>
                                    </td>
                                    <td>
                                        <input type="number"name="product_tax_${product}" id="product_tax_${product}" class="form-control product_tax product_price_count numberinput"  step="any" placeholder="0" min="0" >
                                        <span class="error-msg" id="error-product_tax_${product}" style="color: red"></span>
                                    </td>
                                    <td>
                                        <input type="number" name="product_total_amount_${product}" id="product_total_amount_${product}" class="form-control product_total_amount product_price_count numberinput" value="0" min="0" step="any" readonly>
                                        <span class="error-msg" id="error-product_total_amount_${product}" style="color: red"></span>
                                    </td>
                                </tr>
                            `);
                        }
                    });

                    // Now loop through the selected products and remove the unselected ones
                    $('#data tr').each(function() {
                        var rowProductId = $(this).attr('id').replace('product_',
                            ''); // Get the product ID from the row
                        if (selectedProducts.indexOf(rowProductId) === -1) {
                            $(this)
                                .remove(); // If the product is no longer selected, remove the row
                        }
                    });

                    calculateAmount();
                }
            });

            $(document).on('change', '.product_price_count', function() {
                calculateAmount();
            });



            function calculateAmount() {
                var subtotal = 0;
                var totaltax = 0;
                var totalitems = 0;
                // Get and validate shipping and discount values
                var shipping = parseFloat($('#shipping').val()) || 0;
                var discount = parseFloat($('#discount').val()) || 0;

                // Iterate over each row in the #data table
                $('#data tr').each(function() {
                    // Get the amount from the current row
                    var price = parseFloat($(this).find('.product_price').val()) || 0;
                    var quantity = parseInt($(this).find('.product_quantity').val()) || 0;
                    var tax = parseFloat($(this).find('.product_tax').val()) || 0;

                    var total = price * quantity;

                    if (tax != '' && tax != 0 && !isNaN(tax)) {
                        tax = (total * (tax / 100));
                        total = total + tax;
                        totaltax += tax;
                    }

                    // Only add the amount if it's a valid number
                    if (!isNaN(total)) {
                        subtotal += total;
                    }

                    // Set the total for the current row in the product_total_amount field
                    $(this).find('.product_total_amount').val(total.toFixed(
                        2)); // Optional: Format to 2 decimal places

                    totalitems += quantity;

                });

                $('#itemcount').text(totalitems);
                (totalitems < 2) ? $('#itemcounttext').text(' item'): $('#itemcounttext').text(' items');

                // Ensure `totaltax` and `subtotal` are valid numbers
                totaltax = isNaN(totaltax) ? 0 : totaltax;
                subtotal = isNaN(subtotal) ? 0 : subtotal;

                // Calculate total amount: ensure no NaN values in the final sum
                var totalamount = (totaltax + subtotal + shipping + discount);

                // Set values in the respective fields, ensuring they are numbers
                $('#taxes').val(totaltax.toFixed(2));
                $('#sub_total').val(subtotal.toFixed(2));
                $('#total').val(totalamount.toFixed(2)); // Format total with 2 decimal places
            }



            // Bind the input event to the discount field
            $('#discount').on('input', function() {
                var discountValue = parseFloat($(this).val());

                // If the value is greater than 0, reset it to 0
                if (discountValue > 0) {
                    $(this).val(-Math.abs(discountValue)); // Convert to negative
                }
            });

            // Negative value check on input
            $(document).on('input', '.numberinput', function() {
                var inputValue = $(this).val();
                var numberValue = parseFloat(inputValue);

                // If the value is negative, convert it to positive
                if (numberValue < 0) {
                    $(this).val(Math.abs(numberValue)); // Convert to positive
                }
            });

            // Blank value check on focusout
            $(document).on('focusout', '.numberinput', function() {
                var inputValue = $(this).val();

                // If the input is blank, set it to 0
                if (inputValue === '') {
                    $(this).val(0);
                }
            });

            $(document).on('input', '.product_quantity', function() {
                let value = $(this).val();
                // Remove all decimal points
                if (value.includes('.')) {
                    $(this).val(value.replace(/\./g, ''));
                }
            });

            // redirect pruchase list page on click cancel button
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.viewpurchase', '__viewId__') }}".replace(
                    '__viewId__', po_id);
            });

            //submit form
            $('#purchaseupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formdata = new FormData($(this)[0]);
                itemcount = $('#itemcount').text();

                // Append itemcount to the formdata
                formdata.append('itemcount', itemcount);


                var products = total_received_product;

                // Loop through products and append to formdata if it doesn't already exist
                products.forEach(function(product) {
                    // Check if the product is already in formdata
                    // Here, we'll use formdata.has() to check if the product key is already present.
                    // If it's not present, we append it to the formdata
                    if (!formdata.has(product)) {
                        formdata.append('products[]', product);
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "{{ route('purchase.update', $edit_id) }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.viewpurchase', '__viewId__') }}"
                                .replace('__viewId__', po_id);

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
                        var errorcontainer;
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                // Update the text of the error message
                                $('#error-' + key).text(value[0]);
                                // Store the first error container to scroll to
                                if (!errorcontainer) {
                                    errorcontainer = '#error-' + key;
                                }
                            });

                            // Scroll to the first error container
                            if (errorcontainer) {
                                $('html, body').animate({
                                        scrollTop: $(errorcontainer).offset().top -
                                            100 // Add some offset for better visibility, e.g., 100px from the top
                                    },
                                    1000
                                ); // Duration of the scroll animation (1000ms = 1 second)
                            }
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            });

            // for add new supplier 

            // show country data in dropdown and set default value according logged in user
            ajaxRequest('GET', "{{ route('country.index') }}", {
                token: API_TOKEN,
            }).done(function(response) {
                if (response.status == 200 && response.country != '') {
                    // You can update your HTML with the data here if needed
                    $.each(response.country, function(key, value) {
                        $('#modal_country').append(
                            `<option value='${value.id}'> ${value.country_name}</option>`
                        )
                    });
                    country_id = "{{ Auth::guard('admin')->user()->country_id }}";
                    $('#modal_country').val(country_id);
                    loadstate();
                } else {
                    $('#modal_country').append(`<option> No Data Found</option>`);
                }
                loaderhide();
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });

            // load state in dropdown when country change
            $('#modal_country').on('change', function() {
                loadershow();
                $('#modal_city').html(`<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id);
            });

            // load state in dropdown and select state according to user
            function loadstate(id = 0) {
                $('#modal_state').html(`<option selected="" disabled="">Select your State</option>`);
                let stateSearchUrl = "{{ route('state.search', 'id') }}".replace('id', id);
                var url = stateSearchUrl;
                if (id == 0) {
                    url = "{{ route('state.search', Auth::guard('admin')->user()->country_id) }}";
                }
                ajaxRequest('GET', url, {
                    token: API_TOKEN,
                }).done(function(response) {
                    if (response.status == 200 && response.state != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.state, function(key, value) {
                            $('#modal_state').append(
                                `<option value='${value.id}'> ${value.state_name}</option>`
                            )
                        });
                        if (id == 0) {
                            state_id = "{{ Auth::guard('admin')->user()->state_id }}";
                            $('#modal_state').val(state_id);
                            loadcity();
                        }
                    } else {
                        $('#modal_state').append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }

            // load city in dropdown when state select/change
            $('#modal_state').on('change', function() {
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id);
            });

            function loadcity(id = 0) {
                $('#modal_city').html(`<option selected="" disabled="">Select your City</option>`);
                citySearchUrl = "{{ route('city.search', 'id') }}".replace('id', id);
                url = citySearchUrl;
                if (id == 0) {
                    url = "{{ route('city.search', Auth::guard('admin')->user()->state_id) }}";
                }

                ajaxRequest('GET', url, {
                    token: API_TOKEN,
                }).done(function(response) {
                    if (response.status == 200 && response.city != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.city, function(key, value) {
                            $('#modal_city').append(
                                `<option value='${value.id}'> ${value.city_name}</option>`
                            )
                        });
                        if (id == 0) {
                            $('#modal_city').val("{{ Auth::guard('admin')->user()->city_id }}");
                        }
                    } else {
                        $('#modal_city').append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }

            // close pop up modal and reset new supplier form
            $('#modal_cancelBtn').on('click', function() {
                $('#supplierform')[0].reset();
                $('#exampleModalScrollable').modal('hide');
                $('#supplier option:first').prop('selected', true);
            })

            // submit new supplier  form
            $('#supplierform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.modal-error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('supplier.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#supplierform')[0].reset();
                            $('#exampleModalScrollable').modal('hide');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            suppliers(response.supplier_id);
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

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
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorcontainer;
                            $.each(errors, function(key, value) {
                                $('#modal-error-' + key).text(value[0]);
                                errorcontainer = '#modal-error-' + key;
                            });
                            $('.modal-body').animate({
                                scrollTop: $(errorcontainer).position().top
                            }, 1000);
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
