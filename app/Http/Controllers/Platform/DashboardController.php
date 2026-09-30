<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelInvoice;
use App\Models\HotelSubscription;
use App\Models\PlatformCommunication;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $totalHotels = Hotel::count();
        $activeHotels = Hotel::where('status', 'active')->count();
        $totalRevenue = (float) HotelInvoice::where('status', 'paid')->sum('total_amount');
        $pendingRevenue = (float) HotelInvoice::where('status', 'unpaid')->sum('total_amount');
        $activeSubscriptions = HotelSubscription::where('status', 'active')->count();

        $recentHotels = Hotel::with('currentSubscription')->latest()->take(5)->get();
        $recentInvoices = HotelInvoice::with('hotel')->latest()->take(5)->get();
        $recentComms = PlatformCommunication::with('hotel')->latest()->take(5)->get();

        return view('platform.dashboard', [
            'totalHotels' => $totalHotels,
            'activeHotels' => $activeHotels,
            'totalRevenue' => $totalRevenue,
            'pendingRevenue' => $pendingRevenue,
            'activeSubscriptions' => $activeSubscriptions,
            'recentHotels' => $recentHotels,
            'recentInvoices' => $recentInvoices,
            'recentComms' => $recentComms,
        ]);
    }
}
