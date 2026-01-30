@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Lead Analysis
@endsection
@section('table_title')
    Lead Analysis
@endsection

@section('style')
@endsection

@section('table-content')
    <div class="row">
        <div class="col-md-5">
            <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Conversion Funnel</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="lead-stage-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-7 col-lg-7">
            <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Source Chart</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div id="lead-source-pie-chart"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="iq-card">
                <div class="iq-card-header">
                    <h4 class="card-title">User-wise Lead Count</h4>
                </div>
                <div class="iq-card-body">
                    <table class="table table-bordered" id="user-lead-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Total Leads</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            function fetchAndDrawLeadStageChart() {
                $.ajax({
                    url: "{{ route('lead.stagechart') }}",
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(stageData) {
                        if (Array.isArray(stageData.lead) && stageData.lead.length > 0 && stageData
                            .lead[0]
                            .hasOwnProperty('lead_stage')) {
                            const categories = stageData.lead.map(item => item.lead_stage || 'Unknown');
                            const data = stageData.lead.map(item => parseInt(item.total));

                            Highcharts.chart('lead-stage-chart', {
                                chart: {
                                    type: 'bar'
                                },
                                title: {
                                    text: 'Lead Count by Stage'
                                },
                                xAxis: {
                                    categories: categories,
                                    title: {
                                        text: 'Lead Stage'
                                    }
                                },
                                yAxis: {
                                    min: 0,
                                    title: {
                                        text: 'Number of Leads'
                                    }
                                },
                                series: [{
                                    name: 'Leads',
                                    data: data,
                                    color: '#4caf50'
                                }],
                                credits: {
                                    enabled: false
                                }
                            });
                        } else {
                            $('#lead-stage-chart').html('<p>No stage data available</p>');
                        }
                    },
                    error: function(err) {
                        console.error('Error loading stage chart:', err);
                        $('#lead-stage-chart').html('<p>Error loading chart data</p>');
                    }
                });
            }


            function leadPieChart() {
                loadershow();
                const chartContainer = $("#lead-source-pie-chart");

                if (!chartContainer.length) return;

                // Optional: Clear previous chart content
                chartContainer.html("");

                const params = new URLSearchParams({
                    user_id: "{{ session()->get('user_id') }}",
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}"
                });

                fetch("{{ route('lead.sourcepiechart') }}?" + params.toString())
                    .then(res => res.json())
                    .then(response => {
                        const apiData = response.lead;

                        if (!Array.isArray(apiData) || apiData.length === 0) {
                            chartContainer.html(
                                `<div style="text-align: center; padding: 2rem; font-weight: bold; color: #888;">No lead data found</div>`
                            );
                            return;
                        }

                        const colorPalette = [
                            "#827af3", "#b47af3", "#6ce6f4", "#27b345", "#c8c8c8",
                            "#ff9800", "#4caf50", "#f44336", "#9c27b0", "#00bcd4",
                            "#ffc107", "#8bc34a"
                        ];

                        apiData.forEach((item, index) => {
                            item.color = colorPalette[index % colorPalette.length];
                        });

                        const options = {
                            chart: {
                                width: '100%',
                                type: "pie",
                                height: 300
                            },
                            labels: apiData.map(d => d.name),
                            series: apiData.map(d => d.value),
                            colors: apiData.map(d => d.color),
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: '100%', 
                                        height: 300
                                    },
                                    legend: {
                                        position: "bottom"
                                    }
                                }
                            }]
                        };

                        const chart = new ApexCharts(document.querySelector("#lead-source-pie-chart"), options);
                        chart.render();
                        loaderhide();
                    })
                    .catch(error => {
                        loaderhide();
                        console.error("Chart data fetch error:", error);
                        chartContainer.html(
                            `<div style="text-align: center; padding: 2rem; color: red;">Error loading lead data</div>`
                        );
                    });
            }


            function loadUserLeadTable() {
                $.ajax({
                    url: "{{ route('lead.userwiseleadcount') }}",
                    method: "GET",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        if (response.status === 200 && Array.isArray(response.lead)) {
                            const tableBody = $('#user-lead-table tbody');
                            tableBody.empty();

                            response.lead.forEach(row => {
                                tableBody.append(`
                                    <tr>
                                        <td>${row.user_name}</td>
                                        <td>${row.lead_count}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            console.warn("No data returned for user lead stats.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error loading user lead stats:", xhr);
                    }
                });
            }

            fetchAndDrawLeadStageChart();
            leadPieChart();
            loadUserLeadTable();

        });
    </script>
@endpush
