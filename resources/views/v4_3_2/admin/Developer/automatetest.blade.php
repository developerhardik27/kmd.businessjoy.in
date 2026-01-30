@php
    $folder = session('folder_name');
@endphp

@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Automation Testing
@endsection

@section('title')
    Automation Testing
@endsection

@section('form-content')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 ">
                            <i class="ri-flask-line"></i> Testing Guide
                        </h4>
                        <small>Automated & Manual Testing Documentation</small>
                    </div>
                    <i class="ri-terminal-box-line fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="ri-information-line text-primary"></i> Overview
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        This module manages all <strong>automated testing workflows</strong> used to validate system logic,
                        user permissions, data integrity, and application stability.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Testing Types -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="ri-list-check-2 text-success"></i> Test Types
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Unit Testing</strong> – Tests individual helper functions & services.
                        </li>
                        <li class="list-group-item">
                            <strong>Feature Testing</strong> – Tests complete workflows with database.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Run Command -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="ri-terminal-box-line text-dark"></i> Run Tests
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Run all tests:</strong></p>
                    <pre class="bg-dark text-white p-3 rounded">php artisan test</pre>
                </div>
            </div>
        </div>
    </div>
    <!-- Folder Structure -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="ri-folder-3-line text-warning"></i> Folder Structure
                    </h6>
                </div>
                <div class="card-body">
<pre class="bg-light p-3 rounded">
tests/
├── Feature/
│   ├── MasterSetupTest.php
│   ├── ConfigTest.php
│   ├── CompanyCreationTest.php
│   ├── AuthTest.php
│   ├── SuperAdminTest.php
│   └── AdminModule/
│       ├── CompanyTest.php
│       └── UserTest.php
├── Unit/
   └──ExampleTest.php

</pre>
                </div>
            </div>
        </div>
    </div>
    <!-- Important Notes -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning shadow-sm">
                <h6><i class="ri-alert-line"></i> Important Notes</h6>
                <ul class="mb-0">
                    <li>Always use a <strong>separate testing database</strong>.
                    Never run test commands on production.</li>
                    <li>Ensure <code>.env.testing</code> is properly configured.</li>
                </ul>
            </div>
        </div>
    </div>

</div>

@endsection

@push('ajax')
<script>
    $(document).ready(function () {
        // Global ajax loader handling
        loaderhide();
    });
</script>
@endpush
