<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertySelectController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->is_active, 403);

        if ($user->is_platform_admin) {
            return redirect()->route('platform.dashboard');
        }

        $properties = $user->accessibleProperties();

        // If user only belongs to 1 property, skip selector and enter workspace directly
        if (count($properties) === 1) {
            $prop = $properties[0];
            $hotelId = $prop['type'] === 'hotel' ? $prop['id'] : ($prop['hotel_id'] ?? null);
            if ($hotelId) {
                $request->session()->put(config('hospitality.tenant_session_key', 'current_hotel_id'), $hotelId);
            }
            if ($prop['type'] === 'restaurant') {
                $request->session()->put('current_restaurant_id', $prop['id']);
            }
            return redirect($user->workspaceRoute($hotelId));
        }

        if (count($properties) === 0) {
            return redirect()->route('login')->withErrors(['email' => 'Your account has not been assigned to an active hotel or restaurant workspace yet.']);
        }

        return view('auth.property-select', [
            'properties' => $properties,
            'user' => $user,
        ]);
    }

    public function switch(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->is_active, 403);

        $data = $request->validate([
            'property_type' => ['required', 'string', 'in:hotel,restaurant'],
            'property_id' => ['required', 'integer'],
        ]);

        $type = $data['property_type'];
        $id = (int) $data['property_id'];

        if ($type === 'hotel') {
            abort_unless($user->is_platform_admin || $user->belongsToHotel($id), 403, 'Unauthorized hotel workspace.');
            $hotel = Hotel::whereKey($id)->where('status', 'active')->firstOrFail();
            $request->session()->put(config('hospitality.tenant_session_key', 'current_hotel_id'), $hotel->id);
            $request->session()->forget('current_restaurant_id');

            return redirect($user->workspaceRoute($hotel->id))
                ->with('status', "Workspace switched to {$hotel->name}.");
        }

        if ($type === 'restaurant') {
            abort_unless($user->belongsToRestaurant($id), 403, 'Unauthorized restaurant workspace.');
            $restaurant = Restaurant::whereKey($id)->where('is_active', true)->firstOrFail();
            if ($restaurant->hotel_id) {
                $request->session()->put(config('hospitality.tenant_session_key', 'current_hotel_id'), $restaurant->hotel_id);
            }
            $request->session()->put('current_restaurant_id', $restaurant->id);

            return redirect()->route('admin.orders.index')
                ->with('status', "F&B Workspace switched to {$restaurant->name}.");
        }

        return redirect()->route('admin.dashboard');
    }
}
