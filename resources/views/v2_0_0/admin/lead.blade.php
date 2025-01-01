@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')

@section('page_title')
    {{ config('app.name') }} - Lead
@endsection
@section('table_title')
    Lead
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
                                            }
                                        */

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
            <div class="col-md-12" id="assignedtodiv">
                <label for="assignedto" class="form-label float-left mt-1">Assigned To : </label>
                <select name="assignedto" class="form-control multiple" id="assignedto" multiple>
                    <option value="" disabled selected>-- Select User --</option>
                </select>
            </div>
            <div class="col-md-12 mt-3" id="sourcecolumndiv">
                <label for="source" class="form-label float-left mt-1">Source : </label>
                <select name="source" class="form-control multiple" id="source" multiple>
                    <option value="" disabled selected>-- Select Source --</option>
                </select>
            </div>
            <div class="col-md-12">
                <label for="followupcount" class="form-label float-left">FollowUp:</label>
                <input type="number" id="followupcount" placeholder="Number Of Followup" class="form-input form-control">
            </div>
            <div class="col-md-12">
                <label for="last_followup" class="form-label float-left  ">Last FollowUp:</label>
                <input type="date" id="last_followup" class="form-input form-control  ">
            </div>
            <div class="col-md-12">
                <label for="next_followup" class="form-label float-left ">Next FollowUp:</label>
                <input type="date" id="next_followup" class="form-input form-control ">
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
        <div class="m-2 float-right">
            <!-- Use any element to open the sidenav -->
            <button data-toggle="tooltip" data-placement="bottom" data-original-title="AdvanceFilters" onclick="openNav()"
                class="btn btn-sm btn-rounded btn-info">
                <i class="ri-filter-line"></i>
            </button>
            <button data-toggle="tooltip" data-placement="bottom" data-original-title="FilterRefresh"
                class="btn btn-info btn-sm removefilters">
                <i class="ri-refresh-line"></i>
            </button>
        </div>
        <div class="m-2 float-right">
            <select class="advancefilter multiple form-control w-100" id="leadstagestatus" multiple="multiple">
                <option disabled selected>-- Select Lead Stage --</option>
            </select>
        </div>
        <div class="m-2 float-right">
            <select class="advancefilter multiple form-control w-100" id="advancestatus" multiple="multiple">
                <option disabled selected>-- Select status --</option>
            </select>
        </div>
        <div class="m-2 float-right">
            <input type="radio" class="is_active advancefilter" id="qualified" name="status" value="1">
            <label for="qualified">Qualified</label>
            <input type="radio" class="is_active advancefilter" id="disqualified" name="status" value="0">
            <label for="disqualified">Disqualified</label>
            <input type="radio" class="is_active advancefilter" value="all" checked id="all" name="status">
            <label for="all">All</label>
        </div>
    </div>

    @if (session('user_permissions.leadmodule.lead.add') == '1')
        @section('addnew')
            {{ route('admin.addlead') }}
        @endsection
        @section('addnewbutton')
            <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add Lead"
                class="btn btn-sm btn-primary">
                <span class="">+ Lead</span>
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
                <th>Lead Stage</th>
                <th>createdat</th>
                <th>followup</th>
                <th>Source</th>
                <th>&nbsp;&nbsp;&nbsp;Action&nbsp;&nbsp;&nbsp;</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>

    {{-- modal for add call history module  --}}
    <div class="modal fade" id="addcallhistory" tabindex="-1" role="dialog" aria-labelledby="addcallhistoryTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addcallhistoryTitle"><b>Call History</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="leadhistoryform">
                        <div class="row">
                            <input type="hidden" name="company_id" id="company_id">
                            <input type="hidden" name="leadid" id="leadid">
                            <input type="hidden" name="user_id" id="created_by">
                            <input type="hidden" name="token" id="token">
                            <div class="col-12">
                                Datetime:
                                <input type="datetime-local" name="call_date" id="call_date"class="form-control">
                                <span class="error-msg" id="error-call_date" style="color: red"></span>
                            </div>
                            <br />
                            <div class="col-12">
                                Next FollowUp:
                                <input type="datetime-local" name="next_call_date"
                                    id="next_call_date"class="form-control">
                                <span class="error-msg" id="error-next_call_date" style="color: red"></span>
                            </div>
                            <br />
                            <div class="col-12">
                                Notes:
                                <textarea name="history_notes" id="history_notes" cols="10" rows="1" class="form-control"></textarea>

                                <span class="error-msg" id="error-history_notes" style="color: red"></span>
                            </div>
                            <br />
                            <div class="col-12">
                                Status:
                                <select class="form-control" id="call_status" name="call_status">
                                    <option value='Not Interested'>Not Interested</option>
                                    <option value='Not Receiving'>Not Receiving</option>
                                    <option value='New Lead'>New Lead</option>
                                    <option value='Interested'>Interested</option>
                                    <option value='Switch Off'>Switch Off</option>
                                    <option value='Does Not Exist'>Does Not Exist</option>
                                    <option value='Email Sent'>Email Sent</option>
                                    <option value='Wrong Number'>Wrong Number</option>
                                    <option value='By Mistake'>By Mistake</option>
                                    <option value='Positive'>Positive</option>
                                    <option value='Busy'>Busy</option>
                                    <option value='Call Back'>Call Back</option>
                                </select>
                                <span class="error-msg" id="error-call_status" style="color: red"></span>
                            </div>
                            <br>
                            <div class="col-12 mt-2">
                                <input type="checkbox" name="followup" id="followup" value="1"> <label
                                    for="followup">FollowUp</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <input type="submit" value="submit" class="btn btn-sm btn-primary">
                            <button type="button" class="btn btn-danger resethistoryform" data-dismiss="modal">Close
                            </button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal for view call history module  --}}
    <div class="modal fade" id="viewcallhistory" tabindex="-1" role="dialog" aria-labelledby="viewcallhistoryTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewcallhistoryTitle"><b>Call History</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row historyrecord">

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        /* Simple appearence with animation AN-1*/
        function openNav() {
            var screenWidth = window.innerWidth;
            var width;
            // dynamic width of right filter sidebar
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

            $('#assignedto').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select User --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#assignedto').multiselect('rebuild');
            });
            $('#source').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Source --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#source').multiselect('rebuild');
            });
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
            $('#leadstagestatus').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Lead Stage --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#leadstagestatus').multiselect('rebuild');
            });

            $('#history_notes').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['table']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                placeholder: 'Add Notes',
                tabsize: 2,
                height: 100
            });

            let leadstagename = [];
            let leadstatusname = [];

            function getLeadStatusData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('lead.leadstatusname') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            if (response.status == 200 && response
                                .leadstatus != '') {
                                // You can update your HTML with the data here if needed     
                                $.each(response.leadstatus, function(key, value) {
                                    var optionValue = value.leadstatus_name;
                                    leadstatusname.push(optionValue);
                                    $('#advancestatus').append(
                                        `<option value="${optionValue}">${optionValue}</option>`
                                    );
                                });
                                $('#advancestatus').multiselect(
                                    'rebuild'); // Rebuild multiselect after appending options 
                            } else if (response.status == 500) {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message
                                });
                            } else {
                                $('#advancestatus').append(
                                    `<option> No Lead Status Found </option>`);
                            }
                            loaderhide();
                            resolve();
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
                            reject(errorMessage);
                        }
                    });
                });
            }

            function getLeadStageData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('lead.leadstagename') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            if (response.status == 200 && response
                                .lead != '') {
                                // You can update your HTML with the data here if needed     
                                $.each(response.lead, function(key, value) {
                                    var optionValue = value.leadstage_name;
                                    leadstagename.push(optionValue);
                                    $('#leadstagestatus').append(
                                        `<option value="${optionValue}">${optionValue}</option>`
                                    );
                                });
                                $('#leadstagestatus').multiselect(
                                    'rebuild'); // Rebuild multiselect after appending options 
                            } else if (response.status == 500) {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message
                                });
                            } else {
                                $('#leadstagestatus').append(
                                    `<option> No Lead Stage Found </option>`);
                            }
                            loaderhide();
                            resolve();
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
                            reject(errorMessage);
                        }
                    });
                });
            }

            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.leaduserindex') }}",
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
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                            reject(errorMessage);
                        }
                    });
                });
            }

            function getAllSources() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('lead.sourcecolumn') }}",
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
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
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
                                $('input[name="status"][value="' + value + '"]').prop('checked',
                                    true);
                            }
                        });
                        advancefilters();
                        // Check only the first option
                        $('#advancestatus option:first').prop('selected', true);
                        $('#assignedto option:first').prop('selected', true);
                        $('#source option:first').prop('selected', true);
                        $('#leadstagestatus option:first').prop('selected', true);

                        // Trigger change event to ensure multiselect UI updates
                        $('#advancestatus, #assignedto, #source, #leadstagestatus').multiselect('refresh');

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
                    const [userDataResponse, sourceDataResponse, leadStageDataResponse,
                        leadStatusDataResponse
                    ] = await Promise.all([
                        getUserData(),
                        getAllSources(),
                        getLeadStageData(),
                        getLeadStatusData()
                    ]);


                    // Check if user data is successfully fetched
                    if (userDataResponse.status == 200 && userDataResponse.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(userDataResponse.user, function(key, value) {
                            var optionValue = value.firstname + ' ' + value.lastname;
                            $('#assignedto').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#assignedto').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (userDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: userDataResponse.message
                        });
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                    }

                    // Check if source data is successfully fetched
                    if (sourceDataResponse.status == 200 && sourceDataResponse.sourcecolumn != '') {
                        //         if (response.status == 200 && response.sourcecolumn != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(sourceDataResponse.sourcecolumn, function(key, value) {
                            var optionValue = value
                            $('#source').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#source').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (sourceDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: sourceDataResponse.message
                        });
                    } else {
                        $('#source').append(`<option disabled> No Source Found </option>`);
                        $('#source').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options
                    }
                    loaderhide();
                    // Load filters
                    await loadFilters();

                    // Further code execution after successful AJAX calls and HTML appending
                    // Your existing logic here

                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: "error",
                        title: "An error occurred while initializing"
                    });
                    loaderhide();
                }
            }

            initialize();

            var global_response = '';
            $('#assignedto').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });
            $('#source').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });
            $('#advancestatus').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });
            $('#leadstagestatus').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });


            // get lead data and set in the table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('lead.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.lead != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            var id = 1;
                            $.each(response.lead, function(key, value) {
                                var name = (value.first_name != null ? value.first_name : '') +
                                    ' ' + (value.last_name != null ? value.last_name : '')
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td  class="text-left" >
                                                        ${value.company != null ? "<span class='d-flex mb-2'><b><i class='fas fa-building pr-2'></i></b></span>" : '' }
                                                        <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                                            <b><i class="fas fa-user pr-2"></i></b> ${name != '' ? name : '-'}
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-envelope pr-2"></i></b>
                                                            <a href="mailto:${value.email != null ? value.email : ''}" style='text-decoration:none;'>${value.email != null ? value.email : '-'}</a>
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-phone-alt pr-2"></i></b>
                                                            <a href="tel:${value.contact_no != null ? value.contact_no : ''}" style='text-decoration:none;'> ${value.contact_no != null ? value.contact_no : '-'}</a>
                                                        </span>  
                                                        <span class='d-flex mb-2'>
                                                             @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <select class="ml-1 status form-control" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
                                                                ${ 
                                                                    leadstatusname.map(function(optionValue) {
                                                                            return `<option value="${optionValue}">${optionValue}</option>`;
                                                                        }).join('')
                                                                }
                                                            </select>
                                                       @else
                                                         -
                                                       @endif
                                                        </span>    
                                                    </td>
                                                    <td> 
                                                        <select class="leadstage form-control" data-original-value="${value.lead_stage}" data-leadstageid="${value.id}" id="lead_stage_${value.id}" name="lead_stage_${value.id}">
                                                            ${ 
                                                                leadstagename.map(function(optionValue) {
                                                                        return `<option value="${optionValue}">${optionValue}</option>`;
                                                                    }).join('')
                                                            }
                                                        </select>
                                                    </td> 
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.number_of_follow_up}</td>
                                                    <td>${value.source != null ? value.source : '-'}</td>
                                                    <td>
                                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title='Add Call History'>
                                                            <button data-toggle="modal" data-target="#addcallhistory" data-id='${value.id}'  class='btn btn-sm btn-primary mx-0 my-1 leadid' ><i class='ri-time-fill'></i></button>
                                                        </span>
                                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title='View Call History'>
                                                            <button data-toggle="modal" data-target="#viewcallhistory" data-id='${value.id}' class='btn btn-sm btn-info mx-0 my-1 viewcallhistory' ><i class='ri-eye-fill'></i></button>
                                                        </span>
                                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Whatapp Message">
                                                            <a class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Lead">
                                                                 <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                                    <i class="ri-edit-fill"></i>
                                                                 </button>  
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.leadmodule.lead.delete') == '1')
                                                            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete lead">
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`)
                                $('#status_' + value.id).val(value.status);
                                $('#lead_stage_' + value.id).val(value.lead_stage);
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
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#tabledata').html(`<tr><td colspan='7' >No Data Found</td></tr>`);
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            }


            // view individual lead data
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.lead, function(key, lead) {
                    if (lead.id == data) {
                        $('#details').append(`
                                                <tr> 
                                                    <td>Website Url</td>
                                                    <th style="text-transform: none;">
                                                       <a href="${lead.web_url}" target="_blank">${(lead.web_url != null) ? lead.web_url : '-'}</a>
                                                    </th>
                                                </tr> 
                                                <tr> 
                                                    <td>First name</td>
                                                    <th>${(lead.first_name != null) ? lead.first_name : '-'}</th>
                                                </tr> 
                                                <tr> 
                                                    <td>Last name</td>
                                                    <th>${(lead.last_name != null) ? lead.last_name : '-'}</th>
                                                </tr> 
                                                <tr> 
                                                    <td>Company name</td>
                                                    <th>${(lead.company != null) ? lead.company : '-'}</th>
                                                </tr> 
                                                <tr>
                                                    <td>email</td>
                                                    <th>${(lead.email != null)? lead.email : '-'}</th>
                                                </tr>
                                                <tr>
                                                    <td>contact Number</td>
                                                    <th>${(lead.contact_no != null) ? lead.contact_no : '-'}</th>
                                                    </tr>
                                                <tr>
                                                    <td>Title</td>
                                                    <th>${(lead.title != null) ? lead.title :'-'}</th>
                                                </tr>
                                                <tr>
                                                    <td>Budget</td>
                                                    <th>${(lead.budget != null) ? lead.budget : '-'}</th>
                                                </tr>
                                                <tr>
                                                    <td>Audience Type</td>
                                                    <th>${(lead.audience_type != null) ? lead.audience_type : '-'}</th>
                                                </tr>
                                                <tr>
                                                    <td>Customer Type</td>
                                                    <th>${(lead.customer_type != null) ? lead.customer_type : '-'}</th>
                                                </tr>
                                                <tr>
                                                    <td>Status</td>
                                                    <th>${(lead.status !=null) ? lead.status : '-'}</th>
                                                </tr>
                                                <tr>
                                                    <td>Last Follow Up</td>
                                                    <th>${(lead.last_follow_up != null) ? lead.last_follow_up : '-' }</th>
                                                </tr>
                                                <tr>
                                                    <td>Next Follow Up</td>
                                                    <th>${(lead.next_follow_up != null) ? lead.next_follow_up : '-' }</th>
                                                </tr>
                                                <tr>
                                                    <td>Follow up</td>
                                                    <th>${(lead.number_of_follow_up != null) ? lead.number_of_follow_up : '-' }</th>
                                                </tr>
                                                <tr>
                                                    <td>Attempt</td>
                                                    <th>${(lead.attempt_lead != null) ? lead.attempt_lead : '-' }</th>
                                                </tr>
                                               <tr>
                                                    <td>Created On</td>
                                                    <th>${(lead.created_at_formatted != null) ? lead.created_at_formatted : '-' }</th>
                                                </tr>
                                                <tr> 
                                                    <td>Assigned To</td>
                                                    <th>${(lead.assigned_to != null) ? lead.assigned_to : '-' }</th>
                                                <tr>
                                                <tr> 
                                                    <td>Source</td>
                                                    <th>${(lead.source != null) ? lead.source : '-' }</th>
                                                <tr>
                                                <tr>
                                                    <td >Notes</td>
                                                    <th class='text-wrap'>${(lead.notes != null) ? lead.notes : '-' }</th>
                                                </tr>
                     `);
                    }
                });
            });


            // change lead status
            $(document).on('change', '.status', function() {
                element = $(this);
                var oldstatus = $(this).data('original-value');
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change status?', // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        var statusid = element.data('statusid');
                        var fieldid = element.attr('id');
                        var statusvalue = $('#' + fieldid).val();
                        element.data('original-value', statusvalue);
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('lead.changestatus') }}",
                            data: {
                                statusid: statusid,
                                statusvalue: statusvalue,
                                token: "{{ session()->get('api_token') }}",
                                company_id: " {{ session()->get('company_id') }} ",
                                user_id: " {{ session()->get('user_id') }} ",
                            },
                            success: function(data) {
                                loaderhide();
                                if (data.status == false) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });

                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                    loaderhide();
                                } else {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    advancefilters();
                                }
                            },
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                var errorMessage = "";
                                try {
                                    var responseJSON = JSON.parse(xhr.responseText);
                                    errorMessage = responseJSON.message ||
                                        "An error occurred";
                                } catch (e) {
                                    errorMessage = "An error occurred";
                                }
                                Toast.fire({
                                    icon: "error",
                                    title: errorMessage
                                });
                                reject(errorMessage);
                            }
                        });
                    },
                    () => {
                        // Error callback
                        loaderhide();
                        var fieldid = element.attr('id');
                        $('#' + fieldid).val(oldstatus);
                    }
                );
            })

            // change lead stage status
            $(document).on('change', '.leadstage', function() {
                element = $(this);
                var oldstatus = $(this).data('original-value');
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change lead stage status?', // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        var leadstageid = element.data('leadstageid');
                        var fieldid = element.attr('id');
                        var leadstagevalue = $('#' + fieldid).val();
                        element.data('original-value', leadstagevalue);
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('lead.changeleadstage') }}",
                            data: {
                                leadstageid,
                                leadstagevalue,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                loaderhide();
                                if (data.status == false) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });

                                    loaderhide();
                                } else {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    advancefilters();
                                }
                            },
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                var errorMessage = "";
                                try {
                                    var responseJSON = JSON.parse(xhr.responseText);
                                    errorMessage = responseJSON.message ||
                                        "An error occurred";
                                } catch (e) {
                                    errorMessage = "An error occurred";
                                }
                                Toast.fire({
                                    icon: "error",
                                    title: errorMessage
                                });
                                reject(errorMessage);
                            }
                        });
                    },
                    () => {
                        // Error callback
                        loaderhide();
                        var leadstageid = element.attr('id');
                        $('#' + leadstageid).val(oldstatus);
                    }
                );
            })

            // lead edit redirect - save advanced filter data as it is on local storage session
            $(document).on("click", '.editbtn', function() {
                editid = $(this).data('id');
                // loadershow();
                followupcount = $('#followupcount').val();
                fromdate = $('#fromdate').val();
                todate = $('#todate').val();
                advancestatus = $('#advancestatus').val();
                assignedto = $('#assignedto').val();
                source = $('#source').val();
                leadstagestatus = $('#leadstagestatus').val();
                last_followup = $('#last_followup').val();
                next_followup = $('#next_followup').val();
                activestatusvalue = $('input[name="status"]:checked').val();

                data = {
                    followupcount,
                    fromdate,
                    todate,
                    advancestatus,
                    assignedto,
                    source,
                    leadstagestatus,
                    last_followup,
                    next_followup,
                    activestatusvalue
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                // console.log(data);
                window.location.href = "EditLead/" + editid;
            });

            // lead delete
            $(document).on("click", ".dltbtn", function() {
                var id = $(this).data('uid');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('lead.delete') }}",
                            data: {
                                id: id,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                if (data.status == false) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });

                                } else {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    $(row).closest("tr").fadeOut();
                                }
                                loaderhide();
                            },
                            error: function(xhr, status,
                            error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                    ); // Log the full error response for debugging
                                var errorMessage = "";
                                try {
                                    var responseJSON = JSON.parse(xhr.responseText);
                                    errorMessage = responseJSON.message ||
                                        "An error occurred";
                                } catch (e) {
                                    errorMessage = "An error occurred";
                                }
                                Toast.fire({
                                    icon: "error",
                                    title: errorMessage
                                });
                                reject(errorMessage);
                            }
                        });
                    }
                );
            })


            // advancefilters
            function advancefilters() {

                fromdate = $('#fromdate').val();
                todate = $('#todate').val();

                if (fromdate != '' && todate == '') {
                    todate = fromdate;
                    $('#todate').val(todate);
                }
                followupcount = $('#followupcount').val();
                advancestatus = $('#advancestatus').val();
                assignedto = $('#assignedto').val();
                source = $('#source').val();
                leadstagestatus = $('#leadstagestatus').val();
                LastFollowUpDate = $('#last_followup').val();
                NextFollowUpDate = $('#next_followup').val();
                activestatusvalue = $('input[name="status"]:checked').val();
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
                if (followupcount != '') {
                    data.followupcount = followupcount;
                }
                if (advancestatus != '') {
                    data.status = advancestatus;
                }
                if (assignedto != '') {
                    data.assignedto = assignedto;
                }
                if (source != '') {
                    data.source = source;
                }
                if (leadstagestatus != '') {
                    data.leadstagestatus = leadstagestatus;
                }
                if (LastFollowUpDate != '') {
                    data.lastfollowupdate = LastFollowUpDate;
                }
                if (NextFollowUpDate != '') {
                    data.nextfollowupdate = NextFollowUpDate;
                }
                if (activestatusvalue != '') {
                    data.activestatusvalue = activestatusvalue;
                }

                if (fromdate == '' && todate == '' && advancestatus == '' && assignedto == '' && source == '' &&
                    leadstagestatus == '' && LastFollowUpDate == '' &&
                    NextFollowUpDate == '' && activestatusvalue == '' && followupcount == '') {
                    loaddata();
                }
                if ((fromdate != '' && todate != '' && !(fromDate > toDate)) || advancestatus != '' || assignedto !=
                    '' || source != '' || leadstagestatus != '' ||
                    LastFollowUpDate != '' || NextFollowUpDate != '' || activestatusvalue != '' || followupcount !=
                    '') {
                    loadershow();
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('lead.index') }}",
                        data: data,
                        success: function(response) {
                            if (response.status == 200 && response.lead != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                global_response = response;
                                var id = 1;
                                $.each(response.lead, function(key, value) {
                                    var name = (value.first_name != null ? value.first_name :
                                        '') + ' ' + (value.last_name != null ? value
                                        .last_name : '')
                                    $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td  class="text-left" >
                                                        ${value.company != null ? "<span class='d-flex mb-2'><b><i class='fas fa-building pr-2'></i></b></span>" : '' }
                                                        <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                                            <b><i class="fas fa-user pr-2"></i></b> ${name != '' ? name : '-'}
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-envelope pr-2"></i></b>
                                                            <a href="mailto:${value.email != null ? value.email : ''}" style='text-decoration:none;'>${value.email != null ? value.email : '-'}</a>
                                                        </span>
                                                        <span class='d-flex mb-2'>
                                                            <b><i class="fas fa-phone-alt pr-2"></i></b>
                                                            <a href="tel:${value.contact_no != null ? value.contact_no : ''}" style='text-decoration:none;'> ${value.contact_no != null ? value.contact_no : '-'}</a>
                                                        </span>  
                                                        <span class='d-flex mb-2'>
                                                             @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <select class="ml-1 status form-control" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
                                                                ${ 
                                                                    leadstatusname.map(function(optionValue) {
                                                                            return `<option value="${optionValue}">${optionValue}</option>`;
                                                                        }).join('')
                                                                }
                                                            </select>
                                                       @else
                                                         -
                                                       @endif
                                                        </span>    
                                                    </td>
                                                    <td>
                                                        <select class="leadstage form-control" data-original-value="${value.lead_stage}" data-leadstageid=${value.id} id="lead_stage_${value.id}" name="lead_stage_${value.id}">
                                                            ${ 
                                                                leadstagename.map(function(optionValue) {
                                                                        return `<option value="${optionValue}">${optionValue}</option>`;
                                                                    }).join('')
                                                            }
                                                        </select>
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.number_of_follow_up}</td>
                                                    <td>${value.source != null ? value.source : '-'}</td>
                                                    <td>
                                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title='Add Call History' >
                                                            <button data-toggle="modal"  data-target="#addcallhistory" data-id='${value.id}' class='btn btn-sm btn-primary mx-0 my-1 leadid' ><i class='ri-time-fill'></i></button>
                                                        </span>
                                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title='view Call History' >
                                                            <button data-toggle="modal" data-target="#viewcallhistory" data-id='${value.id}' class='btn btn-sm btn-info mx-0 my-1 viewcallhistory' ><i class='ri-eye-fill'></i></button>
                                                        </span>
                                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Whatapp Message">
                                                            <a  class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit lead">
                                                                    <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button> 
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.leadmodule.lead.delete') == '1')
                                                            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete lead">
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`)
                                    $('#status_' + value.id).val(value.status);
                                    $('#lead_stage_' + value.id).val(value.lead_stage);
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
                                Toast.fire({
                                    icon: "error",
                                    title: response.message
                                });
                            } else {
                                $('#tabledata').html(`<tr><td colspan='7' >No Data Found</td></tr>`);
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
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
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
                $('#followupcount').val('');
                $('#fromdate').val('');
                $('#todate').val('');
                $('#last_followup').val('');
                $('#next_followup').val('');
                $('#invaliddate').text(' ');
                $('#assignedto option').prop('selected', false);
                $('#source option').prop('selected', false);

                if ($("#assignedto option:not(:disabled)").length > 0) {
                    $("#assignedto").prepend(
                        '<option value="" disabled selected>-- Select User --</option>');
                }
                if ($("#source option:not(:disabled)").length > 0) {
                    $("#source").prepend('<option value="" disabled selected>-- Select Source --</option>');
                }

                $('#assignedto option:first').prop('selected', true);
                $('#source option:first').prop('selected', true);
                $('#assignedto').multiselect('refresh');
                $('#source').multiselect('refresh');
                advancefilters();
            });

            // remove all filters
            $('.removefilters').on('click', function() {
                $('#followupcount').val('');
                $('#fromdate').val('');
                $('#todate').val('');
                $('#last_followup').val('');
                $('#next_followup').val('');
                $('#invaliddate').text(' ');
                $("input[name='status'][value='all']").prop("checked", true);
                // Uncheck all options
                $('#advancestatus option').prop('selected', false);
                $('#assignedto option').prop('selected', false);
                $('#source option').prop('selected', false);
                $('#leadstagestatus option').prop('selected', false);


                if ($("#advancestatus option:not(:disabled)").length > 0) {
                    $("#advancestatus").prepend(
                        '<option value="" disabled selected>-- Select Status --</option>');
                }
                if ($("#assignedto option:not(:disabled)").length > 0) {
                    $("#assignedto").prepend(
                        '<option value="" disabled selected>-- Select User --</option>');
                }
                if ($("#source option:not(:disabled)").length > 0) {
                    $("#source").prepend('<option value="" disabled selected>-- Select Source --</option>');
                }
                if ($("#leadstagestatus option:not(:disabled)").length > 0) {
                    $("#leadstagestatus").prepend(
                        '<option value="" disabled selected>-- Select Lead Stage --</option>');
                }

                // Check only the first option
                $('#advancestatus option:first').prop('selected', true);
                $('#assignedto option:first').prop('selected', true);
                $('#source option:first').prop('selected', true);
                $('#leadstagestatus option:first').prop('selected', true);

                // Refresh the multiselect dropdown to reflect changes
                $('#advancestatus').multiselect('refresh');
                $('#assignedto').multiselect('refresh');
                $('#source').multiselect('refresh');
                $('#leadstagestatus').multiselect('refresh');
                loaddata();
            });


            //    leadhistory 
            $(document).on('click', '.leadid', function() {
                $('#history_notes').summernote('code', '');
                leadid = $(this).data('id');
                $('#leadid').val(leadid);

                $.each(global_response.lead, function(key, lead) {
                    if (lead.id == leadid) {
                        $('#addcallhistoryTitle').html(
                            `<b>Call History</b> - ${lead.first_name} ${lead.last_name}`);
                        $('#next_call_date').val(`${lead.next_follow_up}`);
                    }

                });

                var now = new Date();
                var formattedDateTime = now.getFullYear() + '-' +
                    ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
                    ('0' + now.getDate()).slice(-2) + 'T' +
                    ('0' + now.getHours()).slice(-2) + ':' +
                    ('0' + now.getMinutes()).slice(-2);

                $('#call_date').val(formattedDateTime);
                $('#created_by').val("{{ session()->get('user_id') }}");
                $('#company_id').val("{{ session()->get('company_id') }}");
                $('#token').val("{{ session()->get('api_token') }}");
            });

            // view lead call history
            $(document).on('click', '.viewcallhistory', function() {
                $('.historyrecord').html(' ');
                loadershow();
                var historyid = $(this).data('id');

                $.each(global_response.lead, function(key, lead) {
                    if (lead.id == historyid) {
                        $('#viewcallhistoryTitle').html(
                            `<b>Call History</b> - ${lead.first_name} ${lead.last_name}`);
                    }
                });
                let leadHistorySearchUrl = "{{ route('leadhistory.search', '__historyId__') }}".replace(
                    '__historyId__', historyid);
                $.ajax({
                    type: 'GET',
                    url: leadHistorySearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 & response.leadhistory != '') {
                            $.each(response.leadhistory, function(key, value) {
                                $('.historyrecord').append(`
                                <div class="col-12">
                                    <b>Status:</b> ${value.call_status} <br>
                                    <b>Complain Description:</b>  ${value.history_notes} <br>
                                    <small> ${value.call_date}</small>
                                    <hr>
                                </div>
                            `);
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('.historyrecord').append(`
                                <div class="col-12">
                                   No history Found 
                                </div>
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
                        reject(errorMessage);
                    }
                });
            });


            // reset call history form
            $(document).on('click', '.resethistoryform', function() {
                $('#history_notes').summernote('code', '');
                $('#leadhistoryform')[0].reset();
            })

            // leadhistoryform submit 
            $('#leadhistoryform').submit(function(e) {
                e.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serializeArray();
                formdata.push({
                    name: "notes",
                    value: $('#history_notes').summernote('code')
                });
                $.ajax({
                    type: 'POST',
                    url: "{{ route('leadhistory.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#history_notes').summernote('code', '');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#leadhistoryform')[0].reset();
                            $('#addcallhistory').modal('hide');
                            advancefilters();
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
                        $('#history_notes').summernote('code', '');
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
                })
            });

        });
    </script>
@endpush
