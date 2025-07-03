@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Lead Calendar
@endsection

@section('table_title')
    Lead Calendar
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
    </style>
@endsection

@section('table-content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                <div class="iq-card-body">
                    <div class="mb-3">
                        <span class="badge" style="background-color: #dc3545;">&nbsp;&nbsp;</span> Next Follow-Up (Red)
                        &nbsp;&nbsp;&nbsp;
                        <span class="badge" style="background-color: #17a2b8;">&nbsp;&nbsp;</span> Call History (Info Blue)
                    </div>
                    <div id="lead-calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script>
        $(document).ready(function() {
            function leadCalendar() {
                loadershow();

                var calendarEl = document.getElementById('lead-calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'today',
                        center: 'prev title next',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    events: function(fetchInfo, successCallback, failureCallback) {
                        const params = new URLSearchParams({
                            user_id: "{{ session()->get('user_id') }}",
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}"
                        });

                        fetch("{{ route('lead.getcalendardata') }}?" + params.toString())
                            .then(response => {
                                return response.json().then(data => {
                                    if (data.status !== 200) {
                                        throw new Error(data.message ||
                                            'Failed to fetch calendar data');
                                    }
                                    return data.leaddetails || [];
                                });
                            })
                            .then(data => {
                                successCallback(data);
                                loaderhide();
                            })
                            .catch(error => {
                                loaderhide();
                                failureCallback(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Access Denied',
                                    text: error.message,
                                    confirmButtonText: 'Close'
                                });
                            });
                    },
                    eventClick: function(info) {
                        // Format date nicely using JS
                        function formatDate(dateString) {
                            const options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            };
                            const date = new Date(dateString);
                            return date.toLocaleString('en-US', options);
                        }

                        // Extract and parse description to inject formatted date
                        let description = info.event.extendedProps.description;

                        // Regex to replace raw date string in description with formatted one
                        description = description.replace(
                            /(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/,
                            (match) => formatDate(match)
                        );

                        Swal.fire({
                            title: info.event.title,
                            html: `<div style="text-align: start;">${description}</div>`, // left align
                            icon: info.event.extendedProps.type === 'next_follow_up' ?
                                'warning' : 'info',
                            confirmButtonText: 'Close',
                            customClass: {
                                htmlContainer: 'swal2-html-container-left' // optional custom class for more styling if needed
                            }
                        });
                    },
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.type === 'next_follow_up') {
                            info.el.style.setProperty('color', '#ffffff', 'important');
                            info.el.style.backgroundColor = '#dc3545';
                            info.el.style.borderColor = '#dc3545';
                        } else if (info.event.extendedProps.type === 'call_history') {
                            info.el.style.setProperty('color', '#ffffff', 'important');
                            info.el.style.backgroundColor = '#17a2b8';
                            info.el.style.borderColor = '#17a2b8';
                        }
                    }
                });

                calendar.render();
            }

            leadCalendar();
        });
    </script>
@endpush
