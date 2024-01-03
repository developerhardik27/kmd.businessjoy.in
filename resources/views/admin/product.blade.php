@extends('admin.mastertable')
@section('page_title')
    Products
@endsection
@section('table_title')
    Products
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
@if (session('user_permissions.invoicemodule.product.add') === '1')
    @section('addnew')
        {{ route('admin.addproduct') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data" class="table display table-bordered table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Price</th>
                <th>company</th>
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
            // fetch & show products data in table
            function loaddata() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('product.index') }}',
                    data: {
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.product != '') {
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed
                            $.each(response.product, function(key, value) {
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td>${value.name}</td>
                                                    <td>${value.price_per_unit}</td>
                                                    <td>${value.company_name}</td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.product.view') === '1')
                                                            <span>
                                                                <button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                    <i class="ri-indent-decrease"></i>
                                                                </button>
                                                            </span>
                                                        @else 
                                                          -    
                                                        @endif
                                                    </td>
                                                    @if(session('user_permissions.invoicemodule.product.edit') === '1' || session('user_permissions.invoicemodule.product.delete') === '1')
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.product.edit') === '1')
                                                                <span>
                                                                    <a href='EditProduct/${value.id}'>
                                                                        <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                            <i class="ri-edit-fill"></i>
                                                                        </button>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.invoicemodule.product.delete') === '1')
                                                                <span>
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
                            $('#data').append(`<tr><td colspan='6' >No Data Found</td></tr>`)
                        }


                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }
            // call function for show data in table
            loaddata();


            // delete product
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    var $deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'post',
                        url: '/api/product/delete/' + $deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                $(row).closest("tr").fadeOut();
                            }
                        }
                    });
                }
            });

            //view product detail
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.product, function(key, product) {
                    if (product.id == data) {
                        $.each(product, function(fields, value) {
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
