@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')

@section('page_title')
    {{ config('app.name') }} - Reminder
@endsection
@section('table_title')
    Reminder
@endsection

@section('style')
    {{-- lead style --}}
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

        .multiselect-container>li>a>label {
            padding: 3px 16px 3px 23px !important;
        }

        .multiselect {
            border: 0.5px solid #00000073;
        }
    </style>
    {{-- right sidebar style      --}}
    <style>
        /* The side navigation menu */
        .sidenav {
            height: 100%;
            /* 100% Full-height */
            width: 0;
            /* 0 width - change this with JavaScript */
            position: fixed;
            /* Stay in place */
            z-index: 99;
            /* Stay on top */
            top: 0%;
            background-color: #ffffff;
            /* Black*/
            overflow-x: hidden;
            /* Disable horizontal scroll */
            padding-top: 60px;
            /* Place content 60px from the top */
            transition: 0.5s;
            /* 0.5 second transition effect to slide in the sidenav */
        }

        /* The navigation menu links */
        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #972721 !important;
            display: block;
            transition: 0.3s
        }

        /* When you mouse over the navigation links, change their color */
        .sidenav a:hover,
        .offcanvas a:focus {
            color: #f1f1f1;
        }

        /* Position and style the close button (top right corner) */
        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        /* Style page content - use this if you want to push the page content to the right when you open the side navigation */
        #main {
            transition: margin-left .5s;
            padding: 20px;
        }

        .sidenav {
            right: 0;
        }

        /* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
        @media screen and (max-height: 450px) {
            .sidenav {
                padding-top: 15px;
            }

            .sidenav a {
                font-size: 18px;
            }
        }

        .sidenav {
            right: 0;
        }

        /* .multiselect-container {
                width: 300px;
                max-height: 300px;
                overflow: auto;
        } */

        .sidenav .btn-group {
            width: 100%;
            text-align: left;
        }

        .sidenav .btn-group .multiselect {
            text-align: left;
        }

        .sidenav .btn-group li label {
            font-size: 15px;
        }

        span.multiselect-selected-text {
            text-wrap: wrap;
        }
    </style>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('advancefilter')
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="row p-3">
            <div class="col-md-12">
                <h4>Advanced Filters</h4>
            </div>
            <div class="col-md-12" id="citydiv">
                <label for="city" class="form-label float-left mt-1">City : </label>
                <select name="city" class="form-control multiple" id="city" multiple>
                    <option value="" disabled selected>-- Select City --</option>
                </select>
            </div>
            <div class="col-md-12 mt-2" id="customerdiv">
                <label for="customer" class="form-label float-left mt-1">Customers : </label>
                <select name="customer" class="form-control multiple" id="customer" multiple>
                    <option value="" disabled selected>-- Select Customer --</option>
                </select>
            </div>
            <div class="col-md-12 mt-2" id="reminder_status_div">
                <label for="reminder_status" class="form-label float-left mt-1">Reminder Status : </label>
                <select name="reminder_status" class="form-control multiple" id="reminder_status" multiple>
                    <option value="" disabled selected>-- Select Reminder Status --</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col-md-12 mt-2">
                <label for="pincode" class="form-label float-left">Pincode :</label>
                <input type="text" id="pincode" placeholder="Pincode" class="form-input form-control">
            </div>
            <div class="col-md-12 mt-2">
                <label for="last_service" class="form-label float-left">Service Completed Date:</label>
                <input type="date" id="last_service" class="form-input form-control">
            </div>
            <div class="col-md-12 mt-2">
                <label for="next_reminder" class="form-label float-left ">Next Reminder:</label>
                <input type="date" id="next_reminder" class="form-input form-control">
            </div>
            <div class="col-md-12 mt-2">
                <label for="fromdate" class="form-label float-left ">From:</label>
                <input type="date" id="fromdate" class="form-input form-control  float-left">
            </div>
            <div class="col-md-12 mt-2">
                <label for="todate" class="form-label  float-left ">To:</label>
                <input type="date" id="todate" class="form-input form-control float-left">
                <span id="invaliddate" class="font-weight-bold text-danger" style="float: left;"></span>
            </div>
            <div class="col-md-12 mt-3">
                <button class="btn btn-sm btn-rounded btn-primary filtersubmit">Submit</button>
                <button class="btn btn-sm btn-danger btn-rounded removepopupfilters" onclick="closeNav()">cancel</button>
            </div>
        </div>
    </div>
    <div class="col-md-12 text-right pr-5">
        <div class="m-2 float-right">
            <!-- Use any element to open the sidenav -->
            <button data-toggle="tooltip" data-placement="bottom" data-original-title="Advance filters" onclick="openNav()" class="btn btn-sm btn-rounded btn-info">
                <i class="ri-filter-line"></i>
            </button>
            <button data-toggle="tooltip" data-placement="bottom" data-original-title="Reset Filters" class="btn btn-info btn-rounded btn-sm removefilters">
                <i class="ri ri-refresh-line"></i>
            </button>
        </div>
        <div class="m-2 float-right">
            <select class="advancefilter multiple form-control w-100" id="area" multiple="multiple">
                <option disabled selected>-- Select Area --</option>
            </select>
        </div>  
        <div class="m-2 float-right">
            <input type="radio" class="is_active advancefilter" id="paid" name="service_type" value="paid">
            <label for="paid">Paid</label>
            <input type="radio" class="is_active advancefilter" id="free" name="service_type" value="free">
            <label for="free">Free</label>
            <input type="radio" class="is_active advancefilter" value="all" checked id="all" name="service_type">
            <label for="all">All</label>
        </div>
    </div>

    @if (session('user_permissions.remindermodule.reminder.add') == '1')
        @section('addnew')
            {{ route('admin.addreminder') }}
        @endsection
        @section('addnewbutton')
            <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Reminder" class="btn btn-sm btn-primary">
                <span class="">+ Reminder</span>
            </button>
        @endsection
    @endif

