@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - View Purchase Order
@endsection

@section('style')
    <style>
        table,
        table th,
        table td {
            border-right: transparent !important;
            border-left: transparent !important;
        }

        .error-msg {
            color: red;
        }

        body {
            background-color: #eee;
        }

        .mt-70 {
            margin-top: 70px;
        }

        .mb-70 {
            margin-bottom: 70px;
        }

        .card {
            box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 20, 0.03), 0 0.9375rem 1.40625rem rgba(4, 9, 20, 0.03), 0 0.25rem 0.53125rem rgba(4, 9, 20, 0.05), 0 0.125rem 0.1875rem rgba(4, 9, 20, 0.03);
            border-width: 0;
            transition: all .2s;
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(26, 54, 126, 0.125);
            border-radius: .25rem;
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1.25rem;
        }

        .vertical-timeline {
            width: 100%;
            position: relative;
            padding: 1.5rem 0 1rem;
        }

        .vertical-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 4px;
            background: #e9ecef;
            border-radius: .25rem;
        }

        .vertical-timeline-element {
            position: relative;
            margin: 0 0 1rem;
        }

        .vertical-timeline--animate .vertical-timeline-element-icon.bounce-in {
            visibility: visible;
            animation: cd-bounce-1 .8s;
        }

        .vertical-timeline-element-icon {
            position: absolute;
            top: 0;
            left: -7px;
        }

        .vertical-timeline-element-icon .badge-dot-xl {
            box-shadow: 0 0 0 5px #fff;
        }

        .badge-dot-xl {
            width: 18px;
            height: 18px;
            position: relative;
        }

        .badge:empty {
            display: block !important;
        }

        .badge-dot-xl::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: .25rem;
            position: absolute;
            left: 50%;
            top: 50%;
            margin: -5px 0 0 -5px;
            background: #fff;
        }

        .vertical-timeline-element-content {
            position: relative;
            margin-left: 25px;
            font-size: .8rem; 
        }

        .vertical-timeline-element-content .btn {
            text-align: left !important;
        }

        .vertical-timeline-element-content .timeline-title {
            font-size: .8rem;
            margin: 0 0 .5rem;
            padding: 2px 0 0;
        }

        .vertical-timeline-element-content .vertical-timeline-element-date {
            display: block;
            position: absolute; 
            top: 0;
            padding-right: 10px;
            text-align: right;
            color: #adb5bd;
            font-size: .7619rem;
            white-space: nowrap;
        }

        .vertical-timeline-element-content:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Media query for screens with max width of 768px */
        @media (min-width: 769px) {
            .vertical-timeline-element-content .vertical-timeline-element-date {
                right: 0px !important; /* Remove absolute positioning */ 
            }
        }
        /* Media query for screens with max width of 768px */
        @media (max-width: 768px) {
            .vertical-timeline-element-content .vertical-timeline-element-date {
                left: 0px !important; /* Remove absolute positioning */
                top: -20px !important; /* Adjust vertical positioning */
            }
        }
    </style>
@endsection

@section('title')
    Purchase Order
@endsection


