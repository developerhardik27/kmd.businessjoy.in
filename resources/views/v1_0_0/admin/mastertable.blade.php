@php
    $folder = session('folder_name');
@endphp
<style>
    #details th ,  td {
        text-transform: none !important;
    }
</style>

@include($folder.'.admin.header')
@include($folder.'.admin.sidebar')
@include($folder.'.admin.navbar')

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


<!-- Modal -->
<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle"><b>Details</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="details" width='100%'  class="table table-bordered table-responsive-md table-striped">
                </table>
            </div>
            <div class="modal-footer" >
                <span id="addfooterbutton"></span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@include($folder.'.admin.footer')
