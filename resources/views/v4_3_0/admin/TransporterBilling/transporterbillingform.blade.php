@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Add New Transporter Bill
@endsection
@section('title')
    New Transporter Bill
@endsection


@section('style')
    <style>
        table th {
            text-align: center;
        }

        @media (max-width: 769px) {
            /* Your responsive styles here */

            table input.form-control {
                width: auto;
            }

            table textarea.form-control {
                width: auto;
            }

        }
    </style>
@endsection


@section('form-content')
    <form id="transporterbillingform" name="transporterbillingform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-4 mb-2">
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                        placeholder="user_id" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                        placeholder="company_id" required />

                    <label for="bill_number">Bill Number</label><span style="color:red;">*</span>
                    <input type="text" name="bill_number" class="form-control" id="bill_number" placeholder="Bill Number"/>
                    <span class="error-msg" id="error-bill_number" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="bill_date">Bill Date</label><span style="color:red;">*</span>
                    <input type="date" name="bill_date" class="form-control" id="bill_date" />
                    <span class="error-msg" id="error-bill_date" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="party">Party</label><span style="color:red;">*</span>
                    <select type="text" id="party" name="party" class="form-control">
                    </select>
                    <span class="error-msg" id="error-party" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="lr_number">LR Number</label>
                    <input type="text" name="lr_number" class="form-control" id="lr_number" placeholder="LR Number" />
                    <span class="info-msg" id="info-lr_number"></span>
                    <span class="error-msg" id="error-lr_number" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="container_number">Container Number</label>
                    <input type="text" name="container_number" class="form-control" id="container_number"
                        placeholder="Container Number" />
                    <span class="error-msg" id="error-container_number" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="vehicle_number">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" id="vehicle_number"
                        placeholder="Vehical Number" />
                    <span class="error-msg" id="error-vehicle_number" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" class="form-control" id="amount" placeholder="Amount" />
                    <span class="error-msg" id="error-amount" style="color: red"></span>
                </div>

                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset Details" class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save Details" class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>


    {{-- for add new party  --}}
    <div class="modal fade" id="partyFormModal" tabindex="-1" role="dialog" aria-labelledby="partyFormModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="partyFormModalTitle">Add New Party</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="partyform">
                        @csrf
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" placeholder="token" required />
                                    <input type="hidden" value="{{ session('user_id') }}" class="form-control"
                                        name="user_id">
                                    <input type="hidden" value="{{ session('company_id') }}" class="form-control"
                                        name="company_id">

                                    <label for="modal_firstname">FirstName</label><span class="partywithoutgstspan"
                                        style="color:red;">*</span>
                                    <input type="text" id="modal_firstname" class="form-control partywithoutgstinput"
                                        name='firstname' placeholder="First Name" required>
                                    <span class="modal_error-msg" id="modal_error-firstname" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_lastname">LastName</label>
                                    <input type="text" id="modal_lastname" class="form-control requiredinput"
                                        name='lastname' placeholder="Last Name">
                                    <span class="modal_error-msg" id="modal_error-lastname" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_company_name">Company Name</label>
                                    <span class="partywithgstspan" style="color:red;">*</span>
                                    <input type="text" id="modal_company_name" class="form-control partywithgstinput"
                                        name='company_name' id="" placeholder="Company Name">
                                    <span class="modal_error-msg" id="modal_error-company_name"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_gst_number">GST Number</label>
                                    <input type="text" id="modal_gst_number" class="form-control" name='gst_number'
                                        id="" placeholder="GST Number">
                                    <span class="modal_error-msg" id="modal_error-gst_number" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_pan_number">PAN Number</label>
                                    <input type="text" id="modal_pan_number" class="form-control" name='pan_number'
                                        id="" placeholder="PAN Number">
                                    <span class="modal_error-msg" id="modal_error-pan_number" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_email">Email</label>
                                    <input type="email" class="form-control requiredinput" name="email"
                                        id="modal_email" placeholder="Enter Email">
                                    <span class="modal_error-msg" id="modal_error-email" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_contact_number">Contact Number</label>
                                    <input type="tel" class="form-control requiredinput" name='contact_number'
                                        id="modal_contact_number" placeholder="0123456789">
                                    <span class="modal_error-msg" id="modal_error-contact_number"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_country">Select Country</label>
                                    <select class="form-control requiredinput" name='country' id="modal_country">
                                        <option selected="" disabled="">Select your Country</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-country" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_state">Select State</label>
                                    <select class="form-control requiredinput" name='state' id="modal_state">
                                        <option selected="" disabled="">Select your State</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-state" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_city">Select City</label>
                                    <select class="form-control requiredinput" name='city' id="modal_city">
                                        <option selected="" disabled="">Select your City</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-city" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_pincode">Pincode</label>
                                    <input type="text" id="modal_pincode" name='pincode'
                                        class="form-control requiredinput" placeholder="Pin Code">
                                    <span class="modal_error-msg" id="modal_error-pincode" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_house_no_building_name">House no./ Building Name</label>
                                    <textarea class="form-control requiredinput" name='house_no_building_name' id="modal_house_no_building_name"
                                        rows="2" placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                    <span class="modal_error-msg" id="modal_error-house_no_building_name"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_road_name_area_colony">Road Name/Area/Colony</label>
                                    <textarea class="form-control requiredinput" name='road_name_area_colony' id="modal_road_name_area_colony"
                                        rows="2" placeholder="e.g. sardar patel road, jagatpur"></textarea>
                                    <span class="modal_error-msg" id="modal_error-road_name_area_colony"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-12">
                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Cancel" id="party_cancelBtn"
                                        class="btn btn-secondary float-right">Cancel</button>
                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Reset Party Details"
                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Save Party Details"
                                        class="btn btn-primary float-right my-0">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
            loaderhide();

            const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD format
            $('#bill_date').val(today);

            const API_TOKEN = "{{ session()->get('api_token') }}";
            const COMPANY_ID = "{{ session()->get('company_id') }}";
            const USER_ID = "{{ session()->get('user_id') }}";

            $('.partywithgstinput').on('change keyup', function() {
                console.info('yes');
                var val = $(this).val();
                if (val != '') {
                    $('.partywithgstspan').show();
                    $('.partywithoutgstspan').hide();
                    $('.partywithgstinput').attr('required', true);
                    $('.partywithoutgstinput').removeAttr('required');
                } else {
                    $('.partywithgstspan').hide();
                    $('.partywithoutgstspan').show();
                    $('.partywithoutgstinput').attr('required', true);
                    $('.partywithgstinput').removeAttr('required');
                }
            });

            function ajaxRequest(type, url, data) {
                return $.ajax({
                    type,
                    url,
                    data
                });
            }

            function handleAjaxError(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    let firstErrorElement = null;

                    $.each(errors, function(key, value) {
                        let errorElement = $('#error-' + key);
                        errorElement.text(value[0]);

                        // Capture the first error element
                        if (!firstErrorElement) {
                            firstErrorElement = errorElement;
                        }
                    });

                    if (firstErrorElement) {
                        $('html, body').animate({
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

            // party data fetch and set party dropdown
            function partys(partyid = '') {
                loadershow();
                $('#party').html(`
                   <option value="add_party" > Add New Party </option>
                `);

                ajaxRequest('GET', "{{ route('billingparty.getpartylist') }}", {
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    if (response.status == 200 && response.party != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.party, function(key, value) {
                            const partyDetails = [value.firstname, value.lastname, value
                                .company_name, value.contact_no
                            ].filter(Boolean).join(' - ');

                            if (value.is_active == 1) {
                                $('#party').append(
                                    `<option value='${value.id}'>${partyDetails}</option>`
                                )
                            }
                        });
                        $('#party').val(partyid);
                        $('#party').select2({
                            search: true,
                            placeholder: 'Select a Party'
                        }); // search bar in party list
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#party').val(partyid);
                        $('#party').select2({
                            search: true,
                            placeholder: 'Select a Party'
                        }); // search bar in party list
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });


            };

            partys();

            $('#party').on('change', function() {
                loadershow();
                var selectedOption = $(this).find('option:selected');
                var partyid = $(this).val();
                if (partyid == 'add_party') {
                    $('#partyFormModal').modal('show');
                }
                loaderhide();
            });

            // close pop up modal and reset new party form
            $('#party_cancelBtn').on('click', function() {
                $('#partyform')[0].reset();
                $('#partyFormModal').modal('hide');
                $('#party').val('').trigger('change');
            })

            // redirect on transporter billing list page onclick cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.transporterbilling') }}";
            });


            $('#lr_number').on('focusout', function() {
                loadershow();
                let element = $(this);
                let lrnumber = element.val();

                if (lrnumber != '') {
                    url = "{{ route('consignorcopy.getbynumber', '__number__') }}".replace('__number__',
                        lrnumber);
                    ajaxRequest('GET', url, {
                        token: API_TOKEN,
                        company_id: COMPANY_ID,
                        user_id: USER_ID,
                    }).done(function(response) {
                        if (response.status == 200 && response.consignorcopy != '') {
                            let containerNo = response.consignorcopy.container_no;
                            let vehicalNo = response.consignorcopy.truck_number;
                            $('#container_number').val(containerNo);
                            $('#vehical_number').val(vehicalNo);
                            $('#info-lr_number').text('LR Number matched with records.').css(
                                'color', 'green');
                        } else {
                            $('#container_number').val('');
                            $('#vehical_number').val('');
                            $('#info-lr_number').text('LR Number not exits!').css('color',
                                '#00cfde');
                        }
                        loaderhide();
                    }).fail(function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    });
                } else {
                    loaderhide()
                    $('#info-lr_number').text('').css('color', 'green');
                }
            });

            // submit transporter billing form data
            $('#transporterbillingform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('transporterbill.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location =
                                "{{ route('admin.transporterbilling') }}"; // after succesfully data submit redirect on list page
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                })
            });


            $('#partyFormModal').on('show.bs.modal', function() {
                loadstate(0, 'partyform');
                loadcity(0, 'partyform');
            });

            $('#partyFormModal').on('hidden.bs.modal', function() {
                if ($('#party').val() == 'add_party') {
                    $('#party').val('').trigger('change');
                }
            });

            // for add new customer 

            // show country data in dropdown and set default value according logged in user
            ajaxRequest('GET', "{{ route('country.index') }}", {
                token: API_TOKEN,
            }).done(function(response) {
                if (response.status == 200 && response.country != '') {
                    // You can update your HTML with the data here if needed
                    $.each(response.country, function(key, value) {
                        $('#partyform #modal_country').append(
                            `<option value='${value.id}'> ${value.country_name}</option>`
                        )
                    });
                    country_id = "{{ session('user')['country_id'] }}";
                    $('#partyform #modal_country').val(country_id);
                } else {
                    $('#partyform #modal_country').append(`<option> No Data Found</option>`);
                }
                loaderhide();
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });

            // load state in dropdown when country change
            $(document).on('change', '#modal_country', function() {
                let parentform = $(this).closest('form').attr('id');
                loadershow();
                $(`#${parentform} #modal_city`).html(
                    `<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id, parentform);
            });

            // load state in dropdown and select state according to user
            function loadstate(id = 0, parentform = 'partyform') {
                $(`#${parentform} #modal_state`).html(`<option selected="" disabled="">Select your State</option>`);
                let stateSearchUrl = "{{ route('state.search', 'id') }}".replace('id', id);
                var url = stateSearchUrl;
                if (id == 0) {
                    url = "{{ route('state.search', session('user')['country_id']) }}";
                }
                ajaxRequest('GET', url, {
                    token: API_TOKEN,
                }).done(function(response) {
                    if (response.status == 200 && response.state != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.state, function(key, value) {
                            $(`#${parentform} #modal_state`).append(
                                `<option value='${value.id}'> ${value.state_name}</option>`
                            )
                        });
                        if (id == 0) {
                            state_id = "{{ session('user')['state_id'] }}";
                            $(`#${parentform} #modal_state`).val(state_id);
                        }
                    } else {
                        $(`#${parentform} #modal_state`).append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }

            // load city in dropdown when state select/change
            $(document).on('change', '#modal_state', function() {
                parentform = $(this).closest('form').attr('id');
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id, parentform);
            });

            function loadcity(id = 0, parentform = 'partyform') {
                $(`#${parentform} #modal_city`).html(`<option selected="" disabled="">Select your City</option>`);
                citySearchUrl = "{{ route('city.search', 'id') }}".replace('id', id);
                url = citySearchUrl;
                if (id == 0) {
                    url = "{{ route('city.search', session('user')['state_id']) }}";
                }

                ajaxRequest('GET', url, {
                    token: API_TOKEN,
                }).done(function(response) {
                    if (response.status == 200 && response.city != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.city, function(key, value) {
                            $(`#${parentform} #modal_city`).append(
                                `<option value='${value.id}'> ${value.city_name}</option>`
                            )
                        });
                        if (id == 0) {
                            $(`#${parentform} #modal_city`).val(
                                "{{ session('user')['city_id'] }}");
                        }
                    } else {
                        $(`#${parentform} #modal_city`).append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }

            // submit new party  form
            $('#partyform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.modal_error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('billingparty.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#partyform')[0].reset();
                            $('#partyFormModal').modal('hide');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            partys(response.party_id);
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorcontainer;
                            $.each(errors, function(key, value) {
                                $('#partyform #modal_error-' + key).text(value[0]);
                                errorcontainer = '#partyform #modal_error-' + key;
                            });
                            $('.modal-body #partyform').animate({
                                scrollTop: $(errorcontainer).position().top
                            }, 1000);
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush
