@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')
@section('page_title')
    {{ config('app.name') }} - Update Product Category
@endsection
@section('title')
    Update Product Category
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
    </style>
@endsection

@section('form-content')
    <form id="productcategoryupdateform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" value="{{ $user_id }}" class="form-control" name="user_id"
                        placeholder="user_id">
                    <input type="hidden" value="{{ session('company_id') }}" class="form-control" name="company_id"
                        placeholder="company_id">
                    <label for="category_name">Category Name</label><span style="color:red;">*</span>
                    <input id="category_name" type="text" name="category_name" class="form-control"
                        placeholder="Category Name" required />
                    <span class="error-msg" id="error-category_name" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <label for="parent_category">Parent Category</label><span style="color:red;">*</span>
                    <div class="tree-container" id="category-dropdown">
                        <div class="selected-category" data-id="null">Select a category</div>
                        <div class="tree-item" id="maincat">
                            <span data-id="main" id="maincatspan">Consider As A Main Category</span>
                        </div>
                    </div>
                    <span class="error-msg" id="error-parent_category" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom" data-original-title="Reset Details"
                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Update"
                        class="btn btn-primary float-right my-0">Save</button>
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
                    } else { // if request has not found any bank details record
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

            // redirect on product category list page onclick cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.productcategory') }}";
            });


            // show old data in fields
            let productCategorySearchUrl = "{{ route('productcategory.edit', '__editId__') }}".replace('__editId__', edit_id);
            $.ajax({
                type: 'GET',
                url: productCategorySearchUrl,
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    user_id: "{{ session()->get('user_id') }}"
                },
                success: function(response) {
                    if (response.status == 200) {
                        productcategory = response.productcategory ;
                        // You can update your HTML with the data here if needed
                        $('#category_name').val(productcategory.cat_name);
                        if(productcategory.parent_id){
                            var selector = productcategory.parent_id;
                            $(`.parent_${selector}`).addClass('selected');
                            $(`.parent_${selector}`).find('span').first().addClass('selectedspan');
                            var selectedText = $(`.parent_${selector}`).find('span').first().text().trim();
                            $('.selected-category').data('id',selector);
                        }else{
                            var selector = $(`#maincat`);
                            selector.addClass('selected');
                            selector.find('span').first().addClass('selectedspan');
                            var selectedText = selector.find('span').first().text().trim();
                            $('.selected-category').data('id',selector.find('span').data('id'));
                        }
                        $('.selected-category').text(selectedText);
                        resetCategories(); // Collapse all categories except parent categories of selected item
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
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });

            //submit form
            $('#productcategoryupdateform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                let formdata = $(this).serializeArray();
                formdata.push({
                    name: 'parent_category',
                    value: $('.selected-category').data('id')
                });
                $.ajax({
                    type: 'PUT',
                    url: "{{ route('productcategory.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location = "{{ route('admin.productcategory') }}";

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
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            loaderhide();
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
