<?php

namespace Database\Seeders;

use App\Actions\CreateHotelAction;
use App\Actions\GenerateQrCodeAction;
use App\Models\Announcement;
use App\Models\Conversation;
use App\Models\Department;
use App\Models\Facility;
use App\Models\Feedback;
use App\Models\Floor;
use App\Models\GuestSession;
use App\Models\Hotel;
use App\Models\HotelBranding;
use App\Models\HotelGallery;
use App\Models\HotelPolicy;
use App\Models\LocalRecommendation;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Message;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\Restaurant;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PropertyAccess;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\HotelSubscription;
use App\Models\HotelInvoice;
use App\Models\PlatformCommunication;
use App\Models\ServiceRequestStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoHotelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Platform Administrator exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Alexander Vance',
                'password' => Hash::make('Admin12345!'),
                'is_platform_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Ensure Staff Members exist
        $housekeeper = User::firstOrCreate(
            ['email' => 'maria.santos@grandazure.com'],
            [
                'name' => 'Maria Santos (Housekeeping Lead)',
                'password' => Hash::make('Admin12345!'),
                'is_active' => true,
            ]
        );

        $chef = User::firstOrCreate(
            ['email' => 'chef.marcus@grandazure.com'],
            [
                'name' => 'Chef Marcus Drake (Executive Chef)',
                'password' => Hash::make('Admin12345!'),
                'is_active' => true,
            ]
        );

        $hotel = Hotel::where('slug', 'grand-azure')->first();

        if (!$hotel) {
            /** @var CreateHotelAction $createHotel */
            $createHotel = app(CreateHotelAction::class);
            $hotel = $createHotel->execute([
                'name' => 'Grand Azure Resort',
                'slug' => 'grand-azure',
                'email' => 'concierge@grandazure.com',
                'phone' => '+1 (555) 234-5678',
                'timezone' => 'UTC',
                'city' => 'Miami Beach',
                'state' => 'FL',
                'country' => 'USA',
                'admin_name' => $admin->name,
                'admin_email' => $admin->email,
            ]);
        }

        // Attach staff to hotel
        $hotel->users()->syncWithoutDetaching([
            $admin->id => ['status' => 'active', 'is_owner' => true],
            $housekeeper->id => ['status' => 'active', 'is_owner' => false],
            $chef->id => ['status' => 'active', 'is_owner' => false],
        ]);

        // Create Housekeeping Role and assign to Maria Santos
        $hkRole = Role::firstOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'HOUSEKEEPING_LEAD'],
            ['label' => 'Housekeeping Lead', 'scope' => 'hotel']
        );
        $hkRole->permissions()->sync(
            Permission::whereIn('name', [
                'rooms.view', 'rooms.update',
                'requests.view', 'requests.assign', 'requests.update',
                'notifications.view'
            ])->pluck('id')
        );
        $housekeeper->roles()->syncWithoutDetaching([$hkRole->id => ['hotel_id' => $hotel->id]]);

        // Create Executive Chef Role and assign to Marcus Drake
        $chefRole = Role::firstOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'EXECUTIVE_CHEF'],
            ['label' => 'Executive Chef / F&B Director', 'scope' => 'restaurant']
        );
        $chefRole->permissions()->sync(
            Permission::whereIn('name', [
                'orders.view', 'orders.update',
                'menu.manage',
                'notifications.view'
            ])->pluck('id')
        );
        $chef->roles()->syncWithoutDetaching([$chefRole->id => ['hotel_id' => $hotel->id]]);

        /** @var GenerateQrCodeAction $generateQr */
        $generateQr = app(GenerateQrCodeAction::class);

        // 3. Update Hotel Branding with Cover and Color Palette
        HotelBranding::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id],
            [
                'primary_color' => '#0f172a',
                'accent_color' => '#f59e0b',
                'cover_image_path' => 'hotels/1/hero_cover.jpg',
                'logo_path' => 'hotels/1/hero_cover.jpg',
                'welcome_message' => 'Everything you need for an unforgettable stay. Browse in-room dining, request amenities, or message reception directly.',
            ]
        );

        // 4. Hotel Image Gallery
        $galleryImages = [
            [
                'path' => 'hotels/1/hero_cover.jpg',
                'category' => 'exterior',
                'alt_text' => 'Oceanfront Grounds & Heated Pool at Dusk',
                'is_cover' => true,
                'sort_order' => 1,
            ],
            [
                'path' => 'hotels/1/lobby.jpg',
                'category' => 'lobby',
                'alt_text' => 'Grand Crystal Chandelier Reception & Lounge',
                'is_cover' => false,
                'sort_order' => 2,
            ],
            [
                'path' => 'hotels/1/pool.jpg',
                'category' => 'pool',
                'alt_text' => 'Private Cabanas & Infinity Pool Horizon',
                'is_cover' => false,
                'sort_order' => 3,
            ],
            [
                'path' => 'hotels/1/spa.jpg',
                'category' => 'spa',
                'alt_text' => 'Azure Thalasso Spa & Hydrotherapy Sanctuary',
                'is_cover' => false,
                'sort_order' => 4,
            ],
            [
                'path' => 'hotels/1/dining.jpg',
                'category' => 'dining',
                'alt_text' => 'The Azure Grill & Coastal Wine Bar',
                'is_cover' => false,
                'sort_order' => 5,
            ],
            [
                'path' => 'hotels/1/penthouse.jpg',
                'category' => 'rooms',
                'alt_text' => 'Skyline Terrace Penthouse Suite',
                'is_cover' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($galleryImages as $img) {
            HotelGallery::withoutGlobalScopes()->updateOrCreate(
                ['hotel_id' => $hotel->id, 'path' => $img['path']],
                $img
            );
        }

        // 5. Floors
        $floor1 = Floor::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'number' => 1],
            ['name' => 'Floor 1 · Beach Promenade', 'sort_order' => 1]
        );

        $floor2 = Floor::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'number' => 2],
            ['name' => 'Floor 2 · Ocean Panorama', 'sort_order' => 2]
        );

        $floor3 = Floor::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'number' => 3],
            ['name' => 'Floor 3 · Presidential Penthouse', 'sort_order' => 3]
        );

        // 6. Room Types
        $deluxe = RoomType::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'Deluxe Ocean View'],
            [
                'description' => 'Spacious room with king bed, marble bathroom, and private oceanfront balcony',
                'capacity' => 2,
                'base_rate' => 14500.00,
                'is_active' => true,
            ]
        );

        $executive = RoomType::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'Executive Ocean Suite'],
            [
                'description' => 'Expansive suite featuring separate lounge, dual vanities, espresso bar, and wrap-around terrace',
                'capacity' => 3,
                'base_rate' => 24000.00,
                'is_active' => true,
            ]
        );

        $penthouse = RoomType::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'Imperial Penthouse Villa'],
            [
                'description' => 'Ultra-luxury two-level penthouse with private plunge pool, outdoor dining terrace, and dedicated butler',
                'capacity' => 6,
                'base_rate' => 75000.00,
                'is_active' => true,
            ]
        );

        // 7. Rooms across 3 Floors
        $roomsData = [
            // Floor 1
            ['number' => '101', 'name' => 'Sunset Palm Room', 'floor_id' => $floor1->id, 'room_type_id' => $deluxe->id, 'status' => 'occupied'],
            ['number' => '102', 'name' => 'Coral Reef Room', 'floor_id' => $floor1->id, 'room_type_id' => $deluxe->id, 'status' => 'occupied'],
            ['number' => '103', 'name' => 'Azure Lagoon Studio', 'floor_id' => $floor1->id, 'room_type_id' => $deluxe->id, 'status' => 'available'],
            ['number' => '104', 'name' => 'Hibiscus Garden Suite', 'floor_id' => $floor1->id, 'room_type_id' => $deluxe->id, 'status' => 'cleaning'],
            // Floor 2
            ['number' => '201', 'name' => 'Royal Horizon Suite', 'floor_id' => $floor2->id, 'room_type_id' => $executive->id, 'status' => 'occupied'],
            ['number' => '202', 'name' => 'Sapphire Breeze Suite', 'floor_id' => $floor2->id, 'room_type_id' => $executive->id, 'status' => 'available'],
            ['number' => '203', 'name' => 'Emerald Coast Vista', 'floor_id' => $floor2->id, 'room_type_id' => $executive->id, 'status' => 'occupied'],
            ['number' => '204', 'name' => 'Marina Penthouse Suite', 'floor_id' => $floor2->id, 'room_type_id' => $executive->id, 'status' => 'maintenance'],
            // Floor 3
            ['number' => '301', 'name' => 'The Grand Azure Imperial Villa', 'floor_id' => $floor3->id, 'room_type_id' => $penthouse->id, 'status' => 'occupied'],
            ['number' => '302', 'name' => 'Skyline Diamond Penthouse', 'floor_id' => $floor3->id, 'room_type_id' => $penthouse->id, 'status' => 'available'],
        ];

        $createdRooms = [];
        foreach ($roomsData as $r) {
            $room = Room::withoutGlobalScopes()->updateOrCreate(
                ['hotel_id' => $hotel->id, 'number' => $r['number']],
                [
                    'floor_id' => $r['floor_id'],
                    'room_type_id' => $r['room_type_id'],
                    'name' => $r['name'],
                    'status' => $r['status'],
                    'is_active' => true,
                ]
            );

            // Ensure QR exists
            $qr = QrCode::withoutGlobalScopes()
                ->where('hotel_id', $hotel->id)
                ->where('qrable_type', Room::class)
                ->where('qrable_id', $room->id)
                ->first();

            if (!$qr) {
                $qr = $generateQr->execute($hotel->id, $room, 'Room ' . $room->number);
            }

            $createdRooms[$room->number] = ['room' => $room, 'qr' => $qr];
        }

        // 8. In-Room Dining Restaurant & Visual Menu
        $restaurant = Restaurant::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'name' => 'The Azure Grill & Lounge'],
            [
                'description' => 'Artisanal coastal dining, craft cocktails, and 24/7 in-room dining.',
                'tax_rate' => 8.50,
                'currency' => 'INR',
                'opens_at' => '06:00:00',
                'closes_at' => '23:30:00',
                'is_active' => true,
            ]
        );

        $restaurant->users()->syncWithoutDetaching([
            $chef->id => ['role' => 'chef', 'status' => 'active']
        ]);

        $starters = MenuCategory::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'restaurant_id' => $restaurant->id, 'name' => 'Starters & Small Plates'],
            ['sort_order' => 1, 'is_active' => true]
        );

        $mains = MenuCategory::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'restaurant_id' => $restaurant->id, 'name' => 'Signature Mains'],
            ['sort_order' => 2, 'is_active' => true]
        );

        $beverages = MenuCategory::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'restaurant_id' => $restaurant->id, 'name' => 'Beverages & Cocktails'],
            ['sort_order' => 3, 'is_active' => true]
        );

        $dishes = [
            [
                'name' => 'Prime Wagyu Cheeseburger',
                'category_id' => $mains->id,
                'description' => 'Dry-aged Wagyu beef patty, toasted brioche, aged cheddar, caramelised shallots & truffle fries',
                'price' => 1450.00,
                'image_path' => 'menu/1/burger.jpg',
                'preparation_minutes' => 20,
                'dietary_info' => ['Gourmet', 'Signature'],
                'allergens' => ['Gluten', 'Dairy'],
            ],
            [
                'name' => 'Pan-Seared Chilean Sea Bass',
                'category_id' => $mains->id,
                'description' => 'Saffron risotto, charred asparagus spears, citrus thyme beurre blanc',
                'price' => 2200.00,
                'image_path' => 'menu/1/seabass.jpg',
                'preparation_minutes' => 25,
                'dietary_info' => ['Gluten-Free', 'Seafood'],
                'allergens' => ['Fish', 'Dairy'],
            ],
            [
                'name' => 'Crispy Calamari & Lime Aioli',
                'category_id' => $starters->id,
                'description' => 'Flash-fried calamari tossed in smoked sea salt and served with fresh garlic citrus dip',
                'price' => 950.00,
                'image_path' => 'menu/1/calamari.jpg',
                'preparation_minutes' => 15,
                'dietary_info' => ['Seafood'],
                'allergens' => ['Molluscs', 'Gluten', 'Eggs'],
            ],
            [
                'name' => 'Truffle & Parmesan Pommes Frites',
                'category_id' => $starters->id,
                'description' => 'Hand-cut russet fries infused with white Alba truffle oil, aged pecorino, and fresh parsley',
                'price' => 650.00,
                'image_path' => 'menu/1/fries.jpg',
                'preparation_minutes' => 12,
                'dietary_info' => ['Vegetarian'],
                'allergens' => ['Dairy'],
            ],
            [
                'name' => 'Tropical Azure Mocktail',
                'category_id' => $beverages->id,
                'description' => 'Fresh passion fruit nectar, cold-pressed pineapple, bruised mint, and organic coconut water',
                'price' => 450.00,
                'image_path' => 'menu/1/mocktail.jpg',
                'preparation_minutes' => 5,
                'dietary_info' => ['Vegan', 'Gluten-Free'],
                'allergens' => [],
            ],
        ];

        $createdMenuItems = [];
        foreach ($dishes as $dish) {
            $item = MenuItem::withoutGlobalScopes()->updateOrCreate(
                ['hotel_id' => $hotel->id, 'restaurant_id' => $restaurant->id, 'name' => $dish['name']],
                [
                    'menu_category_id' => $dish['category_id'],
                    'description' => $dish['description'],
                    'price' => $dish['price'],
                    'image_path' => $dish['image_path'],
                    'preparation_minutes' => $dish['preparation_minutes'],
                    'dietary_info' => $dish['dietary_info'],
                    'allergens' => $dish['allergens'],
                    'is_available' => true,
                ]
            );
            $createdMenuItems[$dish['name']] = $item;
        }

        // 9. Active Guest Sessions for Demo Rooms
        $room101 = $createdRooms['101']['room'];
        $qr101 = $createdRooms['101']['qr'];
        $session101 = GuestSession::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room101->id],
            [
                'qr_code_id' => $qr101->id,
                'public_id' => 'grand-azure-guest-room-101-session-token-v1',
                'browser_session_hash' => null,
                'expires_at' => now()->addHours(24),
                'last_seen_at' => now(),
                'status' => 'active',
            ]
        );

        $room201 = $createdRooms['201']['room'];
        $qr201 = $createdRooms['201']['qr'];
        $session201 = GuestSession::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room201->id],
            [
                'qr_code_id' => $qr201->id,
                'public_id' => 'grand-azure-guest-room-201-session-token-v1',
                'browser_session_hash' => null,
                'expires_at' => now()->addHours(36),
                'last_seen_at' => now(),
                'status' => 'active',
            ]
        );

        $room102 = $createdRooms['102']['room'];
        $qr102 = $createdRooms['102']['qr'];
        $session102 = GuestSession::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room102->id],
            [
                'qr_code_id' => $qr102->id,
                'public_id' => 'grand-azure-guest-room-102-session-token-v1',
                'browser_session_hash' => null,
                'expires_at' => now()->addHours(18),
                'last_seen_at' => now(),
                'status' => 'active',
            ]
        );

        // Record realistic QR scans
        QrScan::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room101->id, 'anonymous_session_id' => 'demo-user-101'],
            [
                'qr_code_id' => $qr101->id,
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1',
                'ip_hash' => hash_hmac('sha256', '127.0.0.1', config('app.key')),
                'scanned_at' => now()->subMinutes(45),
            ]
        );

        // 10. Live Service Requests & Dispatch Queue
        $housekeepingDept = Department::withoutGlobalScopes()->where('hotel_id', $hotel->id)->where('slug', 'housekeeping')->first();
        $conciergeDept = Department::withoutGlobalScopes()->where('hotel_id', $hotel->id)->where('slug', 'concierge')->first();
        $maintenanceDept = Department::withoutGlobalScopes()->where('hotel_id', $hotel->id)->where('slug', 'maintenance')->first();

        $pillowService = Service::withoutGlobalScopes()->where('hotel_id', $hotel->id)->where('name', 'like', '%Pillow%')->first()
            ?? Service::withoutGlobalScopes()->firstOrCreate([
                'hotel_id' => $hotel->id,
                'name' => 'Plush Extra Pillows & Linens',
            ], [
                'slug' => 'plush-extra-pillows-and-linens',
                'department_id' => $housekeepingDept?->id ?? 1,
                'target_response_minutes' => 10,
                'target_completion_minutes' => 20,
                'is_active' => true,
            ]);

        $transportService = Service::withoutGlobalScopes()->where('hotel_id', $hotel->id)->where('name', 'like', '%Airport%')->first()
            ?? Service::withoutGlobalScopes()->firstOrCreate([
                'hotel_id' => $hotel->id,
                'name' => 'Luxury Private Airport Transfer',
            ], [
                'slug' => 'luxury-private-airport-transfer',
                'department_id' => $conciergeDept?->id ?? 1,
                'target_response_minutes' => 15,
                'target_completion_minutes' => 45,
                'is_active' => true,
            ]);

        $turndownService = Service::withoutGlobalScopes()->firstOrCreate([
            'hotel_id' => $hotel->id,
            'name' => 'Evening Turndown Service & Fresh Water',
        ], [
            'slug' => 'evening-turndown-service-and-fresh-water',
            'department_id' => $housekeepingDept?->id ?? 1,
            'target_response_minutes' => 10,
            'target_completion_minutes' => 25,
            'is_active' => true,
        ]);

        // Request 1: Room 101 - IN_PROGRESS
        $sr1 = ServiceRequest::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room101->id, 'service_id' => $pillowService->id],
            [
                'request_number' => 'SR-1048',
                'guest_session_id' => $session101->id,
                'department_id' => $pillowService->department_id,
                'priority' => 'high',
                'status' => 'IN_PROGRESS',
                'guest_note' => 'Please bring 2 extra goose feather pillows and lavender bath salts.',
                'response_due_at' => now()->subMinutes(15),
                'completion_due_at' => now()->addMinutes(8),
                'created_at' => now()->subMinutes(18),
            ]
        );

        // Request 2: Room 201 - ACCEPTED
        $sr2 = ServiceRequest::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room201->id, 'service_id' => $transportService->id],
            [
                'request_number' => 'SR-1049',
                'guest_session_id' => $session201->id,
                'department_id' => $transportService->department_id,
                'priority' => 'normal',
                'status' => 'ACCEPTED',
                'guest_note' => 'Private chauffeur requested for 2 guests to Miami International Airport tomorrow at 08:30 AM.',
                'response_due_at' => now()->addMinutes(10),
                'completion_due_at' => now()->addMinutes(35),
                'created_at' => now()->subMinutes(6),
            ]
        );

        // Request 3: Room 102 - PENDING (Recent alert)
        ServiceRequest::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room102->id, 'service_id' => $turndownService->id],
            [
                'request_number' => 'SR-1050',
                'guest_session_id' => $session102->id,
                'department_id' => $turndownService->department_id,
                'priority' => 'normal',
                'status' => 'PENDING',
                'guest_note' => 'Evening turndown requested before 8:00 PM if possible.',
                'response_due_at' => now()->addMinutes(8),
                'completion_due_at' => now()->addMinutes(22),
                'created_at' => now()->subMinutes(2),
            ]
        );

        // 11. Live Kitchen Dining Orders
        $burger = $createdMenuItems['Prime Wagyu Cheeseburger'];
        $seabass = $createdMenuItems['Pan-Seared Chilean Sea Bass'];
        $calamari = $createdMenuItems['Crispy Calamari & Lime Aioli'];
        $fries = $createdMenuItems['Truffle & Parmesan Pommes Frites'];
        $mocktail = $createdMenuItems['Tropical Azure Mocktail'];

        // Order 1: Room 101 - PREPARING
        $order1 = Order::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room101->id, 'idempotency_key' => 'demo-order-room-101-lunch'],
            [
                'order_number' => 'ORD-1082',
                'guest_session_id' => $session101->id,
                'restaurant_id' => $restaurant->id,
                'subtotal' => 4450.00,
                'tax' => 378.25,
                'total' => 4828.25,
                'currency' => 'INR',
                'status' => 'PREPARING',
                'special_instructions' => 'Burgers medium-rare. Extra truffle aioli on the side, please.',
                'created_at' => now()->subMinutes(12),
            ]
        );

        OrderItem::withoutGlobalScopes()->updateOrCreate(
            ['order_id' => $order1->id, 'menu_item_id' => $burger->id],
            ['hotel_id' => $hotel->id, 'item_name' => $burger->name, 'unit_price' => $burger->price, 'quantity' => 2, 'line_total' => 2900.00]
        );
        OrderItem::withoutGlobalScopes()->updateOrCreate(
            ['order_id' => $order1->id, 'menu_item_id' => $fries->id],
            ['hotel_id' => $hotel->id, 'item_name' => $fries->name, 'unit_price' => $fries->price, 'quantity' => 1, 'line_total' => 650.00]
        );
        OrderItem::withoutGlobalScopes()->updateOrCreate(
            ['order_id' => $order1->id, 'menu_item_id' => $mocktail->id],
            ['hotel_id' => $hotel->id, 'item_name' => $mocktail->name, 'unit_price' => $mocktail->price, 'quantity' => 2, 'line_total' => 900.00]
        );

        // Order 2: Room 201 - ACCEPTED
        $order2 = Order::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'room_id' => $room201->id, 'idempotency_key' => 'demo-order-room-201-dinner'],
            [
                'order_number' => 'ORD-1083',
                'guest_session_id' => $session201->id,
                'restaurant_id' => $restaurant->id,
                'subtotal' => 5350.00,
                'tax' => 454.75,
                'total' => 5804.75,
                'currency' => 'INR',
                'status' => 'ACCEPTED',
                'special_instructions' => 'Please deliver to the private terrace table overlooking the sea.',
                'created_at' => now()->subMinutes(5),
            ]
        );

        OrderItem::withoutGlobalScopes()->updateOrCreate(
            ['order_id' => $order2->id, 'menu_item_id' => $seabass->id],
            ['hotel_id' => $hotel->id, 'item_name' => $seabass->name, 'unit_price' => $seabass->price, 'quantity' => 2, 'line_total' => 4400.00]
        );
        OrderItem::withoutGlobalScopes()->updateOrCreate(
            ['order_id' => $order2->id, 'menu_item_id' => $calamari->id],
            ['hotel_id' => $hotel->id, 'item_name' => $calamari->name, 'unit_price' => $calamari->price, 'quantity' => 1, 'line_total' => 950.00]
        );

        // 12. Front Desk Guest Chat Conversations
        $conv1 = Conversation::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $hotel->id, 'guest_session_id' => $session101->id],
            ['room_id' => $room101->id, 'status' => 'open']
        );

        Message::withoutGlobalScopes()->firstOrCreate(
            ['conversation_id' => $conv1->id, 'body' => 'Good afternoon! Could we please have 2 extra bath sheets and an ice bucket for Room 101?'],
            ['hotel_id' => $hotel->id, 'sender_type' => 'guest', 'created_at' => now()->subMinutes(25)]
        );

        Message::withoutGlobalScopes()->firstOrCreate(
            ['conversation_id' => $conv1->id, 'body' => 'Good afternoon Mr. Wright! Our team has dispatched fresh linens and an ice bucket directly to your room.'],
            ['hotel_id' => $hotel->id, 'sender_type' => 'staff', 'user_id' => $admin->id, 'created_at' => now()->subMinutes(22)]
        );

        // 13. Guest Feedback Submissions
        Feedback::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'guest_session_id' => $session101->id],
            [
                'room_id' => $room101->id,
                'rating' => 'great',
                'comment' => 'The digital in-room concierge is phenomenal. The Wagyu burger and sea bass were both five-star dining quality.',
            ]
        );

        Feedback::withoutGlobalScopes()->updateOrCreate(
            ['hotel_id' => $hotel->id, 'guest_session_id' => $session201->id],
            [
                'room_id' => $room201->id,
                'rating' => 'great',
                'comment' => 'Effortless QR access right from the bedside table. Concierge service has been completely seamless.',
            ]
        );

        // 14. SaaS Multi-Tenant Subscriptions & Billing
        HotelSubscription::updateOrCreate(
            ['hotel_id' => $hotel->id],
            [
                'plan_name' => 'Professional Cloud',
                'billing_cycle' => 'monthly',
                'fee' => 9999.00,
                'currency' => 'INR',
                'status' => 'active',
                'starts_at' => now()->subMonths(2),
                'renews_at' => now()->addMonth(),
            ]
        );

        // Seed Invoices: 1 Paid, 1 Unpaid
        HotelInvoice::firstOrCreate(
            ['invoice_number' => 'INV-202608-00101'],
            [
                'hotel_id' => $hotel->id,
                'title' => 'Monthly SaaS Platform Subscription (August 2026)',
                'subtotal' => 9999.00,
                'tax_amount' => 1799.82,
                'total_amount' => 11798.82,
                'currency' => 'INR',
                'status' => 'paid',
                'due_date' => now()->subDays(20),
                'paid_at' => now()->subDays(22),
                'payment_method' => 'UPI / QR (GPay)',
                'payment_reference' => 'UPI-983109283120',
                'notes' => 'Settled via Google Pay Corporate UPI.',
            ]
        );

        HotelInvoice::firstOrCreate(
            ['invoice_number' => 'INV-202609-00102'],
            [
                'hotel_id' => $hotel->id,
                'title' => 'Monthly SaaS Platform Subscription & Maintenance (September 2026)',
                'subtotal' => 9999.00,
                'tax_amount' => 1799.82,
                'total_amount' => 11798.82,
                'currency' => 'INR',
                'status' => 'unpaid',
                'due_date' => now()->addDays(10),
                'notes' => 'Multi-tenant cloud platform licensing & dedicated SLA support.',
            ]
        );

        // 15. SaaS Communications Log
        PlatformCommunication::firstOrCreate(
            ['subject' => 'Welcome to Hotel Guest Platform SaaS!'],
            [
                'sender_id' => $admin->id,
                'hotel_id' => $hotel->id,
                'target_audience' => 'hotel_admin',
                'recipient_email' => 'concierge@grandazure.com',
                'message' => "Welcome to the Hotel Guest Platform! Your multi-tenant cloud environment is provisioned and active. We look forward to powering your guest experience.\n\nSaaS Platform Management",
                'category' => 'onboarding',
                'status' => 'sent',
                'sent_at' => now()->subMonths(2),
            ]
        );

        PlatformCommunication::firstOrCreate(
            ['subject' => 'September Service Invoice Generated - Grand Azure Resort'],
            [
                'sender_id' => $admin->id,
                'hotel_id' => $hotel->id,
                'target_audience' => 'hotel_admin',
                'recipient_email' => 'concierge@grandazure.com',
                'message' => "Your monthly SaaS billing invoice #INV-202609-00102 for ₹11,798.82 (including 18% GST) has been generated. Please settle prior to the due date.\n\nSaaS Finance Team",
                'category' => 'billing_notice',
                'status' => 'sent',
                'sent_at' => now()->subDays(5),
            ]
        );

        // 16. Multi-Property Demo: Second Property (Royal Palms Luxury Resort, Kolkata)
        $secondHotel = Hotel::firstOrCreate(
            ['slug' => 'royal-palms-resort'],
            [
                'name' => 'Royal Palms Luxury Resort',
                'email' => 'concierge@royalpalms.com',
                'phone' => '+91 33 2489 1200',
                'timezone' => 'Asia/Kolkata',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'status' => 'active',
            ]
        );

        HotelBranding::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $secondHotel->id],
            [
                'primary_color' => '#1e1b4b',
                'accent_color' => '#10b981',
                'welcome_message' => 'Welcome to Royal Palms Kolkata. Experience authentic royal hospitality.',
            ]
        );

        $secondHotel->users()->syncWithoutDetaching([
            $admin->id => ['status' => 'active', 'is_owner' => true],
            $chef->id => ['status' => 'active', 'is_owner' => false],
        ]);

        $secondAdminRole = Role::firstOrCreate(
            ['hotel_id' => $secondHotel->id, 'name' => 'HOTEL_ADMIN'],
            ['label' => 'Hotel Admin', 'scope' => 'hotel']
        );
        $secondAdminRole->permissions()->sync(Permission::pluck('id'));
        $admin->roles()->syncWithoutDetaching([$secondAdminRole->id => ['hotel_id' => $secondHotel->id]]);

        $secondChefRole = Role::firstOrCreate(
            ['hotel_id' => $secondHotel->id, 'name' => 'EXECUTIVE_CHEF'],
            ['label' => 'Executive Chef / F&B Director', 'scope' => 'restaurant']
        );
        $secondChefRole->permissions()->sync(
            Permission::whereIn('name', ['orders.view', 'orders.update', 'menu.manage', 'notifications.view'])->pluck('id')
        );
        $chef->roles()->syncWithoutDetaching([$secondChefRole->id => ['hotel_id' => $secondHotel->id]]);

        $secondRestaurant = Restaurant::withoutGlobalScopes()->firstOrCreate(
            ['hotel_id' => $secondHotel->id, 'name' => 'The Saffron Pavilion'],
            [
                'description' => 'Royal Indian cuisine, heritage kebabs and artisanal biryanis.',
                'tax_rate' => 5.00,
                'currency' => 'INR',
                'opens_at' => '12:00:00',
                'closes_at' => '23:00:00',
                'is_active' => true,
            ]
        );

        $secondRestaurant->users()->syncWithoutDetaching([
            $chef->id => ['role' => 'chef', 'status' => 'active']
        ]);
    }
}

