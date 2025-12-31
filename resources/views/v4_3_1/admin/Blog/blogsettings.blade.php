@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')

@section('page_title')
    {{ config('app.name') }} - Blog Settings
@endsection
@section('title')
    Blog Settings
@endsection
@section('style')
    <style>

    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            <div class="row">
                @if (session('user_permissions.blogmodule.blogsettings.edit') == 1)
                    <div class="col-12">
                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Blog Settings"
                            type="button" id="editblogsettings"
                            class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                            <i class="ri-edit-fill"></i>
                        </button>
                        <div class="iq-card">
                            <div class="iq-card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Blog Settings</h4>
                                </div>
                            </div>
                            <div class="iq-card-body">
                                <form id="blogsettingsform" style="display: none">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-sm-6">
                                                <input type="hidden" name="token" class="form-control"
                                                    value="{{ session('api_token') }}" placeholder="token" required />
                                                <input type="hidden" value="{{ session('user_id') }}" name="user_id"
                                                    class="form-control">
                                                <input type="hidden" value="{{ session('company_id') }}" name="company_id"
                                                    class="form-control">
                                                <label class="form-label" for="blog_details_endpoint">Blog Details
                                                    Endpoint</label>
                                                <input type="text" id="blog_details_endpoint"
                                                    name='blog_details_endpoint' class="form-control"
                                                    placeholder="e.g. https://abc.com/xyz/" />
                                                <span class="error-msg" id="error-blog_details_endpoint"
                                                    style="color: red"></span><br>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="blog_image_allowed_filetype">Blog Image Allowed Filetype</label>
                                                <input type="text" name="blog_image_allowed_filetype"
                                                    id="blog_image_allowed_filetype" class="form-control"
                                                    placeholder="e.g. jpeg,jpg,png" />
                                                <span class="error-msg" id="error-blog_image_allowed_filetype"
                                                    style="color: red"></span><br>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="blog_image_max_size">Blog Image Max Size (MB)</label>
                                                <input type="number" name="blog_image_max_size" class="form-control"
                                                    id="blog_image_max_size" step="0.1" min="1" />
                                                <span class="error-msg" id="error-blog_image_max_size"
                                                    style="color: red"></span><br>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="blog_image_width">Blog Image Width (px)</label>
                                                <input type="number" name="blog_image_width" class="form-control"
                                                    id="blog_image_width" min="50" />
                                                <span class="error-msg" id="error-blog_image_width"
                                                    style="color: red"></span><br>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="blog_image_height">Blog Image Height (px)</label>
                                                <input type="number" name="blog_image_height" class="form-control"
                                                    id="blog_image_height" min="50" />
                                                <span class="error-msg" id="error-blog_image_height"
                                                    style="color: red"></span><br>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="blog_thumbnail_image_width">Blog Thumbnail Image Width
                                                    (px)</label>
                                                <input type="number" name="blog_thumbnail_image_width" class="form-control"
                                                    id="blog_thumbnail_image_width" min="50" />
                                                <span class="error-msg" id="error-blog_thumbnail_image_width"
                                                    style="color: red"></span><br>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="blog_thumbnail_image_height">Blog Thumbnail Image Height
                                                    (px)</label>
                                                <input type="number" name="blog_thumbnail_image_height"
                                                    class="form-control" id="blog_thumbnail_image_height"
                                                    min="50" />
                                                <span class="error-msg" id="error-blog_thumbnail_image_height"
                                                    style="color: red"></span><br>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="validate_dimenstion">Validate Dimension</label>
                                                <select name="validate_dimenstion" class="form-control">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                                <span class="error-msg" id="error-validate_dimenstion"
                                                    style="color: red"></span><br>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-row">
                                                <div class="col-sm-12 mt-2">
                                                    <button type="button" data-toggle="tooltip"
                                                        id="blogsettings-cancelbtn" data-placement="bottom"
                                                        data-original-title="Cancel"
                                                        class="btn btn-secondary float-right">Cancel</button>
                                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Reset Settings"
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
                                <b>Blog Details Endpoint : </b><span id="DetailsEndpoint"></span> <br>
                                <b>Blog Image Allowed Filetype : </b><span id="imgAllowedFiletype"></span> <br>
                                <b>Blog Image Max Size(MB) : </b><span id="imgMaxSize"></span> <br>
                                <b>Blog Image Width(PX) : </b><span id="imgWidth"></span> <br>
                                <b>Blog Image Height(PX) : </b><span id="imgHeight"></span> <br>
                                <b>Blog Thumbnail Image Width(PX) : </b><span id="thumbnailImgWidth"></span> <br>
                                <b>Blog Thumbnail Image Height(PX) : </b><span id="thumbnailImgHeight"></span> <br>
                                <b>Validate Dimension : </b><span id="validateDimension"></span> <br>
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

            $('#editblogsettings').on('click', function() {
                $(this).toggle();
                $('#blogsettingsform').toggle();
            });


            $('#blogsettings-cancelbtn').on('click', function() {
                $('#editblogsettings').toggle();
                $('#blogsettingsform')[0].reset();
                $('#blogsettingsform').toggle();
            });

            function loaddata() {
                loadershow();
                ajaxRequest('GET', "{{ route('blog.settings') }}", {
                    token: "{{ session()->get('api_token') }}",
                    company_id: {{ session()->get('company_id') }},
                    user_id: {{ session()->get('user_id') }},
                }).done(function(res){
                    if (res.status === 200) {
                        const s = res.blogsettings;
                        $('#blog_details_endpoint').val(s.details_endpoint);
                        $('#blog_image_allowed_filetype').val(s.img_allowed_filetype);
                        $('#blog_image_max_size').val(s.img_max_size);
                        $('#blog_image_width').val(s.img_width);
                        $('#blog_image_height').val(s.img_height);
                        $('#blog_thumbnail_image_width').val(s.thumbnail_img_width);
                        $('#blog_thumbnail_image_height').val(s.thumbnail_img_height);
                        $('select[name="validate_dimenstion"]').val(s.validate_dimension);

                        // Update the summary section
                        $('#DetailsEndpoint').text(s.details_endpoint);
                        $('#imgAllowedFiletype').text(s.img_allowed_filetype);
                        $('#imgMaxSize').text(s.img_max_size);
                        $('#imgWidth').text(s.img_width);
                        $('#imgHeight').text(s.img_height);
                        $('#thumbnailImgWidth').text(s.thumbnail_img_width);
                        $('#thumbnailImgHeight').text(s.thumbnail_img_height);
                        $('#validateDimension').text(s.validate_dimension == 1 ? 'Yes' : 'No');
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: res.message || 'No settings found'
                        });
                    }
                    loaderhide();
                }).fail(function(xhr){
                    loaderhide();
                    handleAjaxError(xhr);
                });  
            }

            loaddata();

            $('#blogsettingsform').submit(function(e) {
                e.preventDefault();
                loadershow(); // Show loading animation
                $('.error-msg').text(''); // Clear previous errors

                const formData = $(this).serializeArray();
                ajaxRequest('put', "{{ route('blog.updatesettings') }}", formData).done(function(res){
                    if (res.status === 200) {
                        Toast.fire({
                            icon: 'success',
                            title: res.message || 'Settings updated successfully!'
                        });

                        $('#blogsettingsform')[0].reset();
                        $('#blogsettingsform').hide();
                        $('#editblogsettings').show();

                        // Optionally refresh shown values:
                        loaddata();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: res.message || 'Update failed.'
                        });
                    }
                    loaderhide();
                }).fail(function(xhr){
                    loaderhide();
                    handleAjaxError(xhr);
                }); 
            });

        });
    </script>
@endpush
