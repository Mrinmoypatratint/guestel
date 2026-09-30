<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CreateRoomAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoomRequest;
use App\Models\{Floor, GuestSession, Order, Room, RoomType, ServiceRequest};
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Room::class);

        return view('admin.rooms.index', [
            'rooms' => Room::with(['roomType', 'floor', 'qrCode'])->orderBy('number')->paginate(30),
            'roomTypes' => RoomType::where('is_active', true)->orderBy('name')->get(),
            'floors' => Floor::orderBy('sort_order')->get(),
        ]);
    }

    public function store(CreateRoomRequest $request, CreateRoomAction $action)
    {
        $room = $action->execute($request->validated());

        return back()->with('status', "Room {$room->number} created with permanent QR.");
    }

    public function show(Room $room)
    {
        $this->authorize('view', $room);

        $room->load(['roomType', 'floor', 'qrCode']);

        $requests = ServiceRequest::withoutGlobalScopes()
            ->where('room_id', $room->id)
            ->with(['service', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $orders = Order::withoutGlobalScopes()
            ->where('room_id', $room->id)
            ->with(['items'])
            ->latest()
            ->limit(10)
            ->get();

        $sessions = GuestSession::withoutGlobalScopes()
            ->where('room_id', $room->id)
            ->latest()
            ->limit(5)
            ->get();

        $scansCount = $room->qrCode ? $room->qrCode->scans()->count() : 0;

        return view('admin.rooms.show', [
            'room' => $room,
            'requests' => $requests,
            'orders' => $orders,
            'sessions' => $sessions,
            'scansCount' => $scansCount,
        ]);
    }

    public function updateStatus(Request $request, Room $room)
    {
        $this->authorize('update', $room);
        $data = $request->validate([
            'status' => 'required|in:available,occupied,maintenance,dirty',
        ]);

        $room->update(['status' => $data['status']]);

        return back()->with('status', "Room {$room->number} status updated to " . ucfirst($data['status']) . ".");
    }
}
