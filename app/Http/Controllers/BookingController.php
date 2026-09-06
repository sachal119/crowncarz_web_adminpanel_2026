<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use App\Models\FixedPrice;
use App\Models\MileagePrice;
use App\Models\PricingPercentage;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Mail\BookingReceiptMail;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Log;
use Illuminate\Support\Facades\Http;
use App\Mail\BookingStatusChanged;
use App\Mail\BookingCompletedMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Database;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Services\MySmsService;

class BookingController extends Controller
{

protected $firebase;



    public function __construct(FirebaseService $firebase)
{
    $this->firebase = $firebase;
    $this->database = $firebase->getDatabase();
    $this->firebaseMessaging = $firebase->getMessaging();
}
    
    public function previous(Request $request)
{
    $phone = $request->phone;
    $email = $request->email;

    $bookings = $this->firebase->getData('bookings') ?? [];
    $results = [];

    foreach ($bookings as $id => $b) {

        $matchPhone = $phone && ($b['phone_no'] ?? '') === $phone;
        $matchEmail = $email && ($b['email'] ?? '') === $email;

        if ($matchPhone || $matchEmail) {
            $results[] = [
                'id'             => $id,
                'ref_no'         => $b['ref_no'] ?? '',
                'passenger_name'  => $b['passenger_name'] ?? '',
                'phone_no'        => $b['phone_no'] ?? '',
                'email'           => $b['email'] ?? '',
                'pickup_date'    => $b['pickup_date'] ?? '',
                'pickup_time'    => $b['pickup_time'] ?? '',
                'pickup_address' => $b['pickup_address'] ?? '',
                'dropoff_address'=> $b['dropoff_address'] ?? '',
                'vehicle'        => $b['vehicle_make'] ?? '',
                'vehicle_make'    => $b['vehicle_make'] ?? '',
                'price'          => $b['price'] ?? 0,
                'payment_type'   => $b['payment_type'] ?? '',
                'status'         => ucfirst($b['status'] ?? ''),
            ];
        }
    }

    return response()->json($results);
}

public function cancelledBookings()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    
    


    try {
        $database = app('App\Services\FirebaseService')->getDatabase();
        $bookingsRef = $database->getReference('bookings')->getValue();
        $driversRef  = $database->getReference('drivers')->getValue() ?? [];
        $accountsRef = $database->getReference('customers')->getValue() ?? [];

        $cancelledBookings = collect();
        
        

        // Driver map
        $driversMap = [];
        foreach ($driversRef as $driverId => $driver) {
            $driversMap[$driverId] = $driver['name'] ?? 'Unknown Driver';
        }

        if ($bookingsRef) {
            foreach ($bookingsRef as $key => $booking) {

                // only cancelled bookings
                if (isset($booking['status']) && strtolower($booking['status']) !== 'job_cancelled') {
                    continue;
                }
                
    //             echo "here";
    // die;

                // attach id & driver name
                $booking['id'] = $key;
                //$booking['driver_name'] = $driversMap[$booking['driver_id']] ?? 'Not Assigned';
                
                
                // Attach driver name
        $booking['driver_name'] = isset($booking['driver_id'], $driversMap[$booking['driver_id']])
            ? $driversMap[$booking['driver_id']]
            : 'Not Assigned';

                $cancelledBookings->push($booking);
            }
        }
        
        

        $cancelledBookings = $cancelledBookings
            ->sortByDesc(fn($b) => isset($b['pickup_time']) ? \Carbon\Carbon::parse($b['pickup_time']) : now())
            ->values();
            
        

        return view('bookings.cancelled', [
            'cancelledBookings' => $cancelledBookings,
            'drivers' => collect($driversRef)->map(fn($v, $k) => ['id' => $k] + $v),
'accounts' => collect($accountsRef)->map(fn($v, $k) => (object)(['id' => $k] + $v)),

        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to load cancelled bookings: ' . $e->getMessage());
    }
}

public function searchCancelledBookings(Request $request)
{
    $bookingsRef = $this->database->getReference('bookings');
    $bookingsData = $bookingsRef->getValue();
    
    $drivers = $this->database->getReference('drivers')->getValue() ?? [];    

        $driversMap = collect($drivers)->mapWithKeys(function ($driver, $id) {
        return [$id => $driver['name'] ?? 'Unknown'];
    });

    $cancelledBookings = collect();

    if ($bookingsData) {
        foreach ($bookingsData as $key => $booking) {
            $driverId = $booking['driver_id'] ?? null;
$booking['driver_name'] = $driverId && isset($driversMap[$driverId])
    ? $driversMap[$driverId]
    : 'Not Assigned';
            
            
            if (isset($booking['status']) && strtolower($booking['status']) === 'job_cancelled') {
                $booking['id'] = $key;
                $cancelledBookings->push($booking);
            }
        }
    }

    $filtered = $cancelledBookings->filter(function ($booking) use ($request) {
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            if (
                !Str::contains(strtolower($booking['passenger_name'] ?? ''), $search) &&
                !Str::contains(strtolower($booking['ref_no'] ?? ''), $search) &&
                !Str::contains(strtolower($booking['phone_no'] ?? ''), $search)
            ) {
                return false;
            }
        }

        if ($request->filled('pickup') &&
            !Str::contains(strtolower($booking['pickup_address'] ?? ''), strtolower($request->pickup))) {
            return false;
        }

        if ($request->filled('dropoff') &&
            !Str::contains(strtolower($booking['dropoff_address'] ?? ''), strtolower($request->dropoff))) {
            return false;
        }

        if ($request->filled('from_date')) {
            $pickupTime = isset($booking['pickup_time']) ? Carbon::parse($booking['pickup_time']) : null;
            if (!$pickupTime || $pickupTime->lt(Carbon::parse($request->from_date))) {
                return false;
            }
        }

        if ($request->filled('to_date')) {
            $pickupTime = isset($booking['pickup_time']) ? Carbon::parse($booking['pickup_time']) : null;
            if (!$pickupTime || $pickupTime->gt(Carbon::parse($request->to_date)->endOfDay())) {
                return false;
            }
        }

        if ($request->filled('driver_id') &&
            ($booking['driver_id'] ?? '') != $request->driver_id) {
            return false;
        }

        if ($request->filled('account_id') &&
            ($booking['account_id'] ?? '') != $request->account_id) {
            return false;
        }

        if ($request->filled('payment_type') &&
            strtolower($booking['payment_type'] ?? '') != strtolower($request->payment_type)) {
            return false;
        }

        return true;
    });

    $cancelledBookings = $filtered
        ->sortByDesc(fn($b) => isset($b['pickup_time']) ? Carbon::parse($b['pickup_time'])->timestamp : now()->timestamp)
        ->values();

    $drivers = $this->database->getReference('drivers')->getValue() ?? [];
    $accounts = $this->database->getReference('customers')->getValue() ?? [];

    return view('bookings.cancelled', [
        'cancelledBookings' => $cancelledBookings,
        'drivers' => collect($drivers)->map(fn($v, $k) => ['id' => $k] + $v),
        'accounts' => collect($accounts)->map(fn($v, $k) => (object)(['id' => $k] + $v)),
    ]);
}

public function searchPreviousBookings(Request $request)
{
    $bookingsData = $this->database
        ->getReference('bookings')
        ->getValue() ?? [];

    $drivers = $this->database->getReference('drivers')->getValue() ?? [];

    $driversMap = collect($drivers)->mapWithKeys(function ($driver, $id) {
        return [$id => $driver['name'] ?? 'Unknown'];
    });

    $previousBookings = collect();

    foreach ($bookingsData as $key => $booking) {

        $pickupRaw = $booking['pickup_datetime']
            ?? $booking['pickup_time']
            ?? null;

        if (!$pickupRaw) {
            continue;
        }

        try {
            $pickupCarbon = Carbon::parse($pickupRaw);
        } catch (\Throwable $e) {
            continue;
        }

        // Only past bookings
        if (!$pickupCarbon->isPast()) {
            continue;
        }

        $booking['id'] = $key;
        $booking['pickup_time'] = $pickupRaw;

        $driverId = $booking['driver_id'] ?? null;
        $booking['driver_name'] = $driverId && isset($driversMap[$driverId])
            ? $driversMap[$driverId]
            : 'Not Assigned';

        $previousBookings->push($booking);
    }

    // 🔎 FILTER SECTION (FIXED)
    $filtered = $previousBookings->filter(function ($booking) use ($request) {

        // General Search
        if ($request->filled('search')) {

            $search = strtolower(trim($request->search));

            $match =
                Str::contains(strtolower($booking['passenger_name'] ?? ''), $search) ||
                Str::contains(strtolower($booking['ref_no'] ?? ''), $search) ||
                Str::contains(strtolower($booking['phone_no'] ?? ''), $search);

            if (!$match) {
                return false;
            }
        }

        // Pickup filter
        if ($request->filled('pickup')) {
            if (!Str::contains(
                strtolower($booking['pickup_address'] ?? ''),
                strtolower($request->pickup)
            )) {
                return false;
            }
        }

        // Dropoff filter
        if ($request->filled('dropoff')) {
            if (!Str::contains(
                strtolower($booking['dropoff_address'] ?? ''),
                strtolower($request->dropoff)
            )) {
                return false;
            }
        }

        // From Date
        if ($request->filled('from_date')) {
            try {
                $pickupTime = Carbon::parse($booking['pickup_time']);
                $fromDate = Carbon::parse($request->from_date)->startOfDay();

                if ($pickupTime->lt($fromDate)) {
                    return false;
                }
            } catch (\Throwable $e) {
                return false;
            }
        }

        // To Date
        if ($request->filled('to_date')) {
            try {
                $pickupTime = Carbon::parse($booking['pickup_time']);
                $toDate = Carbon::parse($request->to_date)->endOfDay();

                if ($pickupTime->gt($toDate)) {
                    return false;
                }
            } catch (\Throwable $e) {
                return false;
            }
        }
        
        
        if ($request->filled('driver_id') &&
            ($booking['driver_id'] ?? '') != $request->driver_id) {
            return false;
        }

        if ($request->filled('account_id') &&
            ($booking['account_id'] ?? '') != $request->account_id) {
            return false;
        }

        if ($request->filled('payment_type') &&
            strtolower($booking['payment_type'] ?? '') != strtolower($request->payment_type)) {
            return false;
        }

        return true;
    });

    $previousBookings = $filtered
        ->sortByDesc(function ($b) {
            try {
                return Carbon::parse($b['pickup_time'])->timestamp;
            } catch (\Throwable $e) {
                return 0;
            }
        })
        ->values();

    $accounts = $this->database->getReference('accounts')->getValue() ?? [];

    return view('bookings.previous', [
        'previousBookings' => $previousBookings,
        'drivers' => collect($drivers)->map(fn($v, $k) => ['id' => $k] + $v),
        'accounts' => collect($accounts)->map(fn($v, $k) => (object)(['id' => $k] + $v)),
    ]);
}


// public function searchPreviousBookings(Request $request)
// {
//         // Fetch all bookings from Firebase
//         $bookingsRef = $this->database->getReference('bookings');
//         $bookingsData = $bookingsRef->getValue();

//         $previousBookings = collect();

//         // Filter completed bookings
//         // if ($bookingsData) {
//         //     foreach ($bookingsData as $key => $booking) {
//         //         if (isset($booking['status']) && strtolower($booking['status']) === 'completed') {
//         //             $booking['id'] = $key; // Keep Firebase key
//         //             $previousBookings->push($booking);
//         //         }
//         //     }
//         // }

//         // Convert to collection for easier filtering
//         $filtered = $previousBookings->filter(function ($booking) use ($request) {
//             // General search (by passenger_name, ref_no, phone)
//             if ($request->filled('search')) {
//                 $search = strtolower($request->search);
//                 if (
//                     !Str::contains(strtolower($booking['passenger_name'] ?? ''), $search) &&
//                     !Str::contains(strtolower($booking['ref_no'] ?? ''), $search) &&
//                     !Str::contains(strtolower($booking['phone_no'] ?? ''), $search)
//                 ) {
//                     return false;
//                 }
//             }
            
            

//             // Pickup address
//             if ($request->filled('pickup') &&
//                 !Str::contains(strtolower($booking['pickup_address'] ?? ''), strtolower($request->pickup))) {
//                 return false;
//             }

//             // Dropoff address
//             if ($request->filled('dropoff') &&
//                 !Str::contains(strtolower($booking['dropoff_address'] ?? ''), strtolower($request->dropoff))) {
//                 return false;
//             }

//             // From date
//             if ($request->filled('from_date')) {
//                 $pickupTime = isset($booking['pickup_time'])
//                     ? Carbon::parse($booking['pickup_time'])
//                     : null;
//                 if (!$pickupTime || $pickupTime->lt(Carbon::parse($request->from_date))) {
//                     return false;
//                 }
//             }

//             // To date
//             if ($request->filled('to_date')) {
//                 $pickupTime = isset($booking['pickup_time'])
//                     ? Carbon::parse($booking['pickup_time'])
//                     : null;
//                 if (!$pickupTime || $pickupTime->gt(Carbon::parse($request->to_date)->endOfDay())) {
//                     return false;
//                 }
//             }

//             // Driver ID
//             // if ($request->filled('driver_id') &&
//             //     ($booking['driver_id'] ?? '') != $request->driver_id) {
//             //     return false;
//             // }

//             // // Account ID
//             // if ($request->filled('account_id') &&
//             //     ($booking['account_id'] ?? '') != $request->account_id) {
//             //     return false;
//             // }

//             // // Payment type
//             // if ($request->filled('payment_type') &&
//             //     strtolower($booking['payment_type'] ?? '') != strtolower($request->payment_type)) {
//             //     return false;
//             // }

//             return true;
//         });

//         // Sort by pickup_time (latest first)
//         $previousBookings = $filtered
//     ->filter(function($b) {
//         return isset($b['pickup_time']) && \Carbon\Carbon::parse($b['pickup_time'])->isPast();
//     })
//     ->sortByDesc(function ($b) {
//         return \Carbon\Carbon::parse($b['pickup_time'])->timestamp;
//     })
//     ->values();
    
//     // echo $previousBookings;
//     // die;

//         // Fetch driver and account data (if needed for dropdowns)
//         $drivers = $this->database->getReference('drivers')->getValue() ?? [];
//         $accounts = $this->database->getReference('accounts')->getValue() ?? [];

//         return view('bookings.previous', [
//             'previousBookings' => $previousBookings,
//             'drivers' => collect($drivers)->map(fn($v, $k) => ['id' => $k] + $v),
//             'accounts' => collect($accounts)->map(fn($v, $k) => (object) (['id' => $k] + $v)),
//         ]);
//     }




    
    public function searchCustomer(Request $request)
{
    $q = mb_strtolower(trim($request->q));
    if (!$q) return response()->json([]);

    $customers = $this->firebase->getData('customers') ?? [];
    $results = [];

    foreach ($customers as $key => $c) {
        $name  = mb_strtolower($c['business_name'] ?? '');
        $phone = mb_strtolower($c['phone'] ?? '');
        $email = mb_strtolower($c['email'] ?? '');

        if (
            str_contains($name, $q) ||
            str_contains($phone, $q) ||
            str_contains($email, $q)
        ) {
            $results[] = [
                'id'            => $key,
                'business_name' => $c['business_name'] ?? '',
                'phone'         => $c['phone'] ?? '',
                'email'         => $c['email'] ?? '',
            ];
        }

        if (count($results) >= 5) break; // limit results
    }

    return response()->json($results);
}

    


public function downloadReceipt(Request $request,$id)
{
    
    
    $booking = $this->firebase->getData("bookings/$id");
    
    
    

    if (!$booking) {
        return response()->json(['success' => false, 'message' => 'Booking not found.']);
    }
    
   
   
    // $booking = $this->firebase->getData("bookings/{$id}");
    
    

    // if (!$booking) {
    //     return abort(404, "Booking not found");
    // }

    $booking['id'] = $id;
    
    
    // echo $id;
    // print_r($booking['id']);
    // die;
    
    try {
    $pdf = Pdf::setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'Helvetica',
                    'isRemoteEnabled' => true
                ])
                ->loadView('receipts.booking', compact('booking'));
} catch (\Exception $e) {
    dd($e->getMessage());
}

    // $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions([
    //                 'dpi' => 150,
    //                 'defaultFont' => 'Helvetica',
    //                 'isRemoteEnabled' => true
    //             ])
    //             ->loadView('receipts.booking', compact('booking'))
    //             ->setPaper('A4');
                
    //   dd($pdf->output());
         
    // // print_r($pdf);
    // // die;

     return $pdf->download("Receipt-{$booking['ref_no']}.pdf");
}

    
    
//     public function updateStatusManual(Request $request, $id)
// {
//     try {
//         $request->validate([
//             'status' => 'required|in:pending,accepted,declined,onroute,arrived,pickedup,completed,job_cancelled,no_show'
//         ]);

//         // Fetch booking
//         $bookingRef = $this->database->getReference("bookings/{$id}");
//         $bookingSnap = $bookingRef->getSnapshot();

//         if (!$bookingSnap->exists()) {
//             return back()->with('error', 'Booking not found.');
//         }

//         // Update status directly (manual override)
//         $bookingRef->update([
//             'status' => $request->status,
//             'updated_at' => now()->toDateTimeString(),
//             'manual_status_change' => true
//         ]);

//         return back()->with('success', 'Booking status updated manually.');

//     } catch (\Exception $e) {
//         return back()->with('error', $e->getMessage());
//     }
// }



// public function updateStatusManual(Request $request, $id)
// {
//     try {
//         $request->validate([
//             'status' => 'required|in:pending,accepted,declined,onroute,arrived,pickedup,completed,job_cancelled,no_show'
//         ]);
        
        

//         // Fetch booking
//         $bookingRef = $this->database->getReference("bookings/{$id}");
//         $bookingSnap = $bookingRef->getSnapshot();

//         if (!$bookingSnap->exists()) {
//             return back()->with('error', 'Booking not found.');
//         }

//         $booking = $bookingSnap->getValue();

//         // Update status directly
//         $bookingRef->update([
//             'status' => $request->status,
//             'updated_at' => now()->toDateTimeString(),
//             // 'manual_status_change' => true
//         ]);
        
//       $oldStatus = $booking['status'] ?? null;
//         $newStatus = $request->status;
// /*
//         |--------------------------------------------------------------------------
//         | STATUS CHANGED EMAIL (except completed)
//         |--------------------------------------------------------------------------
//         */
//         // if ($newStatus !== 'completed' && !empty($booking['email'])) {
//         //     $booking['id'] = $id; // add this
//         //     try {
//         //         Mail::to($booking['email'])
//         //             ->send(new BookingStatusChanged($booking, $oldStatus, $newStatus));

//         //         \Log::info('Status change email sent', ['booking_id' => $id]);
//         //     } catch (\Exception $e) {
//         //         \Log::error('Booking Status Mail Error', [
//         //             'booking_id' => $id,
//         //             'error' => $e->getMessage()
//         //         ]);
//         //     }
//         // } 


//         // ✅ Send email if status is completed
//         // ✅ Actions for completed bookings 
//         // if ($request->status === 'completed') { 
//         //     // Send email 
//         //     if (!empty($booking['email'])) { try { $booking['id'] = $id; // Ensure ID is present for email 
//         //     Mail::to($booking['email'])->send(new BookingCompletedMail($booking)); } catch (\Exception $e) { \Log::error('Booking Completed Mail Error: ' . $e->getMessage()); } } 
//         //     // Send SMS 
//         //     $userPhone = $booking['phone_no'] ?? null; 
//         //     $bookingRefNo = $booking['ref_no'] ?? 
//         //     $id; if ($userPhone) { 
//         //         $cleanPhone = preg_replace('/\D/', '', $userPhone); 
//         //         if (str_starts_with($cleanPhone, '0')) 
//         //         { 
//         //             $cleanPhone = '+44' . substr($cleanPhone, 1);
//         //             } 
//         //             elseif (str_starts_with($cleanPhone, '44'))
//         //             { 
//         //                 $cleanPhone = '+' . $cleanPhone;
//         //                 } 
//         //                 elseif (!str_starts_with($cleanPhone, '+'))
//         //                 {
//         //                     $cleanPhone = '+' . $cleanPhone;
//         //                     } 
//         //                     $messageText = "Booking REF: {$bookingRefNo} with CrownCarz has been completed. Thank you for choosing us!"; 
//         //                     try {
//         //                         $response = Http::post(rtrim(config('services.admin.url'), '/') . '/sms/send', [ 
//         //                             'mobile' => $cleanPhone,
//         //                             'message' => $messageText
//         //                             ]);
//         //                             $smsResponse = $response->json();
//         //                             } 
//         //                             catch (\Exception $e) {
//         //                                 \Log::error('SMS send error: ' . $e->getMessage()); 
                                        
                                    
                                        
//         //                             }
                                    
                
//         //     } 
            
//         // }
//         if ($request->status === 'completed') {

//     // Build receipt link
//     $receiptLink = route('receipt.download', $id);

//     /*
//     |--------------------------------------------------------------------------
//     | SEND EMAIL
//     |--------------------------------------------------------------------------
//     */
//     if (!empty($booking['email'])) {
//         try {
//             $booking['id'] = $id;
//             $booking['receipt_link'] = $receiptLink;   // pass link into email

//             Mail::to($booking['email'])->send(new BookingCompletedMail($booking));
//         } catch (\Exception $e) {
//             \Log::error('Booking Completed Mail Error: ' . $e->getMessage());
//         }
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | SEND SMS
//     |--------------------------------------------------------------------------
//     */
//     $userPhone = $booking['phone_no'] ?? null;
//     $bookingRefNo = $booking['ref_no'] ?? $id;

//     if ($userPhone) {
//         $cleanPhone = preg_replace('/\D/', '', $userPhone);

//         // Convert UK formats
//         if (str_starts_with($cleanPhone, '0')) {
//             $cleanPhone = '+44' . substr($cleanPhone, 1);
//         } elseif (str_starts_with($cleanPhone, '44')) {
//             $cleanPhone = '+' . $cleanPhone;
//         } elseif (!str_starts_with($cleanPhone, '+')) {
//             $cleanPhone = '+' . $cleanPhone;
//         }

//         // SMS text including receipt link
//         $messageText =
//             "Booking REF: {$bookingRefNo} with CrownCarz is completed.\n" .
//             "Download Receipt: {$receiptLink}\n" .
//             "Thank you for choosing us!";

//         try {
//             $response = Http::post(
//                 rtrim(config('services.admin.url'), '/') . '/sms/send',
//                 [
//                     'mobile' => $cleanPhone,
//                     'message' => $messageText
//                 ]
//             );

//             $smsResponse = $response->json();
//         } catch (\Exception $e) {
//             \Log::error('SMS send error: ' . $e->getMessage());
//         }
//     }
// }


//         return back()->with('success', 'Booking status updated manually.');

