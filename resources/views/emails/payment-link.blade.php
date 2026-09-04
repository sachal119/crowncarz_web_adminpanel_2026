<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;">

    <h2 style="color:#8d4ad6;">Payment Request for Booking {{ $bookingRef }}</h2>

    <p>Dear Customer,</p>

    <p>Please complete your payment of 
        <strong style="color:green;">{{ $amount }}</strong>
        for your booking (ID: {{ $bookingId }}).
    </p>

    <p style="margin-top:25px;">
        <a href="{{ $paymentUrl }}" 
           style="display:inline-block;
                  background:#28a745;
                  color:white;
                  padding:12px 25px;
                  text-decoration:none;
                  border-radius:6px;
                  font-size:16px;
                  font-weight:bold;">
            PAY NOW
        </a>
    </p>
    <p>
    Or open this link: <br>
    <a href="{{ $paymentUrl }}">{{ $paymentUrl }}</a>
</p>


    <p style="margin-top:30px;">Thank you,<br>Crown Carz</p>

</body>
</html>
