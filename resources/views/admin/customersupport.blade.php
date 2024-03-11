@extends('admin.mastertable')

@section('page_title')
    {{ config('app.name') }} - Customer Support
@endsection
@section('table_title')
    Customer Support
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
        <select class="advancefilter multiple form-control w-100" id="advancestatus" multiple="multiple">
            <option disabled selected>-- Select status --</option>
            <option value='Open'>Open</option>
            <option value='In Progress'>In Progress</option>
            <option value='Resolved'>Resolved</option>
            <option value='Cancelled'>Cancelled</option>
        </select>
        <select name="assignedto" class="form-control multiple advancefilter" id="assignedto" multiple>
            <option value="" disabled selected>-- Select Assigned To --</option>
        </select>
        <!-- Use any element to open the sidenav -->
        <button title="AdvanceFilters" onclick="openNav()" class="btn btn-sm btn-rounded btn-info">
            <i class="ri-filter-line"></i>
        </button>
        <button title="FilterRefresh" class="btn btn-info btn-sm removefilters">
            <i class="fa fa-refresh"></i>
        </button>
    </div>

    @if (session('user_permissions.customersupportmodule.customersupport.add') == '1')
        @section('addnew')
            {{ route('admin.addcustomersupport') }}
        @endsection
        @section('addnewbutton')
            <button title="Add Lead" class="btn btn-sm btn-primary">
                <span class="">+ Ticket</span>
            </button>
        @endsection
    @endif


@endsection


