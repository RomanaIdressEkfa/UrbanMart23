@extends('emails.master')
@section('content')

<div style="padding: 20px; font-family: Arial, sans-serif;">
    <h2 style="color: #333; margin-bottom: 20px;">Great News! Your Pre-ordered Product Has Arrived</h2>
    
    <p style="font-size: 16px; color: #555; margin-bottom: 15px;">
        Dear {{ $customer->name }},
    </p>
    
    <p style="font-size: 16px; color: #555; margin-bottom: 15px;">
        We're excited to inform you that your pre-ordered product is now available and ready for delivery!
    </p>
    
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="color: #333; margin-bottom: 15px;">Order Details:</h3>
        <p><strong>Order Code:</strong> {{ $preorder->order_code }}</p>
        <p><strong>Product:</strong> {{ $product->name }}</p>
        <p><strong>Quantity:</strong> {{ $preorder->quantity }}</p>
        <p><strong>Remaining Payment:</strong> {{ single_price($preorder->grand_total - $preorder->prepayment) }}</p>
    </div>
    
    <div style="background-color: #e8f4fd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff;">
        <h4 style="color: #007bff; margin-bottom: 10px;">Next Steps:</h4>
        <ol style="color: #555; line-height: 1.6;">
            <li>Complete the remaining payment to secure your order</li>
            <li>Once payment is confirmed, your order will be processed for delivery</li>
            <li>You'll receive tracking information once shipped</li>
        </ol>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('preorder.complete_payment', $preorder->id) }}" 
           style="background-color: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Complete Payment Now
        </a>
    </div>
    
    <p style="font-size: 14px; color: #777; margin-top: 30px;">
        Thank you for choosing us! If you have any questions, please don't hesitate to contact our customer support.
    </p>
    
    <p style="font-size: 14px; color: #777;">
        Best regards,<br>
        {{ get_setting('site_name') }} Team
    </p>
</div>

@endsection

