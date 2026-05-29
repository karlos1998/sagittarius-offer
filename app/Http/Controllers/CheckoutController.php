<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\ResendCheckoutCodeRequest;
use App\Http\Requests\Checkout\StoreCheckoutRequest;
use App\Http\Requests\Checkout\VerifyCheckoutCodeRequest;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\TurnstileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private TurnstileService $turnstileService
    ) {}

    public function index(Request $request): Response|RedirectResponse
    {
        $cartData = $this->checkoutService->getCartWithGuns();
        $order = $this->checkoutService->resolveCurrentOrder($request, $cartData);

        if (empty($cartData['cart']) && ! $order) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Koszyk jest pusty. Dodaj produkty, aby przejść do zamówienia.');
        }

        if ($order) {
            return redirect()->route('checkout.show', $this->publicOrderRouteParameters($order));
        }

        return $this->renderCheckout(null, $cartData);
    }

    public function show(Request $request, Order $order): Response
    {
        $request->session()->put('checkout_order_id', $order->id);

        return $this->renderCheckout($order, $this->checkoutService->getCartWithGuns());
    }

    /**
     * @param  array{cart: array<int|string, mixed>, guns: \Illuminate\Support\Collection<int, mixed>, gunPackages: \Illuminate\Support\Collection<int, mixed>}  $cartData
     */
    private function renderCheckout(?Order $order, array $cartData): Response
    {
        return Inertia::render('Checkout/Index', [
            'cart' => $cartData['cart'],
            'guns' => $cartData['guns'],
            'isClubMember' => $this->checkoutService->isClubMember(),
            'checkoutStep' => $this->checkoutService->determineCheckoutStep($order),
            'requiresVoucherEmailVerification' => $this->checkoutService->requiresVoucherEmailVerification(),
            'paymentMethods' => $this->checkoutService->paymentMethodsForFrontend(),
            'order' => $order ? $this->checkoutService->mapOrderForFrontend($order) : null,
            'turnstile' => $this->turnstileService->configurationForFrontend(),
        ]);
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $result = $this->checkoutService->createPendingOrder($request);

        if ($result['status'] === CheckoutService::CREATE_STATUS_CART_EMPTY) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Koszyk jest pusty.');
        }

        if ($result['status'] === CheckoutService::CREATE_STATUS_ITEMS_INVALID) {
            return back()->with('error', 'Nie udało się przygotować pozycji zamówienia.');
        }

        if (! $this->checkoutService->requiresVoucherEmailVerification() && $result['order'] instanceof Order) {
            $this->checkoutService->confirmOrder($result['order']);

            return redirect()
                ->route('checkout.show', $this->publicOrderRouteParameters($result['order']))
                ->with('success', 'Voucher jest gotowy. Możesz pobrać PDF poniżej.');
        }

        return redirect()
            ->route('checkout.show', $this->publicOrderRouteParameters($result['order']))
            ->with('success', 'Wysłaliśmy kod weryfikacyjny na podany adres e-mail.');
    }

    public function resendCode(ResendCheckoutCodeRequest $request, Order $order): RedirectResponse
    {
        if ($order->verified_at !== null) {
            return redirect()->route('checkout.show', $this->publicOrderRouteParameters($order));
        }

        $this->checkoutService->resendVerificationCode($order);

        return redirect()
            ->route('checkout.show', $this->publicOrderRouteParameters($order))
            ->with('success', 'Wysłaliśmy nowy kod weryfikacyjny na podany adres e-mail.');
    }

    public function verify(VerifyCheckoutCodeRequest $request, Order $order): RedirectResponse
    {
        if ($order->verified_at !== null) {
            return redirect()->route('checkout.show', $this->publicOrderRouteParameters($order));
        }

        $validationError = $this->checkoutService->validateVerificationCode($order, $request->getCode());

        if ($validationError !== null) {
            return back()->withErrors([
                'code' => $validationError,
            ]);
        }

        $this->checkoutService->confirmOrder($order);

        return redirect()
            ->route('checkout.show', $this->publicOrderRouteParameters($order))
            ->with('success', 'Zamówienie zostało potwierdzone. Możesz pobrać PDF.');
    }

    public function downloadPdf(Request $request, Order $order): StreamedResponse
    {
        $token = (string) $request->query('token');

        abort_unless($this->checkoutService->canDownloadPdf($order, $token), 403);

        $pdfContent = $this->checkoutService->generateOrderPdf($order);
        $fileName = $this->checkoutService->generateOrderPdfFileName($order);

        return response()->streamDownload(
            static function () use ($pdfContent): void {
                echo $pdfContent;
            },
            $fileName,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * @return array{order: string}
     */
    private function publicOrderRouteParameters(Order $order): array
    {
        return ['order' => $order->public_id];
    }
}
