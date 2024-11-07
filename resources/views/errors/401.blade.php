<!doctype html>
<html lang="en">
   <head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>{{ config('app.name') }}</title>
      <!-- Favicon -->
      <link rel="shortcut icon" href="{{asset('admin/images/favicon.png')}}"/>
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="{{asset('admin/css/bootstrap.min.css')}}">
      <!-- Typography CSS -->
      <link rel="stylesheet" href="{{asset('admin/css/typography.css')}}">
      <!-- Style CSS -->
      <link rel="stylesheet" href="{{asset('admin/css/style.css')}}">
      <!-- Responsive CSS -->
      <link rel="stylesheet" href="{{asset('admin/css/responsive.css')}}">
   </head>
   <body> 
        <!-- Wrapper Start -->
        <div class="wrapper">
            <div class="container-fluid p-0">
                <div class="row no-gutters">
                    <div class="col-sm-12 text-center">
                        <div class="iq-error">
                            <img src="{{asset('admin/images/error/401.png')}}" class="img-fluid mb-0" alt="">
                            <h2>Unauthorized</h2> 
                            <a class="btn btn-outline-primary mt-3" href="{{route('admin.welcome')}}"><i class="ri-home-4-line"></i>Back to Home</a>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
   </body>
</html>