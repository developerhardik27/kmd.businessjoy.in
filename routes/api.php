<?php

use App\Http\Controllers\api\cityController;
use App\Http\Controllers\api\countryController;
use App\Http\Controllers\api\dbscriptController;
use App\Http\Controllers\api\mailcontroller;
use App\Http\Controllers\api\otherapiController;
use App\Http\Controllers\api\stateController;
use App\Models\api_authorization;
use App\Models\company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// mail route
Route::get('/sendmail', [mailcontroller::class, 'sendmail']);

// middleware route group 

Route::middleware(['dynamic.version','checkToken'])->group(function () {

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
            } elseif ($request->has('site_key') && $request->has('server_key')) {
                $company_id = api_authorization::where('site_key', $request->site_key)
                    ->where('server_key', $request->server_key)
                    ->where('is_active', 1)
                    ->where('is_deleted', 0)
                    ->select('company_id')
                    ->first();

                // If the user exists, retrieve the company's version
                if ($company_id) {
                    $version = Company::find($company_id->company_id);
                }

            }



            // Determine the version based on whether the user and version exist
            $versionexplode = $version ? $version->app_version : "v1_0_0";



        } catch (\Exception $e) {
            // Handle database connection or query exception
            // For example, log the error or display a friendly message 
            $versionexplode = "v1_0_0"; // Set a default version
        }


        return 'App\\Http\\Controllers\\' . $versionexplode . '\\api\\' . $controller;
    }
    // Default version is 1 if company not found

    // customer route
    $customerController = getversion('customerController');
    Route::controller($customerController)->group(function () {
        Route::get('/invoicecustomer', 'invoicecustomer')->name('customer.invoicecustomer');
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
    Route::group([], function () use ($versionupdateController) {
        Route::put('/company/versionupdate', [$versionupdateController, 'updatecompanyversion'])->name('company.versionupdate');
    });

    // product route
    $productController = getversion('productController');
    Route::controller($productController)->group(function () {
        Route::get('/product', 'index')->name('product.index');
        Route::post('/product/insert', 'store')->name('product.store');
        Route::get('/product/search/{id}', 'show')->name('product.search');
        Route::get('/product/edit/{id}', 'edit')->name('product.edit');
        Route::put('/product/update/{id}', 'update')->name('product.update');
        Route::put('/product/delete/{id}', 'destroy')->name('product.delete');
    });


    // user route
    $userController = getversion('userController');
    Route::controller($userController)->group(function () {
        Route::get('/username', 'username')->name('user.username');
        Route::get('/userprofile', 'userprofile')->name('user.profile');
        Route::get('/customersupportuser', 'customersupportuser')->name('user.customersupportindex');
        Route::get('/leaduser', 'leaduser')->name('user.leaduserindex');
        Route::get('/invoiceuser', 'invoiceuser')->name('user.invoiceuserindex');
        Route::get('/techsupportuser', 'techsupportuser')->name('user.techsupportindex');
        Route::get('/user', 'index')->name('user.index');
        Route::post('/user/insert', 'store')->name('user.store');
        Route::get('/user/search/{id}', 'show')->name('user.search');
        Route::get('/user/edit/{id}', 'edit')->name('user.edit');
        Route::put('/user/statusupdate/{id}', 'statusupdate')->name('user.statusupdate');
        Route::post('/user/update/{id}', 'update')->name('user.update');
        Route::put('/user/delete/{id}', 'destroy')->name('user.delete');
        Route::post('/user/changepassword/{id}', 'changepassword')->name('user.changepassword');
        Route::post('/user/setdefaultpage/{id}', 'setdefaultpage')->name('user.setdefaultpage');
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
        Route::put('/techsupport/changeleadstage', 'changeleadstage')->name('techsupport.changeleadstage');
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

    //bank details route
    $bankdetailsController = getversion('bankdetailsController');
    Route::controller($bankdetailsController)->group(function () {
        Route::get('/bank', 'index')->name('bank.index');
        Route::post('/bank/insert', 'store')->name('bank.store');
        Route::get('/bank/search/{id}', 'show')->name('bank.search');
        Route::get('/bank/edit/{id}', 'edit')->name('bank.edit');
        Route::put('/bank/update/{id}', 'update')->name('bank.update');
        Route::put('/bank/delete/{id}', 'destroy')->name('bank.delete');
    });

    $invoiceController = getversion('invoiceController');
    //invoice route
    Route::group([], function () use ($invoiceController) {
        Route::get('/totalinvoice', [$invoiceController, 'totalInvoice'])->name('invoice.totalinvoice');
        Route::get('/checkinvoicenumber', [$invoiceController, 'checkinvoicenumber'])->name('invoice.checkinvoicenumber');
        Route::get('/currency', [$invoiceController, 'currency'])->name('invoice.currency');
        Route::get('/bdetails', [$invoiceController, 'bdetails'])->name('invoice.bankacc');
        Route::get('/columnname', [$invoiceController, 'columnname'])->name('invoice.columnname');
        Route::get('/numbercolumnname', [$invoiceController, 'numbercolumnname'])->name('invoice.numbercolumnname');
        Route::get('/inv_list', [$invoiceController, 'inv_list'])->name('invoice.inv_list');
        Route::put('/inv_status/{id}', [$invoiceController, 'status'])->name('invoice.status');
        Route::get('/invoice/{id}', [$invoiceController, 'index'])->name('invoice.index');
        Route::post('/invoice/insert', [$invoiceController, 'store'])->name('invoice.store');
        Route::get('/invoice/search/{id}', [$invoiceController, 'show'])->name('invoice.search');
        Route::get('/invoice/inv_details/{id}', [$invoiceController, 'inv_details'])->name('invoice.inv_details');
        Route::get('/invoice/edit/{id}', [$invoiceController, 'edit'])->name('invoice.edit');
        Route::put('/invoice/update/{id}', [$invoiceController, 'update'])->name('invoice.update');
        Route::put('/invoice/delete/{id}', [$invoiceController, 'destroy'])->name('invoice.delete');
        Route::get('status_list', [$invoiceController, 'status_list'])->name('invoice.status_list');
        Route::get('chart', [$invoiceController, 'monthlyInvoiceChart'])->name('invoice.chart');
        Route::get('/reportlogs', [$invoiceController, 'reportlogsdetails'])->name('report.logs');
        Route::put('/reportlog/delete/{id}', [$invoiceController, 'reportlogdestroy'])->name('report.delete');
    });

    //payment_details route 
    $PaymentController = getversion('PaymentController');
    Route::controller($PaymentController)->group(function () {
        Route::post('payment_details', 'store')->name('paymentdetails.store');
        Route::get('paymentdetail/{id}', 'paymentdetail')->name('paymentdetails.search');
        Route::get('pendingpayment/{id}', 'pendingpayment')->name('paymentdetails.pendingpayment');
    });


    // purchases route 
    $purchaseController = getversion('purchaseController');
    Route::controller($purchaseController)->group(function () {
        Route::get('/purchase', 'index')->name('purchase.index');
        Route::post('/purchase/insert', 'store')->name('purchase.store');
        Route::get('/purchase/search/{id}', 'show')->name('purchase.search');
        Route::get('/purchase/edit/{id}', 'edit')->name('purchase.edit');
        Route::post('/purchase/update/{id}', 'update')->name('purchase.update');
        Route::put('/purchase/delete/{id}', 'destroy')->name('purchase.delete');
    });

    // tbl_invoice_column route 
    $tblinvoicecolumnController = getversion('tblinvoicecolumnController');
    Route::controller($tblinvoicecolumnController)->group(function () {
        Route::get('/formulacolumnlist', 'formula')->name('invoicecolumn.formulacolumnlist');
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

    // lead route 
    $tblleadController = getversion('tblleadController');
    Route::controller($tblleadController)->group(function () {
        Route::get('/leadstatusname', 'leadstatusname')->name('lead.leadstatusname');
        Route::get('/leadstagename', 'leadstagename')->name('lead.leadstagename');
        Route::get('/lead', 'index')->name('lead.index');
        Route::post('/lead/insert', 'store')->name('lead.store');
        Route::get('/lead/sourcecolumn', 'sourcevalue')->name('lead.sourcecolumn');
        Route::get('/lead/search/{id}', 'show')->name('lead.search');
        Route::get('/lead/edit/{id}', 'edit')->name('lead.edit');
        Route::post('/lead/update/{id}', 'update')->name('lead.update');
        Route::put('/lead/delete', 'destroy')->name('lead.delete');
        Route::put('/lead/changestatus', 'changestatus')->name('lead.changestatus');
        Route::put('/lead/changeleadstage', 'changeleadstage')->name('lead.changeleadstage');
    });

    // lead call history route
    $tblleadhistoryController = getversion('tblleadhistoryController');
    Route::controller($tblleadhistoryController)->group(function () {
        Route::get('/leadhistory', 'index')->name('leadhistory.index');
        Route::post('/leadhistory/insert', 'store')->name('leadhistory.store');
        Route::get('/leadhistory/search/{id}', 'show')->name('leadhistory.search');
        Route::get('/leadhistory/edit/{id}', 'edit')->name('leadhistory.edit');
        Route::post('/leadhistory/update/{id}', 'update')->name('leadhistory.update');
        Route::put('/leadhistory/delete', 'destroy')->name('leadhistory.delete');
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
        Route::get('/customersupporthistory', 'index')->name('customersupporthistory.index');
        Route::post('/customersupporthistory/insert', 'store')->name('customersupporthistory.store');
        Route::get('/customersupporthistory/search/{id}', 'show')->name('customersupporthistory.search');
        Route::get('/customersupporthistory/edit/{id}', 'edit')->name('customersupporthistory.edit');
        Route::post('/customersupporthistory/update/{id}', 'update')->name('customersupporthistory.update');
        Route::put('/customersupporthistory/delete', 'destroy')->name('customersupporthistory.delete');
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
        Route::post('/blogcategory/insert', 'store')->name('blogcategory.store');
        Route::get('/blogcategory/search/{id}', 'show')->name('blogcategory.search');
        Route::get('/blogcategory/edit/{id}', 'edit')->name('blogcategory.edit');
        Route::post('/blogcategory/update/{id}', 'update')->name('blogcategory.update');
        Route::put('/blogcategory/delete/{id}', 'destroy')->name('blogcategory.delete');
    });

    // blog tag route
    $blogtagController = getversion('blogtagController');
    Route::controller($blogtagController)->group(function () {
        Route::get('/blogtag', 'index')->name('blogtag.index');
        Route::post('/blogtag/insert', 'store')->name('blogtag.store');
        Route::get('/blogtag/search/{id}', 'show')->name('blogtag.search');
        Route::get('/blogtag/edit/{id}', 'edit')->name('blogtag.edit');
        Route::post('/blogtag/update/{id}', 'update')->name('blogtag.update');
        Route::put('/blogtag/delete/{id}', 'destroy')->name('blogtag.delete');
    });

    // blog  route
    $blogController = getversion('blogController');
    Route::group([], function () use ($blogController) {
        Route::get('/blog', [$blogController, 'index'])->name('blog.index');
        Route::post('/blog/insert', [$blogController, 'store'])->name('blog.store');
        Route::get('/blog/search/{slug}', [$blogController, 'show'])->name('blog.search');
        Route::get('/blog/edit/{id}', [$blogController, 'edit'])->name('blog.edit');
        Route::post('/blog/update/{id}', [$blogController, 'update'])->name('blog.update');
        Route::put('/blog/delete/{id}', [$blogController, 'destroy'])->name('blog.delete');
    });

    // api_authorization  route
    $apiauthorizationController = getversion('apiauthorizationController');
    Route::controller($apiauthorizationController)->group(function () {
        Route::get('/apiauthorization', 'index')->name('apiauthorization.index');
        Route::post('/apiauthorization/insert', 'store')->name('apiauthorization.store');
        Route::get('/apiauthorization/search/{id}', 'show')->name('apiauthorization.search');
        Route::get('/apiauthorization/edit/{id}', 'edit')->name('apiauthorization.edit');
        Route::post('/apiauthorization/update/{id}', 'update')->name('apiauthorization.update');
        Route::put('/apiauthorization/delete/{id}', 'destroy')->name('apiauthorization.delete');
    });

});

Route::get('/dbscript', [dbscriptController::class, 'dbscript'])->name('dbscript');

Route::controller(otherapiController::class)->group(function () {
    Route::post('/Addlead', 'oceanlead')->name('ocean.lead');
    Route::post('/Addfblead', 'fblead')->name('ocean.fblead');
    Route::post('/track-activity', 'store');
});
