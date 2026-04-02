<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPdfGenerator
{
    public function generate(Order $order): string
    {
        $order->loadMissing('items');

        return Pdf::loadView('pdf.order-voucher', [
            'order' => $order,
        ])
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
            ])
            ->setPaper('a4')
            ->output();
    }
}
