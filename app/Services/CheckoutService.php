<?php

namespace App\Services;

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Http\Requests\Checkout\StoreCheckoutRequest;
use App\Mail\OrderConfirmedMail;
use App\Mail\OrderVerificationCodeMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutService
{
    public const CREATE_STATUS_CREATED = 'created';

    public const CREATE_STATUS_CART_EMPTY = 'cart_empty';

    public const CREATE_STATUS_ITEMS_INVALID = 'items_invalid';

    private const CODE_EXPIRATION_MINUTES = 5;

    public function __construct(
        private CartService $cartService,
        private OrderPdfGenerator $orderPdfGenerator
    ) {}

    /**
     * @return array{cart: array<int|string, mixed>, guns: Collection<int, mixed>}
     */
    public function getCartWithGuns(): array
    {
        return $this->cartService->getCartWithGuns();
    }

    public function isClubMember(): bool
    {
        return $this->cartService->isClubMember();
    }

    public function getCheckoutOrderFromSession(Request $request): ?Order
    {
        $orderId = $request->session()->get('checkout_order_id');

        if (! $orderId) {
            return null;
        }

        return Order::query()
            ->with('items')
            ->find($orderId);
    }

    /**
     * @param  array{cart: array<int|string, mixed>, guns: Collection<int, mixed>}  $cartData
     */
    public function resolveCurrentOrder(Request $request, array $cartData): ?Order
    {
        $order = $this->getCheckoutOrderFromSession($request);

        if ($order && $order->verified_at !== null && ! empty($cartData['cart'])) {
            $request->session()->forget('checkout_order_id');

            return null;
        }

        return $order;
    }

    public function determineCheckoutStep(?Order $order): string
    {
        if (! $order) {
            return 'details';
        }

        return $order->verified_at === null ? 'verify' : 'complete';
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function paymentMethodsForFrontend(): array
    {
        return [
            [
                'value' => OrderPaymentMethod::PayOnSite->value,
                'label' => OrderPaymentMethod::PayOnSite->label(),
            ],
        ];
    }

    /**
     * @return array{status: string, order: Order|null}
     */
    public function createPendingOrder(StoreCheckoutRequest $request): array
    {
        $cartData = $this->cartService->getCartWithGuns();

        if (empty($cartData['cart'])) {
            return [
                'status' => self::CREATE_STATUS_CART_EMPTY,
                'order' => null,
            ];
        }

        $isClubMember = $this->cartService->isClubMember();
        $preparedItems = $this->prepareOrderItems($cartData['cart'], $cartData['guns'], $isClubMember);

        if (empty($preparedItems['items'])) {
            return [
                'status' => self::CREATE_STATUS_ITEMS_INVALID,
                'order' => null,
            ];
        }

        $verificationCode = $this->generateVerificationCode();

        $order = DB::transaction(function () use ($request, $preparedItems, $verificationCode, $isClubMember): Order {
            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'first_name' => $request->getFirstName(),
                'last_name' => $request->getLastName(),
                'street' => $request->getStreet(),
                'house_number' => $request->getHouseNumber(),
                'apartment_number' => $request->getApartmentNumber(),
                'postal_code' => $request->getPostalCode(),
                'city' => $request->getCity(),
                'email' => $request->getEmail(),
                'status' => OrderStatus::PendingVerification,
                'payment_method' => $request->getPaymentMethod(),
                'payment_status' => OrderPaymentStatus::Pending,
                'is_club_member' => $isClubMember,
                'total_shots' => $preparedItems['total_shots'],
                'total_amount' => $preparedItems['total_amount'],
                'verification_code_hash' => Hash::make($verificationCode),
                'verification_code_expires_at' => now()->addMinutes(self::CODE_EXPIRATION_MINUTES),
            ]);

            $order->items()->createMany($preparedItems['items']);

            return $order;
        });

        Mail::to($order->email)->send(new OrderVerificationCodeMail($order, $verificationCode));

        $request->session()->put('checkout_order_id', $order->id);

        return [
            'status' => self::CREATE_STATUS_CREATED,
            'order' => $order,
        ];
    }

    public function resendVerificationCode(Order $order): void
    {
        $newVerificationCode = $this->issueNewVerificationCode($order);

        Mail::to($order->email)->send(new OrderVerificationCodeMail($order, $newVerificationCode));
    }

    public function validateVerificationCode(Order $order, string $code): ?string
    {
        if ($order->verification_code_expires_at?->isPast()) {
            return 'Kod weryfikacyjny wygasł. Kliknij "Wyślij nowy kod".';
        }

        if (! Hash::check($code, (string) $order->verification_code_hash)) {
            return 'Podany kod weryfikacyjny jest nieprawidłowy.';
        }

        return null;
    }

    public function confirmOrder(Order $order): void
    {
        $order->forceFill([
            'status' => OrderStatus::Confirmed,
            'verified_at' => now(),
            'verification_code_hash' => null,
            'verification_code_expires_at' => null,
            'download_token' => Str::random(64),
        ])->save();

        $order->load('items');
        $pdfContent = $this->orderPdfGenerator->generate($order);

        Mail::to($order->email)->send(new OrderConfirmedMail($order, $pdfContent));

        $this->cartService->clearCart();
    }

    public function canDownloadPdf(Order $order, string $token): bool
    {
        if ($order->verified_at === null || $token === '') {
            return false;
        }

        return hash_equals((string) $order->download_token, $token);
    }

    public function generateOrderPdf(Order $order): string
    {
        $order->load('items');

        return $this->orderPdfGenerator->generate($order);
    }

    public function generateOrderPdfFileName(Order $order): string
    {
        return 'zamowienie-'.$order->order_number.'.pdf';
    }

    /**
     * @return array<string, mixed>
     */
    public function mapOrderForFrontend(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'first_name' => $order->first_name,
            'last_name' => $order->last_name,
            'street' => $order->street,
            'house_number' => $order->house_number,
            'apartment_number' => $order->apartment_number,
            'postal_code' => $order->postal_code,
            'city' => $order->city,
            'full_address' => $order->full_address,
            'email' => $order->email,
            'status' => $order->status->value,
            'status_label' => $order->statusLabel(),
            'payment_method' => $order->payment_method->value,
            'payment_method_label' => $order->paymentMethodLabel(),
            'payment_status' => $order->payment_status->value,
            'payment_status_label' => $order->paymentStatusLabel(),
            'verification_code_valid_for_minutes' => self::CODE_EXPIRATION_MINUTES,
            'total_shots' => $order->total_shots,
            'total_amount' => $order->total_amount,
            'verification_code_expires_at' => $order->verification_code_expires_at?->toIso8601String(),
            'download_url' => $order->verified_at
                ? route('orders.download-pdf', [
                    'order' => $order,
                    'token' => $order->download_token,
                ])
                : null,
            'items' => $order->items
                ->map(fn ($item): array => [
                    'gun_name' => $item->gun_name,
                    'ammunition_name' => $item->ammunition_name,
                    'quantity' => $item->quantity,
                    'price_per_shot' => $item->price_per_shot,
                    'line_total' => $item->line_total,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<int|string, mixed>  $cart
     * @return array{items: array<int, array<string, mixed>>, total_shots: int, total_amount: float}
     */
    private function prepareOrderItems(array $cart, Collection $guns, bool $isClubMember): array
    {
        $items = [];
        $totalShots = 0;
        $totalAmount = 0.0;

        foreach ($cart as $gunId => $cartItem) {
            $gun = $guns->firstWhere('id', (int) $gunId);

            if (! $gun || ! isset($cartItem['ammunitions']) || ! is_array($cartItem['ammunitions'])) {
                continue;
            }

            foreach ($cartItem['ammunitions'] as $ammunitionId => $quantity) {
                $ammunition = $gun->caliber?->ammunitions->firstWhere('id', (int) $ammunitionId);

                if (! $ammunition) {
                    continue;
                }

                $shotsQuantity = max((int) $quantity, 0);

                if ($shotsQuantity === 0) {
                    continue;
                }

                $pricePerShot = (float) ($isClubMember ? $ammunition->club_price : $ammunition->standard_price);
                $lineTotal = round($pricePerShot * $shotsQuantity, 2);

                $items[] = [
                    'gun_id' => $gun->id,
                    'ammunition_id' => $ammunition->id,
                    'gun_name' => $gun->name,
                    'ammunition_name' => $ammunition->name,
                    'quantity' => $shotsQuantity,
                    'price_per_shot' => $pricePerShot,
                    'line_total' => $lineTotal,
                ];

                $totalShots += $shotsQuantity;
                $totalAmount += $lineTotal;
            }
        }

        return [
            'items' => $items,
            'total_shots' => $totalShots,
            'total_amount' => round($totalAmount, 2),
        ];
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'SO-'.now()->format('YmdHis').'-'.strtoupper(Str::random(5));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    private function generateVerificationCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function issueNewVerificationCode(Order $order): string
    {
        $oldCodeHash = (string) $order->verification_code_hash;

        do {
            $newCode = $this->generateVerificationCode();
        } while ($oldCodeHash !== '' && Hash::check($newCode, $oldCodeHash));

        $order->forceFill([
            'verification_code_hash' => Hash::make($newCode),
            'verification_code_expires_at' => now()->addMinutes(self::CODE_EXPIRATION_MINUTES),
        ])->save();

        return $newCode;
    }
}
