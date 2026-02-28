<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kod weryfikacyjny zamówienia</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; color: #111827;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f3f4f6; padding: 24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; background-color: #ffffff; border-radius: 14px; overflow: hidden; border: 1px solid #e5e7eb;">
                <tr>
                    <td style="padding: 20px 24px; background: linear-gradient(135deg, #111827, #1f2937); color: #ffffff;">
                        <p style="margin: 0 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af;">{{ config('app.name') }}</p>
                        <h1 style="margin: 0; font-size: 22px; line-height: 1.3;">Kod weryfikacyjny zamówienia</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 24px;">
                        <p style="margin: 0 0 12px; font-size: 15px; line-height: 1.6;">Dzień dobry,</p>
                        <p style="margin: 0 0 12px; font-size: 15px; line-height: 1.6;">
                            Aby potwierdzić zamówienie <strong>{{ $order->order_number }}</strong>, wpisz poniższy kod w formularzu na stronie.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 18px 0;">
                            <tr>
                                <td align="center" style="padding: 18px; border-radius: 12px; background-color: #f9fafb; border: 1px dashed #d1d5db;">
                                    <div style="font-size: 34px; letter-spacing: 0.28em; font-weight: 700; color: #111827;">{{ $verificationCode }}</div>
                                </td>
                            </tr>
                        </table>

                        <p style="margin: 0 0 6px; font-size: 14px; line-height: 1.6; color: #374151;">
                            Kod jest ważny przez <strong>5 minut</strong>.
                        </p>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6b7280;">
                            Jeśli to nie Ty składałeś zamówienie, zignoruj tę wiadomość.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 16px 24px; background-color: #f9fafb; border-top: 1px solid #e5e7eb;">
                        <p style="margin: 0; font-size: 12px; color: #6b7280;">To jest wiadomość automatyczna, prosimy na nią nie odpowiadać.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
