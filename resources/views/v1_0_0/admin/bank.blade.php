@php
    $folder = session('folder_name');
@endphp
@extends($folder.'.admin.mastertable')

@section('page_title')
  Invoice -  Bank Details
@endsection
@section('table_title')
    Bank Details
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
    </style>
@endsection
@if (session('user_permissions.invoicemodule.bank.add') == '1')
    @section('addnew')
        {{ route('admin.addbank') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data" class="table  table-bordered display table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Holder</th>
                <th>Account</th>
                <th>Branch</th>
                <th>Status</th>
                <th>View</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {
          
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';
            // load bank details data in table 
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('bank.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}", //user id is neccesary for fetch api data
                        company_id: "{{ session()->get('company_id') }}", //compnay id is neccesary for fetch api data
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // if response has data then it will be append into list table
                        if (response.status == 200 && response.bankdetail != '') {
                            // You can update your HTML with the data here if needed
                            global_response = response;
                            loaderhide();
                            var id = 1;
                            $.each(response.bankdetail, function(key, value) {
                                $('#data').append(`<tr>
                                                        <td>${id}</td>
                                                        <td>${value.holder_name}</td>
                                                        <td>${value.account_no}</td>
                                                        <td>${value.branch_name}</td>
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.bank.edit') == '1')
                                                                ${value.is_active == 1 ? '<div id=status_'+value.id+ '> <button data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button></div>'  : '<div id=status_'+value.id+ '><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button></div>'}
                                                            @else
                                                              -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.bank.view') == '1')
                                                                <span class=""><button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0"><i class="ri-indent-decrease"></i></button></span>
                                                            @else
                                                              -
                                                            @endif
                                                        </td>
                                                        
                                                        <td> 
                                                            @if (session('user_permissions.invoicemodule.bank.delete') == '1')
                                                                <span class=""><button type="button" data-id= '${value.id}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0"><i class="ri-delete-bin-fill"></i></button></span>
                                                            @else
                                                              -
                                                            @endif
                                                        </td>
                                                    </tr>`)
                                id++;
                            });
                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else if (response.status == 500) { // if database not found
                            toastr.error(response.message);
                            loaderhide();
                        } else {  // if request has not found any bank details record
                            loaderhide();
                            $('#data').append(`<tr><td colspan='6' >No Data Found</td></tr>`)
                        }
                        
                    },
                    error: function(error) { // if calling api request error 
                        loaderhide();
                        toastr.error('Something Went Wrong!');
                    }
                });
            }

            //call function for loaddata
            loaddata();

            //  bank status update active to  deactive              
            $(document).on("click", ".status-active", function() {
                if (confirm('Are you really want to change status to inactive ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/bank/update/' + statusid,
                        data: {
                            status: '0',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button>'
                                );
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                toastr.error('something went wrong !');
                                loaderhide();
                            }
                        }
                    });
                }
            });

            //  bank status update  deactive to  active            
            $(document).on("click", ".status-deactive", function() {
                if (confirm('Are you really want to change status to active ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/bank/update/' + statusid,
                        data: {
                            status: '1',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >Active</button>'
                                );
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                loaderhide();
                                toastr.error('something went wrong !');
                            }
                        }
                    });
                }
            });

            // delete bank             
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/bank/delete/' + deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success('succesfully deleted');
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                loaderhide();
                                toastr.error('something went wrong !');
                            }
                        }
                    });
                }
            });

            // view bank data in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.bankdetail, function(key, bankdetail) {
                    if (bankdetail.id == data) {
                        $.each(bankdetail, function(fields, value) {
                            $('#details').append(`<tr>
                                    <td>${fields}</td>
                                    <td>${value}</td>
                                    </tr>`)
                        })
                    }
                });
            });
        });
    </script>
@endpush
