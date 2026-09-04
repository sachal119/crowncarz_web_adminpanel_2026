<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService; // Assuming this service is correctly configured
// The following are technically not needed if FirebaseService handles creation, but are okay for type-hinting:
use Kreait\Firebase\Factory; 
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\InvalidArgumentException; 
// The following are no longer needed:
// use GuzzleHttp\Client; 
// use Exception;

class NotificationController extends Controller
{
    protected $firebase;
    // protected $client; // REMOVED: Guzzle client is no longer needed

    public function __construct(FirebaseService $firebase)
    {
        // Inject the custom Firebase service
        $this->firebase = $firebase;
        // The Guzzle client initialization is removed as it's not used.
    }
    
    /**
     * Show notification form
     */
    public function create()
    {
        
        $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}
        return view('admin.notifications.create', compact('drivers'));
    }

    /**
     * Send notification & store in Firebase
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'topic' => 'required|in:crownCarzDriver,crownCarzUser,crownCarzAll',
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        try {
            // 2. Get the Messaging instance from the injected service.
            // This assumes your FirebaseService has a method like getMessaging().
            // If it doesn't, you need to add it or use app('firebase.messaging').
            $messaging = $this->firebase->getMessaging(); // USE THE SERVICE INSTEAD OF MANUAL FACTORY

            // 3. Build the CloudMessage using validated request data
            $message = CloudMessage::fromArray([
    'topic' => $request->topic,
    'notification' => [
        'title' => $request->title,
        'body'  => $request->body,
    ],
    'data' => [
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        'screen' => 'home',
    ],
    // The older method requires nested arrays for platform-specific options
    'apns' => [ 
        'headers' => ['apns-priority' => '10'], 
    ],
    'webpush' => [
        'notification' => ['title' => $request->title, 'body' => $request->body],
    ],
]);

$messaging->send($message);


            // 4. Send the Notification
            $messaging->send($message); 

            \Log::info("FCM Notification Sent", [
                'topic' => $request->topic,
                'title' => $request->title,
            ]);
            
            // 5. Save notification in Firebase Realtime DB
            $this->firebase->getDatabase() // Use the injected service for the database
                ->getReference('notifications')
                ->push([
                    'topic' => $request->topic,
                    'title' => $request->title,
                    'body'  => $request->body,
                    'created_at' => now()->toDateTimeString()
                ]);

            return redirect()->route('notifications.history')
                ->with('success', 'Notification sent & stored successfully!');

        } catch (MessagingException $e) {
            \Log::error("FCM Send Failed", ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send notification: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Catch initialization/DB errors or other unexpected issues
            \Log::error("Notification Failed (General Error)", ['error' => $e->getMessage()]);
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    /**
     * Show notification history (from Firebase)
     */
    public function history()
    {
        $notifications = $this->firebase->getDatabase()
            ->getReference('notifications')
            ->getValue();

        // Reverse order (latest first)
        $notifications = $notifications ? array_reverse($notifications) : [];
        
        $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

        return view('admin.notifications.history', compact('notifications','drivers'));
    }
}
