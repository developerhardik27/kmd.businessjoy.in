@extends('admin.mastertable')

@section('page_title')
    Lead
@endsection
@section('table_title')
    Lead
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

        .multiselect-container>li>a>label {
            padding: 3px 16px 3px 23px !important;
        }

        .multiselect {
            border: 0.5px solid #00000073;
        }

        /* #advancestatus {
                            border: 0.5px solid #d3c3d3 !important;
                        } */
    </style>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('advancefilter')
    <div class="col-md-12 text-right float-right">

        <div class="col-md-2 p-1 float-left offset-3">
            <select class="advancefilter multiple form-control w-100" id="advancestatus" multiple="multiple">
                <option disabled selected>status</option>
                <option value='Not Interested'>Not Interested</option>
                <option value='Not Receiving'>Not Receiving</option>
                <option value='New Lead'>New Lead</option>
                <option value='Interested'>Interested</option>
                <option value='Switch Off'>Switch Off</option>
                <option value='Does Not Exist'>Does Not Exist</option>
                <option value='Email Sent'>Email Sent</option>
                <option value='Wrong Number'>Wrong Number</option>
                <option value='By Mistake'>By Mistake</option>
                <option value='Rejected'>Rejected</option>
                <option value='Sale'>Sale</option>
                <option value='Busy'>Busy</option>
                <option value='Call Back'>Call Back</option>
            </select>
        </div>
        <div class="col-md-3 float-left">
            <label for="fromdate" class="form-label float-left p-2 w-25">From:</label>
            <input type="date" id="fromdate" class="form-input form-control  w-75 float-left advancefilter">
           </div>
        <div class="col-md-3 float-left">
            <label for="todate" class="form-label  float-left p-2">To:</label>
            <input type="date" id="todate" class="form-input form-control w-75 float-left advancefilter">
            <span id="invaliddate" class="font-weight-bold text-danger" style="float: left;"></span>
        </div>
        <div class="col-auto float-left text-right mt-1">
            <button title="Refresh" class="btn btn-info btn-sm removefilters">
                <i class="fa fa-refresh"></i>
            </button>
        </div>



        @if (session('user_permissions.leadmodule.lead.add') === '1')
            @section('addnew')
                {{ route('admin.addlead') }}
            @endsection
            @section('addnewbutton')
                <button title="Add Lead" class="btn btn-sm btn-primary">
                    <span class="">+ Lead</span>
                </button>
            @endsection
        @endif

    </div>
@endsection


