@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')
@section('page_title')
    {{ config('app.name') }} - Inventory
@endsection
@section('table_title')
    Inventory
@endsection
@section('style')
    <style>
        .ui-widget-header {
            background: #1518b117 !important;
            border: 1px solid #e1ded9 !important;
        }

        .btn-info {
            background-color: #253566 !important;
            border-color: #253566 !important;
            color: white;
        }

        .btn-info:hover {
            background-color: #39519b !important;
            color: rgb(255, 255, 255);
        }

        .btn-success {
            background-color: #67d5a5d9 !important;
            border-color: var(--iq-success) !important;
            color: black !important;
        }

        .btn-success:hover {
            background-color: #16d07ffa !important;
            border-color: var(--iq-success) !important;
            color: rgb(250, 250, 250) !important;
        }

        /* Styles for the button */
        .modalinventory button {
            border: none;
            background: inherit;
        }

        /* Styles for the button */
        .unavailableinventoryaction button {
            border: none;
            background: inherit;
        }

        .inventorybtn {
            border: none;
            background: inherit;
        }

        /* Styles for the button */
        .unavailableinventoryaction button:hover {
            color: rgb(75, 226, 237);
        }
    </style>
@endsection


@section('table-content')
    <table id="data"
        class="table display table-bordered table-responsive-sm table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>UnAvailable</th>
                <th>Available</th>
                <th>On Hand</th>
                <th>Incoming</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>

    {{-- modal for show Unavailable inventory --}}
    <div class="modal fade" id="unavailable" tabindex="-1" role="dialog" aria-labelledby="unavailableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unavailableTitle"><b>Unavailable inventory</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <table class="table">
                            <tr>
                                <td>Damaged</td>
                                <td class="modalinventory">
                                    <button data-btn=damagedcount>
                                        <span class="damagedcount">0</span>
                                        <span>
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="popover-content d-none">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item addinventory">
                                                <button class="unavailablehandlebtn" data-module="damagedcount"
                                                    data-type="add" data-targetcolumn="damaged" data-title="Add Damaged"
                                                    data-toggle="modal" data-target="#handleunavailable">
                                                    <i class="ri-add-line"></i> Add Inventory
                                                </button>
                                            </li>
                                            <li class="list-group-item movetoavailable">
                                                <button class="unavailablehandlebtn" data-module="damagedcount"
                                                    data-type="move" data-targetcolumn="damaged"
                                                    data-title="Move Damaged to Available" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-arrow-right-line"></i> Move to Available
                                                </button>
                                            </li>
                                            <li class="list-group-item deleteinventory">
                                                <button class="unavailablehandlebtn" data-module="damagedcount"
                                                    data-type="delete" data-targetcolumn="damaged"
                                                    data-title="Delete Damaged" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-delete-bin-7-line"></i> Delete inventory
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Quality Control</td>
                                <td class="modalinventory">
                                    <button data-btn="qualitycontrolcount">
                                        <span class="qualitycontrolcount">0</span>
                                        <span>
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="popover-content d-none">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item addinventory">
                                                <button class="unavailablehandlebtn" data-module="qualitycontrolcount"
                                                    data-type="add" data-targetcolumn="quality_control"
                                                    data-title="Add Quality Control" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-add-line"></i> Add Inventory
                                                </button>
                                            </li>
                                            <li class="list-group-item movetoavailable">
                                                <button class="unavailablehandlebtn" data-module="qualitycontrolcount"
                                                    data-type="move" data-targetcolumn="quality_control"
                                                    data-title="Move Quality Control to Available" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-arrow-right-line"></i> Move to Available
                                                </button>
                                            </li>
                                            <li class="list-group-item deleteinventory">
                                                <button class="unavailablehandlebtn" data-module="qualitycontrolcount"
                                                    data-type="delete" data-targetcolumn="quality_control"
                                                    data-title="Delete Quality Control" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-delete-bin-7-line"></i> Delete inventory
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Safety Stock</td>
                                <td class="modalinventory">
                                    <button data-btn="safetystockcount">
                                        <span class="safetystockcount">0</span>
                                        <span>
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="popover-content d-none">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item addinventory">
                                                <button class="unavailablehandlebtn" data-module="safetystockcount"
                                                    data-type="add" data-targetcolumn="safety_stock"
                                                    data-title="Add Safety Stock" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-add-line"></i> Add Inventory
                                                </button>
                                            </li>
                                            <li class="list-group-item movetoavailable">
                                                <button class="unavailablehandlebtn" data-module="safetystockcount"
                                                    data-type="move" data-targetcolumn="safety_stock"
                                                    data-title="Move Safety Stock to Available" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-arrow-right-line"></i> Move to Available
                                                </button>
                                            </li>
                                            <li class="list-group-item deleteinventory">
                                                <button class="unavailablehandlebtn" data-module="safetystockcount"
                                                    data-type="delete" data-targetcolumn="safety_stock"
                                                    data-title="Delete Safety Stock" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-delete-bin-7-line"></i> Delete inventory
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td class="modalinventory">
                                    <button data-btn="othercount">
                                        <span class="othercount">0</span>
                                        <span>
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="popover-content d-none">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item addinventory">
                                                <button class="unavailablehandlebtn" data-module="othercount"
                                                    data-type="add" data-targetcolumn="other" data-title="Add Other"
                                                    data-toggle="modal" data-target="#handleunavailable">
                                                    <i class="ri-add-line"></i> Add Inventory
                                                </button>
                                            </li>
                                            <li class="list-group-item movetoavailable">
                                                <button class="unavailablehandlebtn" data-module="othercount"
                                                    data-type="move" data-targetcolumn="other"
                                                    data-title="Move Other to Available" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-arrow-right-line"></i> Move to Available
                                                </button>
                                            </li>
                                            <li class="list-group-item deleteinventory">
                                                <button class="unavailablehandlebtn" data-module="othercount"
                                                    data-type="delete" data-targetcolumn="other"
                                                    data-title="Delete Other" data-toggle="modal"
                                                    data-target="#handleunavailable">
                                                    <i class="ri-delete-bin-7-line"></i> Delete inventory
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger resethistoryform" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- modal for handle unavailable inventory --}}
    <div class="modal fade" id="handleunavailable" tabindex="-1" role="dialog"
        aria-labelledby="handleunavailableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="handleunavailableTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="unavailableinventoryform">
                    @csrf
                    <div class="modal-body">
                        <div class="col-12">
                            <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                                placeholder="token" required />
                            <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id"
                                placeholder="user_id">
                            <input type="hidden" value="{{ session(key: 'company_id') }}" class="form-control"
                                name="company_id" placeholder="company_id">
                        </div>
                        <div class="col-12" id="unavailableinventoryformdata">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Save"
                            class="btn btn-primary float-right my-0">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal for handle on hold inventory --}}
    <div class="modal fade" id="onhand" tabindex="-1" role="dialog" aria-labelledby="onhandTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="onhandTitle">On Hand inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="onhandinventoryform">
                    @csrf
                    <div class="modal-body">
                        <div class="col-12">
                            <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                                placeholder="token" required />
                            <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id"
                                placeholder="user_id">
                            <input type="hidden" value="{{ session(key: 'company_id') }}" class="form-control"
                                name="company_id" placeholder="company_id">
                        </div>
                        <div class="col-12" id="onhandinventoryformdata">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Save"
                            class="btn btn-primary float-right my-0">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal for handle available inventory --}}
    <div class="modal fade" id="available" tabindex="-1" role="dialog" aria-labelledby="availableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="availableTitle">Available inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="availableinventoryform">
                    @csrf
                    <div class="modal-body">
                        <div class="col-12">
                            <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                                placeholder="token" required />
                            <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id"
                                placeholder="user_id">
                            <input type="hidden" value="{{ session(key: 'company_id') }}" class="form-control"
                                name="company_id" placeholder="company_id">
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-12">
                                        <select name="available_type" class="form-control" id="available_type">
                                            <option value="adjust">Adjust Available</option>
                                            <option value="move">Move to Unavailable</option>
                                        </select>
                                        <span class="error-msg" id="error-unit" style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-12">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" name='quantity' class="form-control" id="quantity"
                                            value="0">
                                        <span class="error-msg" id="error-unit" style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group d-none moveunavailable">
                                <div class="form-row">
                                    <div class="col-12">
                                        <label for="reason">Reason</label>
                                        <select name="reason" class="form-control" id="reason">
                                            <option value="other">Other (default)</option>
                                            <option value="damaged">Damaged</option>
                                            <option value="quality_control">Quality Control</option>
                                            <option value="safety_stock">Safety Stock</option>
                                        </select>
                                        <span class="error-msg" id="error-unit" style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Save"
                            class="btn btn-primary float-right my-0">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- modal for handle incoming inventory --}}
    <div class="modal fade" id="incoming" tabindex="-1" role="dialog" aria-labelledby="incomingTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incomingTitle">Incoming inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="incoming_purchase_order">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
            var global_response = '';


            // fetch & show products data in table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('inventory.index') }}",
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // Clear and destroy the existing DataTable instance
                        if ($.fn.dataTable.isDataTable('#data')) {
                            $('#data').DataTable().clear().destroy();
                        }

                        // Clear the existing table body
                        $('#tabledata').empty();
                        if (response.status == 200 && response.inventory != '') {
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed
                            $.each(response.inventory, function(key, value) {
                                var UnAvailable = value.damaged + value.quality_control + value
                                    .safety_stock + value.other;
                                $('#tabledata').append(`
                                    <tr> 
                                        <td>${value.name}</td> 
                                        <td>${value.sku != null ? value.sku : '-' }</td>
                                        <td class="modalinventory"> 
                                            <button class="unavailableinventorybtn inventorybtn" data-id=${value.id} data-toggle="modal" data-target="#unavailable">
                                                <span class="unavailablecount">${UnAvailable}</span>
                                                <span class="modalinventoryicon">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </span>
                                            </button> 
                                        </td>
                                        <td>
                                             <button class="availableinventorybtn inventorybtn" data-id=${value.id} data-toggle="modal" data-target="#available">
                                                <span class="availablecount">${value.available}</span>
                                                <span class="modalinventoryicon">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </span>
                                            </button>
                                        </td>
                                        <td>
                                            <button class="onhandinventorybtn inventorybtn" data-id=${value.id} data-toggle="modal" data-target="#onhand">
                                                <span class="onhandcount">${value.on_hand}</span>
                                                <span class="modalinventoryicon">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </span>
                                            </button> 
                                        </td> 
                                        <td>
                                            <button class="incominginventorybtn inventorybtn" data-id=${value.id} data-toggle="modal" data-target="#incoming">
                                                <span class="incomingcount">${value.incoming_count}</span>
                                                <span class="modalinventoryicon">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </span>
                                            </button> 
                                        </td> 
                                    </tr>
                                `);
                                id++;
                                $('[data-toggle="tooltip"]').tooltip('dispose');
                                $('[data-toggle="tooltip"]').tooltip();
                            });

                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize datatable
                                "order": [] // Disable column sorting by default
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            // After appending "No Data Found", re-initialize DataTable so it works properly
                            $('#data').DataTable({});
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
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
                });
            }
            // call function for show data in table
            loaddata();

            $(document).on('click', '.unavailableinventorybtn', function() {
                var id = $(this).data('id');
                $('#unavailable').data('id', id);
                $.each(global_response.inventory, function(key, value) {
                    if (value.id == id) {
                        $('.damagedcount').text(value.damaged);
                        $('.qualitycontrolcount').text(value.quality_control);
                        $('.safetystockcount').text(value.safety_stock);
                        $('.othercount').text(value.other);
                    }
                });
            });

            $('#unavailable .modalinventory>button').on('click', function(e) {
                $('.popover-content').not($(this).siblings('.popover-content')).addClass('d-none');
                var popoverContent = $(this).siblings('.popover-content');
                var clickedbtn = $(this).data('btn');
                var getvalue = $('.' + clickedbtn).text();
                if (getvalue < 1) {
                    popoverContent.find('.movetoavailable button').prop('disabled', true);
                    popoverContent.find('.deleteinventory button').prop('disabled', true);
                } else {
                    popoverContent.find('.movetoavailable button').prop('disabled', false);
                    popoverContent.find('.deleteinventory button').prop('disabled', false);
                }
                // Toggle the 'd-none' class to show/hide the popover
                popoverContent.toggleClass('d-none');

                // Stop event propagation to prevent closing the popover when clicking inside
                e.stopPropagation();
            });

            $('#unavailable').on('hidden.bs.modal', function(e) {
                $('#unavailable').data('id', null);
                // Reset or clean up any changes you made to the modal
                $('.popover-content').addClass('d-none'); // Hide any open popovers

                // Optionally, you can reset the disabled state of the buttons inside the popover
                $('#unavailable .modalinventory .popover-content').each(function() {
                    $(this).find('.movetoavailable button').prop('disabled', false);
                    $(this).find('.deleteinventory button').prop('disabled', false);
                });

                // Reset the counts to 0
                $('.damagedcount').text(0);
                $('.qualitycontrolcount').text(0);
                $('.safetystockcount').text(0);
                $('.othercount').text(0);
            });

            $('.unavailablehandlebtn').on('click', function() {
                $('#unavailableinventoryformdata').html(``);
                var title = $(this).data('title');

                $('#handleunavailableTitle').html('<b>' + title + '</b>');
                $('#unavailable').modal('hide');

                var productid = $('#unavailable').data('id');
                var actiontype = $(this).data('type');
                var targetcolumn = $(this).data('targetcolumn');
                var modulename = $(this).data('module');

                var value = $('.' + modulename).text();

                if (actiontype == 'add') {
                    $('#unavailableinventoryformdata').html(`
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <input type="hidden" id="product_id" name="product_id" value="${productid}">
                                    <input type="hidden" name="targetcolumn" value="${targetcolumn}">
                                    <input type="hidden" name="module" value="${modulename}">
                                    <input type="hidden" name="type" value="${actiontype}">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name='quantity' class="form-control" id="quantity" min="0" value="0">
                                    <span class="error-msg" id="error-unit" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    `);
                } else if (actiontype == 'move') {
                    $('#unavailableinventoryformdata').html(`
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                   <input type="hidden" id="product_id" name="product_id" value="${productid}">
                                    <input type="hidden" name="targetcolumn" value="${targetcolumn}">
                                    <input type="hidden" name="module" value="${modulename}">
                                    <input type="hidden" name="type" value="${actiontype}">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name='quantity' class="form-control" id="quantity" max="${value}" min="0" value="0">
                                    <p>Move up to ${value}</p>
                                    <span class="error-msg" id="error-unit" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    `);
                } else if (actiontype == 'delete') {
                    $('#unavailableinventoryformdata').html(`
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                   <input type="hidden" id="product_id" name="product_id" value="${productid}">
                                    <input type="hidden" name="targetcolumn" value="${targetcolumn}">
                                    <input type="hidden" name="module" value="${modulename}">
                                    <input type="hidden" name="type" value="${actiontype}">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name='quantity' class="form-control" id="quantity" max="${value}" min="0" value="0">
                                    <p>Delete up to ${value}</p>
                                    <span class="error-msg" id="error-unit" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    `);
                }
            });

            $('#unavailableinventoryform').on('submit', function(e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                var id = $('#product_id').val();
                let quantityUpdateUrl = "{{ route('inventory.quantityupdate', '__id__') }}".replace(
                    '__id__', id);
                $.ajax({
                    type: 'put',
                    url: quantityUpdateUrl,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#handleunavailable').modal('hide');
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });;
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
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
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


            $(document).on('click', '.availableinventorybtn', function() {
                var id = $(this).data('id');
                $('#available').data('id', id);
            });

            $(document).on('change', '#available_type', function() {
                var value = $(this).val();
                var modal = $(this).closest('.modal'); // Get the closest modal containing the select box

                // Find the quantity input within the same modal
                var quantityInput = modal.find('#quantity');
                quantityInput.val(0);
                if (value == 'move') {
                    modal.find('.moveunavailable').removeClass(
                        'd-none'); // Show reason dropdown in this modal
                    quantityInput.attr('min', 0); // Set min value to 0 for this modal's quantity
                } else {
                    modal.find('.moveunavailable').addClass('d-none'); // Hide reason dropdown in this modal
                    quantityInput.removeAttr('min'); // Remove min value from this modal's quantity
                }
            });

            $('#available').on('hidden.bs.modal', function(e) {
                $('#available').data('id', null);
                var modal = $(this).closest('.modal'); // Get the closest modal containing the select box
                modal.find('.moveunavailable').addClass('d-none');
                // Find the quantity input within the same modal
                var quantityInput = modal.find('#quantity');
                quantityInput.val(0).removeAttr('min');
                $('#available_type').val('adjust');
            });

            $('#availableinventoryform').on('submit', function(e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                var id = $('#available').data('id');
                let availablequantityUpdateUrl =
                    "{{ route('inventory.availablequantityupdate', '__id__') }}".replace('__id__', id);
                $.ajax({
                    type: 'put',
                    url: availablequantityUpdateUrl,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#available').modal('hide');
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });;
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
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
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


            $(document).on('click', '.onhandinventorybtn', function() {
                $('#onhandinventoryformdata').html(``);
                var productid = $(this).data('id');
                var onhandcount = $(this).find('.onhandcount').text();
                $('#onhandinventoryformdata').html(`
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12">
                                <input type="hidden" id="product_id" name="product_id" value="${productid}">
                                <label for="quantity">Quantity</label>
                                <input type="number" name='quantity' class="form-control" id="quantity" value="0">
                                <p>(Original quantity: ${onhandcount})</p>
                                <span class="error-msg" id="error-unit" style="color: red"></span>
                            </div>
                        </div>
                    </div>
                `);
            });

            $('#onhandinventoryform').on('submit', function(e) {
                e.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                var id = $('#product_id').val();
                let onhandquantityUpdateUrl = "{{ route('inventory.onhandquantityupdate', '__id__') }}"
                    .replace('__id__', id);
                $.ajax({
                    type: 'put',
                    url: onhandquantityUpdateUrl,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#onhand').modal('hide');
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });;
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
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
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


            $(document).on('click', '.incominginventorybtn', function() {
                loadershow();
                var id = $(this).data('id');
                var incomingInventoryUrl = "{{ route('inventory.incominginventory', '__inventoryId__') }}"
                    .replace('__inventoryId__', id);
                $('#incoming_purchase_order').html('');
                $.ajax({
                    type: 'GET',
                    url: incomingInventoryUrl,
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.incominginventory != '') { 
                            var content = '';
                            $.each(response.incominginventory, function(id, count) {
                                var purchaeorderurl =
                                    "{{ route('admin.viewpurchase', '__viewId__') }}"
                                    .replace('__viewId__', id);
                                content += ` 
                                    <tr>
                                        <td class="text-left"><a class="font-weight-bolder text-primary" href="${purchaeorderurl}">#PO${id}</a></td>
                                        <td class="text-right">${count}</td>        
                                    </tr>
                                `;
                            })

                            $('#incoming_purchase_order').html(`
                                <table class="w-100 table ">
                                    <tbody>
                                        ${content}
                                    </tbody}    
                                </table>    
                            `);
                        } else {
                            $('#incoming_purchase_order').html(`
                                <p>
                                    Create 
                                    <a class="font-weight-bolder text-primary" href="{{ route('admin.addpurchase') }}">Purchase Order</a>
                                </p>    
                            `);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
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
                });
            });

        });
    </script>
@endpush
