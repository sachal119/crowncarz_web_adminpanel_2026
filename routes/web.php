<?php


use Illuminate\Support\Facades\Route;

if (app()->environment('local')) {
    Route::get('/public/{path}', fn (string $path) => redirect(asset($path)))
        ->where('path', '.*');
}
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuickLinkController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CallSwitchWebhookController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

Route::post(
    '/webhooks/callswitch/new-call',
    [CallSwitchWebhookController::class, 'receive']
)
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('webhooks.callswitch.new-call');

Route::get(
    '/call-notifications/latest',
    [CallSwitchWebhookController::class, 'latest']
)
    ->middleware('signed')
    ->name('call-notifications.latest');

Route::get('/customers/search', [BookingController::class, 'searchCustomer'])
    ->name('customers.search');
    


Route::get('/bookings/previous', [BookingController::class, 'previous'])
    ->name('bookings.previous');


Route::get('/receipt/{id}', [BookingController::class, 'downloadReceipt'])
    ->name('receipt.download');

Route::post('fixed-prices/update/{id}', [PricingController::class, 'update'])
      ->name('fixed-prices.update');



Route::post('bookings/{id}/update-status-manual', [BookingController::class, 'updateStatusManual'])
    ->name('bookings.updateStatusManual');
    
// BF History screen
Route::get('/drivers/{driverId}/bf-history', [SetupController::class, 'bfHistory'])
    ->name('drivers.bf.history');

// Add / Subtract BF
Route::post('/drivers/{driverId}/bf-update', [SetupController::class, 'updateBF'])
    ->name('drivers.bf.update');
    
Route::put('/drivers/{driverId}/bf/update/{transactionId}', 
    [SetupController::class, 'updateBalanceHistory']
)->name('drivers.bf.edit');

Route::delete('/drivers/{driverId}/bf/delete/{transactionId}', 
    [SetupController::class, 'deleteBalanceHistory']
)->name('drivers.bf.delete');    


// Route::put('/drivers/{driverId}/bf/update/{transactionId}', 
//     [DriverController::class, 'updateBalanceHistory']
// )->name('drivers.bf.edit');

// Route::delete('/drivers/{driverId}/bf/delete/{transactionId}', 
//     [DriverController::class, 'deleteBalanceHistory']
// )->name('drivers.bf.delete');
  


