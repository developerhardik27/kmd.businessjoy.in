@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

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

        .select2 {
            min-width: 100% !important;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('advancefilter')
    <div class="col-sm-12 text-right">
        <button class="btn btn-sm btn-primary m-0 mr-3" data-toggle="tooltip" data-placement="bottom"
            data-original-title="Filters" onclick="showOffCannvas()">
            <i class="ri-filter-line"></i>
        </button>
    </div>
@endsection

@section('sidebar-filters')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6>Lead</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_assigned_to" class="form-label mt-1">Assigned To</label>
                        <select name="filter_assigned_to" class="filter form-control w-100 select2" id="filter_assigned_to"
                            multiple>
                        </select>
                    </div>
                    <div class="col-12 mb-1">
                        <label for="filter_lead_status" class="form-label mt-1">Lead Status</label>
                        <select class="filter select2 form-control w-100" id="filter_lead_status" multiple>
                        </select>
                    </div>
                    <div class="col-12 mb-1">
                        <label for="filter_lead_stage_status" class="form-label mt-1">Lead Stage Status</label>
                        <select class="filter select2 form-control w-100" id="filter_lead_stage_status" multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Source</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_source" class="form-label mt-1">Source</label>
                        <select class="filter select2 form-control w-100" id="filter_source" multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Follow Up</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_followup_count" class="form-label">FollowUp Count:</label>
                        <input type="number" id="filter_followup_count" placeholder="Number Of Followup"
                            class="filter form-input form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="filter_last_followup" class="form-label">Last FollowUp:</label>
                        <input type="date" id="filter_last_followup" class="filter form-input form-control ">
                    </div>
                    <div class="col-md-6">
                        <label for="filter_next_followup" class="form-label">Next FollowUp:</label>
                        <input type="date" id="filter_next_followup" class="filter form-input form-control ">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Created Date</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="filter_from_date" class="form-label">From:</label>
                        <input type="date" id="filter_from_date" class="filter form-input form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="filter_to_date" class="form-label">To:</label>
                        <input type="date" id="filter_to_date" class="filter form-input form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (session('user_permissions.leadmodule.lead.add') == '1')
    @section('addnew')
        {{ route('admin.addlead') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Create New lead">+ Create
                New</span>
        </button>
    @endsection
@endif



@section('table-content')
    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Details</th>
                <th>Lead Stage</th>
                <th>Created At</th>
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
                    <table id="details" width='100%' class="table table-bordered table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped">
                        <form id="leadhistoryform">
                            <div class="row">
                                <input type="hidden" name="company_id" id="company_id">
                                <input type="hidden" name="leadid" id="leadid">
                                <input type="hidden" name="user_id" id="created_by">
                                <input type="hidden" name="token" id="token">
                                <div class="col-12 mb-2">
                                    Datetime:
                                    <input type="datetime-local" name="call_date" id="call_date"class="form-control">
                                    <span class="error-msg" id="error-call_date" style="color: red"></span>
                                </div>
                                <br />
                                <div class="col-12 mb-2">
                                    Next FollowUp:
                                    <input type="datetime-local" name="next_call_date"
                                        id="next_call_date"class="form-control">
                                    <span class="error-msg" id="error-next_call_date" style="color: red"></span>
                                </div>
                                <br />
                                <div class="col-12 mb-2">
                                    Notes:
                                    <textarea name="history_notes" id="history_notes" cols="10" rows="1" class="form-control"></textarea>

                                    <span class="error-msg" id="error-history_notes" style="color: red"></span>
                                </div>
                                <br/>
                                <div class="col-12 mb-2">
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
                                <div class="col-12 mb-2">
                                    <input type="checkbox" name="followup" id="followup" value="1"> <label
                                        for="followup">Include in Follow-Up Count</label>
                                </div>
                            </div> 
                            <div class="modal-footer">
                                <input type="submit" value="submit" class="btn btn-sm btn-primary">
                                <button type="button" class="btn btn-danger resethistoryform" data-dismiss="modal">Close
                                </button>
                            </div>
                        </form>
                    </table>    
                </div>
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
    <script>
        $(document).ready(function() {


            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data


            function decodeHTML(html) {
                let txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value;
            }

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
                                    $('#filter_lead_status').append(
                                        `<option value="${optionValue}">${optionValue}</option>`
                                    );
                                });
                                $('#filter_lead_status').val('');
                                $('#filter_lead_status').select2({
                                    search: true,
                                    placeholder: 'Select Lead Status',
                                    allowClear: true // Optional: adds "clear" (x) button
                                });
                            } else if (response.status == 500) {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message
                                });
                            } else {
                                $('#filter_lead_status').val('');
                                $('#filter_lead_status').select2({
                                    search: true,
                                    placeholder: 'No status found',
                                    allowClear: true // Optional: adds "clear" (x) button
                                });
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
                                    $('#filter_lead_stage_status').append(
                                        `<option value="${optionValue}">${optionValue}</option>`
                                    );
                                });
                                $('#filter_lead_stage_status').val('');
                                $('#filter_lead_stage_status').select2({
                                    search: true,
                                    placeholder: 'Select Lead Stage Status',
                                    allowClear: true // Optional: adds "clear" (x) button
                                });
                            } else if (response.status == 500) {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message
                                });
                            } else {
                                $('#filter_lead_stage_status').val('');
                                $('#filter_lead_stage_status').select2({
                                    search: true,
                                    placeholder: 'No status found',
                                    allowClear: true // Optional: adds "clear" (x) button
                                });
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
                        });

                        // Trigger change event to ensure multiselect UI updates
                        $('#filter_lead_status, #filter_lead_stage_status, #filter_assigned_to, #filter_source')
                            .trigger('change');

                        loaddata();


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
                            $('#filter_assigned_to').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });

                        $('#filter_assigned_to').val('');
                        $('#filter_assigned_to').select2({
                            search: true,
                            placeholder: 'Select User',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (userDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: userDataResponse.message
                        });
                    } else {
                        $('#filter_assigned_to').val('');
                        $('#filter_assigned_to').select2({
                            search: true,
                            placeholder: 'No user found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }

                    // Check if source data is successfully fetched
                    if (sourceDataResponse.status == 200 && sourceDataResponse.sourcecolumn != '') {
                        //         if (response.status == 200 && response.sourcecolumn != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(sourceDataResponse.sourcecolumn, function(key, value) {
                            var optionValue = value
                            $('#filter_source').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#filter_source').val('');
                        $('#filter_source').select2({
                            search: true,
                            placeholder: 'Select Source',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (sourceDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: sourceDataResponse.message
                        });
                    } else {
                        $('#filter_source').val('');
                        $('#filter_source').select2({
                            search: true,
                            placeholder: 'No source found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
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

            let table = '';

            var search = {!! json_encode($search) !!}

            // get lead data and set in the table
            function loaddata() {
                loadershow();
                table = $('#data').DataTable({
                    language: {
                        lengthMenu: '_MENU_ &nbsp;Entries per page'
                    },
                    destroy: true, // allows re-initialization
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('lead.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_assigned_to = $('#filter_assigned_to').val();
                            d.filter_lead_status = $('#filter_lead_status').val();
                            d.filter_lead_stage_status = $('#filter_lead_stage_status').val();
                            d.filter_lead_stage_status = $('#filter_lead_stage_status').val();
                            d.filter_followup_count = $('#filter_followup_count').val();
                            d.filter_last_followup = $('#filter_last_followup').val();
                            d.filter_next_followup = $('#filter_next_followup').val();
                            d.filter_from_date = $('#filter_from_date').val();
                            d.filter_to_date = $('#filter_to_date').val();
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Somethint went wrong!'
                                })
                            }

                            global_response = json;

                            return json.data;
                        },
                        complete: function() {
                            loaderhide();
                        },
                        error: function(xhr) {
                            global_response = '';
                            console.log(xhr.responseText);
                            Toast.fire({
                                icon: "error",
                                title: "Error loading data"
                            });
                        }
                    },
                    order: [
                        [0, 'desc']
                    ],
                    search: {
                        search: search
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                options = '';
                                if (row.company != null) {
                                    options += `
                                    <span class='d-flex mb-2'>
                                        <b>
                                            <i class='fas fa-building pr-2'></i>
                                        </b>${row.company}
                                    </span>
                                    `;
                                }

                                options += `
                                    <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${row.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                        <b><i class="fas fa-user pr-2"></i></b> ${row.name || '-'}
                                    </span>
                                    <span class='d-flex mb-2'>
                                        <b><i class="fas fa-envelope pr-2"></i></b>
                                        <a href="mailto:${row.email || ''}" style='text-decoration:none;'>${row.email || '-'}</a>
                                    </span>
                                    <span class='d-flex mb-2'>
                                        <b><i class="fas fa-phone-alt pr-2"></i></b>
                                        <a href="tel:${row.contact_no || ''}" style='text-decoration:none;'> ${row.contact_no || '-'}</a>
                                    </span> 
                                `;
                                @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                    options += `
                                        <select class="ml-1 status form-control" data-toggle="tooltip" data-placement="bottom" data-original-title='Updates with next call history' data-original-value="${row.status}" data-statusid=${row.id} id='status_${row.id}'>
                                            ${ 
                                                leadstatusname.map(function(optionValue) {
                                                        return `<option value="${optionValue}" ${optionValue == row.status ? 'selected' : ''}>${optionValue}</option>`;
                                                    }).join('')
                                            }
                                        </select>
                                    `;
                                @endif

                                return options;

                            }
                        },
                        {
                            data: 'lead_stage',
                            name: 'lead_stage',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                return `
                                    <select class="leadstage form-control" data-original-value="${row.lead_stage}" data-leadstageid="${row.id}" id="lead_stage_${row.id}" name="lead_stage_${row.id}">
                                        ${ 
                                            leadstagename.map(function(optionValue) {
                                                    return `<option value="${optionValue}" ${optionValue == row.lead_stage ? 'selected' : ''}>${optionValue}</option>`;
                                                }).join('')
                                        }
                                    </select>
                                `;
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'number_of_follow_up',
                            name: 'number_of_follow_up',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'source',
                            name: 'source',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = `
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title='Add Call History'>
                                        <button data-toggle="modal" data-target="#addcallhistory" data-id='${row.id}' class='btn btn-sm btn-primary mx-0 my-1 leadid'>
                                            <i class='ri-time-fill'></i>
                                        </button>
                                    </span>
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title='View Call History'>
                                        <button data-toggle="modal" data-target="#viewcallhistory" data-id='${row.id}' class='btn btn-sm btn-info mx-0 my-1 viewcallhistory'>
                                            <i class='ri-eye-fill'></i>
                                        </button>
                                    </span>
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Whatapp Message">
                                        <a class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/${row.contact_no}">
                                            <i class="ri-whatsapp-line text-white"></i>
                                        </a>
                                    </span>
                                `;

                                @if (session('user_permissions.leadmodule.lead.edit') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Lead">
                                            <button type="button" data-id='${row.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                <i class="ri-edit-fill"></i>
                                            </button>  
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.leadmodule.lead.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete lead">
                                            <button type="button" data-uid= '${row.id}' class="del-btn btn btn-danger btn-rounded btn-sm my-1">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif

                                return actionBtns;
                            }
                        }
                    ],

                    pagingType: "full_numbers",
                    drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip();

                        // 👇 Jump to Page input injection
                        if ($('#jumpToPageWrapper').length === 0) {
                            let jumpHtml = `
                                    <div id="jumpToPageWrapper" class="d-flex align-items-center ml-3" style="gap: 5px;">
                                        <label for="jumpToPage" class="mb-0">Jump to page:</label>
                                        <input type="number" id="jumpToPage" min="1" class="dt-input" style="width: 80px;" />
                                        <button id="jumpToPageBtn" class="btn btn-sm btn-primary">Go</button>
                                    </div>
                                `;
                            $(".dt-paging").after(jumpHtml);
                        }


                        $(document).off('click', '#jumpToPageBtn').on('click', '#jumpToPageBtn',
                            function() {
                                let table = $('#data').DataTable();
                                // Check if table is initialized
                                if ($.fn.DataTable.isDataTable('#data')) {
                                    let page = parseInt($('#jumpToPage').val());
                                    let totalPages = table.page.info().pages;

                                    if (!isNaN(page) && page > 0 && page <= totalPages) {
                                        table.page(page - 1).draw('page');
                                    } else {
                                        Toast.fire({
                                            icon: "error",
                                            title: `Please enter a page number between 1 and ${totalPages}`
                                        });
                                    }
                                } else {

                                    Toast.fire({
                                        icon: "error",
                                        title: `DataTable not yet initialized.`
                                    });
                                }
                            }
                        );
                    }
                });

            }


            // view individual lead data
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, lead) {
                    if (lead.id == data) {
                        $('#details').append(`
                        <tr> 
                            <td>Website Url</td>
                            <th style="text-transform: none;">
                                <a href="${lead.web_url}" target="_blank">${lead.web_url || '-'}</a>
                            </th>
                        </tr> 
                        <tr> 
                            <td>Name</td>
                            <th>${lead.name || '-'}</th>
                        </tr>  
                        <tr> 
                            <td>Company name</td>
                            <th>${lead.company || '-'}</th>
                        </tr> 
                        <tr>
                            <td>email</td>
                            <th>${lead.email || '-'}</th>
                        </tr>
                        <tr>
                            <td>contact Number</td>
                            <th>${lead.contact_no || '-'}</th>
                            </tr>
                        <tr>
                            <td>Title</td>
                            <th>${lead.title ||'-'}</th>
                        </tr>
                        <tr>
                            <td>Budget</td>
                            <th>${lead.budget || '-'}</th>
                        </tr>
                        <tr>
                            <td>Audience Type</td>
                            <th>${lead.audience_type || '-'}</th>
                        </tr>
                        <tr>
                            <td>Customer Type</td>
                            <th>${lead.customer_type || '-'}</th>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <th>${lead.status || '-'}</th>
                        </tr>
                        <tr>
                            <td>Last Follow Up</td>
                            <th>${lead.last_follow_up || '-' }</th>
                        </tr>
                        <tr>
                            <td>Next Follow Up</td>
                            <th>${lead.next_follow_up || '-' }</th>
                        </tr>
                        <tr>
                            <td>Follow up</td>
                            <th>${lead.number_of_follow_up || '-' }</th>
                        </tr>
                        <tr>
                            <td>Attempt</td>
                            <th>${lead.attempt_lead || '-' }</th>
                        </tr>
                        <tr>
                            <td>Created On</td>
                            <th>${lead.created_at_formatted || '-' }</th>
                        </tr>
                        <tr> 
                            <td>Assigned To</td>
                            <th>${lead.assigned_to || '-' }</th>
                        <tr>
                        <tr> 
                            <td>Source</td>
                            <th>${lead.source || '-' }</th>
                        <tr>
                        <tr>
                            <td >Notes</td>
                            <th class='text-wrap'><div>${lead.notes ? decodeHTML(lead.notes) : '-'}</div></th>
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
                                    table.draw();
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
                                    table.draw();
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
                filter_assigned_to = $('#filter_assigned_to').val();
                filter_lead_status = $('#filter_lead_status').val();
                filter_lead_stage_status = $('#filter_lead_stage_status').val();
                filter_source = $('#filter_source').val();
                filter_followup_count = $('#filter_followup_count').val();
                filter_last_followup = $('#filter_last_followup').val();
                filter_next_followup = $('#filter_next_followup').val();
                filter_from_date = $('#filter_from_date').val();
                filter_to_date = $('#filter_to_date').val();

                data = {
                    filter_assigned_to,
                    filter_lead_status,
                    filter_lead_stage_status,
                    filter_source,
                    filter_followup_count,
                    filter_last_followup,
                    filter_next_followup,
                    filter_from_date,
                    filter_to_date
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                editLeadUrl = "{{ route('admin.editlead', '__editid__') }}".replace('__editid__', editid);

                // console.log(data);
                window.location.href = editLeadUrl;
            });

            // lead delete
            $(document).on("click", ".del-btn", function() {
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
                                    table.draw();
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


            $('.applyfilters').on('click', function() {
                table.draw();
                hideOffCanvass(); // close OffCanvass
            });

            //remove filtres
            $('.removefilters').on('click', function() {
                $('#filter_lead_status').val(null).trigger('change');
                $('#filter_lead_stage_status').val(null).trigger('change');
                $('#filter_assigned_to').val(null).trigger('change');
                $('#filter_source').val(null).trigger('change');

                $('#filter_followup_count').val('');
                $('#filter_last_followup').val('');
                $('#filter_next_followup').val('');
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');

                table.draw();
                hideOffCanvass(); // close OffCanvass
            });




            //    leadhistory 
            $(document).on('click', '.leadid', function() {
                $('#history_notes').summernote('code', '');
                leadid = $(this).data('id');
                $('#leadid').val(leadid);

                $.each(global_response.data, function(key, lead) {
                    if (lead.id == leadid) {
                        $('#addcallhistoryTitle').html(`<b>Call History</b> - ${lead.name}`);
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

                $.each(global_response.data, function(key, lead) {
                    if (lead.id == historyid) {
                        $('#viewcallhistoryTitle').html(
                            `<b>Call History</b> - ${lead.name}`);
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
                            table.draw();
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
