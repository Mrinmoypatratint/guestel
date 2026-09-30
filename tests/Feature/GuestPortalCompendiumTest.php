<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Hotel;
use App\Models\HotelPolicy;
use App\Models\Offer;
use App\Models\QrCode;
use App\Models\Restaurant;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestPortalCompendiumTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_portal_resolves_room_compendium_and_dining(): void
    {
        $hotel = Hotel::create([
            'name' => 'Royal Heritage Grand',
            'slug' => 'royal-heritage',
            'status' => 'active',
        ]);

        $type = RoomType::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'name' => 'Ocean Suite',
            'capacity' => 2,
            'base_rate' => 250,
            'is_active' => true,
        ]);

        $room = Room::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'room_type_id' => $type->id,
            'number' => '404',
            'status' => 'occupied',
            'is_active' => true,
        ]);

        $qr = QrCode::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'public_token' => 'permanent-qr-token-for-room-404-luxury-concierge',
            'qrable_type' => Room::class,
            'qrable_id' => $room->id,
            'label' => 'Room 404 QR',
            'is_active' => true,
        ]);

        Facility::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'name' => 'Infinity Rooftop Pool',
            'description' => 'Heated saltwater pool with panoramic skyline views.',
            'opening_hours' => '06:00 - 22:00',
            'location' => 'Level 14',
            'is_active' => true,
        ]);

        HotelPolicy::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'title' => 'Standard Check-Out',
            'content' => 'Check-out time is strictly 11:00 AM.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Offer::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'title' => 'Romantic Sunset Spa Package',
            'description' => 'Couple massage with complimentary champagne.',
            'price' => 140.00,
            'is_active' => true,
        ]);

        $restaurant = Restaurant::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'name' => 'The Grand Dining Room',
            'slug' => 'the-grand-dining-room',
            'status' => 'active',
            'opening_time' => '07:00:00',
            'closing_time' => '23:00:00',
        ]);

        // Step 1: Scan QR code and follow redirect to guest portal
        $portalResponse = $this->followingRedirects()->get("/g/{$qr->public_token}");
        $portalResponse->assertOk()
            ->assertSee('Royal Heritage Grand')
            ->assertSee('Room 404')
            ->assertSee('Infinity Rooftop Pool')
            ->assertSee('Standard Check-Out')
            ->assertSee('Romantic Sunset Spa Package');

        // Step 2: Grab the created session and visit in-room dining
        $session = \App\Models\GuestSession::withoutGlobalScopes()->firstOrFail();
        $diningResponse = $this->get("/stay/{$session->public_id}/restaurant");
        $diningResponse->assertOk()
            ->assertSee('The Grand Dining Room');
    }
}
