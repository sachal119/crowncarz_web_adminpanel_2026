<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Passenger;
use App\Services\FirebaseService;
use Barryvdh\DomPDF\Facade\Pdf; // install barryvdh/laravel-dompdf
use App\Mail\DriverCommissionMail;
use Illuminate\Support\Facades\Mail;
// use App\Mail\DriverCommissionMail;
// use Barryvdh\DomPDF\Facade\Pdf;

// use PDF;
// use Mail;
// use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerReportMail;


class ReportController extends Controller
{

    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    /**
     * Reports module index page
     */
    // public function index()
    // {
    //     return view('reports.index');
    // }
    
public function index()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    // ✅ Fetch drivers
    $drivers = $this->firebase->getData("drivers");
    $driversList = [];

    if ($drivers) {
        foreach ($drivers as $key => $driver) {
            if (!empty($driver['id']) && !empty($driver['name'])) {
                $driversList[] = [
                    'id' => $driver['id'],
                    'name' => $driver['name']
                ];
            }
        }
    }

    // ✅ Fetch customers
    $customers = $this->firebase->getData("customers");
    $customersList = [];
    if ($customers) {
        foreach ($customers as $key => $customer) {
            if (!empty($customer['id']) && !empty($customer['business_name'])) {
                $customersList[] = [
                    'id' => $customer['id'],
                    'name' => $customer['business_name']
                ];
            }
        }
    }
    
          $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}


    // ✅ Pass both lists to the view
    return view('reports.index', compact('driversList', 'customersList','drivers'));
}



    /**
     * Driver Commission Report
     */
    // public function driverCommission(Request $request)
    // {
    //     $from = $request->from_date;
    //     $to   = $request->to_date;
        

    //     $commissionsData = $this->getDriverCommissionData($from, $to);

    //     return view('reports.driver_commission', [
    //         'commissions' => $commissionsData,
    //         'from' => $from,
    //         'to'   => $to,
    //     ]);
    // }

    // public function downloadDriverCommission(Request $request)
    // {
    //     $from = $request->from;
    //     $to   = $request->to;

    //     $commissionsData = $this->getDriverCommissionData($from, $to);

    //     $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.driver_commission_pdf', [
    //         'commissions' => $commissionsData,
    //         'from' => $from,
    //         'to'   => $to,
    //     ]);

    //     return $pdf->download("driver_commission_{$from}_to_{$to}.pdf");
    // }
    
    public function driverCommission(Request $request)
{
    $driverId = $request->driver_id; // ✅ Get driver ID
    $from = $request->from_date;
    $to   = $request->to_date;

    // Get all drivers from Firebase
$firebaseDrivers = $this->firebase->getData('drivers') ?? [];



$firebaseDriverKey = null;

foreach ($firebaseDrivers as $key => $driver) {
    if ((int) ($driver['id'] ?? 0) === (int) $driverId) {
        $firebaseDriverKey = $key; // ✅ -OkYXg9gLrYYL59Ce37J
        break;
    }
}


if (!$firebaseDriverKey) {
    return response()->json([
        'status' => false,
        'message' => 'Driver not found in Firebase'
    ], 404);
}

// ✅ Pass Firebase key
$data = $this->getDriverCommissionData($firebaseDriverKey, $from, $to);

// print_r($data);
// die;

    
          $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

    // ✅ Pass everything properly to the Blade view
    return view('reports.driver_commission', array_merge($data, [
        'driverId' => $driverId,
        'from' => $from,
        'to' => $to,
        'drivers' => $drivers
    ]));
}
    
// public function driverCommission(Request $request)
// {
//     $driverId = $request->driver_id; // ✅ Get driver ID
//     $from = $request->from_date;
//     $to   = $request->to_date;

//     // Get all report data
//     $data = $this->getDriverCommissionData($driverId, $from, $to);
    
//           $driversData = $this->firebase->getData('drivers') ?? [];
// $drivers = collect();

// foreach ($driversData as $id => $driver) {
//     $driver['id'] = $id;
//     $drivers->push($driver);
// }

//     // ✅ Pass everything properly to the Blade view
//     return view('reports.driver_commission', array_merge($data, [
//         'driverId' => $driverId,
//         'from' => $from,
//         'to' => $to,
//         'drivers' => $drivers
//     ]));
// }