Route::prefix('bookings')->group(function () {

    // Route::get('{booking}/track', [BookingController::class, 'track'])->name('bookings.track');
    // Route::get('{booking}/receipt', [BookingController::class, 'receipt'])->name('bookings.receipt');
    // Route::get('{booking}/duplicate', [BookingController::class, 'duplicate'])->name('bookings.duplicate');
    Route::get('{booking}/return', [BookingController::class, 'returnJob'])->name('bookings.return');
    Route::post('{booking}/send-email', [BookingController::class, 'sendConfirmationEmail'])->name('bookings.sendEmail');
    Route::post('{booking}/send-sms', [BookingController::class, 'sendConfirmationSMS'])->name('bookings.sendSMS');
    Route::post('{booking}/recall', [BookingController::class, 'recallJob'])->name('bookings.recall');
    Route::post('{booking}/hide', [BookingController::class, 'hideJob'])->name('bookings.hide');
    Route::get('{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::get('{booking}/{id}/view', [BookingController::class, 'view'])->name('bookings.view');
    Route::get('{booking}/details-json', [BookingController::class, 'getBookingDetailsJson'])->name('bookings.detailsJson');
    Route::get('details-json', [BookingController::class, 'getBookingDetailsJson'])->name('bookings.detailsJsonDirect');
    // Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('booking.update');
});

Route::get('/bookings/details-json', [BookingController::class, 'getBookingDetailsJson']);
Route::get('/admin/bookings/details-json', [BookingController::class, 'getBookingDetailsJson']);
Route::get('/admin/bookings/{booking}/details-json', [BookingController::class, 'getBookingDetailsJson']);

Route::post('/bookings/send-sms', [BookingController::class, 'sendSms'])
    ->name('bookings.sendSmsDashboard');
Route::post('/bookings/send-email', [BookingController::class, 'sendEmaildashboard']);

// Route::prefix('admin/dashboard')->name('admin.dashboard.')->group(function () {
//     Route::post('/bookings/send-email', [BookingController::class, 'sendEmaildashboard'])
//         ->name('bookings.sendEmail');
// });




Route::get('/payment/success', [BookingController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel', [BookingController::class, 'paymentCancel'])->name('payment.cancel');
// web.php
Route::post('/sms/login', [SmsController::class, 'login'])->name('sms.login');
Route::post('/sms/send', [SmsController::class, 'sendSMS'])->name('sms.send');


Route::post('/booking/{booking}/send-confirmation-email', [BookingController::class, 'sendConfirmationEmail']);
Route::post('/booking/{bookingId}/recurring', [BookingController::class, 'createRecurring']);
Route::post('/booking/{booking}/return-job', [BookingController::class, 'createReturnJob']);
Route::post('/booking/{booking}/send-receipt-email', [BookingController::class, 'sendReceiptEmail']);
// web.php
Route::post('/booking/{booking}/payment', [BookingController::class, 'createStripePayment']);




Route::post('/send-driver-commission-email', [ReportController::class, 'sendDriverCommissionEmail']);
Route::post('/pricing/fixed/save-percentages', [PricingController::class, 'savePercentages'])
    ->name('pricing.fixed.savePercentages');

Route::get('/pricing/fixed/getPercentages', [PricingController::class, 'getPercentages'])->name('pricing.fixed.getPercentages');
    

Route::post('/booking/send-email', [BookingController::class, 'sendEmail'])->name('booking.sendEmail');

Route::post('/bookings/dispatch-driver', [BookingController::class, 'dispatchDriver'])
    ->name('booking.dispatchDriver');
    
    
Route::get('/pricing/surcharge', [PricingController::class, 'get_surcharge'])->name('pricing.surcharge');
Route::post('/pricing/add-surcharge', [PricingController::class, 'addSurcharge'])->name('pricing.fixed.addSurcharge');
Route::post('/pricing/update-surcharge/{id}', [PricingController::class, 'updateSurcharge'])->name('pricing.fixed.updateSurcharge');
Route::delete('/pricing/delete-surcharge/{id}', [PricingController::class, 'deleteSurcharge'])->name('pricing.fixed.deleteSurcharge');

Route::get('/pricing/fixed-prices/search', [PricingController::class, 'search'])->name('fixedPrices.search');



Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::post('/locations/store', [LocationController::class, 'store'])->name('locations.store');
Route::delete('/locations/{type}/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
Route::put('/locations/{type}/{id}', [LocationController::class, 'update'])->name('locations.update');








Route::get('/signup', [OtpAuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [OtpAuthController::class, 'signup'])->name('signup.save');

Route::get('/', [OtpAuthController::class, 'showLogin'])->name('login');
Route::post('login_store', [OtpAuthController::class, 'login'])->name('login.save');

Route::get('/otp/verify', [OtpAuthController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/otp/verify', [OtpAuthController::class, 'verifyOtp'])->name('otp.verify');


Route::post('/logout', function () {
    session()->forget('admin_logged_in');
    return redirect()->route('login')->with('success', 'Logged out successfully.');
})->name('logout');

// Route::get('/', [OtpAuthController::class, 'showLogin'])->name('login');

Route::get('/dashboard', [BookingController::class, 'index'])->name('dashboard');
Route::get('/live-map', [BookingController::class, 'liveMap'])->name('live.map');


//Route::get('/', [BookingController::class, 'index'])->name('dashboard');


Route::get('booking/get-price', [BookingController::class, 'getPrice'])->name('booking.get-price');

Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{id}/edit', [BookingController::class, 'edit'])->name('booking.edit');
Route::post('/bookings/{id}', [BookingController::class, 'update'])->name('booking.update');
Route::delete('/booking/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');




Route::get('/bookings/track/{id}', [BookingController::class, 'trackDriver'])->name('bookings.track');
Route::get('/bookings/recall/{id}', [BookingController::class, 'recallJob'])->name('bookings.recall.get');
Route::get('/bookings/receipt/{id}', [BookingController::class, 'receipt'])->name('bookings.receipt');
Route::get('/bookings/duplicate/{id}', [BookingController::class, 'duplicateJob'])->name('bookings.duplicate');
Route::get('/bookings/route/{id}', [BookingController::class, 'routeMap'])->name('bookings.route');



// Route::get('/bookings/track/{id}', [BookingController::class, 'trackDriver'])->name('bookings.track');
// Route::get('/bookings/recall/{id}', [BookingController::class, 'recallJob'])->name('bookings.recall');
// Route::get('/bookings/receipt/{id}', [BookingController::class, 'receipt'])->name('bookings.receipt');
// Route::get('/bookings/duplicate/{id}', [BookingController::class, 'duplicateJob'])->name('bookings.duplicate');
Route::get('/bookings/search', [BookingController::class, 'search'])->name('bookings.search.main');

Route::get('/completed-jobs', [BookingController::class, 'completedJobs'])->name('completed.jobs');
Route::get('/completed_bookings/search', [BookingController::class, 'searchBookings'])->name('bookings.search');

Route::get('/previous-bookings', [BookingController::class, 'previousBookings'])
    ->name('previous.bookings');

Route::get('/previous_bookings/search', [BookingController::class, 'searchPreviousBookings'])
    ->name('previous.bookings.search');
    
    Route::get('/bookings/cancelled', [BookingController::class, 'cancelledBookings'])->name('bookings.cancelled');

Route::get('/cancelled_bookings/search', [BookingController::class, 'searchCancelledBookings'])->name('cancelled.bookings.search');




Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');


Route::get('/bookings/{booking}/messages', [MessageController::class, 'index'])->name('messages.all');


// Reports route
// Route::get('/reports', [ReportController::class, 'index'])->name('reports');
// Route::get('/reports/driver-commission', [ReportController::class, 'driverCommission'])->name('reports.driver_commission');
// Route::get('/reports/turnover', [ReportController::class, 'turnover'])->name('reports.turnover');
// Route::get('/reports/customer', [ReportController::class, 'customer'])->name('reports.customer');
Route::get('/reports', [ReportController::class, 'index'])->name('reports');
Route::get('/reports/driver-commission', [ReportController::class, 'driverCommission'])->name('reports.driver_commission');
Route::get('/reports/driver-commission/download', [ReportController::class, 'downloadDriverCommission'])
    ->name('reports.driver_commission.download');

Route::get('/get-driver-email/{id}', [ReportController::class, 'getDriverEmail']);
// Route::post('/send-driver-commission-email', [ReportController::class, 'sendCommissionEmail']);
    
    
Route::get('/reports/turnover', [ReportController::class, 'turnover'])->name('reports.turnover');
Route::get('/reports/turnover/download', [ReportController::class, 'downloadTurnover'])
    ->name('reports.turnover.download');
Route::post('/reports/turnover/send-email', [ReportController::class, 'sendTurnoverEmail'])
    ->name('reports.turnover.send-email');

Route::get('/reports/customer', [ReportController::class, 'customer'])->name('reports.customer');
Route::get('/reports/customer/download', [ReportController::class, 'downloadCustomerReport'])
    ->name('reports.customer.download');
    
Route::post('/send-customer-report', [ReportController::class, 'sendCustomerReport'])->name('send.customer.report');






Route::prefix('setup')->group(function () {
    Route::get('/', [SetupController::class, 'index'])->name('setup');

    // Store routes for each tab form
    Route::post('/staff/store', [SetupController::class, 'storeStaff'])->name('setup.staff.store');
    Route::match(['post', 'put', 'patch'], '/staff/{id}/access', [SetupController::class, 'updateStaffAccess'])
        ->name('setup.staff.access');
    Route::delete('/staff/{id}', [SetupController::class, 'destroyStaff'])
        ->name('setup.staff.destroy');
    Route::post('/driver/store', [SetupController::class, 'storeDriver'])->name('setup.driver.store');
    Route::post('/vehicle/store', [SetupController::class, 'storeVehicle'])->name('setup.vehicle.store');
    Route::post('/customer/store', [SetupController::class, 'storeCustomer'])->name('setup.customer.store');
    Route::put('/driver/{id}', [SetupController::class, 'update'])->name('setup.driver.update');
    Route::post('/super-admin/password', [SetupController::class, 'updateSuperAdminPassword'])
        ->name('setup.super-admin.password');
    Route::post('/delete-job-by-ref', [SetupController::class, 'deleteJobByRef'])
        ->name('setup.job.delete-by-ref');
    Route::post('/find-job-by-ref', [SetupController::class, 'findJobByRef'])
        ->name('setup.job.find-by-ref');
});

Route::prefix('messages')->group(function () {
    // Customer SMS
    Route::get('/customer/booking', [MessageController::class, 'customerBooking'])->name('messages.customer.booking');
    Route::post('customer/booking_sms/update', [MessageController::class, 'customerBookingSend'])->name('customer.booking_sms.update');
    
    Route::get('/customer/onroute', [MessageController::class, 'customerOnroute'])->name('messages.customer.onroute');
    Route::post('customer/onroute/update', [MessageController::class, 'customerOnrouteSend'])->name('customer.onroute.update');
    
    Route::get('/customer/arrival', [MessageController::class, 'customerArrival'])->name('messages.customer.arrival');
    Route::post('customer/arrival/update', [MessageController::class, 'customerArrivalSend'])->name('customer.arrival.update');
    
    
    Route::get('/customer/complete', [MessageController::class, 'customerComplete'])->name('messages.customer.complete');
    Route::post('customer/complete/update', [MessageController::class, 'customerCompleteSend'])->name('customer.job_complete.update');


    // Driver SMS
    Route::get('/driver/details', [MessageController::class, 'driverDetails'])->name('messages.driver.details');
    Route::post('driver/details/update', [MessageController::class, 'driverDetailsSend'])->name('messages.driver.details.update');

    Route::get('/driver/change', [MessageController::class, 'driverChange'])->name('messages.driver.change');
    Route::post('driver/change/update', [MessageController::class, 'driverChangeSend'])->name('messages.driver.change.update');

    Route::get('/driver/office', [MessageController::class, 'driverOffice'])->name('messages.driver.office');
    Route::post('driver/office/update', [MessageController::class, 'driverOfficeSend'])->name('messages.driver.office.update');
});


Route::post('/mileage/update', [PricingController::class, 'updateMileagePrice']);
Route::post('mileage/delete/{id}', [PricingController::class, 'destroy'])->name('mileage.delete');


Route::prefix('pricing')->group(function () {
    Route::get('/fixed', [PricingController::class, 'fixedIndex'])->name('pricing.fixed');
    Route::post('/fixed/import', [PricingController::class, 'importFixedPrices'])->name('pricing.fixed.import');
    Route::get('/fixed/import-progress/{importId}', [PricingController::class, 'fixedPriceImportProgress'])
        ->name('pricing.fixed.import-progress');
    Route::get('/fixed/export', [PricingController::class, 'exportFixedPrices'])->name('pricing.fixed.export');

    Route::get('/mileage', [PricingController::class, 'mileageIndex'])->name('pricing.mileage');
  
Route::post('/pricing/mileage/save', [PricingController::class, 'saveMileage'])
    ->name('pricing.mileage.save');
    
    Route::get('/pricing/mileage/new', [PricingController::class, 'getMileageData']);
    
    



// routes/web.php
Route::get('/admin/system-settings', [SystemSettingsController::class, 'index'])->name('system.settings');
Route::post('/admin/system-settings/update', [SystemSettingsController::class, 'update'])->name('system.settings.update');




Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
Route::post('/notifications/send', [NotificationController::class, 'store'])->name('notifications.send');
Route::get('/notifications/history', [NotificationController::class, 'history'])->name('notifications.history');


});


// routes/web.php
Route::resource('quick-links', QuickLinkController::class);

Route::get('quick-links',[QuickLinkController::class, 'index'])->name('quick.links');


Route::prefix('admin')->group(function () {
    // Route::get('/pages', [AdminPageController::class, 'index'])->name('admin.pages.index');
    
});

Route::get('{slug}', [PageController::class, 'show']);
Route::get('blogs/pages', [AdminPageController::class, 'index'])->name('pages.index');
Route::get('/pages/create', [AdminPageController::class, 'create'])->name('admin.pages.create');
Route::post('/pages/store', [AdminPageController::class, 'store'])->name('admin.pages.store');
Route::patch('/pages/{id}/toggle-status', [AdminPageController::class, 'toggleStatus'])->name('admin.pages.toggleStatus');
Route::get('/blogs/pages/delete/{id}', [AdminPageController::class, 'delete'])
    ->name('admin.pages.delete');

Route::get('/blogs/pages/edit/{id}', [AdminPageController::class, 'edit'])
    ->name('admin.pages.edit');

Route::post('/blogs/pages/update/{id}', [AdminPageController::class, 'update'])
    ->name('admin.pages.update');

// Route::get('admin/pages', function () {
//         return 'hi';
//     });

//Route::get('/pages', [AdminPageController::class, 'index'])->name('admin.pages.index');
// Setup Office route
// Route::get('/setup', [SetupController::class, 'index'])->name('setup');



// Route::get('/', function () {
//     return view('welcome');
// });




// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';

// routes/web.php






// Add this group early in the file
// Route::prefix('admin')->name('admin.')->group(function () {
//     // Move your booking routes here, e.g.:
//     Route::get('/', [BookingController::class, 'index'])->name('dashboard');  // /admin/
//     Route::get('booking/create', [BookingController::class, 'create'])->name('booking.create');
//     Route::post('booking', [BookingController::class, 'store'])->name('booking.store');
//     Route::get('booking/{id}/edit', [BookingController::class, 'edit'])->name('booking.edit');
//     Route::put('booking/{id}', [BookingController::class, 'update'])->name('booking.update');
//     Route::delete('booking/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');
    
//     // Your recurring route:
//     Route::post('booking/{booking}/recurring', [BookingController::class, 'createRecurring'])->name('booking.recurring');
    
//     // Other sub-routes (adjust paths as needed):
//     Route::post('bookings/{id}/update-status-manual', [BookingController::class, 'updateStatusManual'])->name('bookings.updateStatusManual');
//     Route::post('booking/{booking}/send-email', [BookingController::class, 'sendConfirmationEmail'])->name('booking.sendEmail');
//     Route::post('booking/{booking}/send-sms', [BookingController::class, 'sendConfirmationSMS'])->name('booking.sendSMS');
//     // ... add more as needed (e.g., return, recall, hide)
    
//     // Existing admin routes (move inside if not already):
//     Route::get('system-settings', [SystemSettingsController::class, 'index'])->name('system.settings');
//     Route::post('system-settings/update', [SystemSettingsController::class, 'update'])->name('system.settings.update');
//     // ... etc.
// });
