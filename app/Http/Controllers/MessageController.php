<?php

namespace App\Http\Controllers;


use App\Models\Booking;
use App\Models\Message;
use App\Models\Driver;
use Illuminate\Http\Request;

use App\Services\FirebaseService;



class MessageController extends Controller{
    
    
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index($booking_id)
    {
    $booking = Booking::findOrFail($booking_id);
    $messages = Message::where('booking_id', $booking_id)->orderBy('created_at', 'desc')->get();
    
              $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

    return view('messages.index', compact('booking', 'messages','drivers'));
    }

    // Customer SMS
//     public function customerBooking() {
        
//                   $driversData = $this->firebase->getData('drivers') ?? [];
// $drivers = collect();

// foreach ($driversData as $id => $driver) {
//     $driver['id'] = $id;
//     $drivers->push($driver);
// }
//         return view('messages.customer.booking', compact('drivers'));
//     }
public function customerBooking()
{
    // Load drivers (existing code)
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();

    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }

    // 🔥 Load Booking Confirmation SMS Template from Firebase
    $smsTemplate = $this->firebase->getData('sms_templates/booking_confirmation') 
        ?? ['message' => ''];

    // Convert to object so Blade works with ->message
    $smsTemplate = (object) $smsTemplate;

    return view('messages.customer.booking', compact('drivers','smsTemplate'));
}

    
    // public function customerBookingSend(Request $request)
    // {
    //     $validated = $request->validate([
    //         'message' => 'required|string|max:500',
    //     ]);

    //     // You can save the message to DB or .env depending on your logic
    //     // For now, let’s just simulate success
    //     return back()->with('success', 'SMS template updated successfully!');
    // }
    public function customerBookingSend(Request $request)
{
    $validated = $request->validate([
        'message' => 'required|string|max:2000', // booking sms is long
    ]);

    // 🔥 Save to Firebase
    $this->firebase->updateData("sms_templates/booking_confirmation", [
        'message' => $request->message,
        'updated_at' => now()->toDateTimeString(),
    ]);

    return back()->with('success', 'Booking confirmation SMS template updated successfully!');
}


//     public function customerOnroute() {
        
//                   $driversData = $this->firebase->getData('drivers') ?? [];
// $drivers = collect();

// foreach ($driversData as $id => $driver) {
//     $driver['id'] = $id;
//     $drivers->push($driver);
// }
//         return view('messages.customer.onroute', compact('drivers'));
//     }
    
    
//     public function customerOnrouteSend(Request $request)
//     {
//         $validated = $request->validate([
//             'message' => 'required|string|max:500',
//         ]);

//         // You can save the message to DB or .env depending on your logic
//         // For now, let’s just simulate success
//         return back()->with('success', 'SMS template updated successfully!');
//     }

public function customerOnroute()
{
    // Load drivers (your existing code)
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();

    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }

    // 🔥 Load On-Route SMS template from Firebase
    $smsTemplate = $this->firebase->getData('sms_templates/onroute') 
        ?? ['message' => ''];

    // Convert array → object so Blade works with ->message
    $smsTemplate = (object) $smsTemplate;

    // Return to Blade
    return view('messages.customer.onroute', compact('drivers', 'smsTemplate'));
}

public function customerOnrouteSend(Request $request)
{
    $validated = $request->validate([
        'message' => 'required|string|max:1000', // on-route messages can be long
    ]);

    // 🔥 Save message into Firebase
    $this->firebase->updateData("sms_templates/onroute", [
        'message' => $request->message,
        'updated_at' => now()->toDateTimeString(),
    ]);

    return back()->with('success', 'On-route SMS template updated successfully!');
}


   public function customerArrival()
{
    // Load drivers (existing code)
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();

    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }

    // 🔥 Load Arrival SMS Template from Firebase
    $template = $this->firebase->getData('sms_templates/arrival') ?? ['message' => ''];
    
    
    // print_r($template);
    // die;

    // Send the stored message + drivers to the view
    return view('messages.customer.arrival', compact('drivers', 'template'));
}

    
    // public function customerArrivalSend(Request $request)
    // {
    //     $validated = $request->validate([
    //         'message' => 'required|string|max:500',
    //     ]);

    //     // You can save the message to DB or .env depending on your logic
    //     // For now, let’s just simulate success
    //     return back()->with('success', 'SMS template updated successfully!');
    // }
    
    public function customerArrivalSend(Request $request)
{
    $validated = $request->validate([
        'message' => 'required|string|max:5000',
    ]);

    // Store in Firebase using your FirebaseService
    $this->firebase->updateData('sms_templates/arrival', [
        'message' => $validated['message'],
        'updated_at' => now()->toDateTimeString()
    ]);

    return back()->with('success', 'Arrival SMS template saved to Firebase successfully!');
}