public function downloadDriverCommission(Request $request)
{
    $driverId = $request->driver_id;
    $from = $request->from;
    $to   = $request->to;
    
    $firebaseDriverKey = null;

foreach ($firebaseDrivers as $key => $driver) {
    if ((int) ($driver['id'] ?? 0) === (int) $driverId) {
        $firebaseDriverKey = $key; // ✅ -OkYXg9gLrYYL59Ce37J
        break;
    }
}


if (!$firebaseDriverKey) {
    return response()->json([
        'status' => false,
        'message' => 'Driver not found in Firebase'
    ], 404);
}

    $data = $this->getDriverCommissionData($firebaseDriverKey, $from, $to);

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.driver_commission_pdf', $data)
        ->setPaper('a4', 'portrait');

    return $pdf->download("driver_statement_{$driverId}_{$from}_to_{$to}.pdf");
}


    /**
     * 🔥 Shared commission calculation logic
     */
    // private function getDriverCommissionData($from, $to)
    // {
    //     $firebaseBookings = $this->firebase->getData('bookings'); // your Firebase node
    //     $commissions = [];

    //     if ($firebaseBookings) {
    //         foreach ($firebaseBookings as $booking) {
    //             if (!isset($booking['driver_id'], $booking['created_at'], $booking['price'])) {
    //                 continue;
    //             }

    //             $bookingDate = date('Y-m-d', strtotime($booking['created_at']));

    //             if ($bookingDate >= $from && $bookingDate <= $to) {
    //                 $driverId = $booking['driver_id'];
    //                 $price    = (float) $booking['price'];
    //                 $commission = $price * 0.2; // 20% commission

    //                 if (!isset($commissions[$driverId])) {
    //                     $commissions[$driverId] = [
    //                         'total_commission' => 0,
    //                         'rides' => 0,
    //                     ];
    //                 }

    //                 $commissions[$driverId]['total_commission'] += $commission;
    //                 $commissions[$driverId]['rides'] += 1;
    //             }
    //         }
    //     }

    //     // Transform into objects for Blade
    //     $commissionsData = [];
    //     foreach ($commissions as $driverId => $data) {
    //         $commissionsData[] = (object)[
    //             'driver_id' => $driverId,
    //             'commission_total' => $data['total_commission'],
    //             'rides' => $data['rides'],
    //             'driver' => $this->firebase->getData("drivers/$driverId"),
    //         ];
    //     }
        
        

    //     return $commissionsData;
    // }
    
//     private function getDriverCommissionData($driverId, $from, $to)
// {
//     $firebaseBookings = $this->firebase->getData('bookings') ?? [];
//     $driverData = $this->firebase->getData("drivers/$driverId") ?? [];

//     $accountBookings = [];
//     $cashBookings = [];

//     $totals = [
//         'account_fare' => 0.0,
//         'cash_fare'    => 0.0,
//         'parking'      => 0.0, // account only
//     ];

//     foreach ($firebaseBookings as $booking) {

//         if (
//             empty($booking['driver_id']) ||
//             $booking['driver_id'] !== $driverId ||
//             empty($booking['created_at'])
//         ) {
//             continue;
//         }

//         $bookingDate = date('Y-m-d', strtotime($booking['created_at']));
//         if ($bookingDate < $from || $bookingDate > $to) {
//             continue;
//         }

//         // 🔹 IMPORTANT: use FARE, not price
//         $fare     = (float) ($booking['fare'] ?? 0);
//         $parking  = (float) ($booking['parking'] ?? 0);
//         $type     = strtolower($booking['payment_type'] ?? 'account');

//         $item = [
//             'date'       => $bookingDate,
//             'time'       => date('H:i', strtotime($booking['created_at'])),
//             'booking_id' => $booking['ref_no'] ?? '-',
//             'from'       => $booking['pickup_address'] ?? '-',
//             'to'         => $booking['dropoff_address'] ?? '-',
//             'fare'       => $fare,
//             'parking'    => $parking,
//             'vehicle'    => $booking['vehicle_make'] ?? '-',
//         ];

//         if ($type === 'cash') {
//             // 💵 CASH JOB
//             $cashBookings[] = $item;
//             $totals['cash_fare'] += $fare;

//         } else {
//             // 🏦 ACCOUNT JOB
//             $accountBookings[] = $item;
//             $totals['account_fare'] += $fare;
//             $totals['parking'] += $parking; // ✅ parking only here
//         }
//     }

//     // 🔹 COMMISSION (20% of TOTAL FARE)
//     $totalFare = $totals['account_fare'] + $totals['cash_fare'];
//     $commissionRate = 0.20;
//     $driverCommission = round($totalFare * $commissionRate, 2);

//     // 🔹 BROUGHT FORWARD
//     $broughtForward = (float) ($driverData['brought_forward'] ?? 100);

//     // 🔹 DRIVER EARNING (PDF FORMULA)
//     $driverEarning =
//         $totals['account_fare']
//         + $totals['parking']
//         - $driverCommission
//         - $broughtForward;

//     return [
//         'driver'            => $driverData,
//         'from'              => $from,
//         'to'                => $to,
//         'account_bookings'  => $accountBookings,
//         'cash_bookings'     => $cashBookings,
//         'totals'            => $totals,
//         'commission'        => $driverCommission,
//         'brought_forward'   => $broughtForward,
//         'driver_earning'    => $driverEarning,
//         'total_fare'        => $totalFare,
//     ];
// }

private function getDriverCommissionData($driverId, $from, $to)
{
    $firebaseBookings = $this->firebase->getData('bookings') ?? [];
    $driverData = $this->firebase->getData("drivers/$driverId") ?? [];

    $accountBookings = [];
    $cashBookings = [];

    $totals = [
        'account_fare' => 0.0,
        'cash_fare'    => 0.0,
        'parking'      => 0.0, // account only
    ];

    foreach ($firebaseBookings as $booking) {

        if (
            empty($booking['driver_id']) ||
            $booking['driver_id'] !== $driverId ||
            empty($booking['created_at'])
        ) {
            continue;
        }

        $bookingDate = date('Y-m-d', strtotime($booking['pickup_time']));
        if ($bookingDate < $from || $bookingDate > $to) {
            continue;
        }

        // 🔹 IMPORTANT: use FARE, not price
        $fare     = (float) ($booking['fare'] ?? $booking['price'] ?? 0);
        $parking  = (float) ($booking['parking'] ?? 0);
        $type     = strtolower($booking['payment_type'] ?? 'account');

        $item = [
            'date'       => $bookingDate,
            'time'       => date('H:i', strtotime($booking['pickup_time'])),
            'booking_id' => $booking['ref_no'] ?? '-',
            'from'       => $booking['pickup_address'] ?? '-',
            'to'         => $booking['dropoff_address'] ?? '-',
            'via' => $booking['via'] ?? '-',
            'fare'       => $fare,
            'waiting_fee' => $booking['waiting_fee'] ?? '-',
            'extra' => $booking['extra'] ?? '-',
            'parking'    => $parking,
            'vehicle'    => $booking['vehicle_make'] ?? '-',
            'created_at' => $booking['created_at']
        ];

        if ($type === 'cash') {
            // 💵 CASH JOB
            $cashBookings[] = $item;
            $totals['cash_fare'] += $fare;

        } else {
            // 🏦 ACCOUNT JOB
            $accountBookings[] = $item;
            $totals['account_fare'] += $fare;
            $totals['parking'] += $parking; // ✅ parking only here
        }
    }

    // 🔹 COMMISSION (20% of TOTAL FARE)
    // $totalFare = $totals['account_fare'] + $totals['cash_fare'];
    // $commissionRate = 0.20;
    // $driverCommission = round($totalFare * $commissionRate, 2);

    // // 🔹 BROUGHT FORWARD
    // $broughtForward = (float) ($driverData['brought_forward'] ?? 0);

    // // 🔹 DRIVER EARNING (PDF FORMULA)
    // $driverEarning =
    //     $totals['account_fare']
    //     + $totals['parking']
    //     - $driverCommission
    //     - $broughtForward;
    
    // 🔹 COMMISSION (20% of TOTAL FARE)
$totalFare = $totals['account_fare'] + $totals['cash_fare'];
$commissionRate = 0.20;
$driverCommission = round($totalFare * $commissionRate, 2);

// 🔹 BROUGHT FORWARD
$broughtForward = (float) ($driverData['brought_forward'] ?? 0);

// 🔹 FINAL CALCULATION
// $driverEarning = 
//     ($driverCommission - $totals['account_fare'])
//     + $totals['parking']
//     + $broughtForward;

$base = $driverCommission - $totals['account_fare'];

// If base is negative → subtract parking
if ($base < 0) {
    $base -= $totals['parking'];
} else {
    $base += $totals['parking'];
}

// Always add brought forward normally
$driverEarning = round($base + $broughtForward, 2);

// 🔹 SORT BOOKINGS BY FULL PICKUP DATETIME ASC
$sortBookings = function (&$bookings) {
    usort($bookings, function ($a, $b) {
        $datetimeA = \Carbon\Carbon::parse($a['date'] . ' ' . ($a['time'] ?? '00:00'));
        $datetimeB = \Carbon\Carbon::parse($b['date'] . ' ' . ($b['time'] ?? '00:00'));
        return $datetimeA->lt($datetimeB) ? -1 : ($datetimeA->gt($datetimeB) ? 1 : 0);
    });
};

$sortBookings($accountBookings);
$sortBookings($cashBookings);

// 🔹 Calculate total jobs
$totalJobs = count($accountBookings) + count($cashBookings);

    return [
        'driver'            => $driverData,
        'from'              => $from,
        'to'                => $to,
        'account_bookings'  => $accountBookings,
        'cash_bookings'     => $cashBookings,
        'totals'            => $totals,
        'commission'        => $driverCommission,
        'brought_forward'   => $broughtForward,
        'driver_earning'    => $driverEarning,
        'total_fare'        => $totalFare,
        'total_jobs'        => $totalJobs, // ✅ new variable
    ];
}
//     private function getDriverCommissionData($driverId, $from, $to)
// {
//     $firebaseBookings = $this->firebase->getData('bookings');
//     $driverData = $this->firebase->getData("drivers/$driverId");

//     $accountBookings = [];
//     $cashBookings = [];
//     $totals = [
//         'account_total' => 0,
//         'cash_total' => 0,
//         'tips' => 0,
//         'parking' => 0,
//         'waiting' => 0,
//     ];

//     if ($firebaseBookings) {
//         foreach ($firebaseBookings as $booking) {
//             if (!isset($booking['driver_id'], $booking['created_at'], $booking['price'])) {
//                 continue;
//             }

//             if ($booking['driver_id'] != $driverId) continue;

//             $bookingDate = date('Y-m-d', strtotime($booking['created_at']));
//             if ($bookingDate < $from || $bookingDate > $to) continue;

//             $type = $booking['payment_type'] ?? 'account';
//             $price = (float) ($booking['price'] ?? 0);
//             $tip = (float) ($booking['tip'] ?? 0);
//             $parking = (float) ($booking['parking'] ?? 0);
//             $waiting = (float) ($booking['waiting'] ?? 0);

//             $item = [
//                 'date' => $bookingDate,
//                 'time' => date('H:i', strtotime($booking['created_at'])),
//                 'booking_id' => $booking['booking_id'] ?? '-',
//                 'from' => $booking['pickup_address'] ?? '-',
//                 'to' => $booking['dropoff_address'] ?? '-',
//                 'income' => $price,
//                 'tip' => $tip,
//                 'parking' => $parking,
//                 'vehicle' => $booking['vehicle_type'] ?? '-',
//             ];

//             if (strtolower($type) === 'cash') {
//                 $cashBookings[] = $item;
//                 $totals['cash_total'] += $price;
//             } else {
//                 $accountBookings[] = $item;
//                 $totals['account_total'] += $price;
//             }

//             $totals['tips'] += $tip;
//             $totals['parking'] += $parking;
//             $totals['waiting'] += $waiting;
//         }
//     }

//     $commissionRate = 0.2;
//     $driverCommission = $totals['account_total'] * $commissionRate;

//     return [
//         'driver' => $driverData,
//         'from' => $from,
//         'to' => $to,
//         'account_bookings' => $accountBookings,
//         'cash_bookings' => $cashBookings,
//         'totals' => $totals,
//         'commission' => $driverCommission,
//     ];
// }




    /**
     * Turnover Report
     */
//     public function turnover(Request $request)
// {
//     $from = $request->from_date;
//     $to   = $request->to_date;

//     $firebaseBookings = $this->firebase->getData('bookings'); 
//     $dailyTurnover = [];

//     if ($firebaseBookings) {
//         foreach ($firebaseBookings as $booking) {
//             if (!isset($booking['created_at'], $booking['price'])) {
//                 continue;
//             }

//             $bookingDate = date('Y-m-d', strtotime($booking['created_at']));

//             if ($bookingDate >= $from && $bookingDate <= $to) {
//                 if (!isset($dailyTurnover[$bookingDate])) {
//                     $dailyTurnover[$bookingDate] = 0;
//                 }
//                 $dailyTurnover[$bookingDate] += (float) $booking['price'];
//             }
//         }
//     }

//     // Sort by date
//     ksort($dailyTurnover);

//     // Send to view
//     return view('reports.turnover', [
//         'dailyTurnover' => $dailyTurnover,
//         'from' => $from,
//         'to'   => $to,
//     ]);
// }

public function turnover(Request $request)
{
    $from = $request->from_date;
    $to   = $request->to_date;

    $firebaseBookings = $this->firebase->getData('bookings');
    $totals = [
        'fare_total' => 0,
        'fare_after_commission' => 0,
        'markup_fare' => 0,
        'service_charge' => 0,
        'extras' => 0,
        'markup_extras' => 0,
        'waiting' => 0,
        'parking' => 0,
        'markup_parking' => 0,
        'customer_toll' => 0,
        'driver_toll' => 0,
        'customer_ulez' => 0,
        'driver_ulez' => 0,
        'paid_to_drivers' => 0,
        'money_in_account' => 0,
    ];

    if ($firebaseBookings) {
        foreach ($firebaseBookings as $booking) {
            if (!isset($booking['price'], $booking['pickup_time'])) continue;
            
            // 🔹 EXCLUDE job_cancelled bookings
            if (isset($booking['booking_status']) && 
                strtolower($booking['booking_status']) === 'job_cancelled') {
                continue;
            }

            $bookingDate = date('Y-m-d', strtotime($booking['pickup_time']));
            if ($bookingDate < $from || $bookingDate > $to) continue;

            $price = (float) ($booking['price'] ?? 0);
            $parking = (float) ($booking['parking'] ?? 0);
            $waiting = (float) ($booking['waiting'] ?? 0);
            $extras  = (float) ($booking['extras'] ?? 0);
            $driverPaid = (float) ($booking['driver_paid'] ?? 0);

            $totals['fare_total'] += $price;
            $totals['fare_after_commission'] += $price * 0.8; // 20% commission
            $totals['markup_fare'] += $price;
            $totals['service_charge'] += 0;
            $totals['extras'] += $extras;
            $totals['markup_extras'] += $extras;
            $totals['waiting'] += $waiting;
            $totals['parking'] += $parking;
            $totals['markup_parking'] += $parking;
            $totals['paid_to_drivers'] += $driverPaid;
        }
    }

    // Derive final company earnings & money in account
    $totals['company_earning'] = $totals['fare_total'];
    $totals['company_earning_markup'] = $totals['markup_fare'];
    $totals['money_in_account'] = $totals['fare_total'] - $totals['paid_to_drivers'];
    
              $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

    return view('reports.turnover_pdf', [
        'from' => $from,
        'to' => $to,
        'totals' => $totals,
        'invoiceDate' => date('d M Y'),
        'drivers' => $drivers
    ]);
}



public function downloadTurnover(Request $request)
{
    $from = $request->from;
    $to   = $request->to;

    // Reuse same Firebase logic
    $firebaseBookings = $this->firebase->getData('bookings'); 
    $turnover = 0;

    if ($firebaseBookings) {
        foreach ($firebaseBookings as $booking) {
            if (!isset($booking['created_at'], $booking['price'])) {
                continue;
            }

            $bookingDate = date('Y-m-d', strtotime($booking['created_at']));

            if ($bookingDate >= $from && $bookingDate <= $to) {
                $turnover += (float) $booking['price'];
            }
        }
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.turnover_pdf', [
        'turnover' => $turnover,
        'from' => $from,
        'to'   => $to,
    ]);

    return $pdf->download("turnover_{$from}_to_{$to}.pdf");
}


    /**
     * Customer Report
     */
     
     //Perfected Version
//     public function customer(Request $request)
// {
//     $from = $request->from_date;
//     $to   = $request->to_date;
//     $type = $request->customer_type;
//     $id   = $request->customer_id;

//     // 🔥 Fetch all bookings from Firebase
//     $firebaseBookings = $this->firebase->getData('bookings');
//     $customers = [];

//     if ($firebaseBookings) {
//         foreach ($firebaseBookings as $key => $booking) {
//             // Convert timestamps into Carbon (assuming stored as string or timestamp)
//             $pickupTime = \Carbon\Carbon::parse($booking['created_at']);

//             // Filter by date
//             if ($pickupTime->between($from, $to)) {

//                 // Filter by payment type
//                 if ($type && ($booking['payment_type'] ?? null) !== $type) {
//                     continue;
//                 }

//                 // Filter by passenger id
//                 if ($id && ($booking['passenger']['id'] ?? null) != $id) {
//                     continue;
//                 }

//                 $customers[] = (object) $booking; // cast to object for Blade
//             }
//         }
//     }
    
//     // echo $customers;
//     // die;

//     return view('reports.customer', compact('customers', 'from', 'to', 'type', 'id'));
// }



// public function downloadCustomerReport(Request $request)
// {
//     $from = $request->from;
//     $to   = $request->to;

//     $customers = $this->getCustomerReportData($from, $to);

//     $pdf = Pdf::loadView('reports.customer_report_pdf', [
//         'customers' => $customers,
//         'from' => $from,
//         'to'   => $to,
//     ]);

//     return $pdf->download("customer_report_{$from}_to_{$to}.pdf");
// }


// public function getCustomerReportData($from, $to)
// {
//     // ✅ Ensure $from and $to are Carbon instances
//     try {
//         $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
//         $toDate   = \Carbon\Carbon::parse($to)->endOfDay();
//     } catch (\Exception $e) {
//         // If invalid date input, return empty result
//         return [];
//     }

//     // Fetch bookings from Firebase
//     $firebaseBookings = $this->firebase->getData('bookings');
//     $customers = [];

//     if ($firebaseBookings) {
//         foreach ($firebaseBookings as $key => $booking) {
//             if (!empty($booking['created_at'])) {
//                 try {
//                     // Handle Unix timestamp or string datetime
//                     if (is_numeric($booking['created_at'])) {
//                         $pickupTime = \Carbon\Carbon::createFromTimestamp($booking['created_at']);
//                     } else {
//                         $pickupTime = \Carbon\Carbon::parse($booking['created_at']);
//                     }

//                     // ✅ Compare against Carbon dates
//                     if ($pickupTime->between($fromDate, $toDate)) {
//                         $customers[] = (object) $booking;
//                     }
//                 } catch (\Exception $e) {
//                     // Skip invalid/unclean records
//                     continue;
//                 }
//             }
//         }
//     }

//     return $customers;
// }


// --- Existing function that renders the web view (reports.customer) ---
public function customer(Request $request)
{
    $from = $request->from_date;
    $to   = $request->to_date;
    $type = $request->customer_type;
    $customerId = $request->customer_id;

    // 🔹 Get Bookings
    $firebaseBookings = $this->firebase->getData('bookings') ?? [];

    // 🔹 Get Customers
    $customersData = $this->firebase->getData('customers') ?? [];

    // 🔹 Selected Customer Details
    $selectedCustomerPhone = null;
    $selectedCustomerEmail = null;
    $selectedCustomerName = null;

    if ($customerId) {
        foreach ($customersData as $customer) {

            if (isset($customer['id']) && $customer['id'] == $customerId) {

                //$selectedCustomerPhone = preg_replace('/\D/', '', $customer['phone'] ?? '');
                $selectedCustomerEmail = strtolower(trim($customer['email'] ?? ''));
                $selectedCustomerName = $customer['business_name'] ?? '';
                $selectedCustomerPhone = $customer['phone'] ?? '';

                break;
            }
        }
    }

    $customers = [];

    foreach ($firebaseBookings as $booking) {

        if (!isset($booking['pickup_time'])) {
            continue;
        }

        $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);

        // 🔹 Date Filter
        if ($from && $to) {
            $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
            $toDate   = \Carbon\Carbon::parse($to)->endOfDay();

            if (!$pickupTime->between($fromDate, $toDate)) {
                continue;
            }
        }

        // 🔹 Payment Type Filter
        if ($type && strtolower($booking['payment_type'] ?? '') !== strtolower($type)) {
            continue;
        }

        // 🔹 Booking Status MUST be completed
        if (strtolower($booking['status'] ?? '') !== 'completed') {
            continue;
        }

        // 🔹 Email AND Phone must match
        if ($customerId) {

            $bookingPhone = preg_replace('/\D/', '', $booking['phone_no'] ?? '');
            $bookingEmail = strtolower(trim($booking['email'] ?? ''));
            $bookingType = $booking['payment_type'];

            //$phoneMatch = $bookingPhone === $selectedCustomerPhone;
            $emailMatch = $bookingEmail === $selectedCustomerEmail;
            $typeMatch = $bookingType === $type;

            // BOTH must match
            // if (!($phoneMatch && $emailMatch)) {
            //     continue;
            // }
            if (!($typeMatch && $emailMatch)) {
                continue;
            }
        }

        // 🔹 Add Default Values (Base Fare = Price - Parking)
        $totalPrice          = (float) ($booking['price'] ?? 0.00);
        $parking             = (float) ($booking['parking'] ?? 0.00);
        $booking['parking']  = $parking;
        $booking['fare']     = max(0.00, $totalPrice - $parking);
        $booking['comments'] = $booking['job_comment'] ?? 'N/A';

        $customers[] = (object) $booking;
    }

    // 🔹 Get Drivers
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();

    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }
    
    // 🔹 Sort Customers by pickup_time ASC
usort($customers, function ($a, $b) {
    $timeA = \Carbon\Carbon::parse($a->pickup_time);
    $timeB = \Carbon\Carbon::parse($b->pickup_time);
    return $timeA->lt($timeB) ? -1 : ($timeA->gt($timeB) ? 1 : 0);
});

    return view('reports.customer', compact(
        'customers',
        'from',
        'to',
        'type',
        'customerId',
        'drivers',
        'selectedCustomerPhone',
        'selectedCustomerName'
    ));
}

// --- New/Modified function to generate and download the PDF ---
public function downloadCustomerReport(Request $request)
{
    $from = $request->from_date; // Use from_date and to_date as per your 'customer' method
    $to   = $request->to_date;

    // Use a unified function to get filtered data
    $customers = $this->getCustomerReportData($from, $to, $request->customer_type, $request->customer_id);

    // Load the new PDF-specific view
    $pdf = Pdf::loadView('reports.customer_report_pdf', [
        'customers' => $customers,
        'from' => $from,
        'to'   => $to,
    ]);

    // Download the generated file
    return $pdf->download("customer_report_{$from}_to_{$to}.pdf");
}




// 🔹 Send Driver Commission Report Email
    public function sendDriverCommissionEmail(Request $request)
    {
        
        // return response()->json(['success' => true, 'message' => 'Email sent successfully']);

         $driverId = $request->driver_id;
        $email = $request->email;
        $from = $request->from;
        $to = $request->to;
        
            $firebaseDriverKey = null;
            // ✅ FETCH DRIVERS FIRST
    $firebaseDrivers = $this->firebase->getData("drivers") ?? [];

foreach ($firebaseDrivers as $key => $driver) {
    if ((int) ($driver['id'] ?? 0) === (int) $driverId) {
        $firebaseDriverKey = $key; // ✅ -OkYXg9gLrYYL59Ce37J
        break;
    }
}


if (!$firebaseDriverKey) {
    return response()->json([
        'status' => false,
        'message' => 'Driver not found in Firebase'
    ], 404);
}
        
        // return response()->json(['success' => true, 'message' => 'Email sent successfully']);

        if (!$email || !$driverId) {
            return response()->json(['success' => false, 'message' => 'Missing email or driver ID']);
        }

        // Generate commission data
        $data = $this->getDriverCommissionData($firebaseDriverKey, $from, $to);

//return response()->json(['success' => true, 'message' => 'Email sent successfully']);
        // Send email
        try {
            Mail::to($email)->send(new DriverCommissionMail($data));
            return response()->json(['success' => true, 'message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Email sending failed', 'error' => $e->getMessage()]);
        }
    }
    



// public function sendCustomerReport(Request $request)
// {
//     $request->validate([
//         'email'       => 'required|email',
//         'from'        => 'required|date',
//         'to'          => 'required|date',
//         'customer_id' => 'nullable',
//         'customer_type' => 'nullable'
//     ]);

//     $from = \Carbon\Carbon::parse($request->from)->startOfDay();
//     $to   = \Carbon\Carbon::parse($request->to)->endOfDay();
//     $type = $request->customer_type;
//     $customerId = $request->customer_id;

//     $firebaseBookings = $this->firebase->getData('bookings') ?? [];
//     $customersData    = $this->firebase->getData('customers') ?? [];

//     // 🔹 Find selected customer name
//     $selectedCustomerName = null;

//     if ($customerId) {
//         foreach ($customersData as $firebaseKey => $customer) {
//             if (isset($customer['id']) && $customer['id'] == $customerId) {
//                 $selectedCustomerName = strtolower(trim($customer['business_name']));
//                 break;
//             }
//         }
//     }

//     $filteredBookings = [];

//     foreach ($firebaseBookings as $key => $booking) {

//         if (!isset($booking['created_at'])) {
//             continue;
//         }

//         $createdAt = \Carbon\Carbon::parse($booking['created_at']);

//         // Date filter
//         if (!$createdAt->between($from, $to)) {
//             continue;
//         }

//         // Payment type filter
//         if ($type && strtolower($booking['payment_type'] ?? '') !== strtolower($type)) {
//             continue;
//         }

//         // Customer name match
//         if ($selectedCustomerName) {
//             $bookingPassengerName = strtolower(trim($booking['passenger_name'] ?? ''));

//             if ($bookingPassengerName !== $selectedCustomerName) {
//                 continue;
//             }
//         }

//         $booking['id'] = $key;
//         $booking['fare'] = $booking['price'] ?? 0.00;
//         $booking['parking'] = $booking['parking'] ?? 0.00;

//         $filteredBookings[] = (object) $booking;
//     }

//     try {
//         \Mail::to($request->email)
//             ->send(new \App\Mail\CustomerReportMail($filteredBookings, $from, $to));

//         return response()->json([
//             'success' => true,
//             'message' => 'Report sent successfully!'
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Email sending failed.',
//             'error'   => $e->getMessage()
//         ]);
//     }
// }

// public function sendCustomerReport(Request $request)
// {
//     $request->validate([
//         'email'         => 'required|email',
//         'from'          => 'required|date',
//         'to'            => 'required|date',
//         'customer_id'   => 'nullable',
//         'customer_type' => 'nullable'
//     ]);

