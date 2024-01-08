@extends('admin.masterlayout')

@section('page_title')
    Formula
@endsection
@section('title')
    Formula
@endsection


@section('form-content')
    <form id="formulaform" name="formulaform">
        @csrf
        <span class="add_div float-right mb-3 mr-2">
            <button type="button" class="btn btn-sm iq-bg-success"><i class="ri-add-fill"><span
                        class="pl-1">Formula</span></i>
            </button>
        </span>
        <table id="add_new_div" class="table  table-responsive-md  text-center">
            <tbody>
                <tr class="iteam_row">
                    <td>
                        <select name="firstcolumn_1" class="form-control" id="firstcolumn_1">
                            <option value="test">test</option>
                            <option value="test2">test2</option>
                            <option value="test3">test3</option>
                            <option value="test4">test4</option>
                            <option value="test5">test5</option>
                        </select>
                    </td>
                    <td>
                        <select name="operation_1" class="form-control" id="operation_1">
                            <option value="+">+</option>
                            <option value="-">-</option>
                            <option value="*">*</option>
                            <option value="/">/</option>
                        </select>
                    </td>
                    <td>
                        <select name="secondcolumn_1" class="form-control" id="secondcolumn_1">
                            <option value="test">test</option>
                            <option value="test2">test2</option>
                            <option value="test3">test3</option>
                            <option value="test4">test4</option>
                            <option value="test5">test5</option>
                        </select>
                    </td>
                    <td>
                        <select name="output_1" class="form-control" id="output_1">
                            <option value="test">test</option>
                            <option value="test2">test2</option>
                            <option value="test3">test3</option>
                            <option value="test4">test4</option>
                            <option value="test5">test5</option>
                        </select>
                    </td>
                    <td>
                        <span class="remove-row" data-id="1"><button data-id="1" type="button"
                                class="btn iq-bg-danger btn-rounded btn-sm my-0"><i
                                    class="ri-delete-bin-2-line"></i></button></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table class="table table-responsive" id="totalrow">
            <tbody>
                <tr>
                    <td>Total</td>
                    <td>0</td>
                    <td>
                        <button type="button" class="btn btn-sm iq-bg-success add_totalrow"><i class="ri-add-fill"><span
                                    class="pl-1">Total's Formula</span></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <select name="total_column_1" class="form-control" id="total_column_1">
                            <option value="test">test</option>
                            <option value="test2">test2</option>
                            <option value="test3">test3</option>
                            <option value="test4">test4</option>
                            <option value="test5">test5</option>
                        </select>
                    </td>
                    <td>
                        <span class="remove-totalrow" data-id="1"><button data-id="1" type="button"
                                class="btn iq-bg-danger btn-rounded btn-sm my-0"><i
                                    class="ri-delete-bin-2-line"></i></button></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="ri-check-line"></i></button>
            <div id="loader" class="loader"></div>
            <button id="resetbtn" type="reset" class="btn iq-bg-danger"><i class="ri-refresh-line"></i></button>
        </div>
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            var addname = 1; // for use to this variable is give to dynamic name and id to input type
                addnamedltbtn = 1 ;
            $('.add_div').on('click', function() {
                addname++;
                adddiv();
            });
            addtotalrows = 1;
            addtotalrowsdltbtn = 1;
            $('.add_totalrow').on('click', function() {
                addtotalrows++;
                addtotalrow();
            });
            // function for add new row in table 
            function adddiv() {
                $('#add_new_div').append(`<tr class="iteam_row">
                            <td>
                                <select name="firstcolumn_${addname}"  class="form-control" id="firstcolumn_${addname}">
                                    <option value="test">test</option>
                                    <option value="test2">test2</option>
                                    <option value="test3">test3</option>
                                    <option value="test4">test4</option>
                                    <option value="test5">test5</option>
                                </select>
                            </td>
                            <td>
                                <select name="operation_${addname}" class="form-control" id="operation_${addname}">
                                    <option value="+">+</option>
                                    <option value="-">-</option>
                                    <option value="*">*</option>
                                    <option value="/">/</option>
                                </select>
                            </td>
                            <td>
                                <select name="secondcolumn_${addname}" class="form-control" id="secondcolumn_${addname}">
                                    <option value="test">test</option>
                                    <option value="test2">test2</option>
                                    <option value="test3">test3</option>
                                    <option value="test4">test4</option>
                                    <option value="test5">test5</option>
                                </select>
                            </td>
                            <td>
                                <select name="output_${addname}" class="form-control" id="output_${addname}">
                                    <option value="test">test</option>
                                    <option value="test2">test2</option>
                                    <option value="test3">test3</option>
                                    <option value="test4">test4</option>
                                    <option value="test5">test5</option>
                                </select>
                            </td>
                            <td>
                                <span class="remove-row" data-id="${addname}"><button data-id="${addname}" type="button"
                                        class="btn iq-bg-danger btn-rounded btn-sm my-0"><i
                                            class="ri-delete-bin-2-line"></i></button></span>
                            </td>
                </tr>`);
            }

            function addtotalrow() {
                $('#totalrow').append(` <tr>
                        <td colspan="2" >
                            <select name="total_column_${addtotalrows}" class="form-control" id="total_column_${addtotalrows}">
                                <option value="test">test</option>
                                <option value="test2">test2</option>
                                <option value="test3">test3</option>
                                <option value="test4">test4</option>
                                <option value="test5">test5</option>
                            </select>
                        </td>
                        <td>
                            <span class="remove-totalrow" data-id="${addtotalrows}"><button data-id="${addtotalrows}" type="button"
                                    class="btn iq-bg-danger btn-rounded btn-sm my-0"><i
                                        class="ri-delete-bin-2-line"></i></button></span>
                        </td>
                </tr>`);
            }

            $(document).on('click', '.remove-row', function() {
                $(this).parents("tr").detach();
                addnamedltbtn--;
            });
            $(document).on('click', '.remove-totalrow', function() {
                $(this).parents("tr").detach();
                addtotalrowsdltbtn--;
            });
        });
    </script>
@endpush
