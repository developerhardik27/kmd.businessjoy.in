</div>
{{-- wrapper end  --}}

<!-- Footer -->
<footer class="iq-footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#">Version 4.3.2</a></li>
                    <li class="list-inline-item"><a href="{{ route('privacypolicy') }}">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col-lg-6 text-right">
                Copyright {{ date('Y') }} <a href="#">Business Joy</a> All Rights Reserved.
            </div>
        </div>
    </div>
</footer>


<!-- Footer END -->
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->

<script src="{{ asset('admin/js/jquery.min.js') }} "></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ asset('admin/js/popper.min.js') }}"></script>
<script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>

<!-- Appear JavaScript -->
<script src="{{ asset('admin/js/jquery.appear.js') }}"></script>
<!-- Countdown JavaScript -->
<script src="{{ asset('admin/js/countdown.min.js') }}"></script>
<!-- Counterup JavaScript -->
{{-- <script src="{{asset('admin/js/waypoints.min.js')}}"></script> --}}
<script src="{{ asset('admin/js/jquery.counterup.min.js') }}"></script>
<!-- Wow JavaScript -->
<script src="{{ asset('admin/js/wow.min.js') }}"></script>
<!-- Apexcharts JavaScript -->
{{-- <script src="{{asset('admin/js/apexcharts.js')}}"></script> --}}
<!-- Slick JavaScript -->
<script src="{{ asset('admin/js/slick.min.js') }}"></script>
<!-- Select2 JavaScript -->
<script src="{{ asset('admin/js/select2.min.js') }}"></script>
<!-- Magnific Popup JavaScript -->
<script src="{{ asset('admin/js/jquery.magnific-popup.min.js') }}"></script>
<!-- Smooth Scrollbar JavaScript -->
<script src="{{ asset('admin/js/smooth-scrollbar.js') }}"></script>
<!-- lottie JavaScript -->
<script src="{{ asset('admin/js/lottie.js') }}"></script>
<!-- highcharts JavaScript -->
<script src="{{ asset('admin/js/highcharts.js') }}"></script>
<!-- Apexcharts JavaScript -->
<script src="{{ asset('admin/js/apexcharts.js') }}"></script>
<!-- Chart Custom JavaScript -->
<script async src="{{ asset('admin/js/chart-custom.js') }}"></script>
<!-- Custom JavaScript -->
<script src="{{ asset('admin/js/custom.js') }}"></script>
{{-- summernot javascript --}}
<script src="{{ asset('admin/js/summernote-bs4.js') }}"></script>
{{-- sweet alert  --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Dropzone JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<script>
    // function for loader hide and show 
    function loadershow() {
        $("#loader-container").show();
        $(".wrapper").addClass("blurred-content").removeClass("remove-blur");
    }

    function loaderhide() {
        $("#loader-container").hide();
        $(".wrapper").removeClass("blurred-content").addClass("remove-blur");
    }
    //   end loader function 

    function showOffCannvas() {
        $('#offcanvasMenu').addClass('active');
        $('#offcanvasOverlay').addClass('active');
        $('body').addClass('no-scroll'); // Prevent background scroll 
    }

    function hideOffCanvass() {
        $('#offcanvasMenu').removeClass('active');
        $('#offcanvasOverlay').removeClass('active');
        $('body').removeClass('no-scroll'); // Prevent background scroll 
    }

    function ajaxRequest(type, url, data) {
        return $.ajax({
            type,
            url,
            data
        });
    }


    /**
     * setupDateValidation()
     *
     * Validates native input[type="date"] elements.
     * Rules:
     *  - Day   : 1–31
     *  - Month : 1–12
     *  - Year  : exactly 4 digits AND must not exceed the current year
     */
    function setupDateValidation(inputSelector = 'input[type="date"]') {

        const TODAY = new Date();
        const MAX_YEAR = TODAY.getFullYear(); // always the current year e.g. 2026
        const MAX_DATE = `${MAX_YEAR}-12-31`; // "2026-12-31"
        const MIN_DATE = `1000-01-01`;

        document.querySelectorAll(inputSelector).forEach(function(input) {

            // ── Browser-level constraint ──────────────────────────────────────────
            // Restricts the native date-picker spinner so it won't go past MAX_DATE.
            input.setAttribute('min', MIN_DATE);
            input.setAttribute('max', MAX_DATE);

            // ── Inline error element (injected once, right after the input) ───────
            let errorEl = input.nextElementSibling;
            if (!errorEl || !errorEl.classList.contains('date-val-error')) {
                errorEl = document.createElement('div');
                errorEl.className = 'date-val-error';
                errorEl.style.cssText = [
                    'color:#dc3545',
                    'font-size:12px',
                    'margin-top:3px',
                    'display:none',
                ].join(';');
                input.insertAdjacentElement('afterend', errorEl);
            }

            function showError(msg) {
                errorEl.textContent = msg;
                errorEl.style.display = 'block';
                input.style.borderColor = '#dc3545';
                input.style.boxShadow = '0 0 0 0.2rem rgba(220,53,69,.25)';
            }

            function clearError() {
                errorEl.textContent = '';
                errorEl.style.display = 'none';
                input.style.borderColor = '';
                input.style.boxShadow = '';
            }

            function validate() {
                const value = input.value; // always "YYYY-MM-DD" or ""

                // Empty → nothing to validate
                if (!value) {
                    clearError();
                    return;
                }

                const parts = value.split('-');
                const yearStr = parts[0] || '';
                const month = parseInt(parts[1], 10);
                const day = parseInt(parts[2], 10);
                const year = parseInt(yearStr, 10);

                // Wait until year is fully typed (4 chars) before validating
                if (yearStr.length < 4) {
                    clearError();
                    return;
                }

                // ── Block 5+ digit years (browser freely allows "10000") ──────────
                if (yearStr.length > 4) {
                    showError('Year must be exactly 4 digits.');
                    input.value = '';
                    return;
                }

                // ── Year must not exceed current year ─────────────────────────────
                if (year > MAX_YEAR) {
                    showError(`Year cannot be above ${MAX_YEAR} (current year).`);
                    input.value = '';
                    return;
                }

                // ── Year lower bound ──────────────────────────────────────────────
                if (year < 1000) {
                    showError('Year must be at least 1000.');
                    input.value = '';
                    return;
                }

                // ── Month ─────────────────────────────────────────────────────────
                if (isNaN(month) || month < 1 || month > 12) {
                    showError('Month must be between 1 and 12.');
                    input.value = '';
                    return;
                }

                // ── Day ───────────────────────────────────────────────────────────
                if (isNaN(day) || day < 1 || day > 31) {
                    showError('Day must be between 1 and 31.');
                    input.value = '';
                    return;
                }

                // ── All valid ─────────────────────────────────────────────────────
                clearError();
            }

            // 'input'  → fires on every keystroke / spinner click
            // 'blur'   → catches paste and autofill
            input.addEventListener('input', validate);
            input.addEventListener('blur', validate);
        });
    }

    // ── Run on page load ──────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        setupDateValidation();

        // For inputs inside modals or dynamically loaded content, call again:
        // setupDateValidation('#myModal input[type="date"]');
        // setupDateValidation('.sidebar-filters input[type="date"]');
    });

    function handleAjaxError(xhr) {
        console.log("xhr", xhr);
        if (xhr.status === 422) {
            let errors = xhr.responseJSON.errors;
            let firstErrorElement = null;
            $('.error-msg').text('');

            $.each(errors, function(key, value) {
                let errorElement = null;

                if (key.startsWith('rows.')) {
                    let parts = key.split('.');
                    let rowIndex = parts[1];
                    let field = parts[2];

                    errorElement = $('#error-' + field + '_' + rowIndex);
                } else {
                    errorElement = $('#error-' + key);
                }

                if (errorElement && errorElement.length) {
                    errorElement.text(value[0]);
                    if (!firstErrorElement) {
                        firstErrorElement = errorElement;
                    }
                }
            });
            if (firstErrorElement) {
                $('html, body').animate({
                    scrollTop: firstErrorElement.offset().top - 100
                }, 800);
            }

            return;
        }
        let errorMessage = "An error occurred";
        try {
            let responseJSON = JSON.parse(xhr.responseText);
            errorMessage = responseJSON.message || errorMessage;
        } catch (e) {}

        Toast.fire({
            icon: "error",
            title: errorMessage
        });
    }


    function handleModalAjaxError(xhr) {
        console.log("xhr", xhr);
        if (xhr.status === 422) {

            var errors = xhr.responseJSON.errors;
            let firstErrorElement = null;

            $.each(errors, function(key, value) {
                let errorElement = $('#modal-error-' + key);
                errorElement.text(value[0]);

                // Capture the first error element
                if (!firstErrorElement) {
                    firstErrorElement = errorElement;
                }
            });

            if (firstErrorElement) {
                $('.modal-body').animate({
                    scrollTop: firstErrorElement.offset().top -
                        100 // adjust for spacing
                }, 800);
            }
        } else {
            var errorMessage = "An error occurred";
            try {
                var responseJSON = JSON.parse(xhr.responseText);
                errorMessage = responseJSON.message || errorMessage;
            } catch (e) {}
            Toast.fire({
                icon: "error",
                title: errorMessage
            });
        }
    }

    // sweet alert functions
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            // On mouse enter, stop the timer
            toast.addEventListener('mouseenter', () => {
                Swal.stopTimer();
            });

            // On mouse leave, resume the timer
            toast.addEventListener('mouseleave', () => {
                Swal.resumeTimer();
            });
        }
    });

    // Function to show the SweetAlert2 confirmation box with dynamic icon
    function showConfirmationDialog(title, text, confirmText, cancelText, icon, callback, errorCallback) {
        Swal.fire({
            title: title, // Dynamic title
            text: text, // Dynamic text
            icon: icon, // Dynamic icon (can be 'warning', 'error', 'success', 'info', 'question')
            showCancelButton: true, // Show cancel button
            confirmButtonText: confirmText, // Dynamic confirm button text
            confirmButtonColor: "#253566",
            cancelButtonText: cancelText, // Dynamic cancel button text
            cancelButtonColor: "#FF7A29",
        }).then((result) => {
            if (result.isConfirmed) {
                callback(); // Execute the callback function after the loader
            } else if (result.isDismissed && errorCallback) {
                errorCallback(); // Execute the error callback if canceled and errorCallback is provided
            }
        });
    }
