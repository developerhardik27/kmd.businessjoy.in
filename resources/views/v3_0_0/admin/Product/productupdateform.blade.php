@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Product
@endsection
@section('title')
    Update Product
@endsection

@section('style')
    <style>
        /* Style for the tree container (mimicking select dropdown) */
        .tree-container {
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-family: Arial, sans-serif;
            cursor: pointer;
        }

        /* Style for each tree item */
        .tree-item {
            padding: 8px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            justify-content: space-between;
            /* Ensure text is left-aligned and arrow is right-aligned */
            align-items: center;
        }

        .tree-item:hover {
            background-color: #f1f1f1;
        }

        /* Style for collapsible items */
        .collapsible {
            display: none;
            padding-left: 15px;
        }

        .collapsible.open {
            display: block;
        }

        /* Arrow style for collapsible sections (right and down arrows) */
        .arrow {
            position: relative;
            left: 5%;
            transition: transform 0.3s;
            display: inline-block;
            width: 10px;
            height: 10px;
            border: solid #333;
            border-width: 0 2px 2px 0;
            padding: 3px;
        }

        .arrow.right {
            transform: rotate(-45deg);
            /* Right arrow */
        }

        .arrow.down {
            transform: rotate(45deg);
            /* Down arrow */
        }

        .selectedspan {
            font-weight: bold;
            color: #007bff;
        }

        /* Style for the selected category in the dropdown */
        .selected-category {
            padding: 10px;
            border: 1px solid #ccc;
            font-weight: bold;
            cursor: pointer;
            border-radius: 10px;
        }

        /* Hide the tree items initially (only show when dropdown is active) */
        .tree-container .tree-item {
            display: none;
        }

        /* Show tree items when the container is open */
        .tree-container.open .tree-item {
            display: block;
        }

        .dropzone .dz-preview .dz-image img {
            object-fit: cover;
            object-position: center;
            width: 100%;
            height: 100%;
        }
    </style>
@endsection

