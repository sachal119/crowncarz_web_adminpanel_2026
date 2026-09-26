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
use App\Mail\CustomerReportMail;
use App\Mail\TurnoverReportMail;
use App\Services\WhatsAppGatewayService;


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
                    'name' => $driver['name'],
                    'call_sign' => $driver['call_sign'] ?? $driver['callsign'] ?? ''
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
    
    /**
     * Helper to check if a booking matches the given status filter
     */
    protected function matchesBookingStatus($bookingStatus, $statusFilter = 'completed')
    {
        $bookingStatus = strtolower(trim((string)$bookingStatus));
        
        if (empty($statusFilter)) {
            $statusFilter = 'completed';
        }
        
        $statusFilter = strtolower(trim((string)$statusFilter));
        
        if ($statusFilter === 'all') {
            return true;
        }
        
        $allowedStatuses = array_map('trim', explode(',', $statusFilter));
        
        foreach ($allowedStatuses as $allowed) {
            if ($allowed === 'all') {
                return true;
            }
            if ($allowed === 'cancelled' || $allowed === 'job_cancelled') {
                if ($bookingStatus === 'cancelled' || $bookingStatus === 'job_cancelled') {
                    return true;
                }
            } elseif ($bookingStatus === $allowed) {
                return true;
            }
        }
        
        return false;
    }

    public function driverCommission(Request $request)
{
    $driverId = $request->driver_id; // ✅ Get driver ID
    $from = $request->from_date ?? $request->from;
    $to   = $request->to_date ?? $request->to;
    $status = $request->get('booking_status', $request->get('status', 'completed'));

    // Get all drivers from Firebase
    $firebaseDrivers = $this->firebase->getData('drivers') ?? [];

    $firebaseDriverKey = null;

    foreach ($firebaseDrivers as $key => $driver) {
        if ((int) ($driver['id'] ?? 0) === (int) $driverId) {
            $firebaseDriverKey = $key;
            break;
        }
    }

    if (!$firebaseDriverKey) {
        return response()->json([
            'status' => false,
            'message' => 'Driver not found in Firebase'
        ], 404);
    }

    // ✅ Pass Firebase key and status
    $data = $this->getDriverCommissionData($firebaseDriverKey, $from, $to, $status);

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
        'status' => $status,
        'booking_status' => $status,
        'drivers' => $drivers
    ]));
}

public function downloadDriverCommission(Request $request)
{
    $driverId = $request->driver_id;
    $from = $request->from ?? $request->from_date;
    $to   = $request->to ?? $request->to_date;
    $status = $request->get('booking_status', $request->get('status', 'completed'));
    
    $firebaseDrivers = $this->firebase->getData('drivers') ?? [];
    $firebaseDriverKey = null;

    foreach ($firebaseDrivers as $key => $driver) {
        if ((int) ($driver['id'] ?? 0) === (int) $driverId) {
            $firebaseDriverKey = $key;
            break;
        }
    }

    if (!$firebaseDriverKey) {
        return response()->json([
            'status' => false,
            'message' => 'Driver not found in Firebase'
        ], 404);
    }

    $data = $this->getDriverCommissionData($firebaseDriverKey, $from, $to, $status);
    $driverName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['driver']['name'] ?? 'Driver');
    $callsignRaw = $data['driver']['call_sign'] ?? $data['driver']['callsign'] ?? '';
    $callsign = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string)$callsignRaw);
    $dateStr = date('d-m-Y');
    $minutesStr = date('Hi');
    if (!empty($callsign)) {
        $fileName = "{$driverName}_{$callsign}_{$dateStr}_{$minutesStr}.pdf";
    } else {
        $fileName = "{$driverName}_{$dateStr}_{$minutesStr}.pdf";
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.driver_commission_pdf', array_merge($data, [
        'driverId' => $driverId,
        'status' => $status,
        'booking_status' => $status,
    ]))->setPaper('a4', 'landscape');

    return $pdf->download($fileName);
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

