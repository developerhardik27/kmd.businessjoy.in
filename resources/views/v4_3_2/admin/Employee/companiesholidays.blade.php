@php
    $folder = session('folder_name');
    $user_id = session('user_id');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    Companies Holidays
@endsection
@section('table_title')
    Companies Holidays List
@endsection

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        /* Base button style */
        .fc .fc-toolbar .fc-button {
            background: rgba(130, 122, 243, 0.2) !important;
            color: var(--iq-primary) !important;
            border: none;
            text-transform: capitalize;
            border-radius: 8px;
            box-shadow: none;
        }

        /* Active, focused, or clicked buttons */
        .fc .fc-toolbar .fc-button.fc-button-active,
        .fc .fc-toolbar .fc-button:active,
        .fc .fc-toolbar .fc-button:focus {
            color: var(--iq-white) !important;
            background-color: var(--iq-primary) !important;
        }

        /* Optional: remove borders on hover too */
        .fc .fc-toolbar .fc-button:hover {
            background-color: rgba(130, 122, 243, 0.4) !important;
        }

        .fc-header-toolbar {
            flex-wrap: wrap !important;
            gap: 0.5rem;
            /* Optional spacing between buttons */
        }

        .fc-toolbar-chunk {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }

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
@if (session('user_permissions.hrmodule.companiesholidays.add') == '1' || $user_id == 1)
    @section('advancefilter')
        <div class="col-sm-12 text-right px-4">
            <button type="button"class="btn btn-sm btn-primary" data-toggle="modal" data-target="#holidayModal"
                data-placement="bottom" data-original-title="Add New Employee">
                <span>+ Add New Holidays</span>
            </button>
        </div>
        <div class="modal fade" id="holidayModal" tabindex="-1" role="dialog" aria-labelledby="holidayModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="holidayModalLabel">Add New Holiday</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="holidayForm">
                            <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}" />
                            <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}" />
                            <input type="hidden" name="company_id" class="form-control"
                                value="{{ session('company_id') }}" />
                            <div class="form-group">
                                <label>Holiday Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Enter Holiday Name">
                                <span class="error-msg" id="error-name" style="color:red"></span>
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" placeholder="select Holiday date">
                                <span class="error-msg" id="error-date" style="color:red"></span>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Enter Holiday Description"></textarea>
                                <span class="error-msg" id="error-description" style="color:red"></span>
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" form="holidayForm" class="btn btn-primary">Save</button>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="holidayeditModal" tabindex="-1" role="dialog" aria-labelledby="holidayEditLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="holidayEditForm">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}" />
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}" />
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}" />
                    <input type="hidden" name="id" value="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="holidayEditLabel">Edit Holiday</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Holiday Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Holiday</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection

