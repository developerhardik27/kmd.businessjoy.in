@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Add New Employee
@endsection
@section('title')
    New Employee
@endsection

@section('style')
    <style>
        select+.btn-group {
            border: 1px solid #ced4da;
            width: 100%;
            border-radius: 5px;
        }

        .dropdown-menu {
            width: 100%;
        }
    </style>
@endsection


@section('form-content')
    <form id="employeeform" name="employeeform" enctype="multipart/form-data">

        <div class="form-group">
            <div class="form-row">
                {{-- {{ dd($company_id) }} --}}
                <div class="col-sm-6 mb-2">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}" />
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}" />
                    <input type="hidden" name="company_id" class="form-control" value="{{ $company_id }}" />

                    <label>First Name</label><span style="color:red;">*</span>
                    <input type="text" name="first_name" maxlength="255" class="form-control" placeholder="First Name" />
                    <span class="error-msg" id="error-first_name" style="color:red"></span>
                   
                </div>

                <div class="col-sm-6 mb-2">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" maxlength="255" class="form-control"
                        placeholder="Middle Name" />
                    <span class="error-msg" id="error-middle_name" style="color:red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label>Surname</label><span style="color:red;">*</span>
                    <input type="text" name="surname" maxlength="255" class="form-control" placeholder="Surname" />
                    <span class="error-msg" id="error-surname" style="color:red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label>Email</label><span style="color:red;">*</span>
                    <input type="email" name="email" maxlength="255" class="form-control" placeholder="Email" />
                    <span class="error-msg" id="error-email" style="color:red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label>Mobile</label><span style="color:red;">*</span>
                    <input type="number" name="mobile" maxlength="20" class="form-control" placeholder="Mobile Number" />
                    <span class="error-msg" id="error-mobile" style="color:red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label>Address</label>
                    <textarea name="address" class="form-control" placeholder="Address" rows="2"></textarea>
                    <span class="error-msg" id="error-address" style="color:red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label>Bank Details</label>
                    <textarea name="bank_details" class="form-control" placeholder="Bank Details" rows="2"></textarea>
                    <span class="error-msg" id="error-bank_details" style="color:red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label>CV / Resume</label><br>
                    <input type="file" name="cv_resume" accept=".pdf,.doc,.docx" />
                    <span class="error-msg" id="error-cv_resume" style="color:red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <button type="button" class="btn btn-primary btn-sm"
                        id="add-id_proofs"class="btn btn-primary btn-sm mr-2 rounded"
                        id="add-id_proofs"data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Add Id Proofs"><i class="ri-add-line"></i></button>
                    <label>ID Proofs</label><br>
                    <div id="id_proofs-wrapper">
                        <div class="input-group mb-2  border rounded bg-white mb-2 px-3 py-2 d-flex align-items-center">
                            <input type="file" name="id_proofs[]" /><br>
                            <span class="error-msg" id="error-id_proofs" style="color:red"></span>
                        </div>
                    </div>
                </div>


                <div class="col-sm-6 mb-2">
                    <button type="button" class="btn btn-primary btn-sm"
                        id="add-address_proofs"class="btn btn-primary btn-sm mr-2 rounded"
                        id="add-id_proofs"data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Address Proofs"><i class="ri-add-line"></i></button>
                    <label>Address Proofs</label><br>
                    <div id="address_proofs-wrapper">
                        <div class="input-group mb-2 border rounded bg-white mb-2 px-3 py-2 d-flex align-items-center ">
                            <input type="file" name="address_proofs[]" /><br>
                            <span class="error-msg" id="error-address_proofs" style="color:red"></span>
                        </div>
                    </div>

                </div>

                <div class="col-sm-6 mb-2">
                    <button type="button" class="btn btn-primary btn-sm"
                        id="add-other_attachments"class="btn btn-primary btn-sm mr-2 rounded"
                        id="add-id_proofs"data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Add Other Attachments"><i class="ri-add-line"></i></button>
                    <label>Other Attachments</label><br>
                    <div id="other_attachments-wrapper">
                        <div
                            class="input-group mb-2 d-flex border rounded bg-white mb-2 px-3 py-2 d-flex align-items-center ">
                            <input type="file" name="other_attachments[]" /><br>
                            <span class="error-msg" id="error-other_attachments" style="color:red"></span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <button type="reset" id="resetBtn" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset" class="btn iq-bg-danger float-right">
                        Reset
                    </button>

                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save Employee" class="btn btn-primary float-right my-0">
                        Save
                    </button>
                </div>

            </div>
        </div>
    </form>
@endsection


@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

    <script>
        $('document').ready(function() {
            loaderhide();
            // companyId and userId both are   in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or   data


            function addFileInput(wrapperId, inputName) {
                var fileInput = `
        <div class="border rounded bg-white mb-2 px-3 py-2 d-flex align-items-center"
             data-type="new">
            <input type="file" name="${inputName}[]" class="form-control-file mr-3"/>
            <button type="button" class="btn btn-danger remove-file">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    `;
                $(wrapperId).append(fileInput);
            }


            // Handle add buttons
            $('#add-id_proofs').click(function() {
                addFileInput('#id_proofs-wrapper', 'id_proofs');
            });

            $('#add-address_proofs').click(function() {
                addFileInput('#address_proofs-wrapper', 'address_proofs');
            });

            $('#add-other_attachments').click(function() {
                addFileInput('#other_attachments-wrapper', 'other_attachments');
            });

            // Handle remove buttons (works for all types)
            $(document).on('click', '.remove-file', function() {
                var element = $(this);

                showConfirmationDialog(
                    'Are you sure?',
                    'Do you want to remove this file?',
                    'Yes, remove it!',
                    'No, Cancel!',
                    'warning',
                    () => {
                        element.closest('[data-type="new"]').remove();
                    }
                );
            });

            $("#employeeform").on('submit', function(e) {
                e.preventDefault();
                loadershow()
                $('.error-msg').html(''); // Clear previous error messages

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('employee.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status == "success") {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.employee') }}";
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr) {
                        loaderhide()
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                if (key.startsWith('id_proofs')) {
                                    $('#error-id_proofs').html(value.join('<br>'));
                                } else if (key.startsWith('other_attachments')) {
                                    $('#error-other_attachments').html(value.join(
                                        '<br>'));
                                } else if (key.startsWith('address_proofs')) {
                                    $('#error-address_proofs').html(value.join(
                                        '<br>'));
                                } else {
                                    $('#error-' + key.replace(/\./g, '_')).html(value[
                                        0]);
                                }
                            });

                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "An error occurred. Please try again."
                            });

                        }
                    }
                });
            });
        });
    </script>
@endpush
