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

Route::post('/new', [LandingPageController::class, 'new'])->name('admin.new')->withoutMiddleware([CheckSession::class]);

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
                Route::get('/EditCompany/{id}', 'edit')->name('admin.editcompany')->middleware('checkPermission:adminmodule,company,edit');
                Route::get('/companyprofile/{id}', 'companyprofile')->name('admin.companyprofile');
                Route::get('/EditCompanyprofile/{id}', 'editcompany')->name('admin.editcompanyprofile')->middleware('checkPermission:adminmodule,company,edit');
                Route::get('/ApiAuthorization', 'api_authorization')->name('admin.api_authorization');
            });

            // user route 
            $UserController = getadminversion('UserController');
            Route::controller($UserController)->group(function () {
                Route::get('/MyLoginHistory/{id}', 'loginhistory')->name('admin.myloginhistory');
                Route::get('/User', 'index')->name('admin.user')->middleware('checkPermission:adminmodule,user,show');
                Route::get('/AddNewUser', 'create')->name('admin.adduser')->middleware('checkPermission:adminmodule,user,add');
                Route::get('/EditUser/{id}', 'edit')->name('admin.edituser')->middleware('checkPermission:adminmodule,user,edit');
                Route::get('/EditUserdetail/{id}', 'edituser')->name('admin.edituserdetail')->middleware('checkPermission:adminmodule,user,edit');
                Route::get('/userprofile/{id}', 'profile')->name('admin.userprofile');

                Route::get('/UserRolePermissions', 'userrolepermission')->name('admin.userrolepermission')->middleware('checkPermission:adminmodule,userpermission,show');
                Route::get('/AddNewUserRolePermissions', 'createuserrolepermission')->name('admin.adduserrolepermission')->middleware('checkPermission:adminmodule,userpermission,add');
                Route::get('/EditUserRolePermissions/{id}', 'edituserrolepermission')->name('admin.edituserrolepermission')->middleware('checkPermission:adminmodule,userpermission,edit');
            });

            $VersionUpdateController = getadminversion('VersionUpdateController');
            Route::controller($VersionUpdateController)->group(function () {
                Route::get('/VersionControl', 'versioncontrol')->name('admin.versionupdate');
            });

            //  admin routes end------


            // customer route 
            $CustomerController = getadminversion('CustomerController');
            Route::controller($CustomerController)->group(function () {
                Route::get('/invoice/Customer', 'index')->name('admin.invoicecustomer')->middleware('checkPermission:invoicemodule,customer,show');
                Route::get('/invoice/AddNewCustomer', 'create')->name('admin.addinvoicecustomer')->middleware('checkPermission:invoicemodule,customer,add');
                Route::get('/invoice/EditCustomer/{id}', 'edit')->name('admin.editinvoicecustomer')->middleware('checkPermission:invoicemodule,customer,edit');

                Route::get('/quotation/Customer', 'index')->name('admin.quotationcustomer')->middleware('checkPermission:quotationmodule,quotationcustomer,show');
                Route::get('/quotation/AddNewCustomer', 'create')->name('admin.addquotationcustomer')->middleware('checkPermission:quotationmodule,quotationcustomer,add');
                Route::get('/quotation/EditCustomer/{id}', 'edit')->name('admin.editquotationcustomer')->middleware('checkPermission:quotationmodule,quotationcustomer,edit');
            });

            // quotation route
            $QuotationController = getadminversion('QuotationController');
            Route::controller($QuotationController)->group(function () {
                Route::get('quotation', 'index')->name('admin.quotation')->middleware('checkPermission:quotationmodule,quotation,show');
                Route::get('quotation/managecolumn', 'managecolumn')->name('admin.quotationmanagecolumn')->middleware('checkPermission:quotationmodule,quotationmngcol,edit');
                Route::get('quotation/formula', 'formula')->name('admin.quotationformula')->middleware('checkPermission:quotationmodule,quotationformula,edit');
                Route::get('quotation/othersettings', 'othersettings')->name('admin.quotationothersettings')->middleware('checkPermission:quotationmodule,quotationsetting,view');
                Route::get('/AddNewQuotation', 'create')->name('admin.addquotation')->middleware('checkPermission:quotationmodule,quotation,add');
                Route::get('/EditQuotation/{id}', 'edit')->name('admin.editquotation')->middleware('checkPermission:quotationmodule,quotation,edit');
            });

            // invoice route
            $InvoiceController = getadminversion('InvoiceController');
            Route::controller($InvoiceController)->group(function () {
                Route::get('/invoiceview/{id}', 'invoiceview')->name('admin.invoiceview')->middleware('checkPermission:invoicemodule,invoice,show');
                Route::get('/invoice', 'index')->name('admin.invoice')->middleware('checkPermission:invoicemodule,invoice,show');
                Route::get('/invoice/managecolumn', 'managecolumn')->name('admin.invoicemanagecolumn')->middleware('checkPermission:invoicemodule,mngcol,edit');
                Route::get('/invoice/formula', 'formula')->name('admin.invoiceformula')->middleware('checkPermission:invoicemodule,formula,edit');
                Route::get('/invoice/othersettings', 'othersettings')->name('admin.invoiceothersettings')->middleware('checkPermission:invoicemodule,invoicesetting,view');
                Route::get('/AddNewInvoice', 'create')->name('admin.addinvoice')->middleware('checkPermission:invoicemodule,invoice,add');
                Route::get('/EditInvoice/{id}', 'edit')->name('admin.editinvoice')->middleware('checkPermission:invoicemodule,invoice,edit');
            });

            // bank route 
            $BankDetailsController = getadminversion('BankDetailsController');
            Route::controller($BankDetailsController)->group(function () {
                Route::get('/Bank', 'index')->name('admin.bank')->middleware('checkPermission:invoicemodule,bank,show');
                Route::get('/AddNewBank', 'create')->name('admin.addbank')->middleware('checkPermission:invoicemodule,bank,add');
                Route::get('/EditBank/{id}', 'edit')->name('admin.editbank')->middleware('checkPermission:invoicemodule,bank,edit');
            });

            // report route 
            $ReportController = getadminversion('ReportController');
            Route::controller($ReportController)->group(function () {
                Route::get('/report', 'index')->name('admin.report');
            });
            // invoice module routes end -----

            // inventory module routes start 
            // product category route 
            $ProductCategoryController = getadminversion('ProductCategoryController');
            Route::controller($ProductCategoryController)->group(function () {
                Route::get('/Productcategory', 'index')->name('admin.productcategory')->middleware('checkPermission:inventorymodule,productcategory,show');
                Route::get('/AddNewProductcategory', 'create')->name('admin.addproductcategory')->middleware('checkPermission:inventorymodule,productcategory,add');
                Route::get('/EditProductcategory/{id}', 'edit')->name('admin.editproductcategory')->middleware('checkPermission:inventorymodule,productcategory,edit');
            });

            // product route 
            $ProductController = getadminversion('ProductController');
            Route::controller($ProductController)->group(function () {
                Route::get('/Product', 'index')->name('admin.product')->middleware('checkPermission:inventorymodule,product,show');
                Route::get('/ProductColumnMapping', 'productcolumnmapping')->name('admin.productcolumnmapping')->middleware('checkPermission:inventorymodule,productcolumnmapping,add');
                Route::get('/AddNewProduct', 'create')->name('admin.addproduct')->middleware('checkPermission:inventorymodule,product,add');
                Route::get('/EditProduct/{id}', 'edit')->name('admin.editproduct')->middleware('checkPermission:inventorymodule,product,edit');
            });

            // product category route 
            $InventoryController = getadminversion('InventoryController');
            Route::controller($InventoryController)->group(function () {
                Route::get('/Inventory', 'index')->name('admin.inventory')->middleware('checkPermission:inventorymodule,inventory,show');
            });

            // suppliers route 
            $SupplierController = getadminversion('SupplierController');
            Route::controller($SupplierController)->group(function () {
                Route::get('/Suppliers', 'index')->name('admin.supplier')->middleware('checkPermission:inventorymodule,supplier,show');
                Route::get('/AddNewSuppliers', 'create')->name('admin.addsupplier')->middleware('checkPermission:inventorymodule,supplier,add');
                Route::get('/EditSuppliers/{id}', 'edit')->name('admin.editsupplier')->middleware('checkPermission:inventorymodule,supplier,edit');
            });

            // purchase route
            $PurchaseController = getadminversion('PurchaseController');
            Route::controller($PurchaseController)->group(function () {
                Route::get('/Purchase', 'index')->name('admin.purchase')->middleware('checkPermission:inventorymodule,purchase,show');
                Route::get('/AddNewPurchase', 'create')->name('admin.addpurchase')->middleware('checkPermission:inventorymodule,purchase,add');
                Route::get('/ViewPurchase/{id}', 'show')->name('admin.viewpurchase')->middleware('checkPermission:inventorymodule,purchase,view');
                Route::get('/EditPurchase/{id}', 'edit')->name('admin.editpurchase')->middleware('checkPermission:inventorymodule,purchase,edit');
            });
            // inventory module routes end----- 

            // account module routes start 
            // account module routes end-----

            // lead module routes start 
            // lead route 
            $TblLeadController = getadminversion('TblLeadController');
            Route::controller($TblLeadController)->group(function () {
                Route::get('/Lead', 'index')->name('admin.lead')->middleware('checkPermission:leadmodule,lead,show');
                Route::get('/AddNewLead', 'create')->name('admin.addlead')->middleware('checkPermission:leadmodule,lead,add');
                Route::get('/EditLead/{id}', 'edit')->name('admin.editlead')->middleware('checkPermission:leadmodule,lead,edit');
            });

            // lead module routes end----- 

            // customer support module routes start 
            // customer support route 
            $CustomerSupportController = getadminversion('CustomerSupportController');
            Route::controller($CustomerSupportController)->group(function () {
                Route::get('/customersupport', 'index')->name('admin.customersupport')->middleware('checkPermission:customersupportmodule,customersupport,show');
                Route::get('/AddNewcustomersupport', 'create')->name('admin.addcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,add');
                Route::get('/Editcustomersupport/{id}', 'edit')->name('admin.editcustomersupport')->middleware('checkPermission:customersupportmodule,customersupport,edit');
            });
            // customer support module routes end ----- 

            // reminder module routes start 
            // reminder customer route 
            $ReminderCustomerController = getadminversion('ReminderCustomerController');
            Route::controller($ReminderCustomerController)->group(function () {
                Route::get('/ReminderCustomer', 'index')->name('admin.remindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,show');
                Route::get('/AddNewReminderCustomer', 'create')->name('admin.addremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,add');
                Route::get('/EditReminderCustomer/{id}', 'edit')->name('admin.editremindercustomer')->middleware('checkPermission:remindermodule,remindercustomer,edit');
            });
            // reminder customer route end 

            // reminder route 
            $ReminderController = getadminversion('ReminderController');
            Route::controller($ReminderController)->group(function () {
                Route::get('/Reminder', 'index')->name('admin.reminder')->middleware('checkPermission:remindermodule,reminder,show');
                Route::get('/AddNewReminder/{id?}', 'create')->name('admin.addreminder')->middleware('checkPermission:remindermodule,reminder,add');
                Route::get('/EditReminder/{id}', 'edit')->name('admin.editreminder')->middleware('checkPermission:remindermodule,reminder,edit');
            });


            // technical support route 
            $TechSupportController = getadminversion('TechSupportController');
            Route::controller($TechSupportController)->group(function () {
                Route::get('/Techsupport', 'index')->name('admin.techsupport')->middleware('checkPermission:adminmodule,techsupport,show');
                Route::get('/AddNewTechsupport', 'create')->name('admin.addtechsupport')->middleware('checkPermission:adminmodule,techsupport,add');
                Route::get('/EditTechsupport/{id}', 'edit')->name('admin.edittechsupport')->middleware('checkPermission:adminmodule,techsupport,edit');
            });


            // blog module routes 

            // blog table route  
            $BlogController = getadminversion('BlogController');
            Route::controller($BlogController)->group(function () {
                Route::get('/Blog', 'index')->name('admin.blog')->middleware('checkPermission:blogmodule,blog,show');
                Route::get('/AddNewBlog', 'create')->name('admin.addblog')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/BlogTag', 'blogtag')->name('admin.blogtag')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/BlogCategory', 'blogcategory')->name('admin.blogcategory')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/EditBlog/{id}', 'edit')->name('admin.editblog')->middleware('checkPermission:blogmodule,blog,edit');
            });


            /**
             * logistic module route start
             */

            // consignee route 
            $ConsigneeController = getadminversion('ConsigneeController');
            Route::controller($ConsigneeController)->group(function () {
                Route::get('/Consignee', 'index')->name('admin.consignee')->middleware('checkPermission:logisticmodule,consignee,show');
                Route::get('/AddNewConsignee', 'create')->name('admin.addconsignee')->middleware('checkPermission:logisticmodule,consignee,add');
                Route::get('/EditConsignee/{id}', 'edit')->name('admin.editconsignee')->middleware('checkPermission:logisticmodule,consignee,edit');
            });

            // consignor route 
            $ConsignorController = getadminversion('ConsignorController');
            Route::controller($ConsignorController)->group(function () {
                Route::get('/Consignor', 'index')->name('admin.consignor')->middleware('checkPermission:logisticmodule,consignor,show');
                Route::get('/AddNewConsignor', 'create')->name('admin.addconsignor')->middleware('checkPermission:logisticmodule,consignor,add');
                Route::get('/EditConsignor/{id}', 'edit')->name('admin.editconsignor')->middleware('checkPermission:logisticmodule,consignor,edit');
            });

            //consinger copy route 
            $ConsignorCopyController = getadminversion('ConsignorCopyController');
            Route::controller($ConsignorCopyController)->group(function () {
                Route::get('/ConsignorCopy', 'index')->name('admin.consignorcopy')->middleware('checkPermission:logisticmodule,consignorcopy,show');
                Route::get('/AddNewConsignorCopy', 'create')->name('admin.addconsignorcopy')->middleware('checkPermission:logisticmodule,consignorcopy,add');
                Route::get('/EditConsignorCopy/{id}', 'edit')->name('admin.editconsignorcopy')->middleware('checkPermission:logisticmodule,consignorcopy,edit');
                Route::get('/Logistic/othersettings', 'othersettings')->name('admin.logisticothersettings')->middleware('checkPermission:logisticmodule,logisticsettings,view');
            });


            // pdf routes ------------------------------------ 
            $PdfController = getadminversion('PdfController');
            Route::controller($PdfController)->group(function () {
                Route::get('/download/{fileName}', 'downloadZip')->name('file.download');
                Route::get('/generatepdf/{id}', 'generatepdf')->name('invoice.generatepdf')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/generatequotationpdf/{id}', 'generatequotationpdf')->name('quotation.generatepdf')->middleware('checkPermission:quotationmodule,quotation,view');
                Route::post('/generatepdfzip', 'generatepdfzip')->name('invoice.generatepdfzip');
                Route::get('/generatereciept/{id}', 'generatereciept')->name('invoice.generatereciept')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/generaterecieptall/{id}', 'generaterecieptall')->name('invoice.generaterecieptll')->middleware('checkPermission:invoicemodule,invoice,view');
                
                // generate consignor copy pdf 
                Route::get('/generateconsignorcopypdf/{id}', 'generateconsignorcopypdf')->name('consignorcopy.generatepdf')->middleware('checkPermission:logisticmodule,consignorcopy,view');
            });
        });
    });
});