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
            margin-bottom: 0;
            padding: 8rem 0 0 0;
        }

        .hero-header h1 {
            position: relative;
            top: -25px
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

        .grecaptcha-badge {
            visibility: hidden !important;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>

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
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0 justify-content-center">
                <a href="" class="navbar-brand p-0">
                    <!-- <h1 class="m-0">FitApp</h1> -->
                    <img src="{{ asset('landing/img/logo.png') }}" alt="Logo">
                </a>
            </nav>
            <div class="hero-header">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="mb-5 text-white">Become a Partner</h1>
                </div>
            </div>
        </div>

        <!-- Contact Start -->
        <div class="mb-5" id="become-a-partner">
            <div class="container px-lg-5">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="wow fadeInUp" data-wow-delay="0.3s">
                            <form action="{{ route('admin.storenewpartner') }}" class="bj-landing-forms" method="Post" id="becomeAPartnerForm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control']) maxlength="30"
                                                id="company_name" name="company_name" placeholder="Company Name"
                                                required>
                                            <label for="company_name">Company Name*</label>
                                            <span class="error-msg" id="error-company_name" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control']) maxlength="50"
                                                id="company_website" name="company_website"
                                                placeholder="Company Website">
                                            <label for="company_website">Company Website</label>
                                            <span class="error-msg" id="error-company_website" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea @class(['form-control']) placeholder="Company Address" id="company_address" name="company_address"
                                                style="height: 150px" maxlength="200"></textarea>
                                            <label for="company_address">Company Address</label>
                                            <span class="error-msg" id="error-company_address" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control'])
                                                id="company_area" name="company_area" placeholder="Company Area">
                                            <label for="company_area">Company Area</label>
                                            <span class="error-msg" id="error-company_area" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control'])
                                                id="company_pincode" name="company_pincode"
                                                placeholder="Company Pincode">
                                            <label for="company_pincode">Company Pincode</label>
                                            <span class="error-msg" id="error-company_pincode" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control'])
                                                id="company_city" name="company_city" placeholder="Company city">
                                            <label for="company_city">Company City</label>
                                            <span class="error-msg" id="error-company_city" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control'])
                                                id="company_state" name="company_state"
                                                placeholder="Company State">
                                            <label for="company_state">Company State</label>
                                            <span class="error-msg" id="error-company_state" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control'])
                                                id="company_country" name="company_country"
                                                placeholder="Company Country">
                                            <label for="company_country">Company Country</label>
                                            <span class="error-msg" id="error-company_country" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control']) 
                                                id="company_tax_identification_number"
                                                name="company_tax_identification_number"
                                                placeholder="Company Tac Identification Number">
                                            <label for="company_tax_identification_number">Company Tax Identification
                                                Number</label>
                                            <span class="error-msg" id="error-company_tax_identification_number" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control'])
                                                id="contact_person_name" name="contact_person_name"
                                                placeholder="Company Person Name" required>
                                            <label for="contact_person_name">Contact Person Name*</label>
                                            <span class="error-msg" id="error-contact_person_name" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" @class(['form-control'])
                                                id="contact_person_email" name="contact_person_email"
                                                placeholder="Company Person Email" required>
                                            <label for="contact_person_email">Contact Person Email*</label>
                                            <span class="error-msg" id="error-contact_person_email" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" @class(['form-control']) 
                                                id="contact_person_mobile_number" name="contact_person_mobile_number"
                                                placeholder="Contact Person Mobile Number" required>
                                            <label for="contact_person_mobile_number">Contact Person Mobile
                                                Number*</label>
                                            <span class="error-msg" id="error-contact_person_mobile_number" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button id="submitBtn"
                                            class="btn btn-primary-gradient rounded-pill fs-5 py-3 px-5"
                                            type="submit">Submit</button> 
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
                <div class="row"> 
                    <div class="col-md-6 col-lg-3 text-center">
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
                        <a class="btn btn-link" href="{{ route('faq') }}">FAQ</a>
                        <a class="btn btn-link" href="{{ route('privacypolicy') }}">Privacy Policy</a>
                        <a class="btn btn-link" href="{{ route('termsandconditions') }}">Terms & Condition</a>
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
                        <div class="col-md-6 text-md-start mb-3 mb-md-0">
                            &copy; <a class="border-bottom" href="https://www.businessjoy.in">Business Joy</a>, All
                            Right Reserved.

                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            {{-- Powered By <a class="border-bottom" href="https://www.oceanmnc.com" target="_blank">Ocean
                                MNC</a> --}}
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="footer-menu custom-display-flex">
                                {{-- <a href="#home">Home</a> --}}
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
  
            $('#contact_person_mobile_number').on('input', function() {
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
</body>

</html>
