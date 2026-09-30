<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Room;
use App\Models\ServiceRequest;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request, TenantContext $tenant): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $hotelId = $tenant->requireId();
        $results = [];

        // Rooms
        $rooms = Room::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->where(function ($query) use ($q) {
                $query->where('number', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->with('roomType')
            ->limit(5)
            ->get();

        foreach ($rooms as $room) {
            $results[] = [
                'category' => 'Rooms',
                'title' => 'Room ' . $room->number . ($room->name ? ' (' . $room->name . ')' : ''),
                'subtitle' => ($room->roomType?->name ?? 'Standard') . ' · ' . ucfirst($room->status),
                'url' => route('admin.rooms.show', $room),
                'badge' => ucfirst($room->status),
            ];
        }

        // Service Requests
        $requests = ServiceRequest::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->where(function ($query) use ($q) {
                $query->where('request_number', 'like', "%{$q}%")
                    ->orWhere('note', 'like', "%{$q}%")
                    ->orWhereHas('service', fn ($s) => $s->where('name', 'like', "%{$q}%"));
            })
            ->with(['service', 'room'])
            ->limit(5)
            ->get();

        foreach ($requests as $r) {
            $results[] = [
                'category' => 'Service Requests',
                'title' => $r->request_number . ' · ' . ($r->service?->name ?? 'Service'),
                'subtitle' => 'Room ' . ($r->room?->number ?? '—') . ' · Status: ' . $r->status,
                'url' => route('admin.requests.index'),
                'badge' => $r->status,
            ];
        }

        // Orders
        $orders = Order::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->where('order_number', 'like', "%{$q}%")
            ->with('room')
            ->limit(5)
            ->get();

        foreach ($orders as $order) {
            $results[] = [
                'category' => 'Dining Orders',
                'title' => $order->order_number . ' (' . $order->currency . ' ' . number_format((float) $order->total, 2) . ')',
                'subtitle' => 'Room ' . ($order->room?->number ?? '—') . ' · Status: ' . $order->status,
                'url' => route('admin.orders.index'),
                'badge' => $order->status,
            ];
        }

        // Menu Items
        $menuItems = MenuItem::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get();

        foreach ($menuItems as $item) {
            $results[] = [
                'category' => 'Restaurant Menu',
                'title' => $item->name,
                'subtitle' => 'Price: ' . number_format((float) $item->price, 2) . ($item->is_available ? ' · Available' : ' · Sold Out'),
                'url' => route('admin.restaurant.index'),
                'badge' => $item->is_available ? 'Available' : 'Unavailable',
            ];
        }

        return response()->json(['results' => $results]);
    }
}
