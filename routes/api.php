<?php

use App\Models\User;
use App\Models\company;
use App\Models\api_authorization;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckServerKey;
use App\Http\Controllers\api\cityController;
use App\Http\Controllers\api\stateController;
use App\Http\Controllers\api\countryController;
use App\Http\Controllers\api\dbscriptController;
use App\Http\Controllers\api\otherapiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// middleware route group 

Route::middleware(['checkToken'])->group(function () {

    function getversion($controller)
    {
        $request = request();
        $user = null;
        $version = null;
        try {
            // Check if the user exists
            if ($request->has('user_id')) {
                // Retrieve the user if the user_id exists in the request
                $user = User::find($request->user_id);

                // If the user exists, retrieve the company's version
                if ($user) {
                    $version = Company::find($user->company_id);
                }
            }
            // Determine the version based on whether the user and version exist
            $versionexplode = $version ? $version->app_version : "v4_2_0";
        } catch (\Exception $e) {
            // Handle database connection or query exception
            // For example, log the error or display a friendly message 
            $versionexplode = "v4_2_0"; // Set a default version
        }


        return 'App\\Http\\Controllers\\' . $versionexplode . '\\api\\' . $controller;
    }
    // Default version is 1 if company not found

    // customer route
    $customerController = getversion('customerController');
    Route::controller($customerController)->group(function () {
        Route::get('/invoicecustomer', 'invoicecustomer')->name('customer.invoicecustomer');
        Route::get('/quotationcustomer', 'quotationcustomer')->name('customer.quotationcustomer');
        Route::get('/customers', 'datatable')->name('customer.datatable');
        Route::get('/customer', 'index')->name('customer.index');
        Route::post('/customer/insert', 'store')->name('customer.store');
        Route::get('/customer/search/{id}', 'show')->name('customer.search');
        Route::get('/customer/edit/{id}', 'edit')->name('customer.edit');
        Route::put('/customer/statusupdate/{id}', 'statusupdate')->name('customer.statusupdate');
        Route::put('/customer/update/{id}', 'update')->name('customer.update');
        Route::put('/customer/delete/{id}', 'destroy')->name('customer.delete');
    });

    // company route
    $companyController = getversion('companyController');
    Route::controller($companyController)->group(function () {
        Route::get('/companyprofile', 'companyprofile')->name('company.profile');
        Route::get('/companylist', 'companylistforversioncontrol')->name('company.companylist');
        Route::get('/company', 'index')->name('company.index');
        Route::get('/companydata', 'joincompany')->name('company.joindata');
        Route::post('/company/insert', 'store')->name('company.store');
        Route::get('/company/search/{id}', 'show')->name('company.search');
        Route::get('/company/edit/{id}', 'edit')->name('company.edit');
        Route::post('/company/update/{id}', 'update')->name('company.update');
        Route::post('/company/delete/{id}', 'destroy')->name('company.delete');
        Route::put('/company/statusupdate/{id}', 'statusupdate')->name('company.statusupdate');
    });

    // version control route
    $versionupdateController = getversion('versionupdateController');
    Route::controller($versionupdateController)->group(function () {
        Route::put('/company/versionupdate', 'updatecompanyversion')->name('company.versionupdate');
    });

    // product category route
    $productcategoryController = getversion('productcategoryController');
    Route::controller($productcategoryController)->group(function () {
        Route::get('/fetchproductcategory', 'fetchCategory')->name('productcategory.fetchCategory');
        Route::get('/productcategory', 'datatable')->name('productcategory.datatable');
        Route::get('/productcategory/list', 'index')->name('productcategory.index');
        Route::post('/productcategory/insert', 'store')->name('productcategory.store');
        Route::get('/productcategory/edit/{id}', 'edit')->name('productcategory.edit');
        Route::put('/productcategory/update/{id}', 'update')->name('productcategory.update');
        Route::put('/productcategory/statusupdate/{id}', 'statusupdate')->name('productcategory.statusupdate');
        Route::put('/productcategory/delete/{id}', 'destroy')->name('productcategory.delete');
    });

    // product route
    $productController = getversion('productController');
    Route::controller($productController)->group(function () {
        Route::get('/product', 'datatable')->name('product.datatable');
        Route::get('/product/list', 'index')->name('product.index');
        Route::post('/product/insert', 'store')->name('product.store');
        Route::get('/product/search/{id}', 'show')->name('product.search');
        Route::get('/product/edit/{id}', 'edit')->name('product.edit');
        Route::put('/product/update/{id}', 'update')->name('product.update');
        Route::put('/product/delete/{id}', 'destroy')->name('product.delete');

        Route::get('/productcolumnmapping', 'columnmappingindex')->name('productcolumnmapping.index');
        Route::post('/productcolumnmapping/insert', 'storecolumnmapping')->name('productcolumnmapping.store');
        Route::get('/productcolumnmapping/edit/{id}', 'editcolumnmapping')->name('productcolumnmapping.edit');
        Route::put('/productcolumnmapping/update/{id}', 'updatecolumnmapping')->name('productcolumnmapping.update');
        Route::put('/productcolumnmapping/delete/{id}', 'destroycolumnmapping')->name('productcolumnmapping.delete');
    });

    // temp file route
    $tempimageController = getversion('tempimageController');
    Route::controller($tempimageController)->group(function () {
        Route::post('/docupload', 'store')->name('temp.docupload');
        Route::delete('/docdelete', 'deleteFile')->name('temp.docdelete');
    });

    // temp file route
    $inventoryController = getversion('inventoryController');
    Route::controller($inventoryController)->group(function () {
        Route::get('/inventory', 'index')->name('inventory.index');
        Route::get('/incominginventory/{id}', 'incominginventory')->name('inventory.incominginventory');
        Route::put('/inventory/quantityupdate/{id}', 'quantityupdate')->name('inventory.quantityupdate');
        Route::put('/inventory/onhandquantityupdate/{id}', 'onhandquantityupdate')->name('inventory.onhandquantityupdate');
        Route::put('/inventory/availablequantityupdate/{id}', 'availablequantityupdate')->name('inventory.availablequantityupdate');
    });


    // user route
    $userController = getversion('userController');
    Route::controller($userController)->group(function () {
        Route::get('/userloginhistory', 'loginhistory')->name('user.userloginhistory');
        Route::get('/username', 'username')->name('user.username');
        Route::get('/userprofile', 'userprofile')->name('user.profile');
        Route::get('/customersupportuser', 'customersupportuser')->name('user.customersupportindex');
        Route::get('/leaduser', 'leaduser')->name('user.leaduserindex');
        Route::get('/invoiceuser', 'invoiceuser')->name('user.invoiceuserindex');
        Route::get('/techsupportuser', 'techsupportuser')->name('user.techsupportindex');
        Route::get('/user', 'index')->name('user.index');
        Route::get('/getuser', 'userdatatable')->name('user.datatable');
        Route::post('/user/insert', 'store')->name('user.store');
        Route::get('/user/search/{id}', 'show')->name('user.search');
        Route::get('/user/edit/{id}', 'edit')->name('user.edit');
        Route::put('/user/statusupdate/{id}', 'statusupdate')->name('user.statusupdate');
        Route::post('/user/update/{id}', 'update')->name('user.update');
        Route::put('/user/delete/{id}', 'destroy')->name('user.delete');
        Route::post('/user/changepassword/{id}', 'changepassword')->name('user.changepassword');
        Route::post('/user/setdefaultpage/{id}', 'setdefaultpage')->name('user.setdefaultpage');

        Route::get('/userrolepermission', 'userrolepermissionindex')->name('userrolepermission.index');
        Route::post('/userrolepermission/insert', 'storeuserrolepermission')->name('userrolepermission.store');
        Route::get('/getuserrolepermission', 'userrolepermissiondattable')->name('userrolepermission.datatable');
        Route::get('/userrolepermission/edit/{id}', 'edituserrolepermission')->name('userrolepermission.edit');
        Route::post('/userrolepermission/update/{id}', 'updateuserrolepermission')->name('userrolepermission.update');
        Route::put('/userrolepermission/statusupdate/{id}', 'userrolepermissionstatusupdate')->name('userrolepermission.statusupdate');
        Route::put('/userrolepermission/delete/{id}', 'userrolepermissiondestroy')->name('userrolepermission.delete');

    });


    // customer suppport route 
    $techsupportController = getversion('techsupportController');
    Route::controller($techsupportController)->group(function () {
        Route::get('/techsupport', 'index')->name('techsupport.index');
        Route::post('/techsupport/insert', 'store')->name('techsupport.store');
        Route::get('/techsupport/search/{id}', 'show')->name('techsupport.search');
        Route::get('/techsupport/edit/{id}', 'edit')->name('techsupport.edit');
        Route::post('/techsupport/update/{id}', 'update')->name('techsupport.update');
        Route::put('/techsupport/delete', 'destroy')->name('techsupport.delete');
        Route::put('/techsupport/changestatus', 'changestatus')->name('techsupport.changestatus');
    });

    //bank details route
    $bankdetailsController = getversion('bankdetailsController');
    Route::controller($bankdetailsController)->group(function () {
        Route::get('/bank', 'index')->name('bank.index');
        Route::post('/bank/insert', 'store')->name('bank.store');
        Route::put('/bank/update/{id}', 'update')->name('bank.update');
        Route::put('/bank/delete/{id}', 'destroy')->name('bank.delete');
    });



    //payment_details route 
    $PaymentController = getversion('PaymentController');
    Route::controller($PaymentController)->group(function () {
        Route::post('payment_details', 'store')->name('paymentdetails.store');
        Route::get('paymentdetail/{id}', 'paymentdetail')->name('paymentdetails.search');
        Route::get('pendingpayment/{id}', 'pendingpayment')->name('paymentdetails.pendingpayment');
    });

    // supplier route
    $supplierController = getversion('supplierController');
    Route::controller($supplierController)->group(function () {
        Route::get('/supplier', 'datatable')->name('supplier.datatable');
        Route::get('/supplier/list', 'index')->name('supplier.index');
        Route::post('/supplier/insert', 'store')->name('supplier.store');
        Route::get('/supplier/search/{id}', 'show')->name('supplier.search');
        Route::get('/supplier/edit/{id}', 'edit')->name('supplier.edit');
        Route::put('/supplier/statusupdate/{id}', 'statusupdate')->name('supplier.statusupdate');
        Route::put('/supplier/update/{id}', 'update')->name('supplier.update');
        Route::put('/supplier/delete/{id}', 'destroy')->name('supplier.delete');
    });


    // purchases route 
    $purchaseController = getversion('purchaseController');
    Route::controller($purchaseController)->group(function () {
        Route::get('/purchase', 'index')->name('purchase.index');
        Route::post('/purchase/insert', 'store')->name('purchase.store');
        Route::get('/purchase/search/{id}', 'show')->name('purchase.search');
        Route::get('/purchase/timeline/{id}', 'timeline')->name('purchase.timeline');
        Route::get('/purchase/edit/{id}', 'edit')->name('purchase.edit');
        Route::post('/purchase/update/{id}', 'update')->name('purchase.update');
        Route::post('/purchase/receiveinventory/{id}', 'receiveinventory')->name('purchase.receiveinventory');
        Route::put('/purchase/delete/{id}', 'destroy')->name('purchase.delete');
        Route::put('/purchase/statusupdate/{id}', 'changestatus')->name('purchase.changestatus');
    });

    // tbl_invoice_column route 
    $tblinvoicecolumnController = getversion('tblinvoicecolumnController');
    Route::controller($tblinvoicecolumnController)->group(function () {
        Route::get('/invoice/formulacolumnlist', 'formula')->name('invoicecolumn.formulacolumnlist');
        Route::get('/invoicecolumn', 'index')->name('invoicecolumn.index');
        Route::post('/invoicecolumn/insert', 'store')->name('invoicecolumn.store');
        Route::post('/invoicecolumn/columnorder', 'columnorder')->name('invoicecolumn.columnorder');
        Route::get('/invoicecolumn/search/{id}', 'show')->name('invoicecolumn.search');
        Route::get('/invoicecolumn/edit/{id}', 'edit')->name('invoicecolumn.edit');
        Route::post('/invoicecolumn/update/{id}', 'update')->name('invoicecolumn.update');
        Route::put('/invoicecolumn/delete/{id}', 'destroy')->name('invoicecolumn.delete');
        Route::put('/invoicecolumn/hide/{id}', 'hide')->name('invoicecolumn.hide');
    });

    // tbl_invoice_formula route 
    $tblinvoiceformulaController = getversion('tblinvoiceformulaController');
    Route::controller($tblinvoiceformulaController)->group(function () {
        Route::get('/invoiceformula', 'index')->name('invoiceformula.index');
        Route::post('/invoiceformula/insert', 'store')->name('invoiceformula.store');
        Route::post('/invoiceformula/formulaorder', 'formulaorder')->name('invoiceformula.formulaorder');
        Route::get('/invoiceformula/search/{id}', 'show')->name('invoiceformula.search');
        Route::get('/invoiceformula/edit/{id}', 'edit')->name('invoiceformula.edit');
        Route::post('/invoiceformula/update/{id}', 'update')->name('invoiceformula.update');
        Route::put('/invoiceformula/delete/{id}', 'destroy')->name('invoiceformula.delete');
    });

    $invoiceController = getversion('invoiceController');
    //invoice route
    Route::controller($invoiceController)->group(function () {
        Route::get('/totalinvoice', 'totalInvoice')->name('invoice.totalinvoice');
        Route::get('/checkinvoicenumber', 'checkinvoicenumber')->name('invoice.checkinvoicenumber');
        Route::get('/currency', 'currency')->name('invoice.currency');
        Route::get('/bdetails', 'bdetails')->name('invoice.bankacc');
        Route::get('/columnname', 'columnname')->name('invoice.columnname');
        Route::get('/numbercolumnname', 'numbercolumnname')->name('invoice.numbercolumnname');
        Route::get('/inv_list', 'inv_list')->name('invoice.inv_list');
        Route::put('/inv_status/{id}', 'status')->name('invoice.status');
        Route::get('/invoice/{id}', 'index')->name('invoice.index');
        Route::post('/invoice/insert', 'store')->name('invoice.store');
        Route::get('/invoice/search/{id}', 'show')->name('invoice.search');
        Route::get('/invoice/inv_details/{id}', 'inv_details')->name('invoice.inv_details');
        Route::get('/invoice/edit/{id}', 'edit')->name('invoice.edit');
        Route::put('/invoice/update/{id}', 'update')->name('invoice.update');
        Route::put('/invoice/delete/{id}', 'destroy')->name('invoice.delete');
        Route::get('status_list', 'status_list')->name('invoice.status_list');
        Route::get('chart', 'monthlyInvoiceChart')->name('invoice.chart');
        Route::get('/reportlogs', 'reportlogsdetails')->name('report.logs');
        Route::put('/reportlog/delete/{id}', 'reportlogdestroy')->name('report.delete');
    });

    // lead route 
    $tblleadController = getversion('tblleadController');
    Route::controller($tblleadController)->group(function () {
        Route::get('/leadstatusname', 'leadstatusname')->name('lead.leadstatusname');
        Route::get('/leadstagename', 'leadstagename')->name('lead.leadstagename');
        Route::get('lead/monthlychart', 'monthlyLeadChart')->name('lead.chart');
        Route::get('/lead/piechart', 'piechart')->name('lead.piechart');
        Route::get('lead/stagechart', 'leadStageChart')->name('lead.stagechart');
        Route::get('/lead/sourcepiechart', 'sourcepiechart')->name('lead.sourcepiechart');
        Route::get('/leaddashboardhelper', 'leaddashboardhelper')->name('lead.leaddashboardhelper');
        Route::get('/lead/newleadcount', 'newleadcount')->name('lead.newleadcount');
        Route::get('/lead/userwiseleadcount', 'userwiseleadcount')->name('lead.userwiseleadcount');
        Route::get('/lead/userleadsummary', 'userLeadSummary')->name('lead.userleadsummary');
        Route::get('/lead/followupdueleads', 'followupDueLeads')->name('lead.followupdueleads');
        Route::get('/lead/recentactivity', 'leadrecentactivityindex')->name('lead.recentactivityindex');
        Route::put('/lead/recentactivity/delete', 'leadrecentactivitydestroy')->name('lead.recentactivitydelete');

        Route::get('/lead', 'index')->name('lead.index');
        Route::post('/lead/insert', 'store')->name('lead.store');
        Route::get('/lead/sourcecolumn', 'sourcevalue')->name('lead.sourcecolumn');
        Route::get('/lead/search/{id}', 'show')->name('lead.search');
        Route::get('/lead/edit/{id}', 'edit')->name('lead.edit');
        Route::post('/lead/update/{id}', 'update')->name('lead.update');
        Route::put('/lead/delete', 'destroy')->name('lead.delete');
        Route::put('/lead/buldelete', 'bulkdestroy')->name('lead.bulkdelete');
        Route::put('/lead/changestatus', 'changestatus')->name('lead.changestatus');
        Route::put('/lead/changeleadstage', 'changeleadstage')->name('lead.changeleadstage');
    });

    // lead call history route
    $tblleadhistoryController = getversion('tblleadhistoryController');
    Route::controller($tblleadhistoryController)->group(function () {
        Route::post('/leadhistory/insert', 'store')->name('leadhistory.store');
        Route::get('/leadhistory/search/{id}', 'show')->name('leadhistory.search');
        Route::get('/lead/calendar', 'getcalendardata')->name('lead.getcalendardata');
    });

    // lead api server key route
    $apiserverkeyController = getversion('apiserverkeyController');
    Route::controller($apiserverkeyController)->group(function () {
        Route::get('/other/api/serverkey', 'index')->name('other.getapiserverkey');
        Route::post('/other/api/serverkey/generate', 'store')->name('other.generateserverkey');
        Route::post('/other/api/serverkey/update/{id}', 'update')->name('other.updateserverkey');
        Route::put('/other/api/serverkey/delete/{id}', 'destroy')->name('other.deleteserverkey');
    });

    // customer suppport route 
    $customersupportController = getversion('customersupportController');
    Route::controller($customersupportController)->group(function () {
        Route::get('/customersupport', 'index')->name('customersupport.index');
        Route::post('/customersupport/insert', 'store')->name('customersupport.store');
        Route::get('/customersupport/search/{id}', 'show')->name('customersupport.search');
        Route::get('/customersupport/edit/{id}', 'edit')->name('customersupport.edit');
        Route::post('/customersupport/update/{id}', 'update')->name('customersupport.update');
        Route::put('/customersupport/delete', 'destroy')->name('customersupport.delete');
        Route::put('/customersupport/changestatus', 'changestatus')->name('customersupport.changestatus');
        Route::put('/customersupport/changeleadstage', 'changeleadstage')->name('customersupport.changeleadstage');
    });

    // customer support call history route
    $customersupporthistoryController = getversion('customersupporthistoryController');
    Route::controller($customersupporthistoryController)->group(function () {
        Route::post('/customersupporthistory/insert', 'store')->name('customersupporthistory.store');
        Route::get('/customersupporthistory/search/{id}', 'show')->name('customersupporthistory.search');
    });

    //common controller route
    $commonController = getversion('commonController');
    Route::controller($commonController)->group(function () {
        Route::get('/getdbname/{id}', 'dbname')->name('getdbanme');
    });

    $tblinvoiceothersettingController = getversion('tblinvoiceothersettingController');
    Route::controller($tblinvoiceothersettingController)->group(function () {
        Route::get('/getoverduedays', 'getoverduedays')->name('getoverduedays.index');
        Route::get('/invoicenumberpatterns', 'invoicenumberpatternindex')->name('invoicenumberpatterns.index');
        Route::post('/getoverduedays/update/{id}', 'overduedayupdate')->name('getoverduedays.update');
        Route::post('/invoicepattern/update', 'invoicepatternstore')->name('invoicepattern.store');
        Route::post('/gstsettings/update/{id}', 'gstsettingsupdate')->name('gstsettingsupdate.update');
        Route::get('/termsandconditions', 'termsandconditionsindex')->name('termsandconditions.index');
        Route::post('/termsandconditions/insert', 'invoicetcstore')->name('termsandconditions.store');
        Route::get('/termsandconditions/edit/{id}', 'tcedit')->name('termsandconditions.edit');
        Route::post('/termsandconditions/update/{id}', 'tcupdate')->name('termsandconditions.update');
        Route::put('/termsandconditions/statusupdate/{id}', 'tcstatusupdate')->name('termsandconditions.statusupdate');
        Route::put('/termsandconditions/delete/{id}', 'tcdestroy')->name('termsandconditions.delete');
        Route::post('/customerid', 'customeridstore')->name('customerid.store');
        Route::post('/manualinvoicenumber', 'manual_invoice_number')->name('othersettings.updateinvoicenumberstatus');
        Route::post('/manualinvoicedate', 'manual_invoice_date')->name('othersettings.updateinvoicedatestatus');
    });


    // reminder modules route 
    // reminder customer route
    $remindercustomerController = getversion('remindercustomerController');
    Route::controller($remindercustomerController)->group(function () {
        Route::get('/remidercustomer/count', 'counttotalcustomer')->name('remindercustomer.count');
        Route::get('/remindercustomer/customerreminders/{id}', 'customerreminders')->name('remindercustomer.customerreminders');
        Route::get('/remindercustomer/customers', 'remindercustomer')->name('remindercustomer.customers');
        Route::get('/remindercustomer/area', 'area')->name('remindercustomer.area');
        Route::get('/remindercustomer/city', 'cities')->name('remindercustomer.city');
        Route::get('/remindercustomer', 'index')->name('remindercustomer.index');
        Route::post('/remindercustomer/insert', 'store')->name('remindercustomer.store');
        Route::get('/remindercustomer/search/{id}', 'show')->name('remindercustomer.search');
        Route::get('/remindercustomer/edit/{id}', 'edit')->name('remindercustomer.edit');
        Route::put('/remindercustomer/statusupdate/{id}', 'statusupdate')->name('remindercustomer.statusupdate');
        Route::put('/remindercustomer/update/{id}', 'update')->name('remindercustomer.update');
        Route::put('/remindercustomer/delete/{id}', 'destroy')->name('remindercustomer.delete');
    });

    // lead route 
    $reminderController = getversion('reminderController');
    Route::controller($reminderController)->group(function () {
        Route::get('/reminder/reminderbydays', 'getRemindersByDays')->name('reminder.reminderbydays');
        Route::get('/reminder', 'index')->name('reminder.index');
        Route::post('/reminder/insert', 'store')->name('reminder.store');
        Route::get('/reminder/search/{id}', 'show')->name('reminder.search');
        Route::get('/reminder/edit/{id}', 'edit')->name('reminder.edit');
        Route::post('/reminder/update/{id}', 'update')->name('reminder.update');
        Route::put('/reminder/delete', 'destroy')->name('reminder.delete');
        Route::put('/reminder/changestatus', 'changestatus')->name('reminder.changestatus');
        Route::get('/reminder/status_list', 'status_list')->name('reminder.status_list');
        Route::get('/reminder/chart', 'monthlyInvoiceChart')->name('reminder.chart');
    });

    // blog category route
    $blogcategoryController = getversion('blogcategoryController');
    Route::controller($blogcategoryController)->group(function () {
        Route::get('/blogcategory', 'index')->name('blogcategory.index');
        Route::get('/getblogcategory', 'blogcategorydatatable')->name('blogcategory.datatable');
        Route::post('/blogcategory/insert', 'store')->name('blogcategory.store');
        Route::get('/blogcategory/edit/{id}', 'edit')->name('blogcategory.edit');
        Route::post('/blogcategory/update/{id}', 'update')->name('blogcategory.update');
        Route::put('/blogcategory/delete/{id}', 'destroy')->name('blogcategory.delete');
    });

    // blog tag route
    $blogtagController = getversion('blogtagController');
    Route::controller($blogtagController)->group(function () {
        Route::get('/blogtag', 'index')->name('blogtag.index');
        Route::get('/getblogtag', 'blogtagdatatable')->name('blogtag.datatable');
        Route::post('/blogtag/insert', 'store')->name('blogtag.store');
        Route::get('/blogtag/edit/{id}', 'edit')->name('blogtag.edit');
        Route::post('/blogtag/update/{id}', 'update')->name('blogtag.update');
        Route::put('/blogtag/delete/{id}', 'destroy')->name('blogtag.delete');
    });

    // blog  route
    $blogController = getversion('blogController');
    Route::controller($blogController)->group(function () {
        Route::get('/getslug', 'getSlug')->name('blog.getslug');
        Route::get('/blog', 'index')->name('blog.index');
        Route::get('/getblog', 'blogdatatable')->name('blog.datatable');
        Route::post('/blog/insert', 'store')->name('blog.store');
        Route::get('/blog/search/{slug}', 'show')->name('blog.search');
        Route::get('/blog/edit/{id}', 'edit')->name('blog.edit');
        Route::post('/blog/update/{id}', 'update')->name('blog.update');
        Route::put('/blog/delete/{id}', 'destroy')->name('blog.delete');
    });

    // api_authorization  route
    $apiauthorizationController = getversion('apiauthorizationController');
    Route::controller($apiauthorizationController)->group(function () {
        Route::get('/apiauthorization', 'index')->name('apiauthorization.index');
        Route::post('/apiauthorization/insert', 'store')->name('apiauthorization.store');
        Route::get('/apiauthorization/edit/{id}', 'edit')->name('apiauthorization.edit');
        Route::post('/apiauthorization/update/{id}', 'update')->name('apiauthorization.update');
        Route::put('/apiauthorization/delete/{id}', 'destroy')->name('apiauthorization.delete');
    });

    // tbl_quotation_column route 
    $tblquotationcolumnController = getversion('tblquotationcolumnController');
    Route::controller($tblquotationcolumnController)->group(function () {
        Route::get('/quotation/formulacolumnlist', 'formula')->name('quotationcolumn.formulacolumnlist');
        Route::get('/quotationcolumn', 'index')->name('quotationcolumn.index');
        Route::post('/quotationcolumn/insert', 'store')->name('quotationcolumn.store');
        Route::post('/quotationcolumn/columnorder', 'columnorder')->name('quotationcolumn.columnorder');
        Route::get('/quotationcolumn/search/{id}', 'show')->name('quotationcolumn.search');
        Route::get('/quotationcolumn/edit/{id}', 'edit')->name('quotationcolumn.edit');
        Route::post('/quotationcolumn/update/{id}', 'update')->name('quotationcolumn.update');
        Route::put('/quotationcolumn/delete/{id}', 'destroy')->name('quotationcolumn.delete');
        Route::put('/quotationcolumn/hide/{id}', 'hide')->name('quotationcolumn.hide');
    });


    // tbl_quotation_formula route 
    $tblquotationformulaController = getversion('tblquotationformulaController');
    Route::controller($tblquotationformulaController)->group(function () {
        Route::get('/quotationformula', 'index')->name('quotationformula.index');
        Route::post('/quotationformula/insert', 'store')->name('quotationformula.store');
        Route::post('/quotationformula/formulaorder', 'formulaorder')->name('quotationformula.formulaorder');
        Route::get('/quotationformula/search/{id}', 'show')->name('quotationformula.search');
        Route::get('/quotationformula/edit/{id}', 'edit')->name('quotationformula.edit');
        Route::post('/quotationformula/update/{id}', 'update')->name('quotationformula.update');
        Route::put('/quotationformula/delete/{id}', 'destroy')->name('quotationformula.delete');
    });

    $tblquotationothersettingController = getversion('tblquotationothersettingController');
    Route::controller($tblquotationothersettingController)->group(function () {
        Route::get('/quotation/getoverduedays', 'getoverduedays')->name('getquotationoverduedays.index');
        Route::get('/quotationnumberpatterns', 'quotationnumberpatternindex')->name('quotationnumberpatterns.index');
        Route::post('/quotation/getoverduedays/update/{id}', 'overduedayupdate')->name('getquotationoverduedays.update');
        Route::post('/quotationpattern/update', 'quotationpatternstore')->name('quotationpattern.store');
        Route::post('/quotation/gstsettings/update/{id}', 'gstsettingsupdate')->name('quotationgstsettingsupdate.update');
        Route::get('/quotation/termsandconditions', 'termsandconditionsindex')->name('quotationtermsandconditions.index');
        Route::post('/quotation/termsandconditions/insert', 'quotationtcstore')->name('quotationtermsandconditions.store');
        Route::get('/quotation/termsandconditions/edit/{id}', 'tcedit')->name('quotationtermsandconditions.edit');
        Route::post('/quotation/termsandconditions/update/{id}', 'tcupdate')->name('quotationtermsandconditions.update');
        Route::put('/quotation/termsandconditions/statusupdate/{id}', 'tcstatusupdate')->name('quotationtermsandconditions.statusupdate');
        Route::put('/quotation/termsandconditions/delete/{id}', 'tcdestroy')->name('quotationtermsandconditions.delete');
        Route::post('/manualquotationnumber', 'manual_quotation_number')->name('othersettings.updatequotationnumberstatus');
        Route::post('/manualquotationdate', 'manual_quotation_date')->name('othersettings.updatequotationdatestatus');
    });

    //quotation route
    $quotationController = getversion('quotationController');
    Route::controller($quotationController)->group(function () {
        Route::get('/quotation/totalquotation', 'totalQuotation')->name('quotation.totalquotation');
        Route::get('/quotation/status_list', 'status_list')->name('quotation.status_list');
        Route::get('/quotation/chart', 'monthlyQuotationChart')->name('quotation.chart');
        Route::get('/quotation/checkquotationnumber', 'checkquotationnumber')->name('quotation.checkquotationnumber');
        Route::get('/quotation/currency', 'currency')->name('quotation.currency');
        Route::get('/quotation/columnname', 'columnname')->name('quotation.columnname');
        Route::get('/quotation/numbercolumnname', 'numbercolumnname')->name('quotation.numbercolumnname');
        Route::get('/quotation/quotation_list', 'quotation_list')->name('quotation.quotation_list');
        Route::put('/quotation_status/{id}', 'status')->name('quotation.status');
        Route::get('/quotation/{id}', 'index')->name('quotation.index');
        Route::post('/quotation/insert', 'store')->name('quotation.store');
        Route::get('/quotation/search/{id}', 'show')->name('quotation.search');
        Route::get('/quotation/quotation_details/{id}', 'quotation_details')->name('quotation.quotation_details');
        Route::get('/quotation/edit/{id}', 'edit')->name('quotation.edit');
        Route::put('/quotation/update/{id}', 'update')->name('quotation.update');
        Route::put('/quotation/delete/{id}', 'destroy')->name('quotation.delete');
        Route::get('/quotation/remarks/{id}', 'getquotationremarks')->name('quotation.getquotationremarks');
        Route::post('/quotation/updateremarks', 'updatequotationremarks')->name('quotation.updatequotationremarks');
    });


    // consignee route
    $consigneeController = getversion('consigneeController');
    Route::controller($consigneeController)->group(function () {
        Route::get('/getconsignee', 'consigneelist')->name('consignee.getconsigneelist');
        Route::get('/consignee', 'index')->name('consignee.index');
        Route::post('/consignee/insert', 'store')->name('consignee.store');
        Route::get('/consignee/search/{id}', 'show')->name('consignee.search');
        Route::get('/consignee/edit/{id}', 'edit')->name('consignee.edit');
        Route::put('/consignee/statusupdate/{id}', 'statusupdate')->name('consignee.statusupdate');
        Route::put('/consignee/update/{id}', 'update')->name('consignee.update');
        Route::put('/consignee/delete/{id}', 'destroy')->name('consignee.delete');
    });

    // consignor route
    $consignorController = getversion('consignorController');
    Route::controller($consignorController)->group(function () {
        Route::get('/getconsignor', 'consignorlist')->name('consignor.getconsignorlist');
        Route::get('/consignor', 'index')->name('consignor.index');
        Route::post('/consignor/insert', 'store')->name('consignor.store');
        Route::get('/consignor/search/{id}', 'show')->name('consignor.search');
        Route::get('/consignor/edit/{id}', 'edit')->name('consignor.edit');
        Route::put('/consignor/statusupdate/{id}', 'statusupdate')->name('consignor.statusupdate');
        Route::put('/consignor/update/{id}', 'update')->name('consignor.update');
        Route::put('/consignor/delete/{id}', 'destroy')->name('consignor.delete');
    });

    // consignor copy route
    $consignorcopyController = getversion('consignorcopyController');
    Route::controller($consignorcopyController)->group(function () {
        Route::get('/consignorcopy', 'index')->name('consignorcopy.index');
        Route::post('/consignorcopy/insert', 'store')->name('consignorcopy.store');
        Route::get('/consignorcopy/search/{id}', 'show')->name('consignorcopy.search');
        Route::get('/consignorcopy/edit/{id}', 'edit')->name('consignorcopy.edit');
        Route::put('/consignorcopy/updatetandc/{id}', 'updatetandc')->name('consignorcopy.updatetandc');
        Route::put('/consignorcopy/update/{id}', 'update')->name('consignorcopy.update');
        Route::put('/consignorcopy/delete/{id}', 'destroy')->name('consignorcopy.delete');
    });

    // logistic other settings route
    $logisticothersettingsController = getversion('logisticothersettingsController');
    Route::controller($logisticothersettingsController)->group(function () {
        Route::get('/getlogisticothersettings', 'getlogisticothersettings')->name('getlogisticothersettings');
        Route::post('/logistic/othersettings', 'logisticothersettingsstore')->name('logisticothersettings.store');

        Route::get('/consignorcopy/termsandconditions', 'termsandconditionsindex')->name('consignorcopytermsandconditions.index');
        Route::post('/consignorcopy/termsandconditions/insert', 'consignorcopytcstore')->name('consignorcopytermsandconditions.store');
        Route::get('/consignorcopy/termsandconditions/edit/{id}', 'tcedit')->name('consignorcopytermsandconditions.edit');
        Route::post('/consignorcopy/termsandconditions/update/{id}', 'tcupdate')->name('consignorcopytermsandconditions.update');
        Route::put('/consignorcopy/termsandconditions/statusupdate/{id}', 'tcstatusupdate')->name('consignorcopytermsandconditions.statusupdate');
        Route::put('/consignorcopy/termsandconditions/delete/{id}', 'tcdestroy')->name('consignorcopytermsandconditions.delete');
       
        Route::post('/consignorcopy/consignmentnotenumber', 'consignmentnotenumberstore')->name('consignmentnotenumber.store');
       
        Route::get('/watermark', 'getwatermark')->name('watermark.index');
        Route::post('/watermark/update', 'updatewatermark')->name('watermark.update');


    });

    // system monitor settings route (Developer tools)
    $systemmonitorController = getversion('systemmonitorController');
    Route::controller($systemmonitorController)->group(function () {
        Route::get('/developer/slowpages', 'slowpages')->name('getslowpages');
        Route::put('/developer/slowpages/delete/{id}', 'slowpagedestroy')->name('slowpage.delete');
        Route::get('/developer/errorlogs', 'geterrorlogfiles')->name('geterrorlogs');
        Route::get('/developer/errorlogs/download/{filename}', 'downloaderrorlog')->name('downloaderrorlog');
        Route::get('/developer/cronjobs', 'cronjobs')->name('getcronjobs');
        Route::get('/developer/recentactivitydata', 'recentactivitydata')->name('getrecentactivitydata');
        Route::post('/developer/recentactivitydata/insert', 'storerecentactivitydata')->name('recentactivitydata.store');
        Route::get('/developer/recentactivitydata/edit/{id}', 'editrecentactivitydata')->name('recentactivitydata.edit');
        Route::put('/developer/recentactivitydata/update/{id}', 'updaterecentactivitydata')->name('recentactivitydata.update');
        Route::put('/developer/recentactivitydata/delete/{id}', 'destroyrecentactivitydata')->name('recentactivitydata.delete');
    });

});

