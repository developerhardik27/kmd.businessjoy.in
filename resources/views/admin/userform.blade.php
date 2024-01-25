@extends('admin.masterlayout')
@section('page_title')
    Add New User
@endsection
@section('title')
    New User
@endsection


@section('form-content')
    <form id="userform" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" name="created_by" class="form-control">
                    <input type="hidden" value="{{ $company_id }}" name="company_id" class="form-control">
                    <label for="firstname">FirstName</label>
                    <input type="text" id="firstname" name='firstname' class="form-control" placeholder="First name"
                        required />
                    <span class="error-msg" id="error-firstname" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="lastname">LastName</label>
                    <input type="text" id="lastname" name='lastname' class="form-control" placeholder="Last name"
                        required />
                    <span class="error-msg" id="error-lastname" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="exampleInputEmail3">Email</label>
                    <input type="email" name='email' class="form-control" id="exampleInputEmail3" value=""
                        placeholder="Enter Email" required />
                    <span class="error-msg" id="error-email" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="exampleInputPassword3">Password</label>
                    <input type="password" name='password' class="form-control" id="exampleInputPassword3" value=""
                        placeholder="Enter Password" required />
                    <span class="error-msg" id="error-password" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="exampleInputphone">Contact Number</label>
                    <input type="tel" name='contact_number' class="form-control" id="exampleInputphone" value=""
                        placeholder="0123456789" required />
                    <span class="error-msg" id="error-contact_number" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="country">Select Country</label>
                    <select id="country" class="form-control" name='country' required>
                        <option selected="" disabled="">Select your Country</option>
                    </select>
                    <span class="error-msg" id="error-country" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="state">Select State</label>
                    <select class="form-control" name='state' id="state" required>
                        <option selected="" disabled="">Select your State</option>
                    </select>
                    <span class="error-msg" id="error-state" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="city">Select City</label>
                    <select class="form-control" name='city' id="city" required>
                        <option selected="" disabled="">Select your City</option>
                    </select>
                    <span class="error-msg" id="error-city" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="pincode">Pincode</label>
                    <input type="text" id="pincode" name='pincode' class="form-control" placeholder="Pin Code" required />
                    <span class="error-msg" id="error-pincode" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="img">Image</label><br>
                    <input type="file" name="img" id="img" width="100%" />
                    <span class="error-msg" id="error-img" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary submitBtn"  id="">Submit</button>
            <div id="loader" class="loader"></div>
            <button id="" type="reset" class="btn iq-bg-danger resetbtn">Reset</button>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Invoice Module</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:15% ;">Show/Hide</th>
                                    <th scope="col">Menus</th>
                                    <th scope="col">Add</th>
                                    <th scope="col">View</th>
                                    <th scope="col">Edit</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> <input type="checkbox" id="showinvoicemenu"  name="showinvoicemenu" value="1"></td>
                                    <td>Invoice</td>
                                    <td><input type="checkbox" id="addinvoice"  name="addinvoice" value="1"></td>
                                    <td><input type="checkbox" id="viewinvoice" name="viewinvoice" value="1"></td>
                                    <td><input type="checkbox" id="editinvoice" name="editinvoice" value="1"></td>
                                    <td><input type="checkbox" id="deleteinvoice" name="deleteinvoice" value="1"></td>
                                </tr>
                                <tr>
                                    <td> <input type="checkbox" id="showcompanymenu"  name="showcompanymenu" value="1"></td>
                                    <td>Company</td>
                                    <td><input type="checkbox" id="addcompany" name="addcompany" value="1"></td>
                                    <td><input type="checkbox" id="viewcompany" name="viewcompany" value="1"></td>
                                    <td><input type="checkbox" id="editcompany" name="editcompany" value="1"></td>
                                    <td><input type="checkbox" id="deletecompany" name="deletecompany" value="1"></td>
                                </tr>
                                <tr>
                                    <td> <input type="checkbox" id="showbankmenu" name="showbankmenu" value="1"></td>
                                    <td>Bank</td>
                                    <td><input type="checkbox" id="addbank" name="addbank" value="1"></td>
                                    <td><input type="checkbox" id="viewbank" name="viewbank" value="1"></td>
                                    <td><input type="checkbox" id="editbank" name="editbank" value="1"></td>
                                    <td><input type="checkbox" id="deletebank" name="deletebank" value="1"></td>
                                </tr>
                                <tr>
                                    <td> <input type="checkbox" id="showusermenu"  name="showusermenu" value="1"></td>
                                    <td>User</td>
                                    <td><input type="checkbox" id="adduser" name="adduser" value="1"></td>
                                    <td><input type="checkbox" id="viewuser" name="viewuser" value="1"></td>
                                    <td><input type="checkbox" id="edituser" name="edituser" value="1"></td>
                                    <td><input type="checkbox" id="deleteuser" name="deleteuser" value="1"></td>
                                </tr>
                                <tr>
                                    <td> <input type="checkbox" id="showcustomermenu"  name="showcustomermenu" value="1"></td>
                                    <td>Customer</td>
                                    <td><input type="checkbox" id="addcustomer" name="addcustomer" value="1"></td>
                                    <td><input type="checkbox" id="viewcustomer" name="viewcustomer" value="1"></td>
                                    <td><input type="checkbox" id="editcustomer" name="editcustomer" value="1"></td>
                                    <td><input type="checkbox" id="deletecustomer" name="deletecustomer" value="1"></td>
                                </tr>
                                <tr>
                                    <td> <input type="checkbox" id="showproductmenu" name="showproductmenu" value="1"></td>
                                    <td>Product</td>
                                    <td><input type="checkbox" id="addproduct" name="addproduct" value="1"></td>
                                    <td><input type="checkbox" id="viewproduct" name="viewproduct" value="1"></td>
                                    <td><input type="checkbox" id="editproduct" name="editproduct" value="1"></td>
                                    <td><input type="checkbox" id="deleteproduct" name="deleteproduct" value="1"></td>
                                </tr>
                                <tr>
                                    <td> <input type="checkbox" id="showpurchasemenu" name="showpurchasemenu" value="1"></td>
                                    <td>Purchase</td>
                                    <td><input type="checkbox" id="addpurchase" name="addpurchase" value="1"></td>
                                    <td><input type="checkbox" id="viewpurchase" name="viewpurchase" value="1"></td>
                                    <td><input type="checkbox" id="editpurchase" name="editpurchase" value="1"></td>
                                    <td><input type="checkbox" id="deletepurchase" name="deletepurchase" value="1"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="button-container">
                            <button type="submit" class="btn btn-primary submitBtn" id="">Submit</button>
                            <div id="loader" class="loader"></div>
                            <button id="" type="reset" class="btn iq-bg-danger resetbtn">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Lead Modules</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:15%">Show/Hide</th>
                                    <th scope="col">Menus</th>
                                    <th scope="col">Add</th>
                                    <th scope="col">View</th>
                                    <th scope="col">Edit</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <tbody >
                                <tr>
                                    <td> <input type="checkbox" id="showleadmenu" name="showleadmenu" value="1"></td>
                                    <td>Lead</td>
                                    <td><input type="checkbox" id="addlead" name="addlead" value="1"></td>
                                    <td><input type="checkbox" id="viewlead" name="viewlead" value="1"></td>
                                    <td><input type="checkbox" id="editlead" name="editlead" value="1"></td>
                                    <td><input type="checkbox" id="deletelead" name="deletelead" value="1"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="button-container">
                            <button type="submit" class="btn btn-primary submitBtn" id="">Submit</button>
                            <div id="loader" class="loader"></div>
                            <button id="" type="reset" class="btn iq-bg-danger resetbtn">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Customer Support Modules</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table class="table table-bordered table-responsive-sm w-100 text-center p-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:15%">Show/Hide</th>
                                    <th scope="col">Menus</th>
                                    <th scope="col">Add</th>
                                    <th scope="col">View</th>
                                    <th scope="col">Edit</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <tbody >
                                <tr>
                                    <td> <input type="checkbox" id="showcustomersupportmenu" name="showcustomersupportmenu" value="1"></td>
                                    <td>Customer Support</td>
                                    <td><input type="checkbox" id="addcustomersupport" name="addcustomersupport" value="1"></td>
                                    <td><input type="checkbox" id="viewcustomersupport" name="viewcustomersupport" value="1"></td>
                                    <td><input type="checkbox" id="editcustomersupport" name="editcustomersupport" value="1"></td>
                                    <td><input type="checkbox" id="deletecustomersupport" name="deletecustomersupport" value="1"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="button-container">
                            <button type="submit" class="btn btn-primary submitBtn" id="">Submit</button>
                            <button id="" type="reset" class="btn iq-bg-danger resetbtn">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // show country data in dropdown
            $.ajax({
                type: 'GET',
                url: '{{ route('country.index') }}',
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
                        loaderhide();
                    } else {
                        $('#country').append(`<option> No Data Found</option>`);
                        loaderhide();
                    }
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });


            // show state data when country select
            $('#country').on('change', function() {
                loadershow();
                var country_id = $(this).val();
                $('#state').html(`<option selected="" disabled="">Select your State</option>`);
                $.ajax({
                    type: 'GET',
                    url: "/api/state/search/" + country_id,
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
                            loaderhide();
                        } else {
                            $('#state').append(`<option disabled> No Data Found</option>`);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });

            // show city data when state select
            $('#state').on('change', function() {
                loadershow();
                $('#city').html(`<option selected="" disabled="">Select your City</option>`);
                var state_id = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: "/api/city/search/" + state_id,
                    data: {
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            // You can update your HTML with the data here if needed
                            $.each(response.city, function(key, value) {
                                $('#city').append(
                                    `<option value='${value.id}'> ${value.city_name}</option>`
                                )
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#city').append(`<option disabled> No city Found</option>`)
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            });

            //submit form
            $('#userform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('user.store') }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.user') }}";
                        } else if (response.status == 422) {
                            loaderhide();
                            toastr.error(response.errors);
                        } else {
                            loaderhide();
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                          loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            })
        });
    </script>
@endpush