</script>
<script>
    $('document').ready(function() {

        $(document).on('click', '[data-toggle="tooltip"]', function() {
            $(this).tooltip('hide');
        });

        $.ajax({
            type: 'GET',
            url: "{{ route('user.username') }}",
            data: {
                user_id: "{{ session()->get('user_id') }}",
                token: "{{ session()->get('api_token') }}",
                company_id: "{{ session()->get('company_id') }}"
            },
            success: function(response) {
                var user = response.user[0];
                var username = user.lastname != null ? user.lastname : ' ';
                $('#username').text(user.firstname + ' ' + username);
                $('#usernamein').append(' ' + user.firstname + ' ' + username);
                $('#loggedcompanyname').append(user.name);
                $('#afterclickcompanyname').append(user.name);
                var imgname = user.img;
                if (imgname != null) {
                    var imgElement = $('<img>').attr('src', '/uploads/' + imgname).attr('alt',
                        'User Image').attr('class', 'img-fluid rounded mr-0 mr-lg-3');
                    $('#userimg').prepend(imgElement);
                } else {
                    var firstInitial = user.firstname ? user.firstname.charAt(0).toUpperCase() : '';
                    var lastInitial = user.lastname ? user.lastname.charAt(0).toUpperCase() : '';
                    var initials = firstInitial + lastInitial;

                    var initialsDiv = $('<div>').text(initials).addClass(
                        'avatar-placeholder img-fluid rounded mr-0 mr-lg-3');
                    $('#userimg').prepend(initialsDiv);
                }
            },
            error: function(xhr) {
                if (xhr.status == 401) {
                    window.location.href = "{{ route('admin.singlelogout') }}";
                }
            }

        });

        $('.search-link').on('click', function(e) {
            e.preventDefault();
            var search = $('.search-input').val();
            var url = "{{ route('admin.invoice') }}?search=" + encodeURIComponent(search);
            if ("{{ session()->get('menu') }}" == 'invoice') {
                var url = "{{ route('admin.invoice') }}?search=" + encodeURIComponent(search);
            } else if ("{{ session()->get('menu') }}" == 'lead') {
                var url = "{{ route('admin.lead') }}?search=" + encodeURIComponent(search);
            } else if ("{{ session()->get('menu') }}" == 'admin') {
                var url = "{{ route('admin.user') }}?search=" + encodeURIComponent(search);
            } else if ("{{ session()->get('menu') }}" == 'inventory') {
                var url = "{{ route('admin.product') }}?search=" + encodeURIComponent(search);
            }
            // else if ("{{ session()->get('menu') }}" == 'account') {}
            else if ("{{ session()->get('menu') }}" == 'reminder') {
                var url = "{{ route('admin.reminder') }}?search=" + encodeURIComponent(search);
            } else if ("{{ session()->get('menu') }}" == 'Customer support') {
                var url = "{{ route('admin.customersupport') }}?search=" + encodeURIComponent(search);
            } else if ("{{ session()->get('menu') }}" == 'blog') {
                var url = "{{ route('admin.blog') }}?search=" + encodeURIComponent(search);
            } else if ("{{ session()->get('menu') }}" == 'quotation') {
                var url = "{{ route('admin.blog') }}?search=" + encodeURIComponent(search);
            }
            window.location.href = url;
        })

        $(document).on("click", ".changemenu", function(e) {
            e.preventDefault();
            var element = $(this);
            var value = element.data('value');
            $.ajax({
                url: "{{ route('admin.setmenusession') }}",
                type: "GET",
                data: {
                    value: value
                },
                success: function(response) {
                    $('#menuOption').html(element.html());
                    Toast.fire({
                        icon: "success",
                        title: `Logged in ${response.status} succesfully`
                    });

                    window.location.href = "{{ route('admin.welcome') }}";
                },
                error: function(error) {
                    Toast.fire({
                        icon: "error",
                        title: "something went wrong!"
                    });
                }
            });
        });


        var selectedMenuFromSession = "{{ session()->get('menu') }}";

        if (selectedMenuFromSession) {
            $('#pagemenu').text(selectedMenuFromSession);
        } else {
            $('#nothasmenu').text('Welcome to business joy, Ask your admin for required module access.');
        }

        $('#pagemenu').text(selectedMenuFromSession);
        // Check if the server-side session variable is set
        if (selectedMenuFromSession) {
            var selectedMenuElement = $('.changemenu[data-value="' + selectedMenuFromSession + '"]');
            var selectedMenuHTML = selectedMenuElement.html();
            selectedMenuHTML += '<i class="ri-arrow-down-s-line"></i>';
            $('#menuOption').html(selectedMenuHTML);
        }
    });
</script>
@stack('ajax')
</body>

</html>
