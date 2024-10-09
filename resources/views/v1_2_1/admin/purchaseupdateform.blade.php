@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Update Purchase
@endsection
@section('title')
    Update Purchase
@endsection


@section('form-content')
    <form id="purchaseupdateform" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" name="user_id" class="form-control">
                    <input type="hidden" value="{{ session('company_id') }}" name="company_id" class="form-control">
                    <label for="name">Name</label>
                    <input type="text" id="name" name='name' class="form-control" placeholder="Name" required />
                    <span class="error-msg" id="error-name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="description">Description</label>
                    <textarea class="form-control" required name='description' id="description" rows="1"></textarea>
                    <span class="error-msg" id="error-description" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="amount">Amount</label>
                    <input type="text" name='amount' class="form-control" id="amount" placeholder="Amount"
                        required />
                    <span class="error-msg" id="error-amount" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="amount_type">Select Amount Type</label>
                    <select id="amount_type" class="form-control" name='amount_type' required>
                        <option selected="" disabled="">Select Amount Type</option>
                        <option value="gst">GST</option>
                        <option value="without_gst">Without GST</option>
                    </select>
                    <span class="error-msg" id="error-amount_type" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="date">Date</label>
                    <input type="date" name='date' id="date" class="form-control" required />
                    <span class="error-msg" id="error-date" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="img">Image</label><br>
                    <input type="file" name="img" id="img" width="100%" />
                    <span class="error-msg" id="error-img" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="reset" data-toggle="tooltip" data-placement="bottom" data-original-title="Reset"
                        class="btn iq-bg-danger float-right"><i class="ri-refresh-line"></i></button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Update"
                        class="btn btn-primary float-right my-0"><i class="ri-check-line"></i></button>
                </div>
            </div>
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
            var edit_id = @json($edit_id);
            let purchaseSearchUrl = "{{route('purchase.search','__editId__')}}".replace('__editId__',edit_id);
            // show old data in fields
            $.ajax({
                type: 'GET',
                url: purchaseSearchUrl,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    user_id: "{{ session()->get('user_id') }}",
                },
                success: function(response) {
                    if (response.status == 200 && response.purchases != '') {

                        // You can update your HTML with the data here if needed
                        $('#name').val(response.purchases.name);
                        $('#description').val(response.purchases.description);
                        $('#amount').val(response.purchases.amount);
                        $('#amount_type').val(response.purchases.amount_type);
                        $('#date').val(response.purchases.date);
                        company = response.purchases.company_id;
                    } else if (response.status == 500) {
                        toastr.error(response.message);
                    } else {
                        toastr.error('Something went wrong');
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
                    toastr.error(errorMessage);
                }
            });
 
            //submit form
            $('#purchaseupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                var formData = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('purchase.update', $edit_id) }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.purchase') }}";
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            toastr.error(errorMessage);
                        }
                    }
                });
            });
        });
    </script>
@endpush
