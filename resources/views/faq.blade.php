<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Business Joy</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        body {
            text-align: center;
        }

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
            text-transform: initial !important;
            letter-spacing: auto !important;
            font-weight: 300;
            font-family: Heebo, sans-serif !important;
        }

        .card-title>button,
        .card-title>button:active {
            display: block;
            color: #555;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            word-spacing: 3px;
            text-decoration: none;
            text-align: left
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


        .card-header {
            padding: 10px 0 10px 0 !important;
            text-align: left;
        }

        .card-body {
            text-align: left
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
            <nav class="navbar navbar-expand-lg navbar-light justify-content-center px-4 px-lg-5 py-3 py-lg-0">
                <a href="/" class="navbar-brand p-0">
                    <img src="{{ asset('landing/img/logo.png') }}" alt="Logo">
                </a>
            </nav>
            <div class="hero-header">
                <div class="col-sm-12 text-center">
                    <h1 class="text-white">Frequently Asked questions</h1>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->


        <div class="container my-5">
            <p>
                Welcome to the Business Joy FAQ page! Here you'll find answers to common questions about our CRM
                software.
                If you have any additional questions, please feel free to contact our support team.
            </p>
            <div class="accordion" id="accordionExample">
                <h3 class="py-3">General FAQs</h3>
                <div class="card">
                    <div class="card-header active" id="headingOne">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                1. What is Business Joy?
                            </button>
                        </h2>
                    </div>

                    <div id="collapseOne" class="collapse card-collapse show" aria-labelledby="headingOne"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            <a href="/"> Business Joy </a> is an online CRM software designed to help businesses
                            manage their customer relationships, invoicing, leads, customer support, company roles,
                            permissions, appointments, and notifications efficiently.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                aria-controls="collapseTwo">
                                2. How do I sign up for Business Joy?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="card-collapse collapse" aria-labelledby="headingTwo"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            You can sign up for Business Joy by visiting our <a href="#">
                                Signup page </a> and filling out the registration form with your details. Once you
                            complete
                            the registration process, you can start using our CRM features.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingThree">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                aria-controls="collapseThree">
                                3. What are the system requirements to use Business Joy?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseThree" class="collapse card-collapse" aria-labelledby="headingThree"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            Business Joy is a web-based application that can be accessed via any modern web browser on
                            devices with an internet connection. There are no specific system requirements beyond this.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingFour">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseFour" aria-expanded="false"
                                aria-controls="collapseFour">
                                4. Is there a mobile app for Business Joy?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFour" class="collapse card-collapse" aria-labelledby="headingFour"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            Currently, Business Joy is accessible through mobile web browsers. We are working on
                            developing
                            a dedicated mobile app for a more seamless experience on mobile devices.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingFive">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseFive" aria-expanded="false"
                                aria-controls="collapseFive">
                                5. How can I contact customer support?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFive" class="collapse card-collapse" aria-labelledby="headingFive"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            You can contact our customer support team by emailing
                            <a href="mailto:support@businessjoy.com">support@businessjoy.com</a> or by using the
                            support
                            chat feature available on our website.

                        </div>
                    </div>
                </div>

                <h3 class="py-3"> Invoice Module FAQs</h3>
                <div class="card">
                    <div class="card-header" id="headingSix">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseSix" aria-expanded="false"
                                aria-controls="collapseSix">
                                6. How do I create an invoice?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseSix" class="collapse card-collapse" aria-labelledby="headingSix"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            To create an invoice, navigate to the Invoice module, click on "Create New Invoice," fill in
                            the
                            required details such as client information, items, and amounts, and then save or send the
                            invoice directly to your client.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingSeven">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false"
                                aria-controls="collapseSeven">
                                7. Can I customize the invoice formula?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseSeven" class="collapse card-collapse" aria-labelledby="headingSeven"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            Yes, Business Joy allows you to customize your invoice formula. You can add your
                            calculation,
                            change the login, and include additional fields as needed.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingEight">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseEight" aria-expanded="false"
                                aria-controls="collapseEight">
                                8. How do I track paid and unpaid invoices?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseEight" class="collapse card-collapse" aria-labelledby="headingEight"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            In the Invoice module, you can view all invoices and their statuses. Unpaid invoices will be
                            marked as "Pending" or "Overdue," while paid invoices will be marked as "Paid."
                        </div>
                    </div>
                </div>

                <h3 class="py-3">Lead Management FAQs</h3>
                <div class="card">
                    <div class="card-header" id="headingNine">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseNine" aria-expanded="false"
                                aria-controls="collapseNine">
                                9. How do I add a new lead?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseNine" class="collapse card-collapse" aria-labelledby="headingNine"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            To add a new lead, go to the Leads module, click on "Add New Lead," enter the lead's details
                            such as name, contact information, and lead source, and then save the lead.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTen">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseTen" aria-expanded="false"
                                aria-controls="collapseTen">
                                10. Can I assign leads to specific team members?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTen" class="collapse card-collapse" aria-labelledby="headingTen"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            Yes, you can assign leads to specific team members by selecting the team member from the
                            dropdown menu when adding or editing a lead.
                        </div>
                    </div>
                </div>

                <h3 class="py-3">Customer Support FAQs</h3>
                <div class="card">
                    <div class="card-header" id="headingEleven">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseEleven" aria-expanded="false"
                                aria-controls="collapseEleven">
                                11. How do I create a support ticket?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseEleven" class="collapse card-collapse" aria-labelledby="headingEleven"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            To create a support ticket, navigate to the Customer Support module, click on "Create New
                            Ticket," enter the necessary information such as the customer's issue and contact details,
                            and
                            submit the ticket.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTwelve">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseTwelve" aria-expanded="false"
                                aria-controls="collapseTwelve">
                                12. How do I track the status of support tickets?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwelve" class="collapse card-collapse" aria-labelledby="headingTwelve"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            You can track the status of support tickets in the Customer Support module. Each ticket will
                            display its current status (e.g., Open, In Progress, and Resolved) and any updates or notes.
                        </div>
                    </div>
                </div>
                <h3 class="py-3">Company, Role, and Permission Management
                    FAQs</h3>
                <div class="card">
                    <div class="card-header" id="headingThirteen">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseThirteen" aria-expanded="false"
                                aria-controls="collapseThirteen">
                                13. How do I add a new user to my company?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseThirteen" class="collapse card-collapse" aria-labelledby="headingThirteen"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            Currently, business joy is following per user/month system. So, just email or contact our
                            customer support, they will create new user for your company. The new user will receive an
                            email
                            invitation (welcome email) to join Business Joy.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingFourteen">
                        <h2 class="mb-0 card-title">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseFourteen" aria-expanded="false"
                                aria-controls="collapseFourteen">
                                14. What roles and permissions can I assign?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFourteen" class="collapse card-collapse" aria-labelledby="headingFourteen"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            Business Joy offers customizable roles and permissions. You can create roles such as Admin,
                            Manager, and User, and assign specific permissions to control access to different modules
                            and
                            features.
                        </div>
                    </div>
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
</body>

</html>
