<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAssortment
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');
        $body = $request->isJson() ? $request->json()->all() : $request->request->all();
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        if (! is_string($email) || ! is_string($password) || $email === '' || $password === '') {
            return response()->json(['message' => 'Nieprawidłowy email lub hasło.'], 401);
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Nieprawidłowy email lub hasło.'], 401);
        }

        $request->setUserResolver(fn (): User => $user);

        return $next($request);
    }
}
