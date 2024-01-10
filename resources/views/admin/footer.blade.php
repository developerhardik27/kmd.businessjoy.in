</div>
{{-- wrapper end  --}}

<!-- Footer -->
<footer class="iq-footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#">Version 1.0</a></li>
                    <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                    <li class="list-inline-item"><a href="#">Terms of Use</a></li>
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
<!-- am core JavaScript -->
{{-- <script src="{{asset('admin/js/core.js')}}"></script> --}}
<!-- am charts JavaScript -->
{{-- <script src="{{asset('admin/js/charts.js')}}"></script> --}}
<!-- am animated JavaScript -->
{{-- <script src="{{asset('admin/js/animated.js')}}"></script> --}}
<!-- am kelly JavaScript -->
{{-- <script src="{{asset('admin/js/kelly.js')}}"></script> --}}
<!-- Morris JavaScript -->
{{-- <script src="{{asset('admin/js/morris.js')}}"></script> --}}
<!-- am maps JavaScript -->
{{-- <script src="{{asset('admin/js/maps.js')}}"></script> --}}
<!-- am worldLow JavaScript -->
{{-- <script src="{{asset('admin/js/worldLow.js')}}"></script> --}}
<!-- ChartList Js -->
{{-- <script src="{{asset('admin/js/chartist/chartist.min.js')}}"></script> --}}
<!-- highcharts JavaScript -->
<script src="{{ asset('admin/js/highcharts.js') }}"></script>
<!-- Chart Custom JavaScript -->
<script async src="{{ asset('admin/js/chart-custom.js') }}"></script>
<!-- Custom JavaScript -->
<script src="{{ asset('admin/js/custom.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/r/ju-1.11.4/jqc-1.11.3,dt-1.10.8/datatables.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
       // function for loader hide and show 
       function loadershow() {
            $("#loader-container").show();
        }

        function loaderhide() {
            $("#loader-container").hide();
            $("#wrapper").removeClass("blurred-content").addClass("remove-blur");
        }
        //   end loader function 
</script>
<script>
    $('document').ready(function() {

       
        $.ajax({
            type: 'get',
            url: " /api/username",
            data: {
                user_id: {{ session()->get('user_id') }},
                token: "{{ session()->get('api_token') }}"
            },
            success: function(response) {
                var user = response.user[0];
                $('#username').text(user.firstname + ' ' + user.lastname);
                $('#usernamein').append(' ' + user.firstname + ' ' + user.lastname);
                $('#loggedcompanyname').append('-' + user.name);
                $('#afterclickcompanyname').append('-' + user.name);
                var imgname = user.img;
                if (imgname != null) {
                    var imgElement = $('<img>').attr('src', '/uploads/' + imgname).attr('alt',
                        'User Image').attr('class', 'img-fluid rounded mr-3');
                    $('#userimg').prepend(imgElement);
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON.error === 'Invalid token') {
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
            } else {
                var url = "{{ route('admin.lead') }}?search=" + encodeURIComponent(search);
            }
            window.location.href = url;
        })

        $(document).on("click", ".changemenu", function(e) {
            e.preventDefault();
            var value = $(this).data('value');
            $.ajax({
                url: "{{ route('admin.setmenusession') }}",
                type: "get",
                data: {
                    value: value
                },
                success: function(response) {
                    $('#menuOption').html($(this).html());
                    toastr.success(`Logged in ${response.status} succesfully`);
                    window.location.href = "{{ route('admin.welcome') }}";
                },
                error: function(error) {
                    toastr.error('Something Went Wrong!');
                }
            });
        });


        var selectedMenuFromSession = "{{ session()->get('menu') }}";
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
