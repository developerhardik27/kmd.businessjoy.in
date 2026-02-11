<?php

use Illuminate\Http\Response;
use App\Http\Middleware\CheckSession;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\AmazonController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\landing\LandingPageController;
use App\Http\Controllers\v4_3_2\admin\HrController;

// login page on / 
Route::redirect('/', '/admin/login/');
// Define a function to generate the controller class name based on the session value
if (!function_exists('getadminversion')) {
    function getadminversion($controller)
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $version = $_SESSION['folder_name'];
            return 'App\\Http\\Controllers\\' . $version . '\\admin\\' . $controller;
        } else {
            return 'App\\Http\\Controllers\\v4_2_3\\admin\\' . $controller;
        }
    }
}
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

        Route::controller(AmazonController::class)->group(function () {
            Route::get('/amazon/callback', 'amazoncallback')->name('amazon.callback');
        });

        Route::get('/setmenusession', [AdminLoginController::class, 'setmenusession'])->name('admin.setmenusession');

        Route::group(['middleware' => 'admin.guest'], function () {
            Route::controller(AdminLoginController::class)->group(function () {
                Route::get('/login', 'index')->name('admin.login')->withoutMiddleware([CheckSession::class]);
                Route::match(['get', 'post'], '/authenticate/{id?}', 'authenticate')->name('admin.authenticate')->withoutMiddleware([CheckSession::class]);
                Route::get('/forgotpassword', 'forgot')->name('admin.forgot')->withoutMiddleware([CheckSession::class]);
                Route::post('/forgotpassword', 'forgot_password')->name('admin.forgotpassword')->withoutMiddleware([CheckSession::class]);
                Route::get('/reset/{token}', 'reset_password')->name('admin.resetpassword')->withoutMiddleware([CheckSession::class]);
                Route::post('/reset/{token}', 'post_reset_password')->name('admin.post_resetpassword')->withoutMiddleware([CheckSession::class]);
                Route::get('/setpassword/{token}', 'set_password')->name('admin.setpassword')->withoutMiddleware([CheckSession::class]);
                Route::post('/setpassword/{token}', 'post_set_password')->name('admin.post_setpassword')->withoutMiddleware([CheckSession::class]);
            });
        });
    
        Route::group(['middleware' => 'admin.auth'], function () {

            Route::get('/superadminloginfromanyuser/{userId}', [AdminLoginController::class, 'authenticate'])->name('admin.superadminloginfromanyuser');

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
                Route::get('/MyLoginHistory/{id}', 'loginhistory')->name('admin.myloginhistory')->middleware('checkPermission:adminmodule,loginhistory,show');
                Route::get('/User', 'index')->name('admin.user')->middleware('checkPermission:adminmodule,user,show');
                Route::get('/AddNewUser', 'create')->name('admin.adduser')->middleware('checkPermission:adminmodule,user,add');
                Route::get('/EditUser/{id}', 'edit')->name('admin.edituser')->middleware('checkPermission:adminmodule,user,edit');
                Route::get('/EditUserdetail/{id}', 'edituser')->name('admin.edituserdetail')->middleware('checkPermission:adminmodule,user,edit');
                Route::get('/userprofile/{id}', 'profile')->name('admin.userprofile');

                Route::get('/UserPermissionGroup', 'userrolepermission')->name('admin.userrolepermission')->middleware('checkPermission:adminmodule,userpermission,show');
                Route::get('/AddNewUserPermissionGroup', 'createuserrolepermission')->name('admin.adduserrolepermission')->middleware('checkPermission:adminmodule,userpermission,add');
                Route::get('/EditUserPermissionGroup/{id}', 'edituserrolepermission')->name('admin.edituserrolepermission')->middleware('checkPermission:adminmodule,userpermission,edit');
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

                Route::get('/tdsregister', 'tdsregister')->name('admin.tdsregister')->middleware('checkPermission:invoicemodule,tdsregister,show');
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
                Route::get('/Lead/Settings', 'leadSettings')->name('admin.leadsettings')->middleware('checkPermission:leadmodule,leadsettings,show');
                Route::get('/UpcomingFollowUp', 'upcomingfollowup')->name('admin.upcomingfollowup')->middleware('checkPermission:leadmodule,upcomingfollowup,show');
                Route::get('/Lead/Analysis', 'analysis')->name('admin.analysis')->middleware('checkPermission:leadmodule,analysis,show');
                Route::get('/Lead/OwnerPerformance', 'leadownerperformance')->name('admin.leadownerperformance')->middleware('checkPermission:leadmodule,leadownerperformance,show');
                Route::get('/Lead/RecentActivity', 'recentactivity')->name('admin.recentactivity')->middleware('checkPermission:leadmodule,recentactivity,show');
                Route::get('/Lead/Calendar', 'calendar')->name('admin.calendar')->middleware('checkPermission:leadmodule,calendar,show');
                Route::get('/AddNewLead', 'create')->name('admin.addlead')->middleware('checkPermission:leadmodule,lead,add');
                Route::get('/EditLead/{id}', 'edit')->name('admin.editlead')->middleware('checkPermission:leadmodule,lead,edit');
                Route::get('/Lead/Api', 'leadapi')->name('admin.leadapi')->middleware('checkPermission:leadmodule,leadapi,show');
                Route::get('/Lead/ImportFromExcel', 'importfromexcel')->name('admin.importfromexcel')->middleware('checkPermission:leadmodule,import,add');
                Route::get('/lead/ImportFromExcel/template', 'downloadLeadTemplate')->name('lead.importtemplatedownload');
                Route::get('/lead/ExportHistory', 'exporthistory')->name('admin.exportleadhistory')->middleware('checkPermission:leadmodule,export,show');
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
                Route::get('/Blog/Settings', 'blogsettings')->name('admin.blogsettings')->middleware('checkPermission:blogmodule,blogsettings,show');
                Route::get('/AddNewBlog', 'create')->name('admin.addblog')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/BlogTag', 'blogtag')->name('admin.blogtag')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/BlogCategory', 'blogcategory')->name('admin.blogcategory')->middleware('checkPermission:blogmodule,blog,add');
                Route::get('/EditBlog/{id}', 'edit')->name('admin.editblog')->middleware('checkPermission:blogmodule,blog,edit');
                Route::get('/blog/Api', 'blogapi')->name('admin.blogapi')->middleware('checkPermission:blogmodule,blogapi,show');
            });

            // hr module route start
            $HrController = getadminversion('HrController');
            Route::controller($HrController)->group(function () {
                Route::get('/employee', 'index')->name('admin.employee')->middleware('checkPermission:hrmodule,employees,show');
                Route::get('/AddNewemployee', 'create')->name('admin.addemployee.form')->middleware('checkPermission:hrmodule,employees,add');
                Route::get('/Editemployee/{id}', 'edit')->name('admin.editemployee')->middleware('checkPermission:hrmodule,employees,edit');
                Route::get('/companiesholidays', 'companiesholidays')->name('admin.companiesholidays')->middleware('checkPermission:hrmodule,companiesholidays,show');
                Route::get('/letter', 'letter')->name('admin.letter');
                Route::get('/letter/edit/{id}', 'editletter')->name('admin.editletter');
            });




            // hr module route end

            // tea module route start
            $companymasterController = getadminversion('companymasterController');
            Route::controller($companymasterController)->group(function () {
                Route::get('/companymaster', 'index')->name('admin.companymaster')->middleware('checkPermission:teamodule,companymaster,show');
                Route::get('/AddNewcompanymaster', 'create')->name('admin.companymasterform')->middleware('checkPermission:teamodule,companymaster,add');
                Route::get('/Editcompanymaster/{id}', 'edit')->name('admin.companymasterupdateform')->middleware('checkPermission:teamodule,companymaster,edit');
                Route::get('/garden', 'gardenindex')->name('admin.garden')->middleware('checkPermission:teamodule,garden,show');
                Route::get('/AddNewgarden', 'gardencreate')->name('admin.gardenform')->middleware('checkPermission:teamodule,garden,add');
                Route::get('/Editgarden/{id}', 'gardenedit')->name('admin.gardenupdateform')->middleware('checkPermission:teamodule,garden,edit');
            });

            $partyController = getadminversion('partyController');
            Route::controller($partyController)->group(function () {
                Route::get('/party', 'partyindex')->name('admin.party')->middleware('checkPermission:teamodule,party,show');
                Route::get('/AddNewparty', 'partycreate')->name('admin.partyform')->middleware('checkPermission:teamodule,party,add');
                Route::get('/Editparty/{id}', 'partyedit')->name('admin.partyupdateform')->middleware('checkPermission:teamodule,party,edit');
                Route::get('/grade', 'gradeindex')->name('admin.grade')->middleware('checkPermission:teamodule,grade,show');
                Route::get('/AddNewgrade', 'gradecreate')->name('admin.gradeform')->middleware('checkPermission:teamodule,grade,add');
                Route::get('/Editgrade/{id}', 'gradeedit')->name('admin.gradeupdateform')->middleware('checkPermission:teamodule,grade,edit');
            });
            $brokerpurchaseController = getadminversion('brokerPurchaseController');
            Route::controller($brokerpurchaseController)->group(function () {
                Route::get('/brokerpurchase', 'index')->name('admin.brokerpurchase')->middleware('checkPermission:teamodule,brokerpurchase,show');
                Route::get('/AddNewbrokerpurchase', 'create')->name('admin.brokerpurchaseform')->middleware('checkPermission:teamodule,brokerpurchase,add');
                Route::get('/Editbrokerpurchase/{id}', 'edit')->name('admin.brokerpurchaseupdateform')->middleware('checkPermission:teamodule,brokerpurchase,edit');
                Route::post('/storeInvoiceSession', 'storeInvoiceSession')->name('admin.storeInvoiceSession')->middleware('checkPermission:teamodule,brokerpurchase,show');
                Route::post('/lot_no_storeInvoiceSession', 'lot_no_storeInvoiceSession')->name('admin.lot_no_storeInvoiceSession')->middleware('checkPermission:teamodule,brokerpurchase,show');
            });
            $brokeragebillController = getadminversion('brokeragebillController');
            Route::controller($brokeragebillController)->group(function () {
                Route::get('/brokeragebill', 'index')->name('admin.brokeragebill')->middleware('checkPermission:teamodule,brokeragebill,show');
                Route::get('/brokeragebillpdflist', 'brokeragebillpdflist')->name('admin.brokeragebillpdflist')->middleware('checkPermission:teamodule,brokeragebill,show');
                Route::get('/Addbrokeragebill/{id?}', 'create')->name('admin.brokeragebillform')->middleware('checkPermission:teamodule,brokeragebill,add');
            });

            $orderController = getadminversion('orderController');
            Route::controller($orderController)->group(function () {
                Route::get('/order', 'index')->name('admin.order')->middleware('checkPermission:teamodule,teadashboard,show');
                Route::get('/AddNeworder', 'create')->name('admin.orderform')->middleware('checkPermission:teamodule,teadashboard,add');
                Route::get('/Editorder/{id}', 'edit')->name('admin.orderupdateform')->middleware('checkPermission:teamodule,teadashboard,edit');
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
                Route::get('/lrcolumnmapping', 'lrcolumnmapping')->name('admin.lrcolumnmapping')->middleware('checkPermission:logisticmodule,lrcolumnmapping,add');
            });

            //transporter billing route 
            $TransporterBillingController = getadminversion('TransporterBillingController');
            Route::controller($TransporterBillingController)->group(function () {
                Route::get('/TransporterBilling', 'index')->name('admin.transporterbilling')->middleware('checkPermission:logisticmodule,transporterbilling,show');
                Route::get('/AddNewTransporterBilling', 'create')->name('admin.addtransporterbilling')->middleware('checkPermission:logisticmodule,transporterbilling,add');
                Route::get('/EditTransporterBilling/{id}', 'edit')->name('admin.edittransporterbilling')->middleware('checkPermission:logisticmodule,transporterbilling,edit');
            });

            /**
             * logistic module route end
             */


            /**
             * developer module route start
             */
            $SystemMonitorController = getadminversion('SystemMonitorController');
            Route::controller($SystemMonitorController)->group(function () {
                Route::get('/Developer/SlowPages', 'slowpages')->name('admin.slowpages')->middleware('checkPermission:developermodule,slowpage,show');
                Route::get('/Developer/ErrorLogs', 'errorlogs')->name('admin.errorlogs')->middleware('checkPermission:developermodule,errorlog,show');
                Route::get('/Developer/CronJobs', 'cronjobs')->name('admin.cronjobs')->middleware('checkPermission:developermodule,cronjob,show');
                Route::get('/Developer/TechnicalDocs', 'techdocs')->name('admin.techdocs')->middleware('checkPermission:developermodule,techdoc,show');
                Route::get('/Developer/VersionDocs', 'versiondocs')->name('admin.versiondocs')->middleware('checkPermission:developermodule,versiondoc,show');
                Route::get('/Developer/RecentActivityData', 'recentactivitydata')->name('admin.recentactivitydata')->middleware('checkPermission:developermodule,recentactivitydata,show');
                Route::get('/Developer/RecentActivityData/Add', 'createrecentactivitydata')->name('admin.addrecentactivitydata')->middleware('checkPermission:developermodule,recentactivitydata,add');
                Route::get('/Developer/RecentActivityData/Edit/{id}', 'editrecentactivitydata')->name('admin.editrecentactivitydata')->middleware('checkPermission:developermodule,recentactivitydata,edit');
                Route::get('/Developer/ClearData', 'clearData')->name('admin.cleardata')->middleware('checkPermission:developermodule,cleardata,show');
                Route::get('/Developer/AutomateTest', 'automatetest')->name('admin.automatetest')->middleware('checkPermission:developermodule,automatetest,show');
                Route::get('/Developer/queues', 'queues')->name('admin.queues')->middleware('checkPermission:developermodule,queues,show');
            });

            /**
             * developer module route end
             */

            // pdf routes ------------------------------------ 
            $PdfController = getadminversion('PdfController');
            Route::controller($PdfController)->group(function () {
                Route::get('/download/{fileName}', 'downloadZip')->name('file.download');
                Route::get('/generatepdf/{id}', 'generatepdf')->name('invoice.generatepdf')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/generatequotationpdf/{id}', 'generatequotationpdf')->name('quotation.generatepdf')->middleware('checkPermission:quotationmodule,quotation,view');
                Route::post('/generatepdfzip', 'generatepdfzip')->name('invoice.generatepdfzip');
                Route::get('/generatereciept/{id}', 'generatereciept')->name('invoice.generatereciept')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/generaterecieptall/{id}', 'generaterecieptall')->name('invoice.generaterecieptll')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/brokerbillgeneraterecieptall/{id}', 'brokerBillgeneraterecieptall')->name('invoice.brokerBillgeneraterecieptall')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/brokerbillgeneratereciept/{id}', 'brokerBillgeneratereciept')->name('invoice.brokerBillgeneratereciept')->middleware('checkPermission:invoicemodule,invoice,view');
                // generate consignor copy pdf 
                Route::get('/generateconsignorcopypdf/{id}', 'generateconsignorcopypdf')->name('consignorcopy.generatepdf')->middleware('checkPermission:logisticmodule,consignorcopy,view');
                Route::get('/generatebrokragebillpdf/{id}', 'generatebrokragebillpdf')->name('brokragbill.generatebrokragebillpdf')->middleware('checkPermission:invoicemodule,invoice,view');
                Route::get('/orderreport', 'orderreport')->name('brokragbill.orderreport')->middleware('checkPermission:teamodule,teadashboard,show');
                Route::get('/outstanding', 'outstanding')->name('brokragbill.outstanding')->middleware('checkPermission:teamodule,teadashboard,show');
            });

            Route::controller(AmazonController::class)->group(function () {
                Route::get('/amazon/authorize', 'amazonauthorize')->name('amazon.authorize');
            });
        });
    });
});
