<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Nunito',Arial,sans-serif;">
    <div style="max-width:480px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <div style="background:linear-gradient(135deg,#047857,#065f46);padding:32px;text-align:center;">
            <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group" style="width:56px;height:56px;margin:0 auto 16px;border-radius:12px;background:rgba(255,255,255,0.1);padding:4px;">
            <h2 style="color:#fff;font-size:22px;margin:0 0 4px;">Password Reset</h2>
            <p style="color:#a7f3d0;font-size:13px;margin:0;">ASYX Group ERP System</p>
        </div>
        <div style="padding:32px;">
            <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 24px;">
                You requested a password reset. Use the activation code below to verify your identity. This code expires in <strong>15 minutes</strong>.
            </p>
            <div style="text-align:center;background:#f0fdf4;border:2px dashed #047857;border-radius:12px;padding:24px;margin:0 0 24px;">
                <p style="color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:1px;margin:0 0 8px;">Your Activation Code</p>
                <p style="font-size:36px;font-weight:900;color:#047857;letter-spacing:8px;margin:0;font-family:'Courier New',monospace;">{{ $code }}</p>
            </div>
            <p style="color:#6b7280;font-size:13px;line-height:1.5;margin:0 0 16px;">
                If you did not request a password reset, please ignore this email. Your account security has not been compromised.
            </p>
            <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">
            <p style="color:#9ca3af;font-size:11px;text-align:center;margin:0;">
                &copy; {{ date('Y') }} ASYX Group. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