//     } catch (\Exception $e) {
//         return back()->with('error', $e->getMessage());
//     }
// }
public function updateStatusManual(Request $request, $id)
{
    try {

        $request->validate([
            'status' => 'required|in:pending,accepted,declined,onroute,arrived,pickedup,completed,job_cancelled,no_show'
        ]);

        /*
        |--------------------------------------------------------------------------
        | FETCH BOOKING
        |--------------------------------------------------------------------------
        */

        $bookingRef = $this->database->getReference("bookings/{$id}");
        $bookingSnap = $bookingRef->getSnapshot();

        if (!$bookingSnap->exists()) {
            return back()->with('error', 'Booking not found.');
        }

        $booking = $bookingSnap->getValue();
        $newStatus = $request->status;

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $bookingRef->update([
            'status' => $newStatus,
            'updated_at' => now()->toDateTimeString(),
        ]);

        $bookingRefNo = $booking['ref_no'] ?? $id;

        
        $receiptLink  = route('receipt.download', $id);

        /*
        |--------------------------------------------------------------------------
        | FETCH DRIVER
        |--------------------------------------------------------------------------
        */

        $driverName  = "Driver";
        $driverPhone = null;
        $driverId = $booking['driver_id'] ?? null;

        if ($driverId) {

            $drivers = $this->database->getReference('drivers')->getValue();

            foreach ($drivers as $dKey => $driver) {

                if ((string)$dKey === (string)$driverId || ($driver['id'] ?? null) == $driverId) {

                    $driverName = $driver['name'] ?? $driver['full_name'] ?? 'Driver';
                    $driverPhone = $driver['phone'] ?? $driver['mobile'] ?? null;
                    $driverNId = $driver['id'] ?? null;

                    break;
                }
            }
        }
        $trackingLink = rtrim(config('services.frontend.url'), '/') . "/driver-location/{$driverNId}";
        /*
        |--------------------------------------------------------------------------
        | FETCH VEHICLE
        |--------------------------------------------------------------------------
        */

        $vehicleModel = '';
        $vehiclePlate = '';

        $vehicles = $this->database->getReference('vehicles')->getValue();

        foreach ($vehicles as $vehicle) {

            if (($vehicle['driver_id'] ?? null) == $driverNId) {

                $vehicleModel = $vehicle['model'] ?? '';
                $vehiclePlate = $vehicle['registration'] ?? '';

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BOOKING DETAILS
        |--------------------------------------------------------------------------
        */

        $customerName = $booking['passenger_name'] ?? 'Passenger';
        $customerPhone = $booking['phone_no'] ?? null;
        $pickup = $booking['pickup_address'] ?? '';
        $dropoff = $booking['dropoff_address'] ?? '';
        // $pickupTime = $booking['pickup_time'] ?? '';

$pickupTime = !empty($booking['pickup_time'])
    ? Carbon::parse($booking['pickup_time'])->format('d-M-Y h:i:s a')
    : '';

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER SMS
        |--------------------------------------------------------------------------
        */

//         $customerMessages = [

//             'accepted' =>
// "CrownCarz: Your booking (REF: {$bookingRefNo}) is confirmed.

// Driver: {$driverName}
// Vehicle: {$vehicleModel}
// Plate: {$vehiclePlate}

// Pickup Time: {$pickupTime}",

//             'onroute' =>
// "CrownCarz: {$driverName} is on the way.

// Vehicle: {$vehicleModel}
// Plate: {$vehiclePlate}

// Track Driver:
// {$trackingLink}",

//             'arrived' =>
// "CrownCarz: {$driverName} has arrived.

// Vehicle: {$vehicleModel}
// Plate: {$vehiclePlate}

// Please proceed to the pickup point.",

//             'pickedup' =>
// "CrownCarz: Your journey has started.

// Driver: {$driverName}
// Vehicle: {$vehicleModel}

// Enjoy your ride!",

//             'completed' =>
// "CrownCarz: Your booking (REF: {$bookingRefNo}) has been completed.

// Download receipt:
// {$receiptLink}

// Thank you for choosing CrownCarz.",

//             'job_cancelled' =>
// "CrownCarz: Your booking (REF: {$bookingRefNo}) has been cancelled."
//         ];
$customerMessages = [

// 'accepted' =>
// "CrownCarz: Your booking (REF: {$bookingRefNo}) is confirmed.

// Driver: {$driverName}
// Vehicle: {$vehicleModel}
// Plate: {$vehiclePlate}

// Pickup Time: {$pickupTime}",

'onroute' =>
"Dear {$customerName},

Your driver {$driverName}, driving a {$vehicleModel} vehicle with Registration number: {$vehiclePlate} will be coming to collect you soon.

Track the vehicle by tapping the link below:
{$trackingLink}

Kind Regards,
CrownCarz Ltd.

Tel: +44(0)1189 47 47 47
Email: CrownCarz Support",

'arrived' =>
"Dear {$customerName}, This is your driver. Just to let you know that I am at {$pickup}.

For all future bookings please call on:
+44(0)1189 47 47 47
Email: CrownCarz Support",

// 'pickedup' =>
// "CrownCarz: Your journey has started.

// Driver: {$driverName}
// Vehicle: {$vehicleModel}

// Enjoy your ride!",

'completed' =>
"Dear {$customerName},

Thank you for giving us the opportunity to serve you this time, looking forward to have you on board soon.


To download our app, Leave a review or visit the website, please tap the link below:

CrownCarz

Kind Regards,
CrownCarz LTD

Tel: +44(0)1189 47 47 47
Email: CrownCarz Support",

// 'job_cancelled' =>
// "CrownCarz: Your booking (REF: {$bookingRefNo}) has been cancelled.",


/*
|--------------------------------------------------------------------------
| NEW STATUS
|--------------------------------------------------------------------------
*/

// 'declined' =>
// "CrownCarz: Unfortunately your booking (REF: {$bookingRefNo}) could not be accepted at this time.

// Please contact support or try booking again.

// Tel: +44 1189 47 47 47",

// 'no_show' =>
// "CrownCarz: Driver arrived at pickup location but passenger was not found.

// Booking REF: {$bookingRefNo} marked as No Show.

// If this is an error please contact support.

// Tel: +44 1189 47 47 47"

];

// To download receipt, please tap the link below:
// Download receipt:
// {$receiptLink}
        /*
        |--------------------------------------------------------------------------
        | DRIVER SMS
        |--------------------------------------------------------------------------
        */

//         $driverMessages = [

//             'accepted' =>
// "New Job Assigned – CrownCarz

// Booking REF: {$bookingRefNo}
// Passenger: {$customerName}
// Pickup: {$pickup}
// Dropoff: {$dropoff}
// Time: {$pickupTime}
// Passenger Phone: {$customerPhone}",

//             'onroute' =>
// "CrownCarz Dispatch

// Proceed to pickup location.

// Booking REF: {$bookingRefNo}
// Pickup: {$pickup}
// Dropoff: {$dropoff}",

//             'arrived' =>
// "CrownCarz Dispatch

// You have arrived.

// Passenger: {$customerName}
// Booking REF: {$bookingRefNo}",

//             'pickedup' =>
// "CrownCarz Dispatch

// Journey started.

// Destination:
// {$dropoff}",

//             'completed' =>
// "CrownCarz Dispatch

// Booking REF: {$bookingRefNo} completed.

// Thank you for completing the trip.",

//             'job_cancelled' =>
// "CrownCarz Dispatch

// Booking REF: {$bookingRefNo} has been cancelled."
//         ];

$driverMessages = [

// 'accepted' =>
// "New Job Assigned – CrownCarz

// Booking REF: {$bookingRefNo}
// Passenger: {$customerName}
// Pickup: {$pickup}
// Dropoff: {$dropoff}
// Time: {$pickupTime}
// Passenger Phone: {$customerPhone}",

'onroute' =>
"CrownCarz Dispatch

Proceed to pickup location.

Booking REF: {$bookingRefNo}
Pickup: {$pickup}
Dropoff: {$dropoff}",

'arrived' =>
"CrownCarz Dispatch

You have arrived.

Passenger: {$customerName}
Booking REF: {$bookingRefNo}",

// 'pickedup' =>
// "CrownCarz Dispatch

// Journey started.

// Destination:
// {$dropoff}",

'completed' =>
"CrownCarz Dispatch

Booking REF: {$bookingRefNo} completed.

Thank you for completing the trip.",

// 'job_cancelled' =>
// "CrownCarz Dispatch

// Booking REF: {$bookingRefNo} has been cancelled.",


/*
|--------------------------------------------------------------------------
| NEW STATUS
|--------------------------------------------------------------------------
*/

// 'declined' =>
// "CrownCarz Dispatch

// Booking REF: {$bookingRefNo}

// You have declined this job.
// Dispatch will reassign another driver.",

// 'no_show' =>
// "CrownCarz Dispatch

// Passenger did not arrive.

// Booking REF: {$bookingRefNo} marked as No Show."
];

        $customerMessage = $customerMessages[$newStatus] ?? null;
        $driverMessage   = $driverMessages[$newStatus] ?? null;
        
        
        $authToken = config('services.mysms.auth_token');

        /*
        |--------------------------------------------------------------------------
        | SEND CUSTOMER SMS
        |--------------------------------------------------------------------------
        */

        if ($customerPhone && $customerMessage) {

    try {
        
        $cleanPhone = preg_replace('/\D/', '', $customerPhone);

        // // Convert UK formats
        // if (str_starts_with($cleanPhone, '0')) {
        //     $cleanPhone = '+44' . substr($cleanPhone, 1);
        // } elseif (str_starts_with($cleanPhone, '44')) {
        //     $cleanPhone = '+' . $cleanPhone;
        // } elseif (!str_starts_with($cleanPhone, '+')) {
        //     $cleanPhone = '+' . $cleanPhone;
        // }

        //$formattedPhone = $this->formatPhone($customerPhone);
        
        
    

    $response = Http::post('https://api.mysms.com/json/remote/sms/send', [
        'apiKey'=> config('services.mysms.api_key'),
        'authToken' => $authToken,
        'recipients' => [$cleanPhone],
        'message' => $customerMessage,
        'store' => true
    ]);

    $data = $response->json();

        // $response = Http::post(rtrim(config('services.admin.url'), '/') . '/sms/send', [
        //     'mobile' => $cleanPhone,
        //     'message' => $customerMessage
        // ]);

        \Log::info('Customer SMS sent', [
    'phone' => $cleanPhone,
    'status' => $response->status(),
    'body' => $data,
]);

    } catch (\Exception $e) {

        \Log::error('Customer SMS error', [
            'booking_id' => $id,
            'phone' => $customerPhone,
            'error' => $e->getMessage()
        ]);
    }
}

        /*
        |--------------------------------------------------------------------------
        | SEND DRIVER SMS
        |--------------------------------------------------------------------------
        */

        if ($driverPhone && $driverMessage) {

    try {
        
        $cleanPhone = preg_replace('/\D/', '', $driverPhone);

        // Convert UK formats
        // if (str_starts_with($cleanPhone, '0')) {
        //     $cleanPhone = '+44' . substr($cleanPhone, 1);
        // } elseif (str_starts_with($cleanPhone, '44')) {
        //     $cleanPhone = '+' . $cleanPhone;
        // } elseif (!str_starts_with($cleanPhone, '+')) {
        //     $cleanPhone = '+' . $cleanPhone;
        // }

        //$formattedPhone = $this->formatPhone($driverPhone);
        
        $response = Http::post('https://api.mysms.com/json/remote/sms/send', [
        'apiKey'=> config('services.mysms.api_key'),
        'authToken' => $authToken,
        'recipients' => [$cleanPhone],
        'message' => $driverMessage,
        'store' => true
    ]);

    $data = $response->json();

        // $response = Http::post(rtrim(config('services.admin.url'), '/') . '/sms/send', [
        //     'mobile' => $cleanPhone,
        //     'message' => $driverMessage
        // ]);

        \Log::info('Driver SMS sent', [
    'phone' => $cleanPhone,
    'status' => $response->status(),
    'body' => $data,
]);

    } catch (\Exception $e) {

        \Log::error('Driver SMS error', [
            'booking_id' => $id,
            'phone' => $driverPhone,
            'error' => $e->getMessage()
        ]);
    }
}

        /*
        |--------------------------------------------------------------------------
        | EMAIL WHEN COMPLETED
        |--------------------------------------------------------------------------
        */

        if ($newStatus === 'completed' && !empty($booking['email'])) {

            try {

                $booking['id'] = $id;
                $booking['receipt_link'] = $receiptLink;

                Mail::to($booking['email'])->send(
                    new BookingCompletedMail($booking)
                );

            } catch (\Exception $e) {

                \Log::error('Completed email error', [
                    'booking_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return back()->with('success', 'Booking status updated.');

    } catch (\Exception $e) {

        \Log::error('Status update error', [
            'booking_id' => $id,
            'error' => $e->getMessage()
        ]);

        return back()->with('error', 'Something went wrong.');
    }
}
private function formatPhone($phone)
{
    if (!$phone) return null;

    $clean = preg_replace('/\D/', '', $phone);

    if (str_starts_with($clean, '0')) {
        return '+44' . substr($clean, 1);
    }

    if (str_starts_with($clean, '44')) {
        return '+' . $clean;
    }

    if (!str_starts_with($clean, '+')) {
        return '+' . $clean;
    }

    return $clean;
}
    
    /**
     * Payment Success Callback
     */
    public function paymentSuccess(Request $request)
    {
        $bookingId = $request->query('booking_id');

        if (!$bookingId) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid booking reference.');
        }

        // --- Get payment record from Firebase ---
        $payments = $this->firebase->getData('payments') ?? [];
        $paymentKey = null;
        foreach ($payments as $key => $payment) {
            if ($payment['booking_id'] == $bookingId) {
                $paymentKey = $key;
                break;
            }
        }

        if ($paymentKey) {
            // Update payment status to 'paid'
            $this->firebase->updateData('payments/' . $paymentKey, [
                'status' => 'paid',
                'paid_at' => now()->toDateTimeString(),
            ]);
        }

        // --- Update booking status to 'confirmed' ---
        $bookings = $this->firebase->getData('bookings') ?? [];
        $bookingKey = null;
        foreach ($bookings as $key => $booking) {
            if ($key == $bookingId) {
                $bookingKey = $key;
                break;
            }
        }

        if ($bookingKey) {
            $this->firebase->updateData('bookings/' . $bookingKey, [
                'status' => 'upcoming',
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Payment successful! Your booking is confirmed.');
    }

    /**
     * Payment Cancel Callback
     */
    public function paymentCancel(Request $request)
    {
        $bookingId = $request->query('booking_id');

        if (!$bookingId) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid booking reference.');
        }

        // --- Update payment status to 'cancelled' ---
        $payments = $this->firebase->getData('payments') ?? [];
        $paymentKey = null;
        foreach ($payments as $key => $payment) {
            if ($payment['booking_id'] == $bookingId) {
                $paymentKey = $key;
                break;
            }
        }

        if ($paymentKey) {
            $this->firebase->updateData('payments/' . $paymentKey, [
                'status' => 'cancelled',
                'cancelled_at' => now()->toDateTimeString(),
            ]);
        }

        // --- Update booking status to 'pending_payment' ---
        $bookings = $this->firebase->getData('bookings') ?? [];
        $bookingKey = null;
        foreach ($bookings as $key => $booking) {
            if ($key == $bookingId) {
                $bookingKey = $key;
                break;
            }
        }

        if ($bookingKey) {
            $this->firebase->updateData('bookings/' . $bookingKey, [
                'status' => 'cancelled_payment',
            ]);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Payment was cancelled. Please try again.');
    }
    
    
// public function sendEmail(Request $request)
// {
//     $bookingId = $request->booking_id;
//     $email = $request->email;

//     // // Fetch booking details
//     // $booking = $this->firebase->getData("bookings/$bookingId");
    
//     // Log::info('Booking Data:', $booking);
    
//     // Fetch all bookings OR the node under the ID
//     $data = $this->firebase->getData("bookings");

//     if (!$data || !isset($data[$bookingId])) {
//         return back()->with([
//             'success' => false,
//             'message' => 'Booking not found.'
//         ]);
//     }

//     // Extract the correct booking entry
//     $booking = (object) $data[$bookingId];  // Convert to object so Blade works
//     \Log::info("Using Booking for Email: " . json_encode($booking));
    

//     if (!$booking) {
//         return response()->json(['success' => false, 'message' => 'Booking not found.']);
//     }
    
    
    
    
//     try {
//         // Send email logic (Laravel Mail)
//         Mail::to($email)->send(new BookingReceiptMail($booking));  // ← Uncommented
//         return redirect()->back()->with([
//             'success' => true,  // ← Add this boolean key
//             'message' => 'Email sent successfully.'
//         ]);
//     } catch (\Exception $e) {
//         \Log::error('Email failed for booking ' . $bookingId . ': ' . $e->getMessage());  // Log for debugging
//         return redirect()->back()->with([
//             'success' => false,
//             'message' => 'Failed to send email: ' . $e->getMessage()
//         ]);
//     }

//     // // // Send email logic (Laravel Mail or custom)
//     // Mail::to($email)->send(new BookingReceiptMail($booking));
    
    

//     // return response()->json(['status'=>'success', 'message'=>'Email sent successfully.']);
// }

public function sendEmail(Request $request)
{
    $bookingId = $request->booking_id;
    $email = $request->email;

    if (!$bookingId) {
        return response()->json([
            'success' => false,
            'message' => 'Missing booking ID.'
        ]);
    }
    
    // Get all bookings from Firebase
    $allBookings = $this->firebase->getData("bookings");

    if (!$allBookings || !isset($allBookings[$bookingId])) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found in database.'
        ]);
    }

    // Extract the specific booking only
    $booking = (object) $allBookings[$bookingId];
    
    // Build receipt link
    $receiptLink = route('receipt.download', $bookingId);
    $booking->receipt_link = $receiptLink;

    try {
        Mail::to($email)->send(new BookingReceiptMail($booking));

       return redirect()->back()->with([
            'success' => true,  // ← Add this boolean key
            'message' => 'Email sent successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}



//     public function index()
//     {
//         // Fetch all future bookings
//         // $bookings = Booking::with('passenger', 'driver')->where('pickup_datetime', '>=', now())->orderBy('pickup_datetime')->get();

// $bookings = Booking::with('passenger', 'driver')
//     ->where('pickup_time', '>=', now())
//     ->orderBy('pickup_time')
//     ->get();

//         // Fetch all drivers
//         $drivers = Driver::all();

//         // Fetch all accounts
//         $accounts = Account::all();

//         // Pass to dashboard view
//         return view('dashboard', compact('bookings', 'drivers', 'accounts'));
//     }


//Perfected
// public function index()
// {
//     // 1. Fetch all bookings from Firebase
//     $bookingsSnapshot = $this->firebase->getData('bookings');

//     $bookings = [];

//     if ($bookingsSnapshot) {
//         foreach ($bookingsSnapshot as $id => $booking) {
//             // Normalize fields safely
//             $status = strtolower(trim($booking['status'] ?? ''));

//             // ✅ Only include bookings with status = 'upcoming'
//             if ($status !== 'upcoming') {
//                 continue;
//             }

//             // Parse pickup time (if exists)
//             if (!empty($booking['pickup_time'])) {
//                 try {
//                     $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);
//                 } catch (\Throwable $e) {
//                     continue; // skip invalid dates
//                 }

//                 // ✅ Optional: only include future bookings (today or later)
//                 if ($pickupTime->greaterThanOrEqualTo(now())) {
//                     $booking['id'] = $id; // Keep Firebase ID
//                     $bookings[] = $booking;
//                 }
//             }
//         }

//         // Sort bookings by pickup_time (earliest first)
//         usort($bookings, function ($a, $b) {
//             return strtotime($a['pickup_time']) <=> strtotime($b['pickup_time']);
//         });
//     }

//     // 2. Fetch drivers (from MySQL)
//     $drivers = Driver::all();

//     // 3. Fetch accounts (from MySQL)
//     $accounts = Account::all();

//     // 4. Return to dashboard view
//     return view('dashboard', compact('bookings', 'drivers', 'accounts'));
// }





public function index()
{
    
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    // 1️⃣ Fetch bookings from Firebase
    $bookingsSnapshot = $this->firebase->getData('bookings');
    $bookings = [];
    $today = \Carbon\Carbon::today();

    if ($bookingsSnapshot) {
        foreach ($bookingsSnapshot as $id => $booking) {

            // Ensure hidden field exists
            //$booking['hidden'] = $booking['hidden'] ?? false;

            // Skip if pickup_time is missing or in the past
            if (empty($booking['pickup_time'])) continue;

            try {
                $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);
            } catch (\Throwable $e) {
                continue;
            }
            
            
            
            

            // Only include future pickups
            // ❌ Skip past dates (before today)
        // if ($pickupTime->lt($today)) {
        //     continue;
        // }

            // Skip completed bookings
        if (in_array($booking['status'] ?? null, ['completed', 'job_cancelled','no_show'])) {
    continue;
}


            $booking['id'] = $id;
            $booking['pickup_time_parsed'] = $pickupTime;
            
            $booking['vias'] = [];

if (!empty($booking['via_addresses']) && is_array($booking['via_addresses'])) {
    $booking['vias'] = $booking['via_addresses'];
}

            $bookings[] = $booking;
        }

        // Sort bookings by pickup time
        usort($bookings, function ($a, $b) {
            return $a['pickup_time_parsed']->timestamp <=> $b['pickup_time_parsed']->timestamp;
        });
    }

    // // 2️⃣ Fetch drivers from Firebase
    // $driversSnapshot = $this->firebase->getData('drivers');
    // $driverLocations = [];

    // if ($driversSnapshot) {
    //     foreach ($driversSnapshot as $driverId => $driver) {
    //         $lat = $driver['latitude'] ?? null;
    //         $lng = $driver['longitude'] ?? null;

    //         if ($lat && $lng) {
    //             $driverLocations[] = [
    //                 'id' => $driverId,
    //                 'name' => $driver['name'] ?? 'Unknown',
    //                 'lat' => (float)$lat,
    //                 'lng' => (float)$lng,
    //                 'status' => $driver['status'] ?? 'Unknown',
    //             ];
    //         }
    //     }
    // }
    
    // 1️⃣ Fetch drivers
$driversSnapshot = $this->firebase->getData('drivers');

// 2️⃣ Fetch driver live locations
$locationsSnapshot = $this->firebase->getData('drivers_live_locations');

$driverLocations = [];

if ($driversSnapshot) {
    foreach ($driversSnapshot as $driverId => $driver) {

        // get location against same firebase key
        $location = $locationsSnapshot[$driverId] ?? null;

        if ($location && isset($location['lat']) && isset($location['lng'])) {

            $driverLocations[] = [
                'id' => $driverId,
                'name' => $driver['name'] ?? 'Unknown',
                'lat' => (float) $location['lat'],
                'lng' => (float) $location['lng'],
                'heading' => $location['heading'] ?? 0,
                'speed' => $location['speed'] ?? 0,
                'isOnline' => $location['isOnline'] ?? false,
                'status' => $driver['status'] ?? 'Unknown'
            ];
        }
    }
}



    // 3️⃣ Fetch accounts (if needed from MySQL)
    //$accounts = Account::all();
    
        $accounts = $this->firebase->getData('customers') ?? [];

        // ✅ Pass all data to the Blade
        // return view('bookings.completed', [
        //     'completedBookings' => $completedBookings,
        //     'drivers' => collect($drivers)->map(fn($v, $k) => ['id' => $k] + $v),
        //     'accounts' => collect($accounts)->map(fn($v, $k) => (object) (['id' => $k] + $v)),
        // ]);

    // 4️⃣ Prepare drivers collection
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();
    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }
    
    
    // ───────────────────────────────────────────────
    // 5️⃣ NEW: Calculate Dashboard Metrics
    // ───────────────────────────────────────────────
    
     $users = $this->firebase->getData('users') ?? [];

    // Total Users
    $totalUsers = is_array($users) ? count($users) : 0;

    // Today's Revenue (Sum only bookings created today)
    // $todaysRevenue = 0;

    // if ($bookingsSnapshot) {
    //     foreach ($bookingsSnapshot as $booking) {
    //         if (!isset($booking['price'])) continue;

    //         if (isset($booking['created_at'])) {
    //             try {
    //                 $created = \Carbon\Carbon::parse($booking['created_at']);
    //                 if ($created->isToday()) {
    //                     $todaysRevenue += (float)$booking['price'];
    //                 }
    //             } catch (\Throwable $e) {
    //                 continue;
    //             }
    //         }
    //     }
    // }
    
    // Today's Revenue (Completed bookings with pickup_time today)
$todaysRevenue = 0;

if ($bookingsSnapshot) {
    foreach ($bookingsSnapshot as $booking) {

        if (
            !isset($booking['price']) ||
            !isset($booking['pickup_time']) ||
            ($booking['status'] ?? '') !== 'completed'
        ) {
            continue;
        }

        try {
            $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);

            if ($pickupTime->isToday()) {
                $todaysRevenue += (float) $booking['price'];
            }
        } catch (\Throwable $e) {
            continue;
        }
    }
}

    // Revenue Breakdown (Cash / Card / Account)
    // $revenueBreakdown = [
    //     'Cash' => 0,
    //     'Card' => 0,
    //     'Account' => 0,
    // ];

    // if ($bookingsSnapshot) {
    //     foreach ($bookingsSnapshot as $booking) {
    //         $amount = isset($booking['price']) ? (float)$booking['price'] : 0;
    //         $method = $booking['payment_type'] ?? '';

    //         if ($method === 'cash') {
    //             $revenueBreakdown['Cash'] += $amount;
    //         } elseif ($method === 'card') {
    //             $revenueBreakdown['Card'] += $amount;
    //         } elseif ($method === 'account') {
    //             $revenueBreakdown['Account'] += $amount;
    //         }
    //     }
    // }
    
    
    $revenueBreakdown = [
    'Cash' => 0,
    'Card' => 0,
    'Account' => 0,
];

if ($bookingsSnapshot) {
    foreach ($bookingsSnapshot as $booking) {

        if (
            !isset($booking['price']) ||
            !isset($booking['pickup_time']) ||
            ($booking['status'] ?? '') !== 'completed'
        ) {
            continue;
        }

        try {
            $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);

            if (!$pickupTime->isToday()) {
                continue;
            }

            $amount = (float) $booking['price'];
            $method = strtolower($booking['payment_type'] ?? '');

            if ($method === 'cash') {
                $revenueBreakdown['Cash'] += $amount;
            } elseif ($method === 'card') {
                $revenueBreakdown['Card'] += $amount;
            } elseif ($method === 'account') {
                $revenueBreakdown['Account'] += $amount;
            }

        } catch (\Throwable $e) {
            continue;
        }
    }
}
    
    // ================= PAGINATION =================

// Convert array to collection
$bookingsCollection = collect($bookings);

// Pagination settings
$perPage = 20; // 👈 change if you want
$currentPage = request()->get('page', 1);

// Slice data for current page
$currentPageItems = $bookingsCollection
    ->slice(($currentPage - 1) * $perPage, $perPage)
    ->values();

// Create paginator
$bookings = new LengthAwarePaginator(
    $currentPageItems,
    $bookingsCollection->count(),
    $perPage,
    $currentPage,
    [
        'path' => request()->url(),
        'query' => request()->query(),
    ]
);
    
 

    // 6️⃣ Return View With Dashboard Stats
    return view('dashboard', compact(
        'bookings',
        'driverLocations',
        'accounts',
        'drivers',
        'totalUsers',
        'todaysRevenue',
        'revenueBreakdown'
    ));

    // 5️⃣ Fetch vehicle type for each booking
    // foreach ($bookings as &$booking) {
    //     if (!empty($booking['vehicle_id'])) {
    //         $vehicleData = $this->firebase->getData("vehicles/{$booking['vehicle_id']}");
    //         $booking['vehicle_type'] = $vehicleData['make'] ?? '-';
    //     } else {
    //         $booking['vehicle_type'] = '-';
    //     }
    // }

    // 6️⃣ Return to view
    //return view('dashboard', compact('bookings', 'driverLocations', 'accounts', 'drivers'));
}





// public function index()
// {
//     // 1️⃣ Fetch bookings from Firebase
//     $bookingsSnapshot = $this->firebase->getData('bookings');
//     $bookings = [];

//     if ($bookingsSnapshot) {
//         foreach ($bookingsSnapshot as $id => $booking) {
//             $status = strtolower(trim($booking['status'] ?? ''));

//             // Include only upcoming bookings
//             if ($status !== 'upcoming') continue;

//             if (!empty($booking['pickup_time'])) {
//                 try {
//                     $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);
//                 } catch (\Throwable $e) {
//                     continue;
//                 }

//                 if ($pickupTime->greaterThanOrEqualTo(now())) {
//                     $booking['id'] = $id;
//                     $bookings[] = $booking;
//                 }
//             }
//         }

//         // Sort bookings by pickup time
//         usort($bookings, function ($a, $b) {
//             return strtotime($a['pickup_time']) <=> strtotime($b['pickup_time']);
//         });
//     }

//     // 2️⃣ Fetch drivers from Firebase
//     $driversSnapshot = $this->firebase->getData('drivers');
//     $driverLocations = [];

//     if ($driversSnapshot) {
//         foreach ($driversSnapshot as $driverId => $driver) {
//             $lat = $driver['latitude'] ?? null;
//             $lng = $driver['longitude'] ?? null;

//             if ($lat && $lng) {
//                 $driverLocations[] = [
//                     'id' => $driverId,
//                     'name' => $driver['name'] ?? 'Unknown',
//                     'lat' => (float)$lat,
//                     'lng' => (float)$lng,
//                     'status' => $driver['status'] ?? 'Unknown',
//                 ];
//             }
//         }
//     }
    
    

//     // 3️⃣ Fetch accounts (if needed from MySQL)
//     $accounts = Account::all();
//   $driversData = $this->firebase->getData('drivers') ?? [];
// $drivers = collect();

// foreach ($driversData as $id => $driver) {
//     $driver['id'] = $id;
//     $drivers->push($driver);
// }

// // 5️⃣ Fetch vehicle type for each booking
//     foreach ($bookings as &$booking) {
//         if (!empty($booking['vehicle_id'])) {
//             $vehicleData = $this->firebase->getData("vehicles/{$booking['vehicle_id']}");
//             $booking['vehicle_type'] = $vehicleData['make'] ?? '-';
//         } else {
//             $booking['vehicle_type'] = '-';
//         }
//     }



//     // 4️⃣ Return to view
//     return view('dashboard', compact('bookings', 'driverLocations', 'accounts','drivers'));
// }


// public function index()
// {
//     // 1. Fetch all bookings from Firebase
//     $bookingsSnapshot = $this->firebase->getData('bookings');

//     $bookings = [];

//     if ($bookingsSnapshot) {
//         foreach ($bookingsSnapshot as $id => $booking) {
//             // Convert pickup_time to Carbon for comparison
//             $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);

//             // Only include future bookings
//             if ($pickupTime->greaterThanOrEqualTo(now())) {
//                 $booking['id'] = $id; // Store Firebase ID
//                 $bookings[] = $booking;
//             }
//         }

//         // Sort bookings by pickup_time
//         usort($bookings, function ($a, $b) {
//             return strtotime($a['pickup_time']) <=> strtotime($b['pickup_time']);
//         });
//     }

//     // 2. Fetch drivers (from MySQL, unless you also moved them to Firebase)
//     $drivers = Driver::all();

//     // 3. Fetch accounts (from MySQL, unless moved to Firebase)
//     $accounts = Account::all();

//     // 4. Pass to dashboard view
//     return view('dashboard', compact('bookings', 'drivers', 'accounts'));
// }


    // Example placeholder methods for upcoming modules
    
    public function trackDriver($id)
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    try {
        // Get booking from Firebase
        $booking = $this->firebase->getData("bookings/{$id}");

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }
        $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

        // Pass booking data to track view
        return view('bookings.track', compact('booking', 'id','drivers'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

/**
 * 🔁 Recall Job — mark as recalled
 */
public function recallJob($id)
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    try {
        $ref = $this->firebase->updateData("bookings/{$id}", ['driver_id' => '']);
        // $drivers = collect();
        // $booking = $ref->getValue();updateData("bookings/{$id}", ['status' => 'recalled']);

        if (!$ref) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        // Update booking status
        // $ref->update(['status' => 'recalled']);

        return redirect()->back()->with('success', 'Job recalled successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

/**
 * 🧾 Receipt — view or print booking details
 */
public function receipt($id)
{
    try {
        $booking = $this->firebase->getData("bookings/{$id}");
        
        // return response()->json([
        //     'Booking' => $booking,
        //     'booking_id' => $id
        //     ]);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }
        
           $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

 $bid = $id;
 
//   return response()->json([
//             'Booking' => $booking,
//             'booking_id' => $bid
//             ]);

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

// 🔥 IMPORTANT: pass booking ID to the view
         return view('bookings.receipt', compact('booking','drivers','bid'));

        // return view('bookings.receipt', compact('booking','drivers'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}


// public function dispatchDriver(Request $request)
// {
//     try {
//         // ✅ Validate inputs
//         $validator = \Validator::make($request->all(), [
//             'booking_id' => 'required|string',
//             'driver_id' => 'required|string',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'error' => $validator->errors()->first()
//             ], 422);
//         }

//         $bookingId = $request->booking_id;
//         $driverId = $request->driver_id;

//         // ✅ Make sure the booking node exists (same as updateUser)
//         $bookingRef = $this->database->getReference("bookings/{$bookingId}");
//         $existingBooking = $bookingRef->getValue();

//         if (!$existingBooking) {
//             return response()->json(['error' => 'Booking not found'], 404);
//         }

//         // ✅ Optional: Validate driver exists (same pattern)
//         $driverRef = $this->database->getReference("drivers/{$driverId}");
//         $existingDriver = $driverRef->getValue();

//         if (!$existingDriver) {
//             return response()->json(['error' => 'Driver not found'], 404);
//         }

//         // ✅ Optional: Prevent re-assignment
//         if (isset($existingBooking['driver_id']) && !empty($existingBooking['driver_id'])) {
//             return response()->json([
//                 'error' => 'Booking is already assigned to a driver'
//             ], 409);
//         }

//         // ✅ Prepare updated data (only the fields to change)
//         $bookingData = [
//             'driver_id' => $driverId,
//             'updated_at' => now()->toISOString(), // Optional timestamp
//         ];

//         // ✅ Update existing booking (same as updateUser)
//         $bookingRef->update($bookingData);

//         return response()->json([
//             'success' => true,
//             'message' => 'Driver dispatched successfully.',
//             'data' => [
//                 'booking_id' => $bookingId,
//                 'driver_id' => $driverId,
//             ]
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => 'Something went wrong',
//             'details' => $e->getMessage()
//         ], 500);
//     }
// }


public function dispatchDriver(Request $request)
{
    try {
        // ✅ Validate inputs
        $validator = \Validator::make($request->all(), [
            'booking_id' => 'required|string',
            'driver_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $bookingId = $request->booking_id;
        $driverId = $request->driver_id;

        // ✅ Fetch booking
        $bookingRef = $this->database->getReference("bookings/{$bookingId}");
        $existingBooking = $bookingRef->getValue();
        if (!$existingBooking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // ✅ Fetch driver
        $driverRef = $this->database->getReference("drivers/{$driverId}");
        $existingDriver = $driverRef->getValue();
        if (!$existingDriver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }

        // ✅ Prevent re-assignment
        if (isset($existingBooking['driver_id']) && !empty($existingBooking['driver_id'])) {
            return response()->json([
                'error' => 'Booking is already assigned to a driver'
            ], 409);
        }

        // ✅ Update booking
        $bookingData = [
            'driver_id' => $driverId,
            'status' => 'assigned',
            'updated_at' => now()->toISOString(),
        ];
        $bookingRef->update($bookingData);
        
        // Send SMS to Driver
        if (!empty($existingDriver['phone'])) {

            $smsMessage =
                "Dear {$existingDriver['name']},\n\n" .
                "A new job has been assigned to you.\n" .
                "Ref: {$existingBooking['ref_no']}\n" .
                "Pickup: {$existingBooking['pickup_address']}\n" .
                "Dropoff: {$existingBooking['dropoff_address']}\n\n" .
                "Please check your app.";

            // Fire SMS API request
            Http::post(route('sms.login'), [
                'mobile'  => $existingDriver['phone'],
                'message' => $smsMessage
            ]);
        }

        // --- Fetch vehicle image ---
        $vehicles = [
            ['title'=>'Saloon','img'=>rtrim(config('services.frontend.url'), '/') . '/public/images/1703168146Saloon.png'],
            ['title'=>'Estate','img'=>rtrim(config('services.frontend.url'), '/') . '/public/images/1703168603_vehicles_large_5301798_336_3139_vehicle-5301798-001-20231020-062215-4d4570cb709d89abe6fd892c061e98100a36d35a219e72f070f653c0c93c2407-removebg-preview.png'],
            ['title'=>'MPV','img'=>rtrim(config('services.frontend.url'), '/') . '/public/images/1700064078sharan.png'],
            ['title'=>'8 Seater','img'=>rtrim(config('services.frontend.url'), '/') . '/public/images/17031687775bc3ohfc3rouccucg5jj1432m-removebg-preview.png'],
            ['title'=>'Executive','img'=>rtrim(config('services.frontend.url'), '/') . '/public/images/1700064019exective_s_class-removebg-preview.png']
        ];

        $vehicleImage = null;
        if(isset($existingBooking['vehicle_make'])){
            foreach($vehicles as $v){
                if(strtolower($v['title']) === strtolower($existingBooking['vehicle_make'])){
                    $vehicleImage = $v['img'];
                    break;
                }
            }
        }

        // --- Prepare booking details for email & notification ---
        $bookingDetails = [
            'ref_no' => $existingBooking['ref_no'] ?? null,
            'status' => 'assigned',
            'pickup_address' => $existingBooking['pickup_address'] ?? null,
            'dropoff_address' => $existingBooking['dropoff_address'] ?? null,
            'passenger_name' => $existingBooking['passenger_name'] ?? null,
            'driver' => [
                'name' => $existingDriver['name'] ?? null,
                'phone' => $existingDriver['phone'] ?? null
            ],
            'vehicle_image' => $vehicleImage
        ];
        
        if (!empty($existingDriver['email'])) {
    try {
        Mail::to($existingDriver['email'])
            ->send(new \App\Mail\DriverBookingAssignedMail($bookingDetails));
    } catch (\Exception $e) {
        \Log::error('Mail Error: ' . $e->getMessage());
    }
}
        
        
        
        
        $usersSnap = $this->database->getReference('users')->getSnapshot()->getValue();
$passengerKey = null;
foreach($usersSnap as $key => $user){
    if($user['name'] === $existingBooking['passenger_name']){
        $passengerKey = $key;
        break;
    }
}

if($passengerKey){
    $this->sendFirebaseNotification($passengerKey, "Driver Assigned", "A driver ({$existingDriver['name']}) has been assigned to your booking ({$existingBooking['ref_no']}).", 'user');
}


$driverSnap = $this->database->getReference('drivers')->getSnapshot()->getValue();
$passengerKey = null;
foreach($driverSnap as $key => $driver){
    if($driver['name'] === $existingDriver['name']){
        $passengerKey = $key;
        break;
    }
}

if($passengerKey){
    $this->sendFirebaseNotification($passengerKey, "New Job Assigned", "You have a new job assigned. Ref: ({$existingBooking['ref_no']})", 'driver');
}

        // --- Send Firebase notification to passenger ---
        // if(isset($existingBooking['passenger_id'])){
        //     $this->sendFirebaseNotification(
        //         $existingBooking['passenger_id'],
        //         "Driver Assigned",
        //         "A driver ({$existingDriver['name']}) has been assigned to your booking ({$existingBooking['ref_no']}).", 'user'
        //     );
        // }
        //$this->sendFirebaseNotification($passengerId, "Driver Assigned", "A driver has been assigned.", 'user');
        // if(isset($existingBooking['driver_id'])){
        //     $this->sendFirebaseNotification(
        //         $existingBooking['driver_id'], "New Booking Assigned", "You have a new job assigned. Ref: ({$existingBooking['ref_no']})", 'driver');
        // }

        // --- Send email to passenger ---
        // if(isset($existingBooking['email'])){
        //     Mail::to($existingBooking['email'])
        //         ->send(new \App\Mail\BookingStatusUpdatedMail($bookingDetails));
        // }
        
        // --- Send email to driver ---
        

        return response()->json([
            'success' => true,
            'message' => 'Driver dispatched successfully.',
            'data' => [
                'booking_id' => $bookingId,
                'driver_id' => $driverId,
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'details' => $e->getMessage()
        ], 500);
    }
}

/**
 * Send Firebase push notification to a user
 */
// protected function sendFirebaseNotification($userId, $title, $body)
// {
//     try {
//         $userSnap = $this->database->getReference("users/{$userId}")->getSnapshot();
//         if(!$userSnap->exists()) return;

//         $fcmToken = $userSnap->getValue()['fcm_token'] ?? null;
//         if(!$fcmToken) return;

//         $this->firebaseMessaging->send([
//             'token' => $fcmToken,
//             'notification' => ['title' => $title, 'body' => $body],
//             'android' => ['priority' => 'high'],
//             'apns' => ['headers' => ['apns-priority' => '10']]
//         ]);
//     } catch (\Exception $e) {
//         Log::error("Failed to send FCM notification to user {$userId}: " . $e->getMessage());
//     }
// }

//Perfected version 2026-01-26
// protected function sendFirebaseNotification($userId, $title, $body)
// {
//     try {

//         // Fetch user details
//         $userSnap = $this->database->getReference("users/{$userId}")->getSnapshot();
//         if (!$userSnap->exists()) return;

//         $userData = $userSnap->getValue();
//         $fcmToken = $userData['device_token'] ?? null;  // 🆕 Correct key

//         if (!$fcmToken) return;

//         // Prepare message
//         $message = [
//             'token' => $fcmToken,
//             'notification' => [
//                 'title' => $title,
//                 'body'  => $body,
//             ],
//             'android' => [
//                 'priority' => 'high',
//             ],
//             'apns' => [
//                 'headers' => [
//                     'apns-priority' => '10',
//                 ],
//             ],
//         ];

//         // Send notification
//         $this->firebaseMessaging->send($message);

//     } catch (\Exception $e) {
//         Log::error("Failed to send FCM notification to user {$userId}: " . $e->getMessage());
//     }
// }



protected function sendFirebaseNotification($id, $title, $body, $type)
{
    try {
        // Determine the path in Firebase based on type
        $refPath = $type === 'driver' ? "drivers/{$id}" : "users/{$id}";
        
        Log::info("FCM notification sent to {$type} {$refPath}");

        // Fetch user/driver details
        $snap = $this->database->getReference($refPath)->getSnapshot();
        if (!$snap->exists()) {
            Log::warning("No Firebase record found for {$type} {$id}");
            return;
        }

        $data = $snap->getValue();
        $fcmToken = $data['device_token'] ?? null;  // Make sure key is correct
        // $fcmToken = 'dU3eS-ErQj2ULUaaPJmUIx:APA91bExbP7VY_1mE2lhsNBsorB8ZZtv-yXIYt09h5IcmjDmlU9i_lEVPvqK_BgC6dg39QpTS0M6UOPUKwq3KChl2Mrb3Da3H_99Zqsv7m3hr4mq7eoW10U' ?? null;  // Make sure key is correct
        if (!$fcmToken) {
            Log::warning("No device token found for {$type} {$id}");
            return;
        }
        
        Log::info("FCM notification sent to {$type} {$fcmToken}");

        // Prepare notification
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification($notification)
            ->withAndroidConfig([
                'priority' => 'high',
            ])
            ->withApnsConfig([
                'headers' => [
                    'apns-priority' => '10',
                ],
            ]);

        // Send notification
        $this->firebaseMessaging->send($message);
        Log::info("FCM notification sent to {$type} {$id}");
        
    } catch (MessagingException | FirebaseException $e) {
        Log::error("Failed to send FCM notification to {$type} {$id}: " . $e->getMessage());
    } catch (\Exception $e) {
        Log::error("Unexpected error sending FCM notification to {$type} {$id}: " . $e->getMessage());
    }
}



/**
 * 📄 Duplicate Job — clone existing booking
 */
/**
 * 📄 Duplicate Job — clone existing booking (Firebase version)
 */
public function duplicateJob($id)
{
    try {
        // Fetch all bookings
        $bookings = $this->firebase->getData('bookings');

        if (!$bookings || !isset($bookings[$id])) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $booking = $bookings[$id];

        // Remove Firebase key/ID fields
        unset($booking['id']);

        // Update fields for new booking
        // $booking['ref_no'] = 'REF' . time();
        $booking['ref_no'] = $this->generateRefNo();
        $booking['status'] = 'pending';
        $booking['created_at'] = now()->toDateTimeString();

        // Save new booking back to Firebase
        // $this->firebase->insertData('bookings', $booking);
        $this->firebase->pushData("bookings/{$newKey}", $booking);
        //$this->firebase->pushData('bookings', $bookingData);

        return redirect()->back()->with('success', 'Booking duplicated successfully.');
    } catch (\Exception $e) {
        \Log::error('Duplicate Job Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong while duplicating the job.');
    }
}


/**
 * 🗺️ Route Map — show pickup/dropoff route
 */
public function routeMap($id)
{
    try {
        $booking = $this->firebase->getData("bookings/{$id}");

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }
        
           $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

        return view('bookings.route', compact('booking','drivers'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}


    // public function trackDriver($id)
    // {
    //     // Logic to track driver for booking ID
    //     return view('bookings.track', compact('id'));
    // }

    // public function recallJob($id)
    // {
    //     // Logic to recall job
    //     return redirect()->back()->with('success', 'Job recalled successfully.');
    // }

    // public function receipt($id)
    // {
    //     // Logic to generate receipt
    //     return view('bookings.receipt', compact('id'));
    // }

    // public function duplicateJob($id)
    // {
    //     // Logic to duplicate job
    //     $booking = Booking::findOrFail($id);
    //     $newBooking = $booking->replicate();
    //     $newBooking->ref_no = 'NEW' . rand(1000,9999);
    //     $newBooking->save();

    //     return redirect()->back()->with('success', 'Booking duplicated.');
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'passenger_name' => 'required|string',
    //         'pickup_address' => 'required|string',
    //         'dropoff_address' => 'required|string',
    //         'payment_type' => 'required|in:cash,card,account',
    //         'phone_no' => 'nullable|string',
    //         'driver_id' => 'nullable|exists:drivers,id',
    //         'vehicle_id' => 'nullable|exists:vehicles,id',
    //         'price' => 'nullable|numeric',
    //     ]);

    //     $pickup_time = now()->addHour(); // Example pickup time, adjust as needed

    //     // Create Passenger
    //     $passenger = Passenger::create([
    //         'name' => $validated['passenger_name'],
    //     ]);

        

    //     // Create Booking
    //     Booking::create([
    //         'ref_no' => 'REF'.time(),
    //         'passenger_id' => $passenger->id,
    //         'pickup_address' => $validated['pickup_address'],
    //         'dropoff_address' => $validated['dropoff_address'],
    //         'phone_no' => $validated['phone_no'],
    //         'payment_type' => $validated['payment_type'],
    //         'driver_id' => $validated['driver_id'],
    //         'vehicle_id' => $validated['vehicle_id'],
    //         'price' => $validated['price'],
    //         'pickup_time' => $pickup_time, // Example pickup time
    //     ]);

    //     return redirect()->route('dashboard')->with('success', 'Booking created successfully.');
    // }

    // public function create()
    // {
    //     $drivers = Driver::all();
    //     $vehicles = $this->firebase->getData('vehicles');
    //     return view('booking_form', compact('drivers', 'vehicles'));
    // }
    
    public function create()
{
    
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    // Fetch drivers from local database
    $drivers = Driver::all();
    
    // Fetch vehicles from Firebase
    $firebaseVehicles = $this->firebase->getData('vehicles'); // 'vehicles' node in Firebase
    $vehicles = [];
    if($firebaseVehicles) {
        foreach($firebaseVehicles as $key => $vehicle) {
            $vehicles[] = [
                'id' => $key, // Firebase key
                'make' => $vehicle['make'] ?? '',
                'model' => $vehicle['model'] ?? '',
            ];
        }
    }
    
    // Fetch accounts from Firebase
    $firebaseAccounts = $this->firebase->getData('customers'); // 'customers' node in Firebase
    $accounts = [];
    if($firebaseAccounts) {
        foreach($firebaseAccounts as $key => $account) {
            $accounts[] = [
                'id' => $account['id'] ?? $key,
                'business_name' => $account['business_name'] ?? '',
                'address' => $account['address'] ?? '',
                'email' => $account['email'] ?? '',
                'phone' => $account['phone'] ?? '',
            ];
        }
    }
    
    return view('booking_form', compact('drivers', 'vehicles', 'accounts'));
}
//     public function create()
// {
//     // Fetch drivers from local database
//     $drivers = Driver::all();

//     // Fetch vehicles from Firebase
//     $firebaseVehicles = $this->firebase->getData('vehicles'); // 'vehicles' node in Firebase
//     $vehicles = [];

//     if($firebaseVehicles) {
//         foreach($firebaseVehicles as $key => $vehicle) {
//             $vehicles[] = [
//                 'id' => $key, // Firebase key
//                 'make' => $vehicle['make'] ?? '',
//                 'model' => $vehicle['model'] ?? '',
//             ];
//         }
//     }

//     return view('booking_form', compact('drivers', 'vehicles'));
// }


    
//     public function store(Request $request)
// {
//     $validated = $request->validate([
//         'passenger_name' => 'required|string',
//         'pickup_address' => 'required|string',
//         'dropoff_address' => 'required|string',
//         'payment_type'   => 'required|in:cash,card,account',
//         'phone_no'       => 'nullable|string',
//         'driver_id'      => 'nullable|string',
//         'vehicle_id'     => 'nullable|string',
//         'price'          => 'nullable|numeric',
//     ]);
    
    
//     print_r($validated);
//     die;

//     $pickup_time = now()->addHour();

//     /**
//      * ==========================
//      * 1. Create Passenger in Firebase
//      * ==========================
//      */
//     $passengerData = [
//         'name'       => $validated['passenger_name'],
//         'phone_no'   => $validated['phone_no'] ?? null,
//         'created_at' => now()->toDateTimeString(),
//     ];

//     // Push passenger and get Firebase key
//     $passengerRef = $this->firebase->pushData('passengers', $passengerData);
//     $passengerId = $passengerRef->getKey();  // Firebase returns ["name" => generatedKey]

//     /**
//      * ==========================
//      * 2. Create Booking in Firebase
//      * ==========================
//      */
//     $bookingData = [
//         'ref_no'          => 'REF' . time(),
//         'passenger_id'    => $passengerId,
//         'passenger_name'  => $validated['passenger_name'],
//         'pickup_address'  => $validated['pickup_address'],
//         'dropoff_address' => $validated['dropoff_address'],
//         'phone_no'        => $validated['phone_no'] ?? null,
//         'payment_type'    => $validated['payment_type'],
//         'driver_id'       => $validated['driver_id'] ?? null,
//         'vehicle_id'      => $validated['vehicle_id'] ?? null,
//         'price'           => $validated['price'] ?? null,
//         'pickup_time'     => $pickup_time->toDateTimeString(),
//         'created_at'      => now()->toDateTimeString(),
//     ];

//     $this->firebase->pushData('bookings', $bookingData);

//     return redirect()->route('dashboard')->with('success', 'Booking and passenger created successfully in Firebase.');
// }

//Perfected version at 2025-10-10
// public function store(Request $request)
// {
//     $validated = $request->validate([
//         'passenger_name' => 'required|string',
//         'pickup_address' => 'required|string',
//         'dropoff_address'=> 'required|string',
//         'payment_type'   => 'required|in:cash,card,account',
//         'phone_no'       => 'nullable|string',
//         'vehicle_id'     => 'nullable|string', // vehicle is selected
//         'price'          => 'nullable|numeric',
//         'pickup_date'    => 'required|date',
//         'pickup_time'    => 'required|date_format:H:i',
//     ]);
    
    
//     // print_r($validated);
//     // die;

//     // $pickup_time = now()->addHour();

// // Combine date and time into one Carbon instance
//     $pickup_datetime = Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);

//     /**
//      * ==========================
//      * 1. Get driver_id against vehicle_id
//      * ==========================
//      */
//     $driverId = null;

//     if (!empty($validated['vehicle_id'])) {
//         // 🔹 Example if you have a local DB Vehicle model
//         // $driverId = \App\Models\Vehicle::where('id', $validated['vehicle_id'])
//         //              ->value('driver_id');

//         // 🔹 OR if vehicles are stored in Firebase
//         $vehicleData = $this->firebase->getData('vehicles/' . $validated['vehicle_id']);
        
//         // print_r($vehicleData);
//         // die;
//         $driverId = $vehicleData['driver_id'] ?? null;
//     }
    
    
    

//     /**
//      * ==========================
//      * 2. Create Passenger in Firebase
//      * ==========================
//      */
//     $passengerData = [
//         'name'       => $validated['passenger_name'],
//         'phone_no'   => $validated['phone_no'] ?? null,
//         'created_at' => now()->toDateTimeString(),
//     ];

//     $passengerRef = $this->firebase->pushData('passengers', $passengerData);
//     $passengerId = $passengerRef->getKey();

//     /**
//      * ==========================
//      * 3. Create Booking in Firebase
//      * ==========================
//      */
//     $bookingData = [
//         'ref_no'          => 'REF' . time(),
//         'passenger_id'    => $passengerId,
//         'passenger_name'  => $validated['passenger_name'],
//         'pickup_address'  => $validated['pickup_address'],
//         'dropoff_address' => $validated['dropoff_address'],
//         'phone_no'        => $validated['phone_no'] ?? null,
//         'status'        => 'upcoming',
//         'payment_type'    => $validated['payment_type'],
//         'driver_id'       => $driverId,
//         'vehicle_id'      => $validated['vehicle_id'] ?? null,
//         'price'           => $validated['price'] ?? null,
//         'pickup_time'     => $pickup_datetime->toDateTimeString(), // <-- user selected
//         'created_at'      => now()->toDateTimeString(),
//     ];
    
    
   

//     $this->firebase->pushData('bookings', $bookingData);

//     return redirect()->route('dashboard')->with('success', 'Booking and passenger created successfully in Firebase.');
// }


public function generateRefNo()
{
    $ref = $this->database->getReference('ref_counter/last_number');
    
    // echo $ref->getValue();
    // die;

    try {
        // Atomic increment using transaction
        // $next = $ref->runTransaction(function ($current) {
        //     // Start from 50000 if null
        //     return ($current ?? 50000) + 1;
        // });
        
        $next =  ($ref->getValue() ?? 50000) + 1;
        
        $this->firebase->updateData('ref_counter/',[
                'last_number' => $next
            ]);
        
        return 'CCZ'. $next;

        // // Return the new reference number as a proper response
        // return response()->json([
        //     'status' => 'success',
        //     'ref_no' => 'CCW' . $next->value()
        // ]);
    } catch (\Exception $e) {
        // Catch Firebase or other errors
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}



public function store(Request $request)
{
    $validated = $request->validate([
        'passenger_name' => 'required|string',
        'pickup_address' => 'required|string',
        'dropoff_address'=> 'required|string',
        'payment_type'   => 'required|in:cash,card,account',
        'phone_no'       => 'nullable|string',
        'email'          => 'required|email',
        'vehicle_id'     => 'nullable|string',
        'vehicle_make' => 'nullable|string',
        // 'price'          => 'nullable|numeric',
// ✅ Monetary fields
    'fare'           => 'nullable|numeric|min:0',
    'parking'        => 'nullable|numeric|min:0',
    'extra'          => 'nullable|numeric|min:0',
    'waiting_fee'    => 'nullable|numeric|min:0',
    'price'          => 'nullable|numeric|min:0',
        'pickup_date'    => 'required|date',
        'pickup_time'    => 'required|date_format:H:i',
        'flight_no'      => 'nullable|string',
        'via_addresses'  => 'array',
        'via_addresses.*'=> 'nullable|string',
        'job_comment'       =>'nullable|string',
        'child_seat' => 'nullable|boolean' ,
    ]);

    $pickup_datetime = Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);

    /**
     * ==========================
     * 1. Get driver_id against vehicle_id
     * ==========================
     */
    $driverId = null;
    if (!empty($validated['vehicle_id'])) {
        $vehicleData = $this->firebase->getData('vehicles/' . $validated['vehicle_id']);
        $driverId = $vehicleData['driver_id'] ?? null;
    }

    /**
     * ==========================
     * 2. Create Passenger in Firebase
     * ==========================
     */
    $passengerData = [
        'name'       => $validated['passenger_name'],
        'phone_no'   => $validated['phone_no'] ?? null,
        'created_at' => now()->toDateTimeString(),
    ];

    $passengerRef = $this->firebase->pushData('passengers', $passengerData);
    $passengerId = $passengerRef->getKey();

    /**
     * ==========================
     * 3. Prepare Via Addresses
     * ==========================
     */
    $viaAddresses = [];
    if (!empty($validated['via_addresses'])) {
        foreach ($validated['via_addresses'] as $via) {
            if (!empty(trim($via))) {
                $viaAddresses[] = trim($via);
            }
        }
    }
    // echo"here";
    // die;
    
    $refNo = $this->generateRefNo();

    
    

    /**
     * ==========================
     * 4. Create Booking in Firebase
     * ==========================
     */
    $bookingData = [
        'ref_no'          => $refNo,
        'passenger_id'    => $passengerId,
        'passenger_name'  => $validated['passenger_name'],
        'pickup_address'  => $validated['pickup_address'],
        'via_addresses'   => $viaAddresses,
        'dropoff_address' => $validated['dropoff_address'],
        'flight_no'       => $validated['flight_no'] ?? null,
        'phone_no'        => $validated['phone_no'] ?? null,
        'status'          => 'pending',
        'email'           => $validated['email'],
        // ⭐ NEW LINE — SAFE ADDITION ⭐
    'vehicle_make'    => $validated['vehicle_make'] ?? null,
    'job_comment' => $validated['job_comment'] ?? null,
        'payment_type'    => $validated['payment_type'],
        'driver_id'       => "",
        'vehicle_id'      => $validated['vehicle_id'] ?? null,
        // 'price'           => $validated['price'] ?? null,

        // 💰 PRICE BREAKDOWN (NEW)
    'fare'            => $validated['fare'] ?? 0,
    'parking'         => $validated['parking'] ?? 0,
    'extra'           => $validated['extra'] ?? 0,
    'waiting_fee'     => $validated['waiting_fee'] ?? 0,
    'price'           => $validated['price'] ?? 0,
        'pickup_time'     => $pickup_datetime->toDateTimeString(),
        'created_at'      => now()->toDateTimeString(),
        'platform'        => 2, // ✅ default 1 = web
        'child_seat' => $validated['child_seat']
    ];
    
    // print_r($bookingData);
    // die;

    // Push booking to Firebase
    $bookingRef = $this->firebase->pushData('bookings', $bookingData);
    $bookingId = $bookingRef->getKey();
    $formattedPickupDate = \Carbon\Carbon::parse($request['pickup_date'] ?? now())->format('d/M/Y');
    
    $paymentType = strtolower($validated['payment_type']);

if ($paymentType === 'cash') {
    $paymentLabel = 'Pay in Car';
} elseif ($paymentType === 'card') {
    $paymentLabel = 'Payment Received';
} elseif ($paymentType === 'account') {
    $paymentLabel = 'Account';
}

$viaHtml = '';

if (isset($validated['via_addresses']) && is_array($validated['via_addresses'])) {
    foreach ($validated['via_addresses'] as $index => $via) {
        if (!empty($via)) {
            $viaHtml .= '<strong>Via ' . ($index + 1) . ':</strong> ' . e($via) . '<br>';
        }
    }
}

    /**
     * ==========================
     * 5. Stripe Payment Link (if payment_type = 'card')
     * ==========================
     */
    // 5. Handle payment type
    if ($validated['payment_type'] === 'card') {
        // Stripe payment
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Booking Payment (' . $bookingData['ref_no'] . ')'],
                    'unit_amount' => ($validated['price'] ?? 10) * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['booking_id' => $bookingId]),
            'cancel_url'  => route('payment.cancel', ['booking_id' => $bookingId]),
            'metadata' => ['booking_id' => $bookingId, 'passenger_email' => $validated['email']],
        ]);

        $paymentLink = $session->url;

        // Save payment in Firebase
        $this->firebase->pushData('payments', [
            'booking_id'   => $bookingId,
            'email'        => $validated['email'],
            'amount'       => $validated['price'] ?? 10,
            'currency'     => 'usd',
            'payment_link' => $paymentLink,
            'status'       => 'pending',
            'created_at'   => now()->toDateTimeString(),
        ]);
        
        
        
        

        // Send payment email
        // $emailBody = "Dear {$validated['passenger_name']},\n\nPlease complete your payment using the link below:\n\n{$paymentLink}\n\nThank you for choosing CrownCarz!";
        $emailBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CrownCarz Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;">

<div style="max-width:800px; margin:auto; background:white; padding:20px; border-radius:10px;">

    <div style="text-align:center; margin-bottom:20px;">
        <img src="' . asset('public/images/logo.png') . '"
             alt="CrownCarz" style="width:180px;">
    </div>

    <p>Dear <strong>' . $validated['passenger_name'] . '</strong>,</p>

    <p>
        Booked your ride with CrownCarz using the following Login ID:
        <strong>' . $validated['email'] . '</strong><br>
        Your Reference ID:
        <strong>' . $refNo . '</strong>
    </p>

    <p>
        Your booking has been received successfully.
        Please keep this reference number for future communication.
    </p>

    <h3>Summary</h3>
    <p>
        <strong>Booking number:</strong> ' . $refNo . '<br>
        <strong>Passenger name:</strong> ' . $validated['passenger_name'] . '<br>
        <strong>Passenger email:</strong> ' . $validated['email'] . '<br>
        <strong>Contact no:</strong> ' . ($validated['phone_no'] ?? '-') . '<br>
        <strong>Vehicle Category:</strong> ' . ($validated['vehicle_make'] ?? '-') . '
    </p>

    <h3>Travel Information</h3>
    <p>
        <strong>Date:</strong> ' . $formattedPickupDate . '<br>
        <strong>Time:</strong> ' . $validated['pickup_time'] . '<br>
        <strong>Pickup:</strong> ' . $validated['pickup_address'] . '<br>
         ' . $viaHtml . '
        <strong>Dropoff:</strong> ' . $validated['dropoff_address'] . '
    </p>

    <h3>Payment Details</h3>
    <p>
        <strong>Payment Type:</strong> ' . $paymentLabel . '<br>
        <strong>Base Fare:</strong> £ ' . (isset($validated['fare']) 
    ? ceil((float)$validated['fare']) 
    : '-') .  '<br>
        <strong>Parking Fee:</strong> £ ' . ($validated['parking'] ?? '-') . '<br>
        <strong>Total Fare:</strong> £ ' . ($validated['price'] ?? '-') . '
    </p>

   <p>Please complete your payment using the link below:\n\n'.$paymentLink.'</p>

    <p>
        If any information is incorrect, please contact
        <strong>CrownCarz Support</strong> or call
        <strong>01189 474747</strong> at least 24 hours before pickup.
    </p>

    <br>
    <p>Best Regards,<br><strong>CrownCarz</strong></p>

</div>
</body>
</html>';

        
        
    } else {
        // Cash or account payment email
        $emailBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CrownCarz Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;">

<div style="max-width:800px; margin:auto; background:white; padding:20px; border-radius:10px;">

    <div style="text-align:center; margin-bottom:20px;">
        <img src="' . asset('public/images/logo.png') . '"
             alt="CrownCarz" style="width:180px;">
    </div>

    <p>Dear <strong>' . $validated['passenger_name'] . '</strong>,</p>

    <p>
        Booked your ride with CrownCarz using the following Login ID:
        <strong>' . $validated['email'] . '</strong><br>
        Your Reference ID:
        <strong>' . $refNo . '</strong>
    </p>

    <p>
        Your booking has been received successfully.
        Please keep this reference number for future communication.
    </p>

    <h3>Summary</h3>
    <p>
        <strong>Booking number:</strong> ' . $refNo . '<br>
        <strong>Passenger name:</strong> ' . $validated['passenger_name'] . '<br>
        <strong>Passenger email:</strong> ' . $validated['email'] . '<br>
        <strong>Contact no:</strong> ' . ($validated['phone_no'] ?? '-') . '<br>
        <strong>Vehicle Category:</strong> ' . ($validated['vehicle_make'] ?? '-') . '
    </p>

    <h3>Travel Information</h3>
    <p>
        <strong>Date:</strong> ' . $formattedPickupDate . '<br>
        <strong>Time:</strong> ' . $validated['pickup_time'] . '<br>
        <strong>Pickup:</strong> ' . $validated['pickup_address'] . '<br>
         ' . $viaHtml . '
        <strong>Dropoff:</strong> ' . $validated['dropoff_address'] . '
    </p>

    <h3>Payment Details</h3>
    <p>
        <strong>Payment Type:</strong> ' . $paymentLabel . '<br>
        <strong>Base Fare:</strong> £ ' . (isset($validated['fare']) 
    ? ceil((float)$validated['fare']) 
    : '-') .  '<br>
        <strong>Parking Fee:</strong> £ ' . ($validated['parking'] ?? '-') . '<br>
        <strong>Total Fare:</strong> £ ' . ($validated['price'] ?? '-') . '
    </p>


    <p>
        If any information is incorrect, please contact
        <strong>CrownCarz Support</strong> or call
        <strong>01189 474747</strong> at least 24 hours before pickup.
    </p>

    <br>
    <p>Best Regards,<br><strong>CrownCarz</strong></p>

</div>
</body>
</html>';

    }

    // Send email
    Mail::html($emailBody, function ($message) use ($validated,$refNo) {
    $message->to($validated['email'])
    ->cc('bookings@crownairporttravels.com')
            ->subject('CrownCarz Booking Confirmation - ' . $refNo);
            
            
});


    // Mail::raw($emailBody, function ($message) use ($validated) {
    //     $message->to($validated['email'])
    //             ->subject('Your CrownCarz Booking Details');
    // });

    return redirect()->route('booking.create')
        ->with('success', 'Booking created successfully. Payment link sent if payment type is card.');
}


 /**
     * Send Confirmation Email
     */
    // public function sendConfirmationEmail($bookingId)
    // {
    //     $booking = $this->firebase->getData("bookings/{$bookingId}");

    //     if (!$booking) {
    //         return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
    //     }

    //     // Send email
    //     Mail::raw(
    //         "Dear {$booking['passenger_name']},\n\nYour booking (REF: {$booking['ref_no']}) is confirmed.\n\nThank you!",
    //         function ($message) use ($booking) {
    //             $message->to($booking['email'])
    //                     ->subject("Booking Confirmation - {$booking['ref_no']}");
    //         }
    //     );

    //     return response()->json(['status' => 'success', 'message' => 'Confirmation email sent successfully']);
    // }
public function sendConfirmationEmail($bookingId)
{
    $booking = $this->firebase->getData("bookings/{$bookingId}");

    if (!$booking) {
        return response()->json([
            'status' => 'error',
            'message' => 'Booking not found'
        ], 404);
    }
    
    
    $email = request()->input('email') ?? ($booking['email'] ?? null);


    // All values from form
    $formData = request()->all();

    
    
    // Email from booking OR form
    

    if (!$email) {
        return response()->json([
            'status'=>'error',
            'message'=>'Email not provided'
        ], 400);
    }

    // Example using selected form fields
    // Fallback-safe values
    $passenger   = $formData['passenger_name'] ?? $booking['passenger_name'];
    $pickup      = $formData['pickup_address'] ?? $booking['pickup_address'];
    $dropoff     = $formData['dropoff_address'] ?? $booking['dropoff_address'];
    $pickup_date = $formData['pickup_date'] ?? $booking['pickup_date'];
    $pickup_time = $formData['pickup_time'] ?? $booking['pickup_time'];
    $phone       = $formData['phone_no'] ?? ($booking['phone_no'] ?? '-');
    $vehicleName = $formData['vehicle_make'] ?? ($booking['vehicle_make'] ?? '-');
    $price       = $formData['price'] ?? ($booking['price'] ?? '-');
    $paymentType = $formData['payment_type'] ?? ($booking['payment_type'] ?? '-');
    $flightNo    = $formData['flight_no'] ?? '-';
    $instructions = $formData['job_comment'] ?? $booking['job_comment'] ?? '-';

    // Format date
    $formattedPickupDate = \Carbon\Carbon::parse($pickup_date ?? now())
        ->format('d/M/Y');
        
        //$paymentType = strtolower($request['payment_type']);

if ($paymentType === 'cash') {
    $paymentLabel = 'Pay in Car';
} elseif ($paymentType === 'card') {
    $paymentLabel = 'Payment Received';
} elseif ($paymentType === 'account') {
    $paymentLabel = 'Account';
}

    // HTML Email Body
    $emailBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>CrownCarz Booking Confirmation</title>
    </head>
    <body style="font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;">

        <div style="max-width:800px; margin:auto; background:white; padding:20px; border-radius:10px;">

            <div style="text-align:center; margin-bottom:20px;">
                <img src="' . asset('public/images/logo.png') . '"
                     alt="CrownCarz"
                     style="width:180px;">
            </div>

            <p>Dear <strong>' . $passenger . '</strong>,</p>

            <p>
                Booked your ride with CrownCarz using the following Login ID:
                <strong>' . $email . '</strong><br>
                Your Reference ID:
                <strong>' . $booking['ref_no'] . '</strong>
            </p>

            <p>
                This is to inform you that your booking has been received successfully.
                Please use this reference number for all future communication.
            </p>

            <h3>Summary</h3>

            <p>
                <strong>Booking number:</strong> ' . $booking['ref_no'] . '<br>
                <strong>Passenger name:</strong> ' . $passenger . '<br>
                <strong>Passenger email:</strong> ' . $email . '<br>
                <strong>Contact no:</strong> ' . $phone . '<br>
                <strong>Vehicle Category:</strong> ' . $vehicleName . '
            </p>

            <p><strong>Special Instructions:</strong> ' . $instructions . '</p>

            <h3>Travel Information</h3>

            <p>
                <strong>Date of booking:</strong> ' . $formattedPickupDate . '<br>
                <strong>Time of booking:</strong> ' . $pickup_time . '<br>
                <strong>Pick up point:</strong> ' . $pickup . '<br>
                <strong>Destination:</strong> ' . $dropoff . '<br>
                <strong>Flight number:</strong> ' . $flightNo . '
            </p>

            <h3>Payment Details</h3>

            <p>
                <strong>Pay Type:</strong> ' . $paymentLabel . '<br>
                <strong>Total Cost:</strong> £ ' . $price . '
            </p>

            <p>
                If any of the above information is incorrect, please email
                <strong>CrownCarz Support</strong> or call
                <strong>01189 474747</strong> at least 24 hours before pickup time.
            </p>

            <br>
            <p>Best Regards,<br><strong>CrownCarz</strong></p>

        </div>

    </body>
    </html>';

    // Send HTML Email
    // Mail::send([], [], function ($message) use ($email, $booking, $emailBody) {
    //     $message->to($email)
    //             ->subject("Booking Confirmation - {$booking['ref_no']}")
    //             ->setBody($emailBody, 'text/html');
    // });
    Mail::html($emailBody, function ($message) use ($booking, $email) {
    $message->to($email)
            ->subject("Booking Confirmation - {$booking['ref_no']}");
});

    
//     $passenger = $formData['passenger_name'] ?? $booking['passenger_name'];
//     $pickup = $formData['pickup_address'] ?? $booking['pickup_address'];
//     $dropoff = $formData['dropoff_address'] ?? $booking['dropoff_address'];
//     $pickup_date = $formData['pickup_date'] ?? $booking['pickup_date'];
//     $pickup_time = $formData['pickup_time'] ?? $booking['pickup_time'];
    
    
//     // Format pickup date
// $formattedPickupDate = \Carbon\Carbon::parse($pickup_date ?? now())->format('d/M/Y');

//     // Email Message
//     $messageText = 
// "Dear $passenger,

// Your booking (Ref: {$booking['ref_no']}) is confirmed.

// Pickup: $pickup  
// Dropoff: $dropoff  
// Pickup Date:  $formattedPickupDate
// Pickup Time: $pickup_time  

// Thank you for choosing CrownCarz!";

//     // Send email
//     Mail::raw($messageText, function ($message) use ($email, $booking) {
//         $message->to($email)
//                 ->subject("Booking Confirmation - {$booking['ref_no']}");
//     });

    return response()->json([
        'status' => 'success',
        'message' => 'Confirmation email sent successfully'
    ]);
}



    /**
     * Create Re-Occurring Job
     */
    // public function createRecurring($bookingId)
    // {
    //     $booking = $this->firebase->getData("bookings/{$bookingId}");

    //     if (!$booking) {
    //         return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
    //     }

    //     // Duplicate booking and adjust pickup_date
    //     $newBooking = $booking;
    //     $newBooking['pickup_time'] = Carbon::parse($booking['pickup_time'])->addWeek()->toDateTimeString();
    //     $newBooking['status'] = 'upcoming';
    //     $newBooking['created_at'] = now()->toDateTimeString();

    //     $newRef = $this->firebase->pushData('bookings', $newBooking);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Recurring booking created successfully',
    //         'new_booking_id' => $newRef->getKey()
    //     ]);
    // }
public function createRecurring(Request $request, $bookingId)
{
    $request->validate([
        'from_date' => 'required|date',
        'to_date'   => 'required|date|after_or_equal:from_date'
    ]);

    // Get base booking
    $booking = $this->firebase->getData("bookings/{$bookingId}");
    if (!$booking) {
        return response()->json(['status'=>'error','message'=>'Booking not found'],404);
    }

    // Vehicle mapping
    $vehicleMap = [
        'Saloon'    => 1,
        'Estate'    => 2,
        'MPV'       => 3,
        '8 Seater'  => 4,
        'Executive' => 5,
    ];

    // Fields we want to copy
    $allowedFields = [
        'driver_id',
        'dropoff_address',
        'email',
        'passenger_id',
        'passenger_name',
        'payment_type',
        'phone_no',
        'pickup_address',
        'pickup_time',
        'price',
        'status',
        'vehicle_make' // keep this so we can map it
    ];

    $current = Carbon::parse($request->from_date);
    $end     = Carbon::parse($request->to_date);

    $createdBookings = [];

    while ($current->lte($end)) {

        // Build new booking
        $newBooking = [];
        foreach ($allowedFields as $field) {
            $newBooking[$field] = $booking[$field] ?? null;
        }

        // Build pickup datetime
        $pickupDate = $current->format("Y-m-d");
        $pickupTime = Carbon::parse($booking['pickup_time'])->format('H:i:s');

        $newBooking['pickup_time'] = Carbon::parse($pickupDate . " " . $pickupTime)
                                          ->toDateTimeString();

        // ✅ Set vehicle_id based on vehicle_make
        $make = $newBooking['vehicle_make'] ?? null;
        $newBooking['vehicle_id'] = $vehicleMap[$make] ?? null;

        // Required fields
        $newBooking['created_at'] = now()->toDateTimeString();
        $newBooking['status']     = 'upcoming';
        $newBooking['ref_no']     = $this->generateRefNo();
        // $newBooking['ref_no']     = "REF" . time() . rand(100,999);

        // Save to Firebase
        $newRef = $this->firebase->pushData('bookings', $newBooking);
        $createdBookings[] = $newRef->getKey();

        // Next day
        $current->addDay();
    }

    return response()->json([
        'status'      => 'success',
        'message'     => 'Daily recurring bookings created successfully',
        'booking_ids' => $createdBookings
    ]);
}





    /**
     * Create Return Job
     */
    public function createReturnJob(Request $request, $bookingId)
{
    $booking = $this->firebase->getData("bookings/{$bookingId}");

    if (!$booking) {
        return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
    }

    // Get all form values
    $formData = $request->all();

    // Build return booking with all form values, fallback to original booking if not in form
    $returnBooking = [];
    $fields = [
        'driver_id',
        'dropoff_address',
        'email',
        'passenger_id',
        'passenger_name',
        'payment_type',
        'phone_no',
        'pickup_address',
        'pickup_time',
        'price',
        'status',
        'vehicle_id'
    ];

    foreach ($fields as $field) {
        $returnBooking[$field] = $formData[$field] ?? $booking[$field] ?? null;
    }

    // Swap pickup and dropoff
    $returnBooking['pickup_address'] = $formData['dropoff_address'] ?? $booking['dropoff_address'];
    $returnBooking['dropoff_address'] = $formData['pickup_address'] ?? $booking['pickup_address'];

    // Update required fields
    $returnBooking['status'] = 'upcoming';
    $returnBooking['created_at'] = now()->toDateTimeString();
    $returnBooking['ref_no'] = $this->generateRefNo();
    // $returnBooking['ref_no'] = "REF" . time() . rand(100, 999);

    // Recalculate price if needed
    $returnBooking['price'] = $this->calculateParkingFee($returnBooking['pickup_address'], $returnBooking['dropoff_address']);

    $newRef = $this->firebase->pushData('bookings', $returnBooking);

    return response()->json([
        'status' => 'success',
        'message' => 'Return booking created successfully',
        'return_booking_id' => $newRef->getKey()
    ]);
}

    
    private function calculateParkingFee($pickup, $dropoff)
{
    $pickupChargeTotal = 0;
    $dropoffChargeTotal = 0;
    $pickup = strtoupper(str_replace(' ', '', $pickup));
    $dropoff = strtoupper(str_replace(' ', '', $dropoff));

    $matchedPickup = false;
    $matchedDropoff = false;

    try {
        $categories = ['airports', 'stations', 'ports'];
        foreach ($categories as $cat) {
            $data = $this->database->getReference($cat)->getValue() ?? [];
            foreach ($data as $item) {
                $pc = strtoupper(str_replace(' ', '', $item['post_code'] ?? ''));
                $pickupCharge = (float)($item['pickup_charge'] ?? 0);
                $dropoffCharge = (float)($item['dropoff_charge'] ?? 0);

                // Only match once for pickup
                if (!$matchedPickup && $pc === $pickup) {
                    $pickupChargeTotal = $pickupCharge;
                    $matchedPickup = true;
                }

                // Only match once for dropoff
                if (!$matchedDropoff && $pc === $dropoff) {
                    $dropoffChargeTotal = $dropoffCharge;
                    $matchedDropoff = true;
                }

                // If both matched, stop searching
                if ($matchedPickup && $matchedDropoff) {
                    break 2; // Exit both loops
                }
            }
        }
    } catch (\Exception $e) {
        \Log::error('Parking Fee Calculation Error: ' . $e->getMessage());
    }

    return [
        'pickup_charge'  => $pickupChargeTotal,
        'dropoff_charge' => $dropoffChargeTotal,
        'total'          => $pickupChargeTotal + $dropoffChargeTotal
    ];
}

    /**
     * Send Receipt Email
     */
    public function sendReceiptEmail(Request $request, $bookingId)
{
    $booking = $this->firebase->getData("bookings/{$bookingId}");

    if (!$booking) {
        return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
    }

    // Form Email OR Booking Email
    $email = $request->email ?? ($booking['email'] ?? null);

    if (!$email) {
        return response()->json(['status' => 'error', 'message' => 'Email is required'], 422);
    }

    // Build a detailed booking email
    $form = $request->all();
    
        // Format pickup date
$formattedPickupDate = \Carbon\Carbon::parse($form['pickup_date'] ?? now())->format('d/M/Y');

    $messageBody = "
Booking Receipt  
----------------------

Passenger: {$form['passenger_name']}
Phone: {$form['phone_no']}
Email: {$form['email']}

Pickup: {$form['pickup_address']}
Dropoff: {$form['dropoff_address']}
Pickup Date: {$formattedPickupDate} {$form['pickup_time']}
Flight No: {$form['flight_no']}

Fare: £{$form['fare']}
Parking: £{$form['parking']}
Extra: £{$form['extra']}
Waiting Fee: £{$form['waiting_fee']}
Total: £{$form['price']}

Ref No: {$booking['ref_no']}
        ";

    Mail::raw($messageBody, function ($message) use ($email, $booking) {
        $message->to($email)
                ->subject("Booking Receipt - {$booking['ref_no']}");
    });

    return response()->json(['status' => 'success', 'message' => 'Receipt email sent successfully']);
}

    
    


// public function createStripePayment(Request $request, $bookingId)
// {
//     Log::info('Method entered', ['bookingId' => $bookingId]);  // ← ADD: Entry point check
    
//     try {
//         Log::info('Starting validation', ['amount' => $request->amount ?? 'missing']);
//         $request->validate(['amount' => 'required|numeric|min:1']);
//         Log::info('Validation passed');

//         Log::info('Fetching booking from Firebase');
//         $booking = $this->firebase->getData("bookings/{$bookingId}");
//         if (!$booking) {
//             Log::warning('Booking not found');
//             return response()->json(['status'=>'error','message'=>'Booking not found'], 404);
//         }
//         Log::info('Booking fetched', ['ref_no' => $booking['ref_no'] ?? 'missing']);

//         Log::info('Setting Stripe API key');
//         Stripe::setApiKey(config('services.stripe.secret'));  // Revert to env ASAP!
//         Log::info('API key set (length check)', ['key_len' => strlen(config('services.stripe.secret'))]);  // Use env here for prod

//         Log::info('Creating Stripe session');
//         $session = StripeSession::create([
//             'payment_method_types' => ['card'],
//             'line_items' => [[
//                 'price_data' => [
//                     'currency' => 'gbp',
//                     'product_data' => ['name' => 'Booking Payment (REF: '.$booking['ref_no'].')'],
//                     'unit_amount' => $request->amount * 100, // cents
//                 ],
//                 'quantity' => 1
//             ]],
//             'mode' => 'payment',
//             'success_url' => route('payment.success', ['booking_id' => $bookingId]),
//             'cancel_url' => route('dashboard'),
//             'metadata' => ['booking_id' => $bookingId]
//         ]);
//         Log::info('Session created successfully', ['url' => $session->url]);  // ← MOVED: Before return

//         Log::info('Updating Firebase with payment link');
//         $this->firebase->updateData("bookings/{$bookingId}", ['payment_link' => $session->url]);
//         Log::info('Firebase updated');

//         return response()->json(['status'=>'success','payment_url'=>$session->url]);
//     } catch (ValidationException $e) {
//         Log::warning('Validation failed', ['errors' => $e->errors()]);
//         return response()->json(['status' => 'error', 'message' => 'Invalid input: ' . implode(', ', $e->errors()['amount'])], 422);
//     } catch (Exception $e) {
//         Log::error('Payment error', ['bookingId' => $bookingId, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//         return response()->json(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()], 500);
//     }
//     // Remove the duplicate validate here
// }


public function createStripePayment(Request $request, $bookingId)
{
    Log::info('Method entered', ['bookingId' => $bookingId]);
    
    try {
        Log::info('Starting validation', ['amount' => $request->amount ?? 'missing']);
        $request->validate([
            'amount' => 'required|numeric|min:1']);
        Log::info('Validation passed');

        Log::info('Fetching booking from Firebase');
        $booking = $this->firebase->getData("bookings/{$bookingId}");
        if (!$booking) {
            Log::warning('Booking not found');
            return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        }
        Log::info('Booking fetched', ['ref_no' => $booking['ref_no'] ?? 'missing', 'email' => $booking['email'] ?? 'missing']);
        
        
        $method = $request->method ?? 'pay';

        
        if ($method === "sms") {

    // your SMS code here...
    // --- NEW: Send SMS with payment link (MySMS API) ---
    try {

        $userPhone = $booking['phone_no'] ?? null;

        if ($userPhone) {

            $cleanPhone = preg_replace('/\D/', '', $userPhone);

            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '+44' . substr($cleanPhone, 1);
            } elseif (str_starts_with($cleanPhone, '44')) {
                $cleanPhone = '+' . $cleanPhone;
            } elseif (!str_starts_with($cleanPhone, '+')) {
                $cleanPhone = '+' . $cleanPhone;
            }

            $messageText =
                "Payment Link for Booking REF: " . ($booking['ref_no'] ?? $bookingId) .
                "\nAmount: {$formattedAmount}" .
                "\nPay securely: {$session->url}";

            $response = Http::post(rtrim(config('services.admin.url'), '/') . '/sms/send', [
                'mobile' => $cleanPhone,
                'message' => $messageText
            ]);

            $smsResponse = $response->json();

            Log::info("SMS Result", $smsResponse);

        } else {
            Log::warning("No phone number in booking.");
        }

    } catch (\Exception $smsEx) {
        Log::error("SMS error", ['error' => $smsEx->getMessage()]);
    }

    // return early for SMS-only mode
    return response()->json([
        'status' => 'success',
        'mode' => 'sms'
    ]);
}


        // Extract user email – adjust key if needed
        $userEmail = $booking['email'] ?? null;
        if (!$userEmail) {
            Log::warning('No email in booking data');
            // Still proceed, but email will skip
        }

        Log::info('Setting Stripe API key');
        Stripe::setApiKey(config('services.stripe.secret'));  // ← REVERTED: Use env, not hardcoded
        Log::info('API key set (length check)', ['key_len' => strlen(config('services.stripe.secret'))]);

        // Format amount for logs/email (e.g., "£100.00")
        $formattedAmount = '£' . number_format($request->amount, 2);

        Log::info('Creating Stripe session');
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',  // Confirmed: GBP (pounds)
                    'product_data' => ['name' => 'Booking Payment (REF: ' . ($booking['ref_no'] ?? 'N/A') . ')'],
                    'unit_amount' => $request->amount * 100, // Pence (e.g., 100.00 GBP = 10000)
                ],
                'quantity' => 1
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['booking_id' => $bookingId]),
            'cancel_url' => route('dashboard'),
            'metadata' => ['booking_id' => $bookingId]
        ]);
        Log::info('Session created successfully', ['url' => $session->url, 'formatted_amount' => $formattedAmount]);

        Log::info('Updating Firebase with payment link');
        $this->firebase->updateData("bookings/{$bookingId}", ['payment_link' => $session->url]);
        Log::info('Firebase updated');

        // NEW: Send email with payment link
        if ($userEmail) {
            try {
                Mail::send('emails.payment-link', [  // Blade view name (no .php)
                    'paymentUrl' => $session->url,
                    'bookingRef' => $booking['ref_no'] ?? 'N/A',
                    'amount' => $formattedAmount,
                    'bookingId' => $bookingId
                ], function ($message) use ($userEmail, $booking) {
                    $message->to($userEmail, $booking['customer_name'] ?? 'Customer')  // Optional: Name from booking
                            ->subject('Your Payment Link for Booking ' . ($booking['ref_no'] ?? $bookingId));
                });
                Log::info('Payment email sent successfully', ['email' => $userEmail]);
            } catch (Exception $mailException) {
                Log::error('Failed to send payment email', [
                    'bookingId' => $bookingId,
                    'email' => $userEmail,
                    'error' => $mailException->getMessage()
                ]);
                // Don't fail the whole request – just log
            }
        } else {
            Log::warning('Skipped email: No user email available', ['bookingId' => $bookingId]);
        }

        return response()->json([
            'status' => 'success', 
            'payment_url' => $session->url,
            'email_sent' => $userEmail ? true : false  // Optional: Inform frontend
        ]);

    } catch (ValidationException $e) {
        Log::warning('Validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'status' => 'error', 
            'message' => 'Invalid input: ' . implode(', ', $e->errors()['amount'] ?? ['Unknown'])
        ], 422);
    } catch (Exception $e) {
        Log::error('Payment error', [
            'bookingId' => $bookingId, 
            'error' => $e->getMessage(), 
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'status' => 'error', 
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}
// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup = $request->pickup;
//     $dropoff = $request->dropoff;

//     if(!$vehicleId || !$pickup || !$dropoff){
//         return response()->json(['success' => false, 'message' => 'Missing required parameters']);
//     }

//     // Fetch price from Firebase
//     $firebaseBookings = $this->firebase->getData('pricing'); // Assume you have a 'pricing' node
//     $price = null;

//     // Example: match by vehicle and addresses
//     foreach($firebaseBookings as $key => $item){
//         if($item['vehicle_id'] == $vehicleId &&
//            $item['pickup_address'] == $pickup &&
//            $item['dropoff_address'] == $dropoff){
//                $price = $item['price'];
//                break;
//            }
//     }

//     if($price !== null){
//         return response()->json(['success' => true, 'price' => $price]);
//     } else {
//         return response()->json(['success' => false, 'message' => 'Price not found']);
//     }
// }

// Perfected version of getPrice method on 2025-09-23
// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup = $request->pickup;
//     $dropoff = $request->dropoff;


    
    

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }

//     // Fetch price data from Firebase
//     $firebaseBookings = $this->firebase->getData('fixed_prices');
//     $price = null;

//     foreach ($firebaseBookings as $key => $item) {
//         if (
//             isset($item['vehicle_type'], $item['from_postcode'], $item['to_postcode']) &&
//             $item['vehicle_type'] == $vehicleId &&   // matching with vehicleId
//             $item['from_postcode'] == $pickup &&
//             $item['to_postcode'] == $dropoff
//         ) {
                
//             $company = isset($item['company_price']) ? (float)$item['company_price'] : 0;
//             $driver  = isset($item['driver_price']) ? (float)$item['driver_price'] : 0;
//             $agent   = isset($item['agent_commission']) ? (float)$item['agent_commission'] : 0;

//             $price = $company + $driver + $agent;
//             break;
//         }
//     }

//     if ($price !== null) {
//         return response()->json(['success' => true, 'price' => $price]);
//     } else {
//         return response()->json(['success' => false, 'message' => 'Price not found']);
//     }
// }

// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup    = strtoupper(trim($request->pickup));
//     $dropoff   = strtoupper(trim($request->dropoff));
    
    
//     // // ✅ LOGGING START
//     //Log::info('--- getPrice Request Start ---');
//   // Log::info('Input: ', ['vehicle_id' => $vehicleId, 'pickup' => $pickup, 'dropoff' => $dropoff]);
//     // ✅ END LOGGING

//     //  return response()->json([
//     //         '$vehicleId' => $vehicleId,
//     //         '$pickup'   => $pickup,
//     //         '$dropoff'    => $dropoff
//     //     ]);
    
//     $basePostcode = "RG1 1LZ"; // fixed base location

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }

//     1. Try Fixed Price (from MySQL)
//     $fixed = FixedPrice::where('vehicle_type', $vehicleId)
//         ->where('from_postcode', $pickup)
//         ->where('to_postcode', $dropoff)
//         ->first();
      
//       $fixed = FixedPrice::where('vehicle_type', $vehicleId)
//         ->where('from_postcode', $pickup)
//         ->where('to_postcode', $dropoff)
//         ->first();  
        
        

//     if ($fixed) {
//         $company = (float)$fixed->company_price;
//         $driver  = (float)$fixed->driver_price;
//         $agent   = (float)$fixed->agent_commission;

//         $price = $company + $driver + $agent;

//         return response()->json([
//             'success' => true,
//             'price'   => round($price, 2),
//             'type'    => 'fixed'
//         ]);
//     }
    
//     // Existing variables (from the full context of your getPrice function)
// // $vehicleId: The vehicle type (e.g., 'Saloon', 'Estate', which should match car_type)
// // $perMileRate and $minimumPrice need to be extracted from the result.

// // 2. Mileage Pricing (fallback if no fixed pricing)
// // ASSUMPTION: $vehicleId passed to getPrice is the exact 'car_type' string 
// // (e.g., 'Estate', 'Saloon') as stored in Firebase.

// $pricing = null; // Initialize pricing variable

// try {
//     // Query Firebase Realtime Database
//     // 1. Reference the 'mileage_pricing' node
//     $query = $this->database->getReference('mileage_pricing');
//         // 2. Order by the 'car_type' child key
//         ->orderByChild('car_type')
//         // 3. Find the exact match for the vehicle type
//         ->equalTo($vehicleId);
//         // 4. Limit to the first result
//     $snapshot = $query->getValue();
    
//     // echo $vehicleId;
//     // die;
    
//     $query =$this->database
//     ->getReference('mileage_pricing')
//     ->orderByChild('car_type')
//     ->equalTo($vehicleId)
//     ->getValue();
    
    
//     // echo $query;
//     // die;
    
//     // 🚨 REMOVED DEBUG LINES: 
//     // return response()->json(['data' => $query]);
//     // exit(); 
    
//     // Check if a result was found
//     if (!empty($snapshot)) {
//         // The result is an associative array where the key is the random Firebase ID 
//         // We take the first (and only) item in that array.
//         $recordKey = array_key_first($snapshot);
//         $pricing = (object)$snapshot[$recordKey]; // Cast to object
//     }
// } catch (\Exception $e) {
//     // Log any errors that occur during the Firebase fetch
//     \Log::error('Firebase Mileage Pricing Error: ' . $e->getMessage(), ['vehicle_id' => $vehicleId]);
// }


// if (!$pricing) {
//     return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
// }

// // 3. Calculate Distances (Assumed to calculate in miles by $this->calculateDistance)
// $distBaseToPickup = $this->calculateDistance($basePostcode, $pickup);
// $distPickupToDrop = $this->calculateDistance($pickup, $dropoff);
// $distDropToBase   = $this->calculateDistance($dropoff, $basePostcode);

// // 4. Convert to Prices using per-mile rate and check minimum
// // Use the fields from the Firebase structure: cost_per_mileage and minimum_price
// $perMileRate = (float)($pricing->cost_per_mileage ?? 0.0);
// $minimumPrice = (float)($pricing->minimum_price ?? 0.0); // Use minimum_price field

// if ($perMileRate <= 0) {
//     return response()->json(['success' => false, 'message' => 'Invalid per mile rate found.']);
// }

// // Calculate segment prices
// $priceBaseToPickup = $distBaseToPickup * $perMileRate;
// $pricePickupToDrop = $distPickupToDrop * $perMileRate;
// $priceDropToBase   = $distDropToBase * $perMileRate;

// // 5. Apply Formula
// // The formula assumes the total mileage is (Base->Pickup + Pickup->Drop + Drop->Base) / 2
// $totalPrice = ($priceBaseToPickup + $pricePickupToDrop + $priceDropToBase) / 2;

// // 6. Apply Minimum Price Logic
// if ($totalPrice < $minimumPrice) {
//     \Log::info('Mileage price adjusted to minimum.', ['original' => $totalPrice, 'minimum' => $minimumPrice]);
//     $totalPrice = $minimumPrice;
// }


// return response()->json([
//     'success'   => true,
//     'price'     => round($totalPrice, 2),
//     'distances' => [
//         'base_to_pickup' => $distBaseToPickup, // Distance in miles (as assumed from context)
//         'pickup_to_drop' => $distPickupToDrop,
//         'drop_to_base'   => $distDropToBase
//     ],
//     'type' => 'mileage'
// ]);
//     // // 2. Mileage Pricing (fallback if no fixed pricing)
//     // $pricing = DB::table('mileage_pricing')
//     //     ->where('vehicle_type', $vehicleId)
//     //     ->first();

//     // if (!$pricing) {
//     //     return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     // }

//     // // 3. Calculate Distances
//     // $distBaseToPickup = $this->calculateDistance($basePostcode, $pickup);
//     // $distPickupToDrop = $this->calculateDistance($pickup, $dropoff);
//     // $distDropToBase   = $this->calculateDistance($dropoff, $basePostcode);

//     // // 4. Convert to Prices using per-mile rate
//     // $perMileRate = (float)($pricing->per_mile_rate ?? 2.0);
//     // $priceBaseToPickup = $distBaseToPickup * $perMileRate;
//     // $pricePickupToDrop = $distPickupToDrop * $perMileRate;
//     // $priceDropToBase   = $distDropToBase * $perMileRate;

//     // // 5. Apply Formula
//     // $totalPrice = ($priceBaseToPickup + $pricePickupToDrop + $priceDropToBase) / 2;

//     // return response()->json([
//     //     'success'   => true,
//     //     'price'     => round($totalPrice, 2),
//     //     'distances' => [
//     //         'base_to_pickup' => $distBaseToPickup,
//     //         'pickup_to_drop' => $distPickupToDrop,
//     //         'drop_to_base'   => $distDropToBase
//     //     ],
//     //     'type' => 'mileage'
//     // ]);
// }

// Perfected version of getPrice method on 2025-10-09
// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup    = strtoupper(trim($request->pickup));
//     $dropoff   = strtoupper(trim($request->dropoff));

//     $basePostcode = "RG1 1LZ"; // fixed base location

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }

//     /**
//      * 1. Try Fixed Price (Firebase)
//      */
//     $fixedPricing = [];
//     try {
//         $snapshot = $this->database
//             ->getReference('fixed_pricing')
//             ->orderByChild('vehicle_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 // Ensure both from_postcode & to_postcode match
//                 if (
//                     strtoupper(trim($record['from_postcode'] ?? '')) === $pickup &&
//                     strtoupper(trim($record['to_postcode'] ?? '')) === $dropoff
//                 ) {
//                     $fixedPricing = $record;
//                     break;
//                 }
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Fixed Pricing Error: ' . $e->getMessage(), ['vehicle_id' => $vehicleId]);
//     }

//     if (!empty($fixedPricing)) {
//         $company = (float)($fixedPricing['company_price'] ?? 0);
//         $driver  = (float)($fixedPricing['driver_price'] ?? 0);
//         $agent   = (float)($fixedPricing['agent_commission'] ?? 0);

//         $price = $company + $driver + $agent;

//         return response()->json([
//             'success' => true,
//             'price'   => round($price, 2),
//             'type'    => 'fixed'
//         ]);
//     }

//     /**
//      * 2. Mileage Pricing (Firebase)
//      */
//     $pricingRecords = [];
    
//     // echo $vehicleId;
//     try {
//         $snapshot = $this->database
//             ->getReference('mileage_pricing')
//             ->orderByChild('car_type')
//             ->equalTo($vehicleId)
//             ->getValue();
            
//         // dd($snapshot);    

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 $pricingRecords[] = (object)$record;
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Mileage Pricing Error: ' . $e->getMessage(), ['vehicle_id' => $vehicleId]);
//     }

//     if (empty($pricingRecords)) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 3. Calculate Total Distance
//      */
//     $distBaseToPickup = $this->calculateDistance($basePostcode, $pickup);
//     $distPickupToDrop = $this->calculateDistance($pickup, $dropoff);
//     $distDropToBase   = $this->calculateDistance($dropoff, $basePostcode);

//     $totalDistance = $distBaseToPickup + $distPickupToDrop + $distDropToBase;



// // Sort and select correct tier based on mileage bracket
// $selectedPricing = null;

// foreach ($pricingRecords as $record) {
//     $bracket = trim($record->mileage_bracket ?? '');

//     // Normalize different dash types and remove spaces
//     $bracket = str_replace(['–', '—', 'to', ' '], ['-', '-', '-', ''], $bracket);

//     // Now split into min/max
//     $rangeParts = explode('-', $bracket);

//     // Validate the format
//     if (count($rangeParts) < 2) {
//         // Skip invalid bracket
//         continue;
//     }

//     $min = (float)$rangeParts[0];
//     $max = (float)$rangeParts[1];

//     if ($totalDistance >= $min && $totalDistance <= $max) {
//         $selectedPricing = $record;
//         break;
//     }
// }

// // fallback if no range matched
// if (!$selectedPricing && !empty($pricingRecords)) {
//     $selectedPricing = end($pricingRecords);
// }

//     /**
//      * 4. Select Correct Mileage Tier
//      */
//     // usort($pricingRecords, function ($a, $b) {
//     //     return $a->mileage <=> $b->mileage;
//     // });

//     // $selectedPricing = null;
//     // foreach ($pricingRecords as $record) {
//     //     if ($totalDistance <= (float)$record->mileage) {
//     //         $selectedPricing = $record;
//     //         break;
//     //     }
//     // }

//     // if (!$selectedPricing) {
//     //     $selectedPricing = end($pricingRecords);
//     // }

//     if (!$selectedPricing) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 5. Calculate Price
//      */
//     $perMileRate  = (float)($selectedPricing->cost_per_mileage ?? 0.0);
//     $minimumPrice = (float)($selectedPricing->minimum_price ?? 0.0);

//     if ($perMileRate <= 0) {
//         return response()->json(['success' => false, 'message' => 'Invalid per mile rate found.']);
//     }

//     $priceBaseToPickup = $distBaseToPickup * $perMileRate;
//     $pricePickupToDrop = $distPickupToDrop * $perMileRate;
//     $priceDropToBase   = $distDropToBase * $perMileRate;

//     $totalPrice = ($priceBaseToPickup + $pricePickupToDrop + $priceDropToBase) / 2;

//     if ($totalPrice < $minimumPrice) {
//         $totalPrice = $minimumPrice;
//     }

//     return response()->json([
//         'success'   => true,
//         'price'     => round($totalPrice, 2),
//         'distances' => [
//             'base_to_pickup' => $distBaseToPickup,
//             'pickup_to_drop' => $distPickupToDrop,
//             'drop_to_base'   => $distDropToBase
//         ],
//         'type' => 'mileage',
//         'applied_tier' => [
//             // 'mileage'          => $selectedPricing->mileage,
//             'mileage'   => (float)($selectedPricing->cost_per_mile ?? 0.0),
//             'cost_per_mileage' => $selectedPricing->cost_per_mileage,
//             'minimum_price'    => $selectedPricing->minimum_price
//         ]
//     ]);
// }

//Perfected version at 2025-10-10
// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup    = strtoupper(trim($request->pickup));
//     $dropoff   = strtoupper(trim($request->dropoff));
//     $vias      = $request->vias ?? []; // Expecting array of via addresses like ['SL1 2AA', 'OX1 1ZZ']

//     $basePostcode = "RG1 1LZ"; // fixed base location

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }

//     /**
//      * 1. Try Fixed Price (Firebase)
//      */
//     try {
//         $snapshot = $this->database
//             ->getReference('fixed_pricing')
//             ->orderByChild('vehicle_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 if (
//                     strtoupper(trim($record['from_postcode'] ?? '')) === $pickup &&
//                     strtoupper(trim($record['to_postcode'] ?? '')) === $dropoff
//                 ) {
//                     $company = (float)($record['company_price'] ?? 0);
//                     $driver  = (float)($record['driver_price'] ?? 0);
//                     $agent   = (float)($record['agent_commission'] ?? 0);

//                     $price = $company + $driver + $agent;

//                     return response()->json([
//                         'success' => true,
//                         'price'   => round($price, 2),
//                         'type'    => 'fixed'
//                     ]);
//                 }
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Fixed Pricing Error: ' . $e->getMessage(), ['vehicle_id' => $vehicleId]);
//     }

//     /**
//      * 2. Mileage Pricing (Firebase)
//      */
//     $pricingRecords = [];
//     try {
//         $snapshot = $this->database
//             ->getReference('mileage_pricing')
//             ->orderByChild('car_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 $pricingRecords[] = (object)$record;
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Mileage Pricing Error: ' . $e->getMessage(), ['vehicle_id' => $vehicleId]);
//     }

//     if (empty($pricingRecords)) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 3. Calculate Total Distance (with multiple vias)
//      */
//     $totalDistance = 0;
//     $segments = [];

//     // Build route sequence: base → pickup → via1 → via2 → ... → dropoff → base
//     $routePoints = array_filter(array_merge([$basePostcode, $pickup], $vias, [$dropoff, $basePostcode]));

//     for ($i = 0; $i < count($routePoints) - 1; $i++) {
//         $from = $routePoints[$i];
//         $to   = $routePoints[$i + 1];
//         $distance = $this->calculateDistance($from, $to);
//         $totalDistance += $distance;
//         $segments[] = [
//             'from' => $from,
//             'to'   => $to,
//             'distance' => $distance
//         ];
//     }

//     /**
//      * 4. Select Correct Mileage Tier
//      */
//     $selectedPricing = null;
//     foreach ($pricingRecords as $record) {
//         $bracket = trim($record->mileage_bracket ?? '');
//         $bracket = str_replace(['–', '—', 'to', ' '], ['-', '-', '-', ''], $bracket);
//         $rangeParts = explode('-', $bracket);

//         if (count($rangeParts) < 2) continue;

//         $min = (float)$rangeParts[0];
//         $max = (float)$rangeParts[1];

//         if ($totalDistance >= $min && $totalDistance <= $max) {
//             $selectedPricing = $record;
//             break;
//         }
//     }

//     if (!$selectedPricing && !empty($pricingRecords)) {
//         $selectedPricing = end($pricingRecords);
//     }

//     if (!$selectedPricing) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 5. Calculate Price
//      */
//     $perMileRate  = (float)($selectedPricing->cost_per_mileage ?? 0.0);
//     $minimumPrice = (float)($selectedPricing->minimum_price ?? 0.0);

//     if ($perMileRate <= 0) {
//         return response()->json(['success' => false, 'message' => 'Invalid per mile rate found.']);
//     }

//     // Apply rate to total distance
//     $totalPrice = $totalDistance * $perMileRate;

//     // Apply minimum price rule
//     if ($totalPrice < $minimumPrice) {
//         $totalPrice = $minimumPrice;
//     }

//     return response()->json([
//         'success'   => true,
//         'price'     => round($totalPrice, 2),
//         'total_distance' => round($totalDistance, 2),
//         'segments'  => $segments,
//         'type'      => 'mileage',
//         'applied_tier' => [
//             'mileage_bracket'  => $selectedPricing->mileage_bracket ?? '',
//             'cost_per_mileage' => $selectedPricing->cost_per_mileage,
//             'minimum_price'    => $selectedPricing->minimum_price
//         ]
//     ]);
// }


// private function calculateDistance($from, $to)
// {
//     try {
//         // 1. Geocode "from" address → lat/lng
//         $fromCoords = $this->geocodeAddress($from);
//         $toCoords   = $this->geocodeAddress($to);

//         if (!$fromCoords || !$toCoords) {
//             return 0; // Fallback if geocoding fails
//         }

//         // 2. Call OSRM API for driving route
//         $url = "http://router.project-osrm.org/route/v1/driving/"
//              . "{$fromCoords['lng']},{$fromCoords['lat']};"
//              . "{$toCoords['lng']},{$toCoords['lat']}?overview=false";

//         $response = file_get_contents($url);
//         $data = json_decode($response, true);

//         if (isset($data['routes'][0]['distance'])) {
//             // OSRM gives meters → convert to miles
//             $meters = $data['routes'][0]['distance'];
//             $miles  = $meters / 1609.34;
//             return round($miles, 2);
//         }
//     } catch (\Exception $e) {
//         \Log::error('Distance calculation failed: ' . $e->getMessage());
//     }

//     return 0; // fallback
// }


// Perfect version
// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup    = strtoupper(trim($request->pickup));
//     $dropoff   = strtoupper(trim($request->dropoff));
//     $vias      = $request->vias ?? []; // Expecting array like ['SL1 2AA', 'OX1 1ZZ']
//     $basePostcode = "RG1 1LZ"; // Fixed base location

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }

//     /**
//      * 1️⃣ Try Fixed Price (Firebase)
//      */
//     try {
//         $snapshot = $this->database
//             ->getReference('fixed_pricing')
//             ->orderByChild('vehicle_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 $from = strtoupper(trim($record['from_postcode'] ?? ''));
//                 $to   = strtoupper(trim($record['to_postcode'] ?? ''));

//                 if ($from === $pickup && $to === $dropoff) {
//                     $company = (float)($record['company_price'] ?? 0);
//                     $driver  = (float)($record['driver_price'] ?? 0);
//                     $agent   = (float)($record['agent_commission'] ?? 0);

//                     $price = $company + $driver + $agent;

//                     return response()->json([
//                         'success' => true,
//                         'price'   => round($price, 2),
//                         'type'    => 'fixed',
//                         'vias'    => $vias
//                     ]);
//                 }
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Fixed Pricing Error: ' . $e->getMessage());
//     }

//     /**
//      * 2️⃣ Get Mileage Pricing (Firebase)
//      */
//     $pricingRecords = [];
//     try {
//         $snapshot = $this->database
//             ->getReference('mileage_pricing')
//             ->orderByChild('car_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 $pricingRecords[] = (object)$record;
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Mileage Pricing Error: ' . $e->getMessage());
//     }

//     if (empty($pricingRecords)) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 3️⃣ Build Route with Vias → base → pickup → via(s) → dropoff → base
//      */
//     $routePoints = array_filter(array_merge([$basePostcode, $pickup], $vias, [$dropoff, $basePostcode]));
//     $totalDistance = 0;
//     $segments = [];

//     for ($i = 0; $i < count($routePoints) - 1; $i++) {
//         $from = $routePoints[$i];
//         $to   = $routePoints[$i + 1];

//         $distance = $this->calculateDistance($from, $to);
//         $segments[] = [
//             'from' => $from,
//             'to'   => $to,
//             'distance' => $distance
//         ];
//         $totalDistance += $distance;
//     }

//     /**
//      * 4️⃣ Select Correct Mileage Tier
//      */
//     $selectedPricing = null;
//     foreach ($pricingRecords as $record) {
//         $bracket = str_replace(['–', '—', 'to', ' '], ['-', '-', '-', ''], trim($record->mileage_bracket ?? ''));
//         $range = explode('-', $bracket);

//         if (count($range) === 2) {
//             $min = (float)$range[0];
//             $max = (float)$range[1];
//             if ($totalDistance >= $min && $totalDistance <= $max) {
//                 $selectedPricing = $record;
//                 break;
//             }
//         }
//     }

//     if (!$selectedPricing && !empty($pricingRecords)) {
//         $selectedPricing = end($pricingRecords); // fallback to last tier
//     }

//     if (!$selectedPricing) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 5️⃣ Calculate Price
//      */
//     $perMileRate  = (float)($selectedPricing->cost_per_mileage ?? 0.0);
//     $minimumPrice = (float)($selectedPricing->minimum_price ?? 0.0);

//     if ($perMileRate <= 0) {
//         return response()->json(['success' => false, 'message' => 'Invalid per mile rate found.']);
//     }

//     $totalPrice = $totalDistance * $perMileRate;
//     if ($totalPrice < $minimumPrice) {
//         $totalPrice = $minimumPrice;
//     }

//     return response()->json([
//         'success'   => true,
//         'type'      => 'mileage',
//         'price'     => round($totalPrice, 2),
//         'total_distance' => round($totalDistance, 2),
//         'segments'  => $segments,
//         'vias'      => $vias,
//         'applied_tier' => [
//             'mileage_bracket'  => $selectedPricing->mileage_bracket ?? '',
//             'cost_per_mileage' => $selectedPricing->cost_per_mileage ?? 0,
//             'minimum_price'    => $selectedPricing->minimum_price ?? 0
//         ]
//     ]);
// }

//2026-05-16
// Put this helper inside the same controller class (private method)
// private function getSurchargePercent(string $pickup, string $dropoff, int $bookingTimestamp = null): float
// {
//     try {
//         $surData = $this->database->getReference('surcharges')->getValue() ?? [];

//         $pickup = strtoupper(trim($pickup));
//         $dropoff = strtoupper(trim($dropoff));

//         // If booking timestamp not provided, fall back to "now"
//         $bookingTimestamp = $bookingTimestamp ?: time();

//         foreach ($surData as $row) {
//             $sPick = strtoupper(trim($row['pickup'] ?? ''));
//             $sDrop = strtoupper(trim($row['dropoff'] ?? ''));
//             $percent = floatval($row['surcharge'] ?? 0);

//             if ($percent <= 0) continue;

//             // Match rules:
//             // - exact match OR surcharge row has "OVERALL" for pickup/dropoff
//             // treat empty as not matching; prefer explicit OVERALL token
//             $pickupMatch = ($sPick === 'OVERALL') || ($sPick !== '' && $sPick === $pickup);
//             $dropoffMatch = ($sDrop === 'OVERALL') || ($sDrop !== '' && $sDrop === $dropoff);

//             // require both sides to match (accounting for OVERALL)
//             if (!($pickupMatch && $dropoffMatch)) continue;

//             // Time window check (use inclusive bounds)
//             $from = strtotime(($row['from_date'] ?? '') . ' ' . ($row['from_time'] ?? '00:00'));
//             $to   = strtotime(($row['to_date'] ?? '')   . ' ' . ($row['to_time'] ?? '23:59'));

//             // If dates are not provided or invalid, skip that row
//             if (!$from || !$to) continue;

//             if ($bookingTimestamp >= $from && $bookingTimestamp <= $to) {
//                 return $percent;
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error("Surcharge Error: " . $e->getMessage());
//     }

//     return 0.0;
// }

private function matchesLocation(string $rule, string $value): bool
{
    $rule = strtoupper(trim($rule));
    $value = strtoupper(trim($value));

    // Wildcards
    if ($rule === '*' || $rule === 'OVERALL') {
        return true;
    }

    // Empty rules should not match
    if ($rule === '') {
        return false;
    }

    // Partial match
    return str_contains($value, $rule);
}

private function getSurchargePercent(string $pickup, string $dropoff, ?int $bookingTimestamp = null): float
{
    try {

        $surData = $this->database
            ->getReference('surcharges')
            ->getValue() ?? [];

        $pickup  = strtoupper(trim($pickup));
        $dropoff = strtoupper(trim($dropoff));

        // fallback to current time
        $bookingTimestamp = $bookingTimestamp ?: time();

        foreach ($surData as $row) {

            $sPick = strtoupper(trim($row['pickup'] ?? ''));
            $sDrop = strtoupper(trim($row['dropoff'] ?? ''));

            $percent = (float)($row['surcharge'] ?? 0);

            if ($percent <= 0) {
                continue;
            }

            // ✅ Match pickup/dropoff
            $pickupMatch  = $this->matchesLocation($sPick, $pickup);
            $dropoffMatch = $this->matchesLocation($sDrop, $dropoff);

            if (!($pickupMatch && $dropoffMatch)) {
                continue;
            }

            // ✅ Build datetime range
            $fromDate = trim($row['from_date'] ?? '');
            $fromTime = trim($row['from_time'] ?? '00:00');

            $toDate = trim($row['to_date'] ?? '');
            $toTime = trim($row['to_time'] ?? '23:59');

            $from = strtotime($fromDate . ' ' . $fromTime);
            $to   = strtotime($toDate . ' ' . $toTime);

            // invalid date range
            if (!$from || !$to) {
                continue;
            }

            // ✅ Check booking time inside range
            if ($bookingTimestamp >= $from && $bookingTimestamp <= $to) {

                \Log::info('SURCHARGE APPLIED', [
                    'pickup' => $pickup,
                    'dropoff' => $dropoff,
                    'matched_pickup' => $sPick,
                    'matched_dropoff' => $sDrop,
                    'surcharge_percent' => $percent,
                    'booking_time' => date('Y-m-d H:i:s', $bookingTimestamp),
                    'valid_from' => date('Y-m-d H:i:s', $from),
                    'valid_to' => date('Y-m-d H:i:s', $to),
                ]);

                return $percent;
            }
        }

    } catch (\Exception $e) {

        \Log::error("Surcharge Error: " . $e->getMessage());

    }

    return 0.0;
}

private function isLondonPostcode($postcode)
{
    $postcode = strtoupper(trim($postcode));

    return preg_match('/^(E|EC|W|WC|N|NW|SE|SW|SA|CM)/', $postcode);
}

private function normalizePricingPostcode(?string $postcode): string
{
    return preg_replace('/\s+/', ' ', strtoupper(trim((string) $postcode))) ?? '';
}

private function getMatchingFixedPrices(string $pickup, string $dropoff): array
{
    try {
        $pickup = $this->normalizePricingPostcode($pickup);
        $dropoff = $this->normalizePricingPostcode($dropoff);

        return FixedPrice::query()
            ->where(function ($query) use ($pickup, $dropoff) {
                $query->where(function ($query) use ($pickup, $dropoff) {
                    $query->where('from_postcode', $pickup)
                        ->where('to_postcode', $dropoff);
                })->orWhere(function ($query) use ($pickup, $dropoff) {
                    $query->where('from_postcode', $dropoff)
                        ->where('to_postcode', $pickup);
                });
            })
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (FixedPrice $price) => [$price->id => [
                'from_postcode' => $price->from_postcode,
                'to_postcode' => $price->to_postcode,
                'Saloon' => (float) $price->saloon,
                'Estate' => (float) $price->estate,
                'MPV' => (float) $price->mpv,
                '8 Seater' => (float) $price->seater_8,
                'Executive' => (float) $price->executive,
            ]])
            ->all();
    } catch (\Throwable $e) {
        \Log::error('MySQL fixed-price lookup failed: '.$e->getMessage());
        return [];
    }
}

private function getPricingPercentages(): array
{
    $settings = PricingPercentage::query()->find(1);

    return [
        'estate' => (float) ($settings?->estate ?? 0),
        'mpv' => (float) ($settings?->mpv ?? 0),
        'seater8' => (float) ($settings?->seater8 ?? 0),
        'executive' => (float) ($settings?->executive ?? 0),
    ];
}

private function getMileagePricingRecords(string $vehicleId): array
{
    return MileagePrice::query()
        ->where('car_type', $vehicleId)
        ->orderBy('from_mileage')
        ->get()
        ->map(fn (MileagePrice $price) => [
            'car_type' => $price->car_type,
            'from_mileage' => (float) $price->from_mileage,
            'to_mileage' => (float) $price->to_mileage,
            'cost_per_mileage' => (float) $price->cost_per_mileage,
            'minimum_price' => (float) $price->minimum_price,
        ])
        ->all();
}

public function getPrice(Request $request)
{
    $vehicleId = $request->vehicle_id;
    $pickup    = $this->normalizePricingPostcode($request->pickup);
    $dropoff   = $this->normalizePricingPostcode($request->dropoff);
    $vias      = $request->vias ?? []; // Expecting array like ['SL1 2AA', 'OX1 1ZZ']
    $basePostcode = 'RG1 1LZ';
    
    
    // echo $pickup;
    // echo $dropoff;
    // die;
    
    
    

    if (!$vehicleId || !$pickup || !$dropoff) {
        return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
    }
    
    $londonSurcharge = 0;
    


if ($this->isLondonPostcode($pickup) || $this->isLondonPostcode($dropoff)) {
    $londonSurcharge = 20; // £20 extra
}

    // Determine booking timestamp from request if provided, otherwise fallback to now
    // Accept common field names: pickup_date + pickup_time; fallback to request->date/request->time if present.
    $pickupDate = $request->pickup_date ?? $request->date ?? null;
    $pickupTime = $request->pickup_time ?? $request->time ?? null;

    if ($pickupDate && $pickupTime) {
        $bookingTimestamp = strtotime(trim($pickupDate) . ' ' . trim($pickupTime));
        if ($bookingTimestamp === false) $bookingTimestamp = time();
    } else {
        // fallback: if user didn't provide booking datetime, use current time
        $bookingTimestamp = time();
    }

    // Get surcharge percent based on booking pickup datetime & pickup/dropoff
    $surchargePercent = $this->getSurchargePercent($pickup, $dropoff, $bookingTimestamp);
    // surchargePercent is a percentage (e.g. 15.0 for 15%)
    
    if (empty($vias)) {

    /**
     * 1️⃣ Try Fixed Price (MySQL)
     */
    try {
        // 1️⃣ Fetch latest percentage settings
        $latestPercentages = $this->getPricingPercentages();

        // 2️⃣ Fetch only rows that can match this journey
        $snapshot = $this->getMatchingFixedPrices($pickup, $dropoff);

        if (!empty($snapshot)) {
            foreach ($snapshot as $key => $record) {
                $from = strtoupper(trim($record['from_postcode'] ?? ''));
                $to   = strtoupper(trim($record['to_postcode'] ?? ''));

                // Match either direction (pickup→dropoff OR dropoff→pickup)
                if (
                    ($from === $pickup && $to === $dropoff) ||
                    ($from === $dropoff && $to === $pickup)
                ) {
                    $saloonFare = (float)($record['Saloon'] ?? 0);

                    // if base saloon fare is 0, skip
                    if ($saloonFare <= 0) continue;

                    // Apply dynamic percentage logic
                    $estateFare     = $saloonFare;
                    $seater6Fare    = $saloonFare;
                    $seater7_8Fare  = $saloonFare;
                    $seater12_16Fare = $saloonFare;

                    if ($latestPercentages) {
                        $estateFare      = $saloonFare + ($saloonFare * ($latestPercentages['estate'] ?? 0) / 100);
                        $seater6Fare     = $saloonFare + ($saloonFare * ($latestPercentages['mpv'] ?? 0) / 100);
                        $seater7_8Fare   = $saloonFare + ($saloonFare * ($latestPercentages['seater8'] ?? 0) / 100);
                        $seater12_16Fare = $saloonFare + ($saloonFare * ($latestPercentages['executive'] ?? 0) / 100);
                    }

                    // Choose price by vehicle type (base fare)
                    $price = match ($vehicleId) {
                        'Saloon'    => $saloonFare,
                        'Estate'    => $estateFare,
                        'MPV'       => $seater6Fare,
                        '8 Seater'  => $seater7_8Fare,
                        'Executive' => $seater12_16Fare,
                        default     => $saloonFare
                    };

                    // Calculate total & journey distances
                    $fullRoutePoints = array_values(array_filter(array_merge([$pickup], $vias, [$dropoff])));
                    $totalDistance = 0;
                    $journeyDistance = 0;
                    $segments = [];

                    for ($i = 0; $i < count($fullRoutePoints) - 1; $i++) {
                        $fromSeg = $fullRoutePoints[$i];
                        $toSeg   = $fullRoutePoints[$i + 1];
                        $distance = $this->calculateDistance($fromSeg, $toSeg);
                        $segments[] = [
                            'from' => $fromSeg,
                            'to'   => $toSeg,
                            'distance' => $distance
                        ];
                        $totalDistance += $distance;

                        $journeyDistance += $distance;
                    }

                    // Apply surcharge percent (Option B: only on base fare)
                    $basePrice = $price;
                    $surchargeAmount = 0.0;
                    if ($surchargePercent > 0) {
                        $surchargeAmount = ($basePrice * ($surchargePercent / 100));
                    }
                    $finalPrice = $basePrice + $surchargeAmount + $londonSurcharge;

                    // Return fixed price result
                    return response()->json([
                        'success' => true,
                        'type' => 'fixed',
                        'price' => round($finalPrice, 2),
                        'base_price' => round($basePrice, 2),
                        'surcharge_percent' => $surchargePercent,
                        'surcharge_amount' => round($surchargeAmount, 2),
                        'vehicle_type' => $vehicleId,
                        'pickup' => $pickup,
                        'dropoff' => $dropoff,
                        'applied_percentages' => $latestPercentages,
                        'vias' => $vias,
                        'total_distance' => round($totalDistance, 2),
                        'journey_distance' => round($journeyDistance, 2),
                        'segments' => $segments,
                    ]);
                }
            }
        }
    } catch (\Exception $e) {
        \Log::error('MySQL Fixed Pricing Error: ' . $e->getMessage());
    }
    
    
    /**
     * 2️⃣ Get Mileage Pricing (MySQL)
     */
    $pricingRecords = [];
    try {
        $snapshot = $this->getMileagePricingRecords($vehicleId);

        if (!empty($snapshot)) {
            foreach ($snapshot as $record) {
                $pricingRecords[] = (object)$record;
            }
        }
    } catch (\Exception $e) {
        \Log::error('MySQL Mileage Pricing Error: ' . $e->getMessage());
    }

    if (empty($pricingRecords)) {
        return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
    }
    
    /**
     * 3️⃣ Build Route & Calculate Distances
     */
    $fullRoutePoints = array_values(array_filter(array_merge([$pickup], $vias, [$dropoff])));
    $totalDistance = 0;
    $journeyDistance = 0;
    $segments = [];

    for ($i = 0; $i < count($fullRoutePoints) - 1; $i++) {
        $fromSeg = $fullRoutePoints[$i];
        $toSeg   = $fullRoutePoints[$i + 1];

        $distance = $this->calculateDistance($fromSeg, $toSeg);
        $segments[] = [
            'from' => $fromSeg,
            'to'   => $toSeg,
            'distance' => $distance
        ];
        $totalDistance += $distance;

        $journeyDistance += $distance;
    }

    // ----------------------------------------------------
    // CORRECT MILEAGE FORMULA
    //
    // Route:
    // Base -> Pickup -> Via(s) -> Dropoff -> Base
    //
    // Base leg  = Base -> Pickup
    // Journey   = Pickup -> Via(s) -> Dropoff
    // Return leg= Dropoff -> Base
    //
    // IMPORTANT:
    // Divide the WHOLE route distance by 2.
    // (Base->Pickup + Journey + Dropoff->Base) / 2
    // ----------------------------------------------------
    $baseToPickupDistance = $this->calculateDistance($basePostcode, $pickup);
    $customerJourneyDistance = $journeyDistance;
    $dropToBaseDistance = $this->calculateDistance($dropoff, $basePostcode);

    $fullMileageRouteDistance =
        $baseToPickupDistance
        + $customerJourneyDistance
        + $dropToBaseDistance;

    /**
     * 4️⃣ APPLY CUMULATIVE TIER FORMULA
     * Chargeable distance = full Base -> A -> B -> Base route / 2
     */
    usort($pricingRecords, function($a, $b) {
        return ((float)$a->from_mileage) <=> ((float)$b->from_mileage);
    });

    $effectiveDistance = max(
        0.0,
        $fullMileageRouteDistance / 2
    );
    $prev_to   = 0.0;
    $totalPrice = 0.0;
    $breakdown = [];

    foreach ($pricingRecords as $record) {
        $from = (float)$record->from_mileage;
        $to   = (float)$record->to_mileage;
        $rate = (float)$record->cost_per_mileage;
        $min  = (float)$record->minimum_price;

        // skip invalid bracket
        if ($to <= $from) {
            $prev_to = max($prev_to, $to);
            continue;
        }

        if ($prev_to >= $effectiveDistance) break;

        $cap = min($effectiveDistance, $to);
        $used = $cap - $prev_to;

        if ($used <= 0) {
            $prev_to = max($prev_to, $to);
            continue;
        }

        if ($rate <= 0) {
            $cost = $min;
            $logic = "minimum_applied";
        } else {
            $cost = $used * $rate;
            $logic = "rate_applied";
        }

        $breakdown[] = [
            "from"        => $from,
            "to"          => $to,
            "miles_used"  => round($used, 2),
            "rate"        => $rate,
            "minimum"     => $min,
            "cost"        => round($cost, 2),
            "logic"       => $logic
        ];

        $totalPrice += $cost;
        $prev_to = $cap;
    }

    // leftover if still not covered
    if ($prev_to < $effectiveDistance) {
        $last = end($pricingRecords);
        $leftover = $effectiveDistance - $prev_to;

        $rate = (float)($last->cost_per_mileage ?? 0);
        $min  = (float)($last->minimum_price ?? 0);

        if ($rate <= 0) {
            $cost = $min;
            $logic = "minimum_applied";
        } else {
            $cost = $leftover * $rate;
            $logic = "rate_applied";
        }

        $breakdown[] = [
            "from" => (float)$last->from_mileage,
            "to" => (float)$last->to_mileage,
            "miles_used" => round($leftover, 2),
            "rate" => $rate,
            "minimum" => $min,
            "cost" => round($cost, 2),
            "logic" => $logic
        ];

        $totalPrice += $cost;
    }

    $minimumFare = max(array_map(
        fn ($record) => (float) ($record->minimum_price ?? 0),
        $pricingRecords
    ));

    // Never return a zero fare when a mileage minimum is configured.
    $baseMileagePrice = max($totalPrice, $minimumFare);
    $surchargeAmount = 0.0;
    if ($surchargePercent > 0) {
        $surchargeAmount = ($baseMileagePrice * ($surchargePercent / 100));
    }
    $finalMileagePrice = $baseMileagePrice + $surchargeAmount + $londonSurcharge;
    
    
    /**
     * 5️⃣ Return Final Mileage Pricing (cumulative)
     */
    return response()->json([
        "success" => true,
        "type" => "mileage",
        "price" => round($finalMileagePrice, 2),
        "base_price" => round($baseMileagePrice, 2),
        "surcharge_percent" => $surchargePercent,
        "surcharge_amount" => round($surchargeAmount, 2),
        "vehicle_type" => $vehicleId,
        "pickup" => $pickup,
        "dropoff" => $dropoff,
        "total_distance" => round($totalDistance, 2),
        "journey_distance" => round($journeyDistance, 2),
        "base_to_pickup_distance" => round($baseToPickupDistance, 2),
        "customer_journey_distance" => round($customerJourneyDistance, 2),
        "drop_to_base_distance" => round($dropToBaseDistance, 2),
        "full_mileage_route_distance" => round($fullMileageRouteDistance, 2),
        "pricing_divisor" => 2,
        "chargeable_distance" => round($effectiveDistance, 2),
        "breakdown" => $breakdown,
        "segments" => $segments,
        "vias" => $vias
    ]);
    
    
    }
    else
    {

    /**
     * 2️⃣ Get Mileage Pricing (MySQL)
     */
    $pricingRecords = [];
    try {
        $snapshot = $this->getMileagePricingRecords($vehicleId);

        if (!empty($snapshot)) {
            foreach ($snapshot as $record) {
                $pricingRecords[] = (object)$record;
            }
        }
    } catch (\Exception $e) {
        \Log::error('MySQL Mileage Pricing Error: ' . $e->getMessage());
    }

    if (empty($pricingRecords)) {
        return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
    }

    /**
     * 3️⃣ Build Route & Calculate Distances
     */
    $fullRoutePoints = array_values(array_filter(array_merge([$pickup], $vias, [$dropoff])));
    $totalDistance = 0;
    $journeyDistance = 0;
    $segments = [];

    for ($i = 0; $i < count($fullRoutePoints) - 1; $i++) {
        $fromSeg = $fullRoutePoints[$i];
        $toSeg   = $fullRoutePoints[$i + 1];

        $distance = $this->calculateDistance($fromSeg, $toSeg);
        $segments[] = [
            'from' => $fromSeg,
            'to'   => $toSeg,
            'distance' => $distance
        ];
        $totalDistance += $distance;

        $journeyDistance += $distance;
    }

    // ----------------------------------------------------
    // CORRECT MILEAGE FORMULA
    //
    // Route:
    // Base -> Pickup -> Via(s) -> Dropoff -> Base
    //
    // Base leg  = Base -> Pickup
    // Journey   = Pickup -> Via(s) -> Dropoff
    // Return leg= Dropoff -> Base
    //
    // IMPORTANT:
    // Divide the WHOLE route distance by 2.
    // (Base->Pickup + Journey + Dropoff->Base) / 2
    // ----------------------------------------------------
    $baseToPickupDistance = $this->calculateDistance($basePostcode, $pickup);
    $customerJourneyDistance = $journeyDistance;
    $dropToBaseDistance = $this->calculateDistance($dropoff, $basePostcode);

    $fullMileageRouteDistance =
        $baseToPickupDistance
        + $customerJourneyDistance
        + $dropToBaseDistance;

    /**
     * 4️⃣ APPLY CUMULATIVE TIER FORMULA
     * Chargeable distance = full Base -> A -> B -> Base route / 2
     */
    usort($pricingRecords, function($a, $b) {
        return ((float)$a->from_mileage) <=> ((float)$b->from_mileage);
    });

    $effectiveDistance = max(
        0.0,
        $fullMileageRouteDistance / 2
    );
    $prev_to   = 0.0;
    $totalPrice = 0.0;
    $breakdown = [];

    foreach ($pricingRecords as $record) {
        $from = (float)$record->from_mileage;
        $to   = (float)$record->to_mileage;
        $rate = (float)$record->cost_per_mileage;
        $min  = (float)$record->minimum_price;

        // skip invalid bracket
        if ($to <= $from) {
            $prev_to = max($prev_to, $to);
            continue;
        }

        if ($prev_to >= $effectiveDistance) break;

        $cap = min($effectiveDistance, $to);
        $used = $cap - $prev_to;

        if ($used <= 0) {
            $prev_to = max($prev_to, $to);
            continue;
        }

        if ($rate <= 0) {
            $cost = $min;
            $logic = "minimum_applied";
        } else {
            $cost = $used * $rate;
            $logic = "rate_applied";
        }

        $breakdown[] = [
            "from"        => $from,
            "to"          => $to,
            "miles_used"  => round($used, 2),
            "rate"        => $rate,
            "minimum"     => $min,
            "cost"        => round($cost, 2),
            "logic"       => $logic
        ];

        $totalPrice += $cost;
        $prev_to = $cap;
    }

    // leftover if still not covered
    if ($prev_to < $effectiveDistance) {
        $last = end($pricingRecords);
        $leftover = $effectiveDistance - $prev_to;

        $rate = (float)($last->cost_per_mileage ?? 0);
        $min  = (float)($last->minimum_price ?? 0);

        if ($rate <= 0) {
            $cost = $min;
            $logic = "minimum_applied";
        } else {
            $cost = $leftover * $rate;
            $logic = "rate_applied";
        }

        $breakdown[] = [
            "from" => (float)$last->from_mileage,
            "to" => (float)$last->to_mileage,
            "miles_used" => round($leftover, 2),
            "rate" => $rate,
            "minimum" => $min,
            "cost" => round($cost, 2),
            "logic" => $logic
        ];

        $totalPrice += $cost;
    }

    $minimumFare = max(array_map(
        fn ($record) => (float) ($record->minimum_price ?? 0),
        $pricingRecords
    ));

    // Never return a zero fare when a mileage minimum is configured.
    $baseMileagePrice = max($totalPrice, $minimumFare);
    $surchargeAmount = 0.0;
    if ($surchargePercent > 0) {
        $surchargeAmount = ($baseMileagePrice * ($surchargePercent / 100));
    }
    $finalMileagePrice = $baseMileagePrice + $surchargeAmount + $londonSurcharge;
    
    }

    /**
     * 5️⃣ Return Final Mileage Pricing (cumulative)
     */
    return response()->json([
        "success" => true,
        "type" => "mileage",
        "price" => round($finalMileagePrice, 2),
        "base_price" => round($baseMileagePrice, 2),
        "surcharge_percent" => $surchargePercent,
        "surcharge_amount" => round($surchargeAmount, 2),
        "vehicle_type" => $vehicleId,
        "pickup" => $pickup,
        "dropoff" => $dropoff,
        "total_distance" => round($totalDistance, 2),
        "journey_distance" => round($journeyDistance, 2),
        "base_to_pickup_distance" => round($baseToPickupDistance, 2),
        "customer_journey_distance" => round($customerJourneyDistance, 2),
        "drop_to_base_distance" => round($dropToBaseDistance, 2),
        "full_mileage_route_distance" => round($fullMileageRouteDistance, 2),
        "pricing_divisor" => 2,
        "chargeable_distance" => round($effectiveDistance, 2),
        "breakdown" => $breakdown,
        "segments" => $segments,
        "vias" => $vias
    ]);
}


//Perfected version at 2025-11-26
// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup    = strtoupper(trim($request->pickup));
//     $dropoff   = strtoupper(trim($request->dropoff));
//     $vias      = $request->vias ?? []; // Expecting array like ['SL1 2AA', 'OX1 1ZZ']
//     $basePostcode = "RG1 1LZ"; // Fixed base location
    
    
    

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }
    
//     // 🔍 CHECK SURCHARGE
// $surchargeAmount = 0;

// try {
//     $surchargeRecords = $this->database->getReference("surcharges")->getValue();

//     if (!empty($surchargeRecords)) {
//         foreach ($surchargeRecords as $s) {
//             $sPickup  = strtoupper(trim($s['pickup'] ?? ''));
//             $sDropoff = strtoupper(trim($s['dropoff'] ?? ''));
//             $amount   = (float)($s['surcharge'] ?? 0);

//             // Time matching
//             $fromDate = strtotime($s['from_date'] . ' ' . $s['from_time']);
//             $toDate   = strtotime($s['to_date'] . ' ' . $s['to_time']);
//             $now      = time();

//             if ($now >= $fromDate && $now <= $toDate) {
//                 if ($pickup === $sPickup && $dropoff === $sDropoff) {
//                     $surchargeAmount = $amount;
//                 }
//             }
//         }
//     }
// } catch (\Exception $e) {
//     \Log::error("Surcharge read error: " . $e->getMessage());
// }
 

//     /**
//      * 1️⃣ Try Fixed Price (Firebase)
//      */
//     try {
//     // 1️⃣ Fetch latest percentage settings
//     $percentagesData = $this->database->getReference('fixed_pricing_percentages')->getValue();
    
//     // print_r($percentagesData);
//     // die;
//     $latestPercentages = null;

//     if (!empty($percentagesData)) {
//         // $lastKey = array_key_last($percentagesData);
//     //   $latestPercentages = $percentagesData[$lastKey]['percentages'] ?? null;
//       $latestPercentages = $percentagesData['percentages'] ?? null;
//     }
    
//   $latestPercentages = $percentagesData['percentages'] ?? null;

// // echo "<pre>";
// // print_r($latestPercentages);
// // echo "</pre>";
// // die;


//     // 2️⃣ Fetch fixed prices
//     $snapshot = $this->database->getReference('fixed_prices')->getValue();

//     if (!empty($snapshot)) {
//         foreach ($snapshot as $key => $record) {
//             $from = strtoupper(trim($record['from_postcode'] ?? ''));
//             $to   = strtoupper(trim($record['to_postcode'] ?? ''));

//             // Match either direction (pickup→dropoff OR dropoff→pickup)
//             if (
//                 ($from === $pickup && $to === $dropoff) ||
//                 ($from === $dropoff && $to === $pickup)
//             ) {
//                 $saloonFare = (float)($record['Saloon'] ?? 0);

//                 // if base saloon fare is 0, skip
//                 if ($saloonFare <= 0) continue;

//                 // ✅ Apply dynamic percentage logic
//                 $estateFare     = $saloonFare;
//                 $seater6Fare    = $saloonFare;
//                 $seater7_8Fare  = $saloonFare;
//                 $seater12_16Fare = $saloonFare;

//                 if ($latestPercentages) {
//                     $estateFare      = $saloonFare + ($saloonFare * ($latestPercentages['estate'] ?? 0) / 100);
//                     $seater6Fare     = $saloonFare + ($saloonFare * ($latestPercentages['mpv'] ?? 0) / 100);
//                     $seater7_8Fare   = $saloonFare + ($saloonFare * ($latestPercentages['seater8'] ?? 0) / 100);
//                     $seater12_16Fare = $saloonFare + ($saloonFare * ($latestPercentages['executive'] ?? 0) / 100);
//                 }
                
//                 // echo $vehicleId;
                
//                 // echo $estateFare;
                
//                 // die;

//                 // ✅ Choose price by vehicle type
//                 $price = match ($vehicleId) {
//                     'Saloon'       => $saloonFare,
//                     'Estate'       => $estateFare,
//                     'MPV'      => $seater6Fare,
//                     '8 Seater'    => $seater7_8Fare,
//                     'Executive'  => $seater12_16Fare,
//                     default        => $saloonFare
//                 };

//                 // ✅ Calculate total & journey distances (same logic)
//                 $fullRoutePoints = array_filter(array_merge([$basePostcode, $pickup], $vias, [$dropoff, $basePostcode]));
//                 $totalDistance = 0;
//                 $journeyDistance = 0;
//                 $segments = [];

//                 for ($i = 0; $i < count($fullRoutePoints) - 1; $i++) {
//                     $from = $fullRoutePoints[$i];
//                     $to   = $fullRoutePoints[$i + 1];
//                     $distance = $this->calculateDistance($from, $to);
//                     $segments[] = [
//                         'from' => $from,
//                         'to'   => $to,
//                         'distance' => $distance
//                     ];
//                     $totalDistance += $distance;

//                     if ($i >= 1 && $i <= count($fullRoutePoints) - 3) {
//                         $journeyDistance += $distance;
//                     }
//                 }

//                 // ✅ Return fixed price result
//                 return response()->json([
//                     'success' => true,
//                     'type' => 'fixed',
//                     'price' => round($price, 2),
//                     'vehicle_type' => $vehicleId,
//                     'pickup' => $pickup,
//                     'dropoff' => $dropoff,
//                     'applied_percentages' => $latestPercentages,
//                     'vias' => $vias,
//                     'total_distance' => round($totalDistance, 2),
//                     'journey_distance' => round($journeyDistance, 2),
//                 ]);
//             }
//         }
//     }
// } catch (\Exception $e) {
//     \Log::error('Firebase Fixed Pricing Error: ' . $e->getMessage());
// }


//     /**
//      * 2️⃣ Get Mileage Pricing (Firebase)
//      */
//     $pricingRecords = [];
//     try {
//         $snapshot = $this->database
//             ->getReference('mileage_pricing')
//             ->orderByChild('car_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 $pricingRecords[] = (object)$record;
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Mileage Pricing Error: ' . $e->getMessage());
//     }

//     if (empty($pricingRecords)) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 3️⃣ Build Route & Calculate Distances
//      */
//     // Full route for total distance calculation (for tier selection)
//     $fullRoutePoints = array_filter(array_merge([$basePostcode, $pickup], $vias, [$dropoff, $basePostcode]));
//     $totalDistance = 0; // Distance for pricing tier calculation (includes base legs)
//     $journeyDistance = 0; // NEW: Distance for pickup -> via(s) -> dropoff ONLY
//     $segments = [];

//     for ($i = 0; $i < count($fullRoutePoints) - 1; $i++) {
//         $from = $fullRoutePoints[$i];
//         $to   = $fullRoutePoints[$i + 1];

//         $distance = $this->calculateDistance($from, $to);
//         $segments[] = [
//             'from' => $from,
//             'to'   => $to,
//             'distance' => $distance
//         ];
//         $totalDistance += $distance;

//         // NEW LOGIC: Only add distance to $journeyDistance for customer segments
//         // The customer journey starts from the segment after the base-to-pickup segment.
//         // It ends with the segment before the dropoff-to-base segment.
//         if ($i >= 1 && $i <= count($fullRoutePoints) - 3) {
//             $journeyDistance += $distance;
//         }
//     }

//     /**
//      * 4️⃣ Select Correct Mileage Tier
//      * (Uses $totalDistance as originally intended)
//      */
//     // $selectedPricing = null;
//     // foreach ($pricingRecords as $record) {
//     //     $bracket = str_replace(['–', '—', 'to', ' '], ['-', '-', '-', ''], trim($record->mileage_bracket ?? ''));
//     //     $range = explode('-', $bracket);

//     //     if (count($range) === 2) {
//     //         $min = (float)$range[0];
//     //         $max = (float)$range[1];
//     //         if ($totalDistance >= $min && $totalDistance <= $max) {
//     //             $selectedPricing = $record;
//     //             break;
//     //         }
//     //     }
//     // }

//     // if (!$selectedPricing && !empty($pricingRecords)) {
//     //     $selectedPricing = end($pricingRecords); // fallback to last tier
//     // }

//     // if (!$selectedPricing) {
//     //     return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     // }

//     // /**
//     //  * 5️⃣ Calculate Price
//     //  * (Uses $totalDistance as originally intended)
//     //  */
//     // $perMileRate  = (float)($selectedPricing->cost_per_mileage ?? 0.0);
//     // $minimumPrice = (float)($selectedPricing->minimum_price ?? 0.0);

//     // if ($perMileRate <= 0) {
//     //     return response()->json(['success' => false, 'message' => 'Invalid per mile rate found.']);
//     // }

//     // $totalPrice = $totalDistance * $perMileRate;
//     // if ($totalPrice < $minimumPrice) {
//     //     $totalPrice = $minimumPrice;
//     // }

//     // return response()->json([
//     //     'success'      => true,
//     //     'type'         => 'mileage',
//     //     'price'        => round($totalPrice, 2),
//     //     'total_distance' => round($totalDistance, 2),
//     //     'journey_distance' => round($journeyDistance, 2), // NEW VARIABLE IN RESPONSE
//     //     'segments'     => $segments,
//     //     'vias'         => $vias,
//     //     'applied_tier' => [
//     //         'mileage_bracket'  => $selectedPricing->mileage_bracket ?? '',
//     //         'cost_per_mileage' => $selectedPricing->cost_per_mileage ?? 0,
//     //         'minimum_price'    => $selectedPricing->minimum_price ?? 0
//     //     ]
//     // ]);
    
//     /**
//  * 4️⃣ APPLY CUMULATIVE TIER FORMULA
//  * Bracket-by-bracket consumption until full $totalDistance is used.
//  */

// usort($pricingRecords, function($a, $b) {
//     return ((float)$a->from_mileage) <=> ((float)$b->from_mileage);
// });

// // $effectiveDistance = $totalDistance / 2;
// $effectiveDistance = max(0, (float)$journeyDistance)/2;

// $remaining = $effectiveDistance;
// $prev_to   = 0;
// $totalPrice = 0;
// $breakdown = [];

// foreach ($pricingRecords as $record) {

//     $from = (float)$record->from_mileage;
//     $to   = (float)$record->to_mileage;
//     $rate = (float)$record->cost_per_mileage;
//     $min  = (float)$record->minimum_price;

//     // cap bracket end at totalDistance
//     $cap = min($effectiveDistance, $to);

//     // cumulative formula: used miles = cap - prev_to
//     $used = $cap - $prev_to;
//     if ($used <= 0) {
//         if ($to > $prev_to) $prev_to = $to;
//         continue;
//     }

//     // Apply rules:
//     // rate = 0 → use minimum
//     // rate > 0 → rate × used
//     if ($rate <= 0) {
//         $cost = $min;
//         $logic = "minimum_applied";
//     } else {
//         $cost = $used * $rate;
//         $logic = "rate_applied";
//     }

//     // Save breakdown
//     $breakdown[] = [
//         "from"        => $from,
//         "to"          => $to,
//         "miles_used"  => round($used, 2),
//         "rate"        => $rate,
//         "minimum"     => $min,
//         "cost"        => round($cost, 2),
//         "logic"       => $logic
//     ];

//     $totalPrice += $cost;
//     $prev_to = $cap;

//     if ($prev_to >= $effectiveDistance) break;
// }

// // If leftover miles beyond last bracket
// if ($prev_to < $effectiveDistance) {
//     $last = end($pricingRecords);
//     $leftover = $effectiveDistance - $prev_to;

//     $rate = (float)$last->cost_per_mileage;
//     $min  = (float)$last->minimum_price;

//     if ($rate <= 0) {
//         $cost = $min;
//         $logic = "minimum_applied";
//     } else {
//         $cost = $leftover * $rate;
//         $logic = "rate_applied";
//     }

//     $breakdown[] = [
//         "from" => (float)$last->from_mileage,
//         "to" => (float)$last->to_mileage,
//         "miles_used" => round($leftover, 2),
//         "rate" => $rate,
//         "minimum" => $min,
//         "cost" => round($cost, 2),
//         "logic" => $logic
//     ];

//     $totalPrice += $cost;
// }

// /**
//  * 5️⃣ Return Final Mileage Pricing (cumulative)
//  */
// return response()->json([
//     "success" => true,
//     "type" => "mileage",
//     "price" => round($totalPrice, 2),
//     "vehicle_type" => $vehicleId,
//     "pickup" => $pickup,
//     "dropoff" => $dropoff,
//     "total_distance" => round($totalDistance, 2),
//     "journey_distance" => round($journeyDistance, 2),
//     "breakdown" => $breakdown,
//     "segments" => $segments,
//     "vias" => $vias
// ]);

// }

// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup    = strtoupper(trim($request->pickup));
//     $dropoff   = strtoupper(trim($request->dropoff));
//     $vias      = $request->vias ?? []; // Expecting array like ['SL1 2AA', 'OX1 1ZZ']
//     $basePostcode = "RG1 1LZ"; // Fixed base location

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }

//     /**
//      * 1️⃣ Try Fixed Price (Firebase)
//      */
//     try {
//         $snapshot = $this->database
//             ->getReference('fixed_pricing')
//             ->orderByChild('vehicle_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 $from = strtoupper(trim($record['from_postcode'] ?? ''));
//                 $to   = strtoupper(trim($record['to_postcode'] ?? ''));

//                 if ($from === $pickup && $to === $dropoff) {
//                     $company = (float)($record['company_price'] ?? 0);
//                     $driver  = (float)($record['driver_price'] ?? 0);
//                     $agent   = (float)($record['agent_commission'] ?? 0);

//                     $price = $company + $driver + $agent;

//                     // NOTE: If fixed price is found, the journey distance isn't needed/calculated here.
//                     return response()->json([
//                         'success' => true,
//                         'price'   => round($price, 2),
//                         'type'    => 'fixed',
//                         'vias'    => $vias
//                     ]);
//                 }
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Fixed Pricing Error: ' . $e->getMessage());
//     }

//     /**
//      * 2️⃣ Get Mileage Pricing (Firebase)
//      */
//     $pricingRecords = [];
//     try {
//         $snapshot = $this->database
//             ->getReference('mileage_pricing')
//             ->orderByChild('car_type')
//             ->equalTo($vehicleId)
//             ->getValue();

//         if (!empty($snapshot)) {
//             foreach ($snapshot as $record) {
//                 $pricingRecords[] = (object)$record;
//             }
//         }
//     } catch (\Exception $e) {
//         \Log::error('Firebase Mileage Pricing Error: ' . $e->getMessage());
//     }

//     if (empty($pricingRecords)) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 3️⃣ Build Route & Calculate Distances
//      */
//     // Full route for total distance calculation (for tier selection)
//     $fullRoutePoints = array_filter(array_merge([$basePostcode, $pickup], $vias, [$dropoff, $basePostcode]));
//     $totalDistance = 0; // Distance for pricing tier calculation (includes base legs)
//     $journeyDistance = 0; // NEW: Distance for pickup -> via(s) -> dropoff ONLY
//     $segments = [];

//     for ($i = 0; $i < count($fullRoutePoints) - 1; $i++) {
//         $from = $fullRoutePoints[$i];
//         $to   = $fullRoutePoints[$i + 1];

//         $distance = $this->calculateDistance($from, $to);
//         $segments[] = [
//             'from' => $from,
//             'to'   => $to,
//             'distance' => $distance
//         ];
//         $totalDistance += $distance;

//         // NEW LOGIC: Only add distance to $journeyDistance for customer segments
//         // The customer journey starts from the segment after the base-to-pickup segment.
//         // It ends with the segment before the dropoff-to-base segment.
//         if ($i >= 1 && $i <= count($fullRoutePoints) - 3) {
//             $journeyDistance += $distance;
//         }
//     }

//     /**
//      * 4️⃣ Select Correct Mileage Tier
//      * (Uses $totalDistance as originally intended)
//      */
//     $selectedPricing = null;
//     foreach ($pricingRecords as $record) {
//         $bracket = str_replace(['–', '—', 'to', ' '], ['-', '-', '-', ''], trim($record->mileage_bracket ?? ''));
//         $range = explode('-', $bracket);

//         if (count($range) === 2) {
//             $min = (float)$range[0];
//             $max = (float)$range[1];
//             if ($totalDistance >= $min && $totalDistance <= $max) {
//                 $selectedPricing = $record;
//                 break;
//             }
//         }
//     }

//     if (!$selectedPricing && !empty($pricingRecords)) {
//         $selectedPricing = end($pricingRecords); // fallback to last tier
//     }

//     if (!$selectedPricing) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     /**
//      * 5️⃣ Calculate Price
//      * (Uses $totalDistance as originally intended)
//      */
//     $perMileRate  = (float)($selectedPricing->cost_per_mileage ?? 0.0);
//     $minimumPrice = (float)($selectedPricing->minimum_price ?? 0.0);

//     if ($perMileRate <= 0) {
//         return response()->json(['success' => false, 'message' => 'Invalid per mile rate found.']);
//     }

//     $totalPrice = $totalDistance * $perMileRate;
//     if ($totalPrice < $minimumPrice) {
//         $totalPrice = $minimumPrice;
//     }

//     return response()->json([
//         'success'      => true,
//         'type'         => 'mileage',
//         'price'        => round($totalPrice, 2),
//         'total_distance' => round($totalDistance, 2),
//         'journey_distance' => round($journeyDistance, 2), // NEW VARIABLE IN RESPONSE
//         'segments'     => $segments,
//         'vias'         => $vias,
//         'applied_tier' => [
//             'mileage_bracket'  => $selectedPricing->mileage_bracket ?? '',
//             'cost_per_mileage' => $selectedPricing->cost_per_mileage ?? 0,
//             'minimum_price'    => $selectedPricing->minimum_price ?? 0
//         ]
//     ]);
// }

private function calculateDistance($from, $to): float
{
    try {
        $apiKey = config('services.google_maps.key');
        $payload = [
            'origin' => ['address' => $from],
            'destination' => ['address' => $to],
            'travelMode' => 'DRIVE',
            'routingPreference' => 'TRAFFIC_UNAWARE',
            'computeAlternativeRoutes' => false,
        ];

        if (!empty($apiKey)) {
            $response = Http::connectTimeout(5)
                ->timeout(12)
                ->withHeaders([
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => 'routes.distanceMeters,routes.duration',
                ])
                ->post('https://routes.googleapis.com/directions/v2:computeRoutes', $payload);

            $distanceMeters = (float) ($response->json('routes.0.distanceMeters') ?? 0);
            if ($response->successful() && $distanceMeters > 0) {
                return round($distanceMeters / 1609.344, 2);
            }

            \Log::warning("Google Routes returned no distance for {$from} → {$to}", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        }

        $fallbackDistance = $this->calculateOsrmDistance($from, $to);
        if ($fallbackDistance > 0) {
            \Log::info("Using OSRM distance fallback for {$from} → {$to}", [
                'miles' => $fallbackDistance,
            ]);
        }

        return $fallbackDistance;

    } catch (\Throwable $e) {
        \Log::error("Distance calculation failed for {$from} → {$to}: " . $e->getMessage());
        return $this->calculateOsrmDistance($from, $to);
    }
}

private function calculateOsrmDistance(string $from, string $to): float
{
    try {
        $fromCoordinates = $this->geocodeUkPostcode($from);
        $toCoordinates = $this->geocodeUkPostcode($to);

        if (!$fromCoordinates || !$toCoordinates) {
            \Log::warning('Postcodes.io could not resolve mileage route.', compact('from', 'to'));
            return 0.0;
        }

        $coordinates = sprintf(
            '%.7F,%.7F;%.7F,%.7F',
            $fromCoordinates['lng'],
            $fromCoordinates['lat'],
            $toCoordinates['lng'],
            $toCoordinates['lat']
        );

        $response = Http::connectTimeout(5)
            ->timeout(12)
            ->get("https://router.project-osrm.org/route/v1/driving/{$coordinates}", [
                'overview' => 'false',
                'alternatives' => 'false',
                'steps' => 'false',
            ]);

        $distanceMeters = (float) ($response->json('routes.0.distance') ?? 0);

        return $response->successful() && $distanceMeters > 0
            ? round($distanceMeters / 1609.344, 2)
            : 0.0;
    } catch (\Throwable $e) {
        \Log::error("OSRM distance fallback failed for {$from} → {$to}: ".$e->getMessage());
        return 0.0;
    }
}

private function geocodeUkPostcode(string $address): ?array
{
    $address = strtoupper(trim($address));
    $endpoint = null;

    if (preg_match('/\b([A-Z]{1,2}\d[A-Z\d]?)\s*(\d[A-Z]{2})\b/', $address, $match)) {
        $postcode = $match[1].' '.$match[2];
        $endpoint = 'https://api.postcodes.io/postcodes/'.rawurlencode($postcode);
    } elseif (preg_match('/\b([A-Z]{1,2}\d[A-Z\d]?)\b/', $address, $match)) {
        $endpoint = 'https://api.postcodes.io/outcodes/'.rawurlencode($match[1]);
    }

    if ($endpoint === null) {
        return null;
    }

    try {
        $response = Http::connectTimeout(5)->timeout(8)->acceptJson()->get($endpoint);
        $latitude = $response->json('result.latitude');
        $longitude = $response->json('result.longitude');

        if (!$response->successful() || !is_numeric($latitude) || !is_numeric($longitude)) {
            return null;
        }

        return ['lat' => (float) $latitude, 'lng' => (float) $longitude];
    } catch (\Throwable $e) {
        \Log::error('UK postcode geocoding failed: '.$e->getMessage(), ['address' => $address]);
        return null;
    }
}



// private function calculateDistance($from, $to): float
// {
//     try {
//         $fromCoords = $this->geocodeAddress($from);
//         $toCoords   = $this->geocodeAddress($to);

//         if (!$fromCoords || !$toCoords) {
//             \Log::warning("Missing coordinates for route: {$from} → {$to}");
//             return 0;
//         }

//         $url = sprintf(
//             'https://router.project-osrm.org/route/v1/driving/%f,%f;%f,%f?overview=false',
//             $fromCoords['lng'], $fromCoords['lat'],
//             $toCoords['lng'], $toCoords['lat']
//         );

//         $ch = curl_init($url);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // connection timeout
//         curl_setopt($ch, CURLOPT_TIMEOUT, 10);       // total request timeout
//         $response = curl_exec($ch);

//         if (curl_errno($ch)) {
//             \Log::error("OSRM CURL error for {$from} → {$to}: " . curl_error($ch));
//             curl_close($ch);
//             return 0;
//         }

//         $httpStatus = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
//         curl_close($ch);

//         if ($httpStatus !== 200) {
//             \Log::error("OSRM returned HTTP status {$httpStatus} for {$from} → {$to}");
//             return 0;
//         }

//         $data = json_decode($response, true);
//         if (empty($data['routes'][0]['distance'])) {
//             \Log::warning("OSRM returned no distance for {$from} → {$to}. Response: " . $response);
//             return 0;
//         }

//         // distance is in meters — convert to miles or km as needed
//         return round($data['routes'][0]['distance'] / 1609.34, 2);

//     } catch (\Exception $e) {
//         \Log::error("Distance calculation failed for {$from} → {$to}: " . $e->getMessage());
//         return 0;
//     }
// }





/**
 * Geocode address/postcode into lat/lng
 */
// private function geocodeAddress($address)
// {
//     try {
//         // Use OpenCage API (free, requires API key)
//         $apiKey =config('services.opencage.key'); // add to .env
//         $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($address) . "&key=" . $apiKey;

//         $response = file_get_contents($url);
//         $data = json_decode($response, true);

//         if (!empty($data['results'][0]['geometry'])) {
//             return [
//                 'lat' => $data['results'][0]['geometry']['lat'],
//                 'lng' => $data['results'][0]['geometry']['lng'],
//             ];
//         }
//     } catch (\Exception $e) {
//         \Log::error('Geocoding failed: ' . $e->getMessage(), ['address' => $address]);
//     }

//     return null;
// }


/**
 * Geocode address/postcode into lat/lng
 */
private function geocodeAddress($address)
{
    try {
        $apiKey = config('services.google_maps.key');

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
            'address' => $address,
            'key'     => $apiKey,
        ]);

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (!empty($data['results'][0]['geometry']['location'])) {
            return [
                'lat' => $data['results'][0]['geometry']['location']['lat'],
                'lng' => $data['results'][0]['geometry']['location']['lng'],
            ];
        }

    } catch (\Exception $e) {
        \Log::error('Geocoding failed: ' . $e->getMessage(), ['address' => $address]);
    }

    return null;
}


