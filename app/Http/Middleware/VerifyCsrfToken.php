<?php

    namespace App\Http\Middleware;

    use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

    class VerifyCsrfToken extends Middleware
    {
        /**
         * The URIs that should be excluded from CSRF verification.
         *
         * @var array<int, string>
         */
        // protected $except = [
        //     // Add your API route here to exclude it from CSRF checking
        //     'bookings/*/recurring', 
        // ];
        
        protected $except = [
    'admin/booking/*/recurring',
    'admin/sms/send'
];

    }