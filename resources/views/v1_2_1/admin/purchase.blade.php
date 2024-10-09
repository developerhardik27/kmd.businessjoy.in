@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')
@section('page_title')
    {{ config('app.name') }} - Purchase
@endsection
@section('table_title')
    Purchase
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
@if (session('user_permissions.accountmodule.purchase.add') == '1')
    @section('addnew')
        {{ route('admin.addpurchase') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Purchase"
            class="btn btn-sm btn-primary">
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data"
        class="table display table-bordered table-responsive-sm table-responsive-md table-responsive-lg table-striped text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Description</th>
                <th>Amount</th>
                <th>AmountType</th>
                <th>CompanyName</th>
                <th>View</th>
                <th>Action</th>
            </tr>
        </thead>

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
            // fetch & show purchase data in table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('purchase.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.purchase != '') {
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed     
                            $.each(response.purchase, function(key, value) {
                                $('#data').append(`<tr>
                                                        <td>${id}</td>
                                                        <td>${value.name}</td>
                                                        <td> ${value.description} </td>
                                                        <td>${value.amount}</td>
                                                        <td>${value.amount_type}</td>
                                                        <td>${value.company_name}</td>                                                
                                                        <td>
                                                            @if (session('user_permissions.accountmodule.purchase.view') == '1') 
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Purdchase Details">
                                                                    <button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                        <i class="ri-indent-decrease"></i>
                                                                    </button>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        @if (session('user_permissions.accountmodule.purchase.edit') == '1' ||
                                                                session('user_permissions.accountmodule.purchase.delete') == '1')
                                                        <td>
                                                                @if (session('user_permissions.accountmodule.purchase.edit') == '1') 
                                                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                                                        <a href='EditPurchase/${value.id}'>
                                                                            <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                                <i class="ri-edit-fill"></i>
                                                                            </button>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                                @if (session('user_permissions.accountmodule.purchase.delete') == '1') 
                                                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                                                        <button type="button" data-id= '${value.id}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0">
                                                                            <i class="ri-delete-bin-fill"></i>
                                                                        </button>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        @else
                                                            <td> - </td>
                                                        @endif
                                                            
                                                    </tr>`)
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
                            $('#data').append(`<tr><td colspan='10' >No Data Found</td></tr>`);
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
                    }
                });
            }
            
            //call function for load purchase in table
            loaddata();

            // record delete 
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var deleteid = $(this).data('id');
                    var row = this;
                    let purchaseDeleteUrl = "{{route('purchase.delete','__deleteId__')}}".replace('__deleteId__',deleteid);
                    $.ajax({
                        type: 'PUT',
                        url: purchaseDeleteUrl,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
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
                        }
                    });
                }
            });

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.purchase, function(key, purchase) {
                    if (purchase.id == data) {
                        $('#details').append(`
                                <tr>
                                    <th>Name</th>                         
                                    <td>${purchase.name}</td>
                                </tr>
                                <tr>    
                                    <th>Description</th>                         
                                    <td>${purchase.description}</td>
                                </tr>
                                <tr>    
                                    <th>Amount</th>                         
                                    <td>${purchase.amount}</td>
                                </tr>
                                <tr>    
                                    <th>Amount Type</th>                         
                                    <td>${purchase.amount_type}</td>
                                </tr>
                                <tr>    
                                    <th>Date</th>                         
                                    <td>${purchase.date}</td>
                                </tr>
                            `);
                    }
                });
            });
        });
    </script>
@endpush
