<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Voucher zamówienia {{ $order->order_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #111827;
            background: #f3f4f6;
            font-size: 12px;
            line-height: 1.45;
        }

        .voucher {
            background: #ffffff;
            border: 2px solid #111827;
            border-radius: 14px;
            padding: 24px;
            position: relative;
        }

        .badge {
            position: absolute;
            top: 16px;
            right: 16px;
            border: 1px solid #111827;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            background: #f9fafb;
        }

        .header {
            border-bottom: 2px dashed #9ca3af;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .title {
            margin: 0;
            font-size: 24px;
            line-height: 1.1;
            letter-spacing: 0.03em;
        }

        .subtitle {
            margin: 6px 0 0;
            color: #4b5563;
            font-size: 12px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .meta td {
            width: 50%;
            vertical-align: top;
            padding: 4px 0;
        }

        .label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }

        .value {
            font-size: 12px;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-weight: 700;
            color: #111827;
        }

        .section-title {
            margin: 18px 0 8px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #374151;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
        }

        .items th,
        .items td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .items th {
            background: #f9fafb;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            text-align: left;
        }

        .items td:nth-child(2),
        .items th:nth-child(2),
        .items td:nth-child(3),
        .items th:nth-child(3) {
            text-align: right;
            width: 110px;
        }

        .summary {
            margin-top: 12px;
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 4px 0;
        }

        .summary td:last-child {
            text-align: right;
            font-weight: 700;
        }

        .total-row td {
            font-size: 16px;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
        }

        .footer {
            margin-top: 18px;
            border-top: 2px dashed #9ca3af;
            padding-top: 10px;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="voucher">
    <div class="badge">Voucher</div>

    <div class="header">
        <h1 class="title">POTWIERDZENIE ZAMÓWIENIA</h1>
        <p class="subtitle">{{ config('app.name') }} / Strzelnica Sagittarius</p>
    </div>

    <table class="meta" role="presentation">
        <tr>
            <td>
                <div class="label">Numer zamówienia</div>
                <div class="value">{{ $order->order_number }}</div>
            </td>
            <td>
                <div class="label">Data wystawienia</div>
                <div class="value">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Klient</div>
                <div class="value">{{ $order->customer_full_name }}</div>
            </td>
            <td>
                <div class="label">E-mail</div>
                <div class="value">{{ $order->email }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Adres</div>
                <div class="value">{{ $order->full_address }}</div>
            </td>
            <td>
                <div class="label">Płatność</div>
                <div class="value">{{ $order->paymentMethodLabel() }} / {{ $order->paymentStatusLabel() }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Pozycje zamówienia</div>
    <table class="items" role="presentation">
        <thead>
        <tr>
            <th>Pozycja</th>
            <th>Ilość</th>
            <th>Wartość</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->gun_name }} / {{ $item->ammunition_name }}
                    @if(!empty($item->gun_package_name))
                        <br>
                        <span style="font-size: 10px; color: #4b5563;">
                            Pakiet: {{ $item->gun_package_name }}
                            @if(!empty($item->gun_package_guns_summary))
                                ({{ $item->gun_package_guns_summary }})
                            @endif
                        </span>
                    @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format((float) $item->line_total, 2, ',', ' ') }} zł</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="summary" role="presentation">
        <tr>
            <td>Łączna liczba strzałów</td>
            <td>{{ $order->total_shots }}</td>
        </tr>
        <tr class="total-row">
            <td>Razem do zapłaty</td>
            <td>{{ number_format((float) $order->total_amount, 2, ',', ' ') }} zł</td>
        </tr>
    </table>

    <div class="footer">
        Voucher wygenerowano automatycznie. Prosimy o okazanie numeru zamówienia przy realizacji na miejscu.
    </div>
</div>
</body>
</html>
