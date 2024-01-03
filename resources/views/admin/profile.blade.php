@extends('admin.masterpage')

@section('style')
    <style>
        #editicon {
            top: auto !important;
            right: 8px !important;
            bottom: 10px !important;
            transition: all .3s cubic-bezier(.175, .885, .32, 1.275) !important;
            background: var(--iq-primary) !important;
            color: var(--iq-white) !important;
            border-radius: 50% !important;
            height: 30px !important;
            width: 30px !important;
            line-height: 28px !important;
            text-align: center !important;
            font-size: 16px !important;
            cursor: pointer !important;
        }
    </style>
@endsection
@section('page_title')
    user profile
@endsection

@section('page-content')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">

                    <div class="iq-card-block iq-card-stretch ">
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Profile</h4>
                                </div>
                                <div >
                                  <a href="{{route('admin.edituserdetail',['id'=>Session::get('user_id')])}}"><i id="editicon" class="ri-pencil-fill float-right"></i></a>  
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <div class="user-detail text-center">
                                    <div class="user-profile" id="profile_img">

                                    </div>
                                    <div class="profile-detail mt-3">
                                        <h3 class="d-inline-block" id="name"></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="iq-card-block iq-card-stretch ">
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">About User</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <div class="mt-2">
                                    <h6>Email: <span id="useremail"></span></h6>

                                </div>
                                <div class="mt-2">
                                    <h6>Contact: <span id="usercontact"></span></h6>

                                </div>
                                <div class="mt-2">
                                    <h6>City: <span id="usercity"></span></h6>

                                </div>
                                <div class="mt-2">
                                    <h6>State: <span id="userstate"></span></h6>

                                </div>
                                <div class="mt-2">
                                    <h6>Country: <span id="usercountry"></span></h6>

                                </div>
                                <div class="mt-2">
                                    <h6>Company: <span id="usercompany"></span></h6>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            $.ajax({
                type: 'GET',
                url: '{{ route('user.profile') }}',
                data: {
                    user_id: {{ session()->get('user_id') }},
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {

                    if (response.status == 200 && response.user != '') {
                        var user = response.user[0];
                        $('#name').text(user.firstname + ' ' + user.lastname);
                        $('#useremail').text(user.email);
                        $('#usercontact').text(user.contact_no);
                        $('#usercity').text(user.city_name);
                        $('#userstate').text(user.state_name);
                        $('#usercountry').text(user.country_name);
                        $('#usercompany').text(user.name);
                        var imgElement = $('<img>').attr('src', '/uploads/' + user.img).attr(
                            'alt', 'profile-img').attr('class', 'avatar-130 img-fluid');
                        $('#profile_img').append(imgElement);

                    } else {

                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });
    </script>
@endpush
