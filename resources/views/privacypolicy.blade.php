<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Business Joy - Privacy And Policy</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('landing/img/favicon.png') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500&family=Jost:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('landing/lib/animate/animate.min.css') }} " rel="stylesheet">
    <link href="{{ asset('landing/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('landing/css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('landing/otherpagecss/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/css/responsive.css') }}" />
    <!-- Template Stylesheet -->
    <link href="{{ asset('landing/css/style.css') }}" rel="stylesheet">
    <!-- Responsive Stylesheet -->
    <link href="{{ asset('landing/css/responsive.css') }}" rel="stylesheet">

    <style> 
         .hero-header {
            margin-bottom: 0;
            padding: 8rem 0 0 0;
        }

        .hero-header h1 {
            position: relative;
            top: -25px
        }

        button ,.btn{
            text-transform: capitalize !important;
            letter-spacing: auto !important ; 
            font-weight: 400;
        }

        div.content div {
            margin-top: 10px;
        }

        div.content div p {
            margin-top: 10px;
        }

        div.content ul {
            margin-top: 10px;
        }

        div.content ul li {
            margin-top: 5px;
        }

        @media (max-width: 1024px) {
            .hero-header {
                padding: 4rem 0 0 0;
            }
        }

        @media (max-width: 480px) {
            .hero-header h1 {
                top: -33px;
                font-size: 22px;
            }
        }
    </style>
</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="51">
    <div class="p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="position-relative p-0" id="home">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="/" class="navbar-brand p-0">
                    <!-- <h1 class="m-0">FitApp</h1> -->
                    <img src="{{ asset('landing/img/logo.png') }}" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        
                    </div>
                    <a href="{{ route('admin.login') }}"
                        class="btn btn-primary-gradient rounded-pill py-2 px-4 ms-3 d-none d-lg-block">
                        Login</a>
                </div>
            </nav>

            <div class="hero-header">
                <div class="col-sm-12 d-flex text-center justify-content-center align-items-center">
                    <h1 class="text-white">Privacy And Policy</h1>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        
        <div class="container content mt-5">
            <div class="row">
                <div class="col-sm-12">
                    <h6 class="font-weight-normal">Last updated: 2024-06-06</h6>
                    <h4>Who we are</h4>
                    <ul class="list-unstyled">
                        <li>
                            Business Joy, your trusted CRM software solution. We are committed to protecting your
                            privacy and ensuring the security of your personal data. This Privacy Policy outlines how we
                            collect, use, disclose, and safeguard your information when you use our software and
                            services.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Information We Collect</h4>
                    <h4>Personal Information</h4>
                    <p>
                        We may collect personal information that can identify you, such as your name, email address,
                        phone number, and company details when you:
                    </p>
                    <ul>
                        <li>
                            Register for an account
                        </li>
                        <li>
                            Use our services
                        </li>
                        <li>
                            Contact our customer support
                        </li>
                    </ul>

                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Usage Data</h4>
                    <p>
                        We may collect information that your browser sends whenever you visit our service or when you
                        access the service by or through a mobile device. This data may include:
                    </p>
                    <ul>
                        <li>
                            Your computer's Internet Protocol (IP) address
                        </li>
                        <li>
                            Browser type and version
                        </li>
                        <li>
                            Pages of our service that you visit
                        </li>
                        <li>
                            Time and date of your visit
                        </li>
                        <li>
                            Time spent on those pages
                        </li>
                        <li>
                            Unique device identifiers
                        </li>
                        <li>
                            Other diagnostic data
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>How We Use Your Information</h4>
                    <p>
                        We use the collected data for various purposes:
                    </p>
                    <ul>
                        <li>
                            To provide and maintain our service
                        </li>
                        <li>
                            To notify you about changes to our service
                        </li>
                        <li>
                            To allow you to participate in interactive features of our service
                        </li>
                        <li>
                            To provide customer support
                        </li>
                        <li>
                            To gather analysis or valuable information so that we can improve our service
                        </li>
                        <li>
                            To monitor the usage of our service
                        </li>
                        <li>
                            To detect, prevent and address technical issues
                        </li>
                        <li>
                            To fulfill any other purpose for which you provide it
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Sharing Your Information </h4>
                    <p>
                        We do not share, sell, rent, or trade your personal information with third parties other than as
                        disclosed within this Privacy Policy. We may share information with:
                    </p>
                    <ul>
                        <li>
                            Service providers who perform services on our behalf
                        </li>
                        <li>
                            Legal authorities if required by law
                        </li>
                        <li>
                            Business partners with whom we offer co-branded services or engage in joint marketing
                            activities
                        </li>
                        <li>
                            Other third parties with your consent
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Data Security</h4>
                    <ul>
                        <li>
                            We employ a variety of security measures to ensure the safety of your personal information.
                            We use encryption technology to protect sensitive data transmitted online. However, no
                            method of transmission over the Internet or method of electronic storage is 100% secure.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Your Data Protection Rights</h4>
                    <ul>
                        <li class="list-group">
                            Depending on your location, you may have the following rights:
                        </li>
                        <li>
                            <b>The Right to Access:</b> You can request a copy of your personal data that we hold.You
                            have the right to request copies of your personal data.

                        </li>
                        <li>
                            <b>The Right to Rectification:</b> You have the right to request that we correct any
                            information you believe is inaccurate or complete information you believe
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer Start -->
        <div class="container-fluid bg-primary text-light footer wow fadeIn" data-wow-delay="0.1s">
            <div class="container px-lg-5">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            &copy; <a class="border-bottom" href="https://www.businessjoy.in">Business Joy</a>, All
                            Right Reserved.

                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            {{-- Powered By <a class="border-bottom" href="https://www.oceanmnc.com" target="_blank">Ocean
                                MNC</a> --}}
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="footer-menu custom-display-flex">
                                <a href="/">Home</a>
                                <a href="{{route('faq')}}">FAQs</a>
                                <a href="{{ route('termsandconditions') }}">Terms & Conditions</a>
                                <a href="{{ route('privacypolicy') }}">Privacy Policy</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

    </div>

    <!-- JavaScript Libraries -->
    <script src="{{ asset('admin/js/jquery.min.js') }} "></script>
    <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landing/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('landing/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('landing/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('landing/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('landing/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('landing/js/main.js') }}"></script>
    
</body>

</html>
