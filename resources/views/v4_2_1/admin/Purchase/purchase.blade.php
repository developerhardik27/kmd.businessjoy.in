@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
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

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:hover {
            text-decoration: underline;
        }
    </style>
@endsection
@if (session('user_permissions.inventorymodule.purchase.add') == '1')
    @section('addnew')
        {{ route('admin.addpurchase') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Purchase Order"
            class="btn btn-sm btn-primary">
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Purchase Order</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Received</th>
                <th>Total</th>
                <th>Expected Arrival</th>
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
            // response status == 200 that means response succesfully Received
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            var global_response = '';

            var table = '';
            // fetch & show purchase data in table
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
                        url: "{{ route('purchase.index') }}",
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
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                var viewPurchaseUrl =
                                    "{{ route('admin.viewpurchase', '__id__') }}".replace(
                                        '__id__', data);
                                return `
                                    <span class="clickable-row" data-target="${viewPurchaseUrl}">#PO${data}</span>
                                `;
                            }
                        },
                        {
                            data: 'suppliername',
                            name: 'suppliername',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                var viewPurchaseUrl =
                                    "{{ route('admin.viewpurchase', '__id__') }}".replace(
                                        '__id__', row.id);
                                return `
                                    <span class="clickable-row" data-target="${viewPurchaseUrl}">${data}</span>
                                `;
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                var viewPurchaseUrl =
                                    "{{ route('admin.viewpurchase', '__id__') }}".replace(
                                        '__id__', row.id);
                                return `
                                    <span class="clickable-row" data-target="${viewPurchaseUrl}">${row.is_active == 0 ? 'Closed' : `${data}` }</span>
                                `;
                            }
                        },
                        {
                            data: 'total_items',
                            name: 'total_items',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                var viewPurchaseUrl =
                                    "{{ route('admin.viewpurchase', '__id__') }}".replace(
                                        '__id__', row.id);
                                var received = row.accepted + row.rejected;
                                return `
                                    <span class="clickable-row" data-target="${viewPurchaseUrl}">${received} of ${data}</span>
                                `;
                            }
                        },
                        {
                            data: 'total',
                            name: 'total',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                var viewPurchaseUrl =
                                    "{{ route('admin.viewpurchase', '__id__') }}".replace(
                                        '__id__', row.id);
                                return `
                                    <span class="clickable-row" data-target="${viewPurchaseUrl}">${row.currency_symbol} ${data}</span>
                                `;
                            }
                        },
                        {
                            data: 'estimated_arrival_formatted',
                            name: 'estimated_arrival_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: ' ',
                            render: function(data, type, row) {
                                var viewPurchaseUrl =
                                    "{{ route('admin.viewpurchase', '__id__') }}".replace(
                                        '__id__', row.id);
                                return `
                                    <span class="clickable-row" data-target="${viewPurchaseUrl}">${row.estimated_arrival_formatted || ''}</span>
                                `;
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

            //call function for load purchase in table
            loaddata();

            $(document).on('click', '.clickable-row', function() {
                target = $(this).data('target');
                window.location.href = target;
            })

        });
    </script>
@endpush
