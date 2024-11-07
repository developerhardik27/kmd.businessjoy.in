<!DOCTYPE html>
<html lang="en">

<head>

    {{-- meta updated on 6-6-2024 --}}
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
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

    <style>
        .hero-header {
            margin: 0px;
        }
    </style>

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
                        class="btn btn-primary-gradient d-lg-block m-sm-0 ms-3 px-4 px-lg-4 px-sm-3 py-2 py-md-1 py-sm-1 rounded-pill">
                        Login</a>
                </div>
            </nav>
            <div class="container-xxl bg-primary hero-header">
            </div>
        </div>

        <!-- Contact Start -->
        <div class="container-xxl" id="become-a-partner">
            <div class="container px-lg-5">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="mb-5">Become a Partner</h1>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="wow fadeInUp" data-wow-delay="0.3s">
                            <form action="{{ route('admin.storenewpartner') }}" method="Post"
                                id="becomeAPartnerForm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control', 'is-invalid' => $errors->has('company_name')]) maxlength="30"
                                                id="company_name" name="company_name"
                                                value="{{ old('company_name') }}" placeholder="Company Name" required>
                                            <label for="company_name">Company Name*</label>
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('company_website'),
                                            ]) maxlength="30"
                                                id="company_website" name="company_website"
                                                value="{{ old('company_website') }}" placeholder="Company Website">
                                            <label for="company_website">Company Website</label>
                                            @error('company_website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('company_address'),
                                            ]) placeholder="Company Address" id="company_address" name="company_address"
                                                style="height: 150px">{{ old('company_address') }}</textarea>
                                            <label for="company_address">Company Address</label>
                                            @error('company_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control', 'is-invalid' => $errors->has('company_area')]) maxlength="30"
                                                id="company_area" name="company_area"
                                                value="{{ old('company_area') }}" placeholder="Company Website">
                                            <label for="company_area">Company Area</label>
                                            @error('company_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('company_pincode'),
                                            ]) maxlength="30"
                                                id="company_pincode" name="company_pincode"
                                                value="{{ old('company_pincode') }}" placeholder="Company Website">
                                            <label for="company_pincode">Company Pincode</label>
                                            @error('company_pincode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control', 'is-invalid' => $errors->has('company_city')]) maxlength="30"
                                                id="company_city" name="company_city"
                                                value="{{ old('company_city') }}" placeholder="Company Website">
                                            <label for="company_city">Company City</label>
                                            @error('company_city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('company_state'),
                                            ]) maxlength="30"
                                                id="company_state" name="company_state"
                                                value="{{ old('company_state') }}" placeholder="Company Website">
                                            <label for="company_state">Company State</label>
                                            @error('company_state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('company_country'),
                                            ]) maxlength="30"
                                                id="company_country" name="company_country"
                                                value="{{ old('company_country') }}" placeholder="Company Website">
                                            <label for="company_country">Company Country</label>
                                            @error('company_country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('company_tax_identification_number'),
                                            ]) maxlength="30"
                                                id="company_tax_identification_number"
                                                name="company_tax_identification_number"
                                                value="{{ old('company_tax_identification_number') }}"
                                                placeholder="Company Website">
                                            <label for="company_tax_identification_number">Company Tax Identification
                                                Number</label>
                                            @error('company_tax_identification_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('contact_person_name'),
                                            ]) maxlength="30"
                                                id="contact_person_name" name="contact_person_name"
                                                value="{{ old('contact_person_name') }}"
                                                placeholder="Company Website" required>
                                            <label for="contact_person_name">Contact Person Name*</label>
                                            @error('contact_person_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('contact_person_email'),
                                            ]) maxlength="30"
                                                id="contact_person_email" name="contact_person_email"
                                                value="{{ old('contact_person_email') }}"
                                                placeholder="Company Website" required>
                                            <label for="contact_person_email">Contact Person Email*</label>
                                            @error('contact_person_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class([
                                                'form-control',
                                                'is-invalid' => $errors->has('contact_person_mobile_number'),
                                            ]) maxlength="30"
                                                id="contact_person_mobile_number" name="contact_person_mobile_number"
                                                value="{{ old('contact_person_mobile_number') }}"
                                                placeholder="Company Website" required>
                                            <label for="contact_person_mobile_number">Contact Person Mobile
                                                Number*</label>
                                            @error('contact_person_mobile_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button id="submitBtn"
                                            class="btn btn-primary-gradient rounded-pill fs-5 py-3 px-5"
                                            type="submit">Submit</button>
                                        <span id="waitingBtn" class="btn btn-primary-gradient rounded-pill">Please
                                            Wait...</span>
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
                        <h4 class="text-white mb-4">Contact</h4>
                        {{-- <p><i class="fa fa-map-marker-alt me-3"></i>India</p>
                        <p><a href="tel:+917948558535" class="text-white"><i
                                    class="fa fa-phone-alt me-3"></i>+917948558535 </a></p> --}}
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
                            {{-- Powered By <a class="border-bottom" href="https://www.oceanmnc.com" target="_blank">Ocean
                                MNC</a> --}}
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
        {{-- <a id="whatsapp-button" href="https://wa.me/+917600596975?text=I%20am%20Interested%20in%20BusinessJoy"
            target="_blank">
            <i class="bi bi-whatsapp fa-2x"></i>
        </a> --}}
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


            if ($('.is-invalid').length) {
            // Scroll the first invalid field into view
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100  // Adjust scroll position to bring field into view (you can change the -100 value if needed)
            }, 500);  // Scroll speed in milliseconds
        }

            $('#submitBtn').show();
            $('#waitingBtn').hide();

            $('#contact_person_mobile_number').on('input', function() {
                var inputValue = $(this).val();
                var digitOnlyRegex = /^\d*$/; // Regular expression to allow only digits

                if (!digitOnlyRegex.test(inputValue)) {
                    // Remove non-digit characters from the input
                    $(this).val(inputValue.replace(/\D/g, ''));
                }
            });

            $('#becomeAPartnerForm').on('submit', function() {
                $('#submitBtn').hide();
                $('#waitingBtn').show();
            });

        });
    </script>
</body>

</html>
