@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    Employee
@endsection
@section('table_title')
    Employee List
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

@section('addnew')
    {{ route('admin.addemployee.form') }}
@endsection

@section('addnewbutton')
    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Employee"
        class="btn btn-sm btn-primary">
        <span class="">+ Add New Employee</span>
    </button>
@endsection

@section('table-content')
    <table id="data" class="table table-bordered display table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>first name</th>
                <th>last name</th>
                <th>surname</th>
                <th>email</th>
                <th>address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script>
        loaderhide();
    </script>
    <script>
        $('document').ready(function() {

            // // companyId and userId both are required in every ajax request for all action *************
            // // response status == 200 that means response succesfully recieved
            // // response status == 500 that means database not found
            // // response status == 422 that means api has not got valid or required data

            var global_response = '';
            // load blog in table 
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
                        url: "{{ route('employee.index') }}",
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
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'first_name',
                            name: 'first_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'middle_name',
                            name: 'middle_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'surname',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'surname'
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'address',
                            name: 'address',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },

                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';

                                @if (session('user_permissions.hrmodule.employees.view') == '1')
                                    actionBtns += `   
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button"  data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.hrmodule.employees.edit') == '1')
                                    let editEmployeeUrl =
                                        `{{ route('admin.editemployee', '__id__') }}`.replace(
                                            '__id__', data);
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <a href='${editEmployeeUrl}'>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0 mb-2">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.hrmodule.employees.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                            <button type="button" data-id= '${data}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif

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

            //call function for loaddata
            loaddata();

            // delete bank             
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                var row = this;
                let empDeleteUrl = "{{ route('employee.delete', '__deleteId__') }}".replace(
                    '__deleteId__',
                    deleteid);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this employee!', // Text
                    'Yes, delete it!', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    function() {
                        loadershow();
                        ajaxRequest('PUT', empDeleteUrl, {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        }).done(function(response) {
                            if (response.status == "success") {
                                Toast.fire({
                                    icon: "success",
                                    title: response.message
                                });
                                table.draw();
                            } else {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message ||
                                        "something went wrong!"
                                });
                            }
                            loaderhide();
                        }).fail(function(xhr) {
                            loaderhide();
                            handleAjaxError(xhr);
                        });
                    }
                );

            });

            function decodeHtmlEntities(str) {
                let txt = document.createElement('textarea');
                txt.innerHTML = str;
                return txt.value;
            }
            // view bank data in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, employee) {
                    if (employee.id == data) {
                        let id_proofs = getArray(employee.id_proofs);
                        let address_proofs = getArray(employee.address_proofs);
                        let other_attachments = getArray(employee.other_attachments);

                        function getArray(value) {
                            if (!value) return [];
                            if (Array.isArray(value)) return value;

                            try {
                                return JSON.parse(decodeHtmlEntities(value));
                            } catch {
                                return [];
                            }
                        }

                        $('#details').append(`
                        <tr>
                            <th>Id</th>
                            <td>${employee.id || '-'}</td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td>${employee.first_name + employee.last_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>${employee.email || '-'}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>${employee.mobile || '-'}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>${employee.address || '-'}</td>
                        </tr>
                        <tr>
                            <th>CV</th>
                                <td>
                                ${employee.cv_resume 
                                    ? `<a class="text-primary font-weight-bold" href="/${employee.cv_resume}" target="_blank">${employee.cv_resume.split('/').pop()}</a>` 
                                    : '-'}
                                </td>
                        </tr>
                        <tr>
                            <th>ID Proofs</th>
                            <td>
                                ${id_proofs.length > 0
                                    ? id_proofs.map(attachment => 
                                        `<a class="text-primary font-weight-bold"
                                                            href="/${attachment}"
                                                            target="_blank">
                                                            ${attachment.split('/').pop()}
                                                        </a>`
                                    ).join('<br><br>')
                                    : '-'
                                }
                            </td>
                        </tr>

                        <tr>
                            <th>Address Proofs</th>
                            <td>
                                ${address_proofs.length > 0
                                    ? address_proofs.map(attachment => 
                                        `<a class="text-primary font-weight-bold"
                                                            href="/${attachment}"
                                                            target="_blank">
                                                            ${attachment.split('/').pop()}
                                                        </a>`
                                    ).join('<br><br>')
                                    : '-'
                                }
                            </td>
                        </tr>
                         <tr>
                            <th>Other Attachments</th>
                            <td>
                                ${other_attachments.length > 0
                                    ? other_attachments.map(attachment => 
                                        `<a class="text-primary font-weight-bold"
                                                            href="/${attachment}"
                                                            target="_blank">
                                                            ${attachment.split('/').pop()}
                                                        </a>`
                                    ).join('<br><br>')
                                    : '-'
                                }
                            </td>
                        </tr>
                       
                    `);
                    }
                });
            });
        });
    </script>
@endpush