//country route
Route::controller(countryController::class)->group(function () {
    Route::get('/country', 'index')->name('country.index');
    Route::post('/country/insert', 'store')->name('country.store');
    Route::get('/country/search/{id}', 'show')->name('country.search');
    Route::get('/country/edit/{id}', 'edit')->name('country.edit');
    Route::put('/country/update/{id}', 'update')->name('country.update');
    Route::put('/country/delete/{id}', 'destroy')->name('country.delete');
});

//state route
Route::controller(stateController::class)->group(function () {
    Route::get('/state', 'index')->name('state.index');
    Route::post('/state/insert', 'store')->name('state.store');
    Route::get('/state/search/{id}', 'show')->name('state.search');
    Route::get('/state/edit/{id}', 'edit')->name('state.edit');
    Route::put('/state/update/{id}', 'update')->name('state.update');
    Route::put('/state/delete/{id}', 'destroy')->name('state.delete');
});

//city route
Route::controller(cityController::class)->group(function () {
    Route::get('/city', 'index')->name('city.index');
    Route::post('/city/insert', 'store')->name('city.store');
    Route::get('/city/search/{id}', 'show')->name('city.search');
    Route::get('/city/edit/{id}', 'edit')->name('city.edit');
    Route::put('/city/update/{id}', 'update')->name('city.update');
    Route::put('/city/delete/{id}', 'destroy')->name('city.delete');
});


Route::get('/dbscript', [dbscriptController::class, 'dbscript'])->name('dbscript');


Route::middleware([CheckServerKey::class])->group(function () {
    Route::controller(otherapiController::class)->group(function () {
        Route::post('/OtherApi/AddNewlead', 'newlead')->name('otherapi.addnewlead');
        Route::post('/OtherApi/Addlead', 'oceanlead')->name('ocean.lead');
        Route::get('/OtherApi/Blog', 'blog')->name('otherapi.blog');
        Route::get('/OtherApi/Blog/Search/{slug}', 'blogdetails')->name('otherapi.blogdetails');
    });
});

// Route that does NOT use CheckServerKey
Route::post('/track-activity', [otherapiController::class, 'store'])->name('track.activity');

