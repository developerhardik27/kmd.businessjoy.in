<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\company;
use App\Models\invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class invoiceController extends Controller
{
    // chart monthly invoice counting
    public function monthlyInvoiceChart(Request $request)
    {
        $userId = $request->input('user_id');
        $invoices = DB::table('invoices')
        ->select(DB::raw("MONTH(created_at) as month, COUNT(*) as total_invoices, SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_invoices"))
        ->groupBy(DB::raw("MONTH(created_at)"))->where('created_by', $userId)
        ->get();
        
        return $invoices;
        // return view('admin.invoice_chart', compact('invoices'));
    }

    //status vise invoice list
    public function status_list(Request $request)
    {
        $userId = $request->input('user_id');
        $currentMonth = Carbon::now()->format('Y-m');

        $invoices = DB::table('invoices')->whereYear('created_at', Carbon::now()->year)
        ->whereMonth('created_at', Carbon::now()->month)->where('created_by', $userId)
        ->get();
        $groupedInvoices = $invoices->groupBy('status');
        return $groupedInvoices;
    }

    // currency list
    public function currency()
    {
        $currency = DB::table('currency')->orderBy('country')->get();

        if ($currency->count() > 0) {
            return response()->json([
                'status' => 200,
                'currency' => $currency
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'currency' => 'No Records Found'
            ], 404);
        }
    }
    //get bank details
    public function bdetails(Request $request)
    {    
        $userId = $request->user_id;
        $bank = DB::table('bank_details')->get()->where('is_active', 1)->where('is_deleted', 0)->where('created_by',$userId);

        if ($bank->count() > 0) {
            return response()->json([
                'status' => 200,
                'bank' => $bank
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'bank' => 'No Records Found'
            ], 404);
        }
    }

    public function inv_list(Request $request)
    {
        $userId = $request->input('user_id');
        if ($userId == 1) {
            $invoice = DB::table('invoices')->join('customers', 'invoices.customer_id', '=', 'customers.id')
                ->join('country', 'customers.country_id', '=', 'country.id')
                ->join('state', 'customers.state_id', '=', 'state.id')
                ->join('city', 'customers.city_id', '=', 'city.id')
                ->select('invoices.*', 'customers.address', 'customers.firstname', 'customers.lastname', 'country.country_name', 'state.state_name', 'city.city_name')
                ->get()->where('is_deleted', 0)->where('is_active', 1);
        } else {
            $invoice = DB::table('invoices')->join('customers', 'invoices.customer_id', '=', 'customers.id')
                ->join('country', 'customers.country_id', '=', 'country.id')
                ->join('state', 'customers.state_id', '=', 'state.id')
                ->join('city', 'customers.city_id', '=', 'city.id')
                ->select('invoices.*', 'customers.address', 'customers.firstname', 'customers.lastname', 'country.country_name', 'state.state_name', 'city.city_name')
                ->get()->where('is_deleted', 0)->where('is_active', 1)->where('created_by', $userId);
        }

        if ($invoice->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoice' => $invoice
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoice' => 'No Records Found'
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $invoice =  DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->join('country', 'customers.country_id', '=', 'country.id')
            ->join('state', 'customers.state_id', '=', 'state.id')
            ->join('city', 'customers.city_id', '=', 'city.id')
            ->select('invoices.id', 'invoices.inv_no', 'invoices.inv_date', 'invoices.notes', 'invoices.total','invoices.status', 'invoices.gst', 'invoices.grand_total', 'invoices.payment_type', 'invoices.is_active', 'invoices.is_deleted', 'customers.id as cid', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'customers.pincode', 'customers.gst_no', 'country.country_name', 'state.state_name', 'city.city_name')
            ->groupBy('invoices.id', 'invoices.inv_no', 'invoices.inv_date', 'invoices.notes', 'invoices.total','invoices.status', 'invoices.gst', 'invoices.grand_total', 'invoices.payment_type', 'invoices.is_active', 'invoices.is_deleted', 'customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'customers.pincode', 'customers.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'invoice_details.invoice_id')
            ->where('invoices.is_active', 1)->where('invoices.is_deleted', 0)->where('invoices.id', $id)
            ->get();

        if ($invoice->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoice' => $invoice
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoice' => 'No Records Found'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data =   $request->data;
        $itemdata =   $request->iteam_data;


        $validator = Validator::make($request->data, $request->iteam_data, [
            "payment_mode" => 'required',
            "acc_details" => 'required',
            "product_id_1" => 'required',
            "quantity_1" => 'required',
            "item_description_1" => 'required',
            "customer_id" => 'required',
            "price_1" => 'required',
            "total_amount" => 'required|numeric',
            "gst" => 'required|numeric',
            "currency_id" => 'required|numeric',
            "country_id" => 'required|numeric',
            "created_by" => 'required|numeric',
            'notes',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            // fetch last record from invoice tbl for generate dynamic inv no
            $lastrec = DB::table('invoices')->orderBy('id', 'desc')->first();
            if ($lastrec) {
                $lastinv_no = explode('-', $lastrec->inv_no);
                $lastinv_no_id = $lastinv_no[2];
                $inv_no = '';
                $date = date('y') - $lastinv_no[1];
                if ($date == 0) {
                    if ($data['country_id'] == 1) {
                        $inv_no = "IND-" . date('y') . "-" . ($lastinv_no_id + 1);
                    } else {
                        $inv_no = "EXP-" . date('y') . "-" . ($lastinv_no_id + 1);
                    }
                }
            } else {
                if ($data['country_id'] == 1) {
                    $inv_no = "IND-" . date('y') . "-1";
                } else {
                    $inv_no = "EXP-" . date('y') .  "-1";
                }
            }
           
            $company_details = company::find($data['company_id']);

            if($company_details){

                $company_details_id = $company_details->company_details_id;
                $invoice = DB::table('invoices')->insertGetId([
                    'inv_no' => $inv_no,
                    'customer_id' => $data['customer_id'],
                    'notes' => $data['notes'],
                    'total' =>  $data['total_amount'],
                    'gst' =>  $data['gst'],
                    'grand_total' => ceil($data['total_amount'] + $data['gst']),
                    'currency_id' =>  $data['currency_id'],
                    'payment_type' =>  $data['payment_mode'],
                    'account_id' =>  $data['acc_details'],
                    'company_id' =>  $data['company_id'],
                    'company_details_id' =>  $company_details_id,
                    'created_by' =>  $data['created_by']
                ]);
    
                if ($invoice) {
                    $inv_id = $invoice;
                    foreach ($itemdata as $key => $value) {
                        $invoice_details = DB::table('invoice_details')->insert([
                            'invoice_id' => $inv_id,
                            'product_id' => $value[0],
                            'product_name' => $value[1],
                            'item_description' => $value[2],
                            'quantity' => $value[3],
                            'price' => $value[4],
                            'total_amount' => ($value[3] * $value[4]),
                            'currency_id' => $data['currency_id'],
                            'created_by' => $data['created_by']
                        ]);
                    }
    
    
                    if ($invoice_details) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'invoice  succesfully created'
                        ]);
                    } else {
                        $id = $invoice;
                        $record = invoice::find($id);
                        // Check if the record exists
                        if ($record) {
                            // Delete the record
                            $record->delete();
                        }
                        return response()->json([
                            'status' => 500,
                            'message' => 'invoice details not succesfully created'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'invoice not succesfully created'
                    ], 500);
                }
            }else{
                return response()->json([
                    'status' => 500,
                    'message' => 'company Details not found'
                ], 500);
            }

           
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('invoice_details', 'invoices.id', '=', 'invoice_details.invoice_id')
            ->join('products', 'invoice_details.product_id', '=', 'products.id')
            ->select('invoices.*', 'customers.firstname', 'customers.lastname', 'invoice_details.item_description', 'invoice_details.price', 'products.price_per_unit')
            ->get()->where('is_deleted', 0)->where('is_active', 1)->where('id', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoices = DB::table('invoices')
            ->where('id', $id)
            ->update([
                'is_deleted' => 1
            ]);
        if ($invoices) {
            $invoice_details =  DB::table('invoice_details')
                ->where('invoice_id', $id)
                ->update([
                    'is_deleted' => 1
                ]);


            if ($invoice_details) {
                return response()->json([
                    'status' => 200,
                    'message' => 'invoice succesfully deleted'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'invoice not succesfully delete!'
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'invoice not succesfully delete!'
            ]);
        }
    }


    public function inv_details(string $id)
    {
        $invoice = DB::table('invoice_details')
            ->get()->where('invoice_id', $id);

        if ($invoice->count() > 0) {
            return response()->json([
                'status' => 200,
                'invoice' => $invoice
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'invoice' => 'No Records Found'
            ]);
        }
    }

    public function status(Request $request, string $id)
    {
        $invoices = DB::table('invoices')
            ->where('id', $id)
            ->update([
                'status' => $request->status
            ]);
        if ($invoices){
            return response()->json([
                'status' => 200,
                'message' => 'status updated'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'invoice  status not succesfully updated!'
            ]);
        }
    }
}
