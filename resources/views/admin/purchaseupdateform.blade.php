@extends('admin.masterlayout')

@section('page_title')
    Update Purchase
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
                <input type="hidden" value="{{ $user_id }}" name="updated_by" class="form-control">
                <label for="name">Name</label>
                <input type="text" id="name" name='name' class="form-control" placeholder="Name"
                    required />
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
                <input type="text" name='amount' class="form-control" id="amount" value=""
                    placeholder="Amount" required />
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
                <input type="date" name='date' id="date" class="form-control"  required />
                <span class="error-msg" id="error-date" style="color: red"></span>
            </div>
            <div class="col-sm-6">
                <label for="">Image</label><br>
                <input type="file" name="img" id="" width="100%" />
                <span class="error-msg" id="error-img" style="color: red"></span>
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
            var edit_id = @json($edit_id);
            // show old data in fields
            $.ajax({
                type: 'GET',
                url: '/api/purchase/search/' + edit_id,
                data:{token: "{{ session()->get('api_token') }}"},
                success: function(response) {
                    if (response.status == 200 && response.purchases != '') {
                       
                        // You can update your HTML with the data here if needed
                        $('#name').val(response.purchases.name);
                        $('#description').val(response.purchases.description);
                        $('#amount').val(response.purchases.amount);
                        $('#amount_type').val(response.purchases.amount_type);
                        $('#date').val(response.purchases.date);
                        company = response.purchases.company_id;
                    } else {
                        alert('Something went wrong');
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

         
            //submit form
            $('#purchaseupdateform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                $('#submitBtn').hide();
                $('#resetbtn').hide();
                // Show the loader
                $("#loader").show();
                var formData =new FormData($(this)[0]);       
                $.ajax({
                    type: 'POST',
                    url: "{{ route('purchase.update', $edit_id) }}",
                    data:  formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    dataType:'json',
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.purchase') }}";
                        }  else {
                            toastr.error(response.message);
                            $('#submitBtn').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            $('#submitBtn').show();
                            $('#resetbtn').show();
                            // Show the loader
                            $("#loader").hide();
                        } else {
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            });
        });
    </script>
@endpush
