@php
    $folder = session('folder_name');
@endphp

@include($folder.'.admin.Layout.header')
@include($folder.'.admin.Layout.sidebar')
@include($folder.'.admin.Layout.navbar')

@yield('page-content')

@yield('view-content')
@include($folder.'.admin.Layout.footer')
