@extends('admin.mastertable')

@section('page_title')
    Company
@endsection
@section('table_title')
    Company
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

@if (session('user_permissions.invoicemodule.company.add') === '1')
    @section('addnew')
        {{ route('admin.addcompany') }}
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
                <th>Name</th>
                <th>Email</th>
                <th>ContactNo</th>
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

            var global_response = '';
            // load data in table 
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('company.index') }}',
                    data: {
                        user_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.company != '') {
                            loaderhide();
                            global_response = response;
                            var id = 1;
                            $.each(response.company, function(key, value) {
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td>${value.name}</td>
                                                    <td>${value.email}</td>
                                                    <td>${value.contact_no}</td>
                                                    <td>
                                                        @if(session('user_permissions.invoicemodule.company.view') === '1')
                                                            <span>
                                                                <button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                    <i class="ri-indent-decrease"></i>
                                                                </button>
                                                            </span>
                                                        @else
                                                          -
                                                        @endif
                                                    </td>
                                                    @if(session('user_permissions.invoicemodule.company.edit') === '1' ||
                                                            session('user_permissions.invoicemodule.company.delete') === '1')
                                                        <td> 
                                                            @if(session('user_permissions.invoicemodule.company.edit') === '1')
                                                                <span class="">
                                                                    <a href='EditCompany/${value.id}'>
                                                                        <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                            <i class="ri-edit-fill"></i>
                                                                        </button>
                                                                    </a>
                                                                </span>
                                                            @endif                                                        
                                                            @if(session('user_permissions.invoicemodule.company.delete') === '1')
                                                                <span class="">
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
                            });
                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else {
                            $('#data').append(`<tr><td colspan='6' >No Data Found</td></tr>`);
                            loaderhide();
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        loaderhide();
                    }
                });
            }

            //call function for loaddata
            loaddata();



            // delete company             
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var $deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'post',
                        url: '/api/company/delete/' + $deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                $(row).closest("tr").fadeOut();
                            }
                        }
                    });
                }
            });

            // view company data in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.company, function(key, company) {
                    if (company.id == data) {
                        $.each(company, function(fields, value) {
                            $('#details').append(`<tr>
                                    <th>${fields}</th>
                                    
                                    <td>${value}</td>
                                    </tr>`)
                        })
                    }
                });
            });
        });
    </script>
@endpush