//     $from = \Carbon\Carbon::parse($request->from)->startOfDay();
//     $to   = \Carbon\Carbon::parse($request->to)->endOfDay();
//     $type = $request->customer_type;
//     $customerId = $request->customer_id;

//     $firebaseBookings = $this->firebase->getData('bookings') ?? [];
//     $customersData    = $this->firebase->getData('customers') ?? [];

//     // 🔹 Selected Customer Details
//     $selectedCustomerName  = null;
//     $selectedCustomerPhone = null;

//     if ($customerId) {
//         foreach ($customersData as $firebaseKey => $customer) {

//             if (isset($customer['id']) && $customer['id'] == $customerId) {

//                 $selectedCustomerName  = strtolower(trim($customer['business_name'] ?? ''));
//                 $selectedCustomerPhone = preg_replace('/\D/', '', $customer['phone'] ?? '');

//                 break;
//             }
//         }
//     }

//     $filteredBookings = [];

//     foreach ($firebaseBookings as $key => $booking) {

//         if (!isset($booking['created_at'])) {
//             continue;
//         }

//         $createdAt = \Carbon\Carbon::parse($booking['created_at']);

//         // 🔹 Date Filter
//         if (!$createdAt->between($from, $to)) {
//             continue;
//         }

//         // 🔹 Payment Type Filter
//         if ($type && strtolower($booking['payment_type'] ?? '') !== strtolower($type)) {
//             continue;
//         }

//         // 🔹 Match ONLY Name + Phone
//         if ($selectedCustomerName || $selectedCustomerPhone) {

//             $bookingPassengerName = strtolower(trim($booking['passenger_name'] ?? ''));
//             $bookingPhone         = preg_replace('/\D/', '', $booking['phone_no'] ?? '');

//             $nameMatch  = $selectedCustomerName && $bookingPassengerName === $selectedCustomerName;
//             $phoneMatch = $selectedCustomerPhone && $bookingPhone === $selectedCustomerPhone;

//             if (!$nameMatch && !$phoneMatch) {
//                 continue;
//             }
//         }

//         // 🔹 Add Extra Data
//         $booking['id']      = $key;
//         $booking['fare']    = $booking['price'] ?? 0.00;
//         $booking['parking'] = $booking['parking'] ?? 0.00;
//         $booking['comments'] = $booking['job_comment'] ?? 'N/A';

//         $filteredBookings[] = (object) $booking;
//     }

//     try {

//         \Mail::to($request->email)
//             ->send(new \App\Mail\CustomerReportMail($filteredBookings, $from, $to));

//         return response()->json([
//             'success' => true,
//             'message' => 'Report sent successfully!'
//         ]);

//     } catch (\Exception $e) {

//         return response()->json([
//             'success' => false,
//             'message' => 'Email sending failed.',
//             'error'   => $e->getMessage()
//         ]);
//     }
// }
    
public function sendCustomerReport(Request $request)
{
    $request->validate([
        'email'         => 'required|email',
        'from'          => 'nullable|date',
        'to'            => 'nullable|date',
        'customer_id'   => 'nullable',
        'customer_type' => 'nullable'
    ]);

    $from = $request->from;
    $to   = $request->to;
    $type = $request->customer_type;
    $customerId = $request->customer_id;

    $firebaseBookings = $this->firebase->getData('bookings') ?? [];
    $customersData    = $this->firebase->getData('customers') ?? [];

    // 🔹 Selected Customer Details
    $selectedCustomerPhone = null;
    $selectedCustomerEmail = null;

    if ($customerId) {
        foreach ($customersData as $customer) {

            if (isset($customer['id']) && $customer['id'] == $customerId) {

                $selectedCustomerPhone = preg_replace('/\D/', '', $customer['phone'] ?? '');
                $selectedCustomerEmail = strtolower(trim($customer['email'] ?? ''));

                break;
            }
        }
    }

    $filteredBookings = [];

    foreach ($firebaseBookings as $key => $booking) {

        if (!isset($booking['pickup_time'])) {
            continue;
        }

        $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);

        // 🔹 Date Filter (optional)
        if ($from && $to) {

            $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
            $toDate   = \Carbon\Carbon::parse($to)->endOfDay();

            if (!$pickupTime->between($fromDate, $toDate)) {
                continue;
            }
        }

        // 🔹 Payment Type Filter
        if ($type && strtolower($booking['payment_type'] ?? '') !== strtolower($type)) {
            continue;
        }

        // 🔹 Status must be completed
        if (strtolower($booking['status'] ?? '') !== 'completed') {
            continue;
        }

        // 🔹 Email AND Phone must match
        if ($customerId) {

            $bookingPhone = preg_replace('/\D/', '', $booking['phone_no'] ?? '');
            $bookingEmail = strtolower(trim($booking['email'] ?? ''));
             $bookingType = $booking['payment_type'];

            //$phoneMatch = $bookingPhone === $selectedCustomerPhone;
            $emailMatch = $bookingEmail === $selectedCustomerEmail;
            $typeMatch = $bookingType === $type;

            // BOTH must match
            // if (!($phoneMatch && $emailMatch)) {
            //     continue;
            // }
            if (!($typeMatch && $emailMatch)) {
                continue;
            }
        }

        // 🔹 Add Extra Data (Base Fare = Price - Parking)
        $booking['id']       = $key;
        $totalPrice          = (float) ($booking['price'] ?? 0.00);
        $parking             = (float) ($booking['parking'] ?? 0.00);
        $booking['parking']  = $parking;
        $booking['fare']     = max(0.00, $totalPrice - $parking);
        $booking['comments'] = $booking['job_comment'] ?? 'N/A';

        $filteredBookings[] = (object) $booking;
    }
    
        // 🔹 Sort Customers by pickup_time ASC
