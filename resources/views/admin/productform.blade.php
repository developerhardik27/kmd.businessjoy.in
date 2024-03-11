@extends('admin.masterlayout')
@section('page_title')
{{ config('app.name') }} - Add New Product
@endsection
@section('title')
    New Product
@endsection


@section('form-content')
    <form id="productform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                    placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="user_id"
                        placeholder="user_id">
                    <input type="hidden" value="{{ $company_id }}" class="form-control" name="company_id"
                        placeholder="company_id">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name='name' class="form-control" placeholder="product name">
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="product_code">Product code</label>
                    <input type="text" id="product_code" name='product_code' class="form-control"
                        placeholder="product code">
                        <span class="error-msg" id="error-product_code" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="Unit">Unit</label>
                    <input type="text" name='unit' class="form-control" id="Unit" value=""
                        placeholder="enter Unit">
                        <span class="error-msg" id="error-unit" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="price_per_unit">Price</label>
                    <input type="text" id="price_per_unit" name='price_per_unit' class="form-control"
                        placeholder="Price per Unit">
                        <span class="error-msg" id="error-price_per_unit" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="description">Description</label>
                    <textarea class="form-control" name='description' id="description" rows="2"></textarea>
                    <span class="error-msg" id="error-description" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
            <div id="loader" class="loader"></div>
            <button id="resetbtn" type="reset" class="btn iq-bg-danger">Reset</button>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            loaderhide();
            // submit form
            $('#productform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('product.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.product') }}";

                        } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                loaderhide();
                                toastr.error('something went wrong !');
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
