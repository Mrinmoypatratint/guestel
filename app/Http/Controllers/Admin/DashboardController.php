<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Room;
use App\Models\ServiceRequest;
use App\Support\TenantContext;

class DashboardController extends Controller
{
    public function __invoke(TenantContext $tenant)
    {
        $hotel = $tenant->hotel();
        $hotelId = $tenant->requireId();

        $rooms = Room::withoutGlobalScopes()->where('hotel_id', $hotelId);
        $roomCount = (clone $rooms)->count();
        $occupiedCount = (clone $rooms)->where('status', 'occupied')->count();
        $availableCount = (clone $rooms)->where('status', 'available')->count();
        $maintenanceCount = (clone $rooms)->where('status', 'maintenance')->count();

        // Operational Metrics
        $todayRevenue = Order::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->whereDate('created_at', today())
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->sum('total');

        $activeRequests = ServiceRequest::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->whereIn('status', ['PENDING', 'ACCEPTED', 'ASSIGNED', 'IN_PROGRESS'])
            ->with(['service', 'room', 'department'])
            ->latest()
            ->get();

        $pendingRequestsCount = $activeRequests->where('status', 'PENDING')->count();

        $activeOrders = Order::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING', 'READY'])
            ->with(['room', 'items'])
            ->latest()
            ->get();

        $pendingOrdersCount = $activeOrders->where('status', 'PENDING')->count();

        // SLA Breaches
        $slaBreachesCount = ServiceRequest::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->whereNotIn('status', ['COMPLETED', 'REJECTED', 'CANCELLED'])
            ->whereNotNull('completion_due_at')
            ->where('completion_due_at', '<', now())
            ->count();

        // Guest Satisfaction Score (0 - 100%)
        $totalFeedback = Feedback::withoutGlobalScopes()->where('hotel_id', $hotelId)->count();
        $greatFeedback = Feedback::withoutGlobalScopes()->where('hotel_id', $hotelId)->where('rating', 'great')->count();
        $satisfactionScore = $totalFeedback > 0 ? round(($greatFeedback / $totalFeedback) * 100) : 98;

        // Recent completed or live operations feed
        $recentRequests = ServiceRequest::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->with(['service', 'room', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $recentOrders = Order::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->with(['room', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'hotel' => $hotel,
            'roomCount' => $roomCount,
            'occupiedCount' => $occupiedCount,
            'availableCount' => $availableCount,
            'maintenanceCount' => $maintenanceCount,
            'todayRevenue' => (float) $todayRevenue,
            'activeRequests' => $activeRequests,
            'pendingRequestsCount' => $pendingRequestsCount,
            'activeOrders' => $activeOrders,
            'pendingOrdersCount' => $pendingOrdersCount,
            'slaBreachesCount' => $slaBreachesCount,
            'satisfactionScore' => $satisfactionScore,
            'recentRequests' => $recentRequests,
            'recentOrders' => $recentOrders,
        ]);
    }
}