// public function getPrice(Request $request)
// {
//     $vehicleId = $request->vehicle_id;
//     $pickup = $request->pickup;
//     $dropoff = $request->dropoff;

    
//     $basePostcode = "RG1 1LZ"; // fixed base location

//     if (!$vehicleId || !$pickup || !$dropoff) {
//         return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
//     }

//     // $pricing = $this->firebase->getData('fixed_prices');
        

       

//     // 1. Try Fixed Price (Firebase)
//     $firebaseBookings = $this->firebase->getData('fixed_prices');
//     foreach ($firebaseBookings as $item) {
//         if (
//             isset($item['vehicle_type'], $item['from_postcode'], $item['to_postcode']) &&
//             $item['vehicle_type'] == 0 &&
//             $item['from_postcode'] == $pickup &&
//             $item['to_postcode'] == $dropoff
//         ) {
//             $company = isset($item['company_price']) ? (float)$item['company_price'] : 0;
//             $driver  = isset($item['driver_price']) ? (float)$item['driver_price'] : 0;
//             $agent   = isset($item['agent_commission']) ? (float)$item['agent_commission'] : 0;

//             $price = $company + $driver + $agent;

//             return response()->json(['success' => true, 'price' => $price, 'type' => 'fixed']);
//         }
//     }

//     // // 2. Mileage Pricing
//     // $pricing = \DB::table('mileage_pricing')
//     //     ->where('vehicle_type_id', $vehicleId)
//     //     ->first();

    

