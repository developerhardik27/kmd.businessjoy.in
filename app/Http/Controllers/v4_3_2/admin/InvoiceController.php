<?php

namespace App\Http\Controllers\v4_3_2\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InvoiceController extends Controller
{

    public $version, $invoiceModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->invoiceModel = 'App\\Models\\' . $this->version . "\\invoice";
        } else {
            $this->invoiceModel = 'App\\Models\\v4_3_1\\invoice';
        }
    }
    public function invoiceview(string $id)
    {

        request()->merge([
            'company_id' => session('company_id'),
            'user_id' => session('user_id')
        ]);

        $dbname = company::find(Session::get('company_id'));
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        $invoice = $this->invoiceModel::findOrFail($id);
        $this->authorize('view', $invoice);
        $companyController = "App\\Http\\Controllers\\" . $this->version . "\\api\\companyController";
        $bankdetailsController = "App\\Http\\Controllers\\" . $this->version . "\\api\\bankdetailsController";
        $jsoncompanydetailsdata = app($companyController)->companydetailspdf($invoice->company_details_id);
        $jsonbankdetailsdata = app($bankdetailsController)->bankdetailspdf($invoice->account_id);

        $jsoncompanyContent = $jsoncompanydetailsdata->getContent();
        $jsonbankContent = $jsonbankdetailsdata->getContent();

        $companydetailsdata = json_decode($jsoncompanyContent, true);
        $bankdetailsdata = json_decode($jsonbankContent, true);

        $data = [
            'companydetails' => $companydetailsdata['companydetails'][0],
            'bankdetails' => $bankdetailsdata['bankdetail'][0]
        ];

        return view($this->version . '.admin.Invoice.invoiceview', ['id' => $id, 'data' => $data]);
    }

    /**
     * Invoice settings pages.
     */
    public function managecolumn()
    {
        return view($this->version . '.admin.Invoice.managecolumn', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function formula()
    {
        return view($this->version . '.admin.Invoice.formula', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
    public function othersettings()
    {
        return view($this->version . '.admin.Invoice.othersettings', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }

        return view($this->version . '.admin.Invoice.invoice', ['search' => $search]);
    }

    /**
     * Payment Report view
     */
    public function paymentReport(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }

        return view($this->version . '.admin.Invoice.prompt_report', ['search' => $search]);
    }

    /**
     * Send Prompt Report Mail
     */
    public function sendPromptReportMail(Request $request)
    {
        try {
            \Log::info('Send Prompt Report Mail - Request received', [
                'all_request_data' => $request->all(),
                'buyers_raw' => $request->buyers,
                'buyers_type' => gettype($request->buyers)
            ]);

            $buyers = is_array($request->buyers) ? $request->buyers : json_decode($request->buyers, true);

            \Log::info('Buyers data after decode', ['buyers' => $buyers, 'count' => count($buyers)]);

            if (empty($buyers)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No buyers provided'
                ]);
            }

            $emailsSent = 0;
            $errors = [];

            foreach ($buyers as $buyer) {
                try {
                    // Use selected rows from the buyer data
                    $selectedRows = $buyer['rows'] ?? [];

                    \Log::info('Using selected rows for: ' . $buyer['name'], [
                        'rows_count' => count($selectedRows),
                        'buyer_data' => $buyer,
                        'selected_rows' => $selectedRows
                    ]);

                    // Prepare invoice data for email
                    $invoices = [];
                    foreach ($selectedRows as $row) {
                        \Log::info('Processing row', ['row' => $row]);
                        $invoices[] = [
                            'inv_no' => $row['inv_no'] ?? '-',
                            'inv_date' => $row['inv_date_formatted'] ?? '-',
                            'company_name' => $row['garden_company_name'] ?? '-',
                            'amount' => $row['grand_total'] ?? 0,
                            'paid_amount' => $row['part_payment'] == 1 ? ($row['grand_total'] - $row['pending_amount']) : 0,
                            'pending_amount' => $row['pending_amount'] ?? $row['grand_total'],
                            'currency_symbol' => $row['currency_symbol'] ?? '',
                            'credit_days' => $row['credit_days'] ?? '-',
                            'expected_payment_date' => $row['expected_payment_date'] ?? '-',
                            'status' => $row['status'] ?? '-',
                        ];
                    }

                    \Log::info('Prepared invoices for email', [
                        'buyer' => $buyer['name'],
                        'invoices_count' => count($invoices),
                        'invoices' => $invoices
                    ]);

                    // Default message
                    $message = 'Please find the selected payment reminder details for your review.';

                    // Get current user name using EmailLog model method
                    $sentByName = \App\Models\EmailLog::getUserName(session('user_id'));

                    // Create email log entry
                    $emailLog = \App\Models\EmailLog::create([
                        'report_name' => 'Prompt Report',
                        'from_email' => config('mail.from.address'),
                        'to_email' => $buyer['email'],
                        'email_subject' => 'Payment Reminder - ' . $buyer['name'],
                        'email_content' => $message,
                        'status' => 'pending',
                        'sent_by' => session('user_id'),
                        'sent_by_name' => $sentByName,
                    ]);

                    // Send email with buyer-specific data
                    \Mail::to($buyer['email'])->send(new \App\Mail\PromptReportMail(
                        $buyer['name'],
                        $buyer['email'],
                        $invoices,
                        $message,
                        null
                    ));

                    // Update email log as success
                    $emailLog->update([
                        'status' => 'success',
                        'sent_at' => now(),
                    ]);

                    // Clean up old email logs to keep only the most recent 2000 entries
                    \App\Models\EmailLog::cleanupOldLogs();

                    $emailsSent++;
                    \Log::info('Email sent successfully to: ' . $buyer['email']);
                } catch (\Exception $e) {
                    // Log error but continue with other emails
                    $error = 'Failed to send email to ' . $buyer['email'] . ': ' . $e->getMessage();
                    \Log::error($error);
                    $errors[] = $error;

                    // Update email log as failed if it exists
                    if (isset($emailLog)) {
                        $emailLog->update([
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                    }
                }
            }

            if ($emailsSent > 0) {
                $message = 'Mail sent successfully to ' . $emailsSent . ' buyers';
                if (!empty($errors)) {
                    $message .= '. Some emails failed: ' . implode('; ', $errors);
                }
                return response()->json([
                    'status' => 200,
                    'message' => $message
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to send any emails. Buyers processed: ' . count($buyers) . '. Errors: ' . implode('; ', $errors)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Send Prompt Report Mail Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send mail: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $search = '';
        request()->merge([
            'company_id' => session('company_id'),
            'user_id' => session('user_id')
        ]);
        $invoice_data = session::get('invoice_data');
        $lot_no_invoice_data = session::get('lot_no_invoice_data');

        $data = $invoice_data ?: $lot_no_invoice_data; 
        $invoice_numbers = [];

        if (!empty($data['line_items'])) {
            foreach ($data['line_items'] as $item) {
                if (isset($item['Invoice_no'])) {
                    $invoice_numbers[] = $item['Invoice_no'];
                }
            }
        }

        
       
        $company_id = Session::get('company_id');
        $invoiceController = "App\\Http\\Controllers\\" . $this->version . "\\api\\invoiceController";
        $jsoncompanygardenassigndetails = app($invoiceController)->companygardenassig($invoice_numbers);
        $companygardenassigncontent = $jsoncompanygardenassigndetails->getContent();
        $companygardenassigndetails = json_decode($companygardenassigncontent);
      
        $invoicecolumnController = "App\\Http\\Controllers\\" . $this->version . "\\api\\tblinvoicecolumnController";
        $jsoncolumndetails = app($invoicecolumnController)->column_details($company_id);
        $columncontent = $jsoncolumndetails->getContent();
        $columndetails = json_decode($columncontent);

        $invoiceothersettingController = "App\\Http\\Controllers\\" . $this->version . "\\api\\tblinvoiceothersettingController";
        $jsoninvoiceothersettingdetails = app($invoiceothersettingController)->invoicenumberpatternindex($company_id);
        $invoiceothersettingcontent = $jsoninvoiceothersettingdetails->getContent();
        $invoiceothersettingdetails = json_decode($invoiceothersettingcontent);

        if ($invoiceothersettingdetails->status != 200 || count($invoiceothersettingdetails->pattern) < 2) {
            return view($this->version . '.admin.Invoice.othersettings', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }
        if ($companygardenassigndetails->status != 200) {
           return redirect()->route('admin.companymaster')
                ->with("message", $companygardenassigndetails->message);
        }
        if ($columndetails->status != 200) {
            return view($this->version . '.admin.Invoice.managecolumn', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'message' => 'yes']);
        }

        if (empty($invoice_data) && empty($lot_no_invoice_data)) {
            return redirect()->route('admin.invoice')
                ->with("message", "Please create an invoice using a sample purchase or an invoice number/lot first. You cannot create an invoice directly.");
        }
        return view($this->version . '.admin.Invoice.invoiceform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $invoice = $this->invoiceModel::findOrFail($id);

        $is_editable = $invoice->is_editable;

        return view($this->version . '.admin.Invoice.invoiceupdateform', ['edit_id' => $id, 'user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id'), 'is_editable' => $is_editable]);
    }

    /**
     * Display a listing of the resource.
     */
    public function tdsregister(Request $request)
    {
        return view($this->version . '.admin.Invoice.tdsregister');
    }
}
