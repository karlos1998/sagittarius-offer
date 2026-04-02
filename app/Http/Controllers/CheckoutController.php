<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\StoreCheckoutRequest;
use App\Http\Requests\Checkout\VerifyCheckoutCodeRequest;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
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

        return Inertia::render('Checkout/Index', [
            'cart' => $cartData['cart'],
            'guns' => $cartData['guns'],
            'isClubMember' => $this->checkoutService->isClubMember(),
            'checkoutStep' => $this->checkoutService->determineCheckoutStep($order),
            'paymentMethods' => $this->checkoutService->paymentMethodsForFrontend(),
            'order' => $order ? $this->checkoutService->mapOrderForFrontend($order) : null,
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

        return redirect()
            ->route('checkout.index')
            ->with('success', 'Wysłaliśmy kod weryfikacyjny na podany adres e-mail.');
    }

    public function resendCode(Request $request): RedirectResponse
    {
        $order = $this->checkoutService->getCheckoutOrderFromSession($request);

        if (! $order) {
            return redirect()
                ->route('checkout.index')
                ->with('error', 'Nie znaleziono aktywnego zamówienia do weryfikacji.');
        }

        if ($order->verified_at !== null) {
            return redirect()->route('checkout.index');
        }

        $this->checkoutService->resendVerificationCode($order);

        return redirect()
            ->route('checkout.index')
            ->with('success', 'Wysłaliśmy nowy kod weryfikacyjny na podany adres e-mail.');
    }

    public function verify(VerifyCheckoutCodeRequest $request): RedirectResponse
    {
        $order = $this->checkoutService->getCheckoutOrderFromSession($request);

        if (! $order) {
            return redirect()
                ->route('checkout.index')
                ->with('error', 'Nie znaleziono aktywnego zamówienia do weryfikacji.');
        }

        if ($order->verified_at !== null) {
            return redirect()->route('checkout.index');
        }

        $validationError = $this->checkoutService->validateVerificationCode($order, $request->getCode());

        if ($validationError !== null) {
            return back()->withErrors([
                'code' => $validationError,
            ]);
        }

        $this->checkoutService->confirmOrder($order);

        return redirect()
            ->route('checkout.index')
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
}
