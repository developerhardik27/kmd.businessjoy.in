<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{-- dynamic page title  --}}
    <title> @yield('page_title') </title>
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
        href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" />
        <link rel="stylesheet" type="text/css" 
      href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('admin/css/summernote-bs4.css') }}">
    <!-- Dropzone CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">

    <style>

        .dt-type-numeric{
            text-align: left !important;
        }

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

        .btn-danger {
            border-radius: 5px !important;
        }

        .note-editing-area .card-block {
            display: block !important;
        }

        .multiselect-container {
            width: 300px;
            max-height: 300px;
            overflow: auto;
            /* Set your desired width here */
        }

        /* div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm) {
            background-color: #253566 !important;
        }

        div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-cancel) {
            background-color: #FF7A29 !important;
        } */
    </style>

    @yield('style')
</head>
