<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class SystemSettingsController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
        // Fetch settings from Firebase
        $settings = $this->firebase->getDatabase()
            ->getReference('system_settings')
            ->getValue() ?? [];
            
                  $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}    

        return view('admin.system-settings', compact('settings','drivers'));
    }

    public function update(Request $request)
{
    $data = $request->only([
        'email', 'phone', 'address', 'whatsapp',
        'facebook', 'twitter', 'instagram', 'linkedin',
        'tiktok', 'snapchat', 'playstore', 'appstore',
        'currency','autocomplete_key'
    ]);

    $ref = $this->firebase->getDatabase()->getReference('system_settings');

    // Check if node exists
    $existing = $ref->getValue();

    if ($existing) {
        // If exists, update only given fields
        $ref->update($data);
    } else {
        // If not exists, create new record
        $ref->set($data);
    }

    return redirect()->route('system.settings')
        ->with('success', 'System settings saved successfully!');
}

}
