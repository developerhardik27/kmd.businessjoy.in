@include('admin.header')
@include('admin.sidebar')
@include('admin.navbar')


<!-- Page Content  -->
<div id="content-page" class="content-page ">
    <div class="container-fluid">
        <div class="row">
                @yield('page-content')
        </div>
    </div>
</div>


@yield('view-content')
@include('admin.footer')