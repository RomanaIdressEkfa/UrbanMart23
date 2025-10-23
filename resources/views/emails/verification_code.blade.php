<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e8ebef">
    <tr>
        <td align="center" valign="top" class="container" style="padding:50px 10px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center">
                        <table width="650" border="0" cellspacing="0" cellpadding="0" class="mobile-shell" style="background:#ffffff;">
                            <tr>
                                <td style="padding:40px 30px; border-bottom:4px solid #000000;">
                                    <h2 style="margin:0; font-family:Arial, sans-serif;">Your Verification Code</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:40px 30px 50px 30px; font-family:Arial, sans-serif; color:#000;">
                                    <p style="font-size:16px; line-height:24px; margin:0 0 16px;">Use the following 6‑digit code to verify your email:</p>
                                    <div style="font-size:32px; font-weight:bold; letter-spacing:4px; margin:16px 0;">{{ $code }}</div>
                                    <p style="font-size:14px; line-height:22px; color:#555; margin:16px 0 0;">This code expires in 30 minutes. If you didn’t request this, you can safely ignore this email.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:20px 30px 40px 30px; font-family:Arial, sans-serif; color:#888; font-size:12px;">
                                    <div>&copy; {{ date('Y') }} {{ env('APP_NAME') }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

