@extends('admin.mastertable')

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

        .multiselect-container {
            width: 300px;
            /* Set your desired width here */
        }

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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
        <input type="radio" class="is_active advancefilter" id="qualified" name="status" value="1">
        <label for="qualified">Qualified</label>
        <input type="radio" class="is_active advancefilter" id="disqualified" name="status" value="0">
        <label for="disqualified">Disqualified</label>
        <input type="radio" class="is_active advancefilter" value="all" checked id="all" name="status">
        <label for="all">All</label>
        <select class="advancefilter multiple form-control w-100" id="advancestatus" multiple="multiple">
            <option disabled selected>-- Select status --</option>
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
        <select class="advancefilter multiple form-control w-100" id="leadstagestatus" multiple="multiple">
            <option disabled selected>-- Select Lead Stage --</option>
            <option value='New Lead'>New Lead</option>
            <option value='Requirement Ghathering'>Requirement Ghathering</option>
            <option value='Quotation'>Quotation</option>
            <option value='In Followup'>In Followup</option>
            <option value='Sale'>Sale</option>
            <option value='Cancelled'>Cancelled</option>
            <option value='Disqualified'>Disqualified</option>
        </select>
        <!-- Use any element to open the sidenav -->
        <button title="AdvanceFilters" onclick="openNav()" class="btn btn-sm btn-rounded btn-info">
            <i class="ri-filter-line"></i>
        </button>
        <button title="FilterRefresh" class="btn btn-info btn-sm removefilters">
            <i class="fa fa-refresh"></i>
        </button>
    </div>

    @if (session('user_permissions.leadmodule.lead.add') == '1')
        @section('addnew')
            {{ route('admin.addlead') }}
        @endsection
        @section('addnewbutton')
            <button title="Add Lead" class="btn btn-sm btn-primary">
                <span class="">+ Lead</span>
            </button>
        @endsection
    @endif


@endsection


