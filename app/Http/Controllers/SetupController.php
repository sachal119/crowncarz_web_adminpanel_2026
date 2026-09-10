<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Account;
use App\Models\Staff;
use App\Mail\StaffCredentialsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Services\FirebaseService;

class SetupController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    
public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'call_sign' => 'nullable|string|max:50',
        'email' => 'required|email',
        'phone' => 'required|string|max:20',
        'status' => 'required|in:available,on_job,break,waiting',
        'address' => 'nullable|string',
        'latitude' => 'nullable|string',
        'longitude' => 'nullable|string',
        'brought_forward' => 'nullable|numeric',
    ]);

    // 1️⃣ Get all drivers
    $drivers = $this->firebase
        ->getDatabase()
        ->getReference('drivers')
        ->getValue() ?? [];

    // 2️⃣ Find Firebase key by internal ID
    $firebaseKey = null;

    foreach ($drivers as $key => $driver) {
        if (isset($driver['id']) && (int)$driver['id'] === (int)$id) {
            $firebaseKey = $key;
            break;
        }
    }

    // 3️⃣ If not found
    if (!$firebaseKey) {
        return back()->with('error', 'Driver not found.');
    }

    // 4️⃣ Update correct Firebase node
    $driverRef = $this->firebase
        ->getDatabase()
        ->getReference('drivers/' . $firebaseKey);

    $driverRef->update([
        'name' => $request->name,
        'call_sign' => $request->call_sign,
        'email' => $request->email,
        'phone' => $request->phone,
        'status' => $request->status,
        'address' => $request->address,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        //'brought_forward' => $request->brought_forward ?? 0,
        'updated_at' => now()->toDateTimeString(),
    ]);

    return back()
        ->with('success', 'Driver updated successfully!')
        ->with('active_tab', 'driver');
}




    // In SetupController.php

public function index()
{
    if (!session('admin_logged_in')) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    try {
        // Fetch all data from Firebase
        // NOTE: The Firebase service must handle deserialization (JSON to PHP Array/Object)
        $staffData = $this->firebase->getData('staff');
        $driversData = $this->firebase->getData('drivers');
        $vehiclesData = $this->firebase->getData('vehicles');
        $customersData = $this->firebase->getData('customers');

        // Convert data to Laravel Collections for easy iteration in the view, 
        // or ensure your Firebase service handles this conversion.
        $staff = collect($staffData);
        $drivers = collect($driversData);
        $vehicles = collect($vehiclesData);
        $customers = collect($customersData);
        
        
        

        // Since Firebase data doesn't automatically include relationships (like 'vehicle.driver'),
        // you might need to handle the linkage manually in the view or controller 
        // if your Firebase data structure is flat.
        // For simplicity, we are passing the raw collections/arrays.

        return view('setup.index', compact('staff', 'drivers', 'vehicles', 'customers'));
    
    } catch (\Exception $e) {
        // Handle potential connection errors or data fetching failures from Firebase
        // Log the error and perhaps return an empty dataset or an error view.
        \Log::error("Firebase Data Fetch Error: " . $e->getMessage());
        
        // Return empty collections to prevent errors in the view structure
        $empty = collect([]);
        return view('setup.index', [
            'staff' => $empty,
            'drivers' => $empty,
            'vehicles' => $empty,
            'customers' => $empty,
        ])->with('error', 'Could not retrieve setup data from Firebase.');
    }
}

