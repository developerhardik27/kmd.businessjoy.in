@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Add New Blog
@endsection
@section('title')
    New Blog
@endsection


@section('form-content')
    <form id="blogform" name="blogform" enctype="multipart/form-data">
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
                    <label for="title">Title</label><span style="color:red;">*</span>
                    <input id="title" type="text" name="title" class="form-control" placeholder="Title"
                        required />
                    <span class="error-msg" id="error-title" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="slug">Slug</label><span style="color:red;">*</span>
                    <input type="text" name="slug" class="form-control" id="slug" value=""
                        placeholder="Slug" required />
                    <span class="error-msg" id="error-slug" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="meta_dsc">Meta Description</label> 
                    <textarea name="meta_dsc" placeholder="meta description" class="form-control" id="meta_dsc" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-meta_dsc" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="meta_keywords">Meta Keywords</label> 
                    <textarea name="meta_keywords" placeholder="meta keywords" class="form-control" id="meta_keywords" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-meta_keywords" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="category">Categories:</label> <span
                        style="color:red;">*</span><br />
                    <select name="category[]" class="form-control multiple" id="category" multiple>
                        <option value="" disabled selected>Select Category</option>
                    </select>
                    <span class="error-msg" id="error-category" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="tag">Tag:</label> <span
                    style="color:red;">*</span><br />
                <select name="tag[]" class="form-control multiple" id="tag" multiple>
                    <option value="" disabled selected>Select Tag</option>
                </select>
                <span class="error-msg" id="error-tag" style="color: red"></span>
                </div>
            </div>
        </div> 
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="content">Content</label> 
                    <textarea name="content" placeholder="Blog Content" class="form-control" id="content" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-content" style="color: red"></span>
                </div> 
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="blog_image">Image</label><br>
                    <input type="file" name="blog_image" id="blog_image" width="100%" />
                    <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that is smaller than 10 MB.
                    </p>
                    <span class="error-msg" id="error-blog_image" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                 <div class="col-sm-12">
                     <button type="reset" data-toggle="tooltip" data-placement="bottom" data-original-title="Reset" class="btn iq-bg-danger float-right"><i class='ri-refresh-line'></i></button>
                     <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Save Blog" class="btn btn-primary float-right my-0" ><i class='ri-check-line'></i></button>
                 </div>
            </div>
         </div>
    </form>
@endsection


@push('ajax')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            function getCategoryData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('blogcategory.index') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            function getTagData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('blogtag.index') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }



            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [categoryDataResponse,tagDataResponse] = await Promise.all(
                        [
                            getCategoryData(),
                            getTagData(),
                        ]);

                    // Check if Category data is successfully fetched
                    if (categoryDataResponse.status == 200 && categoryDataResponse.blogcategory != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(categoryDataResponse.blogcategory, function(key, value) {  
                            $('#category').append(
                                `<option value="${value.id}">${value.cat_name}</option>`);
                        });
                        $('#category').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (categoryDataResponse.status == 500) {
                        toastr.error(categoryDataResponse.message);
                    } else {
                        $('#category').append(`<option> No Blog Category Found </option>`);
                    }

                    // Check if Category data is successfully fetched
                    if (tagDataResponse.status == 200 && tagDataResponse.blogtag != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(tagDataResponse.blogtag, function(key, value) {  
                            $('#tag').append(
                                `<option value="${value.id}">${value.tag_name}</option>`);
                        });
                        $('#tag').multiselect(
                            'rebuild'); // Rebuild multiselect after appending options 
                    } else if (tagDataResponse.status == 500) {
                        toastr.error(tagDataResponse.message);
                    } else {
                        $('#tag').append(`<option> No Blog Tag Found </option>`);
                    }

                    loaderhide();

                    // Further code execution after successful AJAX calls and HTML appending


                } catch (error) {
                    console.error('Error:', error);
                    toastr.error("An error occurred while initializing");
                    loaderhide();
                }
            }

            initialize();
            $('#category').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });
            $('#tag').multiselect({
                enableFiltering: true,
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true
            });


            $('#category').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Category --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#category').multiselect('rebuild');
            });

            $('#tag').change(function() {
                if ($(this).val() !== null) {
                    $(this).find('option:disabled').remove(); // remove disabled option
                } else {
                    $(this).prepend(
                        '<option selected disabled>-- Select Tag --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#tag').multiselect('rebuild');
            });


            // submit form data
            $('#blogform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                loadershow();
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('blog.store') }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            window.location = "{{ route('admin.blog') }}";
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
