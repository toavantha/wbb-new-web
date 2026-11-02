<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your WBB Portal Email</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Arial,Helvetica,sans-serif; color:#333333;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f4f6f9; padding:35px 12px;">
        <tr>
            <td align="center">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="width:100%; max-width:600px; background-color:#ffffff; border-radius:10px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding:30px 25px 20px;">
                            <img src="{{ $logoUrl }}" alt="WBB Logo"
                                style="display:block; max-width:190px; width:100%; height:auto; border:0;">
                        </td>
                    </tr>

                    <!-- Heading -->
                    <tr>
                        <td align="center" style="padding:15px 30px 10px;">
                            <h1 style="margin:0; color:#183b63; font-size:25px; font-weight:700;">
                                Welcome to WBB CMS!
                            </h1>
                        </td>
                    </tr>

                    <!-- Message -->
                    <tr>
                        <td style="padding:15px 35px 10px; font-size:15px; line-height:1.7; color:#555555;">

                            <p style="margin:0 0 15px;">
                                Hello {{ $user->name }},
                            </p>

                            <p style="margin:0 0 15px;">
                                Thank you for registering with the WBB CMS.
                                We're pleased to have you join us.
                            </p>

                            <p style="margin:0 0 15px;">
                                To complete your registration, please verify
                                your email address by clicking the button below.
                            </p>

                        </td>
                    </tr>

                    <!-- Verification Button -->
                    <tr>
                        <td align="center" style="padding:20px 25px 25px;">
                            <a href="{{ $verificationUrl }}" style="display:inline-block; background-color:#183b63;
                                      color:#ffffff; text-decoration:none;
                                      padding:14px 32px; border-radius:6px;
                                      font-size:15px; font-weight:bold;">
                                Verify My Email Address
                            </a>
                        </td>
                    </tr>

                    <!-- Approval information -->
                    <tr>
                        <td style="padding:0 35px 25px; font-size:14px; line-height:1.7; color:#666666;">

                            <p style="margin:0 0 12px;">
                                <strong>What's next?</strong>
                            </p>

                            <p style="margin:0 0 15px;">
                                After verifying your email, your registration
                                will be forwarded to the WBB administration team
                                for review and approval.
                            </p>

                            <p style="margin:0;">
                                If you did not create this account, you can
                                safely ignore this email.
                            </p>

                        </td>
                    </tr>

                    <!-- Fallback link -->
                    <tr>
                        <td style="padding:20px 35px; background-color:#f8fafc;
                                   font-size:12px; line-height:1.6; color:#777777;">

                            <p style="margin:0 0 10px;">
                                If the button doesn't work, copy and paste
                                the following link into your browser:
                            </p>

                            <a href="{{ $verificationUrl }}" style="color:#183b63; word-break:break-all;">
                                {{ $verificationUrl }}
                            </a>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:22px 25px; background-color:#183b63;
                                   color:#ffffff; font-size:12px; line-height:1.6;">

                            <p style="margin:0 0 5px;">
                                <strong>WBB Official Web CMS</strong>
                            </p>

                            <p style="margin:0;">
                                © {{ date('Y') }} WBB. All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>