//     if (!$pricing) {
//         return response()->json(['success' => false, 'message' => 'Mileage pricing not found']);
//     }

//     // 3. Calculate Distances
//     $distBaseToPickup   = $this->calculateDistance($basePostcode, $pickup);
//     $distPickupToDrop   = $this->calculateDistance($pickup, $dropoff);
//     $distDropToBase     = $this->calculateDistance($dropoff, $basePostcode);

//     // 4. Convert to Prices using per-mile rate
//     $perMileRate = 2.0; // Example rate, ideally from $pricing
//     $priceBaseToPickup = $distBaseToPickup * $perMileRate;
//     $pricePickupToDrop = $distPickupToDrop * $perMileRate;
//     $priceDropToBase   = $distDropToBase * $perMileRate;

//     // 5. Apply Formula
//     $totalPrice = ($priceBaseToPickup + $pricePickupToDrop + $priceDropToBase) / 2;

//     // echo "<pre>";
//     // echo "Distances (miles): Base->Pickup: $distBaseToPickup, Pickup->Drop: $distPickupToDrop, Drop->Base: $distDropToBase\n";
//     // echo "Prices: Base->Pickup: $priceBaseToPickup, Pickup->Drop: $pricePickupToDrop, Drop->Base: $priceDropToBase\n";
//     // echo "Total Price (before rounding): $totalPrice\n";
//     // echo "</pre>";
//     // die;
    
