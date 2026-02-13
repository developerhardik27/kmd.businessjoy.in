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
    <link href="{{ asset('landing/img/favicon.png') }}" rel="icon">
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">
    <style>
        .loader-container {
            /* display: none; */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.5;
            z-index: 9999;
            /* Semi-transparent background */

        }

        .loader-img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100px;
            transform: translate(-50%, -50%);
            /* Add any additional styling for your loader image */
        }

        .blurred-content {
            filter: blur(5px);
            /* Adjust the blur value according to your preference */
            /* Add any additional styling for your content */
        }

        .remove-blur {
            filter: none;
            /* Remove the blur effect */
        }

        .grecaptcha-badge {
            visibility: hidden !important;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
</head>

<body>
    <div class="container" id="container">
        <div id="loader-container" class="loader-container">
            <img id="loader" class="loader-img" src="{{ asset('admin/images/BusinessJoyLoader.gif') }}"
                alt="Loader">
        </div>

        <div class="form-container sign-in wrapper blurred-content">
            <form id="loginform">
                @csrf
                <img src="{{ asset('admin/images/KMD Logo1.png') }}" width="150px" height="150px" alt="logo">
                <h2>Sign In</h2>

                <span class="error-msg" id="unauthorized" style="color: red">
                    <b>{{ Session::get('unauthorized') }}</b>
                </span>

                <input type="email" name="email" placeholder="email" required>
                <span style="color: red" class="error-msg" id="error-email"></span>
                <input type="password" id="password" name="password" placeholder="password" required>
                <span style="color: red" class="error-msg" id="error-password"></span>

                <span id="error" class="error-msg" style="color: red"><b> {{ Session::get('error') }}</b></span>

                <a href="{{ route('admin.forgot') }}">Forgot Your Password?</a>
                <button>Sign In</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('admin/js/jquery.min.js') }} "></script>
    <script>
        function loadershow() {
            $(".wrapper").addClass("blurred-content").removeClass("remove-blur");
            $("#loader-container").show();
        }

        function loaderhide() {
            $("#loader-container").hide();
            $(".wrapper").removeClass("blurred-content").addClass("remove-blur");
        }

        $('document').ready(function() {
            loaderhide();
            $('#loginform').submit(function(event) {
                loadershow();
                event.preventDefault();
                $('.error-msg').text('');
                const formData = new FormData(this);
                grecaptcha.ready(function() {
                    var recaptchaSiteKey = "{{ env('RECAPTCHA_SITE_KEY') }}";
                    grecaptcha.execute(recaptchaSiteKey, {
                            action: 'submit'
                        })
                        .then(function(token) {
                            // Append reCAPTCHA response token to formData
                            formData.append('g-recaptcha-response', token);

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('admin.authenticate') }}",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    // Handle the response from the server
                                    if (response.status == 200) {
                                        window.location = response
                                            .redirectUrl; // after succesfully data submit redirect on list page
                                    } else {
                                        $('#error').text(response.message);
                                    }
                                    loaderhide();
                                },
                                error: function(xhr, status, error) {
                                    loaderhide();
                                    console.log(xhr.responseText); // Full response for debugging

                                    if (xhr.status === 422) {
                                        var errors = xhr.responseJSON.errors;
                                        $.each(errors, function(key, value) {
                                            $('#error-' + key).text(value[0]);
                                        });
                                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                        // Use the message from JSON response
                                        $('#error').text(xhr.responseJSON.message);
                                    } else {
                                        // Fallback error message
                                        $('#error').text('An unexpected error occurred');
                                    }
                                }
                            })
                        })
                        .catch(function(error) {
                            console.error('reCAPTCHA execution error:', error);
                            toastr.error("Failed to verify reCAPTCHA, please try again.");
                        });
                });
            });
        });
    </script>
</body>

</html>
