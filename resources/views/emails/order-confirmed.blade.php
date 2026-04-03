<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie zamówienia</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; color: #111827;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f3f4f6; padding: 24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 720px; background-color: #ffffff; border-radius: 14px; overflow: hidden; border: 1px solid #e5e7eb;">
                <tr>
                    <td style="padding: 20px 24px; background: linear-gradient(135deg, #111827, #1f2937); color: #ffffff;">
                        <p style="margin: 0 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af;">{{ config('app.name') }}</p>
                        <h1 style="margin: 0; font-size: 22px; line-height: 1.3;">Potwierdzenie zamówienia</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 24px;">
                        <p style="margin: 0 0 12px; font-size: 15px; line-height: 1.6;">Dzień dobry,</p>
                        <p style="margin: 0 0 18px; font-size: 15px; line-height: 1.6; color: #374151;">
                            Twoje zamówienie <strong>{{ $order->order_number }}</strong> zostało potwierdzone.
                            W załączniku znajdziesz plik PDF z podsumowaniem.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; margin-bottom: 18px;">
                            <tr>
                                <td style="padding: 10px 12px; font-size: 13px; color: #6b7280; width: 38%; border-bottom: 1px solid #e5e7eb;">Numer zamówienia</td>
                                <td style="padding: 10px 12px; font-size: 13px; font-weight: 600; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 12px; font-size: 13px; color: #6b7280; width: 38%; border-bottom: 1px solid #e5e7eb;">Klient</td>
                                <td style="padding: 10px 12px; font-size: 13px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $order->customer_full_name }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 12px; font-size: 13px; color: #6b7280; width: 38%; border-bottom: 1px solid #e5e7eb;">Adres</td>
                                <td style="padding: 10px 12px; font-size: 13px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $order->full_address }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 12px; font-size: 13px; color: #6b7280; width: 38%; border-bottom: 1px solid #e5e7eb;">Forma płatności</td>
                                <td style="padding: 10px 12px; font-size: 13px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $order->paymentMethodLabel() }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 12px; font-size: 13px; color: #6b7280; width: 38%;">Status płatności</td>
                                <td style="padding: 10px 12px; font-size: 13px; color: #111827;">{{ $order->paymentStatusLabel() }}</td>
                            </tr>
                        </table>

                        <h2 style="margin: 0 0 10px; font-size: 16px; color: #111827;">Pozycje zamówienia</h2>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; margin-bottom: 16px;">
                            <thead>
                            <tr>
                                <th align="left" style="padding: 10px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb;">Pozycja</th>
                                <th align="right" style="padding: 10px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb;">Ilość</th>
                                <th align="right" style="padding: 10px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb;">Wartość</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td style="padding: 10px; font-size: 13px; color: #111827; border-bottom: 1px solid #f3f4f6;">
                                        {{ $item->gun_name }} / {{ $item->ammunition_name }}
                                        @if(!empty($item->gun_package_name))
                                            <br>
                                            <span style="font-size: 11px; color: #6b7280;">
                                                Pakiet: {{ $item->gun_package_name }}
                                                @if(!empty($item->gun_package_guns_summary))
                                                    ({{ $item->gun_package_guns_summary }})
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td align="right" style="padding: 10px; font-size: 13px; color: #111827; border-bottom: 1px solid #f3f4f6;">
                                        {{ $item->quantity }}
                                    </td>
                                    <td align="right" style="padding: 10px; font-size: 13px; color: #111827; border-bottom: 1px solid #f3f4f6;">
                                        {{ number_format((float) $item->line_total, 2, ',', ' ') }} zł
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; margin-bottom: 18px;">
                            <tr>
                                <td style="padding: 6px 0; font-size: 14px; color: #374151;">Łącznie strzałów</td>
                                <td align="right" style="padding: 6px 0; font-size: 14px; color: #111827; font-weight: 600;">{{ $order->total_shots }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; font-size: 16px; color: #111827; font-weight: 700;">Do zapłaty</td>
                                <td align="right" style="padding: 6px 0; font-size: 16px; color: #111827; font-weight: 700;">{{ number_format((float) $order->total_amount, 2, ',', ' ') }} zł</td>
                            </tr>
                        </table>

                        @if(!empty($order->download_token))
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin-bottom: 10px;">
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.download-pdf', ['order' => $order, 'token' => $order->download_token]) }}"
                                           style="display: inline-block; background-color: #111827; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 12px 18px; border-radius: 8px;">
                                            Pobierz PDF zamówienia
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        @endif
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
