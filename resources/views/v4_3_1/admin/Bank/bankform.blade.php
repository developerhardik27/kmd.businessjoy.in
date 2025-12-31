@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

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
                <div class="col-sm-6 mb-2">
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
                <div class="col-sm-6 mb-2">
                    <label for="account_number">Account Number</label><span style="color:red;">*</span>
                    <input type="text" name="account_number" class="form-control" id="account_number" value=""
                        placeholder="Account Number" required />
                    <span class="error-msg" id="error-account_number" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="swift_code">Swift Code</label>
                    <input type="text" name="swift_code" class="form-control" id="swift_code" value=""
                        placeholder="Swift Code" />
                    <span class="error-msg" id="error-swift_code" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="ifsc_code">IFSC Code</label><span style="color:red;">*</span>
                    <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" placeholder="IFSC Code"
                        required />
                    <span class="error-msg" id="error-ifsc_code" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="bank_name">Bank Name</label><span style="color:red;">*</span>
                    <input type="text" id="bank_name" name="bank_name" class="form-control" placeholder="Bank Name"
                        required />
                    <span class="error-msg" id="error-bank_name" style="color: red"></span>
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="branch_name">Branch Name</label>
                    <input type="text" id="branch_name" name="branch_name" class="form-control"
                        placeholder="Branch Name" />
                    <span class="error-msg" id="error-branch_name" style="color: red"></span>
                </div>
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom" data-original-title="Reset Details"
                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Save Details"
                        class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>
@endsection


@push('ajax')
    @isset($message)
        <script>
            $('document').ready(function() {
                // if  company has not any bank account so user will be redirect here when he click on create invoice link
                alert('You have not any bank account. Please first add bank account!');
            });
        </script>
    @endisset
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            loaderhide();

            // redirect on bank list page onclick cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.bank') }}";
            });

            // submit bank form data
            $('#bankdetailform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                loadershow();
                const formdata = $(this).serialize();

                ajaxRequest('POST', "{{ route('bank.store') }}", formdata).done(function(response) {
                    // Handle the response from the server
                    if (response.status == 200) {
                        // You can perform additional actions, such as showing a success message or redirecting the user
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        window.location =
                            "{{ route('admin.bank') }}"; // after succesfully data submit redirect on list page
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });;
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            });
        });
    </script>
@endpush
