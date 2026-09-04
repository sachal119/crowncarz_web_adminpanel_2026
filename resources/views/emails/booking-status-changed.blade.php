<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status Updated - Crown Airport Travels</title>

    <style>
        body { margin:0; padding:0; min-width:100%; background:#f0f2f5; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        a { text-decoration:none; color:#B87333; }
        img { display:block; border:0; height:auto; }
        table { border-collapse:collapse; }
        td { padding:0; }

        @media only screen and (max-width:600px){
            .main-content { width:100% !important; }
            .content-padding { padding:20px !important; }
            .details-label { width:40% !important; }
            .details-value { width:60% !important; text-align:right !important; }
            .footer-text { font-size:11px !important; }
        }

        @media (prefers-color-scheme: dark){
            body, .bg-dark { background:#202020 !important; color:#fff !important; }
            .container-bg { background:#2c2c2c !important; }
            .text-light { color:#ffffff !important; }
            .text-muted { color:#aaaaaa !important; }
            .details-bg { background:#202020 !important; }
            .details-border { border-top:1px solid #444 !important; }
            h1, h2 { color:#ffffff !important; }
        }
    </style>
</head>

<body>
<table width="100%" bgcolor="#f0f2f5" class="bg-dark" role="presentation">
<tr>
<td align="center" style="padding:40px 0;">

<table class="main-content container-bg" width="600" bgcolor="#ffffff" role="presentation"
       style="border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.1); border-top:5px solid #B87333;">

<tr>
<td class="content-padding" style="padding:30px; color:#333;">

<!-- LOGO -->
<table width="100%">
<tr>
<td align="center" style="padding-bottom:20px;">
    <img src="https://crowncarz.com/admin/public/images/logo.png"
         width="150" alt="Crown Airport Travels">
</td>
</tr>
</table>

<!-- TITLE -->
<h1 class="text-light" style="font-size:24px; margin-bottom:20px;">
    Booking Status Updated
</h1>

<p class="text-light" style="font-size:16px;">
    Hello <b>{{ $booking['passenger_name'] ?? 'Customer' }}</b>,
</p>

<p class="text-light" style="font-size:16px; margin-bottom:25px;">
    Your booking status has been updated. Please see the details below:
</p>

<h2 class="text-light" style="font-size:18px; margin-bottom:10px;">
    Status Update
</h2>

<!-- STATUS DETAILS -->
<table width="100%" class="details-bg" bgcolor="#f8f9fa"
       style="padding:15px; border-radius:4px;" role="presentation">

<tr>
<td style="padding:8px 0;">
<table width="100%">
<tr>
<td class="details-label text-muted" width="35%" style="padding-left:8px;">
    <strong>Booking ID:</strong>
</td>
<td class="details-value text-light" width="65%" align="right" style="padding-right:8px;">
    {{ $booking['ref_no'] }}
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td style="padding:8px 0;">
<table width="100%">
<tr>
<td class="details-label text-muted" width="35%" style="padding-left:8px;">
    <strong>Previous Status:</strong>
</td>
<td class="details-value" width="65%" align="right" style="padding-right:8px; color:#c0392b;">
    {{ ucfirst(str_replace('_',' ', $oldStatus)) }}
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td style="padding:8px 0;">
<table width="100%">
<tr>
<td class="details-label text-muted" width="35%" style="padding-left:8px;">
    <strong>Current Status:</strong>
</td>
<td class="details-value" width="65%" align="right" style="padding-right:8px; color:#27ae60; font-weight:bold;">
    {{ ucfirst(str_replace('_',' ', $newStatus)) }}
</td>
</tr>
</table>
</td>
</tr>

</table>



<!-- FOOTER -->
<table width="100%" style="margin-top:30px;">
<tr>
<td align="center" class="details-border" style="padding-top:15px; border-top:1px solid #eee;">
    <p class="footer-text text-muted" style="font-size:12px;">
        &copy; {{ now()->year }} Crown Carz. All rights reserved.
    </p>
    <p class="footer-text text-muted" style="font-size:12px;">
        Thanks,<br><strong>Crown Carz</strong>
    </p>
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
