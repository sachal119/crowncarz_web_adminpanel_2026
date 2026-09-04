<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Crown Carz Staff Account</title>
</head>
<body style="margin:0; padding:0; background:#f4f1ed; font-family:Arial,Helvetica,sans-serif; color:#252525;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f1ed; padding:32px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(65,42,23,.10);">
                <tr>
                    <td style="background:#101010; padding:26px 32px; text-align:center;">
                        <div style="color:#d69a55; font-size:24px; font-weight:700; letter-spacing:.5px;">CROWN CARZ</div>
                        <div style="color:#d7d7d7; font-size:13px; margin-top:6px;">Staff Portal Access</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <h1 style="font-size:22px; margin:0 0 12px; color:#8c4d18;">Welcome, {{ $staffName }}</h1>
                        <p style="font-size:15px; line-height:1.6; margin:0 0 24px; color:#555;">
                            A Crown Carz staff account has been created for you. Use the credentials below to sign in.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff8ef; border:1px solid #ecd5bb; border-radius:12px;">
                            <tr>
                                <td style="padding:13px 18px; color:#73543a; font-size:13px; width:120px; border-bottom:1px solid #ecd5bb;">Email</td>
                                <td style="padding:13px 18px; font-size:14px; font-weight:700; border-bottom:1px solid #ecd5bb;">{{ $staffEmail }}</td>
                            </tr>
                            <tr>
                                <td style="padding:13px 18px; color:#73543a; font-size:13px; border-bottom:1px solid #ecd5bb;">Password</td>
                                <td style="padding:13px 18px; font-size:14px; font-weight:700; font-family:monospace; border-bottom:1px solid #ecd5bb;">{{ $temporaryPassword }}</td>
                            </tr>
                            <tr>
                                <td style="padding:13px 18px; color:#73543a; font-size:13px;">Role</td>
                                <td style="padding:13px 18px; font-size:14px; font-weight:700;">{{ ucfirst($role) }}</td>
                            </tr>
                        </table>

                        <div style="text-align:center; margin:28px 0 20px;">
                            <a href="{{ $loginUrl }}" style="display:inline-block; background:#b5651d; color:#ffffff; text-decoration:none; font-size:15px; font-weight:700; padding:13px 28px; border-radius:9px;">Sign in to Crown Carz</a>
                        </div>

                        <p style="font-size:13px; line-height:1.6; margin:0; color:#777;">
                            Keep these details private. If you were not expecting this account, please contact the Crown Carz administrator.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background:#faf8f5; border-top:1px solid #eee5dc; padding:18px 32px; text-align:center; color:#8a8179; font-size:12px;">
                        This is an automated account email from Crown Carz.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
