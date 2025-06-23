@php
    $folder = session('folder_name');
@endphp

@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Lead Owner Performance
@endsection

@section('table_title')
    Lead Owner Performance
@endsection

@section('style')
@endsection

@section('table-content')
    <div class="row">
        <div class="col-md-12">
            <div class="iq-card"> 
                <div class="iq-card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Lead Owner</th>
                                <th>Leads Handled</th>
                                <th>% of Total Leads</th>
                                <th>Converted (Sale)</th>
                                <th>Conversion %</th>
                                <th>Follow-ups Today</th>
                            </tr>
                        </thead>
                        <tbody id="summary-body">
                            <tr>
                                <td colspan="6">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-up Details Modal -->
    <div class="modal fade" id="followupModal" tabindex="-1" role="dialog" aria-labelledby="followupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Follow-up Due Leads</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="dueFollowupsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Lead Title</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Assigned To</th>
                                <th>Next Follow-Up</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4">No data</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function() {
            fetchUserLeadSummary();

            function fetchUserLeadSummary() {
                loadershow();
                $.ajax({
                    url: "{{ route('lead.userleadsummary') }}",
                    method: "GET",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        if (response.status === 200 && Array.isArray(response.lead)) {
                            let html = "";
                            response.lead.forEach(row => {
                                html += `
                                    <tr>
                                        <td>${row.owner}</td>
                                        <td>${row.lead_count}</td>
                                        <td>${row.lead_percent}%</td>
                                        <td>${row.converted_count}</td>
                                        <td>${row.conversion_percent}%</td>
                                        <td>
                                            ${
                                                row.delays && row.delays.length > 0
                                                ? `<button class="btn btn-sm btn-danger view-due-followups" data-owner='${row.owner}'>${row.delays.length}</button>`
                                                : `<span class="badge badge-secondary">0</span>`
                                            }
                                        </td>
                                    </tr>
                                `;
                            });
                            $('#summary-body').html(html);
                        } else {
                            $('#summary-body').html('<tr><td colspan="6">No data found</td></tr>');
                        }
                        loaderhide();
                    },
                    error: function() {
                        loaderhide();
                        $('#summary-body').html('<tr><td colspan="6">Error loading data</td></tr>');
                    }
                });
            }

            // View Due Follow-ups Modal
            $(document).on('click', '.view-due-followups', function() {
                loadershow();
                const owner = $(this).data('owner');

                $.ajax({
                    url: "{{ route('lead.followupdueleads') }}",
                    method: 'GET',
                    data: {
                        owner: owner,
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        const tbody = $('#dueFollowupsTable tbody');
                        tbody.empty();

                        if (response.status === 200 && Array.isArray(response.lead) && response.lead.length > 0) {
                            response.lead.forEach(lead => {
                                tbody.append(`
                                    <tr>
                                        <td>${lead.lead_title || '-'}</td>
                                        <td>${lead.first_name || '-'}</td>
                                        <td>${lead.last_name || '-'}</td>
                                        <td>${lead.assigned_to || '-'}</td>
                                        <td>${lead.next_follow_up_formatted || '-'}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            tbody.html('<tr><td colspan="5">No due follow-ups found.</td></tr>');
                        } 

                        $('#followupModal').modal('show');
                        loaderhide();
                    },
                    error: function() {
                        loaderhide();
                        $('#dueFollowupsTable tbody').html('<tr><td colspan="5">Error loading follow-up data.</td></tr>');
                        $('#followupModal').modal('show');
                    }
                });
            });
        });
    </script>
@endpush
