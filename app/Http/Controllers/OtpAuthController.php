<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendOtpMail;
use App\Services\FirebaseService;
use App\Models\Staff;
use App\Models\AdminCredential;
use Illuminate\Support\Facades\Schema;

class OtpAuthController extends Controller
{
    protected $database;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->database = $firebaseService->getDatabase();
    }

    public function showSignup() {
        return view('auth.signup');
    }

    public function signup(Request $request) {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Generate a unique user ID
        $userId = uniqid('superadmin_');

        // Store in Firebase
        $this->database->getReference("superadmin/{$userId}")->set([
            'id'       => $userId,
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_verified' => false,
        ]);

        return redirect()->route('login')->with('success', 'Account created! Please login.');
    }

    public function showLogin() {
        
        if(session('blog_logged_in'))
        {
            return redirect()->route('pages.index')
            ->with('success', 'Welcome back!');
        }
        
        if(session('admin_logged_in'))
        {
            return redirect()->route('dashboard')
            ->with('success', 'Welcome back!');
        }
            
        
        
        return view('auth.login');
    }
    
    
    
public function login(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $email = strtolower(trim($request->email));
    $password = $request->password;

    // The original password is used only until the Super Admin changes it
    // for the first time. Afterwards, only the MySQL hash is accepted.
    if ($email === "info@crowncarz.com") {
        $adminCredential = Schema::hasTable('admin_credentials')
            ? AdminCredential::where('email', $email)->first()
            : null;
        $validAdminPassword = $adminCredential
            ? Hash::check($password, $adminCredential->password)
            : $password === "L@hore123";

        if (! $validAdminPassword) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Invalid email or password.');
        }

        // Store session if needed
        $request->session()->regenerate();
        session([
            'admin_logged_in' => true,
            'staff_role' => 'super_admin',
            'staff_name' => 'Super Admin',
            'staff_id' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome back!');
    }
    
    if ($email === "blog@crowncarz.com" && $password === "Blog123") {
        // Store session if needed
        $request->session()->regenerate();
        session(['blog_logged_in' => true]);

        return redirect()->route('pages.index')
            ->with('success', 'Welcome back!');
    }

    $staff = Staff::whereRaw('LOWER(email) = ?', [$email])->first();

    if ($staff && Hash::check($password, $staff->password)) {
        $request->session()->regenerate();
        session([
            'admin_logged_in' => true,
            'staff_role' => $staff->role ?: 'collaborator',
            'staff_name' => $staff->name,
            'staff_id' => $staff->id,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome back, ' . $staff->name . '!');
    }

    return back()->withInput($request->only('email'))
        ->with('error', 'Invalid email or password.');
}


    // public function login(Request $request) {
    //     // Find user by email in Firebase
    //     $users = $this->database->getReference("superadmin")->getValue();
    //     $user = collect($users)->firstWhere('email', $request->email);

    //     if (!$user || !Hash::check($request->password, $user['password'])) {
    //         return back()->withErrors(['email' => 'Invalid credentials']);
    //     }

    //     // generate OTP
    //     $otp = rand(100000, 999999);

    //     // store OTP in Firebase under user
    //     $this->database->getReference("superadmin/{$user['id']}/otp")->set([
    //         'code' => $otp,
    //         'expires_at' => now()->addMinutes(5)->timestamp,
    //     ]);

    //     // Send OTP via email
    //     // Mail::to($user['email'])->send(new SendOtpMail($otp));

    //     // store in session
    //     session(['otp_user_id' => $user['id']]);

    //     return redirect()->route('dashboard')->with('success', 'OTP sent to your email.');
    // }

    public function showOtpForm() {
        return view('auth.otp');
    }

    public function verifyOtp(Request $request) {
        $request->validate(['otp' => 'required|digits:6']);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired.']);
        }

        // get user from Firebase
        $user = $this->database->getReference("superadmin/{$userId}")->getValue();

        if (!$user || empty($user['otp'])) {
            return back()->withErrors(['otp' => 'OTP not found.']);
        }

        $otpData = $user['otp'];

        if ($otpData['code'] == $request->otp && $otpData['expires_at'] > now()->timestamp) {
            // mark verified
            $this->database->getReference("superadmin/{$userId}")->update([
                'is_verified' => true,
            ]);

            // clear OTP
            $this->database->getReference("superadmin/{$userId}/otp")->remove();

            // login using Laravel Auth (optional: if you want session login with dummy User model)
            $fakeUser = new \App\Models\User([
                'id' => $userId,
                'name' => $user['name'],
                'email' => $user['email'],
            ]);
            Auth::login($fakeUser);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }
}


// namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Mail;
// use App\Models\User;
// use App\Mail\SendOtpMail;


// class OtpAuthController extends Controller
// {
//     public function showSignup() {
//         return view('auth.signup');
//     }

//     public function signup(Request $request) {
//         $request->validate([
//             'name' => 'required',
//             'phone' => 'required',
//             'email' => 'required|email|unique:users',
//             'password' => 'required|min:6',
//         ]);

//         User::create([
//             'name' => $request->name,
//             'phone' => $request->phone,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//         ]);

//         return redirect()->route('login')->with('success', 'Account created! Please login.');
//     }

//     public function showLogin() {
//         return view('auth.login');
//     }


    

//     public function login(Request $request) {
//         // $request->validate([
//         //     'email' => 'required|email',
//         //     'password' => 'required',
//         // ]);


        

//         $user = User::where('email', $request->email)->first();

//         if (!$user || !Hash::check($request->password, $user->password)) {
//             return back()->withErrors(['email' => 'Invalid credentials']);
//         }

//         // generate OTP
//         $otp = rand(100000, 999999);
//         $user->otp_code = $otp;
//         $user->otp_expires_at = now()->addMinutes(5);
//         $user->save();

//         // Send OTP email
//     Mail::to($user->email)->send(new SendOtpMail($otp));

//         // // send OTP email
//         // Mail::raw("Your login OTP is: $otp", function($msg) use ($user) {
//         //     $msg->to($user->email)->subject('Crown Carz Login OTP');
//         // });

//         session(['otp_user_id' => $user->id]);

//         return redirect()->route('otp.verify.form')->with('success', 'OTP sent to your email.');
//     }

//     public function showOtpForm() {
//         return view('auth.otp');
//     }

//     public function verifyOtp(Request $request) {
//         $request->validate(['otp' => 'required|digits:6']);

//         $user = User::find(session('otp_user_id'));

//         if (!$user) {
//             return redirect()->route('login')->withErrors(['otp' => 'Session expired.']);
//         }

//         if ($user->otp_code == $request->otp && $user->otp_expires_at->isFuture()) {
//             $user->is_verified = true;
//             $user->otp_code = null;
//             $user->otp_expires_at = null;
//             $user->save();

//             Auth::login($user);

//             return redirect()->intended('/dashboard');
//         }

//         return back()->withErrors(['otp' => 'Invalid or expired OTP']);
//     }
// }
