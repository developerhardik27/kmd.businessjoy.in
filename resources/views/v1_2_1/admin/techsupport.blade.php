@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')

@section('page_title')
    {{ config('app.name') }} - Technical Support
@endsection
@section('table_title')
    Technical Support
@endsection

@section('style')
    {{-- customersupport style --}}
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
            <div class="col-md-12">
                <label for="last_call" class="form-label float-left  ">Last Call:</label>
                <input type="date" id="last_call" class="form-input form-control  ">
            </div>
            <div class="col-md-12">
                <label for="fromdate" class="form-label float-left ">From:</label>
                <input type="date" id="fromdate" class="form-input form-control  float-left ">
            </div>
            <div class="col-md-12">
                <label for="todate" class="form-label  float-left ">To:</label>
                <input type="date" id="todate" class="form-input form-control float-left ">
                <span id="invaliddate" class="font-weight-bold text-danger" style="float: left;"></span>
            </div>
            <div class="col-md-12 mt-3">
                <button class="btn btn-sm btn-rounded btn-primary filtersubmit">Submit</button>
                <button class="btn btn-sm btn-danger btn-rounded removepopupfilters" onclick="closeNav()">cancel</button>
            </div>
        </div>
    </div>
    <div class="col-md-12 text-right pr-5">
        <select class="advancefilter multiple form-control w-100 m-2" id="advancestatus" multiple="multiple">
            <option disabled selected>-- Select status --</option>
            <option value='pending'>Pending</option>
            <option value='in_progress'>In Progress</option>
            <option value='resolved'>Resolved</option>
            <option value='cancelled'>Cancelled</option>
        </select>
        @if (session('user_permissions.adminmodule.techsupport.alldata') == '1')
            <select name="assignedto" class="form-control multiple advancefilter m-2" id="assignedto" multiple>
                <option value="" disabled selected>-- Select Assigned To --</option>
            </select>
        @endif
        <!-- Use any element to open the sidenav -->
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="AdvanceFilters" onclick="openNav()"
            class="btn btn-sm btn-rounded btn-info m-2">
            <i class="ri-filter-line"></i>
        </button>
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="FilterRefresh"
            class="btn btn-info btn-sm removefilters m-2">
            <i class="ri-refresh-line"></i>
        </button>
    </div>

    @if (session('user_permissions.adminmodule.techsupport.add') == '1')
        @section('addnew')
            {{ route('admin.addtechsupport') }}
        @endsection
        @section('addnewbutton')
            <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add Ticekt"
                class="btn btn-sm btn-primary">
                <span class="">+ Ticket</span>
            </button>
        @endsection
    @endif
@endsection


