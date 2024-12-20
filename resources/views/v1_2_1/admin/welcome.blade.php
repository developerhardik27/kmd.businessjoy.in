@php
    $folder = session('folder_name');
@endphp
@extends($folder.'.admin.masterpage')

@section('page_title')
    {{ config('app.name') }} - Welcome
@endsection

@section('style')
    <style>
        .loader-container {
            display: none;
        }
    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h2>Welcome</h2>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <p id='nothasmenu'>You have logged in <span id="pagemenu">Invoice</span> Module Succesfully </p>
                            <!-- HTML to write -->
                            @if (session('menu') != null)
                                <a href="{{ route('admin.index') }}" id='dashboardbtn'
                                    class="btn btn-sm btn-primary text-white" data-placement="bottom" data-toggle="tooltip"
                                    title="Go To Dashboard">
                                    Go to Dashboard >
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            loaderhide();
        });
    </script>
@endpush
