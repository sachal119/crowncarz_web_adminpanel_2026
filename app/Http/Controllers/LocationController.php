<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class LocationController extends Controller
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
        $airports = $this->firebase->getData('airports') ?? [];
        $stations = $this->firebase->getData('stations') ?? [];
        $ports = $this->firebase->getData('ports') ?? [];
        
                  $driversData = $this->firebase->getData('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

        return view('locations.index', compact('airports', 'stations', 'ports','drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'            => 'required|in:airport,station,port',
            'address'         => 'required|string|max:255',
            'pickup_charge'   => 'nullable|numeric|min:0',
            'dropoff_charge'  => 'nullable|numeric|min:0',
            'extras'          => 'nullable|string|max:255',
            'post_code'       => 'nullable|string|max:50',
        ]);

        $data = [
            'address'         => $request->address,
            'pickup_charge'   => $request->pickup_charge,
            'dropoff_charge'  => $request->dropoff_charge,
            'extras'          => $request->extras,
            'post_code'       => $request->post_code,
            'created_at'      => now()->toDateTimeString(),
        ];

        $table = match ($request->type) {
            'airport' => 'airports',
            'station' => 'stations',
            'port'    => 'ports',
        };

        $this->firebase->pushData($table, $data);

        return redirect()->back()->with('success', ucfirst($request->type) . ' added successfully.');
    }

    public function destroy($type, $id)
    {
        $table = match ($type) {
            'airport' => 'airports',
            'station' => 'stations',
            'port'    => 'ports',
        };

        $this->firebase->deleteData("$table/$id");

        return redirect()->back()->with('success', ucfirst($type) . ' deleted successfully.');
    }
    
    public function update(Request $request, $type, $id)
{
    $validated = $request->validate([
        'address' => 'required|string|max:255',
        'pickup_charge' => 'required|numeric',
        'dropoff_charge' => 'required|numeric',
        'extras' => 'nullable|string',
        'post_code' => 'nullable|string|max:20',
    ]);

    try {
        $this->firebase->updateData("{$type}s/{$id}", $validated); 
        return back()->with('success', ucfirst($type) . ' updated successfully.');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to update: ' . $e->getMessage());
    }
}

}
