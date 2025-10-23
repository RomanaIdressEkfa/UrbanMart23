<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Order Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;">
        <h2 style="color: #333;">Pre-Order Status Update</h2>
        
        <p>Dear {{ $order->shipping_name }},</p>

        {{-- "content" থেকে "content_message"-এ পরিবর্তন করা হয়েছে --}}
        <p>{!! $content_message !!}</p>
        
        <p><strong>Order Code:</strong> {{ $order->order_code }}</p>
        
        <p>Thank you for your patience.</p>

        <p>
            Best Regards,<br>
            {{ get_setting('site_name') }}
        </p>
    </div>

</body>
</html>