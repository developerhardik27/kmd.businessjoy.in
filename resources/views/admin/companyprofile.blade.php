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
    {{ config('app.name') }} - Company profile
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
                                    <h4 class="card-title">Company Profile</h4>
                                </div>
                                <div>
                                    @if (session('user_permissions.invoicemodule.company.edit') == '1')
                                        <a
                                            href="{{ route('admin.editcompanyprofile', ['id' => Session::get('company_id')]) }}"><i
                                                id="editicon" class="ri-pencil-fill float-right"></i>
                                        </a>
                                    @endif
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
                                    <h4 class="card-title">About company</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <div class="mt-2">
                                    <h6>Name: <span id="companyname"></span></h6>
                                </div>
                                <div class="mt-2">
                                    <h6>Email: <span id="companyemail"></span></h6>
                                </div>
                                <div class="mt-2">
                                    <h6>Contact: <span id="companycontact"></span></h6>
                                </div>
                                <div class="mt-2">
                                    <h6>Address: <span id="companyaddress"></span></h6>
                                </div>
                                <div class="mt-2">
                                    <h6>City: <span id="companycity"></span></h6>
                                </div>
                                <div class="mt-2">
                                    <h6>State: <span id="companystate"></span></h6>
                                </div>
                                <div class="mt-2">
                                    <h6>Country: <span id="companycountry"></span></h6>
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

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            
            // get user company data
            $.ajax({
                type: 'GET',
                url: '{{ route('company.profile') }}',
                data: {
                    user_id: {{ session()->get('user_id') }},
                    company_id: {{ session()->get('company_id') }},
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.company != '') {
                        loaderhide();
                        var company = response.company[0];
                        $('#companyname').text(company.name);
                        $('#companyemail').text(company.email);
                        $('#companycontact').text(company.contact_no);
                        $('#companyaddress').text(company.address + ',' + company.pincode);
                        $('#companycity').text(company.city_name);
                        $('#companystate').text(company.state_name);
                        $('#companycountry').text(company.country_name);

                        var imgElement = $('<img>').attr('src', '/uploads/' + company.img).attr(
                            'alt', 'profile-img').attr('class', 'avatar-130 img-fluid');
                        $('#profile_img').prepend(imgElement);

                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    } else {
                        loaderhide();
                        toastr.error('something went wrong !');
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                    loaderhide();
                }
            });
        });
    </script>
@endpush
