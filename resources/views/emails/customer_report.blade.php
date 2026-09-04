<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Customer Booking Report - CrownCarz</title>

    <style>

        /* Base styles matching the image vibe */

        body {

            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;

            color: #333;

            margin: 0;

            padding: 0;

            background-color: #f4f4f4; /* Light background to simulate a page */

        }



        /* Container to simulate physical page */

        .page-container {

            width: 210mm; /* A4 width */

            min-height: 297mm; /* A4 height */

            margin: 20px auto;

            background: white;

            padding: 20px 40px;

            box-shadow: 0 0 10px rgba(0,0,0,0.1);

            position: relative;

            box-sizing: border-box;

        }



        /* The dark header strip */

        .header-dark {

            background-color: #3d3d3d; /* Dark grey like image */

            color: white;

            padding: 40px;

            margin: -20px -40px 30px -40px; /* Offset page padding */

            overflow: auto;

        }



        .header-left {

            float: left;

            width: 50%;

        }



        .header-right {

            float: right;

            width: 50%;

            text-align: right;

        }



        .header-right h1 {

            margin: 0;

            font-size: 36px;

            font-weight: 300;

            letter-spacing: 2px;

            text-transform: uppercase;

        }



        /* Styling for the text that replaces the "Logo" placeholder in image */

        .company-header-info {

            font-size: 13px;

            line-height: 1.5;

        }



        .company-header-info strong {

            font-size: 18px;

            display: block;

            margin-bottom: 5px;

        }



        /* Meta details section below dark header (two column) */

        .meta-details {

            overflow: auto;

            margin: 20px;

        }



        .billing-from {

            float: left;

            width: 40%;

            font-size: 13px;

            line-height: 1.6;

        }



        .billing-from-label {

            color: #888;

            text-transform: uppercase;

            font-size: 11px;

            font-weight: bold;

            margin-bottom: 5px;

        }



        .report-meta {

            float: right;

            width: 50%;

            text-align: right;

            font-size: 13px;

            margin: 20px;

        }



        .report-meta table {

            border-collapse: collapse;

            margin-left: auto; /* align table right */

        }



        .report-meta td {

            padding: 2px 0 2px 20px;

        }



        .meta-label {

            text-transform: uppercase;

            font-size: 11px;

            font-weight: bold;

            color: #333;

        }



        /* The Main Booking Table */

        .booking-table {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 30px;

            font-size: 13px;

        }



        .booking-table thead th {

            text-align: left;

            padding: 12px 8px;

            background-color: transparent; /* Changed from old gold */

            color: #333;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: 1px;

            /* The specific green top border from the image */

            border-top: 3px solid #b09300; 

            border-bottom: 1px solid #e0e0e0;

        }



        .booking-table tbody td {

            padding: 15px 8px;

            border-bottom: 1px solid #e0e0e0; /* Subtle row lines */

            vertical-align: top;

        }



        /* Highlight total row slightly */

        .booking-table tfoot td {

            padding: 15px 8px;

            font-weight: bold;

            border-top: 2px solid #333;

        }



        /* Utilities for alignment/sizing */

        .text-right { text-align: right !important; }

        .w-ref { width: 10%; }

        .w-date { width: 18%; }

        .w-money { width: 10%; }



        /* Comments/Sign-off area */

        .footer-area {

            overflow: auto;

            margin-top: 40px;

            font-size: 13px;

        }



        .comments-section {

            float: left;

            width: 50%;

        }



        .sign-off-section {

            float: right;

            width: 40%;

            text-align: right;

        }



        /* Ensure colors print correctly */

        @media print {

            body { background-color: white; }

            .page-container {

                box-shadow: none;

                margin: 0;

                padding: 10mm;

            }

            .header-dark {

                background-color: #3d3d3d !important;

                -webkit-print-color-adjust: exact;

                color: white !important;

            }

            .booking-table thead th {

                border-top: 3px solid #b09300 !important;

                -webkit-print-color-adjust: exact;

            }

        }

    </style>

