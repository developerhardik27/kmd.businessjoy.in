@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Import New Lead
@endsection
@section('title')
    Import New Lead
@endsection

@section('style')
    <style>
        .multiselect {
            border: 0.5px solid #00000073;
        }
    </style>
@endsection

@section('form-content')
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <strong><i class="fa fa-info-circle"></i> How to Use the Lead Import Template</strong>
        </div>
        <div class="card-body">
            <ul class="mb-3">
                <li>Click the button below to <strong>download the example Excel file</strong>.</li>
                <li>The file contains two sheets:
                    <ul>
                        <li><strong>Leads:</strong> This sheet includes an example record. <span class="text-danger">Please
                                delete or replace it</span> with your own lead data.</li>
                        <li><strong>Reference:</strong> This sheet provides valid options for fields such as <em>Job
                                Title</em>, <em>Budget</em>, <em>Status</em>, <em>Lead Stage</em>, and <em>Customer
                                Type</em>.</li>
                    </ul>
                </li>
                <li>Ensure your values match the reference sheet exactly to avoid import errors.</li>
                <li>After updating the file, use the form below to upload your lead data.</li>
                <li><strong>Note:</strong> The <code>first_name</code> and <code>last_name</code> fields are required for
                    each lead.</li>
                <li><strong>Important:</strong> The sheet containing lead data must be named <code>Leads</code>.</li>
            </ul>

            <a href="{{ route('lead.importtemplatedownload') }}" class="text-white btn btn-info btn-sm">
                <i class="fa fa-download"></i> Download Example Excel File
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-white">
            <strong><i class="fa fa-upload"></i> Upload Lead File</strong>
        </div>
        <div class="card-body">
            <div id="import-message"></div>
            <form id="leadimportform" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ session('user_id') }}">
                <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                <input type="hidden" name="token" value="{{ session('api_token') }}">
                <div class="form-group">
                    <label for="lead_file">Choose Excel File (.xlsx)</label>
                    <input type="file" class="form-control-file" id="lead_file" name="lead_file"
                        accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                </div>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-check"></i> Import Leads
                </button>
            </form>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {
            loaderhide();

            $('#leadimportform').submit(function(e) {
                e.preventDefault();
                $('#import-message').html('');
                loadershow(); // your custom loader function

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('lead.importfromexcel') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        loaderhide();

                        if (response.status === 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#leadimportform')[0].reset();
                        } else if (response.status === 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "warning",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let output =
                                `<div class="alert alert-danger"><strong>Validation failed for the following rows:</strong><br/><ul>`;
                            $.each(errors, function(row, fields) {
                                output += `<li><strong>${row}</strong><ul>`;
                                $.each(fields, function(field, message) {
                                    output += `<li>${field}: ${message}</li>`;
                                });
                                output += '</ul></li>';
                            });
                            output += '</ul></div>';
                            $('#import-message').html(output);
                        } else {
                            let message = "An error occurred. Please try again.";
                            try {
                                let json = JSON.parse(xhr.responseText);
                                message = json.message || message;
                            } catch (e) {}
                            Toast.fire({
                                icon: "error",
                                title: message
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush
