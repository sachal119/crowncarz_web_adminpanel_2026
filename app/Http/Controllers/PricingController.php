<?php


namespace App\Http\Controllers;

use App\Models\FixedPrice;
use App\Models\MileagePrice;
use App\Models\PricingPercentage;
use App\Jobs\ProcessFixedPrices;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class PricingController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebase = $firebaseService->getDatabase();
        //  $this->firebase = $firebaseService;
        $this->database = $firebaseService->getDatabase();
    }

    private function normalizePostcode(?string $postcode): string
    {
        return preg_replace('/\s+/', ' ', strtoupper(trim((string) $postcode))) ?? '';
    }

    private function fixedPriceArray(FixedPrice $price): array
    {
        return [
            'id' => $price->id,
            'from_postcode' => $price->from_postcode,
            'to_postcode' => $price->to_postcode,
            'Saloon' => (float) $price->saloon,
            'Estate' => (float) $price->estate,
            'MPV' => (float) $price->mpv,
            '8 Seater' => (float) $price->seater_8,
            'Executive' => (float) $price->executive,
        ];
    }

    // 🔹 Fixed Prices
//     public function fixedIndex()
// {
//     $snapshot = $this->firebase->getReference('fixed_prices')
//         ->orderByKey()
//         ->limitToFirst(25)
//         ->getValue();

//     $fixedPrices = $snapshot ? array_values($snapshot) : [];

    

//     return view('pricing.fixed', compact('fixedPrices'));
// }

