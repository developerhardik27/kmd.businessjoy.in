<!DOCTYPE html>
<html lang="en">

<head>

    {{-- meta updated on 6-6-2024 --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Joy: Integrated CRM & ERP System </title>
    <meta name="description"
        content="Discover how Business Joy CRM can streamline your customer relationship management with advanced modules for invoicing, lead management, customer support, and more.">
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
</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="51">
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0" id="home">
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
                        <a href="#about" class="nav-item nav-link">Business Problem</a>
                        <a href="#solution" class="nav-item nav-link">Solution</a>
                        <a href="#feature" class="nav-item nav-link">Feature</a>
                        <!-- <a href="#pricing" class="nav-item nav-link">Pricing</a>
                        <a href="#review" class="nav-item nav-link">Review</a> -->
                        <a href="#joinus" class="nav-item nav-link">Join Us</a>
                        <a href="#contact" class="nav-item nav-link">Contact</a>
                    </div>
                    <a href="{{ route('admin.login') }}"
                        class="btn btn-primary-gradient rounded-pill py-2 px-4 ms-3 d-none d-lg-block">
                        Login</a>
                </div>
            </nav>

            <div class="container-xxl bg-primary hero-header">
                <div class="container px-lg-5">
                    <div class="row">
                        <div class="col-lg-7 text-center text-lg-start">
                            <h1 class="text-white mb-4 animated slideInDown">Boost Your Account Process With Business
                                Joy</h1>
                            <p class="text-white pb-3 animated slideInDown">Make your business process Easy,Effective
                                and Engaging</p>
                            <a href="#contact"
                                class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill me-3 animated slideInLeft">Free
                                Demo</a>
                            <!-- <a href="#contact"
                                class="btn btn-secondary-gradient py-sm-3 px-4 px-sm-5 rounded-pill animated slideInRight">Contact
                                Us</a> -->
                        </div>
                        <div class="col-lg-5 m-0 d-flex justify-content-center justify-content-lg-end wow fadeInUp"
                            data-wow-delay="0.3s">
                            {{-- <div class=""> --}}
                            <img class="img-fluid" id="img1" src="{{ asset('landing/img/11.png') }}"
                                alt="">
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
        <div class="container-xxl py-5" id="about">
            <div class="container py-5 px-lg-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="text-primary-gradient fw-medium">Your business problem</h5>
                        <h1 class="mb-4">Unbelivable Fact</h1>
                        <p class="mb-4">
                            According to survery, 80% business get loss due not to Technology adoption every year. They
                            work with slow process with paperwork, Invest more manpower, still they lack of work
                            accuracy, employees get burned out with workload,some time lack of team member company has
                            to oursource their tasks to outside the office which can be of higher cost, time, less
                            sefety and security. At the end of the year they still get business loss.
                        </p>
                        <div class="row g-4 mb-4">
                            <div class="col-sm-6 wow fadeIn" data-wow-delay="0.5s">
                                <div class="d-flex">
                                    <i class="fa fa-cogs fa-2x text-primary-gradient flex-shrink-0 mt-1"></i>
                                    <div class="ms-3">
                                        <h2 class="mb-0" data-toggle="counter-up">1482</h2>
                                        <p class="text-primary-gradient mb-0">Active Install</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 wow fadeIn" data-wow-delay="0.7s">
                                <div class="d-flex">
                                    <i class="fa fa-comments fa-2x text-secondary-gradient flex-shrink-0 mt-1"></i>
                                    <div class="ms-3">
                                        <h2 class="mb-0" data-toggle="counter-up">827</h2>
                                        <p class="text-secondary-gradient mb-0">Clients Reviews</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#contact"
                            class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill mt-3">Yes, I want to
                            remove my business blockages!</a>
                    </div>
                    <div class="col-lg-6">
                        <img class="img-fluid wow fadeInUp float-end img-2" data-wow-delay="0.5s"
                            src="{{ asset('landing/img/2.png') }}">
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->





        <!-- Screenshot Start -->
        <div class="container-xxl py-5" id="solution">
            <div class="container py-5 px-lg-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-4  mock-p-0 ps-0 d-flex justify-content-center justify-content-lg-end wow fadeInUp"
                        data-wow-delay="0.3s">
                        <div class="">
                            <img class="img-fluid img-3" src="{{ asset('landing/img/3.png') }}" alt="">

                        </div>
                    </div>
                    <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                        <h5 class="text-primary-gradient fw-medium">Solution</h5>
                        <h1 class="mb-4">Here is the solution of your daily business challenges</h1>
                        <p class="mb-4">Don't worry! business joy is here to reduce your headache and paperwork. It
                            is
                            fully sysmetic, automatic which will make your business in flow , smoothy, speedy, easy
                            intergrate and engage with others. Most importantantly to stand with your competitors in the
                            market.</p>
                        <p><i class="fa fa-check text-primary-gradient me-3"></i>Absolutely Safe And Secure</p>
                        <p><i class="fa fa-check text-primary-gradient me-3"></i>Genuine Standards</p>
                        <p class="mb-4"><i class="fa fa-check text-primary-gradient me-3"></i>Engaging and Clear UI
                            (user Interface) and UX (user experience)</p>
                        <a href="#contact"
                            class="btn btn-primary-gradient py-sm-3 px-4 px-sm-5 rounded-pill mt-3">Smooth my business
                            process</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Screenshot End -->

        <!-- Features Start -->
        <div class="container-xxl py-5" id="feature">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Business Joy Features</h5>
                    <h1 class="mb-5">Mind Blowing Features</h1>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-eye text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Account</h5>
                            <p class="m-0">Experience unparalleled control and insight into customer relationships
                                with our CRM's dynamic Account module, centralizing key data to drive precision &
                                efficiency in your operations such as invoice, quotation, order etc.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-layer-group text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Lead</h5>
                            <p class="m-0">Empower your sales journey with Business Joy's Lead module, a
                                game-changing tool that seamlessly captures, nurtures, and converts leads, ensuring
                                every opportunity is maximized for business growth.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-edit text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Sales</h5>
                            <p class="m-0">Revolutionize your sales strategy with Sales module, delivering a
                                comprehensive and intuitive platform to streamline pipelines, boost team collaboration,
                                and drive unparalleled success in every deal</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-shield-alt text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Task Management</h5>
                            <p class="m-0">Boost your productivity and stay organized with our software's Task
                                Management module, a powerful tool designed to efficiently plan, execute, and track
                                tasks, ensuring seamless workflow and heightened success for your team
                            <p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-cloud text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Human Resource Management</h5>
                            <p class="m-0">Enhance your workforce management with our CRM's Human Resource module,
                                providing a comprehensive solution to streamline HR processes (Leave Management,
                                Payroll, Announcements, Employee Documents), foster employee engagement, and productive
                                workplace.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle mb-4"
                                style="width: 60px; height: 60px;">
                                <i class="fa fa-mobile-alt text-white fs-4"></i>
                            </div>
                            <h5 class="mb-3">Customer Support System</h5>
                            <p class="m-0">Transform customer interactions into exceptional experiences with our
                                CRM and ERP integrated system's Customer Support System module, designed to streamline
                                query resolution, enhance
                                communication, and ensure unparalleled satisfaction for lasting customer loyalty.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Features End -->
        <!-- Process Start -->
        <div class="container-xxl py-5">
            <div class="container py-5 px-lg-5">
                <div class="text-center pb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">How It Works</h5>
                    <h1 class="mb-5">3 Easy Steps</h1>
                </div>
                <div class="row gy-5 gx-4 justify-content-center">
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-cog fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Input</h5>
                            <p class="mb-0">Supercharge your experience by inputting crucial data into our
                                software—it's the key to unlocking unparalleled insights and maximizing your results.
                                Your success begins with the first click—start transforming your data into actionable
                                intelligence now</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-address-card fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Process</h5>
                            <p class="mb-0">Revolutionize your workflow as our user-friendly interface simplifies
                                data processing, slashing your time investment. Streamline tasks effortlessly,
                                empowering you to focus on what matters most while our software efficiently handles the
                                rest.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="position-relative bg-light rounded pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-gradient rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-check fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4 mb-3">Generate</h5>
                            <p class="mb-0">Watch your business thrive as our product transforms raw data into
                                comprehensive reports, insightful analyses, and tangible results. Harness the power of
                                intelligent data processing to drive strategic decisions and fuel more growth for your
                                enterprise.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Process Start -->


        <!-- Download Start -->
        <div class="container-xxl py-5" id="joinus">
            <div class="container py-5 px-lg-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 ps-0  mock-p-0">
                        <img class="img-fluid wow fadeInUp img-4" data-wow-delay="0.1s"
                            src="{{ asset('landing/img/4.png') }}">
                    </div>
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                        <h5 class="text-primary-gradient fw-medium">Join Us</h5>
                        <h1 class="mb-4">Final And Least Way To Your Business Growth</h1>
                        <p class="mb-4">Fuel your business ambition with our software—the final, least-known pathway
                            to insatiable growth. Feed your hunger for success and let our product be the catalyst for
                            unparalleled prosperity</p>
                        <div class="row g-4">
                            <div class="col-sm-6 wow fadeIn" data-wow-delay="0.5s">
                                <a href="#contact" class="d-flex bg-primary-gradient rounded py-3 px-4">
                                    <i class="fa fa-link fa-3x text-white flex-shrink-0"></i>
                                    <div class="ms-3">
                                        <p class="text-white mb-0">Visit</p>
                                        <h5 class="text-white mb-0">Business Joy</h5>
                                    </div>
                                </a>
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
        <!-- <div class="container-xxl py-5" id="pricing">
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
        <!-- <div class="container-xxl py-5" id="review">
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
        <div class="container-xxl py-5" id="contact">
            <div class="container py-5 px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="text-primary-gradient fw-medium">Inquiry</h5>
                    <h1 class="mb-5">Get In Touch!</h1>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="wow fadeInUp" data-wow-delay="0.3s">
                            <p class="text-center mb-4">Unlock the power of innovation with our cutting-edge software
                                solution. Contact us now to elevate your business with seamless technology integration
                                and unparalleled efficiency</p>
                            <form action="{{ route('admin.new') }}" method="get">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" maxlength="30" id="name"
                                                name="name" placeholder="Your Name" required>
                                            <label for="name">Your Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Your Email" maxlength="40" required>
                                            <label for="email">Your Email</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="subject" name="subject"
                                                placeholder="Subject">
                                            <label for="subject">Subject</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" maxlength="12"
                                                id="contact_no" name="contact_no" placeholder="Mobile number"
                                                required>
                                            <label for="contact_no">Mobile Number</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" placeholder="Leave a message here" id="message" name="msg"
                                                style="height: 150px"></textarea>
                                            <label for="message">Message</label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button class="btn btn-primary-gradient rounded-pill fs-5 py-3 px-5"
                                            type="submit">Skyrocket Your Business</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->


        <!-- Footer Start -->
        <div class="container-fluid bg-primary text-light footer wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5 px-lg-5">
                <div class="row g-5">

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
                        <h4 class="text-white mb-4">Address</h4>
                        <p><i class="fa fa-map-marker-alt me-3"></i>India</p>
                        <p><a href="tel:+917948558535" class="text-white"><i
                                    class="fa fa-phone-alt me-3"></i>+917948558535 </a></p>
                        <p><a href="mailto:support@businessjoy.in" class="text-white"><i
                                    class="fa fa-envelope me-3"></i>support@businessjoy.in</a></p>
                        <div class="d-flex pt-2">
                            <!-- <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a> -->
                            <a class="btn btn-outline-light btn-social" target="_blank"
                                href="https://www.facebook.com/oceanmnc7/"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" target="_blank"
                                href="https://www.instagram.com/oceanmnc/?hl=en"><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-outline-light btn-social" target="_blank"
                                href="https://in.linkedin.com/company/ocean-mnc"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Quick Link</h4>
                        <a class="btn btn-link" href="#contact">Contact Us</a>
                        <a class="btn btn-link" href="{{ route('privacypolicy') }}">Privacy Policy</a>
                        <a class="btn btn-link" href="{{ route('termsandconditions') }}">Terms & Condition</a>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-white mb-4">Newsletter</h4>
                        <p>Raise your inbox with exclusive insights and product updates—subscribe now for a front-row
                            seat to innovation</p>
                        <div class="position-relative w-100 mt-3">
                            <form action="{{ route('admin.new') }}" method="get">
                                @csrf
                                <input type="hidden" name="subscribe" value="yes">
                                <input class="form-control border-0 rounded-pill w-100 ps-4 pe-5" type="email"
                                    placeholder="Your Email" name="email" style="height: 48px;">
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
                            Powered By <a class="border-bottom" href="https://www.oceanmnc.com" target="_blank">Ocean
                                MNC</a>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="footer-menu">
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
        <a id="whatsapp-button" href="https://wa.me/+917600596975?text=I%20am%20Interested%20in%20BusinessJoy"
            target="_blank">
            <i class="bi bi-whatsapp fa-2x"></i>
        </a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('landing/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('landing/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('landing/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('landing/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('landing/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('landing/js/main.js') }}"></script>
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
        });
    </script>
</body>

</html>
