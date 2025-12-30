@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Activity/Recent Data
@endsection
@section('table_title')
    Activity/Recent Data
@endsection

{{-- @if (session('user_permissions.developermodule.recentactivitydata.add') == '1')
    @section('addnew')
        {{ route('admin.addrecentactivitydata') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New"
            class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif --}}

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
@section('table-content')
    <div class="alert alert-info">
        <p>
            <strong>Note: </strong> For change limit value ask to developer (only <code>config/app.php</code> file change
            require).
        </p>
    </div>

    <table id="data" class="table  table-bordered display w-100 table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>Module</th>
                <th>Page</th>
                <th>Limit</th> 
            </tr>
        </thead>
        <tbody id="tabledata">
            @foreach (config('app.recent_activity_pages') as $record)
                <tr>
                    <td> {{$loop->iteration}} </td>
                    <td> {{$record['module']}} </td>
                    <td> {{$record['page']}} </td>
                    <td> {{$record['limit']}} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            loaderhide();
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            // var global_response = '';
            // let table = '';
            // // load slow page records data in table 
            // function loaddata() {
            //     loadershow();
            //     table = $('#data').DataTable({
            //         language: {
            //             lengthMenu: '_MENU_ &nbsp;Entries per page'
            //         },
            //         destroy: true, // allows re-initialization
            //         responsive: true,
            //         processing: true,
            //         serverSide: true,
            //         ajax: {
            //             type: "GET",
            //             url: "{{ route('getrecentactivitydata') }}",
            //             data: function(d) {
            //                 d.user_id = "{{ session()->get('user_id') }}";
            //                 d.company_id = "{{ session()->get('company_id') }}";
            //                 d.token = "{{ session()->get('api_token') }}";
            //             },
            //             dataSrc: function(json) {
            //                 if (json.message) {
            //                     Toast.fire({
            //                         icon: "error",
            //                         title: json.message || 'Somethint went wrong!'
            //                     })
            //                 }

            //                 global_response = json;

            //                 return json.data;
            //             },
            //             complete: function() {
            //                 loaderhide();
            //             },
            //             error: function(xhr) {
            //                 global_response = '';
            //                 console.log(xhr.responseText);
            //                 Toast.fire({
            //                     icon: "error",
            //                     title: "Error loading data"
            //                 });
            //             }
            //         },
            //         order: [
            //             [0, 'desc']
            //         ],
            //         columns: [{
            //                 data: 'id',
            //                 name: 'id',
            //                 orderable: true,
            //                 searchable: true,
            //                 defaultContent: '-'
            //             },
            //             {
            //                 data: 'module',
            //                 name: 'module',
            //                 orderable: true,
            //                 searchable: true,
            //                 defaultContent: '-'
            //             },
            //             {
            //                 data: 'page',
            //                 name: 'page',
            //                 orderable: true,
            //                 searchable: true,
            //                 defaultContent: '-'
            //             },
            //             {
            //                 data: 'limit',
            //                 name: 'limit',
            //                 orderable: true,
            //                 searchable: true,
            //                 defaultContent: '-'
            //             },
            //             {
            //                 data: 'id',
            //                 name: 'id',
            //                 orderable: false,
            //                 searchable: false,
            //                 render: function(data, type, row) {
            //                     let actionBtns = '';

            //                      @if (session('user_permissions.developermodule.recentactivitydata.edit') == '1')
            //                         let editRecordUrl = "{{ route('admin.editrecentactivitydata', '__recordId__') }}".replace('__recordId__', row.id);
            //                         actionBtns += `
            //                             <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Record">
            //                                 <a href=${editRecordUrl}>
            //                                     <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
            //                                         <i class="ri-edit-fill"></i>
            //                                     </button>
            //                                 </a>
            //                             </span>
            //                         `;
            //                     @endif

            //                     @if (session('user_permissions.developermodule.recentactivitydata.delete') == '1')
            //                         actionBtns += `
            //                             <span>
            //                                 <button data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" type="button" data-id= '${row.id}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0">
            //                                     <i  class="ri-delete-bin-fill"></i>
            //                                 </button>
            //                             </span>
            //                         `;
            //                     @endif

            //                     return actionBtns;
            //                 }
            //             }
            //         ],

            //         pagingType: "full_numbers",
            //         drawCallback: function(settings) {
            //             $('[data-toggle="tooltip"]').tooltip({
            //                boundary: 'window',
            //                offset: '0, 10' // Push tooltip slightly away from the button
            //            });

            //             // 👇 Jump to Page input injection
            //             if ($('#jumpToPageWrapper').length === 0) {
            //                 let jumpHtml = `
            //                         <div id="jumpToPageWrapper" class="d-flex align-items-center ml-3" style="gap: 5px;">
            //                             <label for="jumpToPage" class="mb-0">Jump to page:</label>
            //                             <input type="number" id="jumpToPage" min="1" class="dt-input" style="width: 80px;" />
            //                             <button id="jumpToPageBtn" class="btn btn-sm btn-primary">Go</button>
            //                         </div>
            //                     `;
            //                 $(".dt-paging").after(jumpHtml);
            //             }


            //             $(document).off('click', '#jumpToPageBtn').on('click', '#jumpToPageBtn',
            //                 function() {
            //                     let table = $('#data').DataTable();
            //                     // Check if table is initialized
            //                     if ($.fn.DataTable.isDataTable('#data')) {
            //                         let page = parseInt($('#jumpToPage').val());
            //                         let totalPages = table.page.info().pages;

            //                         if (!isNaN(page) && page > 0 && page <= totalPages) {
            //                             table.page(page - 1).draw('page');
            //                         } else {
            //                             Toast.fire({
            //                                 icon: "error",
            //                                 title: `Please enter a page number between 1 and ${totalPages}`
            //                             });
            //                         }
            //                     } else {

            //                         Toast.fire({
            //                             icon: "error",
            //                             title: `DataTable not yet initialized.`
            //                         });
            //                     }
            //                 }
            //             );
            //         }
            //     });

            // }

            // //call function for loaddata
            // loaddata();


            // // delete slow page record             
            // $(document).on("click", ".del-btn", function() {
            //     var deleteid = $(this).data('id');
            //     let pageRecordDeleteUrl = "{{ route('recentactivitydata.delete', '__deleteId__') }}".replace(
            //         '__deleteId__', deleteid);
            //     var row = this;
            //     showConfirmationDialog(
            //         'Are you sure?', // Title
            //         'to delete this record ?', // Text
            //         'Yes, delete', // Confirm button text
            //         'No, cancel', // Cancel button text
            //         'question', // Icon type (question icon)
            //         () => {
            //             // Success callback
            //             loadershow();
            //             $.ajax({
            //                 type: 'PUT',
            //                 url: pageRecordDeleteUrl,
            //                 data: {
            //                     token: "{{ session()->get('api_token') }}",
            //                     company_id: "{{ session()->get('company_id') }}",
            //                     user_id: "{{ session()->get('user_id') }}",
            //                 },
            //                 success: function(response) {
            //                     if (response.status == 200) {
            //                         Toast.fire({
            //                             icon: "success",
            //                             title: "succesfully deleted"
            //                         });
            //                         table.draw();
            //                     } else if (response.status == 500) {
            //                         Toast.fire({
            //                             icon: "error",
            //                             title: response.message
            //                         });
            //                     } else {
            //                         Toast.fire({
            //                             icon: "error",
            //                             title: "something went wrong!"
            //                         });
            //                     }
            //                     loaderhide();
            //                 },
            //                 error: function(xhr, status,
            //                     error) { // if calling api request error 
            //                     loaderhide();
            //                     console.log(xhr
            //                         .responseText
            //                     ); // Log the full error response for debugging
            //                     var errorMessage = "";
            //                     try {
            //                         var responseJSON = JSON.parse(xhr.responseText);
            //                         errorMessage = responseJSON.message ||
            //                             "An error occurred";
            //                     } catch (e) {
            //                         errorMessage = "An error occurred";
            //                     }
            //                     Toast.fire({
            //                         icon: "error",
            //                         title: errorMessage
            //                     });
            //                 }
            //             });
            //         }
            //     );
            // });

        });
    </script>
@endpush
