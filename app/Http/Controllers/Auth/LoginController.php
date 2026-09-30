<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials are invalid.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = auth()->user();

        // 1. Company Super Admin -> Direct to /platform
        if ($user->is_platform_admin) {
            return redirect()->intended(route('platform.dashboard'));
        }

        // 2. Check property access
        $properties = $user->accessibleProperties();

        // Multi-property staff -> Property selector ("Where are you working today?")
        if (count($properties) > 1) {
            return redirect()->route('property.select');
        }

        // Single property staff -> Pre-bind context and redirect to role-specific dashboard
        if (count($properties) === 1) {
            $prop = $properties[0];
            $hotelId = $prop['type'] === 'hotel' ? $prop['id'] : ($prop['hotel_id'] ?? null);
            if ($hotelId) {
                $request->session()->put(config('hospitality.tenant_session_key', 'current_hotel_id'), $hotelId);
            }
            if ($prop['type'] === 'restaurant') {
                $request->session()->put('current_restaurant_id', $prop['id']);
            }
            return redirect()->intended($user->workspaceRoute($hotelId));
        }

        return redirect()->intended($user->workspaceRoute());
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }
}
