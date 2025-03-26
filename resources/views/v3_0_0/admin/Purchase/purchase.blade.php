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

        table tr{
            cursor: pointer;
        }

        .clickable-row{
            cursor: pointer;
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
    <table id="data" class="table display table-bordered table-responsive-sm table-responsive-md table-responsive-lg table-striped text-center">
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
                        // Clear and destroy the existing DataTable instance
                        if ($.fn.dataTable.isDataTable('#data')) {
                            $('#data').DataTable().clear().destroy();
                        }

                        // Clear the existing table body
                        $('#tabledata').empty();
                        if (response.status == 200 && response.purchase != '') {
                            global_response = response;
                            // You can update your HTML with the data here if needed     
                            $.each(response.purchase, function(key, value) {
                                var viewPurchaseUrl = "{{route('admin.viewpurchase','__id__')}}".replace('__id__', value.id);
                                var received = value.accepted + value.rejected ;
                                $('#tabledata').append(`
                                    <tr class="clickable-row" data-target="${viewPurchaseUrl}">
                                        <td>#PO${value.id}</td>
                                        <td>${value.suppliername || '-'}</td>
                                        <td>${value.is_active == 0 ? 'Closed' : `${value.status}` }</td>
                                        <td>${received} of ${value.total_items}</td>
                                        <td>${value.currency_symbol} ${value.total}</td>
                                        <td>${value.estimated_arrival_formatted || ''}</td>
                                    </tr>
                                `);

                                $('[data-toggle="tooltip"]').tooltip('dispose');
                                $('[data-toggle="tooltip"]').tooltip();
                            });

                            var search = {!! json_encode($search) !!}

                            $('#data').DataTable({
                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize datatable
                                "order" : [],
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            // After appending "No Data Found", re-initialize DataTable so it works properly
                            $('#data').DataTable({});
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            }

            //call function for load purchase in table
            loaddata();

            $(document).on('click','.clickable-row',function(){
                target = $(this).data('target');

                window.location.href = target;
            })

        });
    </script>
@endpush
