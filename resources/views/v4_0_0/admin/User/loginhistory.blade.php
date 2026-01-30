@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    User - Login History
@endsection
@section('table_title')
    Login History
@endsection

@section('style')
    <style>
        .ui-widget-header {
            background: #1518b117 !important;
            border: 1px solid #e1ded9 !important;
        }
    </style>
@endsection

@section('table-content')
    @if (session('user.role') == 1)
        <div class="col-md-12 pr-5">
            <div class="m-2 float-right">
                <select class="select2 form-control" id="user">

                </select>
            </div>
        </div>
    @endif

    <table id="data"  class="table table-bordered display table-striped w-100">
        <thead>
            <tr>
                <th>Username</th>
                <th>Logged At</th>
                <th>IP</th>
                <th>Status</th>
                <th>Country</th>
                <th>Device</th>
                <th>Browser</th>
                <th>Via</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody id="tabledata">

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


            @if (session('user.role') == 1)
                $.ajax({
                    type: 'GET',
                    url: "{{ route('user.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) { 
                        if (response.status == 200 && response.user != '') {
                            global_response = response;
                            // You can update your HTML with the data here if needed     
                            $.each(response.user, function(key, value) {
                                fullname = [value.firstname, value.lastname].join(' ');
                                companyname = value.company_name;
                                var username = [companyname, fullname].join(' - ');
                                $('#user').append(`
                                    <option value=${value.id}>${username}</option>
                                `);

                            });

                            $('#user').val('').select2({
                                placeholder: "Select User",
                                search: true
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
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging

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
                });
            @endif

            var global_response = '';
            var table = '' ;
            // load bank details data in table 
            function loaddata(userid = null) {
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
                        url: "{{ route('user.userloginhistory') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.request_id = userid;
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Something went wrong!'
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
                        []
                    ],
                    columns: [{
                            data: 'username',
                            name: 'username',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        }, 
                        {
                            data: 'ip',
                            name: 'ip',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        }, 
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        }, 
                        {
                            data: 'country',
                            name: 'country',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        }, 
                        {
                            data: 'device',
                            name: 'device',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        }, 
                        {
                            data: 'browser',
                            name: 'browser',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        }, 
                        {
                            data: 'via',
                            name: 'via',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        }, 
                        {
                            data: 'message',
                            name: 'message',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        }, 
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

            //call function for loaddata
            loaddata();

            $('#user').on('change', function() {
                var userid = $(this).val();
                loaddata(userid);
            });

        });
    </script>
@endpush
