<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Vygenerovanie Access Tokenu
        $accessToken = $request->user()->createToken('access-token')->plainTextToken;

        // Vytvorenie Refresh Tokenu (uloží sa do httpOnly cookie)
        $refreshToken = bin2hex(random_bytes(40));
        $request->user()->update(['refresh_token' => hash('sha256', $refreshToken)]);

        return response()->json(['access_token' => $accessToken])
            ->cookie('refresh_token', $refreshToken, 7 * 24 * 60, '/', null, false, true);
    }


    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');
        if (!$refreshToken) {
            return response()->json(['message' => 'No refresh token'], 401);
        }

        $user = User::where('refresh_token', hash('sha256', $refreshToken))->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        // Vygenerovanie nového Access Tokenu
        $accessToken = $user->createToken('access-token')->plainTextToken;

        return response()->json(['access_token' => $accessToken]);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete(); // Zmazanie Access tokenov

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out'])
            ->cookie(cookie()->forget('refresh_token'));
    }

}