@section('table-content')
    <table id="data" class="table table-bordered w-100  table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Details</th>
                <th>Complain Desc.</th>
                <th>Status</th>
                <th>&nbsp;&nbsp;&nbsp;History&nbsp;&nbsp;&nbsp;</th>
                <th>createdon</th>
                <th>no.of calls</th>
                <th>&nbsp;&nbsp;&nbsp;Action&nbsp;&nbsp;&nbsp;</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>

    {{-- modal for add call history module  --}}
    <div class="modal fade" id="addcallhistory" tabindex="-1" role="dialog" aria-labelledby="addcallhistoryTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addcallhistoryTitle"><b>Call History</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="customersupporthistoryform">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="company_id" id="company_id">
                            <input type="hidden" name="csid" id="csid">
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
                                <textarea name="history_notes" id="history_notes" cols="10" rows="5" class="form-control"></textarea>
                                <span class="error-msg" id="error-history_notes" style="color: red"></span>
                            </div>
                            <br />
                            <div class="col-12">
                                Status:
                                <select class="form-control" id="call_status" name="call_status">
                                    <option disabled selected>status</option>
                                    <option value='Open'>Open</option>
                                    <option value='In Progress'>In Progress</option>
                                    <option value='Resolved'>Resolved</option>
                                    <option value='Cancelled'>Cancelled</option>
                                </select>
                                <span class="error-msg" id="error-call_status" style="color: red"></span>
                            </div>
                            <br>
                            <div class="col-12">
                                No.Of Calls : <input type="checkbox" name="no_of_calls" id="no_of_calls" value="1">
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
    <script>
        // advance filter sidebar
        /* Simple appearence with animation AN-1*/
        function openNav() {
            document.getElementById("mySidenav").style.width = "30%";
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

            // set old filterdata if filter had been use otherwise filter is refresh
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
            } else {
                loaddata();
            }

            // get and set user into advanced filter dropdown
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

            var global_response = '';
            // make multiple dropdown to designable multiple dropdown
            $('#advancestatus').multiselect();
            $('#assignedto').multiselect();


           // get and set customer support history list in the table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('customersupport.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.customersupport != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            var id = 1;
                            $.each(response.customersupport, function(key, value) {
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
                                                    </td>
                                                    <td title="${value.notes}" >
                                                        <span  class="d-inline-block text-truncate" style="max-width: 150px;">
                                                        ${value.notes}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1')
                                                            <select class="status form-control-sm" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
                                                                <option disabled selected>status</option>
                                                                <option value='Open'>Open</option>
                                                                <option value='In Progress'>In Progress</option>
                                                                <option value='Resolved'>Resolved</option>
                                                                <option value='Cancelled'>Cancelled</option>
                                                            </select>
                                                       @else
                                                         -
                                                       @endif
                                                    </td>
                                                    <td>
                                                        <button data-toggle="modal" data-target="#addcallhistory" data-id='${value.id}' title='Add Call History' class='btn btn-sm btn-primary csid' ><i class='ri-time-fill'></i></button>
                                                        <button data-toggle="modal" data-target="#viewcallhistory" data-id='${value.id}' title='view Call History' class='btn btn-sm btn-info viewcallhistory' ><i class='ri-eye-fill'></i></button>
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.number_of_call}</td>
                                                    <td>
                                                        <span>
                                                            <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1')
                                                            <span>
                                                                 <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-0 editbtn">
                                                                    <i class="ri-edit-fill"></i>
                                                                 </button>  
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.customersupportmodule.customersupport.delete') == '1')
                                                            <span>
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-0">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`)
                                $('#status_' + value.id).val(value.status);
                                id++;
                            });
                            var search = {!! json_encode($search) !!}

                            $('#data').DataTable({

                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize jquery datatable
                            });
                            loaderhide();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            $('#data').append(`<tr><td colspan='7' >No Data Found</td></tr>`);
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
            

            // its commented because it is called in advancefilter condition who has been start of the script
            //call function for loaddata
            // loaddata();


           // show individual customer support history record into the popupbox
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.customersupport, function(key, ticket) {
                    if (ticket.id == data) {
                        $('#details').append(`
                                                <tr> 
                                                    <td>Ticket Number</td>
                                                    <th>${ticket.ticket}</th>
                                                </tr> 
                                                <tr> 
                                                    <td>name</td>
                                                    <th>${ticket.name}</th>
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
                                                    <th>${ticket.status}</th>
                                                </tr>
                                                <tr>
                                                    <td>Last Call</td>
                                                    <th>${ticket.last_call}</th>
                                                </tr>
                                                <tr>
                                                    <td>Follow up</td>
                                                    <th>${ticket.number_of_call}</th>
                                                </tr>
                                               <tr>
                                                    <td>Created On</td>
                                                    <th>${ticket.created_at_formatted}</th>
                                                </tr>
                                                <tr> 
                                                    <td>Assigned To</td>
                                                    <th>${ticket.assigned_to}</th>
                                                <tr>
                                                <tr>
                                                    <td >Notes</td>
                                                    <th class='text-wrap'>${ticket.notes}</th>
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
                        url: "{{ route('customersupport.changestatus') }}",
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
                    last_call,
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                // console.log(data);
                window.location.href = "Editcustomersupport/" + editid;
            });
           

            // delete customer support record
            $(document).on("click", ".dltbtn", function() {

                if (confirm("Are you Sure that to delete this record")) {
                    loadershow();
                    var id = $(this).data('uid');
                    var row = this;

                    $.ajax({
                        url: "{{ route('customersupport.delete') }}",
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
                        url: '{{ route('customersupport.index') }}',
                        data: data,
                        success: function(response) {
                            if (response.status == 200 && response.customersupport != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                global_response = response;
                                var id = 1;
                                $.each(response.customersupport, function(key, value) {
                                    $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td class="text-left" title="${value.notes}" >
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
                                                    </td> 
                                                    <td title="${value.notes}" >
                                                        <span  class="d-inline-block text-truncate" style="max-width: 150px;">
                                                        ${value.notes}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1')
                                                            <select class="status form-control-sm" data-original-value="${value.status}" data-statusid=${value.id} id='status_${value.id}'>
                                                                <option disabled selected>status</option>
                                                                <option value='Open'>Open</option>
                                                                <option value='In Progress'>In Progress</option>
                                                                <option value='Resolved'>Resolved</option>
                                                                <option value='Cancelled'>Cancelled</option>
                                                            </select>
                                                       @else
                                                         -
                                                       @endif
                                                    </td>
                                                    <td>
                                                        <button data-toggle="modal" data-target="#addcallhistory" data-id='${value.id}' title='Add Call History' class='btn btn-sm btn-primary csid' ><i class='ri-time-fill'></i></button>
                                                        <button data-toggle="modal" data-target="#viewcallhistory" data-id='${value.id}' title='view Call History' class='btn btn-sm btn-info viewcallhistory' ><i class='ri-eye-fill'></i></button>
                                                    </td>
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.number_of_call}</td>
                                                    <td>
                                                        <span>
                                                            <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1')
                                                            <span>
                                                                    <button type="button" data-id='${value.id}' class="btn btn-warning btn-rounded btn-sm my-0 editbtn">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button> 
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.customersupportmodule.customersupport.delete') == '1')
                                                            <span>
                                                                <button type="button" data-uid= '${value.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-0">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        @endif
                                                    </td>    
                                                </tr>`)
                                    $('#status_' + value.id).val(value.status);
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
                                $('#data').DataTable({
                                    "destroy": true, //use for reinitialize datatable
                                });
                                $('#tabledata').html(' ');
                                $('#data').append(
                                    `<tr><td colspan='11' >No Data Found</td></tr>`);
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

            // remover all filter who has been in the advance filter sidevar
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

                // Check only the first option
                $('#advancestatus option:first').prop('selected', true);
                $('#assignedto option:first').prop('selected', true);

                // Refresh the multiselect dropdown to reflect changes
                $('#advancestatus').multiselect('refresh');
                $('#assignedto').multiselect('refresh');
                loaddata();

            });


            //    customersupporthistory form

            $(document).on('click', '.csid', function() {
                csid = $(this).data('id');
                $('#csid').val(csid);
                $.each(global_response.customersupport, function(key, ticket) {
                    if (ticket.id == csid) {
                        $('#addcallhistoryTitle').html(`${ticket.name}<br/> - <b>Call History</b>`);
                    }
                });
                // make current date and time and set in the customer support history form intput
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
           

            // view call history
            $(document).on('click', '.viewcallhistory', function() {
                $('.historyrecord').html(' ');
                loadershow();
                var historyid = $(this).data('id');
                $.each(global_response.customersupport, function(key, ticket) {
                    if (ticket.id == historyid) {
                        $('#viewcallhistoryTitle').html(
                        `${ticket.name}<br/> - <b>Call History</b>`);
                    }
                });
                $.ajax({
                    type: 'get',
                    url: "/api/customersupporthistory/search/" + historyid,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 & response.customersupporthistory != '') {
                            $.each(response.customersupporthistory, function(key, value) {
                                $('.historyrecord').append(`
                                <div class="col-12">
                                    <b>Status:</b> ${value.call_status} <br>
                                    <b>Complain Description:</b>  ${value.history_notes} <br>
                                    <small> ${value.call_date}</small>
                                    <hr/>
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

            $(document).on('click', '.resethistoryform', function() {
                $('#customersupporthistoryform')[0].reset();
            });


            // customersupporthistoryform submit 
            $('#customersupporthistoryform').submit(function(e) {
                e.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customersupporthistory.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#customersupporthistoryform')[0].reset();
                            $('#addcallhistory').modal('hide');
                            loaddata();
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