@endsection


@section('table-content')
    <table id="data"
        class="table table-bordered w-100 table-responsive-sm table-responsive-md table-responsive-lg  table-striped text-center">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Details</th>
                <th>Reminder Status</th>
                <th>createdat</th>
                <th>Next Reminder</th>
                <th>Area</th>
                <th>Pincode</th>
                <th>&nbsp;&nbsp;&nbsp;Action&nbsp;&nbsp;&nbsp;</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        /* Simple appearence with animation AN-1*/
        function openNav() {
            var screenWidth = window.innerWidth;
            var width;

            if (screenWidth >= 320 && screenWidth <= 768) {
                width = "100%";
            } else if (screenWidth >= 769 && screenWidth <= 1024) {
                width = "50%";
            } else if (screenWidth >= 1025 && screenWidth <= 1200) {
                width = "30%";
            } else {
                width = "25%";
            }
            document.getElementById("mySidenav").style.width = width;
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }
        /* Simple appearence with animation AN-1*/
    </script>
    <script>
        $(document).ready(function() {


            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            $('#city').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select City --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#city').multiselect('rebuild');
            });
            $('#customer').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Customer --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#customer').multiselect('rebuild');
            });
            $('#area').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Area --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#area').multiselect('rebuild');
            });
            $('#reminder_status').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Reminder Status --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#reminder_status').multiselect('rebuild');
            });

            function getAreaNames() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('remindercustomer.area') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
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
                            toastr.error(errorMessage);
                            reject(errorMessage);
                        }
                    });
                });
            }

            function getCustomerName() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('remindercustomer.customers') }}",
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }} ",
                            user_id: "{{ session()->get('user_id') }} "
                        },
                        success: function(response) {
                            resolve(response);
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
                            toastr.error(errorMessage);
                            reject(errorMessage);
                        }
                    });
                });
            }
            function getCitiesname() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('remindercustomer.city') }}",
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }} ",
                            user_id: "{{ session()->get('user_id') }} "
                        },
                        success: function(response) {
                            resolve(response);
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
                            toastr.error(errorMessage);
                            reject(errorMessage);
                        }
                    });
                });
            }

            function loadFilters() {
                return new Promise((resolve, reject) => {
                    var filterData = JSON.parse(sessionStorage.getItem('filterData'));
                    if (filterData) {
                        $.each(filterData, function(key, value) {
                            if (value != ' ') {
                                $('#' + key).val(value); // Removed `, true`
                            }
                            if (key === 'activestatusvalue') {
                                $('input[name="service_type"][value="' + value + '"]').prop(
                                    'checked',
                                    true);
                            }
                        });
                        advancefilters();
                        // Check only the first option
                        $('#city option:first').prop('selected', true);
                        $('#customer option:first').prop('selected', true);
                        $('#area option:first').prop('selected', true);
                        $('#reminder_status option:first').prop('selected', true);

                        // Trigger change event to ensure multiselect UI updates
                        $('#city, #area , #reminder_status,#customer').multiselect('refresh');

                        sessionStorage.removeItem('filterData');
                        loaderhide();
                        resolve(); // Resolve the promise here after all actions
                    } else {
                        // If no filter data, resolve immediately
                        resolve();
                        loaddata();
                    }
                });
            }


            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [areaDataResponse,customerDataResponse, cityDataResponse] = await Promise.all([
                        getAreaNames(),
                        getCustomerName(),
                        getCitiesname()
                    ]);


                    // Check if area data is successfully fetched
                    if (areaDataResponse.status == 200 && areaDataResponse.area != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(areaDataResponse.area, function(key, value) {
                            var optionValue = value;
                            $('#area').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#area').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (areaDataResponse.status == 500) {
                        toastr.error(areaDataResponse.message);
                    } else {
                        $('#area').append(`<option> No area Found </option>`);
                    }

                    // Check if customer data is successfully fetched
                    if (customerDataResponse.status == 200 && customerDataResponse.customer != '') {
                        // You can update your HTML with the data here if needed 
                        $.each(customerDataResponse.customer, function(key, value) {
                            var optionValue = value.name;
                            $('#customer').append(
                                `<option value="${value.id}">${optionValue}</option>`);
                        });
                        $('#customer').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (customerDataResponse.status == 500) {
                        toastr.error(customerDataResponse.message);
                    } else {
                        $('#customer').append(`<option> No area Found </option>`);
                    }

                    // Check if city data is successfully fetched
                    if (cityDataResponse.status == 200 && cityDataResponse.city != '') {
                        //         if (response.status == 200 && response.sourcecolumn != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(cityDataResponse.city, function(key, value) {
                            $('#city').append(
                                `<option value="${value.id}">${value.city_name}</option>`);
                        });
                        $('#city').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (cityDataResponse.status == 500) {
                        toastr.error(cityDataResponse.message);
                    } else {
                        $('#city').append(`<option disabled> No City Found </option>`);
                        $('#city').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options
                    }
                    loaderhide();
                    // Load filters
                    await loadFilters();

                    // Further code execution after successful AJAX calls and HTML appending
                    // Your existing logic here

                } catch (error) {
                    console.error('Error:', error);
                    toastr.error("An error occurred while initializing");
                    loaderhide();
                }
            }

            initialize();

            var global_response = '';
            $('#area').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });
            $('#customer').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });
            $('#city').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });
            $('#reminder_status').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });


            // get lead data and set in the table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('reminder.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.reminder != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            var id = 1;
                            $.each(response.reminder, function(key, value) {
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td  class="text-left" >
                                                        <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                                            <b><i class="fas fa-user pr-2"></i></b> ${value.name}
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-envelope pr-2"></i></b>
                                                            <a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a>
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-phone-alt pr-2"></i></b>
                                                            <a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no}</a>
                                                        </span>  
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-city pr-2"></i></b>
                                                             ${value.city_name}
                                                        </span>  
                                                    </td>
                                                    <td>
                                                        <select class="reminder_status form-control" data-original-value="${value.reminder_status}" data-reminder_status_id=${value.id} id="reminder_status_${value.id}" name="reminder_status_${value.id}">
                                                           <option value='pending'>Pending</option>
                                                           <option value='in_progress'>In Progress</option>
                                                           <option value='completed'>Completed</option>
                                                        </select>
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.next_reminder_date}</td>
                                                    <td>${value.area}</td>
                                                    <td>${value.pincode}</td>
                                                    <td>
                                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Whatsapp Message">
                                                            <a class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/91${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.remindermodule.reminder.edit') == '1')
                                                            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                                                    <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button> 
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.remindermodule.reminder.delete') == '1')
                                                            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`)
                                $('#reminder_status_' + value.id).val(value.reminder_status);
                                id++;
                                $('[data-toggle="tooltip"]').tooltip('dispose');
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                            var search = {!! json_encode($search) !!}

                            $('#data').DataTable({

                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#tabledata').html(`<tr><td colspan='8' >No Data Found</td></tr>`);
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
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
                        toastr.error(errorMessage);
                    }
                });
            }

            // it is commented beacause it is called base on conditions 
            //call function for loaddata
            // loaddata();


            // view individual reminder data
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.reminder, function(key, reminder) {
                    if (reminder.id == data) {

                        $('#details').append(`
                                                <tr> 
                                                    <td>Customer Name</td>
                                                    <th>${reminder.name !== null ? reminder.name : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>email</td>
                                                    <th>${reminder.email !== null ? reminder.email : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>contact Number</td>
                                                    <th>${reminder.contact_no !== null ? reminder.contact_no : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Address</td>
                                                    <th>${reminder.address !== null ? reminder.address : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Area</td>
                                                    <th>${reminder.area !== null ? reminder.area : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>City</td>
                                                    <th>${reminder.city_name !== null ? reminder.city_name : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Customer Type</td>
                                                    <th>${reminder.customer_type !== null ? reminder.customer_type : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Invoice id</td>
                                                    <th>${reminder.invoice_id !== null ? reminder.invoice_id : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Reminder Status</td>
                                                    <th>${reminder.reminder_status !== null ? reminder.reminder_status : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Service Completed Date</td>
                                                    <th>${reminder.service_completed_date !== null ? reminder.service_completed_date : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Next Reminder Date</td>
                                                    <th>${reminder.next_reminder_date !== null ? reminder.next_reminder_date : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Service type</td>
                                                    <th>${reminder.service_type !== null ? reminder.service_type : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Product Name</td>
                                                    <th>${reminder.product_name !== null ? reminder.product_name : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Amount</td>
                                                    <th>${reminder.amount !== null ? reminder.amount : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Created On</td>
                                                    <th>${reminder.created_at_formatted !== null ? reminder.created_at_formatted : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>Before Service Notes</td>
                                                    <th class='text-wrap'>${reminder.before_service_note !== null ? reminder.before_service_note : ''}</th>
                                                </tr>
                                                <tr>
                                                    <td>After Service Notes</td>
                                                    <th class='text-wrap'>${reminder.after_service_note !== null ? reminder.after_service_note : ''}</th>
                                                </tr>
                     `);
                    }
                });
            });


            // change reminder stage status
            $(document).on('change', '.reminder_status', function() {
                var oldstatus = $(this).data('original-value');
                if (confirm('Are you sure you want to change status?')) {
                    loadershow();
                    var reminderstatusid = $(this).data('reminder_status_id');
                    var fieldid = $(this).attr('id');
                    var reminderstatusvalue = $('#' + fieldid).val();
                    $(this).data('original-value', reminderstatusvalue);
                    let last_service_date;
                    if (reminderstatusvalue == 'completed') {
                        var now = new Date();
                        last_service_date = now.getFullYear() + '-' +
                            ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
                            ('0' + now.getDate()).slice(-2) + 'T' +
                            ('0' + now.getHours()).slice(-2) + ':' +
                            ('0' + now.getMinutes()).slice(-2);
                    }
                    $.ajax({
                        type: 'PUT',
                        url: "{{ route('reminder.changestatus') }}",
                        data: {
                            last_service_date,
                            reminderstatusid,
                            reminderstatusvalue,
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(data) {
                            loaderhide();
                            if (data.status == false) {
                                toastr.error(data.message);
                            } else if (data.status == 500) {
                                toastr.error(data.message);
                                loaderhide();
                            } else {
                                toastr.success(data.message);
                                advancefilters();
                            }
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
                            toastr.error(errorMessage);
                            reject(errorMessage);
                        }
                    });
                    if (reminderstatusvalue == 'completed') {
                        if (confirm('Are you want to create new reminder for this customer?')) {
                            window.location.href = "AddNewReminder/" + reminderstatusid;
                        }
                    }
                } else {
                    loaderhide();
                    var reminderstatusid = $(this).attr('id');
                    $('#' + reminderstatusid).val(oldstatus);
                }
            })

            // lead edit redirect - save advanced filter data as it is on local storage session
            $(document).on("click", '.editbtn', function() {
                editid = $(this).data('id');
                // loadershow();
                pincode = $('#pincode').val();
                fromdate = $('#fromdate').val();
                todate = $('#todate').val();
                reminder_status = $('#reminder_status').val();
                customer = $('#customer').val();
                city = $('#city').val();
                area = $('#area').val();
                last_service = $('#last_service').val();
                next_reminder = $('#next_reminder').val();
                activestatusvalue = $('input[name="service_type"]:checked').val();

                data = {
                    pincode,
                    fromdate,
                    todate,
                    reminder_status,
                    customer,
                    city,
                    area,
                    last_service,
                    next_reminder,
                    activestatusvalue
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                // console.log(data);
                window.location.href = "EditReminder/" + editid;
            });

            // lead delete
            $(document).on("click", ".dltbtn", function() {

                if (confirm("Are you Sure to delete this record")) {
                    loadershow();
                    var id = $(this).data('uid');
                    var row = this;

                    $.ajax({
                        type: 'PUT',
                        url: "{{ route('reminder.delete') }}",
                        data: {
                            id: id,
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(data) {
                            if (data.status == false) {
                                toastr.error(data.message)
                            } else if (data.status == 500) {
                                toastr.error(data.message);
                            } else {
                                toastr.success(data.message);
                                $(row).closest("tr").fadeOut();
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
                            toastr.error(errorMessage);
                            reject(errorMessage);
                        }
                    });
                }
            })


            // advancefilters
            function advancefilters() {

                fromdate = $('#fromdate').val();
                todate = $('#todate').val();

                if (fromdate != '' && todate == '') {
                    todate = fromdate;
                    $('#todate').val(todate);
                }



                pincode = $('#pincode').val();
                fromdate = $('#fromdate').val();
                todate = $('#todate').val();
                reminder_status = $('#reminder_status').val();
                customer = $('#customer').val();
                city = $('#city').val();
                area = $('#area').val();
                last_service = $('#last_service').val();
                next_reminder = $('#next_reminder').val();
                activestatusvalue = $('input[name="service_type"]:checked').val();
                var fromDate = new Date(fromdate);
                var toDate = new Date(todate);

                if (fromDate > toDate) {
                    $('#invaliddate').text('Invalid Date');
                } else {
                    $('#invaliddate').text(' ');
                }

                var data = {
                    user_id: "{{ session()->get('user_id') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    token: "{{ session()->get('api_token') }}"
                };
                if (fromdate != '' && todate != '' && !(fromDate > toDate)) {
                    data.fromdate = fromdate;
                    data.todate = todate;
                }
                if (pincode != '') {
                    data.pincode = pincode;
                }
                if (reminder_status != '') {
                    data.reminder_status = reminder_status;
                }
                if (customer != '') {
                    data.customer = customer;
                }
                if (city != '') {
                    data.city = city;
                }
                if (area != '') {
                    data.area = area;
                }
                if (last_service != '') {
                    data.last_service = last_service;
                }
                if (next_reminder != '') {
                    data.next_reminder = next_reminder;
                }
                if (activestatusvalue != '') {
                    data.activestatusvalue = activestatusvalue;
                }

                if (fromdate == '' && todate == '' && pincode == '' && reminder_status == ''&& customer == '' && city == '' &&
                    area == '' && last_service == '' &&
                    next_reminder == '' && activestatusvalue == '') {
                    loaddata();
                }
                if ((fromdate != '' && todate != '' && !(fromDate > toDate)) || pincode != '' || reminder_status !=
                    ''|| customer != '' || city != '' || area != '' ||
                    last_service != '' || next_reminder != '' || activestatusvalue != '') {
                    loadershow();
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('reminder.index') }}",
                        data: data,
                        success: function(response) {
                            if (response.status == 200 && response.reminder != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                global_response = response;
                                var id = 1;
                                $.each(response.reminder, function(key, value) {
                                    $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td  class="text-left" >
                                                        <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                                            <b><i class="fas fa-user pr-2"></i></b> ${value.name}
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-envelope pr-2"></i></b>
                                                            <a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a>
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-phone-alt pr-2"></i></b>
                                                            <a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no}</a>
                                                        </span>  
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-city pr-2"></i></b>
                                                             ${value.city_name}
                                                        </span>  
                                                    </td>
                                                    <td>
                                                        <select class="reminder_status form-control" data-original-value="${value.reminder_status}" data-reminder_status_id=${value.id} id="reminder_status_${value.id}" name="reminder_status_${value.id}">
                                                           <option value='pending'>Pending</option>
                                                           <option value='in_progress'>In Progress</option>
                                                           <option value='completed'>Completed</option>
                                                        </select>
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.next_reminder_date}</td>
                                                    <td>${value.area}</td>
                                                    <td>${value.pincode}</td>
                                                    <td>
                                                        <span>
                                                            <a title="Send Whatapp Message" class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/91${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.remindermodule.reminder.edit') == '1')
                                                            <span>
                                                                    <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button> 
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.remindermodule.reminder.delete') == '1')
                                                            <span>
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`);
                                    $('#reminder_status_' + value.id).val(value
                                        .reminder_status);
                                    id++;
                                });
                                var search = {!! json_encode($search) !!}

                                $('#data').DataTable({

                                    "search": {
                                        "search": search
                                    },
                                    "destroy": true, //use for reinitialize datatable
                                });
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                $('#tabledata').html(`<tr><td colspan='8' >No Data Found</td></tr>`);
                            }
                            loaderhide();
                            // You can update your HTML with the data here if needed
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
                            toastr.error(errorMessage);
                            reject(errorMessage);
                        }
                    });
                }
            }


            $('.advancefilter').on('change', function() {
                advancefilters();
            });
            $('.filtersubmit').on('click', function(e) {
                e.preventDefault();
                advancefilters();
                closeNav()
            });

            //remove advnaced filtres only (sidebar filtres) 
            $('.removepopupfilters').on('click', function() {
                $('#pincode').val('');
                $('#fromdate').val('');
                $('#todate').val('');
                $('#last_service').val('');
                $('#next_reminder').val('');
                $('#invaliddate').text(' ');

                $('#customer option').prop('selected', false);
                if ($("#customer option:not(:disabled)").length > 0) {
                    $("#customer").prepend('<option value="" disabled selected>-- Select Customer --</option>');
                }
                $('#customer option:first').prop('selected', true);
                $('#customer').multiselect('refresh');

                $('#city option').prop('selected', false);
                if ($("#city option:not(:disabled)").length > 0) {
                    $("#city").prepend('<option value="" disabled selected>-- Select City --</option>');
                }
                $('#city option:first').prop('selected', true);
                $('#city').multiselect('refresh');

                $('#reminder_status option').prop('selected', false);
                if ($("#reminder_status option:not(:disabled)").length > 0) {
                    $("#reminder_status").prepend(
                        '<option value="" disabled selected>-- Select Reminder Status --</option>');
                }
                $('#reminder_status option:first').prop('selected', true);
                $('#reminder_status').multiselect('refresh');
                advancefilters();
            });

            // remove all filters
            $('.removefilters').on('click', function() {
                $('#pincode').val('');
                $('#fromdate').val('');
                $('#todate').val('');
                $('#last_service').val('');
                $('#next_reminder').val('');
                $('#invaliddate').text(' ');
                $("input[name='service_type'][value='all']").prop("checked", true);
                // Uncheck all options
                $('#area option').prop('selected', false);
                $('#customer option').prop('selected', false);
                $('#city option').prop('selected', false);
                $('#reminder_status option').prop('selected', false);

                // prepend disabled option if it doesnt exist
                if ($("#area option:not(:disabled)").length > 0) {
                    $("#area").prepend('<option value="" disabled selected>-- Select Area --</option>');
                }
                if ($("#reminder_status option:not(:disabled)").length > 0) {
                    $("#reminder_status").prepend(
                        '<option value="" disabled selected>-- Select Reminder Status --</option>');
                }
                if ($("#customer option:not(:disabled)").length > 0) {
                    $("#customer").prepend('<option value="" disabled selected>-- Select Customer --</option>');
                }
                if ($("#city option:not(:disabled)").length > 0) {
                    $("#city").prepend('<option value="" disabled selected>-- Select City --</option>');
                }

                // Check only the first option
                $('#area option:first').prop('selected', true);
                $('#customer option:first').prop('selected', true);
                $('#city option:first').prop('selected', true);
                $('#reminder_status option:first').prop('selected', true);

                // Refresh the multiselect dropdown to reflect changes
                $('#area').multiselect('refresh');
                $('#customer').multiselect('refresh');
                $('#city').multiselect('refresh');
                $('#reminder').multiselect('refresh');
                loaddata();
            });




        });
    </script>
@endpush