@section('form-content')
    <div class="row">
        <div class="col-12" id="actionbtndiv" style="display: none">
            <div class="form-group">
                <div class="form-row">
                    <h4><span id="purchaseordernumber"></span><span id="purchaseorderstatus"></span></h4>
                    <div class="col-sm-12 d-flex flex-column flex-md-row justify-content-end">
                        <button type="button" id="receiveinventorybtn" class="btn btn-primary m-0 my-1 mx-1"
                            data-toggle="modal" data-target="#received">Receive inventory</button>
                        <button type="button" id="orderedbtn" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Mark as orderd" class="btn btn-primary m-0 my-1 mx-1">Mark as
                            ordered</button>
                        <button id="cancelbtn" type="button" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Back" class="btn btn-outline-primary my-1 mx-1">Back</button>
                        <button id="editbtn" type="button" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Edit" class="btn btn-outline-primary my-1 mx-1">Edit</button>
                        <button id="orderclosebtn" type="button" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Close purchase order" data-status="closed"
                            class="btn btn-outline-primary my-1 mx-1 statusbtn">Close
                            order</button>
                        <button id="orderopenbtn" type="button" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Reopen purchase order" data-status="reopen"
                            class="btn btn-outline-primary my-1 mx-1 statusbtn">Reopen
                            order</button>
                        <button id="deletebtn" type="button" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Delete" class="btn btn-danger my-1 mx-1">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="iq-card">
                <div class="iq-card-body">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-6 mb-3">
                                <label for="supplier">Supplier</label>
                                <div id="supplierdetail">
                                    None
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="payment">Payment Mode</label>
                                <div id="paymentdetail">
                                </div>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <label for="totalreceived">Total Received</label>
                                <div id="totalreceived">

                                </div>
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
                                <p id="estimated_arrival"></p>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="shipping_carrier">Shipping carrier</label>
                                <p id="shipping_carrier"></p>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="tracking_number">Tracking number</label>
                                <p id="tracking_number"></p>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="tracking_url">Tracking URL</label>
                                <p id="tracking_url"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="iq-card">
                <div class="iq-card-body">
                    <h6 class="mb-3">Ordered product</h6>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-12 table-responsive" id="datatable">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Products</th>
                                            <th>Supplier SKU</th>
                                            <th>Received</th>
                                            <th>Price</th>
                                            <th>Tax</th>
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
                                <p id="reference_number"></p>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <label for="note_to_supplier">Note to supplier</label>
                                <div id="note_to_supplier" style="white-space: pre-line">

                                </div>
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
                                <p id="taxes"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-12 d-flex justify-content-between">
                                <label for="sub_total">Sub Total</label>
                                <p id="sub_total"></p>
                            </div>
                        </div>
                    </div>
                    <p>
                        <span id="itemcount">0</span>
                        <span id="itemcounttext">items</span>
                    </p>
                    <h6 class="mb-2">Cost adjustment</h6>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-12 d-flex justify-content-between">
                                <label for="shipping">Shipping</label>
                                <p id="shipping"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-12 d-flex justify-content-between">
                                <label for="discount">Discount</label>
                                <p id="discount"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-12 d-flex justify-content-between">
                                <h5 for="total">Total</h5>
                                <p id="total"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row d-flex justify-content-center mt-70 mb-70">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Timeline</h5>
                            <div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column"
                                id="timelinerow">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="received" tabindex="-1" role="dialog" aria-labelledby="receivedTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receivedTitle">received inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="receivedinventoryform">
                        @csrf
                        <div class="modal-body">
                            <div class="col-12">
                                <input type="hidden" name="token" class="form-control"
                                    value="{{ session('api_token') }}" placeholder="token" required />
                                <input type="hidden" value="{{ session('user_id') }}" class="form-control"
                                    name="user_id" placeholder="user_id">
                                <input type="hidden" value="{{ session(key: 'company_id') }}" class="form-control"
                                    name="company_id" placeholder="company_id">
                            </div>
                            <div class="col-12">
                                <table class="table table-bordered w-100">
                                    <thead>
                                        <tr>
                                            <th colspan="3" id="received_purchase_number"></th>
                                            <th colspan="2" id="received_total_count">Total received: <span
                                                    id="total_received"></span> of <span id="total_received_from"></span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Product</th>
                                            <th>Supplier SKU</th>
                                            <th>Accept</th>
                                            <th>Reject</th>
                                            <th>Received</th>
                                        </tr>
                                    </thead>
                                    <tbody id="received_inventory">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                data-original-title="Save" class="btn btn-primary float-right my-0">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
            // response status == 200 that means response succesfully Received
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            var po_id = @json($id);
            let purchaseSearchUrl = "{{ route('purchase.search', '__viewId__') }}".replace('__viewId__', po_id);
            let timelineUrl = "{{ route('purchase.timeline', '__viewId__') }}".replace('__viewId__', po_id);

            const API_TOKEN = "{{ session()->get('api_token') }}";
            const COMPANY_ID = "{{ session()->get('company_id') }}";
            const USER_ID = "{{ session()->get('user_id') }}";

            global_response = null;

            function loaddata() {
                // purchase data fetch from purchases table and set
                ajaxRequest('GET', purchaseSearchUrl, {
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    loaderhide();
                    if (response.status == 200 && response.purchase != '') {
                        global_response = response;
                        purchase = response.purchase;
                        purchase_order_details = response.purchase_order_details;

                        if (purchase.status != 'draft') {
                            $('#orderedbtn').remove();
                            $('#deletebtn').remove();
                        } else {
                            $('#receiveinventorybtn').remove();
                            $('#orderclosebtn').remove();
                            $('#orderopenbtn').remove();
                        }

                        if (purchase.is_active == 0) {
                            $('#receiveinventorybtn').remove();
                            $('#editbtn').remove();
                            $('#orderclosebtn').remove();
                        } else {
                            $('#orderopenbtn').remove();
                        }

                        totalreceived = purchase.accepted + purchase.rejected;

                        $('#totalreceived').html(` 
                            <p> ${totalreceived} of ${purchase.total_items} </p>
                            <span class="text-success">Accepted : ${purchase.accepted || 0}</span>
                            <span class="text-danger">Rejected : ${purchase.rejected || 0}</span>
                        `);

                        $('#purchaseordernumber').text(`
                            #PO${purchase.id}                    
                        `);

                        $('#purchaseorderstatus').text(`
                            - ${purchase.is_active == 0 ?  'Closed' : `${purchase.status}`}                   
                        `);


                        if (purchase.suppliername) {
                            $('#supplierdetail').html(`
                                <h4>${purchase.suppliername || 'None'}</h4>
                                <p>Contact :- ${purchase.contact_no || 'None'}</p>
                                <p>Address :- ${purchase.supplieraddress || 'None'}</p>
                            `);
                        }


                        $('#paymentdetail').html(`
                            <p>${purchase.payment_terms || 'None'}</p>
                        `);

                        $('#estimated_arrival').text(`
                            ${purchase.estimated_arrival_formatted ||'None'}
                        `);

                        $('#shipping_carrier').text(`
                            ${purchase.shipping_carrier ||'None'}
                        `);

                        $('#tracking_number').text(`
                            ${purchase.tracking_number ||'None'}
                        `);

                        $('#tracking_url').text(`
                            ${purchase.tracking_url ||'None'}
                        `);

                        $('#reference_number').text(`
                            ${purchase.reference_number ||'None'}
                        `);

                        $('#note_to_supplier').text(`
                            ${purchase.note_to_supplier ||'None'}
                        `);

                        $('#taxes').text(`
                            ${purchase.currency_symbol}${purchase.taxes ||'0.00'}
                        `);

                        $('#sub_total').text(`
                            ${purchase.currency_symbol}${purchase.sub_total ||'0.00'}
                        `);

                        $('#itemcount').text(`
                            ${purchase.total_items}
                        `);

                        $('#shipping').text(`
                            ${purchase.currency_symbol}${purchase.shipping ||'0.00'}
                        `);

                        $('#discount').text(`
                            ${purchase.currency_symbol}${purchase.discount ||'0.00'}
                        `);

                        $('#total').text(`
                            ${purchase.currency_symbol}${purchase.total ||'0.00'}
                        `);

                        $('#data').html('');

                        $.each(purchase_order_details, function(key, product) {
                            var received = product.accepted + product.rejected;
                            $('#data').append(`
                                <tr>
                                    <td>
                                        <p class="mb-0">${product.product_name || '-'}</p>
                                        ${product.track_quantity == 0 ? `<span class="text-danger"><i class="ri-information-line text-bold">Inventory not tracked</i></span>` : ''}
                                    </td>        
                                    <td>${product.supplier_sku || '-'} </td>        
                                    <td>
                                        <p> ${received} of ${product.quantity} </p>
                                        <span class="text-success">Accepted : ${product.accepted || 0}</span>
                                        <span class="text-danger">Rejected : ${product.rejected || 0}</span>
                                    </td>        
                                    <td>${purchase.currency_symbol}${product.price || 0.00}</td>        
                                    <td>${product.tax || 0.00}%</td>        
                                    <td>${purchase.currency_symbol}${product.total || 0.00}</td>        
                                </tr>
                            `);

                        });

                        $('#actionbtndiv').show();

                    } else {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }

            loaddata();


            function loadtimeline() {

                //timeline data fetch 
                ajaxRequest('GET', timelineUrl, {
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    loaderhide();
                    if (response.status == 200 && response.timeline != '') {

                        $('#timelinerow').html('');

                        $.each(response.timeline, function(index, record) {

                            createdat = record.created_at;

                            formatteddate = formatDate(createdat);
                            formatTo12Hour = formatTo12HourFormat(createdat);

                            if ($(`div.${formatteddate}`).length < 1) {
                                $('#timelinerow').append(` 
                                 <div class="vertical-timeline-item vertical-timeline-element ${formatteddate}">
                                    <div>
                                        <span class="vertical-timeline-element-icon bounce-in">
                                             
                                        </span>
                                        <div class="vertical-timeline-element-content bounce-in">
                                            <p class="timeline-title">${formatteddate}</p>  
                                        </div>
                                    </div>
                                </div> 
                            `);
                            }

                            if($(`div#collapse-${record.id}`).length > 0){
                                $(`div#collapse-${record.id} tbody`).append(`
                                         <tr>
                                            <td class="text-left">${record.product_name || '-'}</td>
                                            <td class="text-right">${record.accepted || 0}</td>
                                            <td class="text-right">${record.rejected || 0}</td>
                                        </tr> 
                                `);
                            }else{ 
                                $('#timelinerow').append(`  
                                     <div class="vertical-timeline-item vertical-timeline-element">
                                        <div>
                                            <span class="vertical-timeline-element-icon bounce-in">
                                                <i class="badge badge-dot badge-dot-xl badge-cobalt-blue"></i>
                                            </span>
                                            <div class="vertical-timeline-element-content bounce-in">
                                                
                                                ${record.product_name ? `
                                                    <!-- Accordion Toggle Icon (Arrow) -->
                                                    <h4 class="timeline-title btn" data-toggle="collapse" data-target="#collapse-${record.id}" aria-expanded="false" aria-controls="collapse-${record.id}">${record.action} <i class="ri ri-eye-line" id="icon-${record.id}"></i></h4> 
                                                ` : `
                                                    <h4 class="timeline-title">${record.action}</h4> 
                                                `}
                                                
                                                <span class="vertical-timeline-element-date">${formatTo12Hour}</span>
    
                                                ${record.product_name ? `
                                                    <!-- Accordion Body -->
                                                    <div id="collapse-${record.id}" class="accordion-collapse collapse" aria-labelledby="heading-${record.id}" data-parent="#timelinerow">
                                                        <div class="accordion-body">
                                                            <table class="w-100">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-left">Products</th>
                                                                        <th class="text-right">Accepted</th>
                                                                        <th class="text-right">Rejected</th>
                                                                    </tr>    
                                                                <thead> 
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-left">${record.product_name || '-'}</td>
                                                                        <td class="text-right">${record.accepted || 0}</td>
                                                                        <td class="text-right">${record.rejected || 0}</td>
                                                                    </tr>    
                                                                </tbody>           
                                                            </table>    
                                                        </div>
                                                    </div>
                                                ` : ''}
                                                
                                            </div>
                                        </div>
                                    </div> 
                                `);
                            }

                        });

                    } else {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }

            loadtimeline();

            // Helper function to format the date
            function formatDate(createdAt) {
                const now = new Date();
                const createdDate = new Date(createdAt);

                // Check if the date is today
                if (createdDate.toDateString() === now.toDateString()) {
                    return "Today";
                }

                // Check if the date is yesterday
                const yesterday = new Date(now);
                yesterday.setDate(now.getDate() - 1);
                if (createdDate.toDateString() === yesterday.toDateString()) {
                    return "Yesterday";
                }

                // Otherwise, format the date as dd-mm-yyyy
                const day = createdDate.getDate().toString().padStart(2, '0');
                const month = (createdDate.getMonth() + 1).toString().padStart(2,
                    '0'); // Months are zero-indexed, so +1
                const year = createdDate.getFullYear();
                return `${day}-${month}-${year}`;
            }

            function formatTo12HourFormat(createdAt) {
                const date = new Date(createdAt);
                const now = new Date();
                const diffInMs = now - date; // Get the difference in milliseconds
                const diffInMinutes = Math.floor(diffInMs / (1000 * 60)); // Convert milliseconds to minutes
                const diffInHours = Math.floor(diffInMinutes / 60); // Convert minutes to hours

                // If the time difference is less than an hour, show relative time
                if (diffInMinutes < 1) {
                    return 'Just now';
                } else if (diffInMinutes === 1) {
                    return 'A minute ago';
                } else if (diffInMinutes < 60) {
                    return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
                }

                // If the time difference is greater than an hour, format it in 12-hour format
                let hours = date.getHours();
                let minutes = date.getMinutes();
                const ampm = hours >= 12 ? 'PM' : 'AM';

                // Convert hour from 24-hour format to 12-hour format
                hours = hours % 12;
                hours = hours ? hours : 12; // The hour '0' should be '12'

                // Add leading zero to minutes if necessary
                minutes = minutes < 10 ? '0' + minutes : minutes;

                // Format the time in 12-hour format with AM/PM
                return hours + ':' + minutes + ' ' + ampm;
            }



            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.purchase') }}";
            });

            $('#deletebtn').on('click', function() {
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this purchaseorder?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let purchaseDeleteUrl = "{{ route('purchase.delete', '__deleteId__') }}"
                            .replace('__deleteId__', po_id);
                        $.ajax({
                            type: 'PUT',
                            url: purchaseDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: " {{ session()->get('company_id') }} ",
                                user_id: " {{ session()->get('user_id') }} "
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });

                                    window.location = "{{ route('admin.purchase') }}";

                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });

                                    loaderhide();
                                }
                            },
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                handleAjaxError(xhr);
                            }
                        });
                    }
                );

            });

            $('#orderedbtn').on('click', function() {
                showConfirmationDialog(
                    'Mark as ordered?', // Title
                    "After marking as ordered you will be able to receive incoming inventory from your supplier. This purchase order can't be turned into a draft again.", // Text
                    'Mark as orderd', // Confirm button text
                    'cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let purchaseStatusUpdateUrl =
                            "{{ route('purchase.changestatus', '__orderId__') }}"
                            .replace('__orderId__', po_id);
                        $.ajax({
                            type: 'PUT',
                            url: purchaseStatusUpdateUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: " {{ session()->get('company_id') }} ",
                                user_id: " {{ session()->get('user_id') }} ",
                                status: "ordered"
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });

                                    window.location =
                                        "{{ route('admin.viewpurchase', '__viewId__') }}"
                                        .replace('__viewId__', po_id);

                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });

                                    loaderhide();
                                }
                            },
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                handleAjaxError(xhr);
                            }
                        });
                    }
                );

            });

            $('.statusbtn').on('click', function() {
                loadershow();
                let status = $(this).data('status');
                let purchaseStatusUpdateUrl =
                    "{{ route('purchase.changestatus', '__orderId__') }}"
                    .replace('__orderId__', po_id);
                $.ajax({
                    type: 'PUT',
                    url: purchaseStatusUpdateUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        user_id: " {{ session()->get('user_id') }} ",
                        status: status
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

                            window.location =
                                "{{ route('admin.viewpurchase', '__viewId__') }}"
                                .replace('__viewId__', po_id);

                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });

                            loaderhide();
                        }
                    },
                    error: function(xhr, status,
                        error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText
                        ); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            });

            $('#editbtn').on('click', function() {

                let purchaseEditUrl = "{{ route('admin.editpurchase', '__orderId__') }}"
                    .replace('__orderId__', po_id);
                window.location = purchaseEditUrl;
            });


            $('#receiveinventorybtn').on('click', function() {
                loadershow();
                purchase = global_response.purchase;
                purchase_order_details = global_response.purchase_order_details;


                totalreceived = purchase.accepted + purchase.rejected;

                $('#received_purchase_number').text(` 
                    #PO${purchase.id}
                `);

                $('#total_received').text(` 
                    ${totalreceived}
                `);

                $('#total_received_from').text(`
                    ${purchase.total_items}                   
                `);

                productcontent = '';

                $.each(purchase_order_details, function(key, product) {
                    var received = product.accepted + product.rejected;
                    productcontent += ` 
                            <tr id="product_${product.product_id}" data-product="${product.product_id}">
                                <td>${product.product_name || '-'}</td>        
                                <td>${product.supplier_sku || '-'} </td> 
                                <td>  
                                    <input type="number" name="accepted_${product.product_id}" id="accepted_${product.product_id}" class="form-control product_accepted received_action" min=${-(product.accepted || 0)} value=0>
                                    <p class="text-success">Total Accepted : <span id="product_received_accept_count_${product.product_id}" data-old="${product.accepted || 0}"> ${product.accepted || 0} </span></p> 
                                    <span class="error-msg error-accepted_${product.product_id}"></span>
                                </td>       
                                <td>  
                                    <input type="number" name="rejected_${product.product_id}" id="rejected_${product.product_id}" class="form-control product_rejected received_action" min=${-(product.rejected || 0)} value=0>
                                    <p class="text-danger">Total Rejected : <span id="product_received_reject_count_${product.product_id}" data-old="${product.rejected || 0}"> ${product.rejected || 0} </span></p>
                                    <span class="error-msg error-rejected_${product.product_id}"></span>
                                </td>       
                                <td>
                                    <p><span class="product_received" data-old="${received}" id="product_received_${product.product_id}">${received}</span> of <span id="total_quantity_${product.product_id}">${product.quantity}</span></p>
                                    <span class="error-msg" id="error-product_received_${product.product_id}"></span>
                                </td> 
                            </tr>
                        `;

                });
                $('#received_inventory').html(`
                       ${productcontent}
                `);

                loaderhide();

            });


            $(document).on('change', '.received_action', function() {
                $('.error-msg').text('');
                var parent = $(this).closest('tr').data('product');
                var total_quantity = parseInt($(`#total_quantity_${parent}`).text()) || 0;
                var accepted = parseInt($(`#accepted_${parent}`).val() || 0);
                var rejected = parseInt($(`#rejected_${parent}`).val() || 0);
                var old_total_product_received = parseInt($(`#product_received_${parent}`).data('old')) ||
                    0;
                var product_received_accept_count = parseInt($(`#product_received_accept_count_${parent}`)
                    .data('old')) || 0;
                var product_received_reject_count = parseInt($(`#product_received_reject_count_${parent}`)
                    .data('old')) || 0;

                if (accepted != 0 || rejected != 0) {
                    $(`#product_received_accept_count_${parent}`).text(product_received_accept_count +=
                        accepted);
                    $(`#product_received_reject_count_${parent}`).text(product_received_reject_count +=
                        rejected);
                    var total_product_received = old_total_product_received + (parseInt(accepted)) + (
                        parseInt(rejected));
                } else {
                    var total_product_received = old_total_product_received;
                    $(`#product_received_accept_count_${parent}`).text(product_received_accept_count);
                    $(`#product_received_reject_count_${parent}`).text(product_received_reject_count);
                }

                $(`#product_received_${parent}`).text(total_product_received);

                if (total_product_received > total_quantity || total_product_received < 0) {
                    $(`#error-product_received_${parent}`).text(
                        'Invalid! Please adjust received inventory');
                }

                settotalreceived();

            })

            function settotalreceived() {
                var totalcount = 0; // Initialize total count

                // Iterate through each row in the table
                $('#received_inventory tr').each(function() {
                    // Get the quantity from the .product_received column in the current row
                    var total_quantity = parseInt($(this).find('.product_received').text()) || 0;

                    // Add to the total count
                    totalcount += total_quantity;
                });

                // Update the total_received element with the calculated total
                $('#total_received').text(totalcount);
            }

            // Recalculate total when the values change (when accepted or rejected changes)
            $(document).on('input', '.product_accepted, .product_rejected', function() {
                settotalreceived();
            });

            // Function to check if the received inventory is valid
            function checkreceivedinventoryvalid() {
                let isValid = true; // Flag to keep track of the validation state

                // Loop through each row in the table to check if received values are valid
                $('#received_inventory tr').each(function() {
                    var parent = $(this).data('product');
                    var total_quantity = parseInt($(`#total_quantity_${parent}`).text()) || 0;
                    var accepted = parseInt($(`#accepted_${parent}`).val()) || 0;
                    var rejected = parseInt($(`#rejected_${parent}`).val()) || 0;
                    var old_total_product_received = parseInt($(`#product_received_${parent}`).data(
                        'old')) || 0;

                    // Calculate the total received product
                    var total_product_received = old_total_product_received + accepted + rejected;

                    // Check if the total received product is invalid
                    if (total_product_received > total_quantity || total_product_received < 0) {
                        isValid = false;
                        $(`#error-product_received_${parent}`).text(
                            'Invalid! Please adjust received inventory');
                    } else {
                        // Clear the error message if the entry is valid
                        $(`#error-product_received_${parent}`).text('');
                    }
                });

                return isValid; // Return the overall validation result
            }

            // Submit event handler for the received inventory form
            $('#receivedinventoryform').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Check if the received inventory is valid before submitting
                if (checkreceivedinventoryvalid()) {
                    loadershow();

                    // Serialize the form data
                    var FormData = $(this).serializeArray();

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('purchase.receiveinventory', '__purchaseId__') }}".replace(
                            '__purchaseId__', po_id),
                        data: FormData,
                        success: function(response) {
                            // Handle the response from the server
                            if (response.status == 200) {
                                // You can perform additional actions, such as showing a success message or redirecting the user
                                Toast.fire({
                                    icon: "success",
                                    title: response.message
                                });

                                $('#received').modal('hide');
                                loaddata();
                                loadtimeline();

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


                } else {
                    // If the data is not valid, show an error message
                    Toast.fire({
                        icon: "error",
                        title: 'There are some invalid entries! Please check the inventory data.'
                    });

                }
            });

        });
    </script>
@endpush
