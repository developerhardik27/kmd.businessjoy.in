<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BankDetailsController;
use App\Http\Controllers\admin\CompanyController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\CustomerSupportController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\admin\PdfController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\PurchaseController;
use App\Http\Controllers\admin\TblLeadController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\landing\LandingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';

// admin panel ui route

Route::group(['prefix' => 'admin'], function () {
   
    Route::get('/welcome', function () {
        return view('admin.welcome');
    })->name('admin.welcome');

    Route::get('/new',[LandingPageController::class , 'new'])->name('admin.new');

    Route::get('/setmenusession', [AdminLoginController::class, 'setmenusession'])->name('admin.setmenusession');
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
        Route::get('/forgotpassword', [AdminLoginController::class, 'forgot'])->name('admin.forgot');
        Route::post('/forgotpassword', [AdminLoginController::class, 'forgot_password'])->name('admin.forgotpassword');
        Route::get('/reset/{token}', [AdminLoginController::class, 'reset_password'])->name('admin.resetpassword');
        Route::post('/reset/{token}', [AdminLoginController::class, 'post_reset_password'])->name('admin.post_resetpassword');
    });


    Route::group(['middleware' => 'admin.auth'], function () {

        Route::get('/index', [HomeController::class, 'index'])->name('admin.index');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
        Route::get('/singlelogout', [HomeController::class, 'singlelogout'])->name('admin.singlelogout');


        Route::controller(CompanyController::class)->group(function () {
            Route::get('/Company', 'index')->name('admin.company')->middleware('checkPermission:invoicemodule,company,show');
            Route::get('/AddNewCompany', 'create')->name('admin.addcompany')->middleware('checkPermission:invoicemodule,company,add');
            Route::post('/StoreNewCompany', 'store')->name('admin.storecompany')->middleware('checkPermission:invoicemodule,company,add');
            Route::get('/viewCompany/{id}', 'show')->name('admin.viewcompany')->middleware('checkPermission:invoicemodule,company,view');
            Route::get('/EditCompany/{id}', 'edit')->name('admin.editcompany')->middleware('checkPermission:invoicemodule,company,edit');
            Route::get('/companyprofile/{id}', 'companyprofile')->name('admin.companyprofile');
            Route::get('/EditCompanyprofile/{id}', 'editcompany')->name('admin.editcompanyprofile')->middleware('checkPermission:invoicemodule,company,edit');
            Route::put('/UpdateCompany/{id}', 'update')->name('admin.updatecompany')->middleware('checkPermission:invoicemodule,company,edit');
            // Route::put('/DeleteCompany/{id}','destroy')->name('admin.deletecompany');           
        });

        Route::controller(CustomerController::class)->group(function () {
            Route::get('/Customer', 'index')->name('admin.customer')->middleware('checkPermission:invoicemodule,customer,show');
            Route::get('/AddNewCustomer', 'create')->name('admin.addcustomer')->middleware('checkPermission:invoicemodule,customer,add');
            Route::post('/StoreNewCustomer', 'store')->name('admin.storecustomer')->middleware('checkPermission:invoicemodule,customer,add');
            Route::get('/SearchCustomer/{id}', 'show')->name('admin.searchcustomer')->middleware('checkPermission:invoicemodule,customer,view ');
            Route::get('/EditCustomer/{id}', 'edit')->name('admin.editcustomer')->middleware('checkPermission:invoicemodule,customer,edit');
            Route::put('/UpdateCustomer/{id}', 'update')->name('admin.updatecustomer')->middleware('checkPermission:invoicemodule,customer,edit');
            Route::put('/DeleteCustomer/{id}', 'destroy')->name('admin.deletecustomer')->middleware('checkPermission:invoicemodule,customer,delete');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/Product', 'index')->name('admin.product')->middleware('checkPermission:invoicemodule,product,show');
            Route::get('/AddNewProduct', 'create')->name('admin.addproduct')->middleware('checkPermission:invoicemodule,product,add');
            Route::post('/StoreNewProduct', 'store')->name('admin.storeproduct')->middleware('checkPermission:invoicemodule,product,add');
            Route::get('/SearchProduct/{id}', 'show')->name('admin.searchproduct')->middleware('checkPermission:invoicemodule,product,view');
            Route::get('/EditProduct/{id}', 'edit')->name('admin.editproduct')->middleware('checkPermission:invoicemodule,product,edit');
            Route::put('/UpdateProduct/{id}', 'update')->name('admin.updateproduct')->middleware('checkPermission:invoicemodule,product,edit');
            Route::put('/DeleteProduct/{id}', 'destroy')->name('admin.deleteproduct')->middleware('checkPermission:invoicemodule,product,delete');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('/User', 'index')->name('admin.user')->middleware('checkPermission:invoicemodule,user,show');
            Route::get('/AddNewUser', 'create')->name('admin.adduser')->middleware('checkPermission:invoicemodule,user,add');
            Route::post('/StoreNewUser', 'store')->name('admin.storeuser')->middleware('checkPermission:invoicemodule,user,add');
            Route::get('/SearchUser/{id}', 'show')->name('admin.searchuser')->middleware('checkPermission:invoicemodule,user,view');
            Route::get('/EditUser/{id}', 'edit')->name('admin.edituser')->middleware('checkPermission:invoicemodule,user,edit');
            Route::get('/EditUserdetail/{id}', 'edituser')->name('admin.edituserdetail')->middleware('checkPermission:invoicemodule,user,edit');
            Route::get('/userprofile/{id}', 'profile')->name('admin.userprofile');
            Route::put('/UpdateUser/{id}', 'update')->name('admin.updateuser')->middleware('checkPermission:invoicemodule,user,edit');
            Route::put('/DeleteUser/{id}', 'destroy')->name('admin.deleteuser')->middleware('checkPermission:invoicemodule,user,delete');
        });

        Route::controller(InvoiceController::class)->group(function () {
            Route::get('/invoiceview/{id}', 'invoiceview')->name('admin.invoiceview')->middleware('checkPermission:invoicemodule,invoice,show');
            Route::get('/invoice', 'index')->name('admin.invoice')->middleware('checkPermission:invoicemodule,invoice,show');
            Route::get('/managecolumn', 'managecolumn')->name('admin.managecolumn')->middleware('checkPermission:invoicemodule,invoice,edit');
            Route::get('/formula', 'formula')->name('admin.formula')->middleware('checkPermission:invoicemodule,invoice,edit');
            Route::get('/AddNewInvoice', 'create')->name('admin.addinvoice')->middleware('checkPermission:invoicemodule,invoice,add');
            Route::post('/StoreNewInvoice', 'store')->name('admin.storeinvoice')->middleware('checkPermission:invoicemodule,invoice,add');
            Route::get('/SearchInvoice/{id}', 'show')->name('admin.searchinvoice')->middleware('checkPermission:invoicemodule,invoice,view');
            Route::get('/EditInvoice/{id}', 'edit')->name('admin.editinvoice')->middleware('checkPermission:invoicemodule,invoice,edit');
            Route::put('/UpdateInvoice/{id}', 'update')->name('admin.updateinvoice')->middleware('checkPermission:invoicemodule,invoice,edit');
            Route::put('/DeleteInvoice/{id}', 'destroy')->name('admin.deleteinvoice')->middleware('checkPermission:invoicemodule,invoice,delete');
        });

        Route::controller(BankDetailsController::class)->group(function () {
            Route::get('/Bank', 'index')->name('admin.bank')->middleware('checkPermission:invoicemodule,bank,show');
            Route::get('/AddNewBank', 'create')->name('admin.addbank')->middleware('checkPermission:invoicemodule,bank,add');
            Route::post('/StoreNewBank', 'store')->name('admin.storebank')->middleware('checkPermission:invoicemodule,bank,add');
            Route::get('/SearchBank/{id}', 'show')->name('admin.searchbank')->middleware('checkPermission:invoicemodule,bank,view');
            Route::get('/EditBank/{id}', 'edit')->name('admin.editbank')->middleware('checkPermission:invoicemodule,bank,edit');
            Route::put('/UpdateBank/{id}', 'update')->name('admin.updatebank')->middleware('checkPermission:invoicemodule,bank,edit');
            Route::put('/DeleteBank/{id}', 'destroy')->name('admin.deletebank')->middleware('checkPermission:invoicemodule,bank,delete');
        });
        
        Route::controller(PurchaseController::class)->group(function () {
            Route::get('/Purchase', 'index')->name('admin.purchase')->middleware('checkPermission:invoicemodule,purchase,show');
            Route::get('/AddNewPurchase', 'create')->name('admin.addpurchase')->middleware('checkPermission:invoicemodule,purchase,add');
            Route::post('/StoreNewPurchase', 'store')->name('admin.storepurchase')->middleware('checkPermission:invoicemodule,purchase,add');
            Route::get('/SearchPurchase/{id}', 'show')->name('admin.searchpurchase')->middleware('checkPermission:invoicemodule,purchase,view');
            Route::get('/EditPurchase/{id}', 'edit')->name('admin.editpurchase')->middleware('checkPermission:invoicemodule,purchase,edit');
            Route::put('/UpdatePurchase/{id}', 'update')->name('admin.updatepurchase')->middleware('checkPermission:invoicemodule,purchase,edit');
            Route::put('/DeletePurchase/{id}', 'destroy')->name('admin.deletepurchase')->middleware('checkPermission:invoicemodule,purchase,delete');
        });

        Route::controller(TblLeadController::class)->group(function () {
            Route::get('/Lead', 'index')->name('admin.lead')->middleware('checkPermission:leadmodule,lead,show');
            Route::get('/AddNewLead', 'create')->name('admin.addlead')->middleware('checkPermission:leadmodule,lead,add');
            Route::post('/StoreNewLead', 'store')->name('admin.storelead')->middleware('checkPermission:leadmodule,lead,add');
            Route::get('/SearchLead/{id}', 'show')->name('admin.searchlead')->middleware('checkPermission:leadmodule,lead,view');
            Route::get('/EditLead/{id}', 'edit')->name('admin.editlead')->middleware('checkPermission:leadmodule,lead,edit');
            Route::put('/UpdateLead/{id}', 'update')->name('admin.updatelead')->middleware('checkPermission:leadmodule,lead,edit');
            Route::put('/DeleteLead/{id}', 'destroy')->name('admin.deletelead')->middleware('checkPermission:leadmodule,lead,delete');
        });
        
        Route::controller(CustomerSupportController::class)->group(function () {
            Route::get('/customersupport', 'index')->name('admin.customersupport')->middleware('checkPermission:customersupportmodule,customersupport,show');
            Route::get('/AddNewcustomersupport', 'create')->name('admin.addcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,add');
            Route::post('/StoreNewcustomersupport', 'store')->name('admin.storecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,add');
            Route::get('/Searchcustomersupport/{id}', 'show')->name('admin.searchcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,view');
            Route::get('/Editcustomersupport/{id}', 'edit')->name('admin.editcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,edit');
            Route::put('/Updatecustomersupport/{id}', 'update')->name('admin.updatecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,edit');
            Route::put('/Deletecustomersupport/{id}', 'destroy')->name('admin.deletecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,delete');
        });

        Route::get('/generatepdf/{id}', [PdfController::class, 'generatepdf'])->name('invoice.generatepdf')->middleware('checkPermission:invoicemodule,invoice,view');
        Route::get('/generatereciept/{id}', [PdfController::class, 'generatereciept'])->name('invoice.generatereciept')->middleware('checkPermission:invoicemodule,invoice,view');
    });
});
