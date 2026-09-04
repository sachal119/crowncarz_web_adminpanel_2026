<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CallSwitchWebhookController;
use App\Http\Controllers\PageController;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $routeName = $route?->getName();
        $controller = $route?->getControllerClass();

        $publicRoutes = [
            'login', 'login.save', 'signup', 'signup.save',
            'otp.verify.form', 'otp.verify', 'receipt.download',
            'payment.success', 'payment.cancel',
            'webhooks.callswitch.new-call', 'call-notifications.latest',
            'logout',
        ];

        if (in_array($routeName, $publicRoutes, true)
            || in_array($controller, [PageController::class, CallSwitchWebhookController::class], true)) {
            return $next($request);
        }

        if (session('blog_logged_in') && $controller === AdminPageController::class) {
            return $next($request);
        }

        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Refresh staff access from MySQL on every request. This makes role
        // changes immediate and logs a deleted staff account out safely.
        if (session('staff_id')) {
            $loggedInStaff = Staff::find(session('staff_id'));

            if (! $loggedInStaff) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Your staff account no longer has access.');
            }

            session([
                'staff_role' => $loggedInStaff->role ?: 'collaborator',
                'staff_name' => $loggedInStaff->name,
            ]);
        }

        $role = session('staff_role', 'super_admin');

        if ($routeName === 'setup.super-admin.password' && $role !== 'super_admin') {
            abort(403, 'Only the Super Admin can change this password.');
        }

        if ($role === 'collaborator') {
            $isBookingRoute = $controller === BookingController::class;
            $isBookingSms = $request->is('sms/send');
            $isLogout = $routeName === 'logout';

            if (! $isBookingRoute && ! $isBookingSms && ! $isLogout) {
                abort(403, 'You do not have permission to access this section.');
            }
        }

        return $next($request);
    }
}
