@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Version Documents
@endsection
@section('table_title')
    Version Features
@endsection

@section('style')
    <style>
        .btn-info {
            background-color: #253566 !important;
            border-color: #253566 !important;
            color: white !important;
        }

        .btn-info:hover {
            background-color: #39519b !important;
            color: rgb(255, 255, 255);
        }

        .version-section {
            margin-bottom: 2rem;
        }

        .main-version {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #007bff;
        }

        .sub-versions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .version-card {
            padding: 1rem;
            border: 1px solid #17a2b8;
            background-color: #e9f7fc;
            border-radius: 6px;
            text-align: center;
            min-width: 160px;
        }

        .version-card a {
            text-decoration: none;
        }
    </style>
@endsection
@section('table-content')
    @include('partial.versionchangedocuments')
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            loaderhide();

        });
    </script>
@endpush