@section('form-content')
    <form id="productupdateform">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" placeholder="token" required />
                                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="user_id"
                                        placeholder="user_id">
                                    <input type="hidden" value="{{ session('company_id') }}" class="form-control"
                                        name="company_id" placeholder="company_id">
                                    <label for="name">Product Name</label><span style="color:red;">*</span>
                                    <input type="text" id="name" name='name' class="form-control"
                                        placeholder="product name">
                                    <span class="error-msg" id="error-name" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12">
                                    <label for="short_description">Short Description</label>
                                    <textarea class="form-control" name='short_description' id="short_description" rows="2"></textarea>
                                    <span class="error-msg" id="error-short_description" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12">
                                    <label for="description">Description</label>
                                    <textarea class="form-control summernote" name='description' id="description" rows="2"></textarea>
                                    <span class="error-msg" id="error-description" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <label for="category">Category</label><span style="color:red;">*</span>
                                    <div class="tree-container" id="category-dropdown">
                                        <div class="selected-category" data-id="null">Select a category</div>
                                    </div>
                                    <span class="error-msg" id="error-category" style="color: red"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-body">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <h5 class="card-title">Media</h5>
                                    <div class="dropzone dz-clickable" id="image">
                                        <div class="dz-message">
                                            <br>Drop files here or click to upload. <br><br>
                                        </div>
                                    </div>
                                    <span class="error-msg" id="error-image" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h5 class="card-title">Pricing</h5>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <label for="Unit">Unit</label>
                                    <input type="text" name='unit' class="form-control" id="unit" value=""
                                        placeholder="enter Unit">
                                    <span class="error-msg" id="error-unit" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <label for="price">Price</label><span style="color:red;">*</span>
                                    <input type="text" id="price" name='price' class="form-control"
                                        placeholder="Price">
                                    <span class="error-msg" id="error-price" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h6 class="card-title">Status <span style="color:red;">*</span></h6>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <select class="form-control " name='status' id="status">
                                        <option selected value="1">Active</option>
                                        <option value="0">InActive</option>
                                    </select>
                                    <span class="error-msg" id="error-status" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h6 class="card-title">Product Type</h6>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" name='product_type' id="product_type"
                                        rows="2" placeholder="Product Type" />
                                    <span class="error-msg" id="error-product_type" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-body">
                        <h5 class="card-title">Inventory</h5>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <div class="custom-control custom-switch custom-control-inline">
                                        <input type="checkbox" name="track_quantity" class="custom-control-input"
                                            id="track_quantity" value="yes">
                                        <label class="custom-control-label" for="track_quantity">Track Quantity</label>
                                    </div>
                                    <p style="color: #9b2f27" id="quantity_warning"></p>
                                    <span class="error-msg" id="error-track_quantity" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <div class="custom-control custom-switch custom-control-inline">
                                        <input type="checkbox" name="continue_selling" class="custom-control-input"
                                            id="continue_selling" value="yes">
                                        <label class="custom-control-label" for="continue_selling">Continue selling when
                                            out of stock</label>
                                    </div>
                                    <span class="error-msg" id="error-continue_selling" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <label for="sku">SKU (Stock Keeping Unit)</label>
                                    <input type="text" id="sku" name='sku' class="form-control"
                                        placeholder="Stock Keeping Unit">
                                    <span class="error-msg" id="error-sku" style="color: red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-sm-12">
                            <button type="button" data-toggle="tooltip" data-placement="bottom"
                                data-original-title="Cancel" id="cancelbtn"
                                class="btn btn-secondary float-right">Cancel</button>
                            <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                data-original-title="Reset Details"
                                class="btn iq-bg-danger float-right mr-2">Reset</button>
                            <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                data-original-title="Save Details" class="btn btn-primary float-right my-0">Save</button>
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
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            var edit_id = @json($edit_id);


            var uploadedimgs = [];

            var existingImages = [];

            $('.summernote').summernote({
                height: 200,
            });

            $.ajax({
                type: 'GET',
                url: "{{ route('productcolumnmapping.index') }}",
                data: {
                    user_id: "{{ session()->get('user_id') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.productcolumnmapping != '') {
                        columnlinks = response.productcolumnmapping;
                        hasQuantity = true ;

                        $.each(columnlinks,function(id,columnlink){
                            if(columnlink.product_column == 'quantity'){
                                hasQuantity = false;  
                            }
                        });

                        if (hasQuantity) {
                            $('#quantity_warning').html(`
                                <i class="ri-alert-line"></i>
                                If you want to handle it with an invoice, the quantity column mapping is required otherwise it will not be managed automatically.
                            `); // Set text to 'Yes' if 'quantity' exists
                        } 
                    } 
                },
                error: function(xhr, status, error) { // if calling api request error 
                    console.log(xhr.responseText); // Log the full error response for debugging
                }
            });



            $.ajax({
                type: 'GET',
                url: "{{ route('productcategory.fetchCategory') }}",
                data: {
                    user_id: "{{ session()->get('user_id') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    // if response has data then it will be append into list table
                    if (response.status == 200 && response.productcategory != '') {
                        $.each(response.productcategory, function(key, value) {
                            if (value.parent_id == null) {
                                $('.tree-container').append(`
                                    <div class="tree-item parent_${value.id}">
                                        <span data-id="${value.id}">${value.cat_name}</span> 
                                    </div>
                                `);
                            } else {
                                // Check if the arrow does not already exist
                                if (!$(`.parent_${value.parent_id}`).children('.arrow')
                                    .length) {
                                    // Append the arrow if it doesn't exist
                                    $(`.parent_${value.parent_id}`).append(`
                                        <span class="arrow right"></span>
                                        <div class="collapsible">
                                            <div class="tree-item parent_${value.id}">
                                                <span data-id="${value.id}">${value.cat_name}</span> 
                                            </div> 
                                        </div>
                                    `);
                                } else {
                                    // Only append the collapsible section if the arrow exists
                                    $(`.parent_${value.parent_id}`).children('.collapsible')
                                        .append(`
                                            <div class="tree-item parent_${value.id}">
                                                <span data-id="${value.id}">${value.cat_name}</span> 
                                            </div> 
                                        `);
                                }

                            }
                        });
                    } else  {
                         console.log(response);
                    }
                    loaderhide();
                },
                error: function(xhr, status, error) { // if calling api request error 
                    loaderhide();
                    console.log(xhr.responseText); // Log the full error response for debugging
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


            // Handle dropdown click to open/close the list
            $('#category-dropdown').on('click', function(e) {
                e.stopPropagation();
                $(this).toggleClass('open'); // Toggle visibility of tree items
            });

            // Event delegation: Handle click on dynamically added tree items
            $('#category-dropdown').on('click', '.tree-item', function(e) {
                e.stopPropagation(); // Prevent triggering the dropdown toggle event
                var $collapsible = $(this).children('.collapsible');
                var $arrow = $(this).children('.arrow');

                // If clicked on the arrow, toggle expand/collapse
                if ($(e.target).hasClass('arrow')) {
                    // Toggle collapsible sections and change the arrow direction
                    if ($collapsible.length) {
                        $collapsible.toggleClass('open');
                        $arrow.toggleClass('right down');
                    }
                } else {
                    // If clicked on the category name, select the category
                    // Remove the 'selected' class from any previously selected item
                    $('.tree-item').removeClass('selected');
                    $('.tree-item , .tree-item span').removeClass('selectedspan');

                    // Add the 'selected' class only to the clicked item
                    $(this).addClass('selected');
                    // Get only the category name (text) and set it in the dropdown
                    if ($(this).children('span').length > 0) {
                        // If the element has at least one child <span>
                        $(this).children('span').first().addClass('selectedspan');
                        var selectedId = $(this).children('span').first().data('id');
                        var selectedText = $(this).children('span').first().text().trim();
                    } else {
                        // If no <span> found, look for it in the element itself or another container
                        var selectedId = $(this).data('id');
                        var selectedText = $(this).text().trim();
                        $(this).addClass('selectedspan');
                    }

                    $('#category-dropdown .selected-category').text(
                        selectedText); // Update selected category text
                    $('#category-dropdown .selected-category').data('id',
                        selectedId); // Update selected category id

                    $('#category-dropdown').removeClass('open'); // Close the dropdown

                    resetCategories(); // Collapse all categories except parent categories of selected item
                }
            });

            // Close the dropdown if clicked outside of it
            $(document).on('click', function() {
                $('#category-dropdown').removeClass('open');
            });


            // Reset categories to collapse all, and only expand the parents of the selected category
            function resetCategories() {
                // Collapse all categories
                $('.collapsible').removeClass('open');
                $('.arrow').removeClass('down').addClass('right');

                // Expand parent categories of the selected item
                var selectedItem = $('.selected');
                selectedItem.parents('.collapsible').addClass('open');
                selectedItem.parents('.tree-item').find('.arrow:first').removeClass('right').addClass('down');
            }

            // redirect on product list page onclick cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.product') }}";
            });

            // Initialize Dropzone globally (this automatically applies the configuration to elements with .dropzone class)
            Dropzone.options.image = {
                url: "{{ route('temp.docupload') }}", // URL to send the files
                paramName: "file", // Name for the file input
                maxFilesize: 5, // Max file size in MB
                maxFiles: 5,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.mp4,.mov,.avi,.pdf", // Accepted file types
                dictDefaultMessage: "Drop files here or click to upload",
                dictInvalidFileType: "This file type is not allowed",
                addRemoveLinks: true, // Optionally show remove link for files

                init: function() {
                    var myDropzone = this; // Capture Dropzone instance

                    // Handle file upload behavior
                    this.on("sending", function(file, xhr, formData) {
                        document.getElementById('error-image').innerText = '';
                        if (uploadedimgs.indexOf(file.name) !== -1) {
                            myDropzone.removeFile(file);
                            uploadedimgs.push(file.name);
                            Toast.fire({
                                icon: "error",
                                title: "This file has already been uploaded."
                            });
                        } else {
                            formData.append("user_id", "{{ session()->get('user_id') }}");
                            formData.append("company_id", "{{ session()->get('company_id') }}");
                            formData.append("token", "{{ session()->get('api_token') }}");
                        }
                    });

                    this.on("success", function(file, response) {
                        if (response.status == 200) {
                            uploadedimgs.push(response.filename);
                            // Save the URL of the uploaded file to the file object
                            file.fileUrl = response.fileUrl; // Add fileUrl to the file object
                        }
                    });

                    // Add a custom event listener for file previews to open in a new tab
                    this.on("addedfile", function(file) {
                        // Check if the file has a fileUrl (whether it's old or new)

                        // Handle click event for opening file in a new tab
                        file.previewElement.addEventListener("click", function() {
                            var fileUrl = file.fileUrl;
                            if (fileUrl) {
                                window.open(fileUrl, "_blank");
                            }
                        });
                    });

                    this.on("error", function(file, response) {
                        document.getElementById('error-image').innerText =
                            'Error with the file upload. Please try again.';
                    });

                    this.on("removedfile", function(file) {
                        var index = uploadedimgs.indexOf(file.name);
                        if (index > -1) {
                            uploadedimgs.splice(index, 1);
                        }
                    });

                    this.on("complete", function(file) {
                        console.log('File upload complete.');
                    });
                }
            };

            // Now make the AJAX call to load the product data and preload the images
            let productEditUrl = "{{ route('product.edit', '__editId__') }}".replace('__editId__', edit_id);
            $.ajax({
                type: 'GET',
                url: productEditUrl,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    user_id: "{{ session()->get('user_id') }}"
                },
                success: function(response) {
                    if (response.status == 200) {
                        product = response.product;
                        // You can update your HTML with the data here if needed
                        $('#name').val(product.name);
                        $('#short_description').val(product.short_description);
                        $('#sku').val(product.sku);
                        $('#unit').val(product.unit);
                        $('#price').val(product.price_per_unit);
                        $('#status').val(product.is_active);
                        $('#track_quantity').prop('checked', product.track_quantity);
                        $('#continue_selling').prop('checked', product.continue_selling);
                        $('#product_type').val(product.product_type);
                        $('#description').summernote('code', product.description);

                        // Category
                        var selector = product.product_category;
                        $(`.parent_${selector}`).addClass('selected');
                        $(`.parent_${selector}`).find('span').first().addClass('selectedspan');
                        var selectedText = $(`.parent_${selector}`).find('span').first().text().trim();
                        $('.selected-category').data('id', selector);
                        $('.selected-category').text(selectedText);
                        resetCategories();
                        if (product.product_category == null) {
                            $('#error-category').text('Please select a category,it is mandatory field');
                        } else if (product.pc_status == 0) {
                            $('#error-category').text(
                                `please select a category, you did old category '${product.category_name}' inactive.`
                                );
                            $('.selected-category').data('id', null);
                        }

                        // product media 
                        if (product.product_media != null) {
                            // Preload product images into Dropzone
                            existingImages = product.product_media.split(',');

                            const imageBasePath = "{{ asset('uploads/products/') }}";

                            // Add preloaded images to the Dropzone
                            existingImages.forEach(function(imageUrl) {
                                uploadedimgs.push(imageUrl);
                                const fullImageUrl = imageBasePath + '/' + imageUrl;
                                // Mock file object (arbitrary size)
                                var mockFile = {
                                    name: imageUrl, // Name of the file
                                    size: 12345, // Arbitrary size
                                    fileUrl: fullImageUrl // Set the fileUrl for old files
                                };
                                var dropzoneInstance = Dropzone.forElement(
                                    "#image"); // Get the Dropzone instance for the element
                                dropzoneInstance.emit("addedfile", mockFile);
                                dropzoneInstance.emit("thumbnail", mockFile,
                                    fullImageUrl); // Use the image URL for the thumbnail
                                dropzoneInstance.emit("complete",
                                    mockFile); // Mark the file as uploaded
                            });

                        }


                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: "Something went wrong!"
                        });
                    }
                    loaderhide();
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });




            //submit form
            $('#productupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let formdata = $(this).serializeArray();
                formdata.push({
                    name: 'category',
                    value: $('.selected-category').data('id')
                });
                formdata.push({
                    name: 'images',
                    value: uploadedimgs
                });
                $.ajax({
                    type: 'PUT',
                    url: "{{ route('product.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.product') }}";

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var firstErrorElement =
                            null; // Variable to track the first error element

                            $.each(errors, function(key, value) {
                                var errorElement = $('#error-' + key);
                                errorElement.text(value[
                                0]); // Display the error message

                                // Track the first error element
                                if (!firstErrorElement) {
                                    firstErrorElement = errorElement;
                                }
                            });

                            loaderhide();

                            // If there's a first error, scroll to it
                            if (firstErrorElement) {
                                $('html, body').animate({
                                    scrollTop: firstErrorElement.offset().top -
                                        100 // Adjust this value as needed
                                }, 500); // Scroll duration
                            }
                        } else {
                            loaderhide();
                            Toast.fire({
                                icon: "error",
                                title: 'An error occurred while processing your request. Please try again later.'
                            });
                        }

                    }
                });
            })
        });
    </script>
@endpush