//     return response()->json([
//         'success' => true,
//         'price' => round($totalPrice, 2),
//         'distances' => [
//             'base_to_pickup' => $distBaseToPickup,
//             'pickup_to_drop' => $distPickupToDrop,
//             'drop_to_base'   => $distDropToBase
//         ],
//         'type' => 'mileage'
//     ]);
// }

// private function calculateDistance($from, $to)
// {
    
//     return random_int(5, 15);
//     // For now, return dummy value (miles)
//     // return 10.0;
// }



    // public function edit($bookingId)
    // {
    //     $booking = Booking::findOrFail($id);
    //     $drivers = Driver::all();
    //     $vehicles = Vehicle::all();
    //     return view('booking_form', compact('booking', 'drivers', 'vehicles'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $booking = Booking::findOrFail($id);

    //     $validated = $request->validate([
    //         'pickup_address' => 'required|string',
    //         'dropoff_address' => 'required|string',
    //         'payment_type' => 'required|in:cash,card,account',
    //         'driver_id' => 'nullable|exists:drivers,id',
    //         'vehicle_id' => 'nullable|exists:vehicles,id',
    //         'price' => 'nullable|numeric',
    //     ]);

    //     $booking->update($validated);

    //     return redirect()->route('dashboard')->with('success', 'Booking updated successfully.');
    // }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return back()->with('success', 'Booking deleted.');
    }