@endif
@section('table-content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                <div class="iq-card-body">
                    <div id="holiday-calendar"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="holidayDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div
                class="modal-content"style=" border: none; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
                <div class="d-flex justify-content-between align-items-center"
                    style="padding: 16px 24px; background: #ffffff; border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <h6
                            style="margin: 0; font-size: 19px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #6366f1;">
                            <i class="ri-information-line" style="margin-right: 5px;"></i> Holiday Details
                        </h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div id="holidayDetailActions" class="d-flex gap-2"></div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            style="position:absolute; right:20px; top:33%; transform: translateY(-50%); z-index: 2;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="d-flex">
                        <div
                            style="background: #f8fafc; padding: 30px 20px; text-align: center; border-right: 1px solid #f1f5f9; min-width: 100px;">
                            <div
                                id="holidayMonth"style="font-size: 12px; font-weight: 700; color: #6366f1; text-transform: uppercase;">
                            </div>
                            <div
                                id="holidayDay"style="font-size: 36px; font-weight: 800; color: #1e293b; line-height: 1; margin: 5px 0;">
                            </div>
                            <div id="holidayYear" style="font-size: 13px; font-weight: 600; color: #94a3b8;"></div>
                        </div>

                        <div
                            style="padding: 24px 30px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; gap: 16px;">

                            <div>
                                <label
                                    style="display: block; font-size: 10px; font-weight: 800; color: #6366f1; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Event
                                    Title</label>
                                <div id="holidayName"
                                    style="font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1.2;">
                                </div>
                            </div>

                            <div style="width: 40px; height: 2px; background: #f1f5f9; border-radius: 2px;"></div>

                            <div>
                                <label
                                    style="display: block; font-size: 10px; font-weight: 800; color: #6366f1; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Description</label>
                                <div id="holidayDescription"
                                    style="font-size: 16px; color: #1e293be1; font-weight: 600; line-height: 1.1; max-height: 150px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="height: 6px; background: linear-gradient(90deg, #6366f1 0%, white 100%);"></div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script>
        $(document).ready(function() {
            const params = new URLSearchParams({
                user_id: "{{ session()->get('user_id') }}",
                company_id: "{{ session()->get('company_id') }}",
                token: "{{ session()->get('api_token') }}"
            });
            let user_id = "{{ session()->get('user_id') }}";
            const user_edit = "{{ session()->get('user_permissions.hrmodule.companiesholidays.edit') }}";
            const user_delete = "{{ session()->get('user_permissions.hrmodule.companiesholidays.delete') }}";
            $("#holidayForm").on("submit", function(e) {
                e.preventDefault();
                loadershow()
                $.ajax({
                    url: "{{ route('holiday.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        loaderhide();
                        if (response.status == "success") {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.companiesholidays') }}";
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        loaderhide();
                        console.log(xhr.responseText);
                        handleAjaxError(xhr);
                    }
                });
            });

            function holidayCalendar() {
                loadershow();
                var calendarEl = document.getElementById('holiday-calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'today',
                        center: 'prev title next',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    events: function(fetchInfo, successCallback, failureCallback) {
                        fetch("{{ route('holiday.index') }}?" + params.toString())
                            .then(response => response.json())
                            .then(data => {
                                if (data.status !== 'success') {
                                    throw new Error('Failed to load holidays');
                                }
                                const events = data.holiday.map(item => ({
                                    id: item.id,
                                    title: item.name,
                                    start: item.date, // YYYY-MM-DD works perfectly
                                    allDay: true,
                                    description: item.description,
                                    extendedProps: {
                                        name: item.name,
                                        created_by: item.created_by,
                                        type: 'holiday'
                                    }
                                }));
                                successCallback(events);
                                loaderhide();
                            })
                            .catch(error => {
                                loaderhide();
                                failureCallback(error);

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message
                                });
                            });
                    },
                    eventClick: function(info) {
                        const eventDate = info.event.start;
                        const month = eventDate.toLocaleDateString('en-US', {
                            month: 'short'
                        });
                        const day = eventDate.getDate();
                        const year = eventDate.getFullYear();
                        $('#holidayMonth').text(month);
                        $('#holidayDay').text(day);
                        $('#holidayYear').text(year);
                        $('#holidayName').text(info.event.extendedProps.name);
                        $('#holidayDescription').html(
                            info.event.extendedProps.description ?
                            info.event.extendedProps.description :
                            '<span style="color: #cbd5e0; font-style: italic;">No additional details provided for this event.</span>'
                        );
                        let actionsHTML = '';
                        if (user_edit == 1 || user_id == 1) {
                            actionsHTML += `
                                <button type="button" class="btn edit-holiday" data-id="${info.event.id}" style="border:none; background:#eef2ff; margin-right:5px; color:#4338ca; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; transition:0.3s;">
                                    <i class="ri-edit-2-line" style="font-size:16px;"></i>
                                </button>`;
                        }
                        if (user_delete == 1 || user_id == 1) {
                            actionsHTML += `
                                <button type="button" class="btn delete-holiday" data-id="${info.event.id}" style="border:none; background:#fff1f2; margin-right:5px;  color:#e11d48; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; transition:0.3s;">
                                    <i class="ri-delete-bin-6-line" style="font-size:16px;"></i>
                                </button>`;
                        }
                        $('#holidayDetailActions').html(actionsHTML);
                        $('#holidayDetailModal').modal('show');
                    },
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.type === 'holiday') {
                            info.el.style.backgroundColor = '#28a745';
                            info.el.style.borderColor = '#28a745';
                            info.el.style.color = '#fff';
                        }
                    }

                });

                calendar.render();
            }

            holidayCalendar();
            $('#holidayModal').on('hidden.bs.modal', function(event) {
                $("#holidayForm")[0].reset();
                $("#holidayForm .error-msg").text("");
            })

            $(document).on("click", ".edit-holiday", function() {
                const id = $(this).data("id");
                const url = "{{ route('holiday.edit', ['id' => '__id__']) }}".replace('__id__', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        $("#holidayDetailModal").modal("hide");
                        loaderhide();
                        if (response.status === "success") {
                            const holiday = response.data;
                            $("#holidayeditModal input[name='name']").val(holiday.name);
                            $("#holidayeditModal input[name='date']").val(holiday.date);
                            $("#holidayeditModal textarea[name='description']").val(holiday
                                .description);
                            $("#holidayeditModal input[name='id']").val(holiday.id);
                            $("#holidayeditModal").modal("show");
                            Swal.close();
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Toast.fire({
                            icon: "error",
                            title: "Failed to fetch holiday data"
                        });
                    }
                });
            });

            $("#holidayEditForm").on("submit", function(e) {
                e.preventDefault();
                loadershow();
                const holidayId = $("input[name='id']").val();
                const url = "{{ route('holiday.update', ['id' => '__id__']) }}".replace('__id__',holidayId);
                $.ajax({
                    url: finalUrl,
                    type: "put",
                    data: $(this).serialize(),
                    success: function(response) {
                        loaderhide();
                        if (response.status == "success") {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $("#holidayEditForm")[0].reset();
                            $("#holidayeditModal").modal("hide");
                            window.location = "{{ route('admin.companiesholidays') }}";
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        console.log(xhr.responseText);
                        Toast.fire({
                            icon: "error",
                            title: "Failed to update holiday"
                        });
                    }
                });
            });
            $(document).on("click", ".delete-holiday", function() {
                $("#holidayDetailModal").modal("hide");
                const id = $(this).data("id");
                const url = "{{ route('holiday.delete', ['id' => '__id__']) }}".replace('__id__', id);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the holiday!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "put",
                            data: {
                                user_id: "{{ session()->get('user_id') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                token: "{{ session()->get('api_token') }}"
                            },
                            success: function(response) {
                                if (response.status === "success") {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    window.location =
                                        "{{ route('admin.companiesholidays') }}";
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Toast.fire({
                                    icon: "error",
                                    title: "Failed to delete holiday"
                                });
                            }
                        });
                        Swal.close();
                    }
                });
            });

        });
    </script>
@endpush
