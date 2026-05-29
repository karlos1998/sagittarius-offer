<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AppServiceProvider extends ServiceProvider
{
    private const CHECKOUT_ATTEMPTS_PER_MINUTE = 5;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('checkout-email', function (Request $request): Limit {
            return Limit::perMinute(self::CHECKOUT_ATTEMPTS_PER_MINUTE)
                ->by($this->checkoutEmailThrottleKey($request))
                ->response(fn (Request $request, array $headers): Response => $this->checkoutThrottleResponse($request, $headers));
        });

        RateLimiter::for('checkout-code', function (Request $request): Limit {
            return Limit::perMinute(self::CHECKOUT_ATTEMPTS_PER_MINUTE)
                ->by($this->checkoutCodeThrottleKey($request))
                ->response(fn (Request $request, array $headers): Response => $this->checkoutThrottleResponse($request, $headers));
        });
    }

    private function checkoutEmailThrottleKey(Request $request): string
    {
        $email = trim(Str::lower((string) $request->input('email', '')));
        $identifier = $email !== ''
            ? $email
            : (string) ($this->routeOrderKey($request) ?? $request->session()->get('checkout_order_id', 'guest'));

        return $identifier.'|'.$request->ip();
    }

    private function checkoutCodeThrottleKey(Request $request): string
    {
        return (string) ($this->routeOrderKey($request) ?? $request->session()->get('checkout_order_id', 'guest')).'|'.$request->ip();
    }

    private function routeOrderKey(Request $request): ?string
    {
        $order = $request->route('order');

        if ($order instanceof Model) {
            return (string) $order->getKey();
        }

        return is_string($order) ? $order : null;
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    private function checkoutThrottleResponse(Request $request, array $headers): Response
    {
        $message = 'Za dużo prób. Spróbuj ponownie za chwilę.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], 429, $headers);
        }

        return back()
            ->with('error', $message)
            ->withHeaders($headers);
    }
}
