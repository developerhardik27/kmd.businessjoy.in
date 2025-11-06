</div>
{{-- wrapper end  --}}

<!-- Footer -->
<footer class="iq-footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#">Version 4.2.2</a></li>
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
                }else{
                    var firstInitial = user.firstname ? user.firstname.charAt(0).toUpperCase() : '';
                    var lastInitial = user.lastname ? user.lastname.charAt(0).toUpperCase() : '';
                    var initials = firstInitial + lastInitial;

                    var initialsDiv = $('<div>').text(initials).addClass('avatar-placeholder img-fluid rounded mr-0 mr-lg-3');
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
