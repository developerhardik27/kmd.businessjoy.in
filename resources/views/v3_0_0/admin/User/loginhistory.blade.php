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
            // load bank details data in table 
            function loaddata(userid = null) {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('user.userloginhistory') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        request_id: userid
                    },
                    success: function(response) {
                        if ($.fn.dataTable.isDataTable('#data')) {
                            $('#data').DataTable().clear().destroy();
                        } 
 
                        // Clear the existing table body
                        $('#tabledata').empty();

                        // if response has data then it will be append into list table
                        if (response.status == 200 && response.loginhistory != '') {
                            // Clear and destroy the existing DataTable instance
                            // You can update your HTML with the data here if needed
                            global_response = response;

                            $.each(response.loginhistory, function(key, value) {
                                $('#tabledata').append(`
                                    <tr> 
                                        <td>${value.username || ''}</td>  
                                        <td>${value.created_at_formatted || ''}</td>  
                                        <td>${value.ip || ''}</td>  
                                        <td>${value.status || ''}</td>  
                                        <td>${value.country || ''}</td>  
                                        <td>${value.device || ''}</td>  
                                        <td>${value.browser || ''}</td>  
                                        <td>${value.via || ''}</td>  
                                        <td>${value.message || ''}</td>  
                                    </tr>
                                `)
                            });
                            $('#data').DataTable({
                                responsive : true,
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else { // if database not found
                            Toast.fire({
                                icon: "error",
                                title: response.message || 'No record found!'
                            });  

                            $('#data').DataTable(); 
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
