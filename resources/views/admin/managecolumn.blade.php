@extends('admin.masterlayout')

@section('page_title')
    Manage Columns
@endsection
@section('title')
    Manage Columns
@endsection


@section('form-content')
    <form id="columnform" name="columnform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-6">
                    <input type="text" class="form-control form-input" name="columnname" placeholder="Column Name"
                        id="columnname">
                    <span class="error-msg" id="error-swift_code" style="color: red"></span>
                </div>
                <div class="col-sm-6">
                    <select name="columntype" class="form-control" id="columntype">
                        <option value="text">Text</option>
                        <option value="longtext">Long Text</option>
                        <option value="number">Number</option>
                        <option value="decimal">Decimal</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="ri-add-line"></i></button>
            <div id="loader" class="loader"></div>
            <button id="resetbtn" type="reset" class="btn iq-bg-danger"><i class="ri-refresh-line"></i></i></button>
        </div>
    </form>
    <br>
    <h4>Column List</h4>
    <table id="data" class="table  table-bordered display table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Column Name</th>
                <th>Column Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>test</td>
                <td>test</td>
                <td>
                    <span class="">
                        <a href='#'>
                            <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                <i class="ri-edit-fill"></i>
                            </button>
                        </a>
                    </span> <button type="button" data-id= '${value.id}'
                        class=" del-btn btn btn-danger btn-rounded btn-sm my-0">
                        <i class="ri-delete-bin-fill"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