private function getDriverCommissionData($driverId, $from, $to, $status = 'completed')
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
            (string)$booking['driver_id'] !== (string)$driverId ||
            empty($booking['created_at'])
        ) {
            continue;
        }

        $bStatus = $booking['status'] ?? $booking['booking_status'] ?? '';
        if (!$this->matchesBookingStatus($bStatus, $status)) {
            continue;
        }

        $bookingDate = date('Y-m-d', strtotime($booking['pickup_time']));
        if ($bookingDate < $from || $bookingDate > $to) {
            continue;
        }

        // 🔹 Extract Base Fare: Total Price excludes parking, extras, waiting fee (which are displayed in separate columns)
        $rawFare = isset($booking['fare']) && is_numeric($booking['fare']) ? (float)$booking['fare'] : 0.0;
        $rawPrice = isset($booking['price']) && is_numeric($booking['price']) ? (float)$booking['price'] : 0.0;
        if ($rawPrice <= 0) {
            $rawPrice = (float)($booking['total_price'] ?? $booking['final_price'] ?? $booking['amount'] ?? $booking['base_fare'] ?? $booking['driver_fare'] ?? 0.0);
        }

        $parking = isset($booking['parking']) && is_numeric($booking['parking']) ? (float)$booking['parking'] : 0.0;
        $extra = isset($booking['extra']) && is_numeric($booking['extra']) ? (float)$booking['extra'] : 0.0;
        $waitingFee = isset($booking['waiting_fee']) && is_numeric($booking['waiting_fee']) ? (float)$booking['waiting_fee'] : 0.0;
        $additionalCharges = $parking + $extra + $waitingFee;

        if ($rawFare > 0 && ($rawPrice <= 0 || $rawFare < $rawPrice)) {
            // rawFare is already base fare
            $fare = $rawFare;
        } elseif ($rawPrice > 0) {
            // Price is Total Price, so Base Fare = Total Price - Parking - Extra - Waiting Fee
            $fare = max(0.0, $rawPrice - $additionalCharges);
        } elseif ($rawFare > 0) {
            $fare = max(0.0, $rawFare - $additionalCharges);
        } else {
            $fare = 0.0;
        }

        $type = strtolower(trim($booking['payment_type'] ?? 'account'));

        $item = [
            'date'       => $bookingDate,
            'time'       => date('H:i', strtotime($booking['pickup_time'])),
            'booking_id' => $booking['ref_no'] ?? '-',
            'from'       => $booking['pickup_address'] ?? '-',
            'to'         => $booking['dropoff_address'] ?? '-',
            'via'        => $booking['via'] ?? '-',
            'fare'       => $fare,
            'waiting_fee' => $waitingFee > 0 ? number_format($waitingFee, 2) : ($booking['waiting_fee'] ?? '-'),
            'extra'      => $extra,
            'parking'    => $parking,
            'vehicle'    => $booking['vehicle_make'] ?? '-',
            'created_at' => $booking['created_at']
        ];

        if ($type === 'cash' || $type === 'pay in car') {
            // 💵 CASH JOB
            $cashBookings[] = $item;
            $totals['cash_fare'] += $fare;
            $totals['extra_cash'] = ($totals['extra_cash'] ?? 0.0) + $extra;
        } else {
            // 🏦 ACCOUNT JOB
            $accountBookings[] = $item;
            $totals['account_fare'] += $fare;
            $totals['parking'] += $parking; // ✅ parking only here
            $totals['extra'] = ($totals['extra'] ?? 0.0) + $extra;
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

public function getTurnoverData($from, $to, $status = 'completed')
{
    $firebaseBookings = $this->firebase->getData('bookings') ?? [];
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
            
            // 🔹 Booking Status Filter
            $bStatus = $booking['status'] ?? $booking['booking_status'] ?? '';
            if (!$this->matchesBookingStatus($bStatus, $status)) {
                continue;
            }

            $bookingDate = date('Y-m-d', strtotime($booking['pickup_time']));
            if ($from && $to && ($bookingDate < $from || $bookingDate > $to)) continue;

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

    return $totals;
}

public function turnover(Request $request)
{
    $from = $request->from_date ?? $request->from;
    $to   = $request->to_date ?? $request->to;
    $status = $request->get('booking_status', $request->get('status', 'completed'));

    $totals = $this->getTurnoverData($from, $to, $status);
    
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();

    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }

    return view('reports.turnover_pdf', [
        'from' => $from,
        'to' => $to,
        'status' => $status,
        'booking_status' => $status,
        'totals' => $totals,
        'invoiceDate' => date('d M Y'),
        'drivers' => $drivers
    ]);
}

