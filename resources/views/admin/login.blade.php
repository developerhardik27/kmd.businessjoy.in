<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - login</title>
    <link href="{{asset('landing/img/favicon.png')}}" rel="icon">
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">
</head>

<body>
  

    @if (Auth::guard('admin')->user())
        return redirect()->route('admin.menu');
    @endif

    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="{{ route('admin.authenticate') }}" method="post">
                @csrf
                <img src="{{ asset('admin/images/bjlogo3.png') }}" width="230px" alt="logo">
                <h2>Sign In</h2>
                @if (Session::has('unauthorized'))
                    <span style="color: red"><b> &#9888; {{ Session::get('unauthorized') }}</b></span>
                @endif
                <input type="email" id="username" name="email" placeholder="email" value='{{ session()->has("email") ? session("email") : old("email") }}'
                    required>
                <input type="password" id="password" name="password" placeholder="password" value="" required>
                @if (Session::has('success'))
                    <span style="color: green"><b> &#10003; {{ Session::get('success') }}</b></span>
                @endif
                @if (Session::has('error'))
                    <span style="color: red"><b> &#9888; {{ Session::get('error') }}</b></span>
                @endif
                <a href="{{ route('admin.forgot') }}">Forget your Password?</a>
                <button>Sign In</button>
                <p><a href="#">Version 1.0</a></p>
            </form>
        </div>
    </div>
</body>

</html>
