<?php
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\landing\LandingPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckSession;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/become-a-partner', function () {
    return view('become-a-partner-form');
})->withoutMiddleware([CheckSession::class]);

Route::post('/store-a-partner', [LandingPageController::class, 'storeNewPartner'])->name('admin.storenewpartner')->withoutMiddleware([CheckSession::class]);

Route::get('/privacyandpolicies', function () {
    return view('privacypolicy');
})->name('privacypolicy')->withoutMiddleware([CheckSession::class]);

Route::get('/termsandconditions', function () {
    return view('termsandconditions');
})->name('termsandconditions')->withoutMiddleware([CheckSession::class]);

Route::get('/faq', function () {
    return view('faq');
})->name('faq')->withoutMiddleware([CheckSession::class]);

Route::get('/new', [LandingPageController::class, 'new'])->name('admin.new')->withoutMiddleware([CheckSession::class]);

Route::group(['middleware' => ['CheckSession']], function () {
    // Your protected routes here...

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    require __DIR__ . '/auth.php';

    // admin panel ui route

    Route::group(['prefix' => 'admin'], function () {

        Route::get('/welcome', function () {
            if (session()->has('folder_name')) {
                // If set, return the view based on the session variable
                return view(session('folder_name') . '.admin.welcome');
            } else {
                // If not set, return the admin.login view
                return redirect()->route('admin.login')->with('error', 'Session Expired');
            }
        })->name('admin.welcome');

        Route::get('/setmenusession', [AdminLoginController::class, 'setmenusession'])->name('admin.setmenusession');

        Route::group(['middleware' => 'admin.guest'], function () {
            Route::controller(AdminLoginController::class)->group(function () {
                Route::get('/login', 'index')->name('admin.login')->withoutMiddleware([CheckSession::class]);
                Route::post('/authenticate', 'authenticate')->name('admin.authenticate')->withoutMiddleware([CheckSession::class]);
                Route::get('/forgotpassword', 'forgot')->name('admin.forgot')->withoutMiddleware([CheckSession::class]);
                Route::post('/forgotpassword', 'forgot_password')->name('admin.forgotpassword')->withoutMiddleware([CheckSession::class]);
                Route::get('/reset/{token}', 'reset_password')->name('admin.resetpassword')->withoutMiddleware([CheckSession::class]);
                Route::post('/reset/{token}', 'post_reset_password')->name('admin.post_resetpassword')->withoutMiddleware([CheckSession::class]);
                Route::get('/setpassword/{token}', 'set_password')->name('admin.setpassword')->withoutMiddleware([CheckSession::class]);
                Route::post('/setpassword/{token}', 'post_set_password')->name('admin.post_setpassword')->withoutMiddleware([CheckSession::class]);
            });
        });

        Route::group(['middleware' => 'admin.auth'], function () {

            // Define a function to generate the controller class name based on the session value
            function getadminversion($controller)
            {
                if (session_status() !== PHP_SESSION_ACTIVE)
                    session_start();
                if (isset($_SESSION['folder_name'])) {
                    $version = $_SESSION['folder_name'];
                    return 'App\\Http\\Controllers\\' . $version . '\\admin\\' . $controller;
                } else {
                    return 'App\\Http\\Controllers\\v1_0_0\\admin\\' . $controller;

                }

            }

            Route::get('/superadminloginfromanyuser/{userId}', [AdminLoginController::class, 'superAdminLoginFromAnyUser'])->name('admin.superadminloginfromanyuser');

            Route::controller(HomeController::class)->group(function () {
                Route::get('/index', 'index')->name('admin.index');
                Route::get('/logout', 'logout')->name('admin.logout');
                Route::get('/singlelogout', 'singlelogout')->name('admin.singlelogout');
            });

            // admin module routes start
            // company route 
            $CompanyController = getadminversion('CompanyController');
            Route::controller($CompanyController)->group(function () {
                Route::get('/Company', 'index')->name('admin.company')->middleware('checkPermission:adminmodule,company,show');
                Route::get('/AddNewCompany', 'create')->name('admin.addcompany')->middleware('checkPermission:adminmodule,company,add');
                Route::post('/StoreNewCompany', 'store')->name('admin.storecompany')->middleware('checkPermission:adminmodule,company,add');
                Route::get('/viewCompany/{id}', 'show')->name('admin.viewcompany')->middleware('checkPermission:adminmodule,company,view');
                Route::get('/EditCompany/{id}', 'edit')->name('admin.editcompany')->middleware('checkPermission:adminmodule,company,edit');
                Route::get('/companyprofile/{id}', 'companyprofile')->name('admin.companyprofile');
                Route::get('/EditCompanyprofile/{id}', 'editcompany')->name('admin.editcompanyprofile')->middleware('checkPermission:adminmodule,company,edit');
                Route::put('/UpdateCompany/{id}', 'update')->name('admin.updatecompany')->middleware('checkPermission:adminmodule,company,edit');
                // Route::put('/DeleteCompany/{id}','destroy')->name('admin.deletecompany'); 
                Route::get('/ApiAuthorization', 'api_authorization')->name('admin.api_authorization');
            });

            // user route 
            $UserController = getadminversion('UserController');
            Route::controller($UserController)->group(function () {
                Route::get('/User', 'index')->name('admin.user')->middleware('checkPermission:adminmodule,user,show');
                Route::get('/AddNewUser', 'create')->name('admin.adduser')->middleware('checkPermission:adminmodule,user,add');
                Route::post('/StoreNewUser', 'store')->name('admin.storeuser')->middleware('checkPermission:adminmodule,user,add');
                Route::get('/SearchUser/{id}', 'show')->name('admin.searchuser')->middleware('checkPermission:adminmodule,user,view');
                Route::get('/EditUser/{id}', 'edit')->name('admin.edituser')->middleware('checkPermission:adminmodule,user,edit');
                Route::get('/EditUserdetail/{id}', 'edituser')->name('admin.edituserdetail')->middleware('checkPermission:adminmodule,user,edit');
                Route::get('/userprofile/{id}', 'profile')->name('admin.userprofile');
                Route::put('/UpdateUser/{id}', 'update')->name('admin.updateuser')->middleware('checkPermission:adminmodule,user,edit');
                Route::put('/DeleteUser/{id}', 'destroy')->name('admin.deleteuser')->middleware('checkPermission:adminmodule,user,delete');
            });

            $VersionUpdateController = getadminversion('VersionUpdateController');
            Route::controller($VersionUpdateController)->group(function () {
                Route::get('/VersionControl', 'versioncontrol')->name('admin.versionupdate');
            });

            //  admin routes end------

            // invoice module routes start 
            // customer route 
            $CustomerController = getadminversion('CustomerController');
            Route::controller($CustomerController)->group(function () {
                Route::get('/Customer', 'index')->name('admin.customer')->middleware('checkPermission:invoicemodule,customer,show');
                Route::get('/AddNewCustomer', 'create')->name('admin.addcustomer')->middleware('checkPermission:invoicemodule,customer,add');
                Route::post('/StoreNewCustomer', 'store')->name('admin.storecustomer')->middleware('checkPermission:invoicemodule,customer,add');
                Route::get('/SearchCustomer/{id}', 'show')->name('admin.searchcustomer')->middleware('checkPermission:invoicemodule,customer,view ');
                Route::get('/EditCustomer/{id}', 'edit')->name('admin.editcustomer')->middleware('checkPermission:invoicemodule,customer,edit');
                Route::put('/UpdateCustomer/{id}', 'update')->name('admin.updatecustomer')->middleware('checkPermission:invoicemodule,customer,edit');
                Route::put('/DeleteCustomer/{id}', 'destroy')->name('admin.deletecustomer')->middleware('checkPermission:invoicemodule,customer,delete');
            });

            // invoice route
            $InvoiceController = getadminversion('InvoiceController');
            Route::controller($InvoiceController)->group(function () {
                Route::get('/invoiceview/{id}', 'invoiceview')->name('admin.invoiceview')->middleware('checkPermission:invoicemodule,invoice,show');
                Route::get('/invoice', 'index')->name('admin.invoice')->middleware('checkPermission:invoicemodule,invoice,show');
                Route::get('/managecolumn', 'managecolumn')->name('admin.managecolumn')->middleware('checkPermission:invoicemodule,mngcol,edit');
                Route::get('/formula', 'formula')->name('admin.formula')->middleware('checkPermission:invoicemodule,formula,edit');
                Route::get('/othersettings', 'othersettings')->name('admin.othersettings')->middleware('checkPermission:invoicemodule,invoicesetting,view');
                Route::get('/AddNewInvoice', 'create')->name('admin.addinvoice')->middleware('checkPermission:invoicemodule,invoice,add');
                Route::post('/StoreNewInvoice', 'store')->name('admin.storeinvoice')->middleware('checkPermission:invoicemodule,invoice,add');
                Route::get('/SearchInvoice/{id}', 'show')->name('admin.searchinvoice')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/EditInvoice/{id}', 'edit')->name('admin.editinvoice')->middleware('checkPermission:invoicemodule,invoice,edit');
                Route::put('/UpdateInvoice/{id}', 'update')->name('admin.updateinvoice')->middleware('checkPermission:invoicemodule,invoice,edit');
                Route::put('/DeleteInvoice/{id}', 'destroy')->name('admin.deleteinvoice')->middleware('checkPermission:invoicemodule,invoice,delete');
            });

            // bank route 
            $BankDetailsController = getadminversion('BankDetailsController');
            Route::controller($BankDetailsController)->group(function () {
                Route::get('/Bank', 'index')->name('admin.bank')->middleware('checkPermission:invoicemodule,bank,show');
                Route::get('/AddNewBank', 'create')->name('admin.addbank')->middleware('checkPermission:invoicemodule,bank,add');
                Route::post('/StoreNewBank', 'store')->name('admin.storebank')->middleware('checkPermission:invoicemodule,bank,add');
                Route::get('/SearchBank/{id}', 'show')->name('admin.searchbank')->middleware('checkPermission:invoicemodule,bank,view');
                Route::get('/EditBank/{id}', 'edit')->name('admin.editbank')->middleware('checkPermission:invoicemodule,bank,edit');
                Route::put('/UpdateBank/{id}', 'update')->name('admin.updatebank')->middleware('checkPermission:invoicemodule,bank,edit');
                Route::put('/DeleteBank/{id}', 'destroy')->name('admin.deletebank')->middleware('checkPermission:invoicemodule,bank,delete');
            });

            // report route 
            $ReportController = getadminversion('ReportController');
            Route::controller($ReportController)->group(function () {
                Route::get('/report', 'index')->name('admin.report');
            });
            // invoice module routes end -----

            // inventory module routes start 
            // product route 
            $ProductController = getadminversion('ProductController');
            Route::controller($ProductController)->group(function () {
                Route::get('/Product', 'index')->name('admin.product')->middleware('checkPermission:inventorymodule,product,show');
                Route::get('/AddNewProduct', 'create')->name('admin.addproduct')->middleware('checkPermission:inventorymodule,product,add');
                Route::post('/StoreNewProduct', 'store')->name('admin.storeproduct')->middleware('checkPermission:inventorymodule,product,add');
                Route::get('/SearchProduct/{id}', 'show')->name('admin.searchproduct')->middleware('checkPermission:inventorymodule,product,view');
                Route::get('/EditProduct/{id}', 'edit')->name('admin.editproduct')->middleware('checkPermission:inventorymodule,product,edit');
                Route::put('/UpdateProduct/{id}', 'update')->name('admin.updateproduct')->middleware('checkPermission:inventorymodule,product,edit');
                Route::put('/DeleteProduct/{id}', 'destroy')->name('admin.deleteproduct')->middleware('checkPermission:inventorymodule,product,delete');
            });
            // inventory module routes end----- 

            // account module routes start 
            // purchase route
            $PurchaseController = getadminversion('PurchaseController');
            Route::controller($PurchaseController)->group(function () {
                Route::get('/Purchase', 'index')->name('admin.purchase')->middleware('checkPermission:accountmodule,purchase,show');
                Route::get('/AddNewPurchase', 'create')->name('admin.addpurchase')->middleware('checkPermission:accountmodule,purchase,add');
                Route::post('/StoreNewPurchase', 'store')->name('admin.storepurchase')->middleware('checkPermission:accountmodule,purchase,add');
                Route::get('/SearchPurchase/{id}', 'show')->name('admin.searchpurchase')->middleware('checkPermission:accountmodule,purchase,view');
                Route::get('/EditPurchase/{id}', 'edit')->name('admin.editpurchase')->middleware('checkPermission:accountmodule,purchase,edit');
                Route::put('/UpdatePurchase/{id}', 'update')->name('admin.updatepurchase')->middleware('checkPermission:accountmodule,purchase,edit');
                Route::put('/DeletePurchase/{id}', 'destroy')->name('admin.deletepurchase')->middleware('checkPermission:accountmodule,purchase,delete');
            });
            // account module routes end-----

            // lead module routes start 
            // lead route 
            $TblLeadController = getadminversion('TblLeadController');
            Route::controller($TblLeadController)->group(function () {
                Route::get('/Lead', 'index')->name('admin.lead')->middleware('checkPermission:leadmodule,lead,show');
                Route::get('/AddNewLead', 'create')->name('admin.addlead')->middleware('checkPermission:leadmodule,lead,add');
                Route::post('/StoreNewLead', 'store')->name('admin.storelead')->middleware('checkPermission:leadmodule,lead,add');
                Route::get('/SearchLead/{id}', 'show')->name('admin.searchlead')->middleware('checkPermission:leadmodule,lead,view');
                Route::get('/EditLead/{id}', 'edit')->name('admin.editlead')->middleware('checkPermission:leadmodule,lead,edit');
                Route::put('/UpdateLead/{id}', 'update')->name('admin.updatelead')->middleware('checkPermission:leadmodule,lead,edit');
                Route::put('/DeleteLead/{id}', 'destroy')->name('admin.deletelead')->middleware('checkPermission:leadmodule,lead,delete');
            });
            // lead module routes end----- 

            // customer support module routes start 
            // customer support route 
            $CustomerSupportController = getadminversion('CustomerSupportController');
            Route::controller($CustomerSupportController)->group(function () {
                Route::get('/customersupport', 'index')->name('admin.customersupport')->middleware('checkPermission:customersupportmodule,customersupport,show');
                Route::get('/AddNewcustomersupport', 'create')->name('admin.addcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,add');
                Route::post('/StoreNewcustomersupport', 'store')->name('admin.storecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,add');
                Route::get('/Searchcustomersupport/{id}', 'show')->name('admin.searchcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,view');
                Route::get('/Editcustomersupport/{id}', 'edit')->name('admin.editcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,edit');
                Route::put('/Updatecustomersupport/{id}', 'update')->name('admin.updatecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,edit');
                Route::put('/Deletecustomersupport/{id}', 'destroy')->name('admin.deletecustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,delete');
            });
            // customer support module routes end ----- 

            // reminder module routes start 
            // reminder customer route 
            $ReminderCustomerController = getadminversion('ReminderCustomerController');
            Route::controller($ReminderCustomerController)->group(function () {
                Route::get('/ReminderCustomer', 'index')->name('admin.remindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,show');
                Route::get('/AddNewReminderCustomer', 'create')->name('admin.addremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,add');
                Route::post('/StoreNewReminderCustomer', 'store')->name('admin.storeremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,add');
                Route::get('/SearchReminderCustomer/{id}', 'show')->name('admin.searchremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,view ');
                Route::get('/EditReminderCustomer/{id}', 'edit')->name('admin.editremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,edit');
                Route::put('/UpdateReminderCustomer/{id}', 'update')->name('admin.updateremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,edit');
                Route::put('/DeleteReminderCustomer/{id}', 'destroy')->name('admin.deleteremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,delete');
            });
            // reminder customer route end 

            // reminder route 
            $ReminderController = getadminversion('ReminderController');
            Route::controller($ReminderController)->group(function () {
                Route::get('/Reminder', 'index')->name('admin.reminder')->middleware('checkPermission:remindermodule,reminder,show');
                Route::get('/AddNewReminder/{id?}', 'create')->name('admin.addreminder')->middleware('checkPermission:remindermodule,reminder,add');
                Route::post('/StoreNewReminder', 'store')->name('admin.storereminder')->middleware('checkPermission:remindermodule,reminder,add');
                Route::get('/SearchReminder/{id}', 'show')->name('admin.searchreminder')->middleware('checkPermission:remindermodule,reminder,view');
                Route::get('/EditReminder/{id}', 'edit')->name('admin.editreminder')->middleware('checkPermission:remindermodule,reminder,edit');
                Route::put('/UpdateReminder/{id}', 'update')->name('admin.updatereminder')->middleware('checkPermission:remindermodule,reminder,edit');
                Route::put('/DeleteReminder/{id}', 'destroy')->name('admin.deletereminder')->middleware('checkPermission:remindermodule,reminder,delete');
            });


            // technical support route 
            $TechSupportController = getadminversion('TechSupportController');
            Route::controller($TechSupportController)->group(function () {
                Route::get('/Techsupport', 'index')->name('admin.techsupport');
                Route::get('/AddNewTechsupport', 'create')->name('admin.addtechsupport');
                Route::get('/EditTechsupport/{id}', 'edit')->name('admin.edittechsupport');
            });


            // blog module routes 

            // blog table route  
            $BlogController = getadminversion('BlogController');
            Route::controller($BlogController)->group(function () {
                Route::get('/Blog', 'index')->name('admin.blog')->middleware('checkPermission:blogmodule,blog,show');
                Route::get('/AddNewBlog', 'create')->name('admin.addblog')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/BlogTag', 'blogtag')->name('admin.blogtag')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/BlogCategory', 'blogcategory')->name('admin.blogcategory')->middleware('checkPermission:blogmodule,blog,add');
                Route::post('/StoreNewBlog', 'store')->name('admin.storeblog')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/SearchBlog/{id}', 'show')->name('admin.searchblog')->middleware('checkPermission:blogmodule,blog,view');
                Route::get('/EditBlog/{id}', 'edit')->name('admin.editblog')->middleware('checkPermission:blogmodule,blog,edit');
                Route::put('/UpdateBlog/{id}', 'update')->name('admin.updateblog')->middleware('checkPermission:blogmodule,blog,edit');
                Route::put('/DeleteBlog/{id}', 'destroy')->name('admin.deleteblog')->middleware('checkPermission:blogmodule,blog,delete');
            });



            // pdf routes ------------------------------------ 
            $PdfController = getadminversion('PdfController');
            Route::controller($PdfController)->group(function () {
                Route::get('/download/{fileName}', 'downloadZip')->name('file.download');
                Route::get('/generatepdf/{id}', 'generatepdf')->name('invoice.generatepdf')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::post('/generatepdfzip', 'generatepdfzip')->name('invoice.generatepdfzip');
                Route::get('/generatereciept/{id}', 'generatereciept')->name('invoice.generatereciept')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/generaterecieptall/{id}', 'generaterecieptall')->name('invoice.generaterecieptll')->middleware('checkPermission:invoicemodule,invoice,view');
            });
        });
    });
});