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
     
        .button-container {
            position: relative;
            display: inline-block;
        }

        table.dataTable {
            border-collapse: collapse !important;
        }

        .loader-container {
            /* display: none; */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.5;
            z-index: 9999;
            /* Semi-transparent background */
            
        }

        .loader-img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100px;
            transform: translate(-50%, -50%);
            /* Add any additional styling for your loader image */
        }
        .blurred-content {
            filter: blur(5px);
            /* Adjust the blur value according to your preference */
            /* Add any additional styling for your content */
        }

        .remove-blur {
            filter: none;
            /* Remove the blur effect */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .btn-danger{
            border-radius: 5px !important ;
        }
    </style>
    @yield('style')
</head>
