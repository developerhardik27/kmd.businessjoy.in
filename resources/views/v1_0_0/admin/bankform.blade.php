@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Add New Bank Details
@endsection
@section('title')
    New Bank Details
@endsection


@section('form-content')
    <form id="bankdetailform" name="bankdetailform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                        placeholder="user_id" required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" name="company_id" class="form-control" value="{{ $company_id }}"
                        placeholder="company_id" required />
                    <label for="name">Holder Name</label><span style="color:red;">*</span>
                    <input id="name" type="text" name="holder_name" class="form-control" placeholder="Holder Name"
                        required />
                    <span class="error-msg" id="error-holder_name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="account_number">Account Number</label><span style="color:red;">*</span>
                    <input type="text" name="account_number" class="form-control" id="account_number" value=""
                        placeholder="Account Number" required />
                    <span class="error-msg" id="error-account_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="swift_code">Swift Code</label><span style="color:red;">*</span>
                    <input type="text" name="swift_code" class="form-control" id="swift_code" value=""
                        placeholder="Swift Code" required />
                    <span class="error-msg" id="error-swift_code" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="ifsc_code">IFSC Code</label><span style="color:red;">*</span>
                    <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" placeholder="IFSC Code"
                        required />
                    <span class="error-msg" id="error-ifsc_code" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="branch_name">Branch Name</label><span style="color:red;">*</span>
                    <input type="text" id="branch_name" name="branch_name" class="form-control" placeholder="Branch Name"
                        required />
                    <span class="error-msg" id="error-branch_name" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                 <div class="col-sm-12">
                     <button type="reset" class="btn iq-bg-danger float-right">Reset</button>
                     <button type="submit" class="btn btn-primary float-right my-0" >Submit</button>
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
            loaderhide();

            // if  company has not any bank account so user will be redirect here when he click on create invoice link
            @isset($message)
                alert('You have not any bank account. Please first add bank account!');
            @endisset


            // submit form data
            $('#bankdetailform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'post',
                    url: "{{ route('bank.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.bank') }}";
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
                })
            });
        });
    </script>
@endpush
