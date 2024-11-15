<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Business Joy - Terms and Conditions</title>
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

        button,
        .btn {
            text-transform: capitalize !important;
            letter-spacing: auto !important;
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
                    <h1 class="text-white">Terms And Conditions</h1>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <div class="container content mt-5">
            <div class="row">
                <div class="col-sm-12">
                    <h6 class="font-weight-normal">Last updated: 2024-06-06</h6>
                    <h4>Introduction</h4>
                    <ul class="list-unstyled">
                        <li>
                            Welcome to <a href="/">Business Joy</a> . These Terms and Conditions ("Terms") govern
                            your access to and use of our CRM software and services, including all associated modules
                            such as invoice, lead, customer support, company, appointments, and notifications. By using
                            our services, you agree to comply with and be bound by these Terms.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Use of Our Services</h4>
                    <p>
                        Eligibility:
                    </p>
                    <ul>
                        <li>
                            You must be at least 18 years old to use our services. By using Business Joy, you represent
                            and warrant that you meet this requirement.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Account Registration</h4>
                    <ul>
                        <li>
                            To access certain features of our software, you must register for an account. You agree to
                            provide accurate, current, and complete information during the registration process and to
                            update such information to keep it accurate, current, and complete.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>User Responsibilities</h4>
                    <p>
                        As a user of Business Joy, you agree to:
                    </p>
                    <ul>
                        <li>
                            Use the software in compliance with all applicable laws and regulations.
                        </li>
                        <li>
                            Keep your login credentials secure and confidential.
                        </li>
                        <li>
                            Notify us immediately of any unauthorized use of your account.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Prohibited Activities</h4>
                    <p>You agree not to:</p>
                    <ul>
                        <li>
                            Use the software for any illegal or unauthorized purpose.
                        </li>
                        <li>
                            Interfere with or disrupt the integrity or performance of the software.
                        </li>
                        <li>
                            Attempt to gain unauthorized access to the software or its related systems or networks.
                        </li>
                        <li>
                            Use the software to transmit any malicious code or harmful data.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Fees and Payment</h4>
                    <p>Subscription Fees</p>
                    <ul>
                        <li>
                            Access to certain features of Business Joy may require payment of subscription fees. You
                            agree to pay all applicable fees in accordance with the terms set forth at the time of
                            subscription.
                        </li>
                    </ul>
                    <p>Billing</p>
                    <ul>
                        <li>
                            We will bill you for the subscription fees through the payment method you provide. You
                            authorize us to charge your payment method for the fees due.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Intellectual Property</h4>
                    <p>Ownership</p>
                    <ul>
                        <li>
                            Business Joy and its entire contents, features, and functionality, including but not limited
                            to all information, software, text, displays, images, and video, are owned by Business Joy,
                            its licensors, or other providers of such material and are protected by international
                            copyright, trademark, patent, trade secret, and other intellectual property or proprietary
                            rights laws.
                        </li>
                    </ul>
                    <p>License</p>
                    <ul>
                        <li>
                            Subject to your compliance with these Terms, we grant you a limited, non-exclusive,
                            non-transferable, non-sublicensable license to access and use the software for your internal
                            business purposes.
                        </li>
                    </ul>
                    <p>Termination</p>
                    <ul>
                        <li>
                            We may terminate or suspend your account and access to the software immediately, without
                            prior notice or liability, for any reason, including if you breach these Terms. Upon
                            termination, your right to use the software will cease immediately.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Limitation of Liability</h4>
                    <p>
                        In no event shall Business Joy, its directors, employees, partners, agents, suppliers, or
                        affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages,
                        including without limitation, loss of profits, data, use, goodwill, or other intangible losses,
                        resulting from:
                    </p>
                    <ul>
                        <li>
                            Your access to or use of or inability to access or use the software.

                        </li>
                        <li>
                            Any conduct or content of any third party on the software.
                        </li>
                        <li>
                            Any content obtained from the software.
                        </li>
                        <li>
                            Unauthorized access, use, or alteration of your transmissions or content.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Governing Law</h4>
                    <p>
                        These Terms shall be governed and construed in accordance with the laws of [Your Country/State],
                        without regard to its conflict of law provisions.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12">
                    <h4>Changes to Terms</h4>
                    <p>
                        We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a
                        revision is material, we will provide at least 30 days' notice before any new terms take effect.
                        What constitutes a material change will be determined at our sole discretion.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col sm-12" id="contactsection">
                    <h4>Contact Information</h4>
                    <p>
                        If you have any questions about these Terms, please contact us at:
                    </p>
                    <ul>
                        <li>
                            <span>
                                <b>Email </b>:
                                <a href="mailto:support@businessjoy.in">support@businessjoy.in</a>
                            </span>
                        </li>
                        {{-- <li>
                            <span>
                                <b>Phone </b>:
                                <a href="tel:+917948558535">079 48558535</a>
                            </span>
                        </li>  --}}
                    </ul>
                    <p>
                        By using <a href="/"> Business Joy</a>, you agree to these Terms and Conditions. If you do
                        not agree to these terms, please do not use our software.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Start -->
        <div class="p-0 text-light footer wow fadeIn" data-wow-delay="0.1s">
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
                                <a href="{{ route('faq') }}">FAQs</a>
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
