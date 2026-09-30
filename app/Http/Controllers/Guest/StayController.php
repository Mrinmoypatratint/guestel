<?php

namespace App\Http\Controllers\Guest;

use App\Actions\{CreateOrderAction, CreateServiceRequestAction};
use App\Http\Controllers\Controller;
use App\Models\{Announcement, Conversation, Facility, Feedback, Hotel, HotelGallery, HotelPolicy, LocalRecommendation, Message, Offer, Order, Restaurant, Service, ServiceRequest};
use App\Notifications\NewGuestMessageNotification;
use App\Services\{GuestSessionResolver, HotelNotifier};
use Illuminate\Http\Request;

class StayController extends Controller
{
    public function show(string $session, GuestSessionResolver $resolver)
    {
        $g = $resolver->resolve($session);

        $hotel = Hotel::with('branding')->findOrFail($g->hotel_id);
        $room = $g->room_id ? \App\Models\Room::withoutGlobalScopes()->with(['roomType', 'floor'])->find($g->room_id) : null;

        $coverImage = HotelGallery::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_cover', true)
            ->value('path');

        $gallery = HotelGallery::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $services = Service::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->with(['department', 'category'])
            ->orderBy('name')
            ->get();

        $restaurants = Restaurant::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->with(['categories', 'menuItems' => fn ($q) => $q->where('is_available', true)->with('modifiers')])
            ->get();

        $announcements = Announcement::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest()
            ->limit(5)
            ->get();

        $facilities = Facility::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->get();

        $policies = HotelPolicy::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $recommendations = LocalRecommendation::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->get();

        $offers = Offer::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->get();

        // Live Guest Tracking
        $activeGuestRequests = ServiceRequest::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('guest_session_id', $g->id)
            ->with(['service', 'department'])
            ->latest()
            ->limit(5)
            ->get();

        $activeGuestOrders = Order::withoutGlobalScopes()
            ->where('hotel_id', $g->hotel_id)
            ->where('guest_session_id', $g->id)
            ->with(['items'])
            ->latest()
            ->limit(5)
            ->get();

        $conversation = Conversation::withoutGlobalScopes()
            ->with(['messages' => fn ($q) => $q->orderBy('created_at')])
            ->where('hotel_id', $g->hotel_id)
            ->where('guest_session_id', $g->id)
            ->where('status', 'open')
            ->first();

        return view('guest.home', compact(
            'hotel',
            'room',
            'g',
            'coverImage',
            'gallery',
            'services',
            'restaurants',
            'announcements',
            'facilities',
            'policies',
            'recommendations',
            'offers',
            'activeGuestRequests',
            'activeGuestOrders',
            'conversation'
        ));
    }

    public function service(Request $request, string $session, GuestSessionResolver $resolver, CreateServiceRequestAction $action)
    {
        $g = $resolver->resolve($session);
        $data = $request->validate([
            'service_id' => ['required', 'integer'],
            'priority' => ['nullable', 'in:normal,high,urgent'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $r = $action->execute($g, $data);

        return back()->with('status', "You're all set. The hotel team has received request {$r->request_number}.");
    }

    public function restaurant(string $session, GuestSessionResolver $resolver)
    {
        $g = $resolver->resolve($session);
        $restaurants = Restaurant::withoutGlobalScopes()
            ->with(['categories', 'menuItems' => fn ($q) => $q->where('is_available', true)->with('modifiers')])
            ->where('hotel_id', $g->hotel_id)
            ->where('is_active', true)
            ->get();

        return view('guest.restaurant', compact('g', 'restaurants'));
    }

    public function order(Request $request, string $session, GuestSessionResolver $resolver, CreateOrderAction $action)
    {
        $g = $resolver->resolve($session);
        $request->merge([
            'items' => array_values(array_filter((array) $request->input('items', []), fn ($i) => (int) ($i['quantity'] ?? 0) > 0)),
        ]);

        $data = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'idempotency_key' => ['required', 'string', 'max:80'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.menu_item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.modifier_ids' => ['nullable', 'array', 'max:10'],
            'items.*.modifier_ids.*' => ['integer'],
            'items.*.note' => ['nullable', 'string', 'max:300'],
            'special_instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        $o = $action->execute($g, $data);

        return redirect()->route('guest.stay', $g->public_id)->with('status', "Order {$o->order_number} confirmed. Your total is {$o->currency} " . number_format((float) $o->total, 2) . '. The kitchen has begun preparation.');
    }

    public function message(Request $request, string $session, GuestSessionResolver $resolver, HotelNotifier $notifier)
    {
        $g = $resolver->resolve($session);
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $c = Conversation::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $g->hotel_id, 'guest_session_id' => $g->id, 'status' => 'open'],
            ['room_id' => $g->room_id, 'last_message_at' => now()]
        );

        Message::withoutGlobalScopes()->create([
            'hotel_id' => $g->hotel_id,
            'conversation_id' => $c->id,
            'guest_session_id' => $g->id,
            'sender_type' => 'guest',
            'body' => $data['body'],
        ]);

        $c->forceFill(['last_message_at' => now()])->save();
        $notifier->permission($g->hotel_id, 'chat.manage', new NewGuestMessageNotification($c));

        return back()->with('status', 'Your message has been delivered to reception.');
    }

    public function feedback(Request $request, string $session, GuestSessionResolver $resolver)
    {
        $g = $resolver->resolve($session);
        $data = $request->validate([
            'rating' => ['required', 'in:great,okay,needs_attention'],
            'comment' => ['nullable', 'string', 'max:1500'],
        ]);

        Feedback::withoutGlobalScopes()->create([
            'hotel_id' => $g->hotel_id,
            'room_id' => $g->room_id,
            'guest_session_id' => $g->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return back()->with('status', 'Thank you for sharing your feedback. Our guest relations manager has received it.');
    }
}
