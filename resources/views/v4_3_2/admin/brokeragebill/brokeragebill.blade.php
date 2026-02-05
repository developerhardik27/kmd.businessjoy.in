@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Broker Bill
@endsection
@section('table_title')
    Broker Bill
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
@if (session('user_permissions.teamodule.brokeragebill.add') == '1')
    @section('addnew')
        {{ route('admin.brokeragebillform') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New brokerage bill"
            class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Garden Name</th>
                <th>Total Bags</th>
                <th>Total Net kg</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
    <div class="modal fade" id="pdfDateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="pdfDateForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate PDF</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                                placeholder="token" required />
                            <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                            <input type="hidden" value="{{ session('company_id') }}" class="form-control"
                                name="company_id">
                            <input type="hidden" name="garden_id" id="garden_id" class="form-control" value="">
                        </div>
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Generate</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';

            // function for  get brokerpurchases data and set it into datatable
            function loaddata() {
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
                        url: "{{ route('brokeragebill.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
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
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'garden_id',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'garden_id'
                        },
                        {
                            data: 'garden_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'garden_name'
                        },
                        {
                            data: 'total_bags',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'total_bags'
                        },
                        {
                            data: 'total_net_kg',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'total_net_kg'
                        },

                        {
                            data: 'garden_id',
                            name: 'garden_id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.teamodule.brokeragebill.view') == '1')
                                    actionBtns += `   
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button"  data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0 ">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.teamodule.brokeragebill.edit') == '1')
                                    let editUrl =
                                        `{{ route('admin.brokeragebillform', '__id__') }}`
                                        .replace(
                                            '__id__', data);
                                    actionBtns += `
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Broker Bill">
                                            <a href=${editUrl}>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif
                                // if (row.invoice_created) {
                                //     @if (session('user_permissions.teamodule.brokeragebill.view') == '1')
                                //         let generatePdfUrl =
                                //             "{{ route('brokragbill.generatebrokragebillpdf', '__gardenId__') }}"
                                //             .replace('__gardenId__', data);
                                //         actionBtns += `                                             
                            //         <span data-toggle="tooltip" data-placement="bottom" data-original-title="Download Pdf">
                            //             <a href=${generatePdfUrl} target='_blank' id='pdf'>
                            //                 <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0" ><i class="ri-download-line"></i></button>
                            //             </a>
                            //         </span>
                            //     `;
                                //     @endif
                                // } else {


                                @if (session('user_permissions.teamodule.brokeragebill.view') == '1')
                                    let storePdfUrl =
                                        "{{ route('brokeragebill.brokeragebillpdf', '__gardenId__') }}"
                                        .replace('__gardenId__', data);
                                    actionBtns += `
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Create Broker Bill PDF">
                                            <button class="btn btn-info btn-rounded btn-sm my-0 generate-pdf" data-id ="${data}">
                                                    <i class="ri-file-add-line"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                // }
                                return actionBtns;
                            }
                        }
                    ],

                    pagingType: "full_numbers",
                    drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip({
                            boundary: 'window',
                            offset: '0, 10' // Push tooltip slightly away from the button
                        });

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
            $(document).on("click", ".generate-pdf", function() {
                let gardenId = $(this).data('id');
                $('#garden_id').val(gardenId);
                $('#pdfDateModal').modal('show');


            });
            $('#pdfDateForm').on('submit', function(e) {
                e.preventDefault();
                let formdata = $(this).serialize();
                loadershow();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('brokeragebill.brokeragebillpdf') }}",
                    data: formdata,
                    success: function(response) {
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                               $('#pdfDateForm')[0].reset()
                            $('#pdfDateModal').modal('hide');

                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                                $('#pdfDateForm')[0].reset()
                        }
                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                })
            });

            $(document).on("click", ".view-btn", function() {
                let garden_id = $(this).data('view');

                $("#exampleModalScrollable .modal-dialog").addClass('modal-lg');
                $('#details').html('');
                loadershow();

                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokeragebill.getOtherData') }}",
                    data: {
                        garden_id: garden_id,
                        company_id: "{{ session('company_id') }}",
                        user_id: "{{ session('user_id') }}",
                        token: "{{ session('api_token') }}"
                    },
                    success: function(response) {
                        let totalRows = response.data.length;

                        if (totalRows === 0) {
                            $('#details').html('<tr><td colspan="6">No data found</td></tr>');
                            loaderhide();
                            return;
                        }
                        $('#details').append(`
                            <tr>
                                <th>Id</th>
                                <th>Invoice No</th>
                                <th>Brokerage</th>
                                <th>Grade</th>
                                <th>Bags</th>
                                <th>Total Kg</th>
                                <th>Rate Per kg </th>
                                <th>Total Broker</th>
                            </tr>
                        `);
                        let id = 1;
                        response.data.forEach(function(item, index) {
                            // Only show garden name for first row\
                            $('#viewmodaltitle').text(item.garden_name + `- Details`);
                            $('#viewmodaltitle').addClass('font-weight-bold');
                            $('#details').append(`
                            <tr>
                                <td>${id ?? '-'}</td>
                                <td>${item.invoice_no ?? '-'}</td>
                                <td>${item.brokerage ?? '0'}(%)</td>
                                <td>${item.grade ?? '-'}</td>
                                <td>${item.bags ?? '-'}</td>
                                <td>${item.net_kg ?? '-'}</td>
                                <td>${item.rate ?? '-'}</td>
                                <td>${((item.net_kg ?? 0) * (item.rate ?? 0) * (item.brokerage ?? 1))/100}
                            </tr>
                            
                        `);
                            id++;
                        });

                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                });
            });

            loaddata();

            // record delete 
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
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
                        let brokerpurchaseDeleteUrl =
                            "{{ route('brokerpurchase.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: brokerpurchaseDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                loaderhide();
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    loaddata();
                                    table.drow();
                                } else if (response.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
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
                            }
                        });
                    }
                );
            });



        });
    </script>
@endpush
