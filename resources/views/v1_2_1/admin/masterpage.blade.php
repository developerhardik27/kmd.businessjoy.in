@php
    $folder = session('folder_name');
@endphp

@include($folder.'.admin.header')
@include($folder.'.admin.sidebar')
@include($folder.'.admin.navbar')

@yield('page-content')

@yield('view-content')
@include($folder.'.admin.footer')