//     public function customerComplete() {
        
//                   $driversData = $this->firebase->getData('drivers') ?? [];
// $drivers = collect();

// foreach ($driversData as $id => $driver) {
//     $driver['id'] = $id;
//     $drivers->push($driver);
// }
//         return view('messages.customer.complete', compact('drivers'));
//     }
    
    
//     public function customerCompleteSend(Request $request)
//     {
//         $validated = $request->validate([
//             'message' => 'required|string|max:500',
//         ]);

//         // You can save the message to DB or .env depending on your logic
//         // For now, let’s just simulate success
//         return back()->with('success', 'SMS template updated successfully!');
//     }

public function customerComplete()
{
    // Load drivers (existing code)
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();

    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }

    // 🔥 Load Completed SMS template
    $smsTemplate = $this->firebase->getData('sms_templates/complete') 
        ?? ['message' => ''];

    // Convert array to object so Blade can use ->message
    $smsTemplate = (object) $smsTemplate;

    // Send to view
    return view('messages.customer.complete', compact('drivers', 'smsTemplate'));
}

public function customerCompleteSend(Request $request)
{
    $validated = $request->validate([
        'message' => 'required|string|max:1000', // allow bigger messages
    ]);

    // 🔥 Save the template into Firebase
    $this->firebase->updateData("sms_templates/complete", [
        'message' => $request->message,
        'updated_at' => now()->toDateTimeString(),
    ]);

    return back()->with('success', 'Completed SMS template updated successfully!');
}


    // Driver SMS
//     public function driverDetails() {
        
//         $driversData = $this->firebase->getData('drivers') ?? [];
// $drivers = collect();

// foreach ($driversData as $id => $driver) {
//     $driver['id'] = $id;
//     $drivers->push($driver);
// }
//         return view('messages.driver.details', compact('drivers'));
//     }
    
    
//     public function driverDetailsSend(Request $request)
//     {
//         $validated = $request->validate([
//             'message' => 'required|string|max:500',
//         ]);

//         // You can save the message to DB or .env depending on your logic
//         // For now, let’s just simulate success
//         return back()->with('success', 'SMS template updated successfully!');
//     }

public function driverDetails()
{
    // Load drivers from Firebase
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();

    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }

    // 🔥 Load Driver Details SMS template from Firebase
    $smsTemplate = $this->firebase->getData('sms_templates/driver_details')
        ?? ['message' => ''];

    // Convert array to object for Blade usage
    $smsTemplate = (object) $smsTemplate;

    return view('messages.driver.details', compact('drivers', 'smsTemplate'));
}
public function driverDetailsSend(Request $request)
{
    $validated = $request->validate([
        'message' => 'required|string|max:1000',
    ]);

    // 🔥 Store the driver job details SMS template into Firebase
    $this->firebase->updateData('sms_templates/driver_details', [
        'message' => $request->message,
        'updated_at' => now()->toDateTimeString(),
    ]);

    return back()->with('success', 'Driver Details SMS template updated successfully!');
}


    public function driverChange() {
        
        $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}
        return view('messages.driver.change', compact('drivers'));
    }
    
    
    public function driverChangeSend(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // You can save the message to DB or .env depending on your logic
        // For now, let’s just simulate success
        return back()->with('success', 'SMS template updated successfully!');
    }

    public function driverOffice() {
        
        $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}
        return view('messages.driver.office', compact('drivers'));
    }
    
    
    public function driverOfficeSend(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // You can save the message to DB or .env depending on your logic
        // For now, let’s just simulate success
        return back()->with('success', 'SMS template updated successfully!');
    }
}

