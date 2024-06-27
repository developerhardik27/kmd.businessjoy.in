<?php

use App\Http\Controllers\api\cityController;
use App\Http\Controllers\api\countryController;
use App\Http\Controllers\api\dbscriptController;
use App\Http\Controllers\api\mailcontroller;
use App\Http\Controllers\api\otherapiController;
use App\Http\Controllers\api\stateController;
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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// mail route
Route::get('/sendmail', [mailcontroller::class, 'sendmail']);

// middleware route group 

// Resolve middleware dynamically based on version
$middlewareNamespace = 'App\\Http\\Middleware\\';
try {
    // Retrieve the user and company version
    $user = $request->has('user_id') ? User::find($request->user_id) : null;
    $version = $user ? Company::find($user->company_id) : null;
    $versionexplode = $version ? $version->app_version : "v1_0_0";
} catch (\Exception $e) {
    // Log the error or handle gracefully
    $versionexplode = "v1_0_0"; // Default version in case of error
}

$middlewareClass = $middlewareNamespace . $versionexplode . '\\CheckToken';



Route::middleware($middlewareClass)->group(function () {

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
            }

            // If the user exists, retrieve the company's version
            if ($user) {
                $version = Company::find($user->company_id);
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
    Route::group([], function () use ($customerController) {
        Route::get('/invoicecustomer', [$customerController, 'invoicecustomer'])->name('customer.invoicecustomer');
        Route::get('/customer', [$customerController, 'index'])->name('customer.index');
        Route::post('/customer/insert', [$customerController, 'store'])->name('customer.store');
        Route::get('/customer/search/{id}', [$customerController, 'show'])->name('customer.search');
        Route::get('/customer/edit/{id}', [$customerController, 'edit'])->name('customer.edit');
        Route::put('/customer/statusupdate/{id}', [$customerController, 'statusupdate'])->name('customer.statusupdate');
        Route::put('/customer/update/{id}', [$customerController, 'update'])->name('customer.update');
        Route::put('/customer/delete/{id}', [$customerController, 'destroy'])->name('customer.delete');
    });

    // company route
    $companyController = getversion('companyController');
    Route::group([], function () use ($companyController) {
        Route::get('/companyprofile', [$companyController, 'companyprofile'])->name('company.profile');
        Route::get('/company', [$companyController, 'index'])->name('company.index');
        Route::get('/companydata', [$companyController, 'joincompany'])->name('company.joindata');
        Route::post('/company/insert', [$companyController, 'store'])->name('company.store');
        Route::get('/company/search/{id}', [$companyController, 'show'])->name('company.search');
        Route::get('/company/edit/{id}', [$companyController, 'edit'])->name('company.edit');
        Route::post('/company/update/{id}', [$companyController, 'update'])->name('company.update');
        Route::post('/company/delete/{id}', [$companyController, 'destroy'])->name('company.delete');
        Route::put('/company/statusupdate/{id}', [$companyController, 'statusupdate'])->name('company.statusupdate');

    });

    // product route
    $productController = getversion('productController');
    Route::group([], function () use ($productController) {
        Route::get('/product', [$productController, 'index'])->name('product.index');
        Route::post('/product/insert', [$productController, 'store'])->name('product.store');
        Route::get('/product/search/{id}', [$productController, 'show'])->name('product.search');
        Route::get('/product/edit/{id}', [$productController, 'edit'])->name('product.edit');
        Route::put('/product/update/{id}', [$productController, 'update'])->name('product.update');
        Route::put('/product/delete/{id}', [$productController, 'destroy'])->name('product.delete');
    });


    // user route
    $userController = getversion('userController');
    Route::group([], function () use ($userController) {
        Route::get('/username', [$userController, 'username'])->name('user.username');
        Route::get('/userprofile', [$userController, 'userprofile'])->name('user.profile');
        Route::get('/customersupportuser', [$userController, 'customersupportuser'])->name('user.customersupportindex');
        Route::get('/leaduser', [$userController, 'leaduser'])->name('user.leaduserindex');
        Route::get('/invoiceuser', [$userController, 'invoiceuser'])->name('user.invoiceuserindex');
        Route::get('/techsupportuser', [$userController, 'techsupportuser'])->name('user.techsupportindex');
        Route::get('/user', [$userController, 'index'])->name('user.index');
        Route::post('/user/insert', [$userController, 'store'])->name('user.store');
        Route::get('/user/search/{id}', [$userController, 'show'])->name('user.search');
        Route::get('/user/edit/{id}', [$userController, 'edit'])->name('user.edit');
        Route::put('/user/statusupdate/{id}', [$userController, 'statusupdate'])->name('user.statusupdate');
        Route::post('/user/update/{id}', [$userController, 'update'])->name('user.update');
        Route::put('/user/delete/{id}', [$userController, 'destroy'])->name('user.delete');
        Route::post('/user/changepassword/{id}', [$userController, 'changepassword'])->name('user.changepassword');
        Route::post('/user/setdefaultpage/{id}', [$userController, 'setdefaultpage'])->name('user.setdefaultpage');
    });


    // customer suppport route 
    $techsupportController = getversion('techsupportController');
    Route::group([], function () use ($techsupportController) {
        Route::get('/techsupport', [$techsupportController, 'index'])->name('techsupport.index');
        Route::post('/techsupport/insert', [$techsupportController, 'store'])->name('techsupport.store');
        Route::get('/techsupport/search/{id}', [$techsupportController, 'show'])->name('techsupport.search');
        Route::get('/techsupport/edit/{id}', [$techsupportController, 'edit'])->name('techsupport.edit');
        Route::post('/techsupport/update/{id}', [$techsupportController, 'update'])->name('techsupport.update');
        Route::put('/techsupport/delete', [$techsupportController, 'destroy'])->name('techsupport.delete');
        Route::put('/techsupport/changestatus', [$techsupportController, 'changestatus'])->name('techsupport.changestatus');
        Route::put('/techsupport/changeleadstage', [$techsupportController, 'changeleadstage'])->name('techsupport.changeleadstage');
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
    Route::group([], function () use ($bankdetailsController) {
        Route::get('/bank', [$bankdetailsController, 'index'])->name('bank.index');
        Route::post('/bank/insert', [$bankdetailsController, 'store'])->name('bank.store');
        Route::get('/bank/search/{id}', [$bankdetailsController, 'show'])->name('bank.search');
        Route::get('/bank/edit/{id}', [$bankdetailsController, 'edit'])->name('bank.edit');
        Route::put('/bank/update/{id}', [$bankdetailsController, 'update'])->name('bank.update');
        Route::put('/bank/delete/{id}', [$bankdetailsController, 'destroy'])->name('bank.delete');
    });

    $invoiceController = getversion('invoiceController');
    //invoice route
    Route::group([], function () use ($invoiceController) {
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
    Route::group([], function () use ($PaymentController) {
        Route::post('payment_details', [$PaymentController, 'store'])->name('paymentdetails.store');
        Route::get('paymentdetail/{id}', [$PaymentController, 'paymentdetail'])->name('paymentdetails.search');
        Route::get('pendingpayment/{id}', [$PaymentController, 'pendingpayment'])->name('paymentdetails.pendingpayment');
    });


    // purchases route 
    $purchaseController = getversion('purchaseController');
    Route::group([], function () use ($purchaseController) {
        Route::get('/purchase', [$purchaseController, 'index'])->name('purchase.index');
        Route::post('/purchase/insert', [$purchaseController, 'store'])->name('purchase.store');
        Route::get('/purchase/search/{id}', [$purchaseController, 'show'])->name('purchase.search');
        Route::get('/purchase/edit/{id}', [$purchaseController, 'edit'])->name('purchase.edit');
        Route::post('/purchase/update/{id}', [$purchaseController, 'update'])->name('purchase.update');
        Route::put('/purchase/delete/{id}', [$purchaseController, 'destroy'])->name('purchase.delete');
    });

    // tbl_invoice_column route 
    $tblinvoicecolumnController = getversion('tblinvoicecolumnController');
    Route::group([], function () use ($tblinvoicecolumnController) {
        Route::get('/formulacolumnlist', [$tblinvoicecolumnController, 'formula'])->name('invoicecolumn.formulacolumnlist');
        Route::get('/invoicecolumn', [$tblinvoicecolumnController, 'index'])->name('invoicecolumn.index');
        Route::post('/invoicecolumn/insert', [$tblinvoicecolumnController, 'store'])->name('invoicecolumn.store');
        Route::post('/invoicecolumn/columnorder', [$tblinvoicecolumnController, 'columnorder'])->name('invoicecolumn.columnorder');
        Route::get('/invoicecolumn/search/{id}', [$tblinvoicecolumnController, 'show'])->name('invoicecolumn.search');
        Route::get('/invoicecolumn/edit/{id}', [$tblinvoicecolumnController, 'edit'])->name('invoicecolumn.edit');
        Route::post('/invoicecolumn/update/{id}', [$tblinvoicecolumnController, 'update'])->name('invoicecolumn.update');
        Route::put('/invoicecolumn/delete/{id}', [$tblinvoicecolumnController, 'destroy'])->name('invoicecolumn.delete');
        Route::put('/invoicecolumn/hide/{id}', [$tblinvoicecolumnController, 'hide'])->name('invoicecolumn.hide');
    });

    // tbl_invoice_formula route 
    $tblinvoiceformulaController = getversion('tblinvoiceformulaController');
    Route::group([], function () use ($tblinvoiceformulaController) {
        Route::get('/invoiceformula', [$tblinvoiceformulaController, 'index'])->name('invoiceformula.index');
        Route::post('/invoiceformula/insert', [$tblinvoiceformulaController, 'store'])->name('invoiceformula.store');
        Route::post('/invoiceformula/formulaorder', [$tblinvoiceformulaController, 'formulaorder'])->name('invoiceformula.formulaorder');
        Route::get('/invoiceformula/search/{id}', [$tblinvoiceformulaController, 'show'])->name('invoiceformula.search');
        Route::get('/invoiceformula/edit/{id}', [$tblinvoiceformulaController, 'edit'])->name('invoiceformula.edit');
        Route::post('/invoiceformula/update/{id}', [$tblinvoiceformulaController, 'update'])->name('invoiceformula.update');
        Route::put('/invoiceformula/delete/{id}', [$tblinvoiceformulaController, 'destroy'])->name('invoiceformula.delete');
    });

    // lead route 
    $tblleadController = getversion('tblleadController');
    Route::group([], function () use ($tblleadController) {
        Route::get('/leadstatusname', [$tblleadController, 'leadstatusname'])->name('lead.leadstatusname');
        Route::get('/leadstagename', [$tblleadController, 'leadstagename'])->name('lead.leadstagename');
        Route::get('/lead', [$tblleadController, 'index'])->name('lead.index');
        Route::post('/lead/insert', [$tblleadController, 'store'])->name('lead.store');
        Route::get('/lead/sourcecolumn', [$tblleadController, 'sourcevalue'])->name('lead.sourcecolumn');
        Route::get('/lead/search/{id}', [$tblleadController, 'show'])->name('lead.search');
        Route::get('/lead/edit/{id}', [$tblleadController, 'edit'])->name('lead.edit');
        Route::post('/lead/update/{id}', [$tblleadController, 'update'])->name('lead.update');
        Route::put('/lead/delete', [$tblleadController, 'destroy'])->name('lead.delete');
        Route::put('/lead/changestatus', [$tblleadController, 'changestatus'])->name('lead.changestatus');
        Route::put('/lead/changeleadstage', [$tblleadController, 'changeleadstage'])->name('lead.changeleadstage');
    });

    // lead call history route
    $tblleadhistoryController = getversion('tblleadhistoryController');
    Route::group([], function () use ($tblleadhistoryController) {
        Route::get('/leadhistory', [$tblleadhistoryController, 'index'])->name('leadhistory.index');
        Route::post('/leadhistory/insert', [$tblleadhistoryController, 'store'])->name('leadhistory.store');
        Route::get('/leadhistory/search/{id}', [$tblleadhistoryController, 'show'])->name('leadhistory.search');
        Route::get('/leadhistory/edit/{id}', [$tblleadhistoryController, 'edit'])->name('leadhistory.edit');
        Route::post('/leadhistory/update/{id}', [$tblleadhistoryController, 'update'])->name('leadhistory.update');
        Route::put('/leadhistory/delete', [$tblleadhistoryController, 'destroy'])->name('leadhistory.delete');
    });

    // customer suppport route 
    $customersupportController = getversion('customersupportController');
    Route::group([], function () use ($customersupportController) {
        Route::get('/customersupport', [$customersupportController, 'index'])->name('customersupport.index');
        Route::post('/customersupport/insert', [$customersupportController, 'store'])->name('customersupport.store');
        Route::get('/customersupport/search/{id}', [$customersupportController, 'show'])->name('customersupport.search');
        Route::get('/customersupport/edit/{id}', [$customersupportController, 'edit'])->name('customersupport.edit');
        Route::post('/customersupport/update/{id}', [$customersupportController, 'update'])->name('customersupport.update');
        Route::put('/customersupport/delete', [$customersupportController, 'destroy'])->name('customersupport.delete');
        Route::put('/customersupport/changestatus', [$customersupportController, 'changestatus'])->name('customersupport.changestatus');
        Route::put('/customersupport/changeleadstage', [$customersupportController, 'changeleadstage'])->name('customersupport.changeleadstage');
    });

    // customer support call history route
    $customersupporthistoryController = getversion('customersupporthistoryController');
    Route::group([], function () use ($customersupporthistoryController) {
        Route::get('/customersupporthistory', [$customersupporthistoryController, 'index'])->name('customersupporthistory.index');
        Route::post('/customersupporthistory/insert', [$customersupporthistoryController, 'store'])->name('customersupporthistory.store');
        Route::get('/customersupporthistory/search/{id}', [$customersupporthistoryController, 'show'])->name('customersupporthistory.search');
        Route::get('/customersupporthistory/edit/{id}', [$customersupporthistoryController, 'edit'])->name('customersupporthistory.edit');
        Route::post('/customersupporthistory/update/{id}', [$customersupporthistoryController, 'update'])->name('customersupporthistory.update');
        Route::put('/customersupporthistory/delete', [$customersupporthistoryController, 'destroy'])->name('customersupporthistory.delete');
    });

    //common controller route
    $commonController = getversion('commonController');
    Route::group([], function () use ($commonController) {
        Route::get('/getdbname/{id}', [$commonController, 'dbname'])->name('getdbanme');
    });

    $tblinvoiceothersettingController = getversion('tblinvoiceothersettingController');
    Route::group([], function () use ($tblinvoiceothersettingController) {
        Route::get('/getoverduedays', [$tblinvoiceothersettingController, 'getoverduedays'])->name('getoverduedays.index');
        Route::get('/invoicenumberpatterns', [$tblinvoiceothersettingController, 'invoicenumberpatternindex'])->name('invoicenumberpatterns.index');
        Route::post('/getoverduedays/update/{id}', [$tblinvoiceothersettingController, 'overduedayupdate'])->name('getoverduedays.update');
        Route::post('/invoicepattern/update', [$tblinvoiceothersettingController, 'invoicepatternstore'])->name('invoicepattern.store');
        Route::post('/gstsettings/update/{id}', [$tblinvoiceothersettingController, 'gstsettingsupdate'])->name('gstsettingsupdate.update');
        Route::get('/termsandconditions', [$tblinvoiceothersettingController, 'termsandconditionsindex'])->name('termsandconditions.index');
        Route::post('/termsandconditions/insert', [$tblinvoiceothersettingController, 'invoicetcstore'])->name('termsandconditions.store');
        Route::get('/termsandconditions/edit/{id}', [$tblinvoiceothersettingController, 'tcedit'])->name('termsandconditions.edit');
        Route::post('/termsandconditions/update/{id}', [$tblinvoiceothersettingController, 'tcupdate'])->name('termsandconditions.update');
        Route::put('/termsandconditions/statusupdate/{id}', [$tblinvoiceothersettingController, 'tcstatusupdate'])->name('termsandconditions.statusupdate');
        Route::put('/termsandconditions/delete/{id}', [$tblinvoiceothersettingController, 'tcdestroy'])->name('termsandconditions.delete');
        Route::post('/customerid', [$tblinvoiceothersettingController, 'customeridstore'])->name('customerid.store');
    });


    // reminder modules route 
    // reminder customer route
    $remindercustomerController = getversion('remindercustomerController');
    Route::group([], function () use ($remindercustomerController) {
        Route::get('/remidercustomer/count', [$remindercustomerController, 'counttotalcustomer'])->name('remindercustomer.count');
        Route::get('/remindercustomer/customerreminders/{id}', [$remindercustomerController, 'customerreminders'])->name('remindercustomer.customerreminders');
        Route::get('/remindercustomer/customers', [$remindercustomerController, 'remindercustomer'])->name('remindercustomer.customers');
        Route::get('/remindercustomer/area', [$remindercustomerController, 'area'])->name('remindercustomer.area');
        Route::get('/remindercustomer/city', [$remindercustomerController, 'cities'])->name('remindercustomer.city');
        Route::get('/remindercustomer', [$remindercustomerController, 'index'])->name('remindercustomer.index');
        Route::post('/remindercustomer/insert', [$remindercustomerController, 'store'])->name('remindercustomer.store');
        Route::get('/remindercustomer/search/{id}', [$remindercustomerController, 'show'])->name('remindercustomer.search');
        Route::get('/remindercustomer/edit/{id}', [$remindercustomerController, 'edit'])->name('remindercustomer.edit');
        Route::put('/remindercustomer/statusupdate/{id}', [$remindercustomerController, 'statusupdate'])->name('remindercustomer.statusupdate');
        Route::put('/remindercustomer/update/{id}', [$remindercustomerController, 'update'])->name('remindercustomer.update');
        Route::put('/remindercustomer/delete/{id}', [$remindercustomerController, 'destroy'])->name('remindercustomer.delete');
    });

    // lead route 
    $reminderController = getversion('reminderController');
    Route::group([], function () use ($reminderController) {
        Route::get('/reminder/reminderbydays', [$reminderController, 'getRemindersByDays'])->name('reminder.reminderbydays');
        Route::get('/reminder', [$reminderController, 'index'])->name('reminder.index');
        Route::post('/reminder/insert', [$reminderController, 'store'])->name('reminder.store');
        Route::get('/reminder/search/{id}', [$reminderController, 'show'])->name('reminder.search');
        Route::get('/reminder/edit/{id}', [$reminderController, 'edit'])->name('reminder.edit');
        Route::post('/reminder/update/{id}', [$reminderController, 'update'])->name('reminder.update');
        Route::put('/reminder/delete', [$reminderController, 'destroy'])->name('reminder.delete');
        Route::put('/reminder/changestatus', [$reminderController, 'changestatus'])->name('reminder.changestatus');
        Route::get('/reminder/status_list', [$reminderController, 'status_list'])->name('reminder.status_list');
        Route::get('/reminder/chart', [$reminderController, 'monthlyInvoiceChart'])->name('reminder.chart');
    });

    // blog category route
    $blogcategoryController = getversion('blogcategoryController');
    Route::group([], function () use ($blogcategoryController) {
        Route::get('/blogcategory', [$blogcategoryController, 'index'])->name('blogcategory.index');
        Route::post('/blogcategory/insert', [$blogcategoryController, 'store'])->name('blogcategory.store');
        Route::get('/blogcategory/search/{id}', [$blogcategoryController, 'show'])->name('blogcategory.search');
        Route::get('/blogcategory/edit/{id}', [$blogcategoryController, 'edit'])->name('blogcategory.edit');
        Route::post('/blogcategory/update/{id}', [$blogcategoryController, 'update'])->name('blogcategory.update');
        Route::put('/blogcategory/delete/{id}', [$blogcategoryController, 'destroy'])->name('blogcategory.delete');
    });

    // blog tag route
    $blogtagController = getversion('blogtagController');
    Route::group([], function () use ($blogtagController) {
        Route::get('/blogtag', [$blogtagController, 'index'])->name('blogtag.index');
        Route::post('/blogtag/insert', [$blogtagController, 'store'])->name('blogtag.store');
        Route::get('/blogtag/search/{id}', [$blogtagController, 'show'])->name('blogtag.search');
        Route::get('/blogtag/edit/{id}', [$blogtagController, 'edit'])->name('blogtag.edit');
        Route::post('/blogtag/update/{id}', [$blogtagController, 'update'])->name('blogtag.update');
        Route::put('/blogtag/delete/{id}', [$blogtagController, 'destroy'])->name('blogtag.delete');
    });

    // blog  route
    $blogController = getversion('blogController');
    Route::group([], function () use ($blogController) {
        Route::get('/blog', [$blogController, 'index'])->name('blog.index');
        Route::post('/blog/insert', [$blogController, 'store'])->name('blog.store');
        Route::get('/blog/search/{id}', [$blogController, 'show'])->name('blog.search');
        Route::get('/blog/edit/{id}', [$blogController, 'edit'])->name('blog.edit');
        Route::post('/blog/update/{id}', [$blogController, 'update'])->name('blog.update');
        Route::put('/blog/delete/{id}', [$blogController, 'destroy'])->name('blog.delete');
    });

    // api_authorization  route
    $apiauthorizationController = getversion('apiauthorizationController');
    Route::group([], function () use ($apiauthorizationController) {
        Route::get('/apiauthorization', [$apiauthorizationController, 'index'])->name('apiauthorization.index');
        Route::post('/apiauthorization/insert', [$apiauthorizationController, 'store'])->name('apiauthorization.store');
        Route::get('/apiauthorization/search/{id}', [$apiauthorizationController, 'show'])->name('apiauthorization.search');
        Route::get('/apiauthorization/edit/{id}', [$apiauthorizationController, 'edit'])->name('apiauthorization.edit');
        Route::post('/apiauthorization/update/{id}', [$apiauthorizationController, 'update'])->name('apiauthorization.update');
        Route::put('/apiauthorization/delete/{id}', [$apiauthorizationController, 'destroy'])->name('apiauthorization.delete');
    });

});

Route::get('/dbscript', [dbscriptController::class, 'dbscript'])->name('dbscript');
Route::post('/Addlead', [otherapiController::class, 'oceanlead'])->name('ocean.lead');
Route::post('/Addfblead', [otherapiController::class, 'fblead'])->name('ocean.fblead');
Route::post('/track-activity', [otherapiController::class, 'store']);
