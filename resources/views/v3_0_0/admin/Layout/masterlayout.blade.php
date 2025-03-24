@php
    $folder = session('folder_name');
@endphp

@include($folder.'.admin.Layout.header')
@include($folder.'.admin.Layout.sidebar')
@include($folder.'.admin.Layout.navbar')


<!-- Page Content  -->
<div id="content-page" class="content-page ">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title"> @yield('title') </h4>
                        </div>
                    </div>
                    <div class="iq-card-body">       
                        @yield('form-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@yield('view-content')
@include($folder.'.admin.Layout.footer')