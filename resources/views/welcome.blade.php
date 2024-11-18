<!DOCTYPE html>
<html lang="en">

<head>

    {{-- meta updated on 6-6-2024 --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Joy: Integrated CRM & ERP System </title>
    <meta name="title"
        content="Business Joy - No-Code CRM & ERP | Custom Software Builder for Scalable Business Solutions">
    <meta name="description"
        content="Build custom CRM and ERP solutions with Business Joy’s no-code app builder. Subscribe per module, create custom workflows, and scale your no-code app with ease. Perfect for 2024 business needs.">
    <meta name="keywords"
        content="CRM software, customer relationship management, invoicing, lead management, customer support, Business Joy,Enterprise Resource Planning Software, ERP Software">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.businessjoy.in">
    <meta property="og:title" content="Business Joy CRM - Optimize Your Customer Relationship Management">
    <meta property="og:description"
        content="Streamline your customer relationship management with advanced modules for invoicing, lead management, customer support, and more.">
    <meta name="author" content="Business Joy Team">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">


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

    <!-- Template Stylesheet -->
    <link href="{{ asset('landing/css/style.css') }}" rel="stylesheet">
    <!-- Responsive Stylesheet -->
    <link href="{{ asset('landing/css/responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <style>
        button,
        .btn {
            text-transform: initial !important;
            letter-spacing: auto !important;
            font-weight: 300;
            font-family: Heebo, sans-serif !important;
        }

        .card-title>button,
        .card-title>button:active {
            display: block;
            padding: 15px;
            color: #555;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            word-spacing: 3px;
            text-decoration: none;
        }

        .card-header button::before {
            font-family: 'Font Awesome 5 Free';
            content: "\f0aa";
            position: absolute;
            /* content: '\25B2'; */
            /* float: right; */
            transition: all 0.5s;
            right: 30px;
            font-size: 22px;
        }

        .card-header.active button::before {
            font-family: 'Font Awesome 5 Free';
            content: "\f0ab";
            font-size: 22px;
            /* Unicode for down arrow */
            float: right;
            transition: all 0.5s;
        }

        .grecaptcha-badge {
            visibility: hidden !important;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="51">
    <div class="bg-white p-0">
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
                <a href="" class="navbar-brand p-0">
                    <!-- <h1 class="m-0">FitApp</h1> -->
                    <img src="{{ asset('landing/img/logo.png') }}" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="#home" id="hometab" class="nav-item nav-link active">Home</a>
                        <a href="#customsoftwaebuilder" class="nav-item nav-link">Custom Software Builder</a>
                        <a href="#solution" class="nav-item nav-link">Solution</a>
                        <a href="#modules" class="nav-item nav-link">Modules</a>
                        <!-- <a href="#pricing" class="nav-item nav-link">Pricing</a>
                        <a href="#review" class="nav-item nav-link">Review</a> -->
                        {{-- <a href="#joinus" class="nav-item nav-link">Join Us</a> --}}
                        <a href="#contact" class="nav-item nav-link">Contact</a>
                    </div>
                    <a class="btn btn-primary-gradient d-lg-block ms-0 mx-2 rounded-pill text-white"
                        data-bs-toggle="modal" data-bs-target="#modal">Get a Free Quote</a>
                    <a href="{{ route('admin.login') }}" id="navbarLoginBtn"
                        class="btn btn-primary-gradient d-lg-block m-sm-0 ms-3 px-4 px-lg-4 px-sm-3 py-2 py-md-1 py-sm-1 rounded-pill">
                        Login</a>
                </div>
            </nav>

            <div class="hero-header">
                <div class="container px-lg-5">
                    <div class="row">
                        <div class="col-lg-7 text-center text-lg-start">
                            <h1 class="text-white mb-4 animated slideInDown">Your All-in-One No-Code App Builder for
                                Custom CRM & ERP Solutions</h1>
                            <p class="text-white pb-3 animated slideInDown">Create powerful business applications
                                without coding. Customize workflows, manage processes, and scale with ease using
                                Business Joy’s flexible modules.
                            </p>
                            <a href="#contact"
                                class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill me-3 animated slideInLeft">
                                Contact Us
                            </a>
                            <!-- <a href="#contact"
                                class="btn btn-secondary-gradient py-sm-3 px-4 px-sm-5 rounded-pill animated slideInRight">Contact
                                Us</a> -->
                        </div>
                        <div class="col-lg-5 m-0 d-flex justify-content-center justify-content-lg-end wow fadeInUp"
                            data-wow-delay="0.3s">
                            {{-- <div class=""> --}}
                            <img class="img-fluid" id="img1" src="{{ asset('landing/img/11.png') }}"
                                alt="Business Joy No-Code App Builder">
                            {{-- </div> --}}
                            {{-- <div class="owl-carousel screenshot-carousel">
                                <img class="img-fluid" src="{{asset('landing/img/screenshot-1.png')}}" alt="">
                                <img class="img-fluid" src=" {{asset('landing/img/screenshot-2.png')}}" alt="">
                                <img class="img-fluid" src="{{asset('landing/img/screenshot-3.png')}}" alt="">
                                <img class="img-fluid" src="{{asset('landing/img/screenshot-4.png')}}" alt="">
                                <img class="img-fluid" src="{{asset('landing/img/screenshot-5.png')}}" alt="">
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->


        <!-- About Start -->
        <div class="py-5" id="customsoftwaebuilder">
            <div class="container py-5 px-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="text-primary-gradient fw-medium">Your business problem</h5>
                        <h1 class="mb-4">Customizable Software Builder</h1>
                        <p class="mb-4">
                            Unlock the full potential of your business with Business Joy – the all-in-one, fully
                            <strong>customizable software builder </strong> designed to adapt to your <strong>unique
                                business needs.</strong> With
                            comprehensive CRM and ERP capabilities, Business Joy is the perfect solution for businesses
                            looking to streamline operations, boost productivity, and scale seamlessly. Whether you're
                            managing sales, inventory, or customer support, Business Joy molds to your workflows for
                            maximum efficiency.
                        </p>
                        <div class="row g-4 mb-4">
                            <div class="col-sm-6 wow fadeIn" data-wow-delay="0.5s">
                                <div class="display-flex">
                                    <i class="fa fa-cogs fa-2x text-primary-gradient flex-shrink-0 mt-1"></i>
                                    <div class="ms-3">
                                        <h2 class="mb-0" data-toggle="counter-up">1482</h2>
                                        <p class="text-primary-gradient mb-0">Active Install</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 wow fadeIn" data-wow-delay="0.7s">
                                <div class="display-flex">
                                    <i class="fa fa-comments fa-2x text-secondary-gradient flex-shrink-0 mt-1"></i>
                                    <div class="ms-3">
                                        <h2 class="mb-0" data-toggle="counter-up">827</h2>
                                        <p class="text-secondary-gradient mb-0">Clients Reviews</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <a href="#contact"
                            class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill mt-3">Yes, I want to
                            remove my business blockages!</a> --}}
                    </div>
                    <div class="col-lg-5 display-flex">
                        <img class="img-fluid wow fadeInUp float-end img-2" data-wow-delay="0.5s"
                            src="{{ asset('landing/img/2.png') }}" alt="Custom Workflow Creator">
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->





        <!-- Screenshot Start -->
        <div class="py-5" id="solution">
            <div class="container py-5 px-lg-5">
                <div class="row  align-items-center">
                    <div class="col-lg-4  mock-p-0 ps-0 d-flex justify-content-center justify-content-lg-end wow fadeInUp"
                        data-wow-delay="0.3s">
                        <div class="">
                            <img class="img-fluid img-3" src="{{ asset('landing/img/3.png') }}"
                                alt="Scalable CRM Solution">

                        </div>
                    </div>
                    <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="text-primary-gradient fw-medium">Solution</h5>
                        <h1 class="mb-4">How Business Joy Stands Out Among ERP & CRM Solutions
                        </h1>
                        <p class="mb-4"><i class="fa fa-check text-primary-gradient me-3"></i>Business Joy is here
                            to disrupt the market of CRM/ERP solutions by offering unmatched customization and
                            flexibility. Unlike solutions other software builders, Business Joy is designed to mold
                            precisely to your business’s needs, eliminating the limitations of one-size-fits-all
                            solutions.</p>
                        <p class="mb-4"><i class="fa fa-check text-primary-gradient me-3"></i>
                            With Business Joy, you gain not just CRM but also integrated ERP modules to manage
                            everything from lead generation to accounting—all within one flexible platform.
                        </p>
                        {{-- <p><i class="fa fa-check text-primary-gradient me-3"></i>Absolutely Safe And Secure</p>
                        <p><i class="fa fa-check text-primary-gradient me-3"></i>Genuine Standards</p>
                        <p class="mb-4"><i class="fa fa-check text-primary-gradient me-3"></i>Engaging and Clear UI
                            (user Interface) and UX (user experience)</p> --}}
                        {{-- <a href="#contact"
                            class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill mt-3">Smooth my business
                            process</a> --}}
                    </div>
                </div>
            </div>
        </div>
        <!-- Screenshot End -->

        <!-- Features Start -->
        <div class="py-5" id="modules">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Business Joy Features</h5>
                    <h1 class="mb-5">Feature-Rich Modules</h1>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-file-invoice text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Invoice Module
                            </h5>
                            <p class="m-0">Simplify billing, track payments, and manage financial transactions
                                effortlessly. Our invoice module is designed to handle everything from single invoices
                                to complex billing cycles.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-user-alt text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Lead Module</h5>
                            <p class="m-0">Capture, track, and nurture leads in one place. Business Joy’s lead
                                management module ensures that no potential client slips through the cracks.</p>
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-headphones text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Customer Support Module
                            </h5>
                            <p class="m-0">Provide exceptional support with built-in ticketing, response tracking,
                                and customer history. Ensure satisfaction and build lasting relationships.</p>
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-calculator text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Account Module</h5>
                            <p class="m-0">Monitor cash flow, manage expenses, and gain full financial transparency.
                                Business Joy’s accounting module keeps your financials accurate and accessible.
                            <p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-box-open text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Inventory Module</h5>
                            <p class="m-0">Optimize stock levels, track products, and reduce overhead. With
                                real-time insights into your inventory, you can keep operations running smoothly.</p>
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-clock text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Reminder Module
                            </h5>
                            <p class="m-0">Never miss a follow-up or deadline. Set reminders, automate
                                notifications, and ensure that every task stays on track.</p>
                            <br>
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-chart-bar text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Report Module
                            </h5>
                            <p class="m-0">Generate insights across departments with customizable reports. Make
                                data-driven decisions with ease, from sales trends to customer feedback.
                            </p>
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-file-signature text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Blog Module
                            </h5>
                            <p class="m-0">Engage customers and improve SEO with a built-in blog platform. Share
                                news, updates, and valuable content to keep your audience engaged.</p>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Features End -->
        <!-- Process Start -->
        <div class="py-5">
            <div class="container py-5 px-lg-5">
                <div class="text-center pb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">No-Code CRM/ERP Solution</h5>
                    <h1 class="mb-5">Why Choose Business Joy?</h1>
                    <p>Summarize Business Joy's unique benefits, emphasizing that it’s an affordable, scalable, no-code
                        app solution that allows for quick customization and is designed for both small businesses and
                        larger enterprises. Emphasize that Business Joy is a modern solution that fits well with 2024
                        trends in no-code app development.</p>
                </div>
                <div class="row gy-5 gx-4 justify-content-center">
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-edit fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Affordable modular design</h5>
                            <p class="mb-0">Start with essential modules and add as needed.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-mobile-alt fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Easy-to-use no-code platform </h5>
                            <p class="mb-0">Custom software without programming.
                            </p>
                            <br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-plug fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Scalable no-code app solution </h5>
                            <p class="mb-0">Adapt as your business grows and needs change.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Process Start -->


        <!-- Download Start -->
        <div class="py-5" id="joinus">
            <div class="container py-5 px-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-6 ps-0  mock-p-0">
                        <img class="img-fluid wow fadeInUp img-4" data-wow-delay="0.1s"
                            src="{{ asset('landing/img/4.png') }}">
                    </div>
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                        <h5 class="text-primary-gradient fw-medium">Ultimate</h5>
                        <h1 class="mb-4">Designed for Businesses of All Sizes</h1>
                        <p class="mb-4">Whether you're a small business looking to get organized or an enterprise
                            needing powerful, scalable software, Business Joy is built for you. From e-commerce and
                            retail to service-based businesses, Business Joy adapts seamlessly, making it ideal for any
                            industry.
                        </p>
                        <div class="col-lg-10 col-xl-8 text-start">
                            <div class="wow fadeIn" data-wow-delay="0.5s">
                                <div class="display-flex">
                                    <a href="#contact" class="d-flex bg-primary-gradient rounded py-3 px-4">
                                        <i class="fa fa-link fa-3x text-white flex-shrink-0"></i>
                                        <div class="ms-3">
                                            <p class="text-white mb-0">Yes!</p>
                                            <h5 class="text-white mb-0">I am strongly interested</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <!-- <div class="col-sm-6 wow fadeIn" data-wow-delay="0.7s">
                                <a href="#contact" class="d-flex bg-secondary-gradient rounded py-3 px-4">
                                    <i class="fab fa-android fa-3x text-white flex-shrink-0"></i>
                                    <div class="ms-3">
                                        <p class="text-white mb-0">Available On</p>
                                        <h5 class="text-white mb-0">Play Store</h5>
                                    </div>
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Download End -->


        <!-- Pricing Start -->
        <!-- <div class="py-5" id="pricing">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Pricing Plan</h5>
                    <h1 class="mb-5">Choose Your Plan</h1>
                </div>
                <div class="tab-class text-center pricing wow fadeInUp" data-wow-delay="0.1s">
                    <ul
                        class="nav nav-pills d-inline-flex justify-content-center bg-primary-gradient rounded-pill mb-5">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="pill" href="#tab-1">Monthly</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" href="#tab-2">Yearly</button>
                        </li>
                    </ul>
                    <div class="tab-content text-start">
                        <div id="tab-1" class="tab-pane fade show p-0 active">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="bg-light rounded">
                                        <div class="border-bottom p-4 mb-4">
                                            <h4 class="text-primary-gradient mb-1">Starter Plan</h4>
                                            <span>Powerful & Awesome Features</span>
                                        </div>
                                        <div class="p-4 pt-0">
                                            <h1 class="mb-3">
                                                <small class="align-top"
                                                    style="font-size: 22px; line-height: 45px;">$</small>14.99<small
                                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">/
                                                    Month</small>
                                            </h1>
                                            <div class="d-flex justify-content-between mb-3"><span>HTML5 & CSS3</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Bootstrap v5</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Responsive
                                                    Layout</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"><span>Cross-browser
                                                    Support</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <a href="" class="btn btn-primary-gradient rounded-pill py-2 px-4 mt-4">Get
                                                Started</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="bg-light rounded border">
                                        <div class="border-bottom p-4 mb-4">
                                            <h4 class="text-primary-gradient mb-1">Advance Plan</h4>
                                            <span>Powerful & Awesome Features</span>
                                        </div>
                                        <div class="p-4 pt-0">
                                            <h1 class="mb-3">
                                                <small class="align-top"
                                                    style="font-size: 22px; line-height: 45px;">$</small>24.99<small
                                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">/
                                                    Month</small>
                                            </h1>
                                            <div class="d-flex justify-content-between mb-3"><span>HTML5 & CSS3</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Bootstrap v5</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Responsive
                                                    Layout</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"><span>Cross-browser
                                                    Support</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <a href=""
                                                class="btn btn-secondary-gradient rounded-pill py-2 px-4 mt-4">Get
                                                Started</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="bg-light rounded">
                                        <div class="border-bottom p-4 mb-4">
                                            <h4 class="text-primary-gradient mb-1">Premium Plan</h4>
                                            <span>Powerful & Awesome Features</span>
                                        </div>
                                        <div class="p-4 pt-0">
                                            <h1 class="mb-3">
                                                <small class="align-top"
                                                    style="font-size: 22px; line-height: 45px;">$</small>34.99<small
                                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">/
                                                    Month</small>
                                            </h1>
                                            <div class="d-flex justify-content-between mb-3"><span>HTML5 & CSS3</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Bootstrap v5</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Responsive
                                                    Layout</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"><span>Cross-browser
                                                    Support</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <a href="" class="btn btn-primary-gradient rounded-pill py-2 px-4 mt-4">Get
                                                Started</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane fade p-0">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="bg-light rounded">
                                        <div class="border-bottom p-4 mb-4">
                                            <h4 class="text-primary-gradient mb-1">Starter Plan</h4>
                                            <span>Powerful & Awesome Features</span>
                                        </div>
                                        <div class="p-4 pt-0">
                                            <h1 class="mb-3">
                                                <small class="align-top"
                                                    style="font-size: 22px; line-height: 45px;">$</small>114.99<small
                                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">/
                                                    Yearly</small>
                                            </h1>
                                            <div class="d-flex justify-content-between mb-3"><span>HTML5 & CSS3</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Bootstrap v5</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Responsive
                                                    Layout</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"><span>Cross-browser
                                                    Support</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <a href="" class="btn btn-primary-gradient rounded-pill py-2 px-4 mt-4">Get
                                                Started</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="bg-light rounded border">
                                        <div class="border-bottom p-4 mb-4">
                                            <h4 class="text-primary-gradient mb-1">Advance Plan</h4>
                                            <span>Powerful & Awesome Features</span>
                                        </div>
                                        <div class="p-4 pt-0">
                                            <h1 class="mb-3">
                                                <small class="align-top"
                                                    style="font-size: 22px; line-height: 45px;">$</small>124.99<small
                                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">/
                                                    Yearly</small>
                                            </h1>
                                            <div class="d-flex justify-content-between mb-3"><span>HTML5 & CSS3</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Bootstrap v5</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Responsive
                                                    Layout</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"><span>Cross-browser
                                                    Support</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <a href="" class="btn btn-primary-gradient rounded-pill py-2 px-4 mt-4">Get
                                                Started</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="bg-light rounded">
                                        <div class="border-bottom p-4 mb-4">
                                            <h4 class="text-primary-gradient mb-1">Premium Plan</h4>
                                            <span>Powerful & Awesome Features</span>
                                        </div>
                                        <div class="p-4 pt-0">
                                            <h1 class="mb-3">
                                                <small class="align-top"
                                                    style="font-size: 22px; line-height: 45px;">$</small>134.99<small
                                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">/
                                                    Yearly</small>
                                            </h1>
                                            <div class="d-flex justify-content-between mb-3"><span>HTML5 & CSS3</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Bootstrap v5</span><i
                                                    class="fa fa-check text-primary-gradient pt-1"></i></div>
                                            <div class="d-flex justify-content-between mb-3"><span>Responsive
                                                    Layout</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"><span>Cross-browser
                                                    Support</span><i class="fa fa-check text-primary-gradient pt-1"></i>
                                            </div>
                                            <a href="" class="btn btn-primary-gradient rounded-pill py-2 px-4 mt-4">Get
                                                Started</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Pricing End -->


        <!-- Testimonial Start -->
        <!-- <div class="py-5" id="review">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Testimonial</h5>
                    <h1 class="mb-5">What Say Our Clients!</h1>
                </div>
                <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                    <div class="testimonial-item rounded p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img class="img-fluid bg-white rounded flex-shrink-0 p-1" src="img/testimonial-1.jpg"
                                style="width: 85px; height: 85px;">
                            <div class="ms-4">
                                <h5 class="mb-1">Client Name</h5>
                                <p class="mb-1">Profession</p>
                                <div>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                </div>
                            </div>
                        </div>
                        <p class="mb-0">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et
                            eos. Clita erat ipsum et lorem et sit.</p>
                    </div>
                    <div class="testimonial-item rounded p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img class="img-fluid bg-white rounded flex-shrink-0 p-1" src="img/testimonial-2.jpg"
                                style="width: 85px; height: 85px;">
                            <div class="ms-4">
                                <h5 class="mb-1">Client Name</h5>
                                <p class="mb-1">Profession</p>
                                <div>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                </div>
                            </div>
                        </div>
                        <p class="mb-0">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et
                            eos. Clita erat ipsum et lorem et sit.</p>
                    </div>
                    <div class="testimonial-item rounded p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img class="img-fluid bg-white rounded flex-shrink-0 p-1" src="img/testimonial-3.jpg"
                                style="width: 85px; height: 85px;">
                            <div class="ms-4">
                                <h5 class="mb-1">Client Name</h5>
                                <p class="mb-1">Profession</p>
                                <div>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                </div>
                            </div>
                        </div>
                        <p class="mb-0">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et
                            eos. Clita erat ipsum et lorem et sit.</p>
                    </div>
                    <div class="testimonial-item rounded p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img class="img-fluid bg-white rounded flex-shrink-0 p-1" src="img/testimonial-4.jpg"
                                style="width: 85px; height: 85px;">
                            <div class="ms-4">
                                <h5 class="mb-1">Client Name</h5>
                                <p class="mb-1">Profession</p>
                                <div>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                    <small class="fa fa-star text-warning"></small>
                                </div>
                            </div>
                        </div>
                        <p class="mb-0">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit diam amet diam et
                            eos. Clita erat ipsum et lorem et sit.</p>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Testimonial End -->


        <!-- Contact Start -->
        <div class="py-5" id="contact">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Get In Touch!</h5>
                    {{-- <h1 class="mb-5">Get In Touch!</h1> --}}
                    <h1 class="mb-3">Ready to Build Your No-Code CRM & ERP Solution?
                    </h1>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="wow fadeInUp" data-wow-delay="0.3s">
                            <p class="text-center">Join hundreds of businesses creating custom workflows, managing
                                operations, and scaling effortlessly with Business Joy. Try it free and discover how
                                easy it is to build a no-code CRM and ERP that’s uniquely yours.</p>
                            <form action="{{ route('admin.new') }}" class="bj-landing-forms" method="Post">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" maxlength="30" id="name"
                                                name="name" placeholder="Your Name" required>
                                            <label for="name">Your Name*</label>
                                            <span class="error-msg" id="error-name" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Your Email" maxlength="40" required>
                                            <label for="email">Your Email*</label>
                                            <span class="error-msg" id="error-email" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                maxlength="12" id="mobile_number" name="mobile_number"
                                                placeholder="Mobile number" required>
                                            <label for="mobile_number">Mobile Number*</label>
                                            <span class="error-msg" id="error-mobile_number"
                                                style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="subject" name="subject"
                                                placeholder="Subject" maxlength="25">
                                            <label for="subject">Subject</label>
                                            <span class="error-msg" id="error-subject" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" placeholder="Leave a message here" id="message" name="message"
                                                style="height: 150px" maxlength="1000"></textarea>
                                            <label for="message">Message*</label>
                                            <span class="error-msg" id="error-message" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button id="contactSubmitBtn"
                                            class="btn btn-primary-gradient rounded-pill fs-5 py-3 px-5"
                                            type="submit">Schedule a Free Demo</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->


        {{-- faq start --}}
        <div class="py-5" id="contact">
            <div class="container py-5 px-lg-5">
                <div class="row mt-0">
                    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="text-primary-gradient fw-medium">FAQ</h5>
                        {{-- <h1 class="mb-5">Get In Touch!</h1> --}}
                        <h1 class="mb-3">Frequently Asked Questions
                        </h1>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="accordion" id="accordionExample">
                        <div class="card mb-1">
                            <div class="card-header active" id="headingOne">
                                <h2 class="mb-0 card-title">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        1. Is Business Joy customizable?
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseOne" class="collapse card-collapse show" aria-labelledby="headingOne"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    Absolutely! Every module can be tailored to meet your business needs.
                                </div>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-header" id="headingTwo">
                                <h2 class="mb-0 card-title">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button"
                                        data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        2. What support do you offer?
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseTwo" class="card-collapse collapse" aria-labelledby="headingTwo"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    Our dedicated team is here to assist you with implementation, training, and ongoing
                                    support to ensure success at every stage.
                                </div>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-header" id="headingThree">
                                <h2 class="mb-0 card-title">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button"
                                        data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        3. Is businessjoy suitable for any size of business?
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseThree" class="collapse card-collapse" aria-labelledby="headingThree"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    Yes! Business Joy is designed to be cost-effective and
                                    adaptable, making it a perfect fit for small to large-sized businesses.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center w-100">
                    <a href="{{ route('faq') }}"
                        class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill mt-3">More FAQ</a>
                </div>
            </div>
        </div>
        {{-- faq end --}}


        <!-- Footer Start -->
        <div class="container-fluid bg-primary text-light footer wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5 px-lg-5">
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <!-- <h4 class="text-white mb-4">Popular Link</h4>
                        <a class="btn btn-link" href="">About Us</a>
                        <a class="btn btn-link" href="">Contact Us</a>
                        <a class="btn btn-link" href="">Privacy Policy</a>
                        <a class="btn btn-link" href="">Terms & Condition</a>
                        <a class="btn btn-link" href="">Career</a> -->
                        <img class="w-100" src="{{ asset('landing/img/businessjoyfooterlogo.png') }}"
                            alt="businessjoylogo">
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Contact</h4>
                        {{-- <p><i class="fa fa-map-marker-alt me-3"></i>India</p>
                        <p><a href="tel:+917948558535" class="text-white"><i
                                    class="fa fa-phone-alt me-3"></i>+917948558535 </a></p> --}}
                        <p><a href="mailto:inquiry@businessjoy.in" class="text-white"><i
                                    class="fa fa-envelope me-3"></i>inquiry@businessjoy.in</a></p>
                        <p><a href="mailto:support@businessjoy.in" class="text-white"><i
                                    class="fa fa-envelope me-3"></i>support@businessjoy.in</a></p>
                        {{-- <div class="d-flex pt-2">
                            <!-- <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a> -->
                            <a class="btn btn-outline-light btn-social" target="_blank"
                                href="https://www.facebook.com/oceanmnc7/"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" target="_blank"
                                href="https://www.instagram.com/oceanmnc/?hl=en"><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-outline-light btn-social" target="_blank"
                                href="https://in.linkedin.com/company/ocean-mnc"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div> --}}
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Quick Link</h4>
                        <a class="btn btn-link" href="#contact">Contact Us</a>
                        <a class="btn btn-link" href="{{ route('privacypolicy') }}">Privacy Policy</a>
                        <a class="btn btn-link" href="{{ route('termsandconditions') }}">Terms & Condition</a>
                        <a class="btn btn-link" href="{{ route('faq') }}">FAQ</a>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Newsletter</h4>
                        <p>Raise your inbox with exclusive insights and product updates—subscribe now for a front-row
                            seat to innovation</p>
                        <div class="position-relative w-100 mt-3">
                            <form action="{{ route('admin.new') }}" class="bj-landing-forms" method="post">
                                @csrf
                                <input type="hidden" name="subscribe" value="yes">
                                <input class="form-control border-0 rounded-pill w-100 ps-4 pe-5" type="email"
                                    placeholder="Your Email" name="email" style="height: 48px;">
                                <span class="error-msg" id="error-email" style="color: red"></span>
                                <button type="submit"
                                    class="btn shadow-none position-absolute top-0 end-0 mt-1 me-2"><i
                                        class="fa fa-paper-plane text-primary-gradient fs-4"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
                                <a href="#home">Home</a>
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


        <!-- Back to Top -->
        <a href="#home" class="btn btn-lg btn-lg-square back-to-top pt-2"><i
                class="bi bi-arrow-up text-white"></i></a>
        {{-- <a id="whatsapp-button" href="https://wa.me/+917600596975?text=I%20am%20Interested%20in%20BusinessJoy"
            target="_blank">
            <i class="bi bi-whatsapp fa-2x"></i>
        </a> --}}
    </div>

    @include('modal')

    <!-- JavaScript Libraries -->
    <script src="{{ asset('admin/js/jquery.min.js') }} "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('landing/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('landing/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('landing/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('landing/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('landing/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('landing/js/main.js') }}"></script>

    <script>
        $('.card-collapse').on('show.bs.collapse', function() {
            $(this).siblings('.card-header').addClass('active');
        });

        $('.card-collapse').on('hide.bs.collapse', function() {
            $(this).siblings('.card-header').removeClass('active');
        });
    </script>

    @if (Session::has('success'))
        <script>
            var msg = "{{ Session::get('success') }}";
            toastr.success(msg);
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            var msg = "{{ Session::get('error') }}";
            toastr.error(msg);
        </script>
    @endif
    <script>
        $("document").ready(function() {
            $('#contact_no').on('input', function() {
                var inputValue = $(this).val();
                var digitOnlyRegex = /^\d*$/; // Regular expression to allow only digits

                if (!digitOnlyRegex.test(inputValue)) {
                    // Remove non-digit characters from the input
                    $(this).val(inputValue.replace(/\D/g, ''));
                }
            });


            $('form.bj-landing-forms').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                var this_form = $(this); // Capture form context
                var formData = new FormData(this); // Create FormData from the form
                var action = $(this).attr('action'); // Get the form action URL

                var submitBtn = this_form.find("input[type='submit'], button[type='submit']")
                // disable submit buttons during AJAX request
                submitBtn.prop('disabled', true); // disable submit btn

                grecaptcha.ready(function() {
                    var recaptchaSiteKey = "{{ env('RECAPTCHA_SITE_KEY') }}";
                    grecaptcha.execute(recaptchaSiteKey, {
                            action: 'submit'
                        })
                        .then(function(token) {
                            // Append reCAPTCHA response token to formData
                            formData.append('g-recaptcha-response', token);

                            // Now that the token is appended, send the AJAX request
                            $.ajax({
                                type: 'POST',
                                url: action,
                                data: formData,
                                processData: false, // Don't process the data (important for FormData)
                                contentType: false, // Don't set contentType (important for FormData)
                                success: function(response) {
                                    // Handle the server response
                                    if (response.status == 200) {
                                        toastr.success(response.message);
                                        this_form[0].reset();
                                    } else if (response.status == 500) {
                                        toastr.error(response.message);
                                    } else {
                                        toastr.error('Something went wrong!');
                                    }
                                    submitBtn.prop('disabled', false);
                                },
                                error: function(xhr, status, error) {
                                    console.log(xhr.responseText);
                                    if (xhr.status === 422) {
                                        var errors = xhr.responseJSON.errors;
                                        $.each(errors, function(key, value) {
                                            $(this_form).find('#error-' +
                                                key).text(value[
                                                0]);
                                        });
                                    } else {
                                        var errorMessage = "";
                                        try {
                                            var responseJSON = JSON.parse(xhr
                                                .responseText);
                                            errorMessage = responseJSON.message ||
                                                "An error occurred";
                                        } catch (e) {
                                            errorMessage = "An error occurred";
                                        }
                                        toastr.error(errorMessage);
                                    }
                                    submitBtn.prop('disabled', false);
                                }
                            });
                        })
                        .catch(function(error) {
                            console.error('reCAPTCHA execution error:', error);
                            toastr.error("Failed to verify reCAPTCHA, please try again.");
                        });
                });
            });


        });
    </script>
    <script>
        function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(name) === 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function checkCookie() {
            let popup = getCookie("Popup");
            if (popup === "") {
                setTimeout(function() {
                    $('#modal').modal('show');
                }, 10000); // 10 seconds
                setCookie("Popup", 'yes', 1);
            }
        }

        document.addEventListener("DOMContentLoaded", checkCookie); // Ensure the checkCookie runs after DOM is loaded
    </script>
</body>

</html>
