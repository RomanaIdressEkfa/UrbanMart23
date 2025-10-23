{{-- <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification - Urban Mart</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3498db; color: white; text-align: center; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .code { background: #fff; border: 2px dashed #3498db; padding: 20px; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 3px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Urban Mart</h1>
            <h2>Email Verification</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Welcome to Urban Mart! Please use the verification code below:</p>
            
            <div class="code">{{ $code }}</div>
            
            <p>This verification code will expire in 10 minutes.</p>
            <p>Best regards,<br>Urban Mart Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Urban Mart. All rights reserved.</p>
        </div>
    </div>
</body>
</html> --}}

<!DOCTYPE html>
<html>
<head>
    
    <meta charset="UTF-8">
    <title>Email Verification - Urban Mart</title>
      <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3498db; color: white; text-align: center; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .code { background: #fff; border: 2px dashed #3498db; padding: 20px; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 3px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
     <div class="header">
            <h1>Urban Mart</h1>
            <h2>Email Verification</h2>
        </div>
    <p>Thanks for registering. Please click the button below to verify your email:</p>
    <a href="{{ $url }}" style="display:inline-block;padding:10px 20px;background:#4CAF50;color:white;text-decoration:none;border-radius:5px;">Verify Email</a>
    <p>If you didn’t create an account, no further action is required.</p>
</body>
</html>


