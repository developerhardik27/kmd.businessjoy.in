@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Update Consignor Copy
@endsection
@section('title')
    Update Consignor Copy
@endsection


@section('style')
    <style>
        table th {
            text-align: center;
        }

        @media (max-width: 769px) {
            /* Your responsive styles here */

            table input.form-control {
                width: auto;
            }

            table textarea.form-control {
                width: auto;
            }

        }
    </style>
@endsection


@section('form-content')
    <form id="consignorcopyupdateform" name="consignorcopyupdateform">
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

                    <label for="loading_date">Loading Date</label><span style="color:red;">*</span>
                    <input type="date" name="loading_date" class="form-control" id="loading_date" />
                    <span class="error-msg" id="error-loading_date" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="stuffing_date">Stuffing Date</label><span style="color:red;">*</span>
                    <input type="date" name="stuffing_date" class="form-control" id="stuffing_date" />
                    <span class="error-msg" id="error-stuffing_date" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="truck_number">Truck Number</label>
                    <input type="text" name="truck_number" class="form-control" id="truck_number"
                        placeholder="Truck Number" />
                    <span class="error-msg" id="error-truck_number" style="color: red"></span>
                </div>

                <div class="col-sm-8 mb-2">
                    <label for="driver_name">Driver Name</label>
                    <input type="text" id="driver_name" name="driver_name" class="form-control"
                        placeholder="Driver Name" />
                    <span class="error-msg" id="error-driver_name" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="licence_number">Licence Number</label>
                    <input type="text" id="licence_number" name="licence_number" class="form-control"
                        placeholder="Licence Number" />
                    <span class="error-msg" id="error-licence_number" style="color: red"></span>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" class="form-control"
                        placeholder="Mobile Number" />
                    <span class="error-msg" id="error-mobile_number" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="from">From</label><span style="color:red;">*</span>
                    <input type="text" id="from" name="from" class="form-control" placeholder="From" />
                    <span class="error-msg" id="error-from" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="to">To</label><span style="color:red;">*</span>
                    <input type="text" id="to" name="to" class="form-control" placeholder="To" />
                    <span class="error-msg" id="error-to" style="color: red"></span>
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="to_2">To</label>
                    <input type="text" id="to_2" name="to_2" class="form-control" placeholder="To" />
                    <span class="error-msg" id="error-to_2" style="color: red"></span>
                </div>

                <div class="col-sm-12 mb-2">
                    <label for="gst_tax_payable_by">GST Tax Payable By</label>
                    <select id="gst_tax_payable_by" name="gst_tax_payable_by" class="form-control">
                        <option value="">Select</option>
                        <option value="consignee/consignor">Consignee/Consignor</option>
                        <option value="consignee">Consignee</option>
                        <option value="consignor">Consignor</option>
                    </select>
                    <span class="error-msg" id="error-gst_tax_payable_by" style="color: red"></span>
                </div>

                <div class="col-sm-12 mb-2 table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <td rowspan="2" style="width: 50%">
                                <label for="consignor">Consignor</label><span style="color:red;">*</span>
                                <select type="text" id="consignor" name="consignor" class="form-control"
                                    placeholder="GST Tax Payable By">
                                </select>
                                <span class="error-msg" id="error-consignor" style="color: red"></span>
                            </td>
                            <td>
                                <label for="consignee">Consignee</label><span style="color:red;">*</span>
                                <select type="text" id="consignee" name="consignee" class="form-control"
                                    placeholder="GST Tax Payable By">
                                </select>
                                <span class="error-msg" id="error-consignee" style="color: red"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="cha">CHA</label>
                                <input type="cha" id="cha" name="cha" class="form-control"
                                    placeholder="CHA" />
                                <span class="error-msg" id="error-cha" style="color: red"></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table w-100 table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align: middle">No.of Pallets</th>
                                    <th rowspan="2" style="vertical-align: middle">NATURE OF GOODS (Said to contain)
                                    </th>
                                    <th colspan="2">
                                        WEIGHT
                                        <select name="weight" id="weight" class="form-control">
                                            <option value="">Select</option>
                                            <option value="kg">KG</option>
                                            <option value="ton">TON</option>
                                        </select>
                                    </th>
                                    <th colspan="2">FREIGHT</th>
                                </tr>
                                <tr>
                                    <th>ACTUAL</th>
                                    <th>CHARGED</th>
                                    <th>PAID</th>
                                    <th>TO PAY</th>
                                </tr>
                                <tr>
                                    <td rowspan="3">-</td>
                                    <td rowspan="2" class="text-left">
                                        <div class="align-items-center d-flex mb-2">
                                            <span class="w-25" for="type">Type<span
                                                    style="color:red;">*</span></span>
                                            <div class="w-75">
                                                <select name="type" id="type" class="form-control">
                                                    <option value="import">IMPORT</option>
                                                    <option value="export">EXPORT</option>
                                                    <option value="loose cargo">Loose Cargo</option>
                                                </select>
                                                <span class="error-msg" id="error-type" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="align-items-center d-flex mb-2 justify-content-between">
                                            <div class="align-items-center d-flex">
                                                <label for="container_no">Container No<span
                                                        style="color:red;">*</span></label>
                                                <div>
                                                    <input type="text" id="container_no" name="container_no"
                                                        class="form-control" placeholder="Container Number" />
                                                    <span class="error-msg" id="error-container_no"
                                                        style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="align-items-center d-flex justify-content-between">
                                                <label for="size" class="mx-2">Size</label>
                                                <div>
                                                    <input type="text" id="size" name="size"
                                                        class="form-control" placeholder="Size" />
                                                    <span class="error-msg" id="error-size" style="color: red"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="align-items-center d-flex mb-2">
                                            <span class="w-25" for="shipping_line">Shipping Line</span>
                                            <div class="w-75">
                                                <input type="text" id="shipping_line" name="shipping_line"
                                                    class="form-control" placeholder="Shipping Line" />
                                                <span class="error-msg" id="error-shipping_line"
                                                    style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="align-items-center d-flex mb-2">
                                            <span class="w-25" for="seal_no">Seal No</span>
                                            <div class="w-75">
                                                <input type="text" id="seal_no" name="seal_no" class="form-control"
                                                    placeholder="Seal Number" />
                                                <span class="error-msg" id="error-seal_no" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="align-items-center d-flex mb-2">
                                            <span class="w-25" for="be_inv_no">BE / INV NO.</span>
                                            <div class="w-75">
                                                <input type="text" id="be_inv_no" name="be_inv_no"
                                                    class="form-control" placeholder="BE / INV Number" />
                                                <span class="error-msg" id="error-be_inv_no" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="align-items-center d-flex mb-2">
                                            <span class="w-25" for="port">PORT</span>
                                            <div class="w-75">
                                                <input type="text" id="port" name="port" class="form-control"
                                                    placeholder="PORT" />
                                                <span class="error-msg" id="error-port" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="align-items-center d-flex mb-2">
                                            <span class="w-25" for="pod">POD</span>
                                            <div class="w-75">
                                                <input type="text" id="pod" name="pod" class="form-control"
                                                    placeholder="POD" />
                                                <span class="error-msg" id="error-pod" style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="align-items-center d-flex mb-2 justify-content-between">
                                            <div class="align-items-center d-flex">
                                                <label for="service" class="mr-2">Service</label>
                                                <div>
                                                    <select name="service" id="service" class="form-control">
                                                        <option value="transportation">Transportation</option>
                                                    </select>
                                                    <span class="error-msg" id="error-service" style="color: red"></span>
                                                </div>
                                            </div>
                                            <div class="align-items-center d-flex justify-content-between">
                                                <label for="sac_code" class="mx-2">SAC CODE:</label>
                                                <div>
                                                    <input type="text" id="sac_code" name="sac_code"
                                                        class="form-control" placeholder="SAC CODE" />
                                                    <span class="error-msg" id="error-sac_code"
                                                        style="color: red"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min=0 id="actual" name="actual"
                                            class="form-control" placeholder="Actual" />
                                        <span class="error-msg" id="error-actual" style="color: red"></span>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min=0 id="charged" name="charged"
                                            class="form-control" placeholder="Charged" />
                                        <span class="error-msg" id="error-charged" style="color: red"></span>
                                    </td>
                                    <td rowspan="2" class="text-center">
                                        <div>
                                            <input type="number" step="any" min=0 id="paid" name="paid"
                                                class="form-control" placeholder="Paid" />
                                            <span class="error-msg" id="error-paid" style="color: red"></span>
                                        </div>
                                        <div class="mt-5">
                                            <span>To</span><br>
                                            <span>Be</span><br>
                                            <span>Billed</span><br>
                                            <span>At</span><br>
                                            <span>...</span><br>
                                        </div>
                                    </td>
                                    <td rowspan="2">
                                        <div>
                                            <input type="number" step="any" readonly min=0 id="pay"
                                                name="pay" class="form-control" placeholder="To Pay" />
                                            <span class="error-msg" id="error-pay" style="color: red"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label for="value">Value (RS).</label>
                                        <input type="number" step="any" min=0 id="value" name="value"
                                            class="form-control" placeholder="Value" />
                                        <span class="error-msg" id="error-value" style="color: red"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <p>GOODS BOOKED AS OWNER'S RISK.</p>
                                        <span>Not responsible for Breakage, Leakage, Damage Goods or Fires.</span>
                                    </td>
                                    <td colspan="2" class="text-center" style="border-bottom: transparent;">
                                        <p>For </p>
                                    </td>
                                </tr>
                                <tr class="text-left">
                                    <td colspan="4">
                                        <div class="align-items-baseline d-flex justify-content-between mb-2">
                                            <span for="reached_at_factory_date" class="mr-2">Reached At Factory :</span>
                                            <label for="reached_at_factory_date">Date</label>
                                            <div>
                                                <input type="date" id="reached_at_factory_date"
                                                    name="reached_at_factory_date" class="form-control"
                                                    placeholder="Type" />
                                                <span class="error-msg" id="error-reached_at_factory_date"
                                                    style="color: red"></span>
                                            </div>
                                            <label for="reached_at_factory_time">Time</label>
                                            <div>
                                                <input type="time" id="reached_at_factory_time"
                                                    name="reached_at_factory_time" class="form-control"
                                                    placeholder="Type" />
                                                <span class="error-msg" id="error-reached_at_factory_time"
                                                    style="color: red"></span>
                                            </div>
                                        </div>
                                        <div class="align-items-baseline d-flex justify-content-between mb-2">
                                            <span for="type" class="mr-2">Left From Factory :</span>
                                            <label for="left_from_factory_date">Date</label>
                                            <div>
                                                <input type="date" id="left_from_factory_date"
                                                    name="left_from_factory_date" class="form-control"
                                                    placeholder="Type" />
                                                <span class="error-msg" id="error-left_from_factory_date"
                                                    style="color: red"></span>
                                            </div>
                                            <label for="left_from_factory_time">Time</label>
                                            <div>
                                                <input type="time" id="left_from_factory_time"
                                                    name="left_from_factory_time" class="form-control"
                                                    placeholder="Type" />
                                                <span class="error-msg" id="error-left_from_factory_time"
                                                    style="color: red"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2" class="text-center"
                                        style="vertical-align: bottom;border-top: transparent">
                                        <p>Authorized Signatory</p>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="col-sm-12">
                    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"
                        id="cancelbtn" class="btn btn-secondary float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset Details" class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Save Details" class="btn btn-primary float-right my-0">Save</button>
                </div>
            </div>
        </div>
    </form>



    {{-- for add new consignee  --}}
    <div class="modal fade" id="consigneeFormModal" tabindex="-1" role="dialog"
        aria-labelledby="consigneeFormModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consigneeFormModalTitle">Add New Consignee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="consigneeform">
                        @csrf
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" placeholder="token" required />
                                    <input type="hidden" value="{{ $user_id }}" class="form-control"
                                        name="user_id">
                                    <input type="hidden" value="{{ $company_id }}" class="form-control"
                                        name="company_id">

                                    <label for="modal_firstname">FirstName</label><span class="consigneewithoutgstspan"
                                        style="color:red;">*</span>
                                    <input type="text" id="modal_firstname" class="form-control consigneewithoutgstinput"
                                        name='firstname' placeholder="First Name" required>
                                    <span class="modal_error-msg" id="modal_error-firstname" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_lastname">LastName</label>
                                    <input type="text" id="modal_lastname" class="form-control requiredinput"
                                        name='lastname' placeholder="Last Name">
                                    <span class="modal_error-msg" id="modal_error-lastname" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_company_name">Company Name</label>
                                    <span class="consigneewithgstspan" style="color:red;">*</span>
                                    <input type="text" id="modal_company_name" class="form-control consigneewithgstinput"
                                        name='company_name' id="" placeholder="Company Name">
                                    <span class="modal_error-msg" id="modal_error-company_name"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_gst_number">GST Number</label>
                                    <input type="text" id="modal_gst_number" class="form-control" name='gst_number'
                                        id="" placeholder="GST Number">
                                    <span class="modal_error-msg" id="modal_error-gst_number" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_pan_number">PAN Number</label>
                                    <input type="text" id="modal_pan_number" class="form-control" name='pan_number'
                                        id="" placeholder="PAN Number">
                                    <span class="modal_error-msg" id="modal_error-pan_number" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_email">Email</label>
                                    <input type="email" class="form-control requiredinput" name="email"
                                        id="modal_email" placeholder="Enter Email">
                                    <span class="modal_error-msg" id="modal_error-email" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_contact_number">Contact Number</label>
                                    <input type="tel" class="form-control requiredinput" name='contact_number'
                                        id="modal_contact_number" placeholder="0123456789">
                                    <span class="modal_error-msg" id="modal_error-contact_number"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_country">Select Country</label>
                                    <select class="form-control requiredinput" name='country' id="modal_country">
                                        <option selected="" disabled="">Select your Country</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-country" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_state">Select State</label>
                                    <select class="form-control requiredinput" name='state' id="modal_state">
                                        <option selected="" disabled="">Select your State</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-state" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_city">Select City</label>
                                    <select class="form-control requiredinput" name='city' id="modal_city">
                                        <option selected="" disabled="">Select your City</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-city" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_pincode">Pincode</label>
                                    <input type="text" id="modal_pincode" name='pincode'
                                        class="form-control requiredinput" placeholder="Pin Code">
                                    <span class="modal_error-msg" id="modal_error-pincode" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_house_no_building_name">House no./ Building Name</label>
                                    <textarea class="form-control requiredinput" name='house_no_building_name' id="modal_house_no_building_name"
                                        rows="2" placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                    <span class="modal_error-msg" id="modal_error-house_no_building_name"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_road_name_area_colony">Road Name/Area/Colony</label>
                                    <textarea class="form-control requiredinput" name='road_name_area_colony' id="modal_road_name_area_colony"
                                        rows="2" placeholder="e.g. sardar patel road, jagatpur"></textarea>
                                    <span class="modal_error-msg" id="modal_error-road_name_area_colony"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-12">
                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Cancel" id="consignee_cancelBtn"
                                        class="btn btn-secondary float-right">Cancel</button>
                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Reset Consignee Details"
                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Save Consignee Details"
                                        class="btn btn-primary float-right my-0">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- for add new consignor  --}}
    <div class="modal fade" id="consignorFormModal" tabindex="-1" role="dialog"
        aria-labelledby="consignorFormModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consignorFormModalTitle">Add New Consignor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="consignorform">
                        @csrf
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-sm-6 mb-2">
                                    <input type="hidden" name="token" class="form-control"
                                        value="{{ session('api_token') }}" placeholder="token" required />
                                    <input type="hidden" value="{{ $user_id }}" class="form-control"
                                        name="user_id">
                                    <input type="hidden" value="{{ $company_id }}" class="form-control"
                                        name="company_id">

                                    <label for="modal_firstname">FirstName</label><span class="consignorwithoutgstspan"
                                        style="color:red;">*</span>
                                    <input type="text" id="modal_firstname" class="form-control consignorwithoutgstinput"
                                        name='firstname' placeholder="First Name" required>
                                    <span class="modal_error-msg" id="modal_error-firstname" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_lastname">LastName</label>
                                    <input type="text" id="modal_lastname" class="form-control requiredinput"
                                        name='lastname' placeholder="Last Name">
                                    <span class="modal_error-msg" id="modal_error-lastname" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_company_name">Company Name</label>
                                    <span class="consignorwithgstspan" style="color:red;">*</span>
                                    <input type="text" id="modal_company_name" class="form-control consignorwithgstinput"
                                        name='company_name' id="" placeholder="Company Name">
                                    <span class="modal_error-msg" id="modal_error-company_name"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_gst_number">GST Number</label>
                                    <input type="text" id="modal_gst_number" class="form-control" name='gst_number'
                                        id="" placeholder="GST Number">
                                    <span class="modal_error-msg" id="modal_error-gst_number" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_pan_number">PAN Number</label>
                                    <input type="text" id="modal_pan_number" class="form-control" name='pan_number'
                                        id="" placeholder="PAN Number">
                                    <span class="modal_error-msg" id="modal_error-pan_number" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_email">Email</label>
                                    <input type="email" class="form-control requiredinput" name="email"
                                        id="modal_email" placeholder="Enter Email">
                                    <span class="modal_error-msg" id="modal_error-email" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_contact_number">Contact Number</label>
                                    <input type="tel" class="form-control requiredinput" name='contact_number'
                                        id="modal_contact_number" placeholder="0123456789">
                                    <span class="modal_error-msg" id="modal_error-contact_number"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_country">Select Country</label>
                                    <select class="form-control requiredinput" name='country' id="modal_country">
                                        <option selected="" disabled="">Select your Country</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-country" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_state">Select State</label>
                                    <select class="form-control requiredinput" name='state' id="modal_state">
                                        <option selected="" disabled="">Select your State</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-state" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_city">Select City</label>
                                    <select class="form-control requiredinput" name='city' id="modal_city">
                                        <option selected="" disabled="">Select your City</option>
                                    </select>
                                    <span class="modal_error-msg" id="modal_error-city" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_pincode">Pincode</label>
                                    <input type="text" id="modal_pincode" name='pincode'
                                        class="form-control requiredinput" placeholder="Pin Code">
                                    <span class="modal_error-msg" id="modal_error-pincode" style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_house_no_building_name">House no./ Building Name</label>
                                    <textarea class="form-control requiredinput" name='house_no_building_name' id="modal_house_no_building_name"
                                        rows="2" placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                    <span class="modal_error-msg" id="modal_error-house_no_building_name"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-6 mb-2">
                                    <label for="modal_road_name_area_colony">Road Name/Area/Colony</label>
                                    <textarea class="form-control requiredinput" name='road_name_area_colony' id="modal_road_name_area_colony"
                                        rows="2" placeholder="e.g. sardar patel road, jagatpur"></textarea>
                                    <span class="modal_error-msg" id="modal_error-road_name_area_colony"
                                        style="color: red"></span>
                                </div>

                                <div class="col-sm-12">
                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Cancel" id="consignor_cancelBtn"
                                        class="btn btn-secondary float-right">Cancel</button>
                                    <button type="reset" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Reset Consignee Details"
                                        class="btn iq-bg-danger float-right mr-2">Reset</button>
                                    <button type="submit" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Save Consignee Details"
                                        class="btn btn-primary float-right my-0">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
            loaderhide(); 

            const API_TOKEN = "{{ session()->get('api_token') }}";
            const COMPANY_ID = "{{ session()->get('company_id') }}";
            const USER_ID = "{{ session()->get('user_id') }}";

            $('.consigneewithgstspan, .consignorwithgstspan').hide();
            $('.consigneewithgstinput').on('change keyup', function() {
                console.info('yes');
                var val = $(this).val();
                if (val != '') {
                    $('.consigneewithgstspan').show();
                    $('.consigneewithoutgstspan').hide();
                    $('.consigneewithgstinput').attr('required', true);
                    $('.consigneewithoutgstinput').removeAttr('required');
                } else {
                    $('.consigneewithgstspan').hide();
                    $('.consigneewithoutgstspan').show();
                    $('.consigneewithoutgstinput').attr('required', true);
                    $('.consigneewithgstinput').removeAttr('required');
                }
            });

            $('.consignorwithgstinput').on('change keyup', function() {
                var val = $(this).val();
                if (val != '') {
                    $('.consignorwithgstspan').show();
                    $('.consignorwithoutgstspan').hide();
                    $('.consignorwithgstinput').attr('required', true);
                    $('.consignorwithoutgstinput').removeAttr('required');
                } else {
                    $('.consignorwithgstspan').hide();
                    $('.consignorwithoutgstspan').show();
                    $('.consignorwithoutgstinput').attr('required', true);
                    $('.consignorwithgstinput').removeAttr('required');
                }
            });

            function ajaxRequest(type, url, data) {
                return $.ajax({
                    type,
                    url,
                    data
                });
            }

            function handleAjaxError(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#error-' + key).text(value[0]);
                    });
                    $('html, body').animate({
                        scrollTop: 0
                    }, 1000);
                } else {
                    var errorMessage = "An error occurred";
                    try {
                        var responseJSON = JSON.parse(xhr.responseText);
                        errorMessage = responseJSON.message || errorMessage;
                    } catch (e) {}
                    Toast.fire({
                        icon: "error",
                        title: errorMessage
                    });
                }
            }

            // consignee data fetch and set consignee dropdown
            function consignees(consigneeid = '') {
                loadershow();
                $('#consignee').html(`
                   <option value="add_consignee" > Add New Consignee </option>
                `);

                ajaxRequest('GET', "{{ route('consignee.getconsigneelist') }}", {
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    if (response.status == 200 && response.consignee != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.consignee, function(key, value) {
                            const consigneeDetails = [value.firstname, value.lastname, value
                                .company_name, value
                                .contact_no
                            ].filter(Boolean).join(' - ');

                            $('#consignee').append(
                                `<option value='${value.id}'>${consigneeDetails}</option>`
                            )

                        });
                        $('#consignee').val(consigneeid);
                        $('#consignee').select2({
                            search: true,
                            placeholder: 'Select a Consignee'
                        }); // search bar in consignee list
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#consignee').append(`<option disabled '>No Data found </option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });


            };

            consignees();


            $('#consignee').on('change', function() {
                loadershow();
                var selectedOption = $(this).find('option:selected');
                var consigneeid = $(this).val();
                if (consigneeid == 'add_consignee') {
                    $('#consigneeFormModal').modal('show');
                }
                loaderhide();
            });

            // consignor data fetch and set consignor dropdown
            function consignors(consignorid = '') {
                loadershow();
                $('#consignor').html(`
                   <option value="add_consignor" > Add New Consignor </option>
                `);

                ajaxRequest('GET', "{{ route('consignor.getconsignorlist') }}", {
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    if (response.status == 200 && response.consignor != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.consignor, function(key, value) {
                            const consignorDetails = [value.firstname, value.lastname, value
                                .company_name, value
                                .contact_no
                            ].filter(Boolean).join(' - ');

                            $('#consignor').append(
                                `<option value='${value.id}'>${consignorDetails}</option>`
                            )

                        });
                        $('#consignor').val(consignorid);
                        $('#consignor').select2({
                            search: true,
                            placeholder: 'Select a Consignor'
                        }); // search bar in consignor list
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#consignor').append(`<option disabled '>No Data found </option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });


            };

            consignors();


            $('#consignor').on('change', function() {
                loadershow();
                var selectedOption = $(this).find('option:selected');
                var consignorid = $(this).val();
                if (consignorid == 'add_consignor') {
                    $('#consignorFormModal').modal('show');
                }
                loaderhide();
            });

            // close pop up modal and reset new consignee form
            $('#consignee_cancelBtn').on('click', function() {
                $('#consigneeform')[0].reset();
                $('#consigneeFormModal').modal('hide');
                $('#consignee').val('').trigger('change');
            })

            // close pop up modal and reset new consignor form
            $('#consignor_cancelBtn').on('click', function() {
                $('#consignorform')[0].reset();
                $('#consignorFormModal').modal('hide');
                $('#consignor').val('').trigger('change');
            })

            $('#paid , #value').on('keyup change', function() {
                let paidValue = parseInt($('#paid').val());
                let value = parseInt($('#value').val());
                if (!isNaN(paidValue) && !isNaN(value)) {
                    let pay = value - paidValue;
                    $('#pay').val(pay); // assuming you want to show it somewhere
                } else {
                    $('#pay').val(''); // clear if input is invalid
                }
            });




            // redirect on consignorcopy list page onclick cancel btn
            $('#cancelbtn').on('click', function() {
                loadershow();
                window.location.href = "{{ route('admin.consignorcopy') }}";
            });

            function loaddata() {
                var edit_id = @json($edit_id);
                // show old data in fields
                let consignorCopySearchUrl = "{{ route('consignorcopy.edit', '__editId__') }}".replace('__editId__',
                edit_id);
                $.ajax({
                    type: 'GET',
                    url: consignorCopySearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        user_id: " {{ session()->get('user_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            let consignorcopy = response.consignorcopy ;

                            $('#loading_date').val(consignorcopy.loading_date);
                            $('#stuffing_date').val(consignorcopy.stuffing_date);
                            $('#truck_number').val(consignorcopy.truck_number);
                            $('#driver_name').val(consignorcopy.driver_name);
                            $('#licence_number').val(consignorcopy.licence_number);
                            $('#mobile_number').val(consignorcopy.mobile_number);
                            $('#from').val(consignorcopy.from);
                            $('#to').val(consignorcopy.to);
                            $('#to_2').val(consignorcopy.to_2);
                            $('#gst_tax_payable_by').val(consignorcopy.gst_tax_payable_by);
                            $('#consignor').val(consignorcopy.consignor_id).select2();
                            $('#consignee').val(consignorcopy.consignee_id).select2();
                            $('#cha').val(consignorcopy.cha);
                            $('#weight').val(consignorcopy.weight);
                            $('#type').val(consignorcopy.type);
                            $('#container_no').val(consignorcopy.container_no);
                            $('#size').val(consignorcopy.size);
                            $('#shipping_line').val(consignorcopy.shipping_line);
                            $('#seal_no').val(consignorcopy.seal_no);
                            $('#seal_no').val(consignorcopy.seal_no);
                            $('#be_inv_no').val(consignorcopy.be_inv_no);
                            $('#port').val(consignorcopy.port);
                            $('#pod').val(consignorcopy.pod);
                            $('#service').val(consignorcopy.service);
                            $('#sac_code').val(consignorcopy.sac_code);
                            $('#actual').val(consignorcopy.actual);
                            $('#charged').val(consignorcopy.charged);
                            $('#value').val(consignorcopy.value);
                            $('#paid').val(consignorcopy.paid);
                            $('#pay').val(consignorcopy.to_pay);
                            $('#reached_at_factory_date').val(consignorcopy.reached_at_factory_date);
                            $('#reached_at_factory_time').val(consignorcopy.reached_at_factory_time);
                            $('#left_from_factory_date').val(consignorcopy.left_from_factory_date);
                            $('#left_from_factory_time').val(consignorcopy.left_from_factory_time);
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

            loaddata();

            // submit consignor copy form data
            $('#consignorcopyupdateform').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: "{{ route('consignorcopy.update', $edit_id) }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            window.location =
                                "{{ route('admin.consignorcopy') }}"; // after succesfully data submit redirect on list page
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
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            let firstErrorElement = null;

                            $.each(errors, function(key, value) {
                                let errorElement = $('#error-' + key);
                                errorElement.text(value[0]);

                                // Capture the first error element
                                if (!firstErrorElement) {
                                    firstErrorElement = errorElement;
                                }
                            });

                            if (firstErrorElement) {
                                $('html, body').animate({
                                    scrollTop: firstErrorElement.offset().top -
                                        100 // adjust for spacing
                                }, 800);
                            }
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
                })
            });


            $('#consignorFormModal').on('show.bs.modal', function() {
                loadstate(0, 'consignorform');
                loadcity(0, 'consignorform');
            });

            $('#consignorFormModal').on('hidden.bs.modal', function() {
                if($('#consignor').val() == 'add_consignor'){
                    $('#consignor').val('').trigger('change');
                }
            });

            $('#consigneeFormModal').on('show.bs.modal', function() {
                loadstate(0, 'consigneeform');
                loadcity(0, 'consigneeform');
            });

            $('#consigneeFormModal').on('hidden.bs.modal', function() {
                if($('#consignee').val() == 'add_consignee'){
                    $('#consignee').val('').trigger('change');
                }
            });

            // for add new customer 

            // show country data in dropdown and set default value according logged in user
            ajaxRequest('GET', "{{ route('country.index') }}", {
                token: API_TOKEN,
            }).done(function(response) {
                if (response.status == 200 && response.country != '') {
                    // You can update your HTML with the data here if needed
                    $.each(response.country, function(key, value) {
                        $('#consigneeform #modal_country').append(
                            `<option value='${value.id}'> ${value.country_name}</option>`
                        )
                        $('#consignorform #modal_country').append(
                            `<option value='${value.id}'> ${value.country_name}</option>`
                        )
                    });
                    country_id = "{{ session('user')['country_id'] }}";
                    $('#consigneeform #modal_country').val(country_id);
                    $('#consignorform #modal_country').val(country_id);
                } else {
                    $('#consigneeform #modal_country').append(`<option> No Data Found</option>`);
                    $('#consignorform #modal_country').append(`<option> No Data Found</option>`);
                }
                loaderhide();
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });

            // load state in dropdown when country change
            $(document).on('change', '#modal_country', function() {
                let parentform = $(this).closest('form').attr('id');
                loadershow();
                $(`#${parentform} #modal_city`).html(
                    `<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id, parentform);
            });

            // load state in dropdown and select state according to user
            function loadstate(id = 0, parentform = 'consigneeform') {
                $(`#${parentform} #modal_state`).html(`<option selected="" disabled="">Select your State</option>`);
                let stateSearchUrl = "{{ route('state.search', 'id') }}".replace('id', id);
                var url = stateSearchUrl;
                if (id == 0) {
                    url = "{{ route('state.search', session('user')['country_id']) }}";
                }
                ajaxRequest('GET', url, {
                    token: API_TOKEN,
                }).done(function(response) {
                    if (response.status == 200 && response.state != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.state, function(key, value) {
                            $(`#${parentform} #modal_state`).append(
                                `<option value='${value.id}'> ${value.state_name}</option>`
                            )
                        });
                        if (id == 0) {
                            state_id = "{{ session('user')['state_id'] }}";
                            $(`#${parentform} #modal_state`).val(state_id);
                        }
                    } else {
                        $(`#${parentform} #modal_state`).append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }

            // load city in dropdown when state select/change
            $(document).on('change', '#modal_state', function() {
                parentform = $(this).closest('form').attr('id');
                loadershow();
                var state_id = $(this).val();
                loadcity(state_id, parentform);
            });

            function loadcity(id = 0, parentform = 'consigneeform') {
                $(`#${parentform} #modal_city`).html(`<option selected="" disabled="">Select your City</option>`);
                citySearchUrl = "{{ route('city.search', 'id') }}".replace('id', id);
                url = citySearchUrl;
                if (id == 0) {
                    url = "{{ route('city.search', session('user')['state_id']) }}";
                }

                ajaxRequest('GET', url, {
                    token: API_TOKEN,
                }).done(function(response) {
                    if (response.status == 200 && response.city != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.city, function(key, value) {
                            $(`#${parentform} #modal_city`).append(
                                `<option value='${value.id}'> ${value.city_name}</option>`
                            )
                        });
                        if (id == 0) {
                            $(`#${parentform} #modal_city`).val(
                                "{{ session('user')['city_id'] }}");
                        }
                    } else {
                        $(`#${parentform} #modal_city`).append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            }


            // submit new consignee  form
            $('#consigneeform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.modal_error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('consignee.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#consigneeform')[0].reset();
                            $('#consigneeFormModal').modal('hide');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            consignees(response.consignee_id);
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
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
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorcontainer;
                            $.each(errors, function(key, value) {
                                $('#consigneeform #modal_error-' + key).text(value[0]);
                                errorcontainer = '#consigneeform #modal_error-' + key;
                            });
                            $('.modal-body #consigneeform').animate({
                                scrollTop: $(errorcontainer).position().top
                            }, 1000);
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
            });

            // submit new consignor  form
            $('#consignorform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.modal_error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('consignor.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#consignorform')[0].reset();
                            $('#consignorFormModal').modal('hide');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            consignors(response.consignor_id);
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
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
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorcontainer;
                            $.each(errors, function(key, value) {
                                $('#consignorform #modal_error-' + key).text(value[0]);
                                errorcontainer = '#consignorform #modal_error-' + key;
                            });
                            $('.modal-body #consignorform').animate({
                                scrollTop: $(errorcontainer).position().top
                            }, 1000);
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
            });

        });
    </script>
@endpush
