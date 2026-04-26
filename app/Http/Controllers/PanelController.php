<?php

namespace App\Http\Controllers;

use App\Http\Requests\Panel\PanelOrderIndexRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PanelController extends Controller
{
    public function index(PanelOrderIndexRequest $request): Response
    {
        $search = $request->getSearch();

        $orders = Order::query()
            ->when($search, function ($query, string $searchTerm) {
                $likeSearch = '%'.$searchTerm.'%';
                $searchWords = preg_split('/\s+/', $searchTerm) ?: [];

                $query->where(function ($nestedQuery) use ($likeSearch): void {
                    $nestedQuery
                        ->where('order_number', 'like', $likeSearch)
                        ->orWhere('email', 'like', $likeSearch)
                        ->orWhere('first_name', 'like', $likeSearch)
                        ->orWhere('last_name', 'like', $likeSearch);
                });

                foreach ($searchWords as $searchWord) {
                    $wordLikeSearch = '%'.$searchWord.'%';

                    $query->where(function ($nestedQuery) use ($wordLikeSearch): void {
                        $nestedQuery
                            ->where('order_number', 'like', $wordLikeSearch)
                            ->orWhere('email', 'like', $wordLikeSearch)
                            ->orWhere('first_name', 'like', $wordLikeSearch)
                            ->orWhere('last_name', 'like', $wordLikeSearch);
                    });
                }
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Order $order): array => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_full_name' => $order->customer_full_name,
                'email' => $order->email,
                'status_label' => $order->statusLabel(),
                'payment_status_label' => $order->paymentStatusLabel(),
                'total_amount' => $order->total_amount,
                'is_completed' => $order->is_completed,
                'completed_at' => $order->completed_at?->toIso8601String(),
                'created_at' => $order->created_at?->toIso8601String(),
            ])
            ->values()
            ->all();

        return Inertia::render('Panel/Index', [
            'filters' => [
                'search' => $search ?? '',
            ],
            'orders' => $orders,
        ]);
    }

    public function complete(Order $order): RedirectResponse
    {
        $order->markAsCompleted();

        return redirect()
            ->route('panel.index')
            ->with('success', "Zamówienie {$order->order_number} zostało oznaczone jako zrealizowane.");
    }
}
