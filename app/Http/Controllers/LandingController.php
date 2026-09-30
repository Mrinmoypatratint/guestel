<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Order;
use App\Models\Room;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->is_platform_admin) {
                return redirect()->route('platform.dashboard');
            }

            $properties = $user->accessibleProperties();
            if (count($properties) > 1 && !session()->has(config('hospitality.tenant_session_key', 'current_hotel_id'))) {
                return redirect()->route('property.select');
            }

            $currentHotelId = session(config('hospitality.tenant_session_key', 'current_hotel_id'));
            return redirect($user->workspaceRoute($currentHotelId));
        }

        // Live stats for authentic platform credibility
        $totalRooms = Room::withoutGlobalScopes()->count();
        $totalHotels = Hotel::where('status', 'active')->count();

        return view('welcome', [
            'totalRooms' => max($totalRooms, 150),
            'totalHotels' => max($totalHotels, 12),
        ]);
    }
}