// 🔸 2. Add Surcharge
    public function addSurcharge(Request $request)
    {
        // $request->validate([
        //     'surcharge' => 'required|numeric',
        //     'from_date' => 'required|string',
        //     'to_date' => 'required|string',
        //     'from_time' => 'required|string',
        //     'to_time' => 'required|string',
        //     'pickup' => 'required|string',
        //     'dropoff' => 'required|string',
        // ]);
        
        

        try {
            $data = $request->validate([
        'surcharge' => 'required|numeric',
        'from_date' => 'required',
        'to_date'   => 'required',
        'from_time' => 'required',
        'to_time'   => 'required',
        'pickup'    => 'required|string',
        'dropoff'   => 'required|string',
    ]);

    // Store in Firebase
    $ref = $this->firebase->getReference('surcharges')->push($data);

           return response()->json([
    'success' => true,
    'message' => 'Mileage pricing (bracket-wise) saved successfully!'
]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 🔸 3. Edit Surcharge
    public function updateSurcharge(Request $request, $id)
    {
        try {
            // Get Firebase reference
        $ref = $this->firebase->getReference('surcharges/'.$id);
            
            $updateData = [
                'surcharge'  => $request->surcharge ?? 0,
                'from_date'  => $request->from_date,
                'to_date'    => $request->to_date,
                'from_time'  => $request->from_time,
                'to_time'    => $request->to_time,
                'pickup'     => strtoupper($request->pickup),
                'dropoff'    => strtoupper($request->dropoff),
                'updated_at' => now()->toDateTimeString(),
        ];

        
        
        // ✅ Update existing node (overwrite old percentages)
        $ref->update($updateData);

             return response()->json([
    'success' => true,
    'message' => 'Mileage pricing (bracket-wise) updated successfully!'
]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 🔸 4. Delete Surcharge
    public function deleteSurcharge($id)
    {
        try {
            // $this->firebase->deleteData("surcharges/{$id}");
            
             $ref = $this->database->getReference('surcharges/' . $id);
            // Delete the record
        $ref->remove();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

public function saveMileage(Request $request)
{
    $validated = $request->validate([
        'minimum_price' => ['required', 'numeric', 'min:0'],
        'car_type' => ['required', 'string', 'max:40'],
        'from_mileage' => ['required', 'array', 'min:1'],
        'from_mileage.*' => ['required', 'numeric', 'min:0'],
        'to_mileage' => ['required', 'array'],
        'to_mileage.*' => ['required', 'numeric', 'min:0'],
        'cost_per_mileage' => ['required', 'array'],
        'cost_per_mileage.*' => ['required', 'numeric', 'min:0'],
    ]);

    $now = now();
    $rows = [];
    foreach ($validated['from_mileage'] as $index => $from) {
        $to = (float) ($validated['to_mileage'][$index] ?? 0);
        if ($to < (float) $from) {
            return back()->withErrors(['to_mileage' => 'Each mileage bracket must end after it starts.'])->withInput();
        }

        $rows[] = [
            'minimum_price' => (float) $validated['minimum_price'],
            'car_type' => $validated['car_type'],
            'from_mileage' => (float) $from,
            'to_mileage' => $to,
            'cost_per_mileage' => (float) ($validated['cost_per_mileage'][$index] ?? 0),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    DB::table('mileage_pricing')->upsert(
        $rows,
        ['car_type', 'from_mileage', 'to_mileage'],
        ['minimum_price', 'cost_per_mileage', 'updated_at']
    );

    return redirect()->back()->with('success', 'Mileage pricing (bracket-wise) saved successfully!');
}


public function destroy($id)
{
    try {
        $record = MileagePrice::query()->find($id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        $record->delete();

        return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

// public function saveMileage(Request $request)
// {
//     $minimumPrice = $request->input('minimum_price');
//     $carType      = $request->input('car_type');
//     $mileages     = $request->input('mileage');
//     $costs        = $request->input('cost_per_mileage');

//     $createdAt = now()->toDateTimeString();

//     $savedRows = [];

//     foreach ($mileages as $index => $mileage) {
//         $row = [
//             "minimum_price"     => $minimumPrice,
//             "car_type"          => $carType,
//             "mileage"           => $mileage,
//             "cost_per_mileage"  => $costs[$index] ?? null,
//             "created_at"        => $createdAt,
//         ];

//         // ✅ Push each row directly under 'mileage_pricing'
//         $ref = $this->firebase->getReference('mileage_pricing')->push($row);
        
//         // Store saved row with Firebase key for return
//         $savedRows[$ref->getKey()] = $row;
//     }
    
     
    
//     return redirect()->back()->with('success', 'Mileage pricing saved to Firebase!');

//     // return response()->json([
//     //     "message" => "Mileage pricing saved to Firebase successfully!",
//     //     "data"    => $savedRows
//     // ]);
// }

//Perfected version at 2025-10-27
// public function fixedIndex(Request $request)
// {
//     $perPage = 25;
//     $page = max((int) $request->query('page', 1), 1); // default page 1
//     $startAfter = $request->query('startAfter');      // Firebase cursor

//     // Fetch page data
//     $query = $this->firebase->getReference('fixed_prices')
//         ->orderByKey()
//         ->limitToFirst($perPage + 1);

//     if ($startAfter) {
//         $query = $query->startAfter($startAfter);
//     }

//     $snapshot = $query->getValue();
//     $fixedPrices = $snapshot ? $snapshot : [];

//     // Detect if there's another page
//     $hasMore = false;
//     if (count($fixedPrices) > $perPage) {
//         $hasMore = true;
//         $lastKey = array_key_last($fixedPrices);
//         array_pop($fixedPrices);
//     } else {
//         $lastKey = null;
//     }

//     // ✅ Use counter node (set during import)
//     $totalCount = $this->firebase->getReference('fixed_prices_count')->getValue() ?? 0;

//     // ✅ Calculate ranges
//     $from = (($page - 1) * $perPage) + 1;
//     $to   = min($from + $perPage - 1, $totalCount);

//     return view('pricing.fixed', [
//         'fixedPrices' => $fixedPrices,
//         'hasMore'     => $hasMore,
//         'lastKey'     => $lastKey,
//         'startAfter'  => $startAfter,
//         'totalCount'  => $totalCount,
//         'page'        => $page,
//         'from'        => $from,
//         'to'          => $to,
//     ]);
// }

public function fixedIndex(Request $request)
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    
    $perPage = 25;
    $paginator = FixedPrice::query()->orderBy('id')->paginate($perPage)->withQueryString();
    $fixedPrices = $paginator->getCollection()
        ->mapWithKeys(fn (FixedPrice $price) => [$price->id => $this->fixedPriceArray($price)])
        ->all();

    $page = $paginator->currentPage();
    $hasMore = $paginator->hasMorePages();
    $totalCount = $paginator->total();
    $from = $paginator->firstItem() ?? 0;
    $to = $paginator->lastItem() ?? 0;
    
    $surcharges = $this->firebase->getReference('surcharges')->getValue() ?? [];
    
    $driversData = $this->firebase->getReference('drivers')->getValue() ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

    return view('pricing.fixed', [
        'fixedPrices' => $fixedPrices,
        'hasMore'     => $hasMore,
        'page'        => $page,
        'from'        => $from,
        'to'          => $to,
        'totalCount'  => $totalCount,
        'surcharges' => $surcharges,
        'drivers' => $drivers
    ]);
}

public function get_surcharge()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    $surcharges = $this->firebase->getReference('surcharges')->getValue() ?? [];
    
    $driversData = $this->firebase->getReference('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}
    
    return view('surcharge_screen', [
        'surcharges' => $surcharges,
        'drivers' => $drivers
        ]);
    
}





public function getMileageData()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    $data = MileagePrice::query()
        ->orderBy('car_type')
        ->orderBy('from_mileage')
        ->get()
        ->keyBy('id')
        ->map->toArray()
        ->all();

    if (!$data) {
        return response()->json([
            "message" => "No mileage pricing data found.",
            "data"    => []
        ], 200);
    }

    return response()->json([
        "message" => "Mileage pricing data fetched successfully!",
        "data"    => $data
    ], 200);
}

// public function mileageList()
// {
//     // Fetch from Firebase
//     $snapshot = $this->firebase->getReference('mileage_pricing')->getSnapshot();
//     $data = $snapshot->getValue() ?? [];

//     // Convert to collection
//     $collection = collect($data);

//     // Pagination
//     $currentPage = LengthAwarePaginator::resolveCurrentPage();
//     $perPage = 10;

//     $pagedData = new LengthAwarePaginator(
//         $collection->slice(($currentPage - 1) * $perPage, $perPage)->values(),
//         $collection->count(),
//         $perPage,
//         $currentPage,
//         [
//             'path' => request()->url(),
//             'query' => request()->query()
//         ]
//     );

//     return view('admin.mileage.index', [
//         'mileagePrices' => $pagedData
//     ]);
// }




// public function fixedIndex(Request $request)
// {
//     $perPage = 25;
//     $startAfter = $request->query('startAfter'); // Firebase key for next page

//     $query = $this->firebase->getReference('fixed_prices')
//         ->orderByKey()
//         ->limitToFirst($perPage + 1); // +1 to detect if there's another page

//     if ($startAfter) {
//         $query = $query->startAfter($startAfter);
//     }

//     $snapshot = $query->getValue();
//     $fixedPrices = $snapshot ? $snapshot : [];

//     // Detect if there’s a next page
//     $hasMore = false;
//     if (count($fixedPrices) > $perPage) {
//         $hasMore = true;
//         $lastKey = array_key_last($fixedPrices);
//         array_pop($fixedPrices); // remove the extra record
//     } else {
//         $lastKey = null;
//     }

//     $totalCount = $this->firebase->getReference('fixed_prices_count')->getValue() ?? 0;

//     return view('pricing.fixed', [
//         'fixedPrices' => $fixedPrices,
//         'hasMore'     => $hasMore,
//         'lastKey'     => $lastKey,
//         'startAfter'  => $startAfter,
//         'totalCount'  => $totalCount,
//     ]);
// }





// Perfected CSV import to Firebase but slower than direct upload
//     public function importFixedPrices(Request $request)
// {
//     $file = $request->file('file')->getRealPath();
//     $handle = fopen($file, 'r');

//     // Skip header
//     fgetcsv($handle);

//     while (($row = fgetcsv($handle, 1000, ',')) !== false) {
//         if (count($row) < 7) {
//             continue;
//         }

//         $data = [
//             'from_postcode'    => $row[0],
//             'to_postcode'      => $row[1],
//             'company_price'    => $row[2] ?? 0,
//             'driver_price'     => $row[3] ?? 0,
//             'agent_commission' => $row[4] ?? 0,
//             'type'             => $row[5] ?? 'default',
//             'vehicle_type'     => $row[6] ?? 'default',
//             'created_at'       => now()->toDateTimeString(),
//         ];

//         // ✅ Check for duplicate before insert
//         $existing = $this->firebase->getReference('fixed_prices')
//             ->orderByChild('unique_key')
//             ->equalTo($data['from_postcode'] . '_' . $data['to_postcode'] . '_' . $data['vehicle_type'])
//             ->getValue();

//         if (empty($existing)) {
//             // Add a unique_key for duplicate checking
//             $data['unique_key'] = $data['from_postcode'] . '_' . $data['to_postcode'] . '_' . $data['vehicle_type'];
//             $this->firebase->getReference('fixed_prices')->push($data);
//         }
//     }

//     fclose($handle);

//     return back()->with('success', 'Fixed prices imported to Firebase without duplicates.');
// }

// public function importFixedPrices(Request $request)
// {
//     $file = $request->file('file')->getRealPath();
//     $handle = fopen($file, 'r');

//     // 1. Delete all previous fixed prices
//     $this->firebase->getReference('fixed_prices')->remove();

//     // Skip header row
//     // fgetcsv($handle);

//     // $batchData = [];
//     // $seenKeys = [];
//     // $chunkSize = 5000;
//     // $imported = 0;

//     // while (($row = fgetcsv($handle, 1000, ',')) !== false) {
//     //     if (count($row) < 7) {
//     //         continue;
//     //     }

//     //     $uniqueKey = $row[0] . '_' . $row[1] . '_' . ($row[6] ?? 'default');

//     //     // Skip duplicates inside this import
//     //     if (isset($seenKeys[$uniqueKey])) {
//     //         continue;
//     //     }

//     //     $seenKeys[$uniqueKey] = true;

//     //     $batchData[$uniqueKey] = [
//     //         'from_postcode'    => $row[0],
//     //         'to_postcode'      => $row[1],
//     //         'company_price'    => $row[2] ?? 0,
//     //         'driver_price'     => $row[3] ?? 0,
//     //         'agent_commission' => $row[4] ?? 0,
//     //         'type'             => $row[5] ?? 'default',
//     //         'vehicle_type'     => $row[6] ?? 'default',
//     //         'created_at'       => now()->toDateTimeString(),
//     //         'unique_key'       => $uniqueKey,
//     //     ];

//     //     // 🚀 Upload every 5000 rows
//     //     if (count($batchData) >= $chunkSize) {
//     //         $this->firebase->getReference('fixed_prices')->update($batchData);
//     //         $imported += count($batchData);
//     //         $batchData = []; // reset batch
//     //     }
//     // }

//     // // Upload remaining rows
//     // if (!empty($batchData)) {
//     //     $this->firebase->getReference('fixed_prices')->update($batchData);
//     //     $imported += count($batchData);
//     // }

//     // fclose($handle);
    
//     // // 🔥 Call your count command after import
//     // Artisan::call('count:fixed-prices');

//     return back()->with('success', "Imported {$imported} fixed prices to Firebase without duplicates.");
// }




// XLSX/CSV fixed-price import to the existing MySQL fixed_pricing table.
public function importFixedPrices(Request $request)
{
    $validated = $request->validate([
        'file' => ['required', 'file', 'max:51200', 'mimes:xlsx,xls,csv,txt'],
        'import_id' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_-]{8,64}$/'],
    ]);

    $importId = $validated['import_id'] ?? bin2hex(random_bytes(16));
    try {
        $extension = strtolower($validated['file']->getClientOriginalExtension() ?: 'xlsx');
        $storedPath = $validated['file']->storeAs(
            'fixed-price-imports',
            $importId.'.'.$extension,
            'local'
        );

        if (!$storedPath) {
            throw new \RuntimeException('The spreadsheet could not be stored for background processing.');
        }

        $this->setFixedPriceImportProgress($importId, 5, 'Upload complete. Waiting for the background worker...');
        ProcessFixedPrices::dispatch($storedPath, $importId);

        $message = 'Upload complete. Pricing import has been queued.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'queued' => true,
                'import_id' => $importId,
                'message' => $message,
            ], 202);
        }

        return back()->with('success', $message);
    } catch (Throwable $exception) {
        report($exception);
        $message = 'Import failed: '.$exception->getMessage();
        $this->setFixedPriceImportProgress($importId, 0, $message, null, null, 'failed');

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return back()->with('error', $message);
    }
}

public function fixedPriceImportProgress(string $importId)
{
    abort_unless(session('admin_logged_in'), 403);
    abort_unless((bool) preg_match('/^[A-Za-z0-9_-]{8,64}$/', $importId), 404);

    return response()->json(Cache::get('fixed-price-import:'.$importId, [
        'status' => 'waiting',
        'percent' => 0,
        'message' => 'Waiting for the upload to start...',
        'processed' => 0,
        'total' => null,
    ]));
}

private function setFixedPriceImportProgress(
    string $importId,
    int $percent,
    string $message,
    ?int $processed = null,
    ?int $total = null,
    string $status = 'processing'
): void {
    Cache::put('fixed-price-import:'.$importId, [
        'status' => $status,
        'percent' => max(0, min(100, $percent)),
        'message' => $message,
        'processed' => $processed,
        'total' => $total,
    ], now()->addHour());
}

// public function importFixedPrices(Request $request)
// {
//     $file = $request->file('file')->getRealPath();
//     $handle = fopen($file, 'r');

//     // 1. Delete all previous fixed prices
//     // Firebase
//     // $this->firebase->getReference('fixed_prices')->set(null);

//     // SQL
//     \DB::table('fixed_pricing')->truncate();

//     // Skip header row
//     fgetcsv($handle);

//     $batchDataFirebase = [];
//     $batchDataSQL = [];
//     $seenKeys = [];
//     $chunkSize = 5000;
//     $imported = 0;

//     while (($row = fgetcsv($handle, 1000, ',')) !== false) {
//         if (count($row) < 7) {
//             continue;
//         }

//         $uniqueKey = $row[0] . '_' . $row[1] . '_' . ($row[6] ?? 'default');

//         if (isset($seenKeys[$uniqueKey])) {
//             continue;
//         }
//         $seenKeys[$uniqueKey] = true;

//         $record = [
//             'from_postcode'    => $row[0],
//             'to_postcode'      => $row[1],
//             'company_price'    => $row[2] ?? 0,
//             'driver_price'     => $row[3] ?? 0,
//             'agent_commission' => $row[4] ?? 0,
//             'type'             => $row[5] ?? 'default',
//             'vehicle_type'     => $row[6] ?? 'default',
//             'created_at'       => now()->toDateTimeString(),
//             'unique_key'       => $uniqueKey,
//         ];

//         // Prepare data for Firebase
//         $batchDataFirebase[$uniqueKey] = $record;

//         // Prepare data for SQL (no unique_key as primary key if auto id exists)
//         $batchDataSQL[] = $record;

//         if (count($batchDataFirebase) >= $chunkSize) {
//             // Upload to Firebase
//             $this->firebase->getReference('fixed_prices')->update($batchDataFirebase);

//             // Insert into SQL
//             \DB::table('fixed_pricing')->insert($batchDataSQL);

//             $imported += count($batchDataFirebase);

//             // reset batch
//             $batchDataFirebase = [];
//             $batchDataSQL = [];
//         }
//     }

//     // Upload remaining rows
//     if (!empty($batchDataFirebase)) {
//         $this->firebase->getReference('fixed_prices')->update($batchDataFirebase);
//         \DB::table('fixed_prices')->insert($batchDataSQL);
//         $imported += count($batchDataFirebase);
//     }

//     fclose($handle);

//     Artisan::call('count:fixed-prices');

//     return back()->with('success', "Imported {$imported} fixed prices to Firebase & SQL without duplicates.");
// }





//     public function importFixedPrices(Request $request)
// {
//     $file = $request->file('file')->getRealPath();
//     $handle = fopen($file, 'r');

//     // Skip header
//     fgetcsv($handle);

//     while (($row = fgetcsv($handle, 1000, ',')) !== false) {
//         if (count($row) < 7) {
//             continue;
//         }

//         $data = [
//             'from_postcode'    => $row[0],
//             'to_postcode'      => $row[1],
//             'company_price'    => $row[2] ?? 0,
//             'driver_price'     => $row[3] ?? 0,
//             'agent_commission' => $row[4] ?? 0,
//             'type'             => $row[5] ?? 'default',
//             'vehicle_type'     => $row[6] ?? 'default',
//             'created_at'       => now()->toDateTimeString(),
//         ];

//         $this->firebase->getReference('fixed_prices')->push($data);
//     }

//     fclose($handle);

//     return back()->with('success', 'Fixed prices imported to Firebase.');
// }



public function exportFixedPrices()
{
    $response = new StreamedResponse(function () {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->fromArray([[
            'From Postcode',
            'To Postcode',
            'Saloon Price (£)',
            'Estate Price (£)',
            'MPV Price (£)',
            '8 Seater Price (£)',
            'Executive Price (£)'
        ]], NULL, 'A1');

        $rowNumber = 2;

        FixedPrice::query()->orderBy('id')->chunkById(5000, function ($prices) use ($sheet, &$rowNumber) {
            foreach ($prices as $price) {
                $sheet->setCellValue("A{$rowNumber}", $price->from_postcode);
                $sheet->setCellValue("B{$rowNumber}", $price->to_postcode);
                $sheet->setCellValue("C{$rowNumber}", $price->saloon);
                $sheet->setCellValue("D{$rowNumber}", $price->estate);
                $sheet->setCellValue("E{$rowNumber}", $price->mpv);
                $sheet->setCellValue("F{$rowNumber}", $price->seater_8);
                $sheet->setCellValue("G{$rowNumber}", $price->executive);
                $rowNumber++;
            }
        });

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    });

    $filename = 'fixed_prices_' . date('Y-m-d_H-i-s') . '.xlsx';

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', "attachment;filename=\"{$filename}\"");
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
}




    
//     public function mileageIndex(Request $request)
// {
//     $perPage = 25;
//     $page = max((int) $request->query('page', 1), 1); // default page 1
//     $startAfter = $request->query('startAfter');      // Firebase cursor

//     // Fetch page data
//     $query = $this->firebase->getReference('mileage_pricing')
//         ->orderByKey()
//         ->limitToFirst($perPage + 1);

//     if ($startAfter) {
//         $query = $query->startAfter($startAfter);
//     }

//     $snapshot = $query->getValue();
//     $fixedPrices = $snapshot ? $snapshot : [];

//     // Detect if there's another page
//     $hasMore = false;
//     if (count($fixedPrices) > $perPage) {
//         $hasMore = true;
//         $lastKey = array_key_last($fixedPrices);
//         array_pop($fixedPrices);
//     } else {
//         $lastKey = null;
//     }

//     // ✅ Use counter node (set during import)
//     // $totalCount = $this->firebase->getReference('fixed_prices_count')->getValue() ?? 0;

//     // ✅ Calculate ranges
//     $from = (($page - 1) * $perPage) + 1;
//     $to   = min($from + $perPage - 1, $totalCount);

//     return view('pricing.mileage', [
//         'mileagePrices' => $fixedPrices,
//         'hasMore'     => $hasMore,
//         'lastKey'     => $lastKey,
//         'startAfter'  => $startAfter,
//         'totalCount'  => $totalCount,
//         'page'        => $page,
//         'from'        => $from,
//         'to'          => $to,
//     ]);
//     // return view('pricing.mileage', [
//     //     'mileagePrices' => $paginatedMileagePrices
//     // ]);
// }


public function mileageIndex(Request $request)
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    $mileagePaginator = MileagePrice::query()
        ->orderBy('car_type')
        ->orderBy('from_mileage')
        ->paginate(25)
        ->withQueryString();

    $mileagePrices = $mileagePaginator->getCollection()
        ->mapWithKeys(fn (MileagePrice $price) => [$price->id => $price->toArray()])
        ->all();

    $driversData = $this->firebase->getReference('drivers')->getValue() ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

    return view('pricing.mileage', [
        'mileagePrices' => $mileagePrices, // The data for the current page
        'mileagePaginator' => $mileagePaginator,
        'hasMore' => $mileagePaginator->hasMorePages(),
        'lastKey' => null,
        'startAfter' => null,
        'totalCount' => $mileagePaginator->total(),
        'page' => $mileagePaginator->currentPage(),
        'from' => $mileagePaginator->firstItem() ?? 0,
        'to' => $mileagePaginator->lastItem() ?? 0,
        'drivers' => $drivers
    ]);
}

public function updateMileagePrice(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|integer|exists:mileage_pricing,id',
        'cost_per_mileage' => 'required|numeric|min:0.01',
        'minimum_price' => 'required|numeric|min:0',
        // FIX: Change 'integer' to 'numeric'
        'from_mileage' => 'required|numeric|min:0', 
        'to_mileage' => 'required|numeric|min:0',
    ]);

    MileagePrice::query()->whereKey($validated['id'])->update([
        'cost_per_mileage' => $validated['cost_per_mileage'],
        'from_mileage' => $validated['from_mileage'],
        'to_mileage' => $validated['to_mileage'],
        'minimum_price' => $validated['minimum_price'],
    ]);

    return redirect()->back()->with('success', 'Mileage price updated successfully!');
}

// public function updateMileagePrice(Request $request)
// {
//     try {
//         $validated = $request->validate([
//             'id' => 'required|string',
//             'from_mileage' => 'required|numeric|min:0',
//             'to_mileage' => 'required|numeric|min:0',
//             'minimum_price' => 'required|numeric|min:0',
//             'cost_per_mileage' => 'required|numeric|min:0',
//         ]);

//         $referencePath = 'mileage_pricing/' . $validated['id'];

//         // Check if record exists before updating
//         $existing = $this->firebase->getReference($referencePath)->getValue();
//         if (!$existing) {
//             return response()->json(['success' => false, 'message' => 'Record not found'], 404);
//         }

//         $this->firebase->getReference($referencePath)->update([
//             'from_mileage' => $validated['from_mileage'],
//             'to_mileage' => $validated['to_mileage'],
//             'minimum_price' => $validated['minimum_price'],
//             'cost_per_mileage' => $validated['cost_per_mileage'],
//         ]);

//         return response()->json(['success' => true]);
//     } catch (\Throwable $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }




public function savePercentages(Request $request)
{
    try {
        $percentages = $request->input('percentages', []);

        if (empty($percentages)) {
            return response()->json(['success' => false, 'message' => 'No percentages provided']);
        }

        $values = [
            'estate' => (float) ($percentages['estate'] ?? 0),
            'mpv' => (float) ($percentages['mpv'] ?? 0),
            'seater8' => (float) ($percentages['seater8'] ?? 0),
            'executive' => (float) ($percentages['executive'] ?? 0),
        ];

        // The live pricing_percentages table predates the Eloquent model and
        // may not contain Laravel timestamp columns. Write only the columns
        // that are part of the actual pricing schema.
        DB::table('pricing_percentages')->updateOrInsert(['id' => 1], $values);
        $updateData = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'percentages' => $values,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Percentages updated successfully',
            'data' => $updateData
        ]);

    } catch (Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Could not save percentages: '.$e->getMessage(),
        ], 500);
    }
}
public function getPercentages()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    try {
        $settings = PricingPercentage::query()->find(1);
        $percentages = $settings ? [
            'estate' => (float) $settings->estate,
            'mpv' => (float) $settings->mpv,
            'seater8' => (float) $settings->seater8,
            'executive' => (float) $settings->executive,
        ] : ['estate' => 0, 'mpv' => 0, 'seater8' => 0, 'executive' => 0];

        $data = [
            'timestamp' => $settings?->updated_at?->format('Y-m-d H:i:s'),
            'percentages' => $percentages,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}


// public function search(Request $request)
// {
//     try {
//         $query = strtoupper(trim($request->query('q', '')));
//         // $query = 'OX10';

//         if ($query === '') {
//             return response()->json(['data' => []]);
//         }

//         if (!isset($this->database)) {
//             throw new \Exception('Firebase database not initialized.');
//         }

//         $ref = $this->database->getReference('fixed_prices');

//         $fromResults = $ref->orderByChild('from_postcode')
//             ->startAt($query)
//             ->endAt($query . "\uf8ff")
//             ->getValue() ?? [];

//         $toResults = $ref->orderByChild('to_postcode')
//             ->startAt($query)
//             ->endAt($query . "\uf8ff")
//             ->getValue() ?? [];

//         $merged = collect($fromResults)
//             ->merge($toResults)
//             ->map(fn($item, $key) => array_merge(['id' => $key], (array)$item))
//             ->unique('id')
//             ->values()
//             ->all();

//         return response()->json(['data' => $merged]);
//     } catch (\Throwable $e) {
//         return response()->json([
//             'error' => true,
//             'message' => $e->getMessage(),
//             'trace' => $e->getFile() . ':' . $e->getLine(),
//         ], 500);
//     }
// }

public function search(Request $request)
{
    try {
        $query = $this->normalizePostcode($request->get('q', ''));
        $page = max((int) $request->get('page', 1), 1);
        $limit = 25;

        $builder = FixedPrice::query()->orderBy('id');
        if ($query !== '') {
            $builder->where(function ($builder) use ($query) {
                $builder->where('from_postcode', 'like', $query.'%')
                    ->orWhere('to_postcode', 'like', $query.'%');
            });
        }

        $paginator = $builder->paginate($limit, ['*'], 'page', $page);
        $data = $paginator->getCollection()
            ->map(fn (FixedPrice $price) => $this->fixedPriceArray($price))
            ->values();

        return response()->json([
            'data' => $data,
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $limit,
            'has_more' => $paginator->hasMorePages(),
        ]);

    } catch (\Throwable $e) {
        \Log::error('Fixed Price Search Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
        ], 500);
    }
}


 public function update(Request $request, $id)
    {
        // return response()->json([
        //     'success' => true,
        //     'id' => $id]);
        // die;
        try {
            $validated = $request->validate([
                'saloon_price' => ['required', 'numeric', 'min:0'],
            ]);

            FixedPrice::query()->whereKey($id)->update([
                'company_price' => $validated['saloon_price'],
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


// public function savePercentagesToFirebase(Request $request)
// {
//     try {
//         // Validate input
//         $validated = $request->validate([
//             'percentages' => 'required|array',
//         ]);

//         $data = [
//             'percentages' => $validated['percentages'],
//             'timestamp' => now()->format('Y-m-d H:i:s'),
//         ];

//         // Save in Firebase new table (e.g., "percentage_records")
//         $firebasePath = 'percentage_records'; // your new table name
//         $this->firebase->pushData($firebasePath, $data);

//         return response()->json([
//             'success' => true,
//             'message' => 'Percentages saved successfully!',
//             'data' => $data
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }



// 🔹 Mileage Prices
    // public function mileageIndex()
    // {
    //     $snapshot = $this->firebase->getReference('fixed_prices')->getValue();
    //     $mileagePrices = $snapshot ? array_values($snapshot) : [];

    //     return view('pricing.mileage', compact('mileagePrices'));
    // }

//     public function saveMileage(Request $request)
// {
//     $fromPostcodes    = $request->from_postcode;
//     $toPostcodes      = $request->to_postcode;
//     $saloonFares   = $request->saloon_fare;
//     // $estateFares   = $request->estate_fare;
//     // $seater6Fares  = $request->seater6_fare;
//     // $seater7_8Fares = $request->seater7_8_fare;
//     // $seater12_16Fares = $request->seater10_12_fare;
//     // $companyPrices    = $request->company_price;
//     // $driverPrices     = $request->driver_price;
//     // $agentCommissions = $request->agent_commission;
//     // $types            = $request->type;
//     // $vehicleTypes     = $request->vehicle_type;

//     for ($i = 0; $i < count($fromPostcodes); $i++) {
//         $data = [
//             'from_postcode'    => $fromPostcodes[$i],
//             'to_postcode'      => $toPostcodes[$i],
//             'saloon_fare'      => (float) ($saloonFares[$i] ?? 0),
//             'estate_fare'      => 0,
//             'seater6_fare'     => 0,
//             'seater7_8_fare'   => 0,
//             'seater12_16_fare' => 0,
//             // 'estate_fare'      => (float) ($estateFares[$i] ?? 0),
//             // 'seater6_fare'     => (float) ($seater6Fares[$i] ?? 0),
//             // 'seater7_8_fare'   => (float) ($seater7_8Fares[$i] ?? 0),
//             // 'seater10_12_fare' => (float) ($seater12_16Fares[$i] ?? 0),
//             'created_at'       => now()->toDateTimeString(),
//         ];

//         $this->firebase->getReference('fixed_prices')->push($data);
//     }

//     return redirect()->back()->with('success', 'Mileage pricing saved to Firebase!');
// }

}



// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\FixedPrice;
// use App\Models\MileagePrice;
// use Maatwebsite\Excel\Facades\Excel;
// use App\Imports\FixedPriceImport;
// use App\Exports\FixedPriceExport;
// use App\Services\FirebaseService;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use Symfony\Component\HttpFoundation\StreamedResponse;

// class PricingController extends Controller
// {

//     protected $firebase;

//     public function __construct(FirebaseService $firebaseService)
//     {
//         $this->firebase = $firebaseService->getDatabase();
//     }

//     public function fixedIndex() {
//         $fixedPrices = FixedPrice::all();
//         return view('pricing.fixed', compact('fixedPrices'));
//     }

//     public function importFixedPrices(Request $request) {

//         $file = $request->file('file');
//     $csvData = array_map('str_getcsv', file($file));

//     // Skip header row
//     foreach (array_slice($csvData, 1) as $row) {
//     // Skip invalid/empty rows
//     if (count($row) < 7) {
//         continue;
//     }

    

//     FixedPrice::updateOrCreate([
//         'from_postcode' => $row[0],
//         'to_postcode'   => $row[1],
//     ], [
//         'company_price'    => $row[2] ?? 0,
//         'driver_price'     => $row[3] ?? 0,
//         'agent_commission' => $row[4] ?? 0,
//         'type'             => $row[5] ?? 'default',
//         'vehicle_type'     => $row[6] ?? 'default',
//     ]);
// }


//     // return back()->with('success', 'CSV imported successfully!');
//     //     Excel::import(new FixedPriceImport, $request->file('file'));
//         return back()->with('success', 'Fixed prices imported.');
//     }

//     public function exportFixedPrices() {
//         // Fetch all rows
//     $fixedPrices = FixedPrice::all();

//     // Create spreadsheet
//     $spreadsheet = new Spreadsheet();
//     $sheet = $spreadsheet->getActiveSheet();

//     // Add header row
//     $sheet->fromArray([
//         ['From Postcode', 'To Postcode', 'Company Price', 'Driver Price', 'Agent Commission', 'Type', 'Vehicle Type']
//     ]);

//     // Add data rows
//     $rowNumber = 2;
//     foreach ($fixedPrices as $price) {
//         $sheet->setCellValue("A{$rowNumber}", $price->from_postcode);
//         $sheet->setCellValue("B{$rowNumber}", $price->to_postcode);
//         $sheet->setCellValue("C{$rowNumber}", $price->company_price);
//         $sheet->setCellValue("D{$rowNumber}", $price->driver_price);
//         $sheet->setCellValue("E{$rowNumber}", $price->agent_commission);
//         $sheet->setCellValue("F{$rowNumber}", $price->type);
//         $sheet->setCellValue("G{$rowNumber}", $price->vehicle_type);
//         $rowNumber++;
//     }

//     // Write file to response
//     $writer = new Xlsx($spreadsheet);
//     $response = new StreamedResponse(function() use ($writer) {
//         $writer->save('php://output');
//     });

//     $filename = 'fixed_prices_' . date('Y-m-d_H-i-s') . '.xlsx';

//     $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//     $response->headers->set('Content-Disposition', "attachment;filename=\"{$filename}\"");
//     $response->headers->set('Cache-Control', 'max-age=0');

//     return $response;
//     }

//     public function mileageIndex() {
//         $mileagePrices = MileagePrice::all();
//         return view('pricing.mileage', compact('mileagePrices'));
//     }

//     public function saveMileage(Request $request)
//     {
//         $fromMiles = $request->from_miles;
//         $toMiles   = $request->to_miles;
//         $prices    = $request->price;





//         for ($i = 0; $i < count($fromMiles); $i++) {
//         MileagePrice::create([
//             'from_miles' => $fromMiles[$i],
//             'to_miles' => $toMiles[$i],
//             'price' => $prices[$i],
//         ]);
//     }

//         for ($i = 0; $i < count($fromMiles); $i++) {
//         $data = [
//             'from_miles' => (float) $fromMiles[$i],
//             'to_miles'   => (float) $toMiles[$i],
//             'price'      => (float) $prices[$i],
//             'created_at' => now()->toDateTimeString(),
//         ];

//         $this->firebase->getReference('mileage_pricing')->push($data);
//     }

//         return redirect()->back()->with('success', 'Mileage pricing saved to Firebase!');
//     }

//     // public function saveMileage(Request $request) {
//     //     MileagePrice::create($request->only(['from_miles', 'to_miles', 'price']));
//     //     return back()->with('success', 'Mileage price added.');
//     // }
// }
