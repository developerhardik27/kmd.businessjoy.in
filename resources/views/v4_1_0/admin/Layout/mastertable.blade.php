@php
    $folder = session('folder_name');
@endphp
<style>
    #details th,
    td {
        text-transform: none !important;
    }
</style>

@include($folder . '.admin.Layout.header')
@include($folder . '.admin.Layout.sidebar')
@include($folder . '.admin.Layout.navbar')

<!-- Page Content  -->
<div id="content-page" class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title float-left col-auto">
                            <h4 class="card-title">@yield('table_title')</h4>
                        </div>
                        <div class="float-right row">
                            <div class="float-right">
                                <a href="@yield('addnew')">
                                    @yield('addnewbutton')
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @yield('advancefilter')
                    </div>
                    <div class="iq-card-body">
                        <div id="table" class="table-editable">
                            @yield('table-content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Offcanvas Sidebar (Right Side) -->
<div id="offcanvasMenu" class="offcanvas-custom offcanvas-right">
    <div class="offcanvas-header d-flex justify-content-between align-items-center">
        <h5>Filters</h5>
        <button onclick="hideOffCanvass()" class="close">&times;</button>
    </div>
    <div class="offcanvas-body px-0">
        @yield('sidebar-filters')
    </div>
    <div class="offcanvas-footer d-flex justify-content-between p-3 border-top bg-light">
        <button id="applyfilters" data-toggle="tooltip" data-placement="top" data-original-title="Apply Filters"
            class="btn btn-success btn-rounded btn-sm applyfilters">
            Apply Filters
        </button>
        <button id="removefilters" data-toggle="tooltip" data-placement="top" data-original-title="Reset Filters"
            class="btn btn-outline-danger btn-rounded btn-sm removefilters">
            Reset Filters
        </button>
    </div>
</div>

<!-- Overlay -->
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>


<!-- Modal -->
<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle"><span id="viewmodaltitle"><b>Details</b></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="details" width='100%' class="table table-bordered table-responsive-md table-striped">
                </table>
            </div>
            <div class="modal-footer">
                <span id="addfooterbutton"></span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@include($folder . '.admin.Layout.footer')
