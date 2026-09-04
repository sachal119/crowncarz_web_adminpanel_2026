<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Completed - Crown Airport Travels</title>
    
    <style>
        /* Base Styles for Cross-Client Compatibility */
        body { margin: 0; padding: 0; min-width: 100%; background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        a { text-decoration: none; color: #B87333; }
        img { display: block; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        td { padding: 0; }
        .ExternalClass { width: 100%; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        
        /* Responsive CSS */
        @media only screen and (max-width: 600px) {
            .main-content { width: 100% !important; }
            .content-padding { padding: 20px 20px !important; }
            .details-label { width: 40% !important; text-align: left !important; }
            .details-value { width: 60% !important; text-align: right !important; }
            .footer-text { font-size: 11px !important; }
        }

        /* Dark Mode Specific Styles (for clients that support it, like Apple Mail) */
        @media (prefers-color-scheme: dark) {
            body, .bg-dark { background-color: #202020 !important; color: #ffffff !important; }
            .container-bg { background-color: #2c2c2c !important; }
            .text-light { color: #ffffff !important; }
            .text-muted { color: #aaaaaa !important; }
            .details-bg { background-color: #202020 !important; }
            .details-border { border-top: 1px solid #444444 !important; }
            h1 { color: #ffffff !important; }
            h2 { color: #ffffff !important; }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; min-width: 100%; background-color: #f0f2f5;">
    <table width="100%" bgcolor="#f0f2f5" cellpadding="0" cellspacing="0" border="0" class="bg-dark" role="presentation">
        <tr>
            <td align="center" style="padding: 40px 0;">
                
                <table class="main-content container-bg" width="600" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" role="presentation" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); border-top: 5px solid #B87333;">
                    <tr>
                        <td class="content-padding" style="padding: 30px; color: #333333;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Airport Travels Logo" width="150" style="max-width: 150px; display: block;">
                                    </td>
                                </tr>
                            </table>

                            <h1 style="font-size: 24px; font-weight: 600; margin: 0 0 20px 0; color: #333333;" class="text-light">Booking Completed</h1>

                            <p style="margin: 0 0 10px 0; font-size: 16px; color: #333333;" class="text-light">Hello <b>{{ $booking['passenger_name'] ?? 'Fahad Khan' }}</b>,</p>

                            <p style="margin: 0 0 25px 0; font-size: 16px; color: #333333;" class="text-light">Your booking with Crown Carz has been <b>successfully completed</b>. Please review your travel details below:</p>

                            <h2 style="font-size: 18px; font-weight: bold; margin: 0 0 10px 0; color: #333333;" class="text-light">Your Trip Details</h2>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="details-bg" bgcolor="#f8f9fa" style="padding: 15px; border-radius: 4px;" role="presentation">
                                
                                <tr>
                                    <td style="padding: 8px 0; font-size: 15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td class="details-label" width="35%" valign="top" style="color: #666666; font-weight: normal; padding-right: 15px; padding-left: 8px;" class="text-muted"><strong>Pickup Address:</strong></td>
                                                <td class="details-value" width="65%" valign="top" align="right" style="color: #333333; text-align: right; line-height: 1.4; padding-right: 8px;" class="text-light">{{ $booking['pickup_address'] ?? 'Reading Train Station, Reading RG1 1LZ' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 8px 0; font-size: 15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td class="details-label" width="35%" valign="top" style="color: #666666; font-weight: normal; padding-right: 15px; padding-left: 8px;" class="text-muted"><strong>Dropoff Address:</strong></td>
                                                <td class="details-value" width="65%" valign="top" align="right" style="color: #333333; text-align: right; line-height: 1.4; padding-right: 8px;" class="text-light">{{ $booking['dropoff_address'] ?? 'Reading West Train Station, Oxford Road, Reading RG30 1AA' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding: 8px 0; font-size: 15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td class="details-label" width="35%" style="color: #666666; font-weight: normal; padding-right: 15px; padding-left: 8px;" class="text-muted"><strong>Date & Time:</strong></td>
                                                <td class="details-value" width="65%" align="right" style="color: #333333; text-align: right; padding-right: 8px;" class="text-light">
                                                     {{
                        isset($booking['pickup_time'])
                            ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d-m-Y H:i:s')
                            : '20-01-2026 05:30:00'
                    }}
                                                    </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding: 8px 0; font-size: 15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td class="details-label" width="35%" style="color: #666666; font-weight: normal; padding-right: 15px; padding-left: 8px;" class="text-muted"><strong>Vehicle Type:</strong></td>
                                                <td class="details-value" width="65%" align="right" style="color: #333333; text-align: right; padding-right: 8px;" class="text-light">{{ $booking['vehicle_make'] ?? 'Saloon' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 8px 0; font-size: 15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
                                            <tr>
                                                <td class="details-label" width="35%" style="color: #666666; font-weight: normal; padding-right: 15px; padding-left: 8px;" class="text-muted"><strong>Total Fare:</strong></td>
                                                <td class="details-value" width="65%" align="right" style="color: #333333; font-weight: bold; text-align: right; padding-right: 8px;" class="text-light">£{{ $booking['price'] ?? '10' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- DOWNLOAD BUTTON -->
                        <table width="100%" style="margin-top:30px;">
                            <tr>
                                <td align="center">

                                    <a href="{{$booking['receipt_link']}}"
                                       style="background:#28a745; padding:12px 30px; color:white; font-size:16px; border-radius:6px; display:inline-block;">
                                        Download Receipt
                                    </a>

                                </td>
                            </tr>
                        </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-top: 30px;">
    <tr>
        <td align="left" style="padding-top: 15px; border-top: 1px solid #eeeeee;" class="details-border">
            

            
            <p class="footer-text text-muted" style="margin: 0; font-size: 12px; color: #999999; text-align: center;">&copy; {{ now()->year }} Crown Carz. All rights reserved.</p>
            
            <p class="footer-text text-muted" style="margin: 5px 0 0 0; font-size: 12px; color: #999999; text-align: center;">Thanks,<br>Crown Carz</p>
            
        </td>
    </tr>
</table>

                        </td>
                    </tr>
                </table>
                </td>
        </tr>
    </table>
    </body>
</html>