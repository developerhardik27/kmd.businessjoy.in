<?php

use App\Http\Controllers\admin\PurchaseController;
use App\Http\Controllers\api\bankdetailsController;
use App\Http\Controllers\api\cityController;
use App\Http\Controllers\api\companyController;
use App\Http\Controllers\api\countryController;
use App\Http\Controllers\api\customerController;
use App\Http\Controllers\api\invoiceController;
use App\Http\Controllers\api\mailcontroller;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\productController;
use App\Http\Controllers\api\purchaseController as ApiPurchaseController;
use App\Http\Controllers\api\stateController;
use App\Http\Controllers\api\tblinvoicecolumnController;
use App\Http\Controllers\api\tblinvoiceformulaController;
use App\Http\Controllers\api\tblleadController;
use App\Http\Controllers\api\userController;
use App\Models\tbl_invoice_column;
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
// mail routr
Route::get('/sendmail', [mailcontroller::class, 'sendmail']);

Route::middleware('checkToken')->group(function () {

    // customer route
    Route::controller(customerController::class)->group(function () {
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
    Route::controller(companyController::class)->group(function () {
        Route::get('/companyprofile', 'companyprofile')->name('company.profile');
        Route::get('/company', 'index')->name('company.index');
        Route::get('/companydata', 'joincompany')->name('company.joindata');
        Route::post('/company/insert', 'store')->name('company.store');
        Route::get('/company/search/{id}', 'show')->name('company.search');
        Route::get('/company/edit/{id}', 'edit')->name('company.edit');
        Route::post('/company/update/{id}', 'update')->name('company.update');
        Route::post('/company/delete/{id}', 'destroy')->name('company.delete');
    });

    // product route
    Route::controller(productController::class)->group(function () {
        Route::get('/product', 'index')->name('product.index');
        Route::post('/product/insert', 'store')->name('product.store');
        Route::get('/product/search/{id}', 'show')->name('product.search');
        Route::get('/product/edit/{id}', 'edit')->name('product.edit');
        Route::put('/product/update/{id}', 'update')->name('product.update');
        Route::put('/product/delete/{id}', 'destroy')->name('product.delete');
    });


    // user route
    Route::controller(userController::class)->group(function () {
        Route::get('/username', 'username')->name('user.username');
        Route::get('/userprofile', 'userprofile')->name('user.profile');
        Route::get('/user', 'index')->name('user.index');
        Route::post('/user/insert', 'store')->name('user.store');
        Route::get('/user/search/{id}', 'show')->name('user.search');
        Route::get('/user/edit/{id}', 'edit')->name('user.edit');
        Route::put('/user/statusupdate/{id}', 'statusupdate')->name('user.statusupdate');
        Route::post('/user/update/{id}', 'update')->name('user.update');
        Route::put('/user/delete/{id}', 'destroy')->name('user.delete');
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
    Route::controller(bankdetailsController::class)->group(function () {
        Route::get('/bank', 'index')->name('bank.index');
        Route::post('/bank/insert', 'store')->name('bank.store');
        Route::get('/bank/search/{id}', 'show')->name('bank.search');
        Route::get('/bank/edit/{id}', 'edit')->name('bank.edit');
        Route::put('/bank/update/{id}', 'update')->name('bank.update');
        Route::put('/bank/delete/{id}', 'destroy')->name('bank.delete');
    });

    //invoice route
    Route::controller(invoiceController::class)->group(function () {
        Route::get('/currency', 'currency')->name('invoice.currency');
        Route::get('/bdetails', 'bdetails')->name('invoice.bankacc');
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
    });

    //payment_details route 

    Route::controller(PaymentController::class)->group(function () {
        Route::post('payment_details', 'store')->name('paymentdetails.store');
    });

    // purchases route 
    Route::controller(ApiPurchaseController::class)->group(function () {
        Route::get('/purchase', 'index')->name('purchase.index');
        Route::post('/purchase/insert', 'store')->name('purchase.store');
        Route::get('/purchase/search/{id}', 'show')->name('purchase.search');
        Route::get('/purchase/edit/{id}', 'edit')->name('purchase.edit');
        Route::post('/purchase/update/{id}', 'update')->name('purchase.update');
        Route::put('/purchase/delete/{id}', 'destroy')->name('purchase.delete');
    });
    
    // tbl_invoice_column route 
    Route::controller(tblinvoicecolumnController::class)->group(function () {
        Route::get('/invoicecolumn', 'index')->name('invoicecolumn.index');
        Route::post('/invoicecolumn/insert', 'store')->name('invoicecolumn.store');
        Route::get('/invoicecolumn/search/{id}', 'show')->name('invoicecolumn.search');
        Route::get('/invoicecolumn/edit/{id}', 'edit')->name('invoicecolumn.edit');
        Route::post('/invoicecolumn/update/{id}', 'update')->name('invoicecolumn.update');
        Route::put('/invoicecolumn/delete/{id}', 'destroy')->name('invoicecolumn.delete');
    });
    
    // tbl_invoice_formula route 
    Route::controller(tblinvoiceformulaController::class)->group(function () {
        Route::get('/invoiceformula', 'index')->name('invoiceformula.index');
        Route::post('/invoiceformula/insert', 'store')->name('invoiceformula.store');
        Route::get('/invoiceformula/search/{id}', 'show')->name('invoiceformula.search');
        Route::get('/invoiceformula/edit/{id}', 'edit')->name('invoiceformula.edit');
        Route::post('/invoiceformula/update/{id}', 'update')->name('invoiceformula.update');
        Route::put('/invoiceformula/delete/{id}', 'destroy')->name('invoiceformula.delete');
    });

    // lead route 
    Route::controller(tblleadController::class)->group(function () {
        Route::get('/lead', 'index')->name('lead.index');
        Route::post('/lead/insert', 'store')->name('lead.store');
        Route::get('/lead/search/{id}', 'show')->name('lead.search');
        Route::get('/lead/edit/{id}', 'edit')->name('lead.edit');
        Route::post('/lead/update/{id}', 'update')->name('lead.update');
        Route::put('/lead/delete', 'destroy')->name('lead.delete');
        Route::put('/lead/changestatus', 'changestatus')->name('lead.changestatus');
    });
});


