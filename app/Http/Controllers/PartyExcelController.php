<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\v4_3_2\api\commonController;
use Carbon\Carbon;

class PartyExcelController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $partyModel, $companymasterModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id ?? $request->input('company_id');
        $this->userId    = $request->user_id ?? $request->input('user_id') ?? 'excel_import';

        // Set dynamic database connection based on company_id
        if ($this->companyId) {
            $this->dbname($this->companyId);
        }

        $this->masterdbname       = DB::connection()->getDatabaseName();
        $this->partyModel         = $this->getmodel('party');
        $this->companymasterModel = $this->getmodel('companymaster');
        $this->gardenModel        = $this->getmodel('garden');
        $this->companyGardenModel = $this->getmodel('company_garden'); 
    }

    public function showForm()
    {
        return view('import_partys');
    }

    public function import(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'file'        => 'required|mimes:xlsx,xls,csv',
            'import_type' => 'required|in:party,company,garden,company_garden',
        ]);

        $importType = $request->input('import_type'); // 'party' or 'company'

        try {
            $file     = $request->file('file');
            $filePath = $file->storeAs(
                'temp',
                'import_' . time() . '.' . $file->getClientOriginalExtension()
            );

            \Log::info('Starting import. Type: ' . $importType . ' | File: ' . $file->getClientOriginalName());

            $spreadsheet = IOFactory::load(storage_path('app/' . $filePath));
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();
            $totalRows   = count($rows);

            \Log::info('Total rows in Excel: ' . $totalRows);

            $duplicates = [];
            $imported = 0;
            $errors = [];

            // ----------------------------------------------------------------
            // Pre-load states & cities for fast case-insensitive lookup
            // ----------------------------------------------------------------
            $statesRaw = DB::table('state')->select('id', 'state_name')->get();
            $states     = [];
            $stateNames = [];
            foreach ($statesRaw as $s) {
                $states[strtolower(trim($s->state_name))]  = $s->id;
                $stateNames[$s->id] = ucwords(strtolower(trim($s->state_name)));
            }

            $citiesRaw = DB::table('city')->select('id', 'city_name', 'state_id')->get();
            $cities     = [];
            $cityNames  = [];
            foreach ($citiesRaw as $c) {
                $key            = strtolower(trim($c->city_name)) . '_' . $c->state_id;
                $cities[$key]   = $c->id;
                $cityNames[$c->id] = ucwords(strtolower(trim($c->city_name)));
            }

            // ----------------------------------------------------------------
            // Process rows
            // ----------------------------------------------------------------
            foreach ($rows as $index => $row) {
                try {
                    // Skip first 5 rows (title / header rows)
                    if ($index < 5) {
                        \Log::info('Row ' . ($index + 1) . ' (skipped - header): ' . json_encode($row));
                        continue;
                    }

                    // Skip completely empty rows
                    $isEmptyRow = true;
                    foreach ($row as $cell) {
                        if (!empty(trim((string)$cell))) {
                            $isEmptyRow = false;
                            break;
                        }
                    }
                    if ($isEmptyRow) {
                        continue;
                    }

                    // Debug: log first few data rows
                    if ($index <= 8) {
                        \Log::info('Row ' . ($index + 1) . ' data: ' . json_encode($row));
                        \Log::info('Company name (index 0): ' . ($row[0] ?? 'NULL'));
                    }
                    if ($importType === 'company') {
                        $data = $this->prepareCompanyData($row, $states, $cities, $stateNames, $cityNames);
                        if ($data && !empty($data['company_name'])) {
                            // Check for duplicate company by name
                            $exists = $this->companymasterModel::where('company_name', $data['company_name'])->first();
                            if ($exists) {
                                \Log::info('Company skipped (duplicate): ' . $data['company_name']);
                                \Log::info('Company exists ID: ' . $exists->id);
                                $duplicates[] = ['row' => $index + 1, 'name' => $data['company_name'], 'type' => 'Company'];
                                continue;
                            } else {
                                \Log::info('Company is new: ' . $data['company_name']);
                            }
                            try {
                                $create = $this->companymasterModel::create($data);
                                if ($create) $imported++;
                            } catch (\Exception $e) {
                                \Log::error('Company insert error: ' . $e->getMessage());
                                $errors[] = ['row' => $index + 1, 'error' => $e->getMessage(), 'data' => $data];
                            }
                        } else {
                            continue;
                        }

                    } elseif ($importType === 'company_garden') {
                        // ── Company-Garden Link import ────────────────────────────
                        $data = $this->prepareCompanyGardenData($row);

                        if ($data && !empty($data['garden_id'])) {
                            try {
                                // If company_id exists, create link; otherwise just log garden
                                if (!empty($data['company_id'])) {
                                    // Check if link already exists
                                    $exists = $this->companyGardenModel::where('company_id', $data['company_id'])
                                        ->where('garden_id', $data['garden_id'])
                                        ->first();
                                    if (!$exists) {
                                        $create = $this->companyGardenModel::create([
                                            'company_id' => $data['company_id'],
                                            'garden_id'  => $data['garden_id'],
                                        ]);
                                        if ($create) {
                                            $imported++;
                                            \Log::info('Company-Garden link created: Company ' . $data['company_id'] . ' -> Garden ' . $data['garden_id']);
                                        }
                                    } else {
                                        \Log::info('Company-Garden link skipped (duplicate): Company ' . $data['company_id'] . ' -> Garden ' . $data['garden_id']);
                                        $duplicates[] = ['row' => $index + 1, 'name' => $data['company_name'] . ' -> ' . $data['garden_name'], 'type' => 'Company-Garden Link'];
                                    }
                                } else {
                                    // Garden exists but no company - just log it
                                    \Log::info('Garden found but no company link: ' . $data['garden_name']);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Company-Garden insert error: ' . $e->getMessage());
                                $errors[] = ['row' => $index + 1, 'error' => $e->getMessage(), 'data' => $data];
                            }
                        } else {
                            \Log::info('Company-Garden skipped (garden not found): ' . json_encode($data));
                        }

                    } elseif ($importType === 'garden') {
                        // ── Garden import ─────────────────────────────────────────
                        $data = $this->prepareGardenData($row);

                        if ($data && !empty($data['garden_name'])) {
                            // Check for duplicate garden by name
                            $exists = $this->gardenModel::where('garden_name', $data['garden_name'])->first();
                            if ($exists) {
                                \Log::info('Garden skipped (duplicate): ' . $data['garden_name']);
                                $duplicates[] = ['row' => $index + 1, 'name' => $data['garden_name'], 'type' => 'Garden'];
                                continue;
                            }
                            try {
                                $create = $this->gardenModel::create($data);
                                if ($create) {
                                    $imported++;
                                    \Log::info('Garden inserted: ' . $data['garden_name']);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Garden insert error: ' . $e->getMessage());
                                $errors[] = ['row' => $index + 1, 'error' => $e->getMessage(), 'data' => $data];
                            }
                        } else {
                            continue; // skip blank rows silently
                        }

                    } else {
                        // ── Party import ──────────────────────────────────────────
                        $data = $this->preparePartyData($row, $states, $cities, $stateNames, $cityNames);
                        if ($data && !empty($data['name'])) {
                            $create = $this->partyModel::create($data);
                            if ($create) $imported++;
                        } else {
                            continue;
                        }
                    }

                } catch (\Exception $e) {
                    \Log::error('Error on row ' . ($index + 1) . ': ' . $e->getMessage());
                    $errors[] = [
                        'row'   => $index + 1,
                        'error' => $e->getMessage(),
                        'data'  => $row,
                    ];
                }
            }

            \Storage::delete($filePath);

            $label = match($importType) {
                'garden' => 'Gardens',
                'company_garden' => 'Company-Garden Links',
                'company' => 'Company Masters',
                default => 'Parties',
            };
            \Log::info("Import done. Imported: {$imported} | Errors: " . count($errors));

            return redirect()->back()
                ->with('success', "Imported {$imported} of {$totalRows} {$label} successfully. Errors: " . count($errors) . ", Duplicates: " . count($duplicates))
                ->with('errors', $errors)
                ->with('duplicates', $duplicates);

        } catch (\Exception $e) {
            if (isset($filePath)) {
                \Storage::delete($filePath);
            }
            \Log::error('Import failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PARTY  –  Columns:
    //  0:CODE | 1:NAME | 2:BILL To | 3:GSTIN | 4:TMCO | 5:ADRESS1 |
    //  6:ADRESS2 | 7:CITY | 8:STATE | 9:PIN | 10:EMAIL | 11:C%
    // =========================================================================
    private function preparePartyData($row, $states, $cities, $stateNames, $cityNames)
    {
        $name = $this->getValue($row, 1); // NAME / BILL To

        if (empty($name)) {
            return null;
        }

        $stateName = $this->getValue($row, 8);
        $cityName  = $this->getValue($row, 7);

        [$stateId, $cityId] = $this->resolveStateCity(
            $stateName, $cityName, $states, $cities
        );

        $address1 = $this->getValue($row, 5);
        $address2 = $this->getValue($row, 6);
        $pincode  = $this->getValue($row, 9);

        return [
            'code'                => $this->getValue($row, 0),
            'name'                => $name,
            'bill_to'             => $this->getValue($row, 2),
            'email'               => $this->getValue($row, 10),
            'gst_no'              => $this->getValue($row, 3),
            'tmco'                => $this->getValue($row, 4),
            'c'                   => $this->getValue($row, 11),
            'address'             => trim($address1 . ' ' . $address2),
            'pincode'             => empty($pincode) ? null : $pincode,
            'party_type'          => 'buyer',
            'country_id'          => 101,
            'state_id'            => $stateId,
            'city_id'             => $cityId,
            'contact_person_name' => null,
            'mobile_1'            => null,
            'mobile_2'            => null,
            'pan'                 => null,
            'created_by'          => $this->userId,
            'updated_by'          => $this->userId,
            'is_active'           => 1,
            'is_deleted'          => 0,
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now(),
        ];
    }

    // =========================================================================
    // COMPANY  –  Actual Excel Columns from row data:
    //  0:CODE(null) | 1:NAME | 2:BILL To(null) | 3:GSTIN | 4:TMCO(null)
    //  5:ADRESS1 | 6:ADRESS2(null) | 7:CITY | 8:STATE | 9:PIN
    //  10:EMAIL-1 | 11:EMAIL-2 | 12:C%
    // =========================================================================
    private function prepareCompanyData($row, $states, $cities, $stateNames, $cityNames)
    {
        $companyName = $this->getValue($row, 1); // NAME (index 1)

        if (empty($companyName)) {
            return null;
        }

        $stateName = $this->getValue($row, 8);  // STATE (index 8)
        $cityName  = $this->getValue($row, 7);  // CITY (index 7)

        [$stateId, $cityId] = $this->resolveStateCity(
            $stateName, $cityName, $states, $cities
        );

        $pincode   = $this->getValue($row, 9);  // PIN (index 9)

        // C% → brokerage (index 12); treat "-" or blank as null
        $brokerage = $this->getValue($row, 12);
        if ($brokerage === '-' || $brokerage === '' || $brokerage === null) {
            $brokerage = 0.00;
        }

        // EMAIL-1 primary (index 10), fall back to EMAIL-2 (index 11)
        $email = $this->getValue($row, 10);
        if (empty($email) || $email === '-') {
            $email = $this->getValue($row, 11);
        }

        // Combine ADRESS1 + ADRESS2
        $address1 = $this->getValue($row, 5);
        $address2 = $this->getValue($row, 6);
        $address = trim(($address1 ?? '') . ' ' . ($address2 ?? ''));

        return [
            'company_name'        => $companyName,
            'email'               => $email,
            'gst_no'              => $this->getValue($row, 3),  // GSTIN (index 3)
            'tmco'                => $this->getValue($row, 4),  // TMCO (index 4)
            'address'             => $address,                   // ADRESS1 + ADRESS2
            'pincode'             => (empty($pincode) || $pincode === '-') ? null : $pincode,
            'brokerage'           => $brokerage,                 // C% (index 12)
            'country_id'          => 101,
            'state_id'            => $stateId,
            'city_id'             => $cityId,
            'contact_person_name' => null,
            'mobile_1'            => null,
            'mobile_2'            => null,
            'pan'                 => null,
            'created_by'          => $this->userId,
            'updated_by'          => $this->userId,
            'is_active'           => 1,
            'is_deleted'          => 0,
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now(),
        ];
    }

    // =========================================================================
    // GARDEN  –  Columns (GARDEN/FACTORY MARKS format):
    //  0:MARK NAME | 1:SELLER (not used for import)
    //  Only store unique garden names, skip duplicates
    // =========================================================================
    private function prepareGardenData($row)
    {
        $name = $this->getValue($row, 0); // MARK NAME only

        if (empty($name)) {
            return null;
        }

        // Skip if this is the header row itself
        if (strtoupper($name) === 'MARK NAME') {
            return null;
        }

        return [
            'garden_name'       => $name,
            'created_by' => $this->userId,
            'updated_by' => $this->userId,
            'country_id' => 101,
            'is_active'  => 1,
            'is_deleted' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
    // =========================================================================
    // COMPANY-GARDEN LINK  –  Excel Columns:
    //  0: MARK NAME (garden name)  |  1: SELLER (company name)
    //  Finds company_id from companymaster, garden_id from gardens
    //  Inserts into company_garden table
    // =========================================================================
    private function prepareCompanyGardenData($row)
    {
        $gardenName = $this->getValue($row, 0);   // MARK NAME
        $companyName = $this->getValue($row, 1);   // SELLER

        // Garden name is required
        if (empty($gardenName)) {
            return null;
        }

        // Skip if this is the header row itself
        if (strtoupper($gardenName) === 'MARK NAME') {
            return null;
        }

        // Find garden_id by garden_name
        $garden = $this->gardenModel::where('garden_name', $gardenName)->first();
        if (!$garden) {
            \Log::warning('Garden not found: ' . $gardenName);
            return null;
        }

        $result = [
            'garden_id'  => $garden->id,
            'garden_name' => $gardenName,
        ];

        // If company name exists, find company_id
        if (!empty($companyName)) {
            $company = $this->companymasterModel::where('company_name', $companyName)->first();
            if ($company) {
                $result['company_id'] = $company->id;
                $result['company_name'] = $companyName;
            } else {
                \Log::warning('Company not found: ' . $companyName);
            }
        }

        return $result;
    }

    // =========================================================================
    // Shared helper – resolve state + city IDs from pre-loaded cache
    // =========================================================================
    private function resolveStateCity($stateName, $cityName, $states, $cities)
    {
        $stateId = null;
        $cityId  = null;

        if (!empty($stateName)) {
            $stateId = $states[strtolower(trim($stateName))] ?? null;
        }

        if ($stateId && !empty($cityName)) {
            $cityKey = strtolower(trim($cityName)) . '_' . $stateId;
            $cityId  = $cities[$cityKey] ?? null;
        }

        return [$stateId, $cityId];
    }

    private function getValue($row, $index)
    {
        $val = isset($row[$index]) ? trim((string)$row[$index]) : null;
        return ($val === '' || $val === '-' || $val === null) ? null : $val;
    }
}
