<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    {{-- <link rel="stylesheet" href="{{asset('admin/css/typography.css')}} "> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | Forgot Password</title>
    <link href="{{asset('landing/img/favicon.png')}}" rel="icon">
    <link rel="stylesheet" href="{{asset('admin/css/login.css')}}">
</head>

<body>
    @if (Auth::guard('admin')->user())
        return redirect()->route('admin.index');
    @endif




    <div class="container" id="container">
        
        <div class="form-container sign-in">
            <form action="{{ route('admin.forgotpassword') }}" method="post">
                <img src="{{ asset('admin/images/bjlogo3.png') }}" width="230px" alt="logo">
                @csrf
                <h2>Forgot Password</h2>
                <input type="email" id="username" name="email" placeholder="Enter Email" required>
                @if (Session::has('success'))
                <span style="color: green"><b> &#10003; {{ Session::get('success') }}</b></span>
                @endif
                @if (Session::has('error'))
                    <span style="color: red"><b> &#9888; {{ Session::get('error') }}</b></span>
                @endif
                <a href="{{ route('admin.login') }}">&larr; Back to Login</a>
                <button>Send link</button>
            </form>
        </div>
    </div>
</body>

</html>