//     public function search(Request $request)
// {
//     $query = Booking::query();

//     if ($request->search) {
//         $query->whereHas('passenger', function($q) use ($request) {
//             $q->where('name', 'like', '%' . $request->search . '%');
//         })
//         ->orWhere('ref_no', 'like', '%' . $request->search . '%')
//         ->orWhere('pickup_address', 'like', '%' . $request->search . '%')
//         ->orWhere('dropoff_address', 'like', '%' . $request->search . '%')
//         ->orWhere('mobile', 'like', '%' . $request->search . '%')
//         ->orWhere('email', 'like', '%' . $request->search . '%');
//     }

//     if ($request->from_date) {
//         $query->whereDate('pickup_datetime', '>=', $request->from_date);
//     }

//     if ($request->to_date) {
//         $query->whereDate('pickup_datetime', '<=', $request->to_date);
//     }

//     if ($request->driver_id) {
//         $query->where('driver_id', $request->driver_id);
//     }

//     if ($request->account_id) {
//         $query->where('account_id', $request->account_id);
//     }

//     if ($request->payment_type) {
//         $query->where('payment_type', $request->payment_type);
//     }

//     $bookings = $query->orderBy('pickup_datetime', 'asc')->get();

//     $drivers = Driver::all();
//     $accounts = Account::all();

