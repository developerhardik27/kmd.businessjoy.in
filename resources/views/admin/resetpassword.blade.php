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
    <title>{{ config('app.name') }} | Reset Password</title>
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">

</head>

<body>
    @if (Auth::guard('admin')->user())
        return redirect()->route('admin.index');
    @endif
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="" method="post">
                <img src="{{ asset('admin/images/KMD Logo1.png') }}" width="150px" height="150px" alt="logo">
                @csrf
                <h1>Reset Password</h1>
                <p> Enter Your New Password</p>
                <input type="password" id="password" name="password" placeholder="password" required>
                <input type="text" id="cpassword" name="cpassword" placeholder="confirm password" required>
                @if (Session::has('success'))
                    <span style="color: green"><b> &#10003; {{ Session::get('success') }}</b></span>
                @endif
                @if (Session::has('error'))
                    <span style="color: red"><b> &#9888; {{ Session::get('error') }}</b></span>
                @endif
                <button>Reset</button>
            </form>
        </div>
    </div>
</body>

</html>