usort($filteredBookings, function ($a, $b) {
    $timeA = \Carbon\Carbon::parse($a->pickup_time);
    $timeB = \Carbon\Carbon::parse($b->pickup_time);
    return $timeA->lt($timeB) ? -1 : ($timeA->gt($timeB) ? 1 : 0);
});

    try {

        \Mail::to($request->email)
            ->send(new \App\Mail\CustomerReportMail($filteredBookings, $from, $to));

        return response()->json([
            'success' => true,
            'message' => 'Report sent successfully!'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Email sending failed.',
            'error'   => $e->getMessage()
        ]);
    }
}

/**
 * Helper function to retrieve filtered report data, ensuring all necessary fields exist.
 * This combines your existing filtering logic into a reusable function for both web and PDF.
 */
// public function getCustomerReportData($from, $to, $type = null, $id = null)
// {
//     try {
//         $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
//         $toDate   = \Carbon\Carbon::parse($to)->endOfDay();
//     } catch (\Exception $e) {
//         return [];
//     }

//     $firebaseBookings = $this->firebase->getData('bookings');
//     $customers = [];

//     if ($firebaseBookings) {
//         foreach ($firebaseBookings as $booking) {
//             if (empty($booking['created_at'])) {
//                 continue;
//             }

//             try {
//                 $pickupTime = is_numeric($booking['created_at'])
//                     ? \Carbon\Carbon::createFromTimestamp($booking['created_at'])
//                     : \Carbon\Carbon::parse($booking['created_at']);

//                 if ($pickupTime->between($fromDate, $toDate)) {

//                     if ($type && ($booking['payment_type'] ?? null) !== $type) {
//                         continue;
//                     }

//                     if ($id && ($booking['passenger']['id'] ?? null) != $id) {
//                         continue;
//                     }

//                     // Map fields for PDF consistency (FARE, PARKING, COMMENTS)
//                     $booking['fare'] = $booking['price'] ?? 0.00;
//                     $booking['parking'] = $booking['parking'] ?? 0.00; // Placeholder
//                     $booking['comments'] = $booking['comments'] ?? ($booking['ref_no'] ?? 'N/A');

//                     $customers[] = (object) $booking;
//                 }
//             } catch (\Exception $e) {
//                 // Skip invalid records
//                 continue;
//             }
//         }
//     }

//     return $customers;
// }



// public function sendCustomerReport(Request $request)
// {
//     $request->validate(['email' => 'required|email']);

//     $from = $request->from;
//     $to = $request->to;
    
    

//     $customers = Booking::whereBetween('created_at', [$from, $to])->get();

//     // Generate PDF
//     $pdf = PDF::loadView('reports.customer_report_pdf', compact('customers', 'from', 'to'));

//     // Send email with PDF
//     Mail::to($request->email)->send(new CustomerReportMail($pdf->output(), $from, $to));

//     return response()->json(['success' => true]);
// }






public function getDriverEmail($id)
{
    try {
        // Fetch all drivers
        $drivers = $this->firebase->getData("drivers");

        if (!$drivers) {
            return response()->json(['error' => 'No drivers found'], 404);
        }

        // Search for the driver with matching id
        foreach ($drivers as $key => $driver) {
            if (isset($driver['id']) && (string)$driver['id'] === (string)$id) {
                return response()->json([
                    'email' => $driver['email'] ?? null,
                    'name' => $driver['name'] ?? 'Unknown',
                    'phone' => $driver['phone'] ?? 'N/A'
                ]);
            }
        }

        return response()->json(['error' => 'Driver not found'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


// public function sendCommissionEmail(Request $request)
// {
//     try {
//         $email = $request->email;
//         $from = $request->from;
//         $to = $request->to;
//         $driverId = $request->driver_id;

//         \Log::info("📩 Sending commission email to: $email for driver $driverId from $from to $to");

//         // 🔹 (Optional) fetch data if needed
//         // $data = $this->getDriverCommissionData($driverId, $from, $to);

//         // 🧾 Generate PDF (simple test)
//         ini_set('memory_limit', '512M');
//         set_time_limit(300);

//         $pdf = Pdf::loadView('reports.driver_commission_pdf', [
//             'driver' => ['name' => 'Test Driver'],
//             'from' => '2025-09-01',
//             'to' => '2025-10-31',
//             'account_bookings' => [],
//             'cash_bookings' => [],
//             'totals' => [],
//             'commission' => 0,
//         ]);

//         // ✅ Debug: Check if PDF actually renders
//         $pdfOutput = $pdf->output();
//         if (!$pdfOutput) {
//             throw new \Exception("PDF generation failed — no output returned.");
//         }

//         // 📧 Send Email (temporarily commented for debugging)
//         // \Mail::to($email)->send(new \App\Mail\DriverCommissionMail($pdfOutput, $from, $to));

//         \Log::info("✅ PDF generated successfully for $email");

//         return response()->json(['success' => true]);
//     } catch (\Exception $e) {
//         \Log::error('❌ Driver Commission Email failed: ' . $e->getMessage());
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }




}