//     return view('dashboard', compact('bookings', 'drivers', 'accounts'));
// }


public function search(Request $request)
{
    // 1) Fetch bookings from Firebase
    $firebaseBookings = $this->firebase->getData('bookings') ?? [];
    $results = [];

    foreach ($firebaseBookings as $key => $item) {
        $normalized = $item;
        $normalized['pickup_datetime'] = $item['pickup_datetime'] ?? $item['pickup_time'] ?? null;
        $normalized['mobile'] = $item['mobile'] ?? $item['phone_no'] ?? null;
        $normalized['pickup_address']  = $item['pickup_address'] ?? null;
        $normalized['dropoff_address'] = $item['dropoff_address'] ?? null;
        $normalized['price'] = $item['price'] ?? null;

        // ✅ Only include bookings where status == "upcoming"
        $status = strtolower(trim($item['status'] ?? ''));
//         if (!in_array($status, ['upcoming', 'pending','assigned'])) {
//     continue;
// }
if (in_array($status ?? null, ['completed', 'job_cancelled','no_show'])) {
    continue;
}

        $match = true;

        // 🔎 Search text filter
        if ($request->filled('search')) {
            $search = mb_strtolower($request->search);
            $fieldsToSearch = [
                'passenger_name',
                'ref_no',
                'from_postcode',
                'to_postcode',
                'mobile',
                'email',
                'pickup_address',
                'dropoff_address',
                'payment_type'
            ];

            $match = false;
            foreach ($fieldsToSearch as $field) {
                if (isset($normalized[$field]) && $normalized[$field] !== '') {
                    if (str_contains(mb_strtolower((string)$normalized[$field]), $search)) {
                        $match = true;
                        break;
                    }
                }
            }
        }
        
        

        // 📅 From date filter
        if ($match && $request->filled('from_date') && $normalized['pickup_datetime']) {
            try {
                $pickupDt = Carbon::parse($normalized['pickup_datetime']);
                $fromDate = Carbon::parse($request->from_date);
                if ($pickupDt->lt($fromDate)) {
                    $match = false;
                }
            } catch (\Throwable $e) {}
        }

        // 📅 To date filter
        if ($match && $request->filled('to_date') && $normalized['pickup_datetime']) {
            try {
                $pickupDt = Carbon::parse($normalized['pickup_datetime']);
                $toDate = Carbon::parse($request->to_date);
                if ($pickupDt->gt($toDate)) {
                    $match = false;
                }
            } catch (\Throwable $e) {}
        }

        // 🚖 Driver filter
        if ($match && $request->filled('driver_id')) {
            $recDriverId = isset($normalized['driver_id']) ? (string)$normalized['driver_id'] : null;
            if ($recDriverId !== (string)$request->driver_id) {
                $match = false;
            }
        }

        // 🏢 Account filter
        if ($match && $request->filled('account_id')) {
            $recAccountId = isset($normalized['account_id']) ? (string)$normalized['account_id'] : null;
            if ($recAccountId !== (string)$request->account_id) {
                $match = false;
            }
        }

        // 💳 Payment type filter
        // 💳 Payment type filter
if ($match && $request->filled('payment_type')) {

    $recPaymentType = isset($normalized['payment_type'])
        ? strtolower(trim((string)$normalized['payment_type']))
        : null;

    $reqPaymentType = strtolower(trim((string)$request->payment_type));

    if ($recPaymentType !== $reqPaymentType) {
        $match = false;
    }
}

        // 🕒 ✅ Only include future bookings (pickup_datetime > now)
        // if ($match && !empty($normalized['pickup_datetime'])) {
        //     try {
        //         $pickupDt = Carbon::parse($normalized['pickup_datetime']);
        //         if ($pickupDt->isPast()) {
        //             $match = false;
        //         }
        //     } catch (\Throwable $e) {}
        // }

        if ($match) {
            $normalized['id'] = $key;
            $results[] = $normalized;
        }
    }

    // ⏰ Sort bookings by pickup time
    usort($results, function ($a, $b) {
        try {
            $ad = isset($a['pickup_datetime']) && $a['pickup_datetime'] ? Carbon::parse($a['pickup_datetime']) : null;
        } catch (\Throwable $e) {
            $ad = null;
        }
        try {
            $bd = isset($b['pickup_datetime']) && $b['pickup_datetime'] ? Carbon::parse($b['pickup_datetime']) : null;
        } catch (\Throwable $e) {
            $bd = null;
        }

        if ($ad === null && $bd === null) return 0;
        if ($ad === null) return 1;
        if ($bd === null) return -1;
        return $ad->timestamp <=> $bd->timestamp;
    });

    // 2) Fetch drivers from Firebase
    $firebaseDrivers = $this->firebase->getData('drivers') ?? [];
    $drivers = [];
    foreach ($firebaseDrivers as $driverKey => $driver) {
        $driver['id'] = $driverKey;
        $drivers[] = $driver;
    }
    $drivers = collect($drivers);

    // 3) Fetch accounts from MySQL
    // $accounts = Account::all();
    $accounts = $this->firebase->getData('customers') ?? [];
    
// ================= PAGINATION =================

$perPage = 20;
$currentPage = request()->get('page', 1);

// Convert filtered results to collection
$resultsCollection = collect($results);

// Slice results for current page
$currentPageItems = $resultsCollection
    ->slice(($currentPage - 1) * $perPage, $perPage)
    ->values();

// Create paginator
$bookings = new LengthAwarePaginator(
    $currentPageItems,
    $resultsCollection->count(),
    $perPage,
    $currentPage,
    [
        'path'  => request()->url(),
        'query' => request()->query(), // keeps filters in pagination links
    ]
);

    // 4) Return data to view
    return view('dashboard', [
        'bookings' => $bookings,
        'drivers'  => $drivers,
        'accounts' => $accounts,
    ]);
}



