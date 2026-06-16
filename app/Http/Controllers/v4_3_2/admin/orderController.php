<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class orderController extends Controller
{
    public $version, $orderModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->orderModel = 'App\\Models\\' . $this->version . "\\order";
        } else {
            $this->orderModel = 'App\\Models\\v4_3_2\\order';
        }
    }
    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.order.order', ["search" => $search]);
    }
    public function create()
    {
        request()->merge([
            'company_id' => session('company_id'),
            'user_id' => session('user_id')
        ]);
        $partycontroller = "App\\Http\\Controllers\\" . $this->version . "\\api\\partyController";
        $jsonbuyerdetails = app($partycontroller)->buyerindex();
        $buyerdetailscontent = $jsonbuyerdetails->getContent();
        $pdetails = json_decode($buyerdetailscontent);

        $partycontroller = "App\\Http\\Controllers\\" . $this->version . "\\api\\partyController";
        $jsontransportdetails = app($partycontroller)->transportindex();
        $transportdetailscontent = $jsontransportdetails->getContent();
        $tdetails = json_decode($transportdetailscontent);

        $partycontroller = "App\\Http\\Controllers\\" . $this->version . "\\api\\partyController";
        $jsongradedetails = app($partycontroller)->gradeindex();
        $gradedetailscontent = $jsongradedetails->getContent();
        $gradedetails = json_decode($gradedetailscontent);

        $companymasterController = "App\\Http\\Controllers\\" . $this->version . "\\api\\companymasterController";
        $jsongardendetails = app($companymasterController)->gardenindex();
        $gardendetailscontent = $jsongardendetails->getContent();
        $gardendetails = json_decode($gardendetailscontent);

        if ($gardendetails->status != 200) {
            return redirect()->route('admin.gardenform')->with("message", "Please create Garden before creating Order");
        }
        if ($gradedetails->status != 200) {
            return redirect()->route('admin.gradeform')->with("message", "Please create Grade before creating Order");
        }
        if ($tdetails->status != 200) {
            return redirect()->route('admin.partyform')->with("message", "Please create Transporter before creating Order");
        }
        if ($pdetails->status != 200) {
            return redirect()->route('admin.partyform')->with("message", "Please create Party before creating Order");
        }
        return view($this->version . '.admin.order.orderform', ['company_id' => Session::get('company_id')]);
    }
    public function edit($id)
    {
        return view($this->version . '.admin.order.orderupdateform', ['edit_id' => $id]);
    }
   
    public function expectedDispatchReport()
    {
        return view($this->version . '.admin.order.expecteddispatchreport');
    }
    public function pendingInvoiceReport()
    {
        return view($this->version . '.admin.order.pendinginvoicereport');
    }
    public function pendingSamplePurchaseReport()
    {
        return view($this->version . '.admin.order.pendingsamplepurchasereport');
    }
    public function turnoverReport()
    {
        return view($this->version . '.admin.order.turnoverreport');
    }

    public function sendExpectedDispatchMail(Request $request)
    {
        try {
            $companies = json_decode($request->companies, true);
            $token = $request->token;
            $user_id = $request->user_id;
            $company_id = $request->company_id;

            // Debug: Log received data
            \Log::info('Send Mail Request Data', [
                'companies' => $companies,
                'token' => $token,
                'user_id' => $user_id,
                'company_id' => $company_id
            ]);

            // Check if companies array is empty
            if (empty($companies) || !is_array($companies)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No companies found in request'
                ]);
            }

            // Check if any company has null or blank email
            $companiesWithoutEmail = [];
            $companiesWithEmail = [];
            foreach ($companies as $company) {
                if (empty($company['email']) || trim($company['email']) === '') {
                    $companiesWithoutEmail[] = $company['name'];
                } else {
                    $companiesWithEmail[] = $company;
                }
            }

            \Log::info('Email Check Results', [
                'companies_without_email' => $companiesWithoutEmail,
                'companies_with_email' => count($companiesWithEmail),
                'total_companies' => count($companies)
            ]);

            // If any company is missing email, return error with company names
            if (!empty($companiesWithoutEmail)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Some companies are missing email addresses: ' . implode(', ', $companiesWithoutEmail),
                    'missing_email_companies' => $companiesWithoutEmail
                ]);
            }

            // All companies have emails, proceed to send individual emails
            $emailsSent = 0;
            $errors = [];
            
            foreach ($companiesWithEmail as $company) {
                try {
                    \Log::info('Attempting to send email to: ' . $company['email'] . ' for company: ' . $company['name']);
                    
                    // Use selected rows from the company data
                    $selectedRows = $company['rows'] ?? [];
                    
                    \Log::info('Using selected rows for: ' . $company['name'], [
                        'rows_count' => count($selectedRows)
                    ]);

                    // Get current user name using EmailLog model method
                    $sentByName = \App\Models\EmailLog::getUserName(session('user_id'));

                    // Create email log entry
                    $emailLog = \App\Models\EmailLog::create([
                        'report_name' => 'Expected Dispatch Report',
                        'from_email' => config('mail.from.address'),
                        'to_email' => $company['email'],
                        'email_subject' => 'Expected Dispatch Report - ' . $company['name'],
                        'email_content' => 'Expected dispatch report for your records',
                        'status' => 'pending',
                        'sent_by' => session('user_id'),
                        'sent_by_name' => $sentByName,
                    ]);

                    // Send email with selected rows only
                    \Mail::to($company['email'])->send(new \App\Mail\ExpectedDispatchReportMail(
                        [$company], // Only this company
                        $selectedRows, // Selected rows only
                        null,
                        null,
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
                    \Log::info('Email sent successfully to: ' . $company['email']);
                } catch (\Exception $e) {
                    // Log error but continue with other emails
                    $error = 'Failed to send email to ' . $company['email'] . ': ' . $e->getMessage();
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
                $message = 'Mail sent successfully to ' . $emailsSent . ' companies';
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
                    'message' => 'Failed to send any emails. Companies processed: ' . count($companies) . '. Companies with email: ' . count($companiesWithEmail) . '. Errors: ' . implode('; ', $errors)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Send Mail Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send mail: ' . $e->getMessage()
            ]);
        }
    }

    public function sendPendingInvoiceMail(Request $request)
    {
        try {
            \Log::info('Send Pending Invoice Mail - Request received', $request->all());

            $companies = json_decode($request->companies, true);

            if (empty($companies)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No companies provided'
                ]);
            }

            $emailsSent = 0;
            $errors = [];

            foreach ($companies as $company) {
                try {
                    // Use selected rows from the company data
                    $selectedRows = $company['rows'] ?? [];
                    
                    \Log::info('Using selected rows for: ' . $company['name'], [
                        'rows_count' => count($selectedRows)
                    ]);

                    // Get current user name using EmailLog model method
                    $sentByName = \App\Models\EmailLog::getUserName(session('user_id'));

                    // Create email log entry
                    $emailLog = \App\Models\EmailLog::create([
                        'report_name' => 'Pending Invoice Report',
                        'from_email' => config('mail.from.address'),
                        'to_email' => $company['email'],
                        'email_subject' => 'Pending Invoice Report - ' . $company['name'],
                        'email_content' => 'Pending invoice report for your records',
                        'status' => 'pending',
                        'sent_by' => session('user_id'),
                        'sent_by_name' => $sentByName,
                    ]);

                    // Send email with selected rows only
                    \Mail::to($company['email'])->send(new \App\Mail\PendingInvoiceReportMail(
                        [$company], // Only this company
                        $selectedRows, // Selected rows only
                        null,
                        null,
                        null,
                        null,
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
                    \Log::info('Email sent successfully to: ' . $company['email']);
                } catch (\Exception $e) {
                    // Log error but continue with other emails
                    $error = 'Failed to send email to ' . $company['email'] . ': ' . $e->getMessage();
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
                $message = 'Mail sent successfully to ' . $emailsSent . ' companies';
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
                    'message' => 'Failed to send any emails. Companies processed: ' . count($companies) . '. Errors: ' . implode('; ', $errors)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Send Pending Invoice Mail Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send mail: ' . $e->getMessage()
            ]);
        }
    }

    public function sendPendingSampleMail(Request $request)
    {
        try {
            \Log::info('Send Pending Sample Mail - Request received', $request->all());

            $companies = json_decode($request->companies, true);

            if (empty($companies)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No companies provided'
                ]);
            }

            $emailsSent = 0;
            $errors = [];

            foreach ($companies as $company) {
                try {
                    // Use selected rows from the company data
                    $selectedRows = $company['rows'] ?? [];
                    
                    \Log::info('Using selected rows for: ' . $company['name'], [
                        'rows_count' => count($selectedRows)
                    ]);

                    // Get current user name using EmailLog model method
                    $sentByName = \App\Models\EmailLog::getUserName(session('user_id'));

                    // Create email log entry
                    $emailLog = \App\Models\EmailLog::create([
                        'report_name' => 'Pending Sample Report',
                        'from_email' => config('mail.from.address'),
                        'to_email' => $company['email'],
                        'email_subject' => 'Pending Sample Report - ' . $company['name'],
                        'email_content' => 'Pending sample report for your records',
                        'status' => 'pending',
                        'sent_by' => session('user_id'),
                        'sent_by_name' => $sentByName,
                    ]);

                    // Send email with selected rows only
                    \Mail::to($company['email'])->send(new \App\Mail\PendingSampleReportMail(
                        [$company], // Only this company
                        $selectedRows, // Selected rows only
                        null,
                        null,
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
                    \Log::info('Email sent successfully to: ' . $company['email']);
                } catch (\Exception $e) {
                    // Log error but continue with other emails
                    $error = 'Failed to send email to ' . $company['email'] . ': ' . $e->getMessage();
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
                $message = 'Mail sent successfully to ' . $emailsSent . ' companies';
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
                    'message' => 'Failed to send any emails. Companies processed: ' . count($companies) . '. Errors: ' . implode('; ', $errors)
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Send Pending Sample Mail Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send mail: ' . $e->getMessage()
            ]);
        }
    }
}