@section('table-content')
    <table id="data" class="table table-bordered w-100  table-responsive table-striped text-center">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Title</th>
                <th>Status</th>
                <th>createdat</th>
                <th>followup</th>
                <th>Active</th>
                <th>Source</th>
                <th class="actionwidth">Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        $(document).ready(function() {

            var global_response = '';
            $('#advancestatus').multiselect();

            function loaddata() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('lead.index') }}',
                    data: {
                        user_id: "{{ session()->get('company_id') }}",
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
                                                    <td style="cursor:pointer;" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn text-info" >${value.name}</td>
                                                    <td ><a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a></td>
                                                    <td ><a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no} </a></td>
                                                    <td>${value.title === null ? '-':value.title}</td>
                                                    <td>
                                                        @if (session('user_permissions.leadmodule.lead.edit') === '1')
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
                                                                <option value='Rejected'>Rejected</option>
                                                                <option value='Sale'>Sale</option>
                                                                <option value='Busy'>Busy</option>
                                                                <option value='Call Back'>Call Back</option>
                                                            </select>
                                                       @else
                                                         -
                                                       @endif
                                                    </td>
                                                    <td>${value.created_at_formatted}</td>
                                                    <td>${value.number_of_follow_up}</td>
                                                    <td> ${value.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                                                    <td>${value.source}</td>
                                                    <td class="actionwidth">
                                                        <span>
                                                            <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                                <i class="ri-whatsapp-line text-white"></i>
                                                            </a>
                                                        </span>
                                                        @if (session('user_permissions.leadmodule.lead.edit') === '1')
                                                            <span>
                                                                <a href='EditLead/${value.id}'>
                                                                    <button type="button" class="btn btn-warning btn-rounded btn-sm my-0">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button>
                                                                </a>   
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.leadmodule.lead.delete') === '1')
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
                        } else {
                            $('#data').append(`<tr><td colspan='11' >No Data Found</td></tr>`)
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }

            //call function for loaddata
            loaddata();

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
                                                    <td>Created On</td>
                                                    <th>${lead.created_at_formatted}</th>
                                                </tr>
                                                <tr> 
                                                    <td>IP Address</td>
                                                    <th>${lead.ip}</th>
                                                <tr>
                                                <tr> 
                                                    <td>Source</td>
                                                    <th>${lead.source}</th>
                                                <tr>
                                                <tr>
                                                    <td>Notes</td>
                                                    <th>${lead.notes}</th>
                                                </tr>
                     `);
                    }
                });
            });

            $(document).on('change', '.status', function() {
                var oldstatus = $(this).data('original-value');
                if (confirm('Are you Sure That to change status  ?')) {
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
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(data) {
                            if (data.status == false) {
                                toastr.error(data.message);
                            } else {
                                toastr.success(data.message);
                                loaddata();
                            }
                        }
                    });
                } else {
                    var fieldid = $(this).attr('id');
                    $('#' + fieldid).val(oldstatus);
                }
            })


            $(document).on("click", ".dltbtn", function() {

                if (confirm("Are you Sure that to delete this record")) {
                    var id = $(this).data('uid');
                    var row = this;

                    $.ajax({
                        url: "{{ route('lead.delete') }}",
                        type: 'PUT',
                        data: {
                            id: id,
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(data) {
                            if (data.status == false) {
                                toastr.error(data.message)
                            } else {
                                toastr.success(data.message);
                                $(row).closest("tr").fadeOut();
                            }

                        }
                    });
                }
            })


            $('.advancefilter').on('change', function() {
                fromdate = $('#fromdate').val();
                todate = $('#todate').val();
                advancestatus = $('#advancestatus').val();
                console.log('fromdate :- ' + fromdate + ' todate :- ' + todate);
                var fromDate = new Date(fromdate);
                var toDate = new Date(todate);

                if(fromDate > toDate){
                   $('#invaliddate').text('Invalid Date');
                }else{
                    $('#invaliddate').text(' ');
                }
                if (fromdate != '' && todate != '' && advancestatus != '' && !(fromDate > toDate)) {

                    $.ajax({
                        type: 'GET',
                        url: '{{ route('lead.index') }}',
                        data: {
                            user_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}",
                            fromdate: fromdate,
                            todate: todate,
                            status: advancestatus
                        },
                        success: function(response) {
                            if (response.status == 200 && response.lead != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                // console.log(response.lead);
                                // $('#tabledata').val(' ');
                                global_response = response;
                                var id = 1;
                                $.each(response.lead, function(key, value) {
                                    $('#data').append(`<tr>
                                                <td>${id}</td>
                                                <td style="cursor:pointer;" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn text-info" >${value.name}</td>
                                                <td ><a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a></td>
                                                <td ><a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no} </a></td>
                                                <td>${value.title === null ? '-':value.title}</td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.edit') === '1')
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
                                                            <option value='Rejected'>Rejected</option>
                                                            <option value='Sale'>Sale</option>
                                                            <option value='Busy'>Busy</option>
                                                            <option value='Call Back'>Call Back</option>
                                                        </select>
                                                   @else
                                                     -
                                                   @endif
                                                </td>
                                                <td>${value.created_at_formatted}</td>
                                                <td>${value.number_of_follow_up}</td>
                                                <td> ${value.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                                                <td>${value.source}</td>
                                                <td class="actionwidth">
                                                    <span>
                                                        <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                            <i class="ri-whatsapp-line text-white"></i>
                                                        </a>
                                                    </span>
                                                    @if (session('user_permissions.leadmodule.lead.edit') === '1')
                                                        <span>
                                                            <a href='EditLead/${value.id}'>
                                                                <button type="button" class="btn btn-warning btn-rounded btn-sm my-0">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </a>   
                                                        </span>
                                                    @endif
                                                    @if (session('user_permissions.leadmodule.lead.delete') === '1')
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
                            } else {
                                $('#tabledata').html(' ');
                                $('#data').append(
                                    `<tr><td colspan='11'>No Data Found</td></tr>`)
                            }
                            // You can update your HTML with the data here if needed
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                } else if (fromdate != '' && todate != '' && advancestatus == '' && !(fromDate > toDate)) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('lead.index') }}',
                        data: {
                            user_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}",
                            fromdate: fromdate,
                            todate: todate,
                        },
                        success: function(response) {
                            if (response.status == 200 && response.lead != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                // console.log(response.lead);
                                // $('#tabledata').val(' ');
                                global_response = response;
                                var id = 1;
                                $.each(response.lead, function(key, value) {
                                    $('#data').append(`<tr>
                                                <td>${id}</td>
                                                <td style="cursor:pointer;" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn text-info" >${value.name}</td>
                                                <td ><a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a></td>
                                                <td ><a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no} </a></td>
                                                <td>${value.title === null ? '-':value.title}</td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.edit') === '1')
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
                                                            <option value='Rejected'>Rejected</option>
                                                            <option value='Sale'>Sale</option>
                                                            <option value='Busy'>Busy</option>
                                                            <option value='Call Back'>Call Back</option>
                                                        </select>
                                                   @else
                                                     -
                                                   @endif
                                                </td>
                                                <td>${value.created_at_formatted}</td>
                                                <td>${value.number_of_follow_up}</td>
                                                <td> ${value.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                                                <td>${value.source}</td>
                                                <td class="actionwidth">
                                                    <span>
                                                        <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                            <i class="ri-whatsapp-line text-white"></i>
                                                        </a>
                                                    </span>
                                                    @if (session('user_permissions.leadmodule.lead.edit') === '1')
                                                        <span>
                                                            <a href='EditLead/${value.id}'>
                                                                <button type="button" class="btn btn-warning btn-rounded btn-sm my-0">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </a>   
                                                        </span>
                                                    @endif
                                                    @if (session('user_permissions.leadmodule.lead.delete') === '1')
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
                            } else {
                                $('#tabledata').html(' ');
                                $('#data').append(
                                    `<tr><td colspan='11'>No Data Found</td></tr>`)
                            }
                            // You can update your HTML with the data here if needed
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                } else if (fromdate == '' && todate == '' && advancestatus != '') {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('lead.index') }}',
                        data: {
                            user_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}",
                            status: advancestatus
                        },
                        success: function(response) {
                            if (response.status == 200 && response.lead != '') {
                                $('#data').DataTable().destroy();
                                $('#tabledata').empty();
                                // console.log(response.lead);
                                // $('#tabledata').val(' ');
                                global_response = response;
                                var id = 1;
                                $.each(response.lead, function(key, value) {
                                    $('#data').append(`<tr>
                                                <td>${id}</td>
                                                <td style="cursor:pointer;" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn text-info" >${value.name}</td>
                                                <td ><a href="mailto:${value.email}" style='text-decoration:none;'>${value.email}</a></td>
                                                <td ><a href="tel:${value.contact_no}" style='text-decoration:none;'> ${value.contact_no} </a></td>
                                                <td>${value.title === null ? '-':value.title}</td>
                                                <td>
                                                    @if (session('user_permissions.leadmodule.lead.edit') === '1')
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
                                                            <option value='Rejected'>Rejected</option>
                                                            <option value='Sale'>Sale</option>
                                                            <option value='Busy'>Busy</option>
                                                            <option value='Call Back'>Call Back</option>
                                                        </select>
                                                   @else
                                                     -
                                                   @endif
                                                </td>
                                                <td>${value.created_at_formatted}</td>
                                                <td>${value.number_of_follow_up}</td>
                                                <td> ${value.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                                                <td>${value.source}</td>
                                                <td class="actionwidth">
                                                    <span>
                                                        <a title="Send Whatapp Message" class='btn btn-success btn-sm' target="_blank" href="https://wa.me/${value.contact_no}">
                                                            <i class="ri-whatsapp-line text-white"></i>
                                                        </a>
                                                    </span>
                                                    @if (session('user_permissions.leadmodule.lead.edit') === '1')
                                                        <span>
                                                            <a href='EditLead/${value.id}'>
                                                                <button type="button" class="btn btn-warning btn-rounded btn-sm my-0">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </a>   
                                                        </span>
                                                    @endif
                                                    @if (session('user_permissions.leadmodule.lead.delete') === '1')
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
                            } else {
                                $('#tabledata').html(' ');
                                $('#data').append(
                                    `<tr><td colspan='11'>No Data Found</td></tr>`)
                            }
                            // You can update your HTML with the data here if needed
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                } else if (fromdate == '' && todate == '' && advancestatus == '') {
                    loaddata();
                }
            });

            $('.removefilters').on('click', function() {
                 $('#fromdate').val('');
                 $('#todate').val('');
                $('#invaliddate').text(' ');
                // Uncheck all options
                $('#advancestatus option').prop('selected', false);

                // Check only the first option
                $('#advancestatus option:first').prop('selected', true);

                // Refresh the multiselect dropdown to reflect changes
                $('#advancestatus').multiselect('refresh');
                loaddata();

            });
        });
    </script>
@endpush
