<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Invoice - @yield('page_title') </title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }} " />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    <!-- Chart list Js -->
    <link rel="stylesheet" href="{{ asset('admin/js/chartist/chartist.min.css') }}">
    <!-- Typography CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/typography.css') }}">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/responsive.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/r/ju-1.11.4/jqc-1.11.3,dt-1.10.8/datatables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    
    <style>
        .loader {
            display: none;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .button-container {
            position: relative;
            display: inline-block;
        }
     
        table.dataTable {
            border-collapse: collapse !important;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    @yield('style')
</head>
