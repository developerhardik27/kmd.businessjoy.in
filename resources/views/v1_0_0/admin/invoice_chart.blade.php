<!DOCTYPE html>
<html>
<head>
    <title>Invoice Chart</title>
    <!-- Include Highcharts library -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<body>
    <div id="high-linendcolumn-chart" style="width:100%; height:400px;"></div>

    <script type="text/javascript">
        // Parse the data passed from Laravel controller
        const invoicesData = {!! json_encode($invoices) !!};

        // Function to map month numbers to month names
        function getMonthName(monthNumber) {
            const months = [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];
            return months[monthNumber - 1];
        }

        // Mapping month numbers to month names for xAxis categories
        const xAxisCategories = invoicesData.map(item => getMonthName(item.month));

        // Chart configuration for displaying monthly invoice counts with month names
        Highcharts.chart("high-linendcolumn-chart", {
            chart: {
                type: "column"
            },
            title: {
                text: "Monthly Invoice Counts"
            },
            xAxis: {
                categories: xAxisCategories,
                crosshair: true
            },
            yAxis: {
                title: {
                    text: "Number of Invoices"
                }
            },
            series: [{
                name: "Invoices",
                data: invoicesData.map(item => item.count),
                color: "#fbc647"
            }]
        });
    </script>
</body>
</html>