public function downloadTurnover(Request $request)
{
    $from = $request->from ?? $request->from_date;
    $to   = $request->to ?? $request->to_date;
    $status = $request->get('booking_status', $request->get('status', 'completed'));

    $totals = $this->getTurnoverData($from, $to, $status);
    $invoiceDate = date('d M Y');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.turnover_export_pdf', [
        'totals' => $totals,
        'from' => $from,
        'to'   => $to,
        'status' => $status,
        'booking_status' => $status,
        'invoiceDate' => $invoiceDate,
    ]);

    return $pdf->download("turnover_{$from}_to_{$to}.pdf");
}

public function sendTurnoverEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'from'  => 'required|date',
        'to'    => 'required|date',
    ]);

    $from = $request->from;
    $to   = $request->to;
    $status = $request->get('booking_status', $request->get('status', 'completed'));
    $totals = $this->getTurnoverData($from, $to, $status);
    $invoiceDate = date('d M Y');

    try {
        \Mail::to($request->email)->send(new \App\Mail\TurnoverReportMail($totals, $from, $to, $invoiceDate));
        return response()->json(['success' => true, 'message' => 'Turnover Report emailed successfully!']);
    } catch (\Exception $e) {
        \Log::error('Turnover email error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
    }
}


    /**
     * Customer Report
     */
    /**
     * Helper to get unified filtered customer report bookings based on account/customer and date range
     */
    public function getCustomerReportData($from, $to, $type = null, $customerId = null, $status = 'completed')
    {
        return $this->getCustomerReportDataInternal($from, $to, $type, $customerId, $status)['bookings'];
    }

    private function getCustomerReportDataInternal($from, $to, $type = null, $customerId = null, $status = 'completed')
    {
        $firebaseBookings = $this->firebase->getData('bookings') ?? [];
        $customersData    = $this->firebase->getData('customers') ?? [];

        $selectedCustomerEmail = null;
        $selectedCustomerName  = null;
        $selectedCustomerPhone = null;

        if ($customerId) {
            foreach ($customersData as $key => $customer) {
                $cId = isset($customer['id']) ? (string) $customer['id'] : (string) $key;
                if ($cId === (string) $customerId || (string) $key === (string) $customerId) {
                    $selectedCustomerEmail = strtolower(trim($customer['email'] ?? ''));
                    $selectedCustomerName  = trim($customer['business_name'] ?? ($customer['name'] ?? ''));
                    $selectedCustomerPhone = trim($customer['phone'] ?? '');
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

            // 🔹 Date Filter
            if ($from && $to) {
                $fromDate = \Carbon\Carbon::parse($from)->startOfDay();
                $toDate   = \Carbon\Carbon::parse($to)->endOfDay();

                if (!$pickupTime->between($fromDate, $toDate)) {
                    continue;
                }
            }

            // 🔹 Payment Type Filter
            if (!empty($type) && strtolower(trim($booking['payment_type'] ?? '')) !== strtolower(trim($type))) {
                continue;
            }

            // 🔹 Booking Status Filter
            $bStatus = $booking['status'] ?? $booking['booking_status'] ?? '';
            if (!$this->matchesBookingStatus($bStatus, $status)) {
                continue;
            }

            // 🔹 Account / Customer Matching Filter (Strictly match only selected account or customer)
            if ($customerId) {
                $bookingPaymentType = strtolower(trim((string) ($booking['payment_type'] ?? '')));
                $bookingAccountId   = (string) ($booking['account_id'] ?? '');
                $bookingAccName     = trim((string) ($booking['account_name'] ?? ''));
                $bookingEmail       = strtolower(trim((string) ($booking['email'] ?? '')));
                $bookingPhone       = preg_replace('/\D/', '', (string) ($booking['phone_no'] ?? ''));

                $matched = false;

                // 1. Account Bookings: Strictly match by Account ID or exact Business Name
                if ($bookingPaymentType === 'account') {
                    if ($bookingAccountId !== '' && $bookingAccountId === (string) $customerId) {
                        $matched = true;
                    } elseif ($selectedCustomerName !== '' && $bookingAccName !== '' && strcasecmp($bookingAccName, $selectedCustomerName) === 0) {
                        $matched = true;
                    }
                } else {
                    // 2. Individual Cash/Card customer bookings: Match by Customer Email or Phone
                    if ($selectedCustomerEmail !== '' && $bookingEmail !== '' && $bookingEmail === $selectedCustomerEmail) {
                        $matched = true;
                    } elseif ($selectedCustomerPhone !== '' && $bookingPhone !== '' && $bookingPhone === preg_replace('/\D/', '', $selectedCustomerPhone)) {
                        $matched = true;
                    }
                }

                if (!$matched) {
                    continue;
                }
            }

            // 🔹 Add Default Values (Base Fare = Price - Parking)
            $totalPrice          = (float) ($booking['price'] ?? 0.00);
            $parking             = (float) ($booking['parking'] ?? 0.00);
            $booking['id']       = $key;
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

        return [
            'bookings'              => $filteredBookings,
            'selectedCustomerEmail' => $selectedCustomerEmail,
            'selectedCustomerName'  => $selectedCustomerName,
            'selectedCustomerPhone' => $selectedCustomerPhone,
        ];
    }

    public function customer(Request $request)
    {
        $from = $request->from_date ?? $request->from;
        $to   = $request->to_date ?? $request->to;
        $type = $request->customer_type;
        $customerId = $request->customer_id;
        $status = $request->get('booking_status', $request->get('status', 'completed'));

        $reportData = $this->getCustomerReportDataInternal($from, $to, $type, $customerId, $status);
        $customers = $reportData['bookings'];
        $selectedCustomerName  = $reportData['selectedCustomerName'];
        $selectedCustomerPhone = $reportData['selectedCustomerPhone'];
        $selectedCustomerEmail = $reportData['selectedCustomerEmail'];

        // 🔹 Get Drivers
        $driversData = $this->firebase->getData('drivers') ?? [];
        $drivers = collect();

        foreach ($driversData as $id => $driver) {
            $driver['id'] = $id;
            $drivers->push($driver);
        }

        return view('reports.customer', compact(
            'customers',
            'from',
            'to',
            'type',
            'customerId',
            'drivers',
            'selectedCustomerPhone',
            'selectedCustomerName',
            'selectedCustomerEmail',
            'status'
        ));
    }

    // --- Function to generate and download the PDF ---
    public function downloadCustomerReport(Request $request)
    {
        $from = $request->from_date ?? $request->from;
        $to   = $request->to_date ?? $request->to;
        $type = $request->customer_type;
        $customerId = $request->customer_id;
        $status = $request->get('booking_status', $request->get('status', 'completed'));

        // Use unified function to get filtered data
        $customers = $this->getCustomerReportData($from, $to, $type, $customerId, $status);

        // Load the PDF-specific view
        $pdf = Pdf::loadView('reports.customer_report_pdf', [
            'customers' => $customers,
            'from' => $from,
            'to'   => $to,
            'status' => $status,
        ]);

        return $pdf->download("customer_report_{$from}_to_{$to}.pdf");
    }

    // 🔹 Send Driver Commission Report Email
    public function sendDriverCommissionEmail(Request $request)
    {
        $driverId = $request->driver_id;
        $email = $request->email;
        $from = $request->from;
        $to = $request->to;
        $status = $request->get('booking_status', $request->get('status', 'completed'));
        
        $firebaseDriverKey = null;
        $firebaseDrivers = $this->firebase->getData("drivers") ?? [];

        foreach ($firebaseDrivers as $key => $driver) {
            if ((int) ($driver['id'] ?? 0) === (int) $driverId) {
                $firebaseDriverKey = $key;
                break;
            }
        }

        if (!$firebaseDriverKey) {
            return response()->json([
                'status' => false,
                'message' => 'Driver not found in Firebase'
            ], 404);
        }

        if (!$email || !$driverId) {
            return response()->json(['success' => false, 'message' => 'Missing email or driver ID']);
        }

        // Generate commission data
        $data = $this->getDriverCommissionData($firebaseDriverKey, $from, $to, $status);

        // Send email
        try {
            Mail::to($email)->send(new DriverCommissionMail($data));
            return response()->json(['success' => true, 'message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Email sending failed', 'error' => $e->getMessage()]);
        }
    }

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
        $status = $request->get('booking_status', $request->get('status', 'completed'));

        $filteredBookings = $this->getCustomerReportData($from, $to, $type, $customerId, $status);

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

    public function getDriverEmail($id)
    {
        try {
            $drivers = $this->firebase->getData("drivers");

            if (!$drivers) {
                return response()->json(['error' => 'No drivers found'], 404);
            }

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

    /**
     * 🟢 Send Driver Commission Statement PDF via WhatsApp Gateway
     */
    public function sendDriverCommissionWhatsApp(Request $request, WhatsAppGatewayService $wa)
    {
        $request->validate([
            'driver_id' => 'required',
            'from'      => 'required',
            'to'        => 'required',
            'phone'     => 'nullable|string|max:30',
        ]);

        $driverId = $request->driver_id;
        $from     = $request->from;
        $to       = $request->to;
        $status   = $request->get('booking_status', $request->get('status', 'completed'));

        // Fetch driver from Firebase
        $firebaseDrivers = $this->firebase->getData("drivers") ?? [];
        $firebaseDriverKey = null;
        $driverData = null;

        foreach ($firebaseDrivers as $key => $driver) {
            if ((int) ($driver['id'] ?? 0) === (int) $driverId || (string)($driver['id'] ?? '') === (string)$driverId) {
                $firebaseDriverKey = $key;
                $driverData = $driver;
                break;
            }
        }

        if (!$firebaseDriverKey) {
            return response()->json(['success' => false, 'message' => 'Driver not found in system.'], 404);
        }

        $phone = $request->input('phone') ?: ($driverData['phone'] ?? null);
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Driver WhatsApp phone number is required.'], 422);
        }

        try {
            $data = $this->getDriverCommissionData($firebaseDriverKey, $from, $to, $status);
            $driverName = $data['driver']['name'] ?? 'Driver';
            $callsignRaw = $data['driver']['call_sign'] ?? $data['driver']['callsign'] ?? '';
            $dateStr = date('d-m-Y');
            $minutesStr = date('Hi');
            $fileName = !empty($callsignRaw) 
                ? "{$driverName}_{$callsignRaw}_{$dateStr}_{$minutesStr}.pdf" 
                : "{$driverName}_{$dateStr}_{$minutesStr}.pdf";

            $pdf = Pdf::loadView('reports.driver_commission_pdf', array_merge($data, [
                'driverId' => $driverId,
                'status' => $status,
                'booking_status' => $status,
            ]))->setPaper('a4', 'landscape');

            $rawPdf = $pdf->output();

            $formattedFrom = \Carbon\Carbon::parse($from)->format('d M Y');
            $formattedTo   = \Carbon\Carbon::parse($to)->format('d M Y');

            $caption = "Dear {$driverName},\n\nPlease find attached your Driver Commission Statement for the period {$formattedFrom} to {$formattedTo}.\n\nThank you,\nCrown Carz Management";

            $result = $wa->sendPdfBinary(
                phone: (string) $phone,
                rawPdfContent: $rawPdf,
                fileName: $fileName,
                caption: $caption
            );

            return response()->json($result, ($result['success'] ?? false) ? 200 : 502);
        } catch (\Throwable $e) {
            \Log::error('Driver Commission WhatsApp Send Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to generate/send statement: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🟢 Send Turnover Report PDF via WhatsApp Gateway
     */
    public function sendTurnoverWhatsApp(Request $request, WhatsAppGatewayService $wa)
    {
        $request->validate([
            'phone' => 'required|string|max:30',
            'from'  => 'required|date',
            'to'    => 'required|date',
        ]);

        $from = $request->from;
        $to   = $request->to;
        $phone = $request->phone;
        $status = $request->get('booking_status', $request->get('status', 'completed'));

        try {
            $totals = $this->getTurnoverData($from, $to, $status);
            $invoiceDate = date('d M Y');

            $pdf = Pdf::loadView('reports.turnover_export_pdf', [
                'totals'      => $totals,
                'from'        => $from,
                'to'          => $to,
                'status'      => $status,
                'invoiceDate' => $invoiceDate,
            ]);

            $rawPdf = $pdf->output();
            $fileName = "Turnover_Report_{$from}_to_{$to}.pdf";

            $formattedFrom = \Carbon\Carbon::parse($from)->format('d M Y');
            $formattedTo   = \Carbon\Carbon::parse($to)->format('d M Y');

            $caption = "Crown Carz - Official Turnover Report\nPeriod: {$formattedFrom} to {$formattedTo}\nGenerated on: {$invoiceDate}";

            $result = $wa->sendPdfBinary(
                phone: (string) $phone,
                rawPdfContent: $rawPdf,
                fileName: $fileName,
                caption: $caption
            );

            return response()->json($result, ($result['success'] ?? false) ? 200 : 502);
        } catch (\Throwable $e) {
            \Log::error('Turnover WhatsApp Send Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to generate/send turnover report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🟢 Send Customer Report PDF via WhatsApp Gateway
     */
    public function sendCustomerReportWhatsApp(Request $request, WhatsAppGatewayService $wa)
    {
        $request->validate([
            'phone'         => 'required|string|max:30',
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'customer_id'   => 'nullable',
            'customer_type' => 'nullable'
        ]);

        $from = $request->from;
        $to   = $request->to;
        $type = $request->customer_type;
        $customerId = $request->customer_id;
        $phone = $request->phone;
        $status = $request->get('booking_status', $request->get('status', 'completed'));

        $result = $this->getCustomerReportDataInternal($from, $to, $type, $customerId, $status);
        $filteredBookings     = $result['bookings'];
        $selectedCustomerName = $result['selectedCustomerName'];

        try {
            $pdf = Pdf::loadView('reports.customer_report_pdf', [
                'customers' => $filteredBookings,
                'from'      => $from,
                'to'        => $to,
                'status'    => $status,
            ]);

            $rawPdf = $pdf->output();
            $fileName = "Customer_Report_{$from}_to_{$to}.pdf";

            $customerLabel = $selectedCustomerName ?: 'Customer';
            $caption = "Dear {$customerLabel},\n\nPlease find attached your Customer Booking Statement for the period {$from} to {$to}.\n\nThank you for choosing Crown Carz.";

            $result = $wa->sendPdfBinary(
                phone: (string) $phone,
                rawPdfContent: $rawPdf,
                fileName: $fileName,
                caption: $caption
            );

            return response()->json($result, ($result['success'] ?? false) ? 200 : 502);
        } catch (\Throwable $e) {
            \Log::error('Customer Report WhatsApp Send Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to generate/send customer report: ' . $e->getMessage()], 500);
        }
    }
}
