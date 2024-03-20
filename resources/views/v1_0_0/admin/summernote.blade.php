<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Summernote with Bootstrap 4</title>


    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    
    <!-- Style CSS -->
    
    {{-- <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('admin/css/summernote-bs4.css') }}">
  <style>
   .note-editing-area .card-block{
        display: block !important;
    }
  </style>

</head>

<body>
    <textarea name="history_notes" id="history_notes" cols="10" rows="1" class="form-control"></textarea>
                                
    <script src="{{ asset('admin/js/jquery.min.js') }} "></script>
    <script src="{{ asset('admin/js/popper.min.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
    
   
    <script src="{{ asset('admin/js/summernote-bs4.js') }}"></script>
   

    <script>
        $('document').ready(function() {
            $('#history_notes').summernote({
                placeholder: 'Hello Bootstrap 4',
                tabsize: 2,
                height: 100
            });
        });
    </script>
</body>

</html>