public function updateSuperAdminPassword(Request $request)
{
    abort_unless(session('admin_logged_in'), 403);
    abort_unless(session('staff_role') === 'super_admin', 403);

    // Accept both the standard Laravel field names and the names used by the
    // existing Setup modal on older live deployments.
    $request->merge([
        'password' => $request->input('password', $request->input('new_password')),
        'password_confirmation' => $request->input(
            'password_confirmation',
            $request->input('new_password_confirmation')
        ),
    ]);

    $validated = $request->validate([
        'current_password' => ['required', 'string'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $sessionId = session('staff_id') ?? session('admin_id');
    $sessionEmail = strtolower(trim((string) (
        session('staff_email') ?? session('admin_email') ?? session('email') ?? ''
    )));

    foreach (['staff', 'superadmin'] as $firebasePath) {
        $records = $this->firebase->getData($firebasePath) ?? [];

        foreach ($records as $firebaseKey => $record) {
            if (!is_array($record)) {
                continue;
            }

            $recordId = (string) ($record['id'] ?? $firebaseKey);
            $recordEmail = strtolower(trim((string) ($record['email'] ?? '')));
            $matchesSession = ($sessionId !== null && (string) $sessionId === $recordId)
                || ($sessionEmail !== '' && hash_equals($sessionEmail, $recordEmail));

            if (!$matchesSession) {
                continue;
            }

            $currentHash = (string) ($record['password'] ?? '');
            if ($currentHash === '' || !Hash::check($validated['current_password'], $currentHash)) {
                return back()
                    ->withErrors(['current_password' => 'The current password is incorrect.'])
                    ->withInput();
            }

            $this->firebase->getDatabase()
                ->getReference($firebasePath.'/'.$firebaseKey)
                ->update([
                    'password' => Hash::make($validated['password']),
                    'updated_at' => now()->toDateTimeString(),
                ]);

            return back()->with('success', 'Super admin password updated successfully.');
        }
    }

    return back()->withErrors([
        'current_password' => 'The logged-in super admin record could not be found.',
    ]);
}

    // Store Staff/User
    public function storeStaff(Request $request)
    {
        $request->merge([
            'password_confirmation' => $request->input(
                'password_confirmation',
                $request->input('confirm_password')
            ),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:staff,email', // Assuming 'staff' table
            'password' => 'required|min:6|confirmed',
            'phone' => 'required',
            'role' => 'nullable|in:admin,collaborator,staff',
        ]);
        
        
        

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('active_tab', 'staff'); // Redirect back to the staff tab
        }

        $validated = $validator->validated();
        $plainPassword = $validated['password'];
        $role = $validated['role'] ?? 'collaborator';

        $staff = Staff::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($plainPassword),
            'phone' => $validated['phone'],
        ]);

        $this->firebase->pushData('staff', array_merge($staff->toArray(), [
            'role' => $role,
        ]));

        try {
            Mail::to($staff->email)->send(new StaffCredentialsMail(
                staffName: $staff->name,
                staffEmail: $staff->email,
                temporaryPassword: $plainPassword,
                role: $role,
                loginUrl: route('login')
            ));
        } catch (\Throwable $exception) {
            \Log::error('Staff credentials email failed: '.$exception->getMessage(), [
                'staff_id' => $staff->id,
                'email' => $staff->email,
            ]);

            return redirect()->route('setup')
                ->with('success', 'Staff account created, but the credentials email could not be sent. Check the mail configuration/logs.')
                ->with('active_tab', 'staff');
        }

        return redirect()->route('setup')
            ->with('success', 'Staff account created and login credentials emailed successfully.')
            ->with('active_tab', 'staff');
    }

    public function updateStaffAccess(Request $request, $id)
    {
        abort_unless(session('admin_logged_in'), 403);
        abort_unless(session('staff_role') === 'super_admin', 403);

        $validated = $request->validate([
            'role' => ['required', 'in:admin,collaborator,staff'],
        ]);

        if ((string) session('staff_id') === (string) $id) {
            return back()
                ->withErrors(['role' => 'You cannot change your own access level.'])
                ->with('active_tab', 'staff');
        }

        $firebaseKey = $this->findFirebaseStaffKey($id);
        if ($firebaseKey === null) {
            return back()
                ->withErrors(['role' => 'Staff account was not found.'])
                ->with('active_tab', 'staff');
        }

        $this->firebase->updateData('staff/'.$firebaseKey, [
            'role' => $validated['role'],
            'updated_at' => now()->toDateTimeString(),
        ]);

        return back()
            ->with('success', 'Staff access updated successfully.')
            ->with('active_tab', 'staff');
    }

    public function destroyStaff($id)
    {
        abort_unless(session('admin_logged_in'), 403);
        abort_unless(session('staff_role') === 'super_admin', 403);

        if ((string) session('staff_id') === (string) $id) {
            return back()
                ->withErrors(['staff' => 'You cannot delete your own account.'])
                ->with('active_tab', 'staff');
        }

        $firebaseKey = $this->findFirebaseStaffKey($id);
        if ($firebaseKey === null) {
            return back()
                ->withErrors(['staff' => 'Staff account was not found.'])
                ->with('active_tab', 'staff');
        }

        $this->firebase->deleteData('staff/'.$firebaseKey);
        Staff::query()->whereKey($id)->delete();

        return back()
            ->with('success', 'Staff account deleted successfully.')
            ->with('active_tab', 'staff');
    }

    private function findFirebaseStaffKey($id): ?string
    {
        $staffRecords = $this->firebase->getData('staff') ?? [];

        foreach ($staffRecords as $firebaseKey => $record) {
            if (!is_array($record)) {
                continue;
            }

            if ((string) ($record['id'] ?? $firebaseKey) === (string) $id) {
                return (string) $firebaseKey;
            }
        }

        return null;
    }

    // Store Driver
    public function storeDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:drivers,email',
            'phone'     => 'required|string|max:255',
            'status'    => 'required|in:available,on_job,break,waiting',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'call_sign' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('active_tab', 'driver'); // Redirect back to the driver tab
        }

    //     $driver = Driver::create([
    //         'name'      => $request->name,
    //         'email'     => $request->email,
    //         'phone'     => $request->phone,
    //         'status'    => $request->status,
    //         'latitude'  => $request->latitude,
    //         'longitude' => $request->longitude,
    //     ]);
        
    //     // Get the validated data array
    // $validatedData = $validator->validated();

    // // 1. Store in SQL Database (Eloquent)
    // // Use $validatedData to ensure all validated fields are used, even if they are nullable.
    // // Ensure your Driver model has mass-assignment protection set up (fillable/guarded).
    // $driver = Driver::create($validatedData);

    // // 2. Store in Firebase Realtime Database
    // // We pass the clean, validated array to the Firebase service.
    // // If you only want to save specific fields to Firebase, use:
    // // $firebaseData = $request->only(['name', 'email', 'phone', 'status', 'call_sign', 'address']);
    
    // // Using the full validated data for consistency, with the 'id' from the SQL database
    // $firebaseData = $validatedData;

    //     // $driverRef = app('firebase.database')->getReference('drivers')->push($validator);

    //   $this->firebase->pushData('drivers', $firebaseData);
       
       // 1️⃣ Store in SQL
    $driver = Driver::create($validator->validated());

    // 2️⃣ Store in Firebase WITH SQL ID
    $firebaseData = array_merge(
        $validator->validated(),
        ['id' => $driver->id] // 🔥 THIS IS THE KEY FIX
    );

    $this->firebase->pushData('drivers', $firebaseData);

        return redirect()->route('setup')->with('success', 'Driver added successfully.')->with('active_tab', 'driver');
    }

    // Store Vehicle
    public function storeVehicle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required',
            'model' => 'required',
            'color' => 'required',
            'registration' => 'required|unique:vehicles,registration',
            'driver_id' => 'required|exists:drivers,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('active_tab', 'vehicle'); // Redirect back to the vehicle tab
        }

        $vehicle = Vehicle::create([
            'make' => $request->make,
            'model' => $request->model,
            'color' => $request->color,
            'registration' => $request->registration,
            'driver_id' => $request->driver_id,
        ]);

        $this->firebase->pushData('vehicles', $vehicle->toArray());

        return redirect()->route('setup')->with('success', 'Vehicle added successfully.')->with('active_tab', 'vehicle');
    }

    // Store Customer Account
    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:accounts,email',
            'phone' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('active_tab', 'customer'); // Redirect back to the customer tab
        }

        $customer = Account::create([
            'business_name' => $request->business_name,
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $this->firebase->pushData('customers', $customer->toArray());

        return redirect()->route('setup')->with('success', 'Customer account added successfully.')->with('active_tab', 'customer');
    }
    
    
    public function bfHistory($driverId)
{
    $driver = $this->firebase->getData("drivers/$driverId");

    $history = $this->firebase->getData("driver_bf_history/$driverId") ?? [];
    
    $driversData = $this->firebase->getData('drivers');
        $drivers = collect($driversData);

    return view('drivers.bf_history', [
        'drivers' => $drivers,
        'driver' => $driver,
        'history' => $history,
        'driverId' => $driverId
    ]);
}


public function updateBF(Request $request, $driverId)
{
    $request->validate([
        'amount' => 'required|numeric',
        'type' => 'required|in:add,subtract',
        'comment' => 'nullable|string'
    ]);

   // Find the real Firebase key by searching for matching 'id'
    $drivers = $this->firebase->getData('drivers') ?? [];

    $realDriverKey = null;
    foreach ($drivers as $key => $driver) {
        if (isset($driver['id']) && $driver['id'] == $driverId) {
            $realDriverKey = $key;
            break;
        }
    }

    if (!$realDriverKey) {
        return redirect()->back()->with('error', 'Driver not found');
    }

    $driverPath = "drivers/{$realDriverKey}";

    $driver = $this->firebase->getData($driverPath);

    $currentBF = floatval($driver['brought_forward'] ?? 0);
    $amount = floatval($request->amount);

    $newBF = $request->type === 'add'
        ? $currentBF + $amount
        : $currentBF - $amount;

    // Update driver BF
   $this->firebase->updateData($driverPath, [
        'brought_forward' => $newBF
    ]);

    // Store history
    $this->firebase->pushData("driver_bf_history/$driverId", [
        'amount' => $amount,
        'type' => $request->type,
        'comment' => $request->comment,
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);

    return redirect()->back()->with('success', 'Brought Forward updated');
}

    public function updateBalanceHistory(Request $request, $driverId, $transactionId)
{
    $request->validate([
        'amount' => 'required|numeric',
        'type'   => 'required|in:add,subtract'
    ]);

    $path = "driver_bf_history/$driverId/$transactionId";

    $history = $this->firebase->getData($path);

    if (!$history) {
        return back()->with('error', 'Transaction not found');
    }

    // 1️⃣ Update transaction
    $updateData = [
        'amount'  => $request->amount,
        'type'    => $request->type,
        'comment' => $request->comment ?? null,
    ];

    $this->firebase->updateData($path, $updateData);
    
    $drivers = $this->firebase->getData('drivers') ?? [];
    
    $realDriverKey = null;
    foreach ($drivers as $key => $driver) {
        if (isset($driver['id']) && $driver['id'] == $driverId) {
            $realDriverKey = $key;
            break;
        }
    }

    if (!$realDriverKey) {
        return redirect()->back()->with('error', 'Driver not found');
    }

    $driverPath = "drivers/{$realDriverKey}";

    $driver = $this->firebase->getData($driverPath);

    // 2️⃣ Recalculate total balance
    $allTransactions = $this->firebase->getData("driver_bf_history/$driverId");

    $newBalance = 0;

    if ($allTransactions) {
        foreach ($allTransactions as $transaction) {
            if ($transaction['type'] === 'add') {
                $newBalance += (float) $transaction['amount'];
            } else {
                $newBalance -= (float) $transaction['amount'];
            }
        }
    }

    // 3️⃣ Update driver's main balance
    $this->firebase->updateData("drivers/$realDriverKey", [
        'brought_forward' => $newBalance
    ]);

    return back()->with('success', 'Transaction updated & balance recalculated successfully');
}
public function deleteBalanceHistory($driverId, $transactionId)
{
    $path = "driver_bf_history/$driverId/$transactionId";
    
    

    $this->firebase->deleteData($path);

    // Recalculate balance
    $allTransactions = $this->firebase->getData("driver_bf_history/$driverId");
    
    

    $newBalance = 0;

    if ($allTransactions) {
        foreach ($allTransactions as $transaction) {
            if ($transaction['type'] === 'add') {
                $newBalance += (float) $transaction['amount'];
            } else {
                $newBalance -= (float) $transaction['amount'];
            }
        }
    }
    
    $drivers = $this->firebase->getData('drivers') ?? [];
    
    $realDriverKey = null;
    foreach ($drivers as $key => $driver) {
        if (isset($driver['id']) && $driver['id'] == $driverId) {
            $realDriverKey = $key;
            break;
        }
    }

    if (!$realDriverKey) {
        return redirect()->back()->with('error', 'Driver not found');
    }

    $driverPath = "drivers/{$realDriverKey}";

    $this->firebase->updateData("drivers/$realDriverKey", [
        'brought_forward' => $newBalance
    ]);

    return back()->with('success', 'Transaction deleted & balance updated');
}

    public function findJobByRef(Request $request)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $ref = trim($request->input('ref_no', ''));
        if ($ref === '') {
            return response()->json(['success' => false, 'message' => 'Please provide a Reference #.']);
        }

        $cleanRef = strtoupper(ltrim($ref, '#'));
        $rawRef = $ref;

        $database = $this->firebase->getDatabase();
        $bookings = $database->getReference('bookings')->getValue() ?? [];

        $matched = [];
        foreach ($bookings as $key => $b) {
            $bookingRef = strtoupper(trim($b['ref_no'] ?? ''));
            $bookingId = (string) ($b['id'] ?? '');

            if (
                $bookingRef === $cleanRef ||
                $bookingRef === strtoupper($rawRef) ||
                (string)$key === $rawRef ||
                (string)$key === $cleanRef ||
                $bookingId === $rawRef ||
                $bookingId === $cleanRef
            ) {
                $matched[] = [
                    'key' => $key,
                    'ref_no' => $b['ref_no'] ?? $key,
                    'passenger_name' => $b['passenger_name'] ?? 'N/A',
                    'phone_no' => $b['phone_no'] ?? 'N/A',
                    'pickup_date' => $b['pickup_date'] ?? '',
                    'pickup_time' => $b['pickup_time'] ?? '',
                    'pickup_address' => $b['pickup_address'] ?? 'N/A',
                    'dropoff_address' => $b['dropoff_address'] ?? 'N/A',
                    'price' => $b['price'] ?? 0,
                    'status' => ucfirst($b['status'] ?? 'pending'),
                ];
            }
        }

        if (empty($matched)) {
            try {
                $sqlBooking = \App\Models\Booking::where('ref_no', $rawRef)
                    ->orWhere('ref_no', $cleanRef)
                    ->orWhere('id', $rawRef)
                    ->first();

                if ($sqlBooking) {
                    $matched[] = [
                        'key' => 'mysql_' . $sqlBooking->id,
                        'ref_no' => $sqlBooking->ref_no ?? ('ID: ' . $sqlBooking->id),
                        'passenger_name' => $sqlBooking->passenger->name ?? 'N/A',
                        'phone_no' => $sqlBooking->passenger->phone ?? 'N/A',
                        'pickup_date' => '',
                        'pickup_time' => '',
                        'pickup_address' => $sqlBooking->pickup_address ?? 'N/A',
                        'dropoff_address' => $sqlBooking->dropoff_address ?? 'N/A',
                        'price' => $sqlBooking->price ?? 0,
                        'status' => ucfirst($sqlBooking->status ?? 'pending'),
                    ];
                }
            } catch (\Throwable $e) {
                // Ignore fallback error
            }
        }

        if (empty($matched)) {
            return response()->json([
                'success' => false,
                'message' => "No job found matching Reference # '{$ref}'."
            ]);
        }

        return response()->json([
            'success' => true,
            'count' => count($matched),
            'jobs' => $matched,
        ]);
    }

    public function deleteJobByRef(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $request->validate([
            'ref_no' => 'required|string|max:100',
        ]);

        $ref = trim($request->input('ref_no'));
        $cleanRef = strtoupper(ltrim($ref, '#'));
        $rawRef = $ref;

        $database = $this->firebase->getDatabase();
        $bookings = $database->getReference('bookings')->getValue() ?? [];

        $deletedCount = 0;
        $deletedDetails = [];

        foreach ($bookings as $key => $b) {
            $bookingRef = strtoupper(trim($b['ref_no'] ?? ''));
            $bookingId = (string) ($b['id'] ?? '');

            if (
                $bookingRef === $cleanRef ||
                $bookingRef === strtoupper($rawRef) ||
                (string)$key === $rawRef ||
                (string)$key === $cleanRef ||
                $bookingId === $rawRef ||
                $bookingId === $cleanRef
            ) {
                $pName = $b['passenger_name'] ?? 'N/A';
                $rNo = $b['ref_no'] ?? $key;
                $deletedDetails[] = "{$rNo} (Passenger: {$pName})";
                $database->getReference('bookings/' . $key)->remove();
                $deletedCount++;
            }
        }

        try {
            $sqlDeleted = \App\Models\Booking::where('ref_no', $rawRef)
                ->orWhere('ref_no', $cleanRef)
                ->orWhere('id', $rawRef)
                ->delete();
            $deletedCount += $sqlDeleted;
        } catch (\Throwable $e) {
            // Ignore fallback error
        }

        if ($deletedCount > 0) {
            $detailsStr = !empty($deletedDetails) ? ' [' . implode(', ', $deletedDetails) . ']' : '';
            $msg = "Job with Ref # '{$ref}'{$detailsStr} has been permanently deleted from the database.";
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'deleted_count' => $deletedCount,
                ]);
            }
            return redirect()->route('setup')->with('success', $msg);
        }

        $errorMsg = "No job found with Ref # '{$ref}'. Nothing was deleted.";
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMsg,
            ], 404);
        }
        return redirect()->route('setup')->with('error', $errorMsg);
    }
    
}
