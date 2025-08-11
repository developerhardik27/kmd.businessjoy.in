@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')

@section('page_title')
    {{ config('app.name') }} - Clear Data
@endsection
@section('title')
    Clear Data
@endsection


@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            <div class="row">
                @if (session('user_permissions.developermodule.cleardata.edit') == 1)
                    <div class="col-12">
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Clear Data</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="cleardataform">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-12 mb-2">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" placeholder="token" required />
                                                <input type="hidden" value="{{ session('user_id') }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ session('company_id') }}" name="company_id"
                                                    class="form-control">
                                                <label for="company">Company</label><span style="color:red;">*</span>
                                                <select class="form-control select2" id="company" name="company[]"
                                                    multiple required>
                                                </select>
                                                <span class="error-msg" id="error-company" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-6 mb-2">
                                                <label for="from_date">From Date</label>
                                                <input type="date" name="from_date" id="from_date" class="form-control">
                                                <span class="error-msg" id="error-from_date" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-6 mb-2">
                                                <label for="to_date">To Date</label>
                                                <input type="date" name="to_date" id="to_date" class="form-control">
                                                <span class="error-msg" id="error-to_date" style="color: red"></span>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-sm-12 mt-2 text-right">
                                                    <button type="button" data-toggle="tooltip" id="analyzeBtn"
                                                        data-placement="bottom" data-original-title="Analyze Data"
                                                        class="btn btn-success m-1">Analyze
                                                    </button>
                                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Reset Settings"
                                                        class="btn iq-bg-danger m-1 resetBtn">Reset
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="col-12" id="analyzeData">

                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group" style="display:none;" id="submitBtnDiv">
                                        <div class="form-row">
                                            <div class="col-sm-12 mb-2">
                                                <p class="text-danger font-weight-bold">
                                                    Note: The records will be permanently deleted and cannot be restored
                                                    after this action.
                                                </p>
                                            </div>
                                            <div class="col-sm-12 mt-2 text-right">
                                                <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Clear Data" class="btn btn-primary m-1">Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {
            loaderhide();

            var global_response = ''; // declare global variable for using company detail globally

            // function for get company data and load into table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('company.companylist') }}",
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}",
                        is_deleted: 'yes'
                    },
                    success: function(response) {
                        // You can update your HTML with the data here if needed
                        if (response.status == 200 && response.company != '') {
                            global_response = response;
                            $('#company').html(``);
                            $.each(response.company, function(key, company) {
                                let isCompanyDeleted = company.is_deleted == 1 ? 'Deleted' : '';
                                let companydetails = [company.name, company.contact_no, company
                                    .email, company.app_version.replace(/_/g, '.'),
                                    isCompanyDeleted
                                ].filter(Boolean).join(' - ');;
                                $('#company').append(`
                                     <option value="${company.id}">${companydetails}</option>
                                `);
                            });
                            $('.select2').select2({
                                mulitple: 'true',
                                placeholder: 'Select a company'
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#company').html(`<option>No Data Found<option>`);
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

            //call loaddata function for make ajax call
            loaddata(); // this function is for get company details

            $(document).on('click', '.resetBtn', function() {
                $('#company').val(null).trigger('change');
                $('#analyzeData').html('');
                $('#submitBtnDiv').hide();
            });

            // analyze clear data
            $('#analyzeBtn').click(function() {
                loadershow();
                let companyIds = $('#company').val();

                if (companyIds.length < 1) {
                    Toast.fire({
                        icon: "info",
                        title: 'Please select a company'
                    });
                    loaderhide();
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: "{{ route('cleardata.analyzation') }}",
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}",
                        company: companyIds,
                        from_date: $('#from_date').val(),
                        to_date: $('#to_date').val(),
                    },
                    success: function(response) {
                        // You can update your HTML with the data here if needed
                        if (response.status === 200) {
                            let html = '';
                            response.data.forEach(company => {
                                html += `<table class="table table-bordered text-center">
                                        <thead>
                                            <tr><th colspan="2">${company.company_name}</th></tr>
                                            <tr><th>Module Name</th><th>Rows Affected</th></tr>
                                        </thead><tbody>`;

                                // List modules with counts, even zero counts
                                let modules = company.modules;
                                for (let modName in modules) {
                                    html +=
                                        `<tr><td>${modName}</td><td>${modules[modName]}</td></tr>`;
                                }

                                // Show note if exists
                                if (company.note) {
                                    html +=
                                        `<tr><td colspan="2" class="text-center text-danger font-italic">${company.note}</td></tr>`;
                                }

                                html += '</tbody></table><br/>';
                            });

                            $('#analyzeData').html(html);
                            $('#submitBtnDiv').show();
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
            });


            $('#cleardataform').on('submit', function(e) {
                e.preventDefault();

                let companyIds = $('#company').val();

                if (companyIds.length < 1) {
                    Toast.fire({
                        icon: "info",
                        title: 'Please select a company'
                    });
                    return;
                }

                showConfirmationDialog(
                    'Are you sure?',
                    "This will permanently delete soft-deleted records and their attachments.",
                    'Yes, delete it!',
                    'Cancel',
                    'warning',
                    () => {
                        loadershow();

                        $.ajax({
                            type: 'GET',
                            url: "{{ route('developer.cleanup.softdeleted') }}", // Use the final route name
                            data: {
                                user_id: {{ session()->get('user_id') }},
                                company_id: {{ session()->get('company_id') }},
                                token: "{{ session()->get('api_token') }}",
                                company: companyIds,
                                from_date: $('#from_date').val(),
                                to_date: $('#to_date').val(),
                            },
                            success: function(response) {
                                loaderhide();
                                if (response.status === 200 && response.data) {
                                    let html = '';
                                    response.data.forEach(company => {
                                        html += `<h5 class="mb-2">Cleared Data Result for <strong>${company.company_name}</strong></h5>`;
                                        html += `<table class="table table-bordered text-center">
                                                    <thead>
                                                        <tr><th>Module Name</th><th>Rows Cleared</th></tr>
                                                    </thead><tbody>`;

                                        // List modules with counts, even zero counts
                                        let modules = company.modules;
                                        for (let modName in modules) {
                                            html += `<tr><td>${modName}</td><td>${modules[modName]}</td></tr>`;
                                        }

                                        // Show note if exists
                                        if (company.note) {
                                            html += `<tr><td colspan="2" class="text-center text-danger font-italic">${company.note}</td></tr>`;
                                        }

                                        html += '</tbody></table><br/>';
                                    });


                                    $('#analyzeData').html(html);
                                    $('#submitBtnDiv').hide();

                                    Toast.fire({
                                        icon: "success",
                                        title: "Deletion completed successfully!"
                                    });

                                    loaddata();
                                    $('#cleardataform')[0].reset();

                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message ||
                                            "Something went wrong"
                                    });
                                }
                            },
                            error: function(xhr) {
                                loaderhide();
                                var errorMessage = "An error occurred";
                                try {
                                    var res = JSON.parse(xhr.responseText);
                                    errorMessage = res.message || errorMessage;
                                } catch (e) {}
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
