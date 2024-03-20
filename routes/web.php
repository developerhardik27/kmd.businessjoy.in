<?php
// $folderName = session('version');

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\landing\LandingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use illuminate\support\Facades\Session;





Route::get('/summernote', function () {
    return view('v1_0_0.admin.summernote');
});
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
        return view(session('folder_name').'.admin.welcome');
    })->name('admin.welcome');

    Route::get('/new', [LandingPageController::class, 'new'])->name('admin.new');

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


        function getadminversion($controller)
        {
            if (session_status() !== PHP_SESSION_ACTIVE)
                session_start();
            if (isset ($_SESSION['folder_name'])) {
                $version = $_SESSION['folder_name'];
                return 'App\\Http\\Controllers\\' . $version . '\\admin\\' . $controller;
            } else {
                return 'App\\Http\\Controllers\\v1_0_0\\admin\\' . $controller;

            }

        }
        // Define a function to generate the controller class name based on the session value

        Route::get('/index', [HomeController::class, 'index'])->name('admin.index');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
        Route::get('/singlelogout', [HomeController::class, 'singlelogout'])->name('admin.singlelogout');

        // company route 
        $CompanyController = getadminversion('CompanyController');
        Route::group([], function () use ($CompanyController) {
            Route::get('/Company', [$CompanyController, 'index'])->name('admin.company')->middleware('checkPermission:invoicemodule,company,show');
            Route::get('/AddNewCompany', [$CompanyController, 'create'])->name('admin.addcompany')->middleware('checkPermission:invoicemodule,company,add');
            Route::post('/StoreNewCompany', [$CompanyController, 'store'])->name('admin.storecompany')->middleware('checkPermission:invoicemodule,company,add');
            Route::get('/viewCompany/{id}', [$CompanyController, 'show'])->name('admin.viewcompany')->middleware('checkPermission:invoicemodule,company,view');
            Route::get('/EditCompany/{id}', [$CompanyController, 'edit'])->name('admin.editcompany')->middleware('checkPermission:invoicemodule,company,edit');
            Route::get('/companyprofile/{id}', [$CompanyController, 'companyprofile'])->name('admin.companyprofile');
            Route::get('/EditCompanyprofile/{id}', [$CompanyController, 'editcompany'])->name('admin.editcompanyprofile')->middleware('checkPermission:invoicemodule,company,edit');
            Route::put('/UpdateCompany/{id}', [$CompanyController, 'update'])->name('admin.updatecompany')->middleware('checkPermission:invoicemodule,company,edit');
            // Route::put('/DeleteCompany/{id}','destroy')->name('admin.deletecompany');           
        });

        // customer route 
        $CustomerController = getadminversion('CustomerController');
        Route::group([], function () use ($CustomerController) {
            Route::get('/Customer', [$CustomerController, 'index'])->name('admin.customer')->middleware('checkPermission:invoicemodule,customer,show');
            Route::get('/AddNewCustomer', [$CustomerController, 'create'])->name('admin.addcustomer')->middleware('checkPermission:invoicemodule,customer,add');
            Route::post('/StoreNewCustomer', [$CustomerController, 'store'])->name('admin.storecustomer')->middleware('checkPermission:invoicemodule,customer,add');
            Route::get('/SearchCustomer/{id}', [$CustomerController, 'show'])->name('admin.searchcustomer')->middleware('checkPermission:invoicemodule,customer,view ');
            Route::get('/EditCustomer/{id}', [$CustomerController, 'edit'])->name('admin.editcustomer')->middleware('checkPermission:invoicemodule,customer,edit');
            Route::put('/UpdateCustomer/{id}', [$CustomerController, 'update'])->name('admin.updatecustomer')->middleware('checkPermission:invoicemodule,customer,edit');
            Route::put('/DeleteCustomer/{id}', [$CustomerController, 'destroy'])->name('admin.deletecustomer')->middleware('checkPermission:invoicemodule,customer,delete');
        });

        // product route 
        $ProductController = getadminversion('ProductController');
        Route::group([], function () use ($ProductController) {
            Route::get('/Product', [$ProductController, 'index'])->name('admin.product')->middleware('checkPermission:invoicemodule,product,show');
            Route::get('/AddNewProduct', [$ProductController, 'create'])->name('admin.addproduct')->middleware('checkPermission:invoicemodule,product,add');
            Route::post('/StoreNewProduct', [$ProductController, 'store'])->name('admin.storeproduct')->middleware('checkPermission:invoicemodule,product,add');
            Route::get('/SearchProduct/{id}', [$ProductController, 'show'])->name('admin.searchproduct')->middleware('checkPermission:invoicemodule,product,view');
            Route::get('/EditProduct/{id}', [$ProductController, 'edit'])->name('admin.editproduct')->middleware('checkPermission:invoicemodule,product,edit');
            Route::put('/UpdateProduct/{id}', [$ProductController, 'update'])->name('admin.updateproduct')->middleware('checkPermission:invoicemodule,product,edit');
            Route::put('/DeleteProduct/{id}', [$ProductController, 'destroy'])->name('admin.deleteproduct')->middleware('checkPermission:invoicemodule,product,delete');
        });

        // user route 
        $UserController = getadminversion('UserController');
        Route::group([], function () use ($UserController) {
            Route::get('/User', [$UserController, 'index'])->name('admin.user')->middleware('checkPermission:invoicemodule,user,show');
            Route::get('/AddNewUser', [$UserController, 'create'])->name('admin.adduser')->middleware('checkPermission:invoicemodule,user,add');
            Route::post('/StoreNewUser', [$UserController, 'store'])->name('admin.storeuser')->middleware('checkPermission:invoicemodule,user,add');
            Route::get('/SearchUser/{id}', [$UserController, 'show'])->name('admin.searchuser')->middleware('checkPermission:invoicemodule,user,view');
            Route::get('/EditUser/{id}', [$UserController, 'edit'])->name('admin.edituser')->middleware('checkPermission:invoicemodule,user,edit');
            Route::get('/EditUserdetail/{id}', [$UserController, 'edit'])->name('admin.edituserdetail')->middleware('checkPermission:invoicemodule,user,edit');
            Route::get('/userprofile/{id}', [$UserController, 'profile'])->name('admin.userprofile');
            Route::put('/UpdateUser/{id}', [$UserController, 'update'])->name('admin.updateuser')->middleware('checkPermission:invoicemodule,user,edit');
            Route::put('/DeleteUser/{id}', [$UserController, 'destroy'])->name('admin.deleteuser')->middleware('checkPermission:invoicemodule,user,delete');
        });

        // invoice route
        $InvoiceController = getadminversion('InvoiceController');
        Route::group([], function () use ($InvoiceController) {
            Route::get('/invoiceview/{id}', [$InvoiceController, 'invoiceview'])->name('admin.invoiceview')->middleware('checkPermission:invoicemodule,invoice,show');
            Route::get('/invoice', [$InvoiceController, 'index'])->name('admin.invoice')->middleware('checkPermission:invoicemodule,invoice,show');
            Route::get('/managecolumn', [$InvoiceController, 'managecolumn'])->name('admin.managecolumn')->middleware('checkPermission:invoicemodule,mngcol,edit');
            Route::get('/formula', [$InvoiceController, 'formula'])->name('admin.formula')->middleware('checkPermission:invoicemodule,formula,edit');
            Route::get('/othersettings', [$InvoiceController, 'othersettings'])->name('admin.othersettings')->middleware('checkPermission:invoicemodule,invoicesetting,view');
            Route::get('/AddNewInvoice', [$InvoiceController, 'create'])->name('admin.addinvoice')->middleware('checkPermission:invoicemodule,invoice,add');
            Route::post('/StoreNewInvoice', [$InvoiceController, 'store'])->name('admin.storeinvoice')->middleware('checkPermission:invoicemodule,invoice,add');
            Route::get('/SearchInvoice/{id}', [$InvoiceController, 'show'])->name('admin.searchinvoice')->middleware('checkPermission:invoicemodule,invoice,view');
            Route::get('/EditInvoice/{id}', [$InvoiceController, 'edit'])->name('admin.editinvoice')->middleware('checkPermission:invoicemodule,invoice,edit');
            Route::put('/UpdateInvoice/{id}', [$InvoiceController, 'update'])->name('admin.updateinvoice')->middleware('checkPermission:invoicemodule,invoice,edit');
            Route::put('/DeleteInvoice/{id}', [$InvoiceController, 'destroy'])->name('admin.deleteinvoice')->middleware('checkPermission:invoicemodule,invoice,delete');
        });

        // bank route 
        $BankDetailsController = getadminversion('BankDetailsController');
        Route::group([], function () use ($BankDetailsController) {
            Route::get('/Bank', [$BankDetailsController, 'index'])->name('admin.bank')->middleware('checkPermission:invoicemodule,bank,show');
            Route::get('/AddNewBank', [$BankDetailsController, 'create'])->name('admin.addbank')->middleware('checkPermission:invoicemodule,bank,add');
            Route::post('/StoreNewBank', [$BankDetailsController, 'store'])->name('admin.storebank')->middleware('checkPermission:invoicemodule,bank,add');
            Route::get('/SearchBank/{id}', [$BankDetailsController, 'show'])->name('admin.searchbank')->middleware('checkPermission:invoicemodule,bank,view');
            Route::get('/EditBank/{id}', [$BankDetailsController, 'edit'])->name('admin.editbank')->middleware('checkPermission:invoicemodule,bank,edit');
            Route::put('/UpdateBank/{id}', [$BankDetailsController, 'update'])->name('admin.updatebank')->middleware('checkPermission:invoicemodule,bank,edit');
            Route::put('/DeleteBank/{id}', [$BankDetailsController, 'destroy'])->name('admin.deletebank')->middleware('checkPermission:invoicemodule,bank,delete');
        });

        // purchase route
        $PurchaseController = getadminversion('PurchaseController');
        Route::group([], function () use ($PurchaseController) {
            Route::get('/Purchase', [$PurchaseController, 'index'])->name('admin.purchase')->middleware('checkPermission:invoicemodule,purchase,show');
            Route::get('/AddNewPurchase', [$PurchaseController, 'create'])->name('admin.addpurchase')->middleware('checkPermission:invoicemodule,purchase,add');
            Route::post('/StoreNewPurchase', [$PurchaseController, 'store'])->name('admin.storepurchase')->middleware('checkPermission:invoicemodule,purchase,add');
            Route::get('/SearchPurchase/{id}', [$PurchaseController, 'show'])->name('admin.searchpurchase')->middleware('checkPermission:invoicemodule,purchase,view');
            Route::get('/EditPurchase/{id}', [$PurchaseController, 'edit'])->name('admin.editpurchase')->middleware('checkPermission:invoicemodule,purchase,edit');
            Route::put('/UpdatePurchase/{id}', [$PurchaseController, 'update'])->name('admin.updatepurchase')->middleware('checkPermission:invoicemodule,purchase,edit');
            Route::put('/DeletePurchase/{id}', [$PurchaseController, 'destroy'])->name('admin.deletepurchase')->middleware('checkPermission:invoicemodule,purchase,delete');
        });

        // lead route 
        $TblLeadController = getadminversion('TblLeadController');
        Route::group([], function () use ($TblLeadController) {
            Route::get('/Lead', [$TblLeadController, 'index'])->name('admin.lead')->middleware('checkPermission:leadmodule,lead,show');
            Route::get('/AddNewLead', [$TblLeadController, 'create'])->name('admin.addlead')->middleware('checkPermission:leadmodule,lead,add');
            Route::post('/StoreNewLead', [$TblLeadController, 'store'])->name('admin.storelead')->middleware('checkPermission:leadmodule,lead,add');
            Route::get('/SearchLead/{id}', [$TblLeadController, 'show'])->name('admin.searchlead')->middleware('checkPermission:leadmodule,lead,view');
            Route::get('/EditLead/{id}', [$TblLeadController, 'edit'])->name('admin.editlead')->middleware('checkPermission:leadmodule,lead,edit');
            Route::put('/UpdateLead/{id}', [$TblLeadController, 'update'])->name('admin.updatelead')->middleware('checkPermission:leadmodule,lead,edit');
            Route::put('/DeleteLead/{id}', [$TblLeadController, 'destroy'])->name('admin.deletelead')->middleware('checkPermission:leadmodule,lead,delete');
        });

        // customer support route 
        $CustomerSupportController = getadminversion('CustomerSupportController');
        Route::group([], function () use ($CustomerSupportController) {
            Route::get('/customersupport', [$CustomerSupportController, 'index'])->name('admin.customersupport')->middleware('checkPermission:customersupportmodule,customersupport,show');
            Route::get('/AddNewcustomersupport', [$CustomerSupportController, 'create'])->name('admin.addcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,add');
            Route::post('/StoreNewcustomersupport', [$CustomerSupportController, 'store'])->name('admin.storecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,add');
            Route::get('/Searchcustomersupport/{id}', [$CustomerSupportController, 'show'])->name('admin.searchcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,view');
            Route::get('/Editcustomersupport/{id}', [$CustomerSupportController, 'edit'])->name('admin.editcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,edit');
            Route::put('/Updatecustomersupport/{id}', [$CustomerSupportController, 'update'])->name('admin.updatecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,edit');
            Route::put('/Deletecustomersupport/{id}', [$CustomerSupportController, 'destroy'])->name('admin.deletecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,delete');
        });

        // pdf route 
        $PdfController = getadminversion('PdfController');
        Route::group([], function () use ($PdfController) {
            Route::get('/generatepdf/{id}', [$PdfController, 'generatepdf'])->name('invoice.generatepdf')->middleware('checkPermission:invoicemodule,invoice,view');
            Route::get('/generatereciept/{id}', [$PdfController, 'generatereciept'])->name('invoice.generatereciept')->middleware('checkPermission:invoicemodule,invoice,view');
            Route::get('/generaterecieptall/{id}', [$PdfController, 'generaterecieptall'])->name('invoice.generaterecieptll')->middleware('checkPermission:invoicemodule,invoice,view');
        });
    });
});
