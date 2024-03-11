@extends('admin.masterlayout')

@section('page_title')
{{ config('app.name') }} - Invoice Calculation Formula
@endsection
@section('title')
    Formula
@endsection
@section('style')
    <style>
        .error-row {
            border: 2px solid red;
        }
    </style>
@endsection

@section('form-content')
    <form id="formulaform" name="formulaform">
        @csrf
        <input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}">
        <input type="hidden" name="updated_by" id="updated_by">
        <input type="hidden" name="edit_id" id="edit_id">
        <input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
        <span class="add_div float-right mb-3 mr-2">
            <button type="button" class="btn btn-sm iq-bg-success"><i class="ri-add-fill"><span
                        class="pl-1">Formula</span></i>
            </button>
        </span>
        <table class="table  table-responsive-md  text-center">
            <tbody id="add_new_div">
            </tbody>
        </table>
        <hr>

        {{-- <table class="table table-responsive">
            <tbody id="totalrow">
                <tr>
                    <td>Total</td>
                    <td>0</td>
                    <td>
                        <button type="button" class="btn btn-sm iq-bg-success add_totalrow"><i class="ri-add-fill"><span
                                    class="pl-1">Total's Formula</span></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table> --}}
        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="ri-check-line"></i></button>
            <div id="loader" class="loader"></div>
            <button id="resetbtn" type="reset" class="btn iq-bg-danger"><i class="ri-refresh-line"></i></button>
        </div>
    </form>
    <hr>
    <table id="data" class="table  table-bordered display table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Sr</th>
                <th>First Column</th>
                <th>Operation</th>
                <th>Second Column</th>
                <th>Output Column</th>
                <th>Formula Order</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">
        </tbody>
        <tr>
            <td colspan="7">
                <button class="btn btn-sm btn-primary saveformulaorder float-right" title="Save formula order"><i
                        class="ri-check-line"></i></button>
            </td>
        </tr>
    </table>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            let allColumnNames = [];
            // get column list for make formula 
            $.ajax({
                type: "GET",
                url: "{{ route('invoicecolumn.formulacolumnlist') }}",
                data: {
                    user_id: "{{ session()->get('user_id') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    if (response.status == 200 && response.invoicecolumn != '') {
                        loaderhide();
                        global_response = response;
                        var id = 1;
                        // Gather all column_name values

                        $.each(response.invoicecolumn, function(key, value) {
                            $.each(value, function(innerKey, innerValue) {
                                if (innerKey === 'column_name') {
                                    allColumnNames.push(innerValue);
                                }
                            });
                        });
                        $('#add_new_div').append(`
                                            <tr class="iteam_row">
                                                <td>
                                                    <select name="firstcolumn_1" class="form-control firstcolumn" id="firstcolumn_1">
                                                        <optgroup label='Your Column'>
                                                        ${allColumnNames.map(columnName => `<option value="${columnName}">${columnName}</option>`).join('')}
                                                        </optgroup>
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="operation_1" class="form-control operation" id="operation_1">
                                                        <option disabled selected>operator</option>
                                                        <option value="+">+</option>
                                                        <option value="-">-</option>
                                                        <option value="*">*</option>
                                                        <option value="/">/</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="secondcolumn_1" class="form-control secondcolumn" id="secondcolumn_1">
                                                        <optgroup label='Your Column'>
                                                        ${allColumnNames.map(columnName => `<option value="${columnName}">${columnName}</option>`).join('')}
                                                        </optgroup>
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="output_1" class="form-control output " id="output_1">
                                                        <optgroup label='Your Column'>
                                                        ${allColumnNames.map(columnName => `<option value="${columnName}">${columnName}</option>`).join('')}
                                                        </optgroup>
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                 <span class="remove-row" data-id="1"><button data-id="1" type="button" class="btn iq-bg-danger btn-rounded btn-sm my-0"><i class="ri-delete-bin-2-line"></i></button></span> 
                                                </td>
                                            </tr>
                        `);

                    } else if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    } else {
                        $('#add_new_div').append(`
                                            <tr class="iteam_row">
                                                <td>
                                                    <select name="firstcolumn_1" class="form-control firstcolumn" id="firstcolumn_1">
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="operation_1" class="form-control operation" id="operation_1">
                                                        <option disabled selected>operator</option>
                                                        <option value="+">+</option>
                                                        <option value="-">-</option>
                                                        <option value="*">*</option>
                                                        <option value="/">/</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="secondcolumn_1" class="form-control secondcolumn" id="secondcolumn_1">
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="output_1" class="form-control output " id="output_1">
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                 <span class="remove-row" data-id="1"><button data-id="1" type="button" class="btn iq-bg-danger btn-rounded btn-sm my-0"><i class="ri-delete-bin-2-line"></i></button></span> 
                                                </td>
                                            </tr>
                        `);
                    }
                },
                error: function(error) {
                    loaderhide();
                    console.error('Error:', error);
                }
            });

            // code for add row,totalrow and delete add row and totalrow
            var addname = 1; // for use to this variable is give to dynamic name and id to input type
            addnamedltbtn = 1;
            // call function to add new row of columnlist for make formula 
            $('.add_div').on('click', function() {
                addname++;
                adddiv();
            });
            // function for add new row in table 
            function adddiv() {
                if (allColumnNames.length > 0) {
                    $('#add_new_div').append(`
                                            <tr class="iteam_row">
                                                <td>
                                                    <select name="firstcolumn_${addname}" class="form-control firstcolumn" id="firstcolumn_${addname}">
                                                        <optgroup label='Your Column'>
                                                        ${allColumnNames.map(columnName => `<option value="${columnName}">${columnName}</option>`).join('')}
                                                        </optgroup>
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="operation_${addname}" class="form-control operation" id="operation_${addname}">
                                                        <option disabled selected>operator</option>
                                                        <option value="+">+</option>
                                                        <option value="-">-</option>
                                                        <option value="*">*</option>
                                                        <option value="/">/</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="secondcolumn_${addname}" class="form-control secondcolumn" id="secondcolumn_${addname}">
                                                        <optgroup label='Your Column'>
                                                        ${allColumnNames.map(columnName => `<option value="${columnName}">${columnName}</option>`).join('')}
                                                        </optgroup>
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="output_${addname}" class="form-control output " id="output_${addname}">
                                                        <optgroup label='Your Column'>
                                                        ${allColumnNames.map(columnName => `<option value="${columnName}">${columnName}</option>`).join('')}
                                                        </optgroup>
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                 <span class="remove-row" data-id="${addname}"><button data-id="${addname}" type="button" class="btn iq-bg-danger btn-rounded btn-sm my-0"><i class="ri-delete-bin-2-line"></i></button></span> 
                                                </td>
                                            </tr>
                        `);
                } else {
                    $('#add_new_div').append(`
                                            <tr class="iteam_row">
                                                <td>
                                                    <select name="firstcolumn_${addname}" class="form-control firstcolumn" id="firstcolumn_${addname}">
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="operation_${addname}" class="form-control operation" id="operation_${addname}">
                                                        <option disabled selected>operator</option>
                                                        <option value="+">+</option>
                                                        <option value="-">-</option>
                                                        <option value="*">*</option>
                                                        <option value="/">/</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="secondcolumn_${addname}" class="form-control secondcolumn" id="secondcolumn_${addname}">
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="output_${addname}" class="form-control output " id="output_${addname}">
                                                        <optgroup label='Default Column'>
                                                            <option value='Amount'>Amount</option>
                                                            <option value='Total'>Total</option>
                                                            <option value='Gst'>Gst</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                 <span class="remove-row" data-id="${addname}"><button data-id="${addname}" type="button" class="btn iq-bg-danger btn-rounded btn-sm my-0"><i class="ri-delete-bin-2-line"></i></button></span> 
                                                </td>
                                            </tr>
                        `);

                }

            }

            // remove row of make formula
            $(document).on('click', '.remove-row', function() {
                if (confirm('Are you sure to delete this formula?')) {
                    $(this).parents("tr").detach();
                    addnamedltbtn--;
                }
            });
            // code end for add row,totalrow and delete add row and totalrow


            // formula table code 
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('invoiceformula.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.invoiceformula != '') {
                            loaderhide();
                            global_response = response;
                            var id = 1;
                            $.each(response.invoiceformula, function(key, value) {
                                $('#tabledata').append(` <tr>
                                                        <td>${id}</td>
                                                        <td>${value.first_column}</td>
                                                        <td>${value.operation}</td>
                                                        <td>${value.second_column}</td>
                                                        <td>${value.output_column}</td>
                                                        <td><input type='text' placeholder='Set Formula Order' data-id='${value.id}' value='${value.formula_order}'  class='formulaorder'></td>
                                                        <td>
                                                            <span>
                                                                <button type="button" data-id='${value.id}'
                                                                     class="btn edit-btn iq-bg-success btn-rounded btn-sm my-0">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-id= '${value.id}'
                                                                    class=" del-btn btn iq-bg-danger btn-rounded btn-sm my-0">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        </td>
                                                    </tr>`)
                                id++;
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#tabledata').append(`<tr><td colspan='6' >No Data Found</td></tr>`)
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }

            //call function for loaddata
            loaddata();

            // edit formula 
            $(document).on("click", ".edit-btn", function() {
                if (confirm("You want edit this Formula ?")) {
                    loadershow();
                    var editid = $(this).data('id');
                    $.ajax({
                        type: 'get',
                        url: '/api/invoiceformula/edit/' + editid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: " {{ session()->get('company_id') }}",
                            user_id: " {{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            if (response.status == 200 && response.invoiceformula != '') {
                                var invoiceformula = response.invoiceformula;
                                loaderhide();
                                $('#updated_by').val("{{ session()->get('user_id') }}");
                                $('#edit_id').val(editid);
                                $('#firstcolumn_1').val(invoiceformula.first_column).focus();
                                $('#operation_1').val(invoiceformula.operation);
                                $('#secondcolumn_1').val(invoiceformula.second_column);
                                $('#output_1').val(invoiceformula.output_column);
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                toastr.error('Something Went Wrong! please try again later ');
                            }
                        },
                        error: function(error) {
                            loaderhide();
                            console.error('Error:', error);
                        }
                    });
                }
            });

            // delete formula
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this Formula ?')) {
                    var deleteid = $(this).data('id');
                    var row = this;
                    loadershow();
                    $.ajax({
                        type: 'put',
                        url: '/api/invoiceformula/delete/' + deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: " {{ session()->get('company_id') }}",
                            user_id: " {{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success('Formula succesfully deleted');
                                loaderhide();
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                toastr.error('something went wrong !');
                                loaderhide();
                            }
                        }
                    });
                }
            });


            // formula order submit
            $('.saveformulaorder').on('click', function() {
                var formulaorders = [];
                $('input.formulaorder').each(function() {
                    formulaid = $(this).data('id');
                    formulaorder = $(this).val();
                    if (formulaid != null && formulaorder != null) {
                        formulaorders[formulaid] = formulaorder;
                    }
                });
                $.ajax({
                    type: 'Post',
                    url: '{{ route('invoiceformula.formulaorder') }}',
                    data: {
                        formulaorders,
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            toastr.success(response.message);
                            loaderhide();
                            loaddata();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            toastr.error(response.message);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        toastr.error('Something Went Wrong !');
                    }
                });
            });
            

            // formula submit form (check formula conditions)
            var formula_data = [];
            $('#formulaform').submit(function(e) {
                e.preventDefault();
                // Clear existing red borders
                $('table tr.iteam_row').removeClass('error-row');
                $('.checkformula').removeClass('error-row');

                var hasError = false;
                var uniqueArrays = [];
                var uniqueOutputColumns = [];
                var uniqueFormulas = [];

                var i = 0;
                $('table tr.iteam_row').each(function() {
                    formula_data[i] = new Array();
                    formula_data[i][0] = $(this).find('td').find('.firstcolumn').val();
                    formula_data[i][1] = $(this).find('td').find('.operation').val();
                    formula_data[i][2] = $(this).find('td').find('.secondcolumn').val();
                    i++;
                });


                $('table tr.iteam_row').each(function() {
                    var currentArray = [
                        $(this).find('td').find('.firstcolumn').val(),
                        $(this).find('td').find('.operation').val(),
                        $(this).find('td').find('.secondcolumn').val(),
                        $(this).find('td').find('.output').val()
                    ];

                    // Check for repeated columns within a single row
                    if (hasRepeatedColumns(currentArray)) {
                        hasError = true;
                        $(this).addClass('error-row');
                        toastr.error('Repeated columns found in the rows.');
                        return; // Stop processing if there's an issue
                    }

                    // Check if the output column is unique across all rows
                    if (uniqueOutputColumns.includes(currentArray[3])) {
                        hasError = true;
                        $(this).addClass('error-row');
                        toastr.error('Duplicate output column found across rows.');
                        return; // Stop processing if there's an issue
                    }
                    uniqueOutputColumns.push(currentArray[3]);


                    uniqueArrays.push(currentArray);
                });
                // Function to check if an array has repeated columns
                function hasRepeatedColumns(array) {
                    var uniqueColumns = [];
                    for (var i = 0; i < array.length; i++) {
                        if (uniqueColumns.includes(array[i])) {
                            return true; // Repeated column found
                        }
                        uniqueColumns.push(array[i]);
                    }
                    return false; // No repeated column found
                }

                function hasDuplicateCombinations(data) {
                    for (let i = 0; i < data.length - 1; i++) {
                        for (let j = i + 1; j < data.length; j++) {
                            if (areArraysEqual(data[i], data[j]) || areArraysEqual(data[i], reverseArray(
                                    data[j]))) {
                                // Duplicate combination found
                                hasError = true;
                                $('table tr.iteam_row td span.remove-row button[data-id="' + (j + 1) + '"]')
                                    .closest('tr').addClass('error-row');
                                return true;
                            }
                        }
                    }
                    return false;
                }

                function areArraysEqual(arr1, arr2) {
                    if (arr1.length !== arr2.length) {
                        return false;
                    }

                    for (let i = 0; i < arr1.length; i++) {
                        if (arr1[i] !== arr2[i]) {
                            return false;
                        }
                    }

                    return true;
                }

                function reverseArray(arr) {
                    return arr.slice().reverse();
                }


                if (hasDuplicateCombinations(formula_data)) {
                    toastr.error('Duplicate combinations found.');
                }

                if (!hasError) {
                    loadershow();
                    var editid = $('#edit_id').val();
                    if (editid != '') {
                        var updated_by = $('#updated_by').val();
                        var first_column = $('#firstcolumn_1').val();
                        var operation = $('#operation_1').val();
                        var second_column = $('#secondcolumn_1').val();
                        var output_column = $('#output_1').val();
                        $.ajax({
                            type: "post",
                            url: "/api/invoiceformula/update/" + editid,
                            data: {
                                first_column,
                                operation,
                                second_column,
                                output_column,
                                editid,
                                updated_by,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    $('#edit_id').val('');
                                    loaderhide();
                                    // You can perform additional actions, such as showing a success message or redirecting the user
                                    toastr.success(response.message);
                                    $('#formulaform')[0].reset();
                                    loaddata();
                                } else if (response.status == 422) {
                                    loaderhide();
                                    toastr.error('something Went wrong! Please try again later')
                                } else if (response.status == 500) {
                                    toastr.error(response.message);
                                    loaderhide();
                                } else {
                                    loaderhide();
                                    toastr.error(response.message);
                                }

                            },
                            error: function(xhr, status, error) {
                                loaderhide();
                                toastr.error(
                                    'An error occurred while processing your request. Please try again later.'
                                );
                            }

                        });
                    } else {
                        formuladata = [];
                        var i = 0;
                        $('table tr.iteam_row').each(function() {
                            formuladata[i] = new Array();
                            formuladata[i][0] = $(this).find('td').find('.firstcolumn').val();
                            formuladata[i][1] = $(this).find('td').find('.operation').val();
                            formuladata[i][2] = $(this).find('td').find('.secondcolumn').val();
                            formuladata[i][3] = $(this).find('td').find('.output').val();
                            i++;
                        });
                        var company_id = $('#company_id').val();
                        var created_by = $('#user_id').val();
                        if (formuladata != '') {
                            $.ajax({
                                type: "post",
                                url: "{{ route('invoiceformula.store') }}",
                                data: {
                                    formuladata,
                                    token: "{{ session()->get('api_token') }}",
                                    company_id: company_id,
                                    user_id: created_by
                                },
                                success: function(response) {
                                    if (response.status == 200) {
                                        loaderhide();
                                        // You can perform additional actions, such as showing a success message or redirecting the user
                                        toastr.success(response.message);
                                        $('#formulaform')[0].reset();
                                        loaddata();
                                    } else if (response.status == 500) {
                                        toastr.error(response.message);
                                        loaderhide();
                                    } else if (response.status == 422) {
                                        loaderhide();
                                    } else {
                                        loaderhide();
                                        toastr.error(response.message);
                                    }

                                },
                                error: function(xhr, status, error) {
                                    loaderhide();
                                    toastr.error(
                                        'An error occurred while processing your request. Please try again later.'
                                    );
                                }
                            });
                        }
                    }

                }
            });

        });
    </script>
@endpush
