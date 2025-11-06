<?php

namespace App\Http\Controllers\v4_2_3\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class TblLeadController extends Controller
{

    public $version, $leadModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->leadModel = 'App\\Models\\' . $this->version . "\\tbllead";
        } else {
            $this->leadModel = 'App\\Models\\v4_2_3\\tbllead';
        }
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
        return view($this->version . '.admin.Lead.lead', ['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     * create new lead
     */
    public function create()
    {
        return view($this->version . '.admin.Lead.leadform');
    }

    /**
     * Show the form for editing the specified resource.
     * edit lead
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Lead.leadupdateform', ['edit_id' => $id]);
    }

    /**
     * Display a listing of the resource.
     * lead upcoming follow up
     */
    public function upcomingfollowup()
    {
        return view($this->version . '.admin.Lead.upcomingfollowup');
    }

    /**
     * Summary of analysis
     * lead analysis page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function analysis()
    {
        return view($this->version . '.admin.Lead.analysis');
    }

    /**
     * Summary of leadownerperformance
     * lead owner performance view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function leadownerperformance()
    {
        return view($this->version . '.admin.Lead.leadownerperformance');
    }

    /**
     * Summary of recentactivity
     * lead recent activity 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function recentactivity()
    {
        return view($this->version . '.admin.Lead.recentactivity');
    }

    /**
     * Summary of calendar
     * next follow up and call history in calendar view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function calendar()
    {
        return view($this->version . '.admin.Lead.calendar');
    }

    /**
     * Display a listing of the resource.
     * return leadapi table view
     */
    public function leadapi()
    {
        return view($this->version . '.admin.otherapi', ['module' => 'lead']);
    }

    /**
     *  Display a listing of the resource.
     * return export history table view
     */

    public function exportHistory()
    {
        return view($this->version . '.admin.Lead.exporthistory');
    }

    /**
     * Display a listing of the resource.
     * return leadapi table view
     */
    public function importfromexcel()
    {
        return view($this->version . '.admin.Lead.importfromexcel');
    }

    public function downloadLeadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // ===== Sheet 1: Leads =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads');

        $headers = [
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'lead_title',
            'job_title',
            'budget',
            'company_name',
            'status',
            'lead_stage',
            'customer_type',
            'source',
            'website_url',
            'notes'
        ];

        $sheet->fromArray($headers, NULL, 'A1');

        $example = [
            'John',
            'Doe',
            'john@example.com',
            '1234567890',
            'Project Inquiry',
            'Manager',
            '$5,000 - $10,000',
            'Acme Corp',
            'New Lead',
            'Quotation',
            'Global',
            'LinkedIn',
            'https://www.example.com',
            'Hot lead'
        ];

        $sheet->fromArray($example, NULL, 'A2');

        // ===== Sheet 2: Reference =====
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Reference');

        // Define lists
        $jobTitles = ['Student', 'Employee', 'Manager', 'Business Owner', 'Self Employeed', 'Other'];
        $budgets = ['10,000 to 50,000', 'More than 50,000', 'More than 1,00,000', 'Less than $1000', '$1,000 - $5,000', '$5,000 - $10,000', 'More than $10,000'];
        $customerTypes = ['Local', 'Global'];
        $statuses = ['Not Interested', 'Not Receiving', 'New Lead', 'Interested', 'Switch Off', 'Does Not Exist', 'Email Sent', 'Wrong Number', 'By Mistake', 'Positive', 'Busy', 'Call Back'];
        $leadStages = ['New Lead', 'Requirement Gathering', 'Quotation', 'In Followup', 'Sale', 'Cancelled', 'Disqualified', 'Future Lead', 'Retargeting'];

        // Reference sheet headers
        $refHeaders = ['job_title', 'budget', 'customer_type', 'status', 'lead_stage'];
        $refSheet->fromArray($refHeaders, NULL, 'A1');

        // Find max length among all lists to align rows nicely
        $maxLength = max(
            count($jobTitles),
            count($budgets),
            count($customerTypes),
            count($statuses),
            count($leadStages)
        );

        // Fill reference columns row-wise
        for ($i = 0; $i < $maxLength; $i++) {
            $refSheet->setCellValue('A' . ($i + 2), $jobTitles[$i] ?? '');
            $refSheet->setCellValue('B' . ($i + 2), $budgets[$i] ?? '');
            $refSheet->setCellValue('C' . ($i + 2), $customerTypes[$i] ?? '');
            $refSheet->setCellValue('D' . ($i + 2), $statuses[$i] ?? '');
            $refSheet->setCellValue('E' . ($i + 2), $leadStages[$i] ?? '');
        }

        // ===== Export the file =====
        $writer = new Xlsx($spreadsheet);
        $fileName = 'lead_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
