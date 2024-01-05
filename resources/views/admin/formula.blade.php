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
            <button type="button" class="btn btn-sm iq-bg-success"><i class="ri-add-fill"><span class="pl-1">Add
                        New Formula</span></i>
            </button>
        </span>
        <table id="add_new_div" class="table table-bordered table-responsive-md table-striped text-center">
            <thead>
                <tr>
                    <th>column1</th>
                    <th>operation</th>
                    <th>column2</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <tr class="iteam_row">
                    <td>
                        <select name="column1" class="form-control" id="column1">
                            <option value="test">test</option>
                            <option value="test2">test2</option>
                            <option value="test3">test3</option>
                            <option value="test4">test4</option>
                            <option value="test5">test5</option>
                        </select>
                    </td>
                    <td>
                        <select name="operation" class="form-control" id="operation">
                            <option value="+">Addition (+)</option>
                            <option value="-">Substraction (-)</option>
                            <option value="*">Multipication (*)</option>
                            <option value="/">Division (/)</option>
                        </select>
                    </td>
                    <td>
                        <select name="column2" class="form-control" id="column2">
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
    </form>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            var addname = 1; // for use to this variable is give to dynamic name and id to input type
            $('.add_div').on('click', function() {
                addname++;
                adddiv();
            });

            // function for add new row in table 
            function adddiv() {
                $('#add_new_div').append(`<tr class="iteam_row">
                    <td>
                        <select name="column1" class="form-control" id="column1">
                            <option value="test">test</option>
                            <option value="test2">test2</option>
                            <option value="test3">test3</option>
                            <option value="test4">test4</option>
                            <option value="test5">test5</option>
                        </select>
                    </td>
                    <td>
                        <select name="operation" class="form-control" id="operation">
                            <option value="+">Addition (+)</option>
                            <option value="-">Substraction (-)</option>
                            <option value="*">Multipication (*)</option>
                            <option value="/">Division (/)</option>
                        </select>
                    </td>
                    <td>
                        <select name="column2" class="form-control" id="column2">
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
                                     </tr>`);
            }

            $(document).on('click', '.remove-row', function() {
                    $(this).parents("tr").detach();
            });
        });
    </script>
@endpush
