<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2340 100%); padding: 40px; text-align: center; border-radius: 16px 16px 0 0;">
                            @php $school = \App\Models\SchoolProfile::first(); @endphp
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">{{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}</h1>
                            <p style="color: rgba(255,255,255,0.8); margin: 10px 0 0; font-size: 14px;">Reset Password</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #333333; margin: 0 0 20px; font-size: 20px;">Halo, {{ $user->name }}!</h2>

                            <p style="color: #666666; font-size: 15px; line-height: 1.6; margin: 0 0 25px;">
                                Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah ini untuk membuat password baru:
                            </p>

                            <table role="presentation" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #1e3a5f 0%, #2c4f7c 100%); color: #ffffff; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #666666; font-size: 14px; line-height: 1.6; margin: 25px 0 0;">
                                Link ini akan kadaluarsa dalam <strong>60 menit</strong>. Jika Anda tidak meminta reset password, abaikan email ini.
                            </p>

                            <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

                            <p style="color: #999999; font-size: 12px; margin: 0;">
                                Jika tombol tidak berfungsi, salin dan tempel link berikut ke browser Anda:
                            </p>
                            <p style="color: #1e3a5f; font-size: 12px; word-break: break-all; margin: 10px 0 0;">
                                {{ $resetUrl }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 25px; text-align: center; border-radius: 0 0 16px 16px;">
                            <p style="color: #999999; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} {{ $school->name ?? 'SMP Negeri 6 Sudimoro' }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>