@section('table-content')
    <table id="data" class="table table-bordered w-100  table-responsive-sm table-striped text-center">
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
                <form id="leadhistoryform">
                    <div class="modal-body">
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
                            <div class="col-12">
                                FollowUp : <input type="checkbox" name="followup" id="followup" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" value="submit" class="btn btn-sm btn-primary">
                        <button type="button" class="btn btn-danger resethistoryform" data-dismiss="modal">Close
                        </button>
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        /* Simple appearence with animation AN-1*/
        function openNav() {
            document.getElementById("mySidenav").style.width = "25%";
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

            $('#history_notes').summernote({
                height: 200
            });
            // set advanced filter data if it is selected on lead edit time
            var filterData = JSON.parse(sessionStorage.getItem('filterData'));
            if (filterData) {
                $.each(filterData, function(key, value) {
                    if (value != ' ') {
                        $('#' + key).val(value);
                    }
                    if (key === 'activestatusvalue') {
                        $('input[name="status"][value="' + value + '"]').prop('checked', true);
                    }
                });
                advancefilters();
                 // Check only the first option
                 $('#advancestatus option:first').prop('selected', true);
                $('#assignedto option:first').prop('selected', true);
                $('#source option:first').prop('selected', true);
                $('#leadstagestatus option:first').prop('selected', true);
                sessionStorage.removeItem('filterData');
            } else {
                loaddata();
            }
           

            // get user data for advanced filters
            $.ajax({
                type: 'GET',
                url: '{{ route('user.index') }}',
                data: {
                    user_id: "{{ session()->get('user_id') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(response.user, function(key, value) {
                            var optionValue = value.firstname + ' ' + value.lastname;
                            $('#assignedto').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#assignedto').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options
                        loaderhide();
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    } else {
                        $('#assignedto').append(`<option> No User Found </option>`);
                        loaderhide();
                    }
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });
            

            // get all sources for advanced filters
            $.ajax({
                type: 'GET',
                url: '{{ route('lead.sourcecolumn') }}',
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }} ",
                    user_id: "{{ session()->get('user_id') }} "
                },
                success: function(response) {
                    if (response.status == 200 && response.sourcecolumn != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(response.sourcecolumn, function(key, value) {
                            var optionValue = value
                            $('#source').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#source').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options
                        loaderhide();
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    } else {
                        $('#source').append(`<option> No User Found </option>`);
                        loaderhide();
                    }
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });
            var global_response = '';
            $('#advancestatus').multiselect();
            $('#leadstagestatus').multiselect();


            // get lead data and set in the table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('lead.index') }}',
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
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td  class="text-left" >
                                                        <span style="cursor:pointer;" class="view-btn" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                                            <b>Name:</b> ${value.name}
                                                        </span>
                                                        <br/>
                                                        <span>
                                                            <b>Email:</b>
                                                            <a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a>
                                                        </span>
                                                        <br/>
                                                        <span>
                                                            <b>Contact:</b>
                                                            <a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no}</a>
                                                        </span>
                                                        <br/>
                                                        <span>
                                                            <b>Title:</b> ${value.title === null ? '-':value.title}
                                                        </span>
                                                        <br/>    
                                                        <span>
                                                            <b>Status:</b> 
                                                             @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <select class="status form-control-sm" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
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
                                                       @else
                                                         -
                                                       @endif
                                                        </span>    
                                                    </td>
                                                    <td> 
                                                        <select class="leadstage form-control" data-original-value="${value.lead_stage}" data-leadstageid=${value.id} id="lead_stage_${value.id}" name="lead_stage_${value.id}">
                                                            <option value='New Lead'>New Lead</option>
                                                            <option value='Requirement Ghathering'>Requirement Ghathering</option>
                                                            <option value='Quotation'>Quotation</option>
                                                            <option value='In Followup'>In Followup</option>
                                                            <option value='Sale'>Sale</option>
                                                            <option value='Cancelled'>Cancelled</option>
                                                            <option value='Disqualified'>Disqualified</option>
                                                        </select>
                                                    </td> 
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.number_of_follow_up}</td>
                                                    <td>${value.source}</td>
                                                    <td>
                                                        <span>
                                                            <button data-toggle="modal" data-target="#addcallhistory" data-id='${value.id}' title='Add Call History' class='btn btn-sm btn-primary mx-0 leadid' ><i class='ri-time-fill'></i></button>
                                                        </span>
                                                        <span>
                                                            <button data-toggle="modal" data-target="#viewcallhistory" data-id='${value.id}' title='view Call History' class='btn btn-sm btn-info mx-0 viewcallhistory' ><i class='ri-eye-fill'></i></button>
                                                        </span>
                                                        <span>
                                                            <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <span>
                                                                 <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-0 editbtn">
                                                                    <i class="ri-edit-fill"></i>
                                                                 </button>  
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.leadmodule.lead.delete') == '1')
                                                            <span>
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-0">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`)
                                $('#status_' + value.id).val(value.status);
                                $('#lead_stage_' + value.id).val(value.lead_stage);
                                id++;
                            });
                            var search = {!! json_encode($search) !!}

                            $('#data').DataTable({

                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize datatable
                            });
                            loaderhide();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            $('#data').append(`<tr><td colspan='11' >No Data Found</td></tr>`);
                            loaderhide();
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }

            // it is commented beacause it is called base on conditions 
            //call function for loaddata
            // loaddata();

   
            // view individual lead data
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.lead, function(key, lead) {
                    if (lead.id == data) {

                        $('#details').append(`
                                                <tr> 
                                                    <td>name</td>
                                                    <th>${lead.name}</th>
                                                </tr> 
                                                <tr>
                                                    <td>email</td>
                                                    <th>${lead.email}</th>
                                                </tr>
                                                <tr>
                                                    <td>contact Number</td>
                                                    <th>${lead.contact_no}</th>
                                                    </tr>
                                                <tr>
                                                    <td>Title</td>
                                                    <th>${lead.title}</th>
                                                </tr>
                                                <tr>
                                                    <td>Budget</td>
                                                    <th>${lead.budget}</th>
                                                </tr>
                                                <tr>
                                                    <td>Audience Type</td>
                                                    <th>${lead.audience_type}</th>
                                                </tr>
                                                <tr>
                                                    <td>Customer Type</td>
                                                    <th>${lead.customer_type}</th>
                                                </tr>
                                                <tr>
                                                    <td>Status</td>
                                                    <th>${lead.status}</th>
                                                </tr>
                                                <tr>
                                                    <td>Last Follow Up</td>
                                                    <th>${lead.last_follow_up}</th>
                                                </tr>
                                                <tr>
                                                    <td>Next Follow Up</td>
                                                    <th>${lead.next_follow_up}</th>
                                                </tr>
                                                <tr>
                                                    <td>Follow up</td>
                                                    <th>${lead.number_of_follow_up}</th>
                                                </tr>
                                                <tr>
                                                    <td>Attempt</td>
                                                    <th>${lead.attempt_lead}</th>
                                                </tr>
                                               <tr>
                                                    <td>Created On</td>
                                                    <th>${lead.created_at_formatted}</th>
                                                </tr>
                                                <tr> 
                                                    <td>Assigned To</td>
                                                    <th>${lead.assigned_to}</th>
                                                <tr>
                                                <tr> 
                                                    <td>Source</td>
                                                    <th>${lead.source}</th>
                                                <tr>
                                                <tr>
                                                    <td >Notes</td>
                                                    <th class='text-wrap'>${lead.notes}</th>
                                                </tr>
                     `);
                    }
                });
            });
         

            // change lead status
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

            // change lead stage status
            $(document).on('change', '.leadstage', function() {
                var oldstatus = $(this).data('original-value');
                if (confirm('Are you Sure That to change lead stage status  ?')) {
                    loadershow();
                    var leadstageid = $(this).data('leadstageid');
                    var fieldid = $(this).attr('id');
                    var leadstagevalue = $('#' + fieldid).val();
                    $(this).data('original-value', leadstagevalue);
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
                    var leadstageid = $(this).attr('id');
                    $('#' + leadstageid).val(oldstatus);
                }
            })

            // lead edit redirect - save advanced filter data as it is on local storage session
            $(document).on("click", '.editbtn', function() {
                editid = $(this).data('id');
                // loadershow();
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

                if (confirm("Are you Sure that to delete this record")) {
                    loadershow();
                    var id = $(this).data('uid');
                    var row = this;

                    $.ajax({
                        url: "{{ route('lead.delete') }}",
                        type: 'PUT',
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
            

            // advancefilters
            function advancefilters() {

                fromdate = $('#fromdate').val();
                todate = $('#todate').val();

                if (fromdate != '' && todate == '') {
                    todate = fromdate;
                    $('#todate').val(todate);
                }
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
                    NextFollowUpDate == '' && activestatusvalue == '') {
                    loaddata();
                }
                if ((fromdate != '' && todate != '' && !(fromDate > toDate)) || advancestatus != '' || assignedto !=
                    '' || source != '' || leadstagestatus != '' ||
                    LastFollowUpDate != '' || NextFollowUpDate != '' || activestatusvalue != '') {
                    loadershow();
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('lead.index') }}',
                        data: data,
                        success: function(response) {
                            if (response.status == 200 && response.lead != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                global_response = response;
                                var id = 1;
                                $.each(response.lead, function(key, value) {
                                    $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td class="text-left" >
                                                        <span style="cursor:pointer;" class="view-btn" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable">
                                                            <b>Name:</b> ${value.name}
                                                        </span>
                                                        <br/>
                                                        <span>
                                                            <b>Email:</b>
                                                            <a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a>
                                                        </span>
                                                        <br/>
                                                        <span>
                                                            <b>Contact:</b>
                                                            <a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no}</a>
                                                        </span>
                                                        <br/>
                                                        <span>
                                                            <b>Title:</b> ${value.title === null ? '-':value.title}
                                                        </span> 
                                                        <br/>    
                                                        <span>
                                                            <b>Status:</b> @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <select class="status form-control-sm" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
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
                                                       @else
                                                         -
                                                       @endif
                                                        </span>    
                                                    </td>
                                                    <td>
                                                        <select class="leadstage form-control" data-original-value="${value.lead_stage}" data-leadstageid=${value.id} id="lead_stage_${value.id}" name="lead_stage_${value.id}">
                                                            <option value='New Lead'>New Lead</option>
                                                            <option value='Requirement Ghathering'>Requirement Ghathering</option>
                                                            <option value='Quotation'>Quotation</option>
                                                            <option value='In Followup'>In Followup</option>
                                                            <option value='Sale'>Sale</option>
                                                            <option value='Cancelled'>Cancelled</option>
                                                            <option value='Disqualified'>Disqualified</option>
                                                        </select>
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.number_of_follow_up}</td>
                                                    <td>${value.source}</td>
                                                    <td>
                                                        <span>
                                                            <button data-toggle="modal" data-target="#addcallhistory" data-id='${value.id}' title='Add Call History' class='btn btn-sm btn-primary mx-0 leadid' ><i class='ri-time-fill'></i></button>
                                                        </span>
                                                        <span>
                                                            <button data-toggle="modal" data-target="#viewcallhistory" data-id='${value.id}' title='view Call History' class='btn btn-sm btn-info mx-0 viewcallhistory' ><i class='ri-eye-fill'></i></button>
                                                        </span>
                                                        <span>
                                                            <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                                            <span>
                                                                    <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-0 editbtn">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button> 
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.leadmodule.lead.delete') == '1')
                                                            <span>
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-0">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`)
                                    $('#status_' + value.id).val(value.status);
                                    $('#lead_stage_' + value.id).val(value.lead_stage);
                                    id++;
                                });
                                var search = {!! json_encode($search) !!}

                                $('#data').DataTable({

                                    "search": {
                                        "search": search
                                    },
                                    "destroy": true, //use for reinitialize datatable
                                });
                                loaderhide();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                $('#data').DataTable({
                                    "destroy": true, //use for reinitialize datatable
                                });
                                loaderhide();
                            }
                            // You can update your HTML with the data here if needed
                        },
                        error: function(error) {
                            loaderhide();
                            console.error('Error:', error);
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
                $('#fromdate').val('');
                $('#todate').val('');
                $('#last_followup').val('');
                $('#next_followup').val('');
                $('#invaliddate').text(' ');
                $('#assignedto option').prop('selected', false);
                $('#assignedto option:first').prop('selected', true);
                $('#assignedto').multiselect('refresh');
                $('#source option').prop('selected', false);
                $('#source option:first').prop('selected', true);
                $('#source').multiselect('refresh');
                advancefilters();
            });
              
           // remove all filters
            $('.removefilters').on('click', function() {
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
                leadid = $(this).data('id');
                $('#leadid').val(leadid);

                $.each(global_response.lead, function(key, lead) {
                    if (lead.id == leadid) {
                        $('#addcallhistoryTitle').html(`${lead.name}<br/> - <b>Call History</b>`);
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
                        $('#viewcallhistoryTitle').html(`${lead.name}<br/> - <b>Call History</b>`);
                    }
                });

                $.ajax({
                    type: 'get',
                    url: "/api/leadhistory/search/" + historyid,
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
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            $('.historyrecord').append(`
                                <div class="col-12">
                                   No history Found 
                                </div>
                            `);
                        }


                        loaderhide();
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });
            

            // reset call history form
            $(document).on('click', '.resethistoryform', function() {
                $('#leadhistoryform')[0].reset();
            })

            // leadhistoryform submit 
            $('#leadhistoryform').submit(function(e) {
                e.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('leadhistory.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#leadhistoryform')[0].reset();
                            $('#addcallhistory').modal('hide');
                            advancefilters();
                        } else if (response.status == 500) {
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
                })
            });

        });
    </script>
@endpush