@section('table-content')
    <table id="data"
        class="table table-bordered w-100  table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped text-center">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Details</th>
                <th>Ticket Number</th>
                <th>Complain Desc.</th>
                <th>Status</th>
                <th>createdon</th>
                @if (session('user_permissions.adminmodule.techsupport.edit') == '1' ||
                        session('user_permissions.adminmodule.techsupport.delete') == '1')
                    <th>Action</th>
                @endif

            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        // advance filter sidebar
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

            // refresh tooltip for dynamic data
            function managetooltip() {
                $('body').find('[data-toggle="tooltip"]').tooltip('dispose');
                // Reinitialize tooltips
                $('body').find('[data-toggle="tooltip"]').tooltip();
            }

            // add/remove disabled option on change assigned to  dynamic
            $('#assignedto').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Assigned To --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#assignedto').multiselect('rebuild');
            });

            // add/remove disabled option on change adnvace status to  dynamic
            $('#advancestatus').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Status --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#advancestatus').multiselect('rebuild');
            });

            // get user data and return new promise
            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.techsupportindex') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }

            // get filterd tech data if has any filter otherwise called without filter and return new promise
            function loadFilters() {
                return new Promise((resolve, reject) => {
                    var filterData = JSON.parse(sessionStorage.getItem('filterData'));
                    if (filterData) {
                        $.each(filterData, function(key, value) {
                            if (value != ' ') {
                                $('#' + key).val(value);
                            }

                        });
                        advancefilters();
                        $('#advancestatus option:first').prop('selected', true);
                        $('#assignedto option:first').prop('selected', true);
                        sessionStorage.removeItem('filterData');

                        // Trigger change event to ensure multiselect UI updates
                        $('#advancestatus, #assignedto').multiselect('refresh');

                        sessionStorage.removeItem('filterData');
                        resolve(); // Resolve the promise here after all actions
                    } else {
                        // If no filter data, resolve immediately
                        resolve();
                        loaddata();
                    }
                });
            }

            // intialize  function async and await
            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [userDataResponse] = await Promise.all([
                        getUserData()
                    ]);

                    // Check if user data is successfully fetched
                    if (userDataResponse.status == 200 && userDataResponse.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(userDataResponse.user, function(key, value) {
                            var optionValue = value.firstname + ' ' + value.lastname;
                            $('#assignedto').append(
                                `<option value="${value.id}">${optionValue}</option>`);
                        });
                        $('#assignedto').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options
                        loaderhide();
                    } else if (userDataResponse.status == 500) {
                        toastr.error(userDataResponse.message);
                        loaderhide();
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                        loaderhide();
                    }

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
            // make multiple dropdown to designable multiple dropdown
            $('#assignedto').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });

            $('#advancestatus').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });

            // get and set customer support history list in the table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('techsupport.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.techsupport != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            var id = 1;
                            $.each(response.techsupport, function(key, value) {
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td  class="text-left" >
                                                        <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                                            <b><i class="fas fa-user pr-2"></i></b> ${value.first_name} ${value.last_name}
                                                        </span>
                                                        <span class="d-flex mb-2">
                                                            <b><i class="fas fa-envelope pr-2"></i></b>
                                                            <a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a>
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-phone-alt pr-2"></i></b>
                                                            <a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no}</a>
                                                        </span>  
                                                        @if (session('user_id') == 1)
                                                            <span>
                                                                <b><i class="fas fa-building pr-2"></i></b> ${value.company_name} 
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span  class="d-inline-block" style="max-width: 150px;">
                                                        <div> ${value.ticket} </div>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span  class="d-inline-block text-truncate" style="max-width: 150px;">
                                                        <div> ${value.description} </div>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                                            <select class="status form-control-sm" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
                                                                <option disabled selected>status</option>
                                                                <option value='pending'>Pending</option>
                                                                <option value='in_progress'>In Progress</option>
                                                                <option value='resolved'>Resolved</option>
                                                                <option value='cancelled'>Cancelled</option>
                                                            </select>
                                                       @else
                                                        ${value.status}
                                                       @endif
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    @if (session('user_permissions.adminmodule.techsupport.edit') == '1' ||
                                                            session('user_permissions.adminmodule.techsupport.delete') == '1')
                                                        <td>
                                                            @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Message">
                                                                    <a title="Send Whatapp Message" class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                        <i class="ri-whatsapp-line text-white"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Ticket">
                                                                    <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button>  
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.adminmodule.techsupport.delete') == '1')
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Ticket">
                                                                    <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
                                                                        <i class="ri-delete-bin-fill"></i>
                                                                    </button>
                                                                </span>
                                                            @endif
                                                        </td>  
                                                    @endif   
                                                </tr>`)
                                $('#status_' + value.id).val(value.status);
                                id++;
                            });

                            managetooltip();
                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize jquery datatable
                            });
                            loaderhide();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            $('#tabledata').html('');
                            $('#data').append(`<tr><td colspan='7' >No Data Found</td></tr>`);
                            loaderhide();
                        }
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

            // show individual customer support history record into the popupbox
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.techsupport, function(key, ticket) {
                    if (ticket.id == data) {
                        // Ensure ticket.attachment is an array
                        let attachments = Array.isArray(ticket.attachment) ?
                            ticket.attachment :
                            (ticket.attachment ? JSON.parse(ticket.attachment) : []);
                        $('#details').append(`
                            <tr> 
                                <td>Ticket Number</td>
                                <th>${ticket.ticket}</th>
                            </tr> 
                            <tr> 
                                <td>first name</td>
                                <th>${ticket.first_name}</th>
                            </tr> 
                            <tr> 
                                <td>last name</td>
                                <th>${ticket.last_name}</th>
                            </tr> 
                            <tr>
                                <td>email</td>
                                <th>${ticket.email}</th>
                            </tr>
                            <tr>
                                <td>contact Number</td>
                                <th>${ticket.contact_no}</th>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <th>${ticket.status.replace(/_/g, ' ')}</th>
                            </tr>
                            <tr>
                                <td>Module Name</td>
                                <th>${ticket.module_name}</th>
                            <tr>
                                <td>Issue type</td>
                                <th>${ticket.issue_type}</th>
                            </tr>
                            <tr>
                                <td>Created On</td>
                                <th>${ticket.created_at_formatted}</th>
                            </tr>
                            <tr>
                                <td >Notes</td>
                                <th class='text-wrap'>${ticket.description != null ? ticket.description : '-'}</th>
                            </tr>
                            <tr>
                                <td >Remarks</td>
                                <th class='text-wrap'>${ticket.remarks != null ? ticket.remarks : '-'}</th>
                            </tr>
                            <tr>
                                <td> Attachments </td>
                                <th>
                                    ${attachments.length > 0
                                        ? attachments.map(attachment => 
                                            `<a class='text-primary font-weight-bold' href='/uploads/files/${attachment}' target='_blank'>${attachment}</a>`
                                        ).join('<br>') // Display each attachment on a new line
                                        : '-'
                                    }
                                </th>
                            </tr>
                        `);
                    }
                });
            });


            // change customer support status
            $(document).on('change', '.status', function() {
                var oldstatus = $(this).data('original-value');
                if (confirm('Are you Sure That to change status  ?')) {
                    loadershow();
                    var statusid = $(this).data('statusid');
                    var fieldid = $(this).attr('id');
                    var statusvalue = $('#' + fieldid).val();
                    $(this).data('original-value', statusvalue);
                    $.ajax({
                        type: 'PUT',
                        url: "{{ route('techsupport.changestatus') }}",
                        data: {
                            statusid: statusid,
                            statusvalue: statusvalue,
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
                        }
                    });
                } else {
                    loaderhide();
                    var fieldid = $(this).attr('id');
                    $('#' + fieldid).val(oldstatus);
                }
            })

            //  on click edit button this will be save advanced filter data on 
            // local server session and redirect update page
            $(document).on("click", '.editbtn', function() {
                editid = $(this).data('id');
                // loadershow();
                fromdate = $('#fromdate').val();
                todate = $('#todate').val();
                advancestatus = $('#advancestatus').val();
                assignedto = $('#assignedto').val();
                last_call = $('#last_call').val();


                data = {
                    fromdate,
                    todate,
                    advancestatus,
                    assignedto,
                    last_call
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                // console.log(data);
                window.location.href = "EditTechsupport/" + editid;
            });


            // delete customer support record
            $(document).on("click", ".dltbtn", function() {

                if (confirm("Are you Sure that to delete this record")) {
                    loadershow();
                    var id = $(this).data('uid');
                    var row = this;

                    $.ajax({
                        type: 'PUT',
                        url: "{{ route('techsupport.delete') }}",
                        data: {
                            id: id,
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(data) {
                            loaderhide();
                            if (data.status == false) {
                                toastr.error(data.message)
                            } else if (data.status == 500) {
                                toastr.error(data.message);
                                loaderhide();
                            } else {
                                toastr.success(data.message);
                                $(row).closest("tr").fadeOut();
                            }

                        }
                    });
                }
            })


            // record filter 
            function advancefilters() {
                fromdate = $('#fromdate').val();
                todate = $('#todate').val();
                advancestatus = $('#advancestatus').val();
                assignedto = $('#assignedto').val();
                LastCall = $('#last_call').val();
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
                if (advancestatus != '') {
                    data.status = advancestatus;
                }
                if (assignedto != '') {
                    data.assignedto = assignedto;
                }
                if (LastCall != '') {
                    data.lastcall = LastCall;
                }

                if (fromdate == '' && todate == '' && advancestatus == '' && assignedto == '' && LastCall == '') {
                    loaddata();
                }
                if ((fromdate != '' && todate != '' && !(fromDate > toDate)) || advancestatus != '' || assignedto !=
                    '' ||
                    LastCall != '') {
                    loadershow();
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('techsupport.index') }}",
                        data: data,
                        success: function(response) {
                            if (response.status == 200 && response.techsupport != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                $('#tabledata').html(' ');
                                global_response = response;
                                var id = 1;
                                $.each(response.techsupport, function(key, value) {
                                    $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td  class="text-left" >
                                                        <span style="cursor:pointer;" class="view-btn d-flex  mb-2" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                                            <b><i class="fas fa-user pr-2"></i></b> ${value.first_name} ${value.last_name}
                                                        </span>
                                                        <span class="d-flex  mb-2">
                                                            <b><i class="fas fa-envelope pr-2"></i></b>
                                                            <a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a>
                                                        </span>
                                                        <span class='d-flex'>
                                                            <b><i class="fas fa-phone-alt pr-2"></i></b>
                                                            <a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no}</a>
                                                        </span>  
                                                    </td>
                                                    <td>
                                                        <span  class="d-inline-block" style="max-width: 150px;">
                                                        <div> ${value.ticket} </div>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                                            <div> ${value.description} </div>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                                            <select class="status form-control-sm" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
                                                                <option disabled selected>status</option>
                                                                <option value='pending'>Pending</option>
                                                                <option value='in_progress'>In Progress</option>
                                                                <option value='resolved'>Resolved</option>
                                                                <option value='cancelled'>Cancelled</option>
                                                            </select>
                                                       @else
                                                            ${value.status}
                                                       @endif
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                     @if (session('user_permissions.adminmodule.techsupport.edit') == '1' ||
                                                             session('user_permissions.adminmodule.techsupport.delete') == '1')
                                                        <td>
                                                            @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                                                <span>
                                                                    <a title="Send Whatapp Message" class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                        <i class="ri-whatsapp-line text-white"></i>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                                                <span>
                                                                    <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button>  
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.adminmodule.techsupport.delete') == '1')
                                                                <span>
                                                                    <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
                                                                        <i class="ri-delete-bin-fill"></i>
                                                                    </button>
                                                                </span>
                                                            @endif
                                                        </td>   
                                                    @endif 
                                                </tr>`)
                                    $('#status_' + value.id).val(value.status);
                                    id++;
                                });

                                $('#data').DataTable({
                                    "destroy": true, //use for reinitialize datatable
                                });
                                loaderhide();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                $('#tabledata').html('');
                                $('#data').append(`<tr><td colspan='7' >No Data Found</td></tr>`);
                                loaderhide();
                            }
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
            }

            // call advance filter function on change advance filter
            $('.advancefilter').on('change', function() {
                advancefilters();
            });

            // call advance filter function on change sidebar filter
            $('.filtersubmit').on('click', function(e) {
                e.preventDefault();
                advancefilters();
                closeNav()
            });

            // remover all filter who has been in the advance filter sidebar
            $('.removepopupfilters').on('click', function() {
                $('#fromdate').val('');
                $('#todate').val('');
                $('#last_call').val('');
                $('#invaliddate').text(' ');
                advancefilters();
            });

            // remove all filters
            $('.removefilters').on('click', function() {
                $('#fromdate').val('');
                $('#todate').val('');
                $('#last_call').val('');
                $('#invaliddate').text(' ');
                // Uncheck all options
                $('#advancestatus option').prop('selected', false);
                $('#assignedto option').prop('selected', false);

                if ($("#advancestatus option:not(:disabled)").length > 0) {
                    $("#advancestatus").prepend(
                        '<option value="" disabled selected>-- Select Status --</option>');
                }
                if ($("#assignedto option:not(:disabled)").length > 0) {
                    $("#assignedto").prepend(
                        '<option value="" disabled selected>-- Select Assigned To --</option>');
                }

                // Check only the first option
                $('#advancestatus option:first').prop('selected', true);
                $('#assignedto option:first').prop('selected', true);

                // Refresh the multiselect dropdown to reflect changes
                $('#advancestatus').multiselect('refresh');
                $('#assignedto').multiselect('refresh');
                loaddata();
            });

        });
    </script>
@endpush
