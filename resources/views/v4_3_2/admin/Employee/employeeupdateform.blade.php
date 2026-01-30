@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Update Employee
@endsection
@section('title')
    Update Employee
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

        .file-preview div {
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('form-content')
    <form id="employeeupdateform" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="token" value="{{ session('api_token') }}">
        <input type="hidden" name="user_id" value="{{ session('user_id') }}">
        <input type="hidden" name="company_id" value="{{ session('company_id') }}">
        <input type="hidden" id="emp_id" value="{{ $id }}">

        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6 mb-2">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                    <span class="error-msg" id="error-first_name" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" class="form-control">
                    <span class="error-msg" id="error-middle_name" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Surname <span class="text-danger">*</span></label>
                    <input type="text" name="surname" id="surname" class="form-control" required>
                    <span class="error-msg" id="error-surname" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <span class="error-msg" id="error-email" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input type="number" name="mobile" id="mobile" class="form-control" required>
                    <span class="error-msg" id="error-mobile" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Address</label>
                    <textarea name="address" id="address" class="form-control"></textarea>
                    <span class="error-msg" id="error-address" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>Bank Details</label>
                    <textarea name="bank_details" id="bank_details" class="form-control"></textarea>
                    <span class="error-msg" id="error-bank_details" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label>CV / Resume</label>
                    <div class="d-flex">
                        <input type="file" name="cv_resume" id="cv_resume" accept=".pdf,.doc,.docx"
                            class="form-control-file">
                        <div id="cv_preview"></div>
                    </div>
                    <span class="error-msg" id="error-cv_resume" style="color:red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <button type="button" class="btn btn-primary btn-sm mr-2 rounded" id="add-id_proofs"data-toggle="tooltip" data-placement="bottom" data-original-title="Add Id Proofs"><i class="ri-add-line"></i></button>
                    <label>ID Proofs</label>
                    <div id="id_proofs-wrapper"></div>
                    <div id="id_proofs_preview" class="file-preview"></div>
                    <input type="hidden" name="remove_id_proofs" id="remove_id_proofs">
                </div>
                <div class="col-sm-6 mb-2">
                    <button type="button" class="btn btn-primary btn-sm mr-2 rounded" id="add-address_proofs"data-toggle="tooltip" data-placement="bottom" data-original-title="Add Address Proofs"><i class="ri-add-line" ></i></button>
                    <label>Address Proofs</label>
                    <div id="address_proofs-wrapper"></div>
                    <div id="address_proofs_preview" class="file-preview"></div>
                    <input type="hidden" name="remove_address_proofs" id="remove_address_proofs">
                </div>
                <div class="col-sm-6 mb-2">
                    <button type="button" class="btn btn-primary btn-sm mr-2 rounded"id="add-other_attachments" data-toggle="tooltip" data-placement="bottom" data-original-title="Add Other Attachments"><i class="ri-add-line"></i></button>
                    <label>Other Attachments</label>
                    <div id="other_attachments-wrapper"></div>
                    <div id="other_attachments_preview" class="file-preview"></div>
                    <input type="hidden" name="remove_other_attachments" id="remove_other_attachments">
                </div>
                <div class="col-sm-12 text-right mt-3">
                    <button type="reset" class="btn btn-danger">Reset</button>
                    <button type="submit" class="btn btn-primary ml-2" data-toggle="tooltip" data-placement="bottom" data-original-title="Update Employee">Save </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $(document).ready(function() {
            const emp_id = $('#emp_id').val();

            function getArray(value) {
                if (!value) return [];
                if (Array.isArray(value)) return value;
                try {
                    return JSON.parse(value); // parse JSON string to array
                } catch {
                    return [];
                }
            }

            function renderFilePreview(container, files, inputName) {
                const c = $(container);
                c.empty();
                const arr = getArray(files);

                arr.forEach(file => {
                    const fileName = file.split('/').pop();
                    c.append(`
                    <div data-type="existing" class="border rounded bg-white mb-2 px-2 py-2 d-flex align-items-center">
                        <div class="d-flex w-100 align-items-center m-0">
                            <div class="col-8 d-flex align-items-center text-truncate m-0">
                                <b class="text-black-100 text-truncate font-size-14" title="${fileName}">
                                    ${fileName}
                                </b>
                            </div>
                            <div class="col-4 d-flex justify-content-end gap-2 m-0">
                                <a href="/${file}" target="_blank"class="btn btn-sm btn-info text-white m-1" data-toggle="tooltip" data-placement="bottom" data-original-title="View File">
                                    <i class="ri-eye-fill"></i>
                                </a>
                                <button type="button"class="btn btn-sm btn-danger remove-file m-1"  data-toggle="tooltip" data-placement="bottom" data-original-title="Remove File">
                                    <i class="ri-delete-bin-fill"></i>
                                </button>
                            </div>
                            <input type="hidden" name="${inputName}_existing[]" value="${file}">
                        </div>
                    </div>
                    `);
                });
            }

            // Generic function to add file input
            function addFileInput(wrapper, inputName) {
                $(wrapper).append(`
                <div class="border rounded bg-white mb-2 px-3 py-2 d-flex align-items-center" data-type="new">
                    
                    <button type="button"class="btn btn-sm btn-outline-danger remove-file mr-3"title="Remove">
                        <i class="ri-delete-bin-line"></i>
                    </button>

                    <input type="file"name="${inputName}[]"class="form-control-file"/>
                </div>
                `);
            }


            $(document).on('click', '.remove-file', function() {
                const parent = $(this).closest('[data-type]'); // Either "new" or "existing"
                const type = parent.data('type');
                const container = parent.parent();

                if (type === 'existing') {
                    const file = parent.find('b').text(); // get filename
                    // Append a hidden input to track removed files
                    $('<input>').attr({
                        type: 'hidden',
                        name: `remove_${container.attr('id')}[]`,
                        value: file
                    }).appendTo(container);
                }
                // Remove the file preview from DOM
                parent.remove();
            });
            // Add buttons
            $('#add-id_proofs').click(() => addFileInput('#id_proofs-wrapper', 'id_proofs'));
            $('#add-address_proofs').click(() => addFileInput('#address_proofs-wrapper', 'address_proofs'));
            $('#add-other_attachments').click(() => addFileInput('#other_attachments-wrapper',
                'other_attachments'));
            // Load employee data
            function loadData() {
                $.get("{{ route('employee.edit', '__id__') }}".replace('__id__', emp_id), {
                    token: "{{ session('api_token') }}",
                    company_id: "{{ session('company_id') }}",
                    user_id: "{{ session('user_id') }}"
                }, function(res) {
                    if (res.status === 'success') {
                        loaderhide();
                        const data = res.data;
                        $('#first_name').val(data.first_name);
                        $('#middle_name').val(data.middle_name);
                        $('#surname').val(data.surname);
                        $('#email').val(data.email);
                        $('#mobile').val(data.mobile);
                        $('#address').val(data.address);
                        $('#bank_details').val(data.bank_details);

                        if (data.cv_resume) {
                            $('#cv_preview').html(
                                `<a href="/${data.cv_resume}" target="_blank" class="btn btn-sm btn-primary mt-1 text-white w-100" data-toggle="tooltip" data-placement="bottom" data-original-title="View CV"><i class="ri-eye-fill"></i></a>`
                            );
                        }
                        renderFilePreview('#id_proofs_preview', data.id_proofs, 'id_proofs');
                        renderFilePreview('#address_proofs_preview', data.address_proofs, 'address_proofs');
                        renderFilePreview('#other_attachments_preview', data.other_attachments,'other_attachments');
                    }
                });
            }
            loadData();

            // Submit form
            $('#employeeupdateform').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('employee.update', '__id__') }}".replace('__id__', emp_id),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        loaderhide();
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
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                    }
                });
            });
        });
    </script>
@endpush