// public function search(Request $request)
// {
//     // 1) Fetch bookings from Firebase (ensure we have an array)
//     $firebaseBookings = $this->firebase->getData('bookings') ?? [];
//     $results = [];

//     foreach ($firebaseBookings as $key => $item) {
//         // Normalize and guard against missing keys
//         $normalized = $item;

//         // Normalized pickup datetime: prefer 'pickup_datetime' then 'pickup_time'
//         $normalized['pickup_datetime'] = $item['pickup_datetime'] ?? $item['pickup_time'] ?? null;

//         // Normalize mobile/phone
//         $normalized['mobile'] = $item['mobile'] ?? $item['phone_no'] ?? null;

//         // Keep addresses normalized (if you have from/to postcode fields you can set them here)
//         $normalized['pickup_address']  = $item['pickup_address']  ?? null;
//         $normalized['dropoff_address'] = $item['dropoff_address'] ?? null;
        
//         // Normalize price safely
//         $normalized['price'] = $item['price'] ?? null;

//         $match = true;

//         // 🔎 Search text across multiple possible fields
//         if ($request->filled('search')) {
//             $search = mb_strtolower($request->search);

//             $fieldsToSearch = [
//                 'passenger_name',
//                 'ref_no',
//                 'from_postcode',   // if you use these fields in some records
//                 'to_postcode',
//                 'mobile',
//                 'email',
//                 'pickup_address',
//                 'dropoff_address'
//             ];

//             $match = false;
//             foreach ($fieldsToSearch as $field) {
//                 if (isset($normalized[$field]) && $normalized[$field] !== '') {
//                     if (str_contains(mb_strtolower((string)$normalized[$field]), $search)) {
//                         $match = true;
//                         break;
//                     }
//                 }
//             }
//         }

//         // 📅 Date filters (use Carbon; handle missing/invalid dates)
//         if ($match && $request->filled('from_date') && $normalized['pickup_datetime']) {
//             try {
//                 $pickupDt = Carbon::parse($normalized['pickup_datetime']);
//                 $fromDate = Carbon::parse($request->from_date);
//                 if ($pickupDt->lt($fromDate)) {
//                     $match = false;
//                 }
//             } catch (\Throwable $e) {
//                 // invalid date format -> skip date filtering for this record
//             }
//         }

//         if ($match && $request->filled('to_date') && $normalized['pickup_datetime']) {
//             try {
//                 $pickupDt = Carbon::parse($normalized['pickup_datetime']);
//                 $toDate = Carbon::parse($request->to_date);
//                 if ($pickupDt->gt($toDate)) {
//                     $match = false;
//                 }
//             } catch (\Throwable $e) {
//                 // invalid date format -> skip date filtering for this record
//             }
//         }

//         // 🚖 Driver filter (safe compare)
//         if ($match && $request->filled('driver_id')) {
//             $recDriverId = isset($normalized['driver_id']) ? (string)$normalized['driver_id'] : null;
//             if ($recDriverId !== (string)$request->driver_id) {
//                 $match = false;
//             }
//         }

//         // 🏢 Account filter
//         if ($match && $request->filled('account_id')) {
//             $recAccountId = isset($normalized['account_id']) ? (string)$normalized['account_id'] : null;
//             if ($recAccountId !== (string)$request->account_id) {
//                 $match = false;
//             }
//         }

//         // 💳 Payment filter
//         if ($match && $request->filled('payment_type')) {
//             $recPaymentType = isset($normalized['payment_type']) ? (string)$normalized['payment_type'] : null;
//             if ($recPaymentType !== (string)$request->payment_type) {
//                 $match = false;
//             }
//         }

//         if ($match) {
//             $normalized['id'] = $key; // keep Firebase key
//             $results[] = $normalized;
//         }
//     }

//     // Sort by pickup time (safe parsing; missing dates go last)
//     usort($results, function ($a, $b) {
//         try {
//             $ad = isset($a['pickup_datetime']) && $a['pickup_datetime'] ? Carbon::parse($a['pickup_datetime']) : null;
//         } catch (\Throwable $e) {
//             $ad = null;
//         }
//         try {
//             $bd = isset($b['pickup_datetime']) && $b['pickup_datetime'] ? Carbon::parse($b['pickup_datetime']) : null;
//         } catch (\Throwable $e) {
//             $bd = null;
//         }

//         if ($ad === null && $bd === null) return 0;
//         if ($ad === null) return 1; // put nulls after real dates
//         if ($bd === null) return -1;

//         // ascending: earlier first
//         return $ad->timestamp <=> $bd->timestamp;
//     });

//     // 2) Fetch drivers from Firebase
//     $firebaseDrivers = $this->firebase->getData('drivers') ?? [];
//     $drivers = [];
//     foreach ($firebaseDrivers as $driverKey => $driver) {
//         $driver['id'] = $driverKey;
//         $drivers[] = $driver;
//     }
//     $drivers = collect($drivers);

//     // 3) Fetch accounts from MySQL
//     $accounts = Account::all();

//     // 4) Send everything to view
//     return view('dashboard', [
//         'bookings' => $results,
//         'drivers'  => $drivers,
//         'accounts' => $accounts,
//     ]);
// }

public function previousBookings()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    try {
        $database = app('App\Services\FirebaseService')->getDatabase();
        $bookingsRef = $database->getReference('bookings')->getValue();
        $driversRef  = $database->getReference('drivers')->getValue() ?? [];
        $accountsRef = $database->getReference('customers')->getValue() ?? [];

        $today = \Carbon\Carbon::today();

        $previousBookings = collect();
        
         // 🔹 Create driver map [driver_id => driver_name]
        $driversMap = [];
        foreach ($driversRef as $driverId => $driver) {
            $driversMap[$driverId] = $driver['name'] ?? 'Unknown Driver';
        }

        if ($bookingsRef) {
            foreach ($bookingsRef as $key => $booking) {
                
                    // $booking['id'] = $key;
                    
                    if (empty($booking['pickup_time'])) {
                continue;
            }

            try {
                $pickupTime = \Carbon\Carbon::parse($booking['pickup_time']);
            } catch (\Throwable $e) {
                continue;
            }

            // Only bookings BEFORE today
            if ($pickupTime->gte($today)) {
                continue;
            }

            // Attach IDs & driver name
            $booking['id'] = $key;
            $booking['driver_name'] = isset($booking['driver_id'], $driversMap[$booking['driver_id']])
                ? $driversMap[$booking['driver_id']]
                : 'Not Assigned';
                    
                    
                    
                    
                    $previousBookings->push($booking);
                
            }
        }

        $previousBookings = $previousBookings
            ->sortByDesc(fn($b) =>
                isset($b['pickup_time'])
                    ? \Carbon\Carbon::parse($b['pickup_time'])
                    : now()
            )
            ->values();

        $drivers = $database->getReference('drivers')->getValue() ?? [];
        $accounts = $database->getReference('customers')->getValue() ?? [];

        return view('bookings.previous', [
            'previousBookings' => $previousBookings,
            'drivers' => collect($drivers)->map(fn($v, $k) => ['id' => $k] + $v),
            'accounts' => collect($accounts)->map(fn($v, $k) => (object)(['id' => $k] + $v)),
        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to load previous bookings: ' . $e->getMessage());
    }
}




   public function completedJobs()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    try {
        $database = app('App\Services\FirebaseService')->getDatabase();

        // ✅ Fetch all bookings
        $bookingsRef = $database->getReference('bookings')->getValue();
        $driversRef  = $database->getReference('drivers')->getValue() ?? [];
        $completedBookings = collect();
        
         // 🔹 Create driver map [driver_id => driver_name]
        $driversMap = [];
        foreach ($driversRef as $driverId => $driver) {
            $driversMap[$driverId] = $driver['name'] ?? 'Unknown Driver';
        }

        if (!empty($bookingsRef)) {
    foreach ($bookingsRef as $key => $booking) {

        // Attach ID
        $booking['id'] = $key;

        // Attach driver name
        $booking['driver_name'] = isset($booking['driver_id'], $driversMap[$booking['driver_id']])
            ? $driversMap[$booking['driver_id']]
            : 'Not Assigned';

        // Only push completed bookings
        if (isset($booking['status']) && strtolower($booking['status']) === 'completed') {
            $completedBookings->push($booking);
        }
    }
}


        // ✅ Sort by pickup_time (latest first)
        $completedBookings = $completedBookings->sortByDesc(function ($b) {
            return isset($b['pickup_time']) ? \Carbon\Carbon::parse($b['pickup_time']) : now();
        })->values();

        // ✅ Fetch driver and account lists for filters
        $drivers = $database->getReference('drivers')->getValue() ?? [];
        $accounts = $database->getReference('customers')->getValue() ?? [];

        // ✅ Pass all data to the Blade
        return view('bookings.completed', [
            'completedBookings' => $completedBookings,
            'drivers' => collect($drivers)->map(fn($v, $k) => ['id' => $k] + $v),
            'accounts' => collect($accounts)->map(fn($v, $k) => (object) (['id' => $k] + $v)),
        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to load completed jobs: ' . $e->getMessage());
    }
}


public function searchBookings(Request $request)
{
    $bookingsData = $this->database
        ->getReference('bookings')
        ->getValue() ?? [];

    $drivers = $this->database->getReference('drivers')->getValue() ?? [];
    $accounts = $this->database->getReference('accounts')->getValue() ?? [];

    // 🔹 Driver ID → Name Map
    $driversMap = collect($drivers)->mapWithKeys(function ($driver, $id) {
        return [$id => $driver['name'] ?? 'Unknown'];
    });

    $completedBookings = collect();

    // ✅ SINGLE LOOP ONLY
    foreach ($bookingsData as $key => $booking) {

        if (!isset($booking['status']) || strtolower($booking['status']) !== 'completed') {
            continue;
        }

        $booking['id'] = $key;

        // Attach driver name safely
        $driverId = $booking['driver_id'] ?? null;
        $booking['driver_name'] = $driverId && isset($driversMap[$driverId])
            ? $driversMap[$driverId]
            : 'Not Assigned';

        $completedBookings->push($booking);
    }

    // 🔎 Apply Filters
    $filtered = $completedBookings->filter(function ($booking) use ($request) {

        if ($request->filled('search')) {
            $search = strtolower($request->search);

            if (
                !Str::contains(strtolower($booking['passenger_name'] ?? ''), $search) &&
                !Str::contains(strtolower($booking['ref_no'] ?? ''), $search) &&
                !Str::contains(strtolower($booking['phone_no'] ?? ''), $search)
            ) {
                return false;
            }
        }

        if ($request->filled('pickup') &&
            !Str::contains(strtolower($booking['pickup_address'] ?? ''), strtolower($request->pickup))) {
            return false;
        }

        if ($request->filled('dropoff') &&
            !Str::contains(strtolower($booking['dropoff_address'] ?? ''), strtolower($request->dropoff))) {
            return false;
        }

        if ($request->filled('from_date')) {
            $pickupTime = isset($booking['pickup_time'])
                ? Carbon::parse($booking['pickup_time'])
                : null;

            if (!$pickupTime || $pickupTime->lt(Carbon::parse($request->from_date)->startOfDay())) {
                return false;
            }
        }

        if ($request->filled('to_date')) {
            $pickupTime = isset($booking['pickup_time'])
                ? Carbon::parse($booking['pickup_time'])
                : null;

            if (!$pickupTime || $pickupTime->gt(Carbon::parse($request->to_date)->endOfDay())) {
                return false;
            }
        }

        if ($request->filled('driver_id') &&
            ($booking['driver_id'] ?? '') != $request->driver_id) {
            return false;
        }

        if ($request->filled('account_id') &&
            ($booking['account_id'] ?? '') != $request->account_id) {
            return false;
        }

        if ($request->filled('payment_type') &&
            strtolower($booking['payment_type'] ?? '') != strtolower($request->payment_type)) {
            return false;
        }

        return true;
    });

    // 🔽 Sort latest first
    $completedBookings = $filtered
        ->sortByDesc(function ($b) {
            return isset($b['pickup_time'])
                ? Carbon::parse($b['pickup_time'])->timestamp
                : 0;
        })
        ->values();

    return view('bookings.completed', [
        'completedBookings' => $completedBookings,
        'drivers' => collect($drivers)->map(fn($v, $k) => ['id' => $k] + $v),
        'accounts' => collect($accounts)->map(fn($v, $k) => (object)(['id' => $k] + $v)),
    ]);
}
    
    
    // Create Return Job
    public function returnJob($bookingId) {
        $booking = $this->firebase->getData("bookings/{$bookingId}");
        $returnBooking = $booking;
        unset($returnBooking['id']);
        $returnBooking['ref_no'] = $this->generateRefNo();
        // $returnBooking['ref_no'] = 'REF' . time();
        // Swap pickup/dropoff
        $tmp = $returnBooking['pickup_address'];
        $returnBooking['pickup_address'] = $returnBooking['dropoff_address'];
        $returnBooking['dropoff_address'] = $tmp;

        $newBookingRef = $this->firebase->pushData('bookings', $returnBooking);
        return redirect()->back();
        
        
    // return response()->json([
    //     'status' => 'success',
    //     'message' => 'Return booking created successfully',
    //     // 'return_booking_id' => $newRef->getKey()
    // ]);
        
    }

    // Send Confirmation Email
    // public function sendConfirmationEmail(Request $request, $bookingId) {
    //     $booking = $this->firebase->getData("bookings/{$bookingId}");
    //     if (!empty($booking['email'])) {
    //         Mail::raw("Your booking (Ref: {$booking['ref_no']}) has been confirmed!", function($message) use ($booking){
    //             $message->to($booking['email'])
    //                     ->subject('Booking Confirmation - CrownCarz');
    //         });
    //     }
    //     return response()->json(['status'=>'success', 'message'=>'Email sent']);
    // }

    // Send Confirmation SMS
    public function sendConfirmationSMS(Request $request, $bookingId, MySmsService $smsService) {
        $booking = $this->firebase->getData("bookings/{$bookingId}");

        if (empty($booking) || empty($booking['phone_no'])) {
            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Booking phone number was not found.',
            ], 422);
        }

        $message = $request->input('message')
            ?: "Your booking (Ref: ".($booking['ref_no'] ?? $bookingId).") has been confirmed.";
        $result = $smsService->send((string) $booking['phone_no'], (string) $message);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            ...$result,
        ], $result['success'] ? 200 : 502);
    }
    
    
    public function sendSms(Request $request, MySmsService $smsService)
{
    $request->validate([
        'booking_id' => 'required|string',
        'phone' => 'required|string|max:30',
        'message' => 'required|string|max:2000',
    ]);

    $result = $smsService->send(
        (string) $request->string('phone')->trim(),
        (string) $request->string('message')
    );

    return response()->json([
        'status' => $result['success'] ? 'success' : 'error',
        ...$result,
    ], $result['success'] ? 200 : 502);
}

public function sendEmaildashboard(Request $request)
{
    
    $request->validate([
        'booking_id' => 'required',
        'email' => 'required|email',
        'message' => 'required|string',
    ]);

    // Fetch booking from Firebase
    $bookingId = $request->booking_id;
    $booking = $this->database->getReference("bookings/{$bookingId}")->getValue();

    if (!$booking) {
        return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
    }

    $refNo = $booking['ref_no'] ?? 'N/A';
    $formattedPickupDate = isset($booking['pickup_date']) 
        ? \Carbon\Carbon::parse($booking['pickup_date'])->format('d M Y') 
        : '-';

    Mail::to($request->email)
        ->cc('bookings@crownairporttravels.com')
        ->send(new \App\Mail\BookingConfirmationMail(
            $booking,
            $request->message,
            $refNo,
            $formattedPickupDate
        ));

    return response()->json(['status'=>'success', 'message'=>'Email sent successfully.']);
}



    // Recall Job
    // public function recallJob(Request $request, $bookingId) {
    //     $this->firebase->updateData("bookings/{$bookingId}", ['status'=>'recalled']);
    //     return response()->json(['status'=>'success', 'message'=>'Booking recalled']);
    // }

    // Hide Job
    public function hideJob(Request $request, $bookingId) {
        $this->firebase->updateData("bookings/{$bookingId}", ['hidden'=>true]);
        return response()->json(['status'=>'success', 'message'=>'Booking hidden']);
    }

    // Edit Booking
  // Show edit form
    public function edit($bookingId) {
        
        if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
        
    $booking = $this->firebase->getData("bookings/{$bookingId}");
    if (!$booking) return redirect()->back()->with('error','Booking not found');
    
    
    // print_r($booking);
    // die;

    $booking['id'] = $bookingId; // ✅ add this line

    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect($driversData)->map(fn($d, $id) => ['id' => $id, 'name' => $d['name']]);

    $vehiclesData = $this->firebase->getData('vehicles') ?? [];
    $vehicles = collect($vehiclesData)->map(fn($v, $id) => ['id' => $id, 'make' => $v['make'], 'model' => $v['model']]);
    
    // Fetch accounts from Firebase
    $firebaseAccounts = $this->firebase->getData('customers'); // 'customers' node in Firebase
    $accounts = [];
    if($firebaseAccounts) {
        foreach($firebaseAccounts as $key => $account) {
            $accounts[] = [
                'id' => $account['id'] ?? $key,
                'business_name' => $account['business_name'] ?? '',
                'address' => $account['address'] ?? '',
                'email' => $account['email'] ?? '',
                'phone' => $account['phone'] ?? '',
            ];
        }
    }

    return view('booking_form', compact('booking','drivers','vehicles','accounts'));
}


// Update booking
    public function update(Request $request, $bookingId) {
        $booking = $this->firebase->getData("bookings/{$bookingId}");
        if (!$booking) return redirect()->back()->with('error','Booking not found');

        $validated = $request->validate([
            'passenger_name' => 'required|string',
            'phone_no'       => 'required|string',
            'email'          => 'nullable|email',
            'pickup_address' => 'required|string',
            'dropoff_address'=> 'required|string',
            'pickup_date'    => 'required|date',
            'pickup_time'    => 'required|date_format:H:i',
            'vehicle_id'     => 'nullable|string',
            'vehicle_make'  => 'nullable|string',
            
            // 'price'          => 'nullable|numeric',
            
            'price'          => 'nullable|numeric',
            'fare'           => 'nullable|numeric',
            'parking'        => 'nullable|numeric',
            'extra'          => 'nullable|numeric',
            'waiting_fee'    => 'nullable|numeric',
            
            'payment_type'   => 'required|in:cash,card,account',
            'flight_no'      => 'nullable|string',
            'via_addresses'  => 'array',
            'via_addresses.*'=> 'nullable|string',
            'child_seat' => 'nullable|boolean' ,
            'job_comment'       =>'nullable|string',
        ]);

        $pickup_datetime = Carbon::parse($validated['pickup_date'].' '.$validated['pickup_time']);
        $driverId = null;
        if (!empty($validated['vehicle_id'])) {
            $vehicle = $this->firebase->getData('vehicles/'.$validated['vehicle_id']);
            $driverId = $vehicle['driver_id'] ?? null;
        }

        $updatedBooking = [
            'passenger_name' => $validated['passenger_name'],
            'phone_no' => $validated['phone_no'],
            'email' => $validated['email'] ?? null,
            'pickup_address' => $validated['pickup_address'],
            'dropoff_address' => $validated['dropoff_address'],
            'vias' => array_filter($validated['via_addresses'] ?? []),
            'pickup_time' => $pickup_datetime->toDateTimeString(),
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'vehicle_make' => $validated['vehicle_make'],
            //'driver_id' => $driverId ?? "",
            // 'price' => $validated['price'] ?? null,
            
            'fare'         => $validated['fare'] ?? 0,
            'parking'      => $validated['parking'] ?? 0,
            'extra'        => $validated['extra'] ?? 0,
            'waiting_fee'  => $validated['waiting_fee'] ?? 0,
            'price'        => $validated['price'] ?? 0,
            
            'payment_type' => $validated['payment_type'],
            'flight_no' => $validated['flight_no'] ?? null,
            'child_seat' => $validated['child_seat'],
            'job_comment' => $validated['job_comment'] ?? null,
        ];

        $this->firebase->updateData("bookings/{$bookingId}", $updatedBooking);

       return redirect('/dashboard')->with('success', 'Booking updated successfully!');
    }


    // View Booking
    public function view($bookingId) {
        $booking = $this->firebase->getData("bookings/{$bookingId}");
        return view('bookings.view', ['booking' => $booking]);
    }


}