</head>

<body>



    <div class="page-container">

        

        <!-- Dark Header matching image_0.png -->

        <div class="header-dark">

            <div class="header-left">

                <div class="company-header-info">

                    <!-- This replaces the 'Logo' area with company details as typically done in this style -->

                    <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Airport Travels Logo" width="150" style="max-width: 150px; display: block;">

                </div>

            </div>

            <div class="header-right">

                <!-- Using 'BOOKING REPORT' instead of 'INVOICE' to match the data content -->

                <h1>Customer Booking Report</h1>

            </div>

        </div>



        <!-- Meta info section: Company Details & Report Period -->

        <div class="meta-details">

            <div class="billing-from">

                <div class="billing-from-label">REPORT FROM</div>

                CrownCarz<br>

                Street address<br>

                City, Province Postal code

            </div>

            

            <div class="report-meta">

                <table>

                    <tr>

                        <td class="meta-label">REPORT DATE</td>

                        <!-- Dynamic date of generation -->

                        <td>{{ \Carbon\Carbon::now()->format('d-M-Y') }}</td>

                    </tr>

                    <tr>

                        <td class="meta-label">PERIOD FROM</td>

                        <td>{{ $from }}</td>

                    </tr>

                    <tr>

                        <td class="meta-label">PERIOD TO</td>

                        <td>{{ $to }}</td>

                    </tr>

                </table>

            </div>

        </div>



        <!-- Main Data Table -->

        <table class="booking-table">

            <thead>

                <tr>

                    <th class="w-ref">REF</th>

                    <th class="w-date">DATE/TIME</th>

                    <th>PICK UP</th>

                    <th>DROP OFF</th>

                    <th class="w-money text-right">FARE (£)</th>

                    <th class="w-money text-right">PARKING (£)</th>

                    <!-- Commented out COMMENTS column from original template as it often breaks layouts in clean invoice-style sheets -->

                     <!--<th>COMMENTS</th> -->

                </tr>

            </thead>

            <tbody>

                @php $totalFare = 0; $totalParking = 0; @endphp

                @foreach ($customers as $b)

                    @php 

                        $totalFare += $b->price ?? 0; 

                        $totalParking += $b->parking ?? 0;

                    @endphp

                    <tr>

                        <td>{{ $b->ref_no ?? 'N/A' }}</td>

                        <td>{{ \Carbon\Carbon::parse($b->pickup_time)->format('d-M-Y h:i A') }}</td>

                        <td>{{ $b->pickup_address ?? 'N/A' }}</td>

                        <td>{{ $b->dropoff_address ?? 'N/A' }}</td>

                        <td class="text-right">{{ number_format($b->price ?? 0, 2) }}</td>

                        <td class="text-right">{{ number_format($b->parking ?? 0, 2) }}</td>

                         <!--<td>{{ $b->comments ?? 'N/A' }}</td> -->

                    </tr>

                @endforeach

            </tbody>

            <tfoot>

                <tr>

                    <td colspan="4" class="text-right">SUBTOTAL</td>

                    <td class="text-right">£ {{ number_format($totalFare, 2) }}</td>

                    <td class="text-right">£ {{ number_format($totalParking, 2) }}</td>

                </tr>

                <tr style="font-size: 16px;">

                    <td colspan="4" class="text-right">TOTAL REPORT AMOUNT</td>

                    <td colspan="2" class="text-right" style="border-bottom: 2px solid #333;">£ {{ number_format($totalFare + $totalParking, 2) }}</td>

                </tr>

            </tfoot>

        </table>



        <!-- Bottom section with comments and sign-off -->

        <div class="footer-area">

            <div class="comments-section">

                <div class="billing-from-label">REPORT NOTES</div>

                This report displays all completed bookings within the specified period.

            </div>

            

            <div class="sign-off-section">

                <p>Kind regards,<br><br><br><strong>CrownCarz Team</strong></p>

            </div>

        </div>



    </div><!-- .page-container -->



</body>

</html>