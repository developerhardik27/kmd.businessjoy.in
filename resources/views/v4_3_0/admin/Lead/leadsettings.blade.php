@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')

@section('page_title')
    {{ config('app.name') }} - Lead Settings
@endsection
@section('title')
    Lead Settings
@endsection


@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            <div class="row">
                @if (session('user_permissions.leadmodule.leadsettings.edit') == 1)
                    <div class="col-12">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Lead Settings"
                            type="button" id="editleadsettings"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Lead Settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="leadsettingsform" style="display: none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-4 mb-2">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" placeholder="token" required />
                                                <input type="hidden" value="{{ session('user_id') }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ session('company_id') }}" name="company_id"
                                                    class="form-control">
                                                <label for="country_field">Country Field</label>
                                                <select name="country_field" id="country_field" class="form-control">
                                                    <option value="1">Show</option>
                                                    <option value="0">Hide</option>
                                                </select>
                                                <span class="error-msg" id="error-country_field" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-4 mb-2">
                                                <label for="state_field">State Field</label>
                                                <select name="state_field" id="state_field" class="form-control">
                                                    <option value="1">Show</option>
                                                    <option value="0">Hide</option>
                                                </select>
                                                <span class="error-msg" id="error-state_field" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-4 mb-2">
                                                <label for="city_field">City Field</label>
                                                <select name="city_field" id="city_field" class="form-control">
                                                    <option value="1">Show</option>
                                                    <option value="0">Hide</option>
                                                </select>
                                                <span class="error-msg" id="error-city_field" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-4 mb-2">
                                                <label for="autofill_value">Auto-Fill Value</label>
                                                <select name="autofill_value" id="autofill_value" class="form-control">
                                                    <option value="Blank">Blank</option>
                                                    <option value="As Per User">As Per User</option>
                                                    <option value="As Per Company">As Per Company</option>
                                                    <option value="Custom">Custom</option>
                                                </select>
                                                <span class="error-msg" id="error-autofill_value" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-4 mb-2 customValueField" style="display: none">
                                                <label for="country">Custom Country</label>
                                                <select name="custom_country" id="country" class="form-control">
                                                </select>
                                                <span class="error-msg" id="error-country" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-4 mb-2 customValueField" style="display: none">
                                                <label for="state">Custom State</label>
                                                <select name="custom_state" id="state" class="form-control">
                                                </select>
                                                <span class="error-msg" id="error-state" style="color: red"></span>
                                            </div>

                                            <div class="col-sm-4 mb-2 customValueField" style="display: none">
                                                <label for="city">Custom City</label>
                                                <select name="custom_city" id="city" class="form-control">
                                                </select>
                                                <span class="error-msg" id="error-city" style="color: red"></span>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-sm-12 mt-2">
                                                    <button type="button" data-toggle="tooltip"
                                                        id="leadsettings-cancelbtn" data-placement="bottom"
                                                        data-original-title="Cancel"
                                                        class="btn btn-secondary float-right">Cancel</button>
                                                    <button type="reset" id="resetBtn" data-toggle="tooltip"
                                                        data-placement="bottom" data-original-title="Reset Settings"
                                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Update Settings"
                                                        class="btn btn-primary float-right my-0">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </form>
                                <p><b>Country Field : </b><span id="countryField"></span></p>
                                <p><b>State Field : </b><span id="stateField"></span></p>
                                <p><b>City Field : </b><span id="cityField"></span></p>
                                <p><b>Auto-fill Value : </b><span id="autoFillValue"></span></p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {
            loaderhide();

            $('#resetBtn').on('click', function() {
                $('.customValueField').hide();
            });

            // show country data in dropdown and set default value according logged in user
            $.ajax({
                type: 'GET',
                url: "{{ route('country.index') }}",
                data: {
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {

                    if (response.status == 200 && response.country != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.country, function(key, value) {
                            $('#country').append(
                                `<option value='${value.id}'> ${value.country_name}</option>`
                            )
                        });
                    } else {
                        $('#country').append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                },
                error: function(xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr
                        .responseText); // Log the full error response for debugging
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
            });

            // load state in dropdown and select state according to user
            function loadstate(id = 0, customValue = null) {
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                let stateSearchUrl = "{{ route('state.search', '__countryId__') }}".replace('__countryId__', id);
                var url = stateSearchUrl;
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.state != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.state, function(key, value) {
                                $('#state').append(
                                    `<option value='${value.id}'> ${value.state_name}</option>`
                                )
                            });
                            if (customValue) {
                                $('#state').val(customValue);
                            }
                        } else {
                            $('#state').append(`<option> No Data Found</option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
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
                });
            }

            // load city in dropdown and select state according to user
            function loadcity(id = 0, customValue = null) {
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                let citySearchUrl = "{{ route('city.search', '__stateId__') }}".replace('__stateId__', id);
                url = citySearchUrl;
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.city != '') {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                            if (customValue) {
                                $('#city').val(customValue);
                            }
                        } else {
                            $('#city').append(`<option> No Data Found</option>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
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
                });
            }

            // load state in dropdown when country change
            $('#country').on('change', function() {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id);
            });

            // load city in dropdown when state select/change
            $('#state').on('change', function() {
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id);
            });

            $('#editleadsettings').on('click', function() {
                $(this).toggle();
                $('#leadsettingsform').toggle();
            });

            $('#leadsettings-cancelbtn').on('click', function() {
                $('#editleadsettings').toggle(); 
                $('#leadsettingsform').toggle();
            });


            $('#autofill_value').on('change', function() {
                let autofill_value = $(this).val();
                if (autofill_value == 'Custom') {
                    $('.customValueField').show();
                } else {
                    $('.customValueField').hide();
                }
            });

            function loaddata() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('lead.settings') }}",
                    data: {
                        token: "{{ session('api_token') }}",
                        company_id: "{{ session('company_id') }}",
                        user_id: "{{ session('user_id') }}"
                    },
                    success: function(res) {
                        if (res.status === 200) {
                            const s = res.leadsettings;
                            $('#country_field').val(s.country);
                            $('#state_field').val(s.state);
                            $('#city_field').val(s.city);
                            $('#autofill_value').val(s.autofill_value);


                            if (s.autofill_value != 'Custom') {
                                $('.customValueField').val('');
                                $('.customValueField').hide();
                            } else {
                                $('.customValueField').show();
                                $('#country').val(s.country_default_value);
                                loadstate(s.country_default_value, s.state_default_value);
                                loadcity(s.state_default_value, s.city_default_value);
                            }


                            // Update the summary section
                            $('#countryField').text(s.country == 1 ? 'Show' : 'Hide');
                            $('#stateField').text(s.state == 1 ? 'Show' : 'Hide');
                            $('#cityField').text(s.city == 1 ? 'Show' : 'Hide');
                            $('#autoFillValue').text(s.autofill_value);


                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: res.message || 'No settings found'
                            });
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Failed to fetch lead settings'
                        });
                    }
                });
            }

            loaddata();

            $('#leadsettingsform').submit(function(e) {
                e.preventDefault();
                const autofillValue = $('#autofill_value').val();
                const countryField = $('#country_field').val();
                const stateField = $('#state_field').val();
                const cityField = $('#city_field').val();

                // Rule 1: If autofill is Blank and any field is hidden, block submission
                if (autofillValue === 'Blank' && (countryField == '0' || stateField == '0' || cityField ==
                        '0')) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Autofill cannot be Blank if Country, State, or City field is hidden.'
                    });
                    return;
                }

                // Rule 2: If autofill is Custom, ensure country, state, and city are selected
                if (autofillValue === 'Custom') {
                    const customCountry = $('#country').val();
                    const customState = $('#state').val();
                    const customCity = $('#city').val();

                    if (!customCountry || !customState || !customCity) {
                        Toast.fire({
                            icon: 'warning',
                            title: 'Please select Country, State, and City for Custom autofill.'
                        });
                        return;
                    }
                }

                loadershow(); // Show loading animation
                $('.error-msg').text(''); // Clear previous errors

                const formData = $(this).serializeArray();

                $.ajax({
                    type: 'put',
                    url: "{{ route('lead.updatesettings') }}",
                    data: formData,
                    success: function(res) {
                        loaderhide();
                        if (res.status === 200) {
                            Toast.fire({
                                icon: 'success',
                                title: res.message || 'Settings updated successfully!'
                            });

                            $('#leadsettingsform').hide();
                            $('#editleadsettings').show();

                            // Optionally refresh shown values:
                            loaddata();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: res.message || 'Update failed.'
                            });
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            Toast.fire({
                                icon: 'warning',
                                title: 'Please fix the highlighted errors.'
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Unexpected error occurred.'
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush
