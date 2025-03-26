@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Blog
@endsection
@section('title')
    Update Blog
@endsection

@section('style')
    <style>
        select+.btn-group {
            border: 1px solid #ced4da;
            width: 100%;
            border-radius: 5px;
        }

        .dropdown-menu {
            width: 100%;
        }
    </style>
@endsection

@section('form-content')
    <form id="blogupdateform" name="blogupdateform" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="user_id" class="form-control" value="{{ $user_id }}" placeholder="user_id"
                        required />
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" name="company_id" class="form-control" value="{{ $company_id }}"
                        placeholder="company_id" required />
                    <label for="title">Title</label><span style="color:red;">*</span>
                    <input id="title" maxlength="100" type="text" name="title" class="form-control"
                        placeholder="Title" required />
                    <span class="error-msg" id="error-title" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="slug">Slug</label><span style="color:red;">*</span>
                    <input type="text" readonly maxlength="100" name="slug" class="form-control" id="slug"
                        value="" placeholder="Slug" required />
                    <span class="error-msg" id="error-slug" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="meta_dsc">Meta Description</label>
                    <textarea name="meta_dsc" maxlength="200" placeholder="meta description" class="form-control" id="meta_dsc"
                        cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-meta_dsc" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="meta_keywords">Meta Keywords</label>
                    <textarea name="meta_keywords" maxlength="200" placeholder="Enter comma-separated keywords e.g., abc,"
                        class="form-control" id="meta_keywords" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-meta_keywords" style="color: red"></span>
                    <p style="font-size: 0.9em; color: #666;">If provided, please enter keywords separated by commas.</p>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label class="form-label" for="category">Categories:</label> <span style="color:red;">*</span><br />
                    <select name="category[]" class="form-control multiple" id="category" multiple>
                        <option value="" disabled selected>-- Select Category --</option>
                    </select>
                    <span class="error-msg" id="error-category" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="tag">Tag:</label> <span style="color:red;">*</span><br />
                    <select name="tag[]" class="form-control multiple" id="tag" multiple>
                        <option value="" disabled selected>-- Select Tag --</option>
                    </select>
                    <span class="error-msg" id="error-tag" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="short_description">Short Description</label>
                    <textarea name="short_description" maxlength="250" placeholder="Blog Short Description" class="form-control"
                        id="short_description" cols="" rows="2"></textarea>
                    <span class="error-msg" id="error-short_description" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="content">Content</label>
                    <textarea name="content" placeholder="Blog Content" class="form-control" id="content" cols=""
                        rows="2"></textarea>
                    <span class="error-msg" id="error-content" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <label for="blog_image">Thumbnail Image</label><br>
                    <img src="" alt="" id="oldimg" width="100px">
                    <input type="file" accept=".jpg,.png,.jpeg" name="blog_image" id="blog_image" width="100%" />
                    <p class="text-primary">Please select a photo file (JPG, JPEG, or PNG) that is smaller than 10 MB and
                        has dimensions of 600 x 400 px.
                    </p>
                    <span class="error-msg" id="error-blog_image" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="reset" id="resetBtn" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset" class="btn iq-bg-danger float-right">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Save Blog"
                        class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('ajax')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

    <script>
        function validateMetaKeywords() {
            var metaKeywords = $('#meta_keywords').val().trim();
            var errorMsg = $('#error-meta_keywords');

            // Clear previous error message
            errorMsg.text("");

            // If the field is empty, skip validation (no error)
            if (metaKeywords === "") {
                return true; // Field is optional, so no validation needed if empty
            }

            // If there's only one keyword (no commas), it's allowed
            if (!metaKeywords.includes(",")) {
                return true;
            }

            // Regex explanation:
            // - Allows letters, numbers, hyphens, and underscores
            // - Allows single spaces between words and after commas
            // - Prevents multiple consecutive spaces anywhere
            // - Allows different spacing styles around commas
            var regex = /^[a-zA-Z0-9-_]+(?:\s[a-zA-Z0-9-_]+)*(?:\s?,\s?[a-zA-Z0-9-_]+(?:\s[a-zA-Z0-9-_]+)*)*$/;

            // Check for multiple consecutive spaces anywhere
            if (/\s{2,}/.test(metaKeywords)) {
                errorMsg.text("Multiple consecutive spaces are not allowed.");
                return false;
            }

            if (!regex.test(metaKeywords)) {
                errorMsg.text(
                    "Meta keywords must be comma-separated. A single space is allowed between words, but multiple consecutive spaces are not."
                );
                return false;
            }

            return true;
        }
    </script>
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            $('#content').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['table']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                placeholder: 'Add Content',
                tabsize: 2,
                height: 100
            });



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
                    const [categoryDataResponse, tagDataResponse] = await Promise.all(
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
                        Toast.fire({
                            icon: "error",
                            title: categoryDataResponse.message
                        });
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
                        Toast.fire({
                            icon: "error",
                            title: tagDataResponse.message
                        });
                    } else {
                        $('#tag').append(`<option> No Blog Tag Found </option>`);
                    }

                    // Further code execution after successful AJAX calls and HTML appending
                    await loaddata();


                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: "error",
                        title: "An error occurred while initializing"
                    });
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


            function loaddata() {
                var edit_id = @json($edit_id);
                // show old data in fields
                let blogEditUrl = "{{ route('blog.edit', '__editId__') }}".replace('__editId__', edit_id);
                $.ajax({
                    type: 'GET',
                    url: blogEditUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        user_id: " {{ session()->get('user_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            data = response.blog
                            // You can update your HTML with the data here if needed
                            $('#title').val(data.title);
                            $('#slug').val(data.slug);
                            $('#meta_dsc').val(data.meta_dsc);
                            $('#meta_keywords').val(data.meta_keywords);
                            $('#short_description').val(data.short_desc);
                            $('#content').summernote('code', data.content);

                            var imageUrl = '{{ asset('blog/') }}' + '/' + data.img;
                            $('#oldimg').attr('src', imageUrl);

                            $('#tag').find('option:disabled').remove(); // remove disabled option
                            tag = data.tag_ids;
                            tagarray = tag.split(',');
                            tagarray.forEach(function(value) {
                                $('#tag').multiselect('select', value);
                            });
                            $('#tag').multiselect('rebuild');

                            $('#category').find('option:disabled').remove(); // remove disabled option
                            category = data.cat_ids;
                            categoryarray = category.split(',');
                            categoryarray.forEach(function(value) {
                                $('#category').multiselect('select', value);
                            });
                            $('#category').multiselect('rebuild');
                        } else if (response.status == 500) {
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


            $('#title').on('change', function() {
                var edit_id = @json($edit_id);
                $('#slug').val('');
                $('#error-slug').text('');
                var element = $(this);
                $.ajax({
                    type: 'get',
                    url: "{{ route('blog.getslug') }}",
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}",
                        slug: element.val(),
                        edit_id: edit_id,
                    },
                    success: function(response) {
                        $('#slug').val(response.slug);
                        if (response.status == 422) {
                            $('#error-slug').text(response.message);
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
            });


            $('#blog_image').on('change', function(event) {
                var input = event.target;
                var input = event.target;

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#oldimg').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            });


            $('#resetBtn').on('click', function(e) {
                e.preventDefault();
                $(this).closest('form')[0].reset();

                if ($('#category').find('option:disabled').length == 0) {
                    $('#category').prepend(
                        '<option selected disabled>-- Select Category --</option>'
                    ); // prepend "Please choose an option"
                }

                $('#category').multiselect('rebuild');

                if ($('#tag').find('option:disabled').length == 0) {
                    $('#tag').prepend(
                        '<option selected disabled>-- Select Tag --</option>'
                    ); // prepend "Please choose an option"
                }
                $('#tag').multiselect('rebuild');

                $('#content').summernote('reset');
            });

            //submit form
            $('#blogupdateform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                // Validate the Meta Keywords field
                if (!validateMetaKeywords()) {
                    return false; // Stop form submission if validation fails
                }
                loadershow();
                var formdata = new FormData($(this)[0]);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('blog.update', $edit_id) }}",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.blog') }}";
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            })
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            })
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
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            })
        });
    </script>
@endpush
