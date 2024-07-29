@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')

@section('page_title')
    Blog
@endsection
@section('table_title')
    Blog
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
{{-- @if (session('user_permissions.invoicemodule.bank.add') == '1') --}}
@section('addnew')
    {{ route('admin.addblog') }}
@endsection
@section('addnewbutton')
    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Account"
        class="btn btn-sm btn-primary">
        <span class="">+ Add New</span>
    </button>
@endsection
{{-- @endif --}}
@section('table-content')
    <table id="data"
        class="table  table-bordered display table-responsive-sm table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>View</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script>
        loaderhide();
    </script>
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
                    type: 'get',
                    url: '{{ route('blog.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}", //user id is neccesary for fetch api data
                        company_id: "{{ session()->get('company_id') }}", //compnay id is neccesary for fetch api data
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // if response has data then it will be append into list table
                        if (response.status == 200 && response.blog != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            // You can update your HTML with the data here if needed
                            global_response = response;
                            var id = 1;
                            $.each(response.blog, function(key, value) {
                                $('#data').append(`<tr>
                                                        <td>${id}</td>
                                                        <td>${value.title != null ? value.title : '-'}</td>  
                                                        <td>
                                                            @if (session('user_permissions.blogmodule.blog.view') == '11')
                                                                <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details"><button type="button"  data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0"><i class="ri-indent-decrease"></i></button></span>
                                                            @else
                                                              -
                                                            @endif
                                                        </td> 
                                                        @if (session('user_permissions.blogmodule.blog.edit') == '1' ||
                                                                session('user_permissions.blogmodule.blog.delete') == '1')
                                                            <td>
                                                                @if (session('user_permissions.blogmodule.blog.edit') == '1')
                                                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                                                        <a href='EditBlog/${value.id}'>
                                                                            <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                                <i class="ri-edit-fill"></i>
                                                                            </button>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                                @if (session('user_permissions.blogmodule.blog.delete') == '1')
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
                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else if (response.status == 500) { // if database not found
                            toastr.error(response.message);
                        } else { // if request has not found any bank details record
                            $('#data').append(`<tr><td colspan='4' >No Data Found</td></tr>`)
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
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

            //call function for loaddata
            loaddata();

            // delete bank             
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you sure to delete this blog ?')) {
                    loadershow();
                    var deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/blog/delete/' + deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success('succesfully deleted');
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error('something went wrong !');
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

            // view bank data in pop-up
            // $(document).on("click", ".view-btn", function() {
            //     $('#details').html('');
            //     var data = $(this).data('view');
            //     $.each(global_response.bankdetail, function(key, bankdetail) {
            //         if (bankdetail.id == data) {
            //             $('#details').append(`
            //                     <tr>
            //                         <th>Holder Name</th>
            //                         <td>${(bankdetail.holder_name != null)? bankdetail.holder_name : '-'}</td>
            //                     </tr>
            //                     <tr>
            //                         <th>Account Number</th>
            //                         <td>${(bankdetail.account_no!= null)? bankdetail.account_no : '-'}</td>
            //                     </tr>
            //                     <tr>
            //                         <th>IFSC Code</th>
            //                         <td>${(bankdetail.ifsc_code!= null)? bankdetail.ifsc_code : '-'}</td>
            //                     </tr>
            //                     <tr>
            //                         <th>Swift Code</th>
            //                         <td>${(bankdetail.swift_code!= null)? bankdetail.swift_code : '-'}</td>
            //                     </tr>
            //                     <tr>
            //                         <th>Branch Name</th>
            //                         <td>${(bankdetail.branch_name!= null)? bankdetail.branch_name : '-'}</td>
            //                     </tr>
            //             `);
            //         }
            //     });
            // });
        });
    </script>
@endpush
