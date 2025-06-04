@php
    $folder = session('folder_name');
@endphp
@extends($folder.'.admin.Layout.masterlayout')
@section('page_title')
{{ config('app.name') }} - Create New Invoice
@endsection
@section('title')
    Create Invoice
@endsection

@section('style')
 <style>
    .disableinput{
        border: none;
    }

    /* For select2 dropdown, override the default select2 styles for disabled items */
    .select2-results__option[aria-disabled="true"] {
        color: red !important; 
    }

    table input.form-control {
        width: auto;
        min-width: 100%;
    }

    table textarea.form-control{
        width: auto;
        min-width: 100%;
    }

    .select2-container{
        width: 100% !important;
    }
 </style>
 <link rel="stylesheet" href="{{asset('admin/css/select2.min.css')}}">
@endsection

@section('form-content')
    <form id="invoiceform" name="invoiceform">
        @csrf
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-4 mb-3">
                      <input type="hidden" name="country_id" id="country" class="form-control" value="" />
                    <input type="hidden" name="user_id" id="created_by" class="form-control"
                        value="{{ $user_id }}" />
                    <input type="hidden" name="company_id" id="company_id" class="form-control"
                        value="{{ $company_id }}" />
                    <label for="customer">Customer</label><span
                    style="color:red;">*</span>
                    <select class="form-control select2" id="customer" name="customer" required>
                        <option selected="" disabled=""> Select Customer</option>
                        <option  value="add_customer" > Add New Customer </option>
                    </select>
                    <span class="error-msg" id="error-customer" style="color: red"></span>
                </div>
                <div class="col-sm-4 mb-3">
                    <label for="currency">Currency</label><span
                    style="color:red;">*</span>
                    <select class="form-control" id="currency" name="currency" required>
                        <option selected="" disabled=""> Select Currency</option>
                    </select>
                    <span class="error-msg" id="error-currency" style="color: red"></span>
                </div> 
                <div class="col-sm-4 mb-3">
                    <label for="type">Tax-Type</label><span
                    style="color:red;">*</span>
                    <select class="form-control" id="type" name="type" required>
                        <option selected="" disabled="">Select Type</option>
                        <option value="1">GST</option>
                        <option value="2">Without GST</option>
                    </select>
                    <span class="error-msg" id="error-tax_type" style="color: red"></span>
                </div> 
                <div class="col-sm-4 mb-3">
                    <label for="acc_details">Bank Account </label><span
                    style="color:red;">*</span>
                    <select class="form-control" id="acc_details" name="acc_details" required>
                        <option selected="" disabled="">Select Account</option>
                    </select>
                    <span class="error-msg" id="error-bank_account" style="color: red"></span>
                </div> 
                <div class="col-sm-4 mb-3" id="inv_date_div">
                    <label for="invoice_date">Invoice Date</label> 
                    <input type="datetime-local" class="form-control" id="invoice_date" name="invoice_date">
                    <span class="error-msg" id="error-invoice_date" style="color: red"></span>
                </div>
                <div class="col-sm-4 mb-3" id="inv_number_div">
                    <label for="inv_number">Invoice Number</label> 
                    <input type="text" name="inv_number" id="inv_number" class="form-control" placeholder="Invoice Number">
                    <span class="error-msg" id="error-inv_number" style="color: red"></span>
                </div> 
            </div>
        </div> 
        <div id="table" class="table-editable" style="overflow-x:auto">
            
            <table id="data" class="table table-bordered  table-striped text-center producttable">
                <thead>
                    <tr id="columnname" style="text-transform: uppercase">

                       
                    </tr>
                </thead>
                <tbody  id="add_new_div">
                </tbody>
                <tr>
                    <th class="newdivautomaticcolspan">
                        <div style="display:flex;justify-content: center;"> 
                            <span class="add_div mb-3 mr-2"> 
                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Row" class="btn btn-sm iq-bg-success">
                                    <i class="ri-add-fill">
                                        <span class="pl-1"> Add New Item </span>
                                    </i>
                                </button> 
                            </span>
                            <span> 
                                <select class="form-control select2" id="product" name="product">
                                    
                                </select> 
                            </span> 
                        </div>
                    </th>
                </tr>
                <tr class="text-right">
                    <th class="automaticcolspan">Sub total</th>
                    <td id=""> 
                       <div class="d-flex justify-content-between">
                            <b>
                                <span class="currentcurrencysymbol"></span>
                            </b>  
                            <input class="disableinput" type="number" step="any" name="total_amount" id="totalamount" readonly required>
                        </div>
                    </td>
                </tr>
                <tr id="sgstline" class="text-right">
                    <th class="automaticcolspan">SGST <span id="sgstpercentage"></span></th>
                    <td>
                        <div class="d-flex justify-content-between">
                            <b><span class="currentcurrencysymbol"></span></b> <input class="disableinput" type="number" step="any" name="sgst" id="sgst" readonly required>
                        </div>
                    </td>
                </tr>
                <tr id="cgstline" class="text-right">
                    <th class="automaticcolspan">CGST <span id="cgstpercentage"></span></th>
                    <td>
                        <div class="d-flex justify-content-between">
                            <b><span class="currentcurrencysymbol"></span></b> <input class="disableinput" type="number" step="any" name="cgst" id="cgst" readonly required>
                        </div>
                    </td>
                </tr>
                <tr id="gstline" class="text-right">
                    <th class="automaticcolspan">Total GST <span id="gstpercentage"></span></th>
                    <td>
                        <div class="d-flex justify-content-between">
                            <b><span class="currentcurrencysymbol"></span></b> <input class="disableinput" type="number" step="any" name="gst" id="gst" readonly required>
                        </div>
                    </td>
                </tr>
                <tr id="roundoffline" class="text-right">
                    <th class="automaticcolspan">Roundoff</th>
                    <td>
                        <div class="d-flex justify-content-between">
                            <b>
                                <span class="currentcurrencysymbol"></span>
                            </b> 
                            <input class="disableinput" type="text" name="roundoff" id="roundoff" readonly required>
                        </div>
                    </td>
                </tr>
                <tr id="grandtotalline" class="text-right">
                    <th class="automaticcolspan font-weight-bold">Total</th>
                    <td>
                        <div class="d-flex justify-content-between">
                            <b>
                                <span class="currentcurrencysymbol"></span>
                            </b> 
                            <input class="disableinput" type="number" step="any" name="grandtotal" id="grandtotal" readonly required>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                    <span class="error-msg" id="error-notes" style="color: red"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
           <div class="form-row">
                <div class="col-sm-12">
                    <button id="cancelbtn" type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel" class="btn btn-secondary btn-rounded float-right">Cancel</button>
                    <button type="reset" data-toggle="tooltip" data-placement="bottom" data-original-title="Reset Details" class="btn iq-bg-danger float-right mr-2">Reset</button>
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Submit Details" class="btn btn-primary float-right my-0" >Submit</button>
                </div>
           </div>
        </div>
    </form>


    {{-- for add new customer direct from invoiceform --}}
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="details" width='100%' class="table table-bordered table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped">
                        <form id="customerform">
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
                                        <label for="firstname">FirstName</label><span class="withoutgstspan" style="color:red;">*</span>
                                        <input type="text" class="form-control withoutgstinput" id="firstname" name='firstname'
                                            placeholder="First name" required>
                                        <span class="modal-error-msg" id="modal-error-firstname"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label for="lastname">LastName</label>
                                        {{-- <span style="color:red;">*</span> --}}
                                        <input type="text" class="form-control" id="lastname" name='lastname'
                                            placeholder="Last name">
                                        <span class="modal-error-msg" id="modal-error-lastname"
                                            style="color: red"></span>
                                    </div> 
                                    <div class="col-sm-6 mb-2">
                                        <label for="company_name">Company Name</label>
                                        <span class="withgstspan" style="color:red;">*</span>
                                        <input type="text" class="form-control withgstiput" id="company_name" name='company_name'
                                            id="" placeholder="Company name">
                                        <span class="modal-error-msg" id="modal-error-company_name"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label for="gst_number">GST Number</label>
                                        {{-- <span class="withgstspan" style="color:red;">*</span> --}}
                                        <input type="text" class="form-control" name='gst_number' id="gst_number"
                                            placeholder="GST Number">
                                        <span class="modal-error-msg" id="modal-error-gst_number"
                                            style="color: red"></span>
                                    </div> 
                                    <div class="col-sm-6 mb-2">
                                        <label for="modal_email">Email</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <input type="email" class="form-control requiredinput" name="email" id="modal_email"
                                            placeholder="Enter Email"/>
                                        <span class="modal-error-msg" id="modal-error-email" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label for="modal_exampleInputphone">Contact Number</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <input type="tel" class="form-control requiredinput" name='contact_number'
                                            id="modal_exampleInputphone" placeholder="0123456789">
                                        <span class="modal-error-msg" id="modal-error-contact_number"
                                            style="color: red"></span>
                                    </div> 
                                    <div class="col-sm-6 mb-2">
                                        <label for="modal_country">Select Country</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <select class="form-control requiredinput" name='country' id="modal_country">
                                            <option selected="" disabled="">Select your Country</option>
                                        </select>
                                        <span class="modal-error-msg" id="modal-error-country"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label for="modal_state">Select State</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <select class="form-control requiredinput" name='state' id="modal_state">
                                            <option selected="" disabled="">Select your State</option>
                                        </select>
                                        <span class="modal-error-msg" id="modal-error-state"
                                            style="color: red"></span>
                                    </div> 
                                    <div class="col-sm-6 mb-2">
                                        <label for="modal_city">Select City</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <select class="form-control requiredinput" name='city' id="modal_city">
                                            <option selected="" disabled="">Select your City</option>
                                        </select>
                                        <span class="modal-error-msg" id="modal-error-city"
                                            style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label for="modal_pincode">Pincode</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <input type="text" id="modal_pincode" name='pincode' class="form-control requiredinput"
                                            placeholder="Pin Code">
                                        <span class="modal-error-msg" id="modal-error-pincode"
                                            style="color: red"></span>
                                    </div> 
                                    <div class="col-sm-6 mb-2">
                                        <label for="house_no_building_name">House no./ Building Name</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <textarea class="form-control requiredinput" name='house_no_building_name' id="house_no_building_name" rows="2" placeholder="e.g. 2nd floor/ 04 ABC Appartment"></textarea>
                                        <span class="modal-error-msg" id="modal-error-house_no_building_name" style="color: red"></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label for="road_name_area_colony">Road Name/Area/Colony</label>
                                        {{-- <span class="requiredinputspan" style="color:red;">*</span> --}}
                                        <textarea class="form-control requiredinput" name='road_name_area_colony' id="road_name_area_colony" rows="2" placeholder="e.g. sardar patel road, jagatpur"></textarea>
                                        <span class="modal-error-msg" id="modal-error-road_name_area_colony" style="color: red"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="modal_submitBtn">Save</button>
                                <button id="modal_resetbtn" type="reset" class="btn iq-bg-danger mr-2">Reset</button>
                                <button id="modal_cancelBtn" type="btn" class="btn btn-secondary">Cancel</button>
                            </div>
                        </form>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@push('ajax')
   <script src="{{asset('admin/js/select2.min.js')}}"></script>
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
 

            // customer form  -> dynamic required attribute (if enter company name then only company name required otherwise only firstname)
            $('.withgstspan').hide(); 

            // This will listen for any change event on the form's inputs.
            $('#add_new_div').on('change', 'input, textarea', function() {
                // Iterate through all the inputs and update their title attribute with their current value.
                $('#add_new_div input, #add_new_div textarea').each(function() {
                    var inputValue = $(this).val();  // Get the current value of the input/textarea
                    $(this).attr('title', inputValue);  // Set the title attribute with the value
                });
            });

            $('#company_name').on('change keyup', function() {
                var val = $(this).val();
                if (val != '') {
                    $('.withgstspan').show();
                    $('.withoutgstspan').hide();
                    $('.withgstinput').attr('required', true);
                    $('.withoutgstinput').removeAttr('required');
                } else {
                    $('.withgstspan').hide();
                    $('.withoutgstspan').show();
                    $('.withoutgstinput').attr('required', true);
                    $('.withgstinput').removeAttr('required');
                }
            });

            //function for refresh tooltip after dynamic record append,delete 
            function managetooltip(){
                $('body').find('[data-toggle="tooltip"]').tooltip('dispose');
                // Reinitialize tooltips
                $('body').find('[data-toggle="tooltip"]').tooltip();
            };

            let allColumnData = []; // all column name with column details
            let allColumnNames = []; // all column name 
            let hiddencolumn = 0 ; // hidden columns 
            let formula = [];  // formula
            let columnlinks = []; // product column links 
            let prodctdata = [];
            let sgst,cgst,gst,currentcurrency,currentcurrencysymbol; 


            const API_TOKEN = "{{ session()->get('api_token') }}";
            const COMPANY_ID = "{{ session()->get('company_id') }}";
            const USER_ID = "{{ session()->get('user_id') }}";

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
                    $('html, body').animate({ scrollTop: 0 }, 1000);
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


            // fetch other settings like gst and inv number and inv date
            ajaxRequest('GET', "{{ route('getoverduedays.index') }}", { 
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.overdueday != '') {
                        var othersettingdata = response.overdueday[0];
                        sgst = othersettingdata['sgst'];
                        cgst = othersettingdata['cgst'];
                        gst = othersettingdata['gst'];
                        manualinvnumber = othersettingdata['invoice_number'];
                        manualinvdate = othersettingdata['invoice_date'];
                        totalgstpercentage = sgst + cgst ;
                        if (sgst % 1 === 0) { // Checks if sgst is an integer
                                $('#sgstpercentage').text(`(${sgst}.00 %)`);
                        } else {
                            $('#sgstpercentage').text(`(${sgst} %)`);
                        }
                        if (cgst % 1 === 0) { // Checks if cgst is an integer
                                $('#cgstpercentage').text(`(${cgst}.00 %)`);
                        } else {
                            $('#cgstpercentage').text(`(${cgst} %)`);
                        }
                        if (totalgstpercentage % 1 === 0) { // Checks if gst is an integer
                                $('#gstpercentage').text(`(${totalgstpercentage}.00 %)`);
                        } else {
                            $('#gstpercentage').text(`(${totalgstpercentage} %)`);
                        }
                        if(gst != 0){
                            $('#sgstline,#cgstline').hide();
                        }else{
                            $('#gstline').hide();
                        }

                        if(manualinvnumber == 0){
                            $('#inv_number_div').hide();
                        }

                        if(manualinvdate == 0){
                            $('#inv_date_div').hide();
                        }

                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                    loaderhide();
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });

            // fetch product columns link  
            ajaxRequest('GET', "{{ route('productcolumnmapping.index') }}", { 
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.productcolumnmapping != '') {
                    columnlinks = response.productcolumnmapping;
                }else if(response.status == 500){
                    Toast.fire({
                        icon: "error",
                        title: response.message
                    });
                    loaderhide();
                } 
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }); 


            // fetch invoice formula for calculation  
            ajaxRequest('GET', "{{ route('invoiceformula.index') }}", { 
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.invoiceformula != '') {
                            formula = response.invoiceformula;
                }else if(response.status == 500){
                    Toast.fire({
                        icon: "error",
                        title: response.message
                    });
                    loaderhide();
                } 
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }); 
            
            // fetch users own columnname and set it into table  
            ajaxRequest('GET', "{{ route('invoice.columnname') }}", { 
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                allColumnData = response.columnname; //store all column data for use globally in entire page
                //  if(allColumnData.length > 6){  
                //      $('.producttable').css('width',allColumnData.length * 200 + 'px');
                //  }
                if (response.status == 200 && response.columnname != '') {
                    // You can update your HTML with the data here if needed
                    $.each(response.columnname, function(key, value) {
                        $.each(value, function(innerKey, innerValue) {
                            if (innerKey === 'column_name') {
                                allColumnNames.push(innerValue); // store column name
                            }
                        });
                    });
                    
                    $('#columnname').prepend(
                        `${allColumnData.map(columnName => `<th style="width: ${columnName.column_width}%; ${columnName.is_hide ? 'display: none;' : ''}">${columnName.column_name}</th>`).join('')} 
                            <th>Amount</th>
                            <th>Move</th>
                            <th>Action</th>
                        `
                        );


                    // const targetRow = $('#add_new_div');
                    
                    // // Append input elements dynamically to the target row
                    // targetRow.append(`
                    //         <tr class="iteam_row_1">
                    //             ${allColumnData.map(columnData => {
                    //             var columnName = columnData.column_name.replace(/\s+/g, '_');
                    //                     var inputcontent = null ;
                    //                     ( columnData.is_hide === 1 )? hiddencolumn++ : '';
                    //                     if (columnData.column_type === 'time') {
                    //                         return `<td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} "><input type="time" name="${columnName}_1" id="${columnName}_1" class="form-control iteam_${columnName}"></td>`;
                    //                     } else if (columnData.column_type === 'number' || columnData.column_type === 'percentage' ||columnData.column_type === 'decimal') {
                    //                         return `<td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} "><input type="number" step="any" name="${columnName}_1" id="${columnName}_1" data-id="1" class="form-control iteam_${columnName} counttotal calculation"  min=0></td>`;
                    //                     } else if (columnData.column_type === 'longtext') {
                    //                         return `<td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} "><textarea name="${columnName}_1" id="${columnName}_1" class="form-control iteam_${columnName}" rows="1"></textarea></td>`;
                    //                     } else {
                    //                         return `<td class="invoicesubmit  ${(columnData.is_hide === 1)? 'd-none' :''} "><input type="text" name="${columnName}_1" id="${columnName}_1" class="form-control iteam_${columnName}" placeholder="${columnData.column_name}"></td>`;
                    //                     }
                    //                 }).join('')
                    //             }
                    //         <td><input type="number" step="any" data-id="1" class="form-control iteam_Amount changeprice calculation" id="Amount_1"
                    //                 placeholder="Amount" name='Amount_1' min=0 required>
                    //         </td>
                    //         <td>
                    //             <span class="table-up"><a href="#!" class="indigo-text"><i class="fa fa-long-arrow-up"
                    //                         aria-hidden="true"></i></a></span>
                    //             <span class="table-down"><a href="#!" class="indigo-text"><i
                    //                         class="fa fa-long-arrow-down" aria-hidden="true"></i></a></span>
                    //         </td>
                    //         <td>
                    //             <span class="duplicate-row" data-id="1">
                    //                 <button data-toggle="tooltip" data-placement="bottom" data-original-title="Duplicate Row" data-id="1" type="button"
                    //                     class="btn iq-bg-primary btn-rounded btn-sm mx-0 my-1">
                    //                     <i  class="ri-align-bottom"></i>
                    //                 </button>
                    //             </span>
                    //             <span class="remove-row" data-id="1">
                    //                 <button data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Row" data-id="1" type="button"
                    //                     class="btn iq-bg-danger btn-rounded btn-sm mx-0 my-1">
                    //                     <i class="ri-delete-bin-2-line"></i>
                    //                 </button>
                    //             </span>
                    //         </td>
                    //         </tr>
                    // `);
                    managetooltip();
                } else if(response.status == 500){
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                        loaderhide();
                }else {
                    $('#columnname').append(` <th>Name</th>
                    <th>Description</th>
                    <th>Quantity</th>`);
                }
                $('.automaticcolspan').attr('colspan',allColumnNames.length - hiddencolumn); // set autocolspan for title (subtotal,gst,total etc.. )
                $('.newdivautomaticcolspan').attr('colspan',allColumnNames.length - hiddencolumn); // set autocolspan for add new button row
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });  

            
            // Handle moving row up (editable-table)
            const $tableID = $("#table");
            $tableID.on("click", ".table-up", function() {
                const $row = $(this).closest("tr"); // Use closest to get the closest ancestor
                if ($row.index() !== 0) {
                    $row.prev().before($row); // Move the row
                }
            });

            // account data fetch and set account detials dropdown
            ajaxRequest('GET', "{{ route('invoice.bankacc') }}", { 
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.bank != '') {
                        var countrecords = Object.keys(response.bank).length;  
                        // You can update your HTML with the data here if needed
                        $.each(response.bank, function(key, value) {
                            var bankdetails = '';
                            if (value.account_no != null) {
                                bankdetails += value.account_no;
                            }

                            if (value.branch_name != null) {
                                if (bankdetails.length > 0) {
                                    bankdetails += '-'; // Add '-' between account no and branch name if both are present
                                }
                                bankdetails += value.branch_name;
                            } 
                            $('#acc_details').append(
                                `<option ${countrecords === 1 ? 'selected' : ''} value='${value.id}'>${bankdetails}</option>`
                            );
                        });
                    }else if(response.status == 500){
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                            loaderhide();
                    } else {
                        $('#acc_details').append(
                            `<option disabled '>No Data found </option>`
                        );
                    }
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });

            // get product data 
            ajaxRequest('GET', "{{ route('product.index') }}", { 
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.product != '') {
                    productdata = response.product;
                    // You can update your HTML with the data here if needed
                    $.each(response.product, function(key, value) { 
                        if(value.is_active == 1){ 
                            $('#product').append(
                                 `<option id="product_option_${value.id}" ${value.track_quantity == 0 ? 'disabled' : ''} value='${value.id}'>${value.name} ${value.track_quantity == 0 ? ' - inventory not tracked' : ''}</option>`
                            );
                        }
                    });  
                    $('#product').val('');
                    $('#product').select2({
                        placeholder: "Select Product",
                        search: true,
                    });  // search bar in product list 
                }else if(response.status == 500){
                    Toast.fire({
                        icon: "error",
                        title: response.message
                    });
                }else {
                    $('#product').append(`<option disabled selected>No Product found</option>`);
                }
                loaderhide();                   
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });
            
            // currency data fetch from country table and set currensy dropdown
            ajaxRequest('GET', "{{ route('country.index') }}", { 
                token: API_TOKEN,
                company_id: COMPANY_ID,
                user_id: USER_ID
            }).done(function(response) {
                if (response.status == 200 && response.country != '') {
                    // You can update your HTML with the data here if needed
                    $.each(response.country, function(key, value) {
                        $('#currency').append(
                            `<option data-symbol='${value.currency_symbol}' data-currency='${value.currency}' value='${value.id}'>${value.country_name} - ${value.currency_name} - ${value.currency} - ${value.currency_symbol} </option>`
                        );
                    });
                } else {
                    $('#currency').append(
                        `<option disabled '>No Data found </option>`);
                }
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            });
 
 
            loaderhide();

            // customer data fetch and set customer dropdown
            function customers(customerid = 0) { 
                loadershow();
                $('#customer').html(`
                   <option selected="" value=0 disabled=""> Select Customer</option>
                   <option value="add_customer" > Add New Customer </option>
                `);

                ajaxRequest('GET', "{{ route('customer.invoicecustomer') }}", { 
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    if (response.status == 200 && response.customer != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.customer, function(key, value) {
                            const customerDetails = [value.firstname, value.lastname, value.company_name, value.contact_no, value.email].filter(Boolean).join(' - ');

                            $('#customer').append(
                                `<option  data-gstno='${value.gst_no}' value='${value.id}'>${customerDetails}</option>`
                            )
                        });
                        $('#customer').val(customerid);
                        $('#customer').select2();  // search bar in customer list
                        if(customerid != 0){  // get customer data if its new added from add new customerform
                            loadershow();
                            var selectedOption = $('#customer').find('option:selected');
                        
                            var gstno = selectedOption.data('gstno');
                            if (gstno != null) {    // show gst line if customer has gst no other wise hide
                                $('#type').val(1);  // set gst type to gst
                                if(gst != 0){
                                    $('#sgstline,#cgstline').hide();
                                    $('#gstline').show();
                                }else{
                                    $('#gstline').hide();
                                    $('#sgstline,#cgstline').show();

                                }
                                dynamiccalculaton();
                            } else {
                                $('#type').val(2); // set gst type to withoutgst
                                $('#sgstline,#cgstline,#gstline').hide();
                                dynamiccalculaton();
                            }
                            let customerSearchUrl = "{{route('customer.search','__customerId__')}}".replace('__customerId__',customerid);

                            ajaxRequest('GET', customerSearchUrl, { 
                                token: API_TOKEN,
                                company_id: COMPANY_ID,
                                user_id: USER_ID
                            }).done(function(response) {
                                // You can update your HTML with the data here if needed
                                if (response.status == 200 && response.customers != '') {
                                    var countryid = response.customer.country_id
                                    if(countryid != null){
                                        $('#country').val(countryid);
                                        $('#currency').val(countryid);
                                        currentcurrency = $('#currency option:selected').data('currency');
                                        currentcurrencysymbol = $('#currency option:selected').data('symbol');
                                        $('.currentcurrencysymbol').text(currentcurrencysymbol);
                                    }
                                    
                                }else if(response.status == 500){
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                }
                                loaderhide();
                            }).fail(function(xhr) {
                                loaderhide();
                                handleAjaxError(xhr);
                            }); 
                        }
                    }else if(response.status == 500){
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#customer').append(`<option disabled '>No Data found </option>`);
                    }
                    loaderhide();                   
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });

                
            };

            customers();

            // fetch country id from selected customer and set input value for hidden file
            $('#customer').on('change', function() {
                loadershow();
                var selectedOption = $(this).find('option:selected');
               
                var customerid = $(this).val();
                if(customerid == 'add_customer'){
                    $('#exampleModalScrollable').modal('show');
                }
                var gstno = selectedOption.data('gstno');
                if (gstno != null) {
                    $('#type').val(1);
                    if(gst != 0){
                        $('#sgstline,#cgstline').hide();
                        $('#gstline').show();
                    }else{
                        $('#gstline').hide();
                        $('#sgstline,#cgstline').show();

                    }
                    dynamiccalculaton();
                } else {
                    $('#type').val(2);
                    $('#sgstline,#cgstline,#gstline').hide();
                    dynamiccalculaton();
                }
                customerSearchUrl = "{{route('customer.search','__customerId__')}}".replace('__customerId__',customerid);

                ajaxRequest('GET', customerSearchUrl, { 
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    // You can update your HTML with the data here if needed
                    if (response.status == 200 && response.customers != '') {
                        var countryid = response.customer.country_id
                        if(countryid != null){
                            $('#country').val(countryid);
                            $('#currency').val(countryid);
                            currentcurrency = $('#currency option:selected').data('currency');
                            currentcurrencysymbol = $('#currency option:selected').data('symbol');
                            $('.currentcurrencysymbol').text(currentcurrencysymbol);
                        }
                        
                    }else if(response.status == 500){
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                }); 
                
            });

            // append currency symbol according currency
            $('#currency').on('change',function(){
                currentcurrency = $('#currency option:selected').data('currency');
                currentcurrencysymbol = $('#currency option:selected').data('symbol');
                $('.currentcurrencysymbol').text(currentcurrencysymbol);
            });

            // call function to append row in table  on click add new button 
            var addname = 0; // for use to this variable for give to dynamic name and id to input 
            $('.add_div').on('click', function() {
                addname++; 
                adddiv();
            });

            // function for add new row in table 
            function adddiv() {
                hiddencolumn = 0;
                $('#add_new_div').append(
                    `<tr class="iteam_row_${addname}" data-inventory="null">
                        ${allColumnData.map(columnData => {
                                var columnName = columnData.column_name.replace(/\s+/g, '_');
                                var inputcontent = null ;
                                ( columnData.is_hide === 1 ) ? hiddencolumn++ : '';
                                if (columnData.column_type === 'time') {
                                    return `<td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} "><input type="time" name="${columnName}_${addname}" value="${columnData.default_value || ''}" id='${columnName}_${addname}' class="form-control iteam_${columnName} "></td>`;
                                } else if (columnData.column_type === 'number' || columnData.column_type === 'percentage' ||columnData.column_type === 'decimal') {
                                    return `<td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} "><input type="number" step="any" name="${columnName}_${addname}" value="${columnData.default_value || ''}" id='${columnName}_${addname}' data-id = ${addname} class="form-control iteam_${columnName} counttotal calculation"  min=0></td>`;
                                } else if (columnData.column_type === 'longtext') {
                                    return `<td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} "><textarea name="${columnName}_${addname}" value="${columnData.default_value || ''}" id='${columnName}_${addname}' class="form-control iteam_${columnName} " rows="1"> ${columnData.default_value || ''}</textarea></td>`;
                                } else {
                                    return `<td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} "><input type="text" name="${columnName}_${addname}" value="${columnData.default_value || ''}" id='${columnName}_${addname}' class="form-control iteam_${columnName} " placeholder="${columnData.column_name}"></td>`;
                                }
                            }).join('')
                        }
                        <td>
                            <input type="number" step="any" data-id =${addname} id="Amount_${addname}" min=0 name="Amount_${addname}" class="form-control iteam_Amount changeprice calculation" placeholder="Amount" required>
                        </td>   
                        <td>
                            <span class="table-up">
                                <a href="#!" class="indigo-text">
                                    <i class="fa fa-long-arrow-up" aria-hidden="true"></i>
                                </a>
                            </span>
                            <span class="table-down">
                                <a href="#!" class="indigo-text">
                                    <i class="fa fa-long-arrow-down" aria-hidden="true"></i>
                                </a>
                            </span>
                        </td>
                        <td>
                            <span class='duplicate-row' data-id = ${addname}> 
                                <button type="button"  data-toggle="tooltip" data-placement="bottom" data-original-title="Duplicate Row" class="btn iq-bg-primary btn-rounded btn-sm mx-0 my-1">
                                <i class="ri-align-bottom"></i>
                                </button>
                            </span>
                            <span class='remove-row' data-id=${addname}>
                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Row" class="btn iq-bg-danger btn-rounded btn-sm mx-0 my-1">
                                <i class="ri-delete-bin-2-line"></i>
                                </button>
                            </span>
                        </td>
                    </tr>`
                );
                managetooltip();
            };


            //call function duplicate row 
            $(document).on('click', '.duplicate-row', function() {
                var element = $(this);
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to add duplicate column?', // Text
                    'Yes, add',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        var id = element.data('id'); 
                        addname++;
                        var inventoryproduct = $(this).closest('tr').data('inventory'); 
                        duplicatediv(id,inventoryproduct);
                        if('#Amount_'.id != null || '#Amount_'.id != ''){
                            dynamiccalculaton();
                        } 
                        managetooltip();
                    } 
                );   
            });

            // function for duplicate row
            function duplicatediv(id,inventoryproduct){ 
                amountinput = $('#Amount_'+id);
                var productid = amountinput.data('product');
                $('#add_new_div').append(`
                    <tr class="iteam_row_${addname}" data-inventory="${inventoryproduct}">
                        ${allColumnData.map(columnData => {
                            var columnName = columnData.column_name.replace(/\s+/g, '_');
                                var inputcontent = null ;
                                if (columnData.column_type === 'time') {
                                    return `
                                        <td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} ">
                                            <input type="time" name="${columnName}_${addname}" value="${$('#' + columnName + '_' + id).val() || `${columnData.default_value || ''}`}" id='${columnName}_${addname}' class="form-control iteam_${columnName} ">
                                        </td>
                                    `;
                                } else if (columnData.column_type === 'number' || columnData.column_type === 'percentage' ||columnData.column_type === 'decimal') {
                                    return `
                                        <td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} ">
                                            <input type="number" step="any" name="${columnName}_${addname}" value="${$('#' + columnName + '_' + id).val() || `${columnData.default_value || ''}`}" id='${columnName}_${addname}' data-id = ${addname} class="form-control iteam_${columnName} counttotal calculation"  min=0>
                                        </td>
                                    `;
                                } else if (columnData.column_type === 'longtext') {
                                    return `
                                        <td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} ">
                                            <textarea name="${columnName}_${addname}" id='${columnName}_${addname}' class="form-control iteam_${columnName} " rows="1">${$('#' + columnName + '_' + id).val() || `${columnData.default_value || ''}`}</textarea>
                                        </td>
                                    `;
                                } else {
                                    return `
                                        <td class="invoicesubmit ${(columnData.is_hide === 1)?'d-none':''} ">
                                            <input type="text" name="${columnName}_${addname}" value="${$('#' + columnName + '_' + id).val() || `${columnData.default_value || ''}`}" id='${columnName}_${addname}' class="form-control iteam_${columnName} " placeholder="${columnData.column_name}">
                                        </td>
                                    `;
                                }
                            }).join('')
                        }
                        <td>
                            <input type="number" step="any" data-id = ${addname} id="Amount_${addname}" data-product="${productid || ''}" value="${$('#Amount' + '_' + id).val() || ''}" min=0 name="Amount_${addname}" class="form-control iteam_Amount changeprice calculation" placeholder="Amount" required>
                        </td>   
                        <td>
                            <span class="table-up">
                                <a href="#!" class="indigo-text">
                                    <i class="fa fa-long-arrow-up" aria-hidden="true"></i>
                                </a>
                            </span>
                            <span class="table-down">
                                <a href="#!" class="indigo-text">
                                    <i class="fa fa-long-arrow-down" aria-hidden="true"></i>
                                </a>
                            </span>
                        </td>
                        <td>
                            <span class='duplicate-row' data-id = ${addname}>
                                <button type="button"  data-toggle="tooltip" data-placement="bottom" data-original-title="Duplicate Row" class="btn iq-bg-primary btn-rounded btn-sm mx-0 my-1">
                                <i class="ri-align-bottom"></i>
                                </button>
                            </span>
                            <span class='remove-row' data-id=${addname}>
                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Row" class="btn iq-bg-danger btn-rounded btn-sm mx-0 my-1">
                                <i class="ri-delete-bin-2-line"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                `);
                managetooltip();
            };

            // delete row 
            $(document).on('click', '.remove-row', function() {
                var element = $(this); 
                showConfirmationDialog(
                    'Are you sure?',  // Title
                    'to delete this?', // Text
                    'Yes, delete',  // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        var row = element.closest("tr");
                        // Dispose of tooltips in the row to be removed
                        managetooltip(); 
                        // Remove the row
                        row.remove();
                        dynamiccalculaton(element); 
                    } 
                );   
            });

            // call function for gst or without gst counting
            $('#type').on('change', function() {
                if ($(this).val() == 2) {
                    $('#sgstline,#cgstline,#gstline').hide();
                    var totalval = $('#totalamount').val() ;
                    grandtotalval = Math.round($('#totalamount').val()); 
                    if(grandtotalval >= totalval){
                       var roundoffval = grandtotalval - totalval ;
                       if(roundoffval == 0){ 
                           $('#roundoff').val(`${roundoffval.toFixed(2)}`)
                       }else{ 
                           $('#roundoff').val(`+ ${roundoffval.toFixed(2)}`)
                       }
                    }else{
                        var roundoffval = totalval - grandtotalval ;
                        if(roundoffval == 0){
                            $('#roundoff').val(`${roundoffval.toFixed(2)}`)
                        }else{ 
                            $('#roundoff').val(`- ${roundoffval.toFixed(2)}`)
                        }
                    }
                    $('#grandtotal').val(grandtotalval);

                    if(gst != 0){
                        $('#gst').val(0);
                    }else{
                        $('#sgstline,#cgstline').val(0);
                    }
                    $('#gst').val(0);
                } else {
                    if(gst != 0){
                        $('#sgstline,#cgstline').hide();
                        $('#gstline').show();

                    }else{
                        $('#sgstline,#cgstline').show();
                        $('#gstline').hide();
                    }
                    dynamiccalculaton();
                }
            })

            $('#product').on('change', function () {
                selectedproduct = $(this).val();
                addname++;

                if(columnlinks.length > 1){  
                    if (productdata != null) {
                        $.each(productdata, function (key, value) {
                            if (value.id == selectedproduct) {
                                $('#add_new_div').append(
                                    `<tr class="iteam_row_${addname}" data-inventory="${value.id}">
                                        ${allColumnData.map(columnData => {
                                            var columnName = columnData.column_name.replace(/\s+/g, '_');
                                            var inputcontent = null;
    
                                            // Initialize productColumnValue as empty
                                            let productColumnValue = '';
    
                                            // Check if the column matches the invoice_column in columnlinks
                                            let matchingLink = columnlinks.find(link => link.invoice_column === columnData.column_name);
                                            if (matchingLink) {
                                                // Get the product data column value from the matching link
                                                productColumnValue = value[matchingLink.product_column] || '';  // Fallback to empty string if no value
                                                text = '' ;
                                                validation = '';
                                                if(matchingLink.product_column == 'quantity'){
                                                    var text = `<p class="text-muted">Available Stock : ${value.available_stock}</p>`;
                                                    if(value.continue_selling == 0){ 
                                                        validation = `max="${value.available_stock}"`; 
                                                    }
                                                }
                                            }
                                             
                                            // Handle different column types and set the value accordingly
                                            if (columnData.column_type === 'time') {
                                                inputcontent = `<td class="invoicesubmit ${(columnData.is_hide === 1) ? 'd-none' : ''} ">
                                                                    <input type="time" value="${productColumnValue || `${columnData.default_value || ''}`}" name="${columnName}_${addname}" id='${columnName}_${addname}' 
                                                                    class="form-control iteam_${columnName}">
                                                                </td>`;
                                            } else if (columnData.column_type === 'number' || columnData.column_type === 'percentage' || columnData.column_type === 'decimal') {
                                                inputcontent = `<td class="invoicesubmit ${(columnData.is_hide === 1) ? 'd-none' : ''} ">
                                                                    <input type="number" value="${productColumnValue  || `${columnData.default_value || ''}`}" step="any" name="${columnName}_${addname}" ${validation} id='${columnName}_${addname}' 
                                                                    data-id=${addname} class="form-control iteam_${columnName} counttotal calculation" min=0>
                                                                    ${text || ''}
                                                                </td>`;
                                            } else if (columnData.column_type === 'longtext') {
                                                inputcontent = `<td class="invoicesubmit ${(columnData.is_hide === 1) ? 'd-none' : ''} ">
                                                                    <textarea name="${columnName}_${addname}" id='${columnName}_${addname}' class="form-control iteam_${columnName}" rows="1">${productColumnValue  || `${columnData.default_value || ''}`}</textarea>
                                                                </td>`;
                                            } else {
                                                inputcontent = `<td class="invoicesubmit ${(columnData.is_hide === 1) ? 'd-none' : ''} ">
                                                                    <input type="text" value="${productColumnValue || `${columnData.default_value || ''}`}" name="${columnName}_${addname}" id='${columnName}_${addname}' 
                                                                    class="form-control iteam_${columnName}" placeholder="${columnData.column_name}">
                                                                </td>`;
                                            }
    
                                            return inputcontent;
                                        }).join('')}
                                        <td>
                                            <input type="number" step="any" data-id=${addname} id="Amount_${addname}" data-product="${value.id}" min=0 name="Amount_${addname}" 
                                            class="form-control iteam_Amount changeprice calculation" placeholder="Amount" required>
                                        </td>
                                        <td>
                                            <span class="table-up">
                                                <a href="#!" class="indigo-text">
                                                    <i class="fa fa-long-arrow-up" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                            <span class="table-down">
                                                <a href="#!" class="indigo-text">
                                                    <i class="fa fa-long-arrow-down" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class='remove-row' data-id=${addname}>
                                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Row" class="btn iq-bg-danger btn-rounded btn-sm mx-0 my-1">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </button>
                                            </span>
                                        </td>
                                    </tr>`
                                ); 
                                managetooltip();
                            }
                        });
                        dynamiccalculaton('#Amount_'+addname);
                    }
                
                }else{
                    Toast.fire({
                        icon: "info",
                        title: "Product column mapping required to use product"
                    });
                }
                $('#product').val('');
                $('#product').select2({
                    placeholder: "Select Product",
                    search: true,
                });  // search bar in product list 
            });

            // submit invoice form 
            $('#invoiceform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.error-msg').text('');
                
                const iteam_data = collectRowData();
                const invoiceDetails = collectInvoiceDetails();
                
                ajaxRequest('POST', "{{ route('invoice.store') }}", {
                    data: invoiceDetails,
                    iteam_data,
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).done(function(response) {
                    if (response.status === 200) {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        window.location = "{{ route('admin.invoice') }}";
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                        loaderhide();
                    }
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            });

            function collectRowData() {
                const iteam_data = [];
                $('tbody#add_new_div tr').each(function() {
                    const rowData = {};
                    const rowNumber = $(this).attr('class').match(/\d+/)[0];
                    const inventoryproduct = $(this).data('inventory'); // it would be product id or null

                    $.each(allColumnNames, function(key, columnName) {
                        const columnNameWithUnderscores = columnName.replace(/\s+/g, '_');
                        rowData[columnNameWithUnderscores] = $(this).find(`#${columnNameWithUnderscores}_${rowNumber}`).val();
                    }.bind(this));
                    
                    rowData['amount'] = $(this).find(`#Amount_${rowNumber}`).val(); 
                    rowData['inventoryproduct'] = inventoryproduct;  // add inventory product id
                    iteam_data.push(rowData);
                });
                return iteam_data;
            }

            function collectInvoiceDetails() {
                return {
                    country_id: $('#country').val(),
                    user_id: $('#created_by').val(),
                    company_id: $('#company_id').val(), 
                    bank_account: $('#acc_details').val(),
                    invoice_date: $('#invoice_date').val(),
                    inv_number: $('#inv_number').val(),
                    currency: $('#currency').val(),
                    customer: $('#customer').val(),
                    total_amount: $('#totalamount').val(),
                    grandtotal: $('#grandtotal').val(),
                    tax_type: $('#type').val(),
                    notes: $('#notes').val(),
                    gstsettings: {
                        sgst: sgst,
                        cgst: cgst,
                        gst: gst
                    },
                    ...getGSTValues()
                };
            }

            function getGSTValues() {
                if (gst === 0) {
                    return {
                        sgst: $('#sgst').val(),
                        cgst: $('#cgst').val()
                    };
                } else {
                    return {
                        gst: $('#gst').val()
                    };
                }
            } 
           
            //check inv number will not duplicate (on manual inv number)
            $('#inv_number').on('blur', function() {
                $('.error-msg').text('');
                const inv_number = $(this).val();

                ajaxRequest('GET', "{{ route('invoice.checkinvoicenumber') }}", {
                    inv_number,
                    token: API_TOKEN,
                    company_id: COMPANY_ID,
                    user_id: USER_ID
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                });
            });

            // redirect invoice list page on click cancel button
            $('#cancelbtn').on('click',function(){
                loadershow();
                window.location.href = "{{route('admin.invoice')}}" ;
            }); 

            // dynamic calculation function 
            function dynamiccalculaton(targetdata){
                var editid = $(targetdata).data('id');
                var rowData = {};
                $.each(allColumnNames, function (key, value) {
                    var modifiedValue = value.replace(/\s+/g, '_');
                    $(`table tr.iteam_row_${editid} td`).find(`input[type="number"]#${modifiedValue}_${editid}`).each(function () {
                        rowData[value] = $(this).val();
                    });
                });
             
                var iteam_data = new Array();
                iteam_data.push(rowData);

                function performCalculation(operation, value1, value2) {
                        switch (operation) {
                            case '*':
                                return  value1 * value2;
                             break;
                            case '/':
                                return value1 / value2;
                              break;
                            case '+':
                                return value1 + value2;
                             break;
                            case '-':
                                return value1 - value2;
                              break;
                            default:
                                return 0;
                        }
                }
                   
                var results = {};

                formula.forEach(function (formula) {
                    var value1 = parseFloat(iteam_data[0][formula.first_column]) || 0;
                    var value2 = parseFloat(iteam_data[0][formula.second_column]) || 0;
                        outputvalue =  performCalculation(formula.operation, value1, value2)
                    iteam_data[0][formula.output_column] = outputvalue.toFixed(2);
                    results[formula.output_column] = outputvalue.toFixed(2);
                    $(`#${formula.output_column}_${editid}`).val(outputvalue.toFixed(2));
                });
                var total = 0;
                $('input.changeprice').each(function(){
                    total += parseFloat($(this).val());
                });
                total = total.toFixed(2);
                if(!isNaN(total)){
                $('#totalamount').val(total);
                if($('#type').val()==1){ 
                    var sgstvalue = ((total * sgst) / 100);
                    var cgstvalue = ((total * cgst) / 100);
                    sgstvalue = sgstvalue.toFixed(2);
                    cgstvalue = cgstvalue.toFixed(2);
                    if(gst == 0){
                    $('#sgst').val(sgstvalue);
                    $('#cgst').val(cgstvalue);
                    }else{
                        $('#gst').val(parseFloat(sgstvalue) + parseFloat(cgstvalue));
                    }
                    var totalval = parseFloat(total) + parseFloat(sgstvalue) + parseFloat(cgstvalue);
                    grandtotalval = Math.round(totalval)
                    if(grandtotalval >= totalval){
                        roundoffval = (parseFloat(grandtotalval) - parseFloat(totalval)).toFixed(2) ;
                        if(roundoffval == 0){
                            $('#roundoff').val(`${roundoffval}`);
                        }else{ 
                            $('#roundoff').val(`+ ${roundoffval}`);
                        }
                    }else{
                        roundoffval = (parseFloat(totalval) - parseFloat(grandtotalval)).toFixed(2) ;
                        if(roundoffval == 0){
                            $('#roundoff').val(`${roundoffval}`);
                        }else{ 
                            $('#roundoff').val(`- ${roundoffval}`);
                        }  
                    }
                    $('#grandtotal').val(grandtotalval);
                }else{
                    $('#grandtotal').val(Math.round(total));
                    var totalval = parseFloat(total);
                    grandtotalval = Math.round(totalval)
                    if(grandtotalval >= totalval){
                        roundoffval = (parseFloat(grandtotalval) - parseFloat(totalval)).toFixed(2) ;
                        if(roundoffval == 0){
                            $('#roundoff').val(`${roundoffval}`);
                        }else{ 
                            $('#roundoff').val(`+ ${roundoffval}`);
                        }
                    }else{
                        roundoffval = (parseFloat(totalval) - parseFloat(grandtotalval)).toFixed(2) ;
                        if(roundoffval == 0){
                            $('#roundoff').val(`${roundoffval}`);
                        }else{ 
                            $('#roundoff').val(`- ${roundoffval}`);
                        }
                    }
                }
                }           
                         
            }

            // call dynamic calculation function on enter value in number input
            $(document).on('keyup change','.calculation',function(){ 
                dynamiccalculaton(this);
            });


            // for add new customer 

            // show country data in dropdown and set default value according logged in user
            ajaxRequest('GET', "{{ route('country.index') }}", { 
                token: API_TOKEN, 
            }).done(function(response) {
                if (response.status == 200 && response.country != '') {
                    // You can update your HTML with the data here if needed
                    $.each(response.country, function(key, value) {
                        $('#modal_country').append(
                            `<option value='${value.id}'> ${value.country_name}</option>`
                        )
                    });
                    country_id = "{{ Auth::guard('admin')->user()->country_id }}";
                    $('#modal_country').val(country_id);
                    loadstate();
                } else {
                    $('#modal_country').append(`<option> No Data Found</option>`);
                }
                loaderhide();
            }).fail(function(xhr) {
                loaderhide();
                handleAjaxError(xhr);
            }); 
 
            // load state in dropdown when country change
            $('#modal_country').on('change', function() {
                loadershow(); 
                $('#modal_city').html(`<option selected="" disabled="">Select your city</option>`);
                var country_id = $(this).val();
                loadstate(country_id);
            });

            // load state in dropdown and select state according to user
            function loadstate(id = 0) {
                $('#modal_state').html(`<option selected="" disabled="">Select your State</option>`);
                let stateSearchUrl = "{{route('state.search','id')}}".replace('id',id);
                var url = stateSearchUrl;
                if (id == 0) {
                    url = "{{ route('state.search', Auth::guard('admin')->user()->country_id ) }}";
                }
                ajaxRequest('GET', url, { 
                    token: API_TOKEN,  
                }).done(function(response) {
                    if (response.status == 200 && response.state != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.state, function(key, value) {
                            $('#modal_state').append(
                                `<option value='${value.id}'> ${value.state_name}</option>`
                            )
                        });
                        if (id == 0) {
                            state_id = "{{ Auth::guard('admin')->user()->state_id }}";
                            $('#modal_state').val(state_id);
                            loadcity();
                        }
                    } else {
                        $('#modal_state').append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                }); 
            }
 
            // load city in dropdown when state select/change
            $('#modal_state').on('change', function() {
                loadershow(); 
                var state_id = $(this).val();
                loadcity(state_id);
            });

            function loadcity(id = 0) {
                $('#modal_city').html(`<option selected="" disabled="">Select your City</option>`);
                citySearchUrl = "{{route('city.search','id')}}".replace('id',id);
                url = citySearchUrl;
                if (id == 0) {
                    url = "{{ route('city.search', Auth::guard('admin')->user()->state_id ) }}";
                }

                ajaxRequest('GET', url, { 
                    token: API_TOKEN, 
                }).done(function(response) {
                    if (response.status == 200 && response.city != '') {
                        // You can update your HTML with the data here if needed
                        $.each(response.city, function(key, value) {
                            $('#modal_city').append(
                                `<option value='${value.id}'> ${value.city_name}</option>`
                            )
                        });
                        if (id == 0) {
                            $('#modal_city').val("{{ Auth::guard('admin')->user()->city_id }}");
                        }
                    } else {
                        $('#modal_city').append(`<option> No Data Found</option>`);
                    }
                    loaderhide();
                }).fail(function(xhr) {
                    loaderhide();
                    handleAjaxError(xhr);
                }); 
            }

            // close pop up modal and reset new customer form
            $('#modal_cancelBtn').on('click',function(){
                $('#customerform')[0].reset();
                $('#exampleModalScrollable').modal('hide');
                $('#customer option:first').prop('selected', true);
            })

            // submit new customer  form
            $('#customerform').submit(function(event) {
                event.preventDefault();
                loadershow();
                $('.modal-error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customer.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#customerform')[0].reset();
                            $('#exampleModalScrollable').modal('hide');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            customers(response.customer_id); 
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

                        }else if(response.status == 500){
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
                        console.log(xhr.responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorcontainer;
                            $.each(errors, function(key, value) {
                                $('#modal-error-' + key).text(value[0]);
                                errorcontainer = '#modal-error-' + key ;
                            });
                            $('.modal-body').animate({
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
