@extends('admin.masterlayout')

@section('page_title')
    Add New Bank Details
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
                    <label for="">Holder Name</label>
                    <input id="name" type="text" name="holder_name" class="form-control" placeholder="Holder Name"
                        required />
                    <span class="error-msg" id="error-holder_name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="exampleInputEmail3">Account Number</label>
                    <input type="text" name="account_number" class="form-control" id="exampleInputEmail3" value=""
                        placeholder="Account Number" required />
                    <span class="error-msg" id="error-account_number" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="exampleInputphone">Swift Code</label>
                    <input type="text" name="swift_code" class="form-control" id="exampleInputphone" value=""
                        placeholder="Swift Code" required />
                    <span class="error-msg" id="error-swift_code" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="">IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control" placeholder="IFSC Code" required />
                    <span class="error-msg" id="error-ifsc_code" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="">Branch Name</label>
                    <input type="text" name="branch_name" class="form-control" placeholder="Branch Name" required />
                    <span class="error-msg" id="error-branch_name" style="color: red"></span>
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
        
            loaderhide();
         
            @isset($message)
                alert('you have not any bank account so first add bank account');
            @endisset


            // submit form data
            $('#bankdetailform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
               loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('bank.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.bank') }}";

                        }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            toastr.error(response.message);
                           loaderhide();

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
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                            loaderhide();
                        }
                    }
                })
            });
        });
    </script>
@endpush
