<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_only_returns_records_belonging_to_current_tenant(): void
    {
        $this->seed(PermissionSeeder::class);

        $hotelA = Hotel::create(['name' => 'Azure Hotel', 'slug' => 'azure-hotel', 'status' => 'active']);
        $hotelB = Hotel::create(['name' => 'Emerald Resort', 'slug' => 'emerald-resort', 'status' => 'active']);

        $typeA = RoomType::withoutGlobalScopes()->create([
            'hotel_id' => $hotelA->id,
            'name' => 'Deluxe Suite A',
            'capacity' => 2,
            'base_rate' => 150,
            'is_active' => true,
        ]);

        $typeB = RoomType::withoutGlobalScopes()->create([
            'hotel_id' => $hotelB->id,
            'name' => 'Deluxe Suite B',
            'capacity' => 2,
            'base_rate' => 150,
            'is_active' => true,
        ]);

        $roomA = Room::withoutGlobalScopes()->create([
            'hotel_id' => $hotelA->id,
            'room_type_id' => $typeA->id,
            'number' => '999',
            'status' => 'available',
            'is_active' => true,
        ]);

        $roomB = Room::withoutGlobalScopes()->create([
            'hotel_id' => $hotelB->id,
            'room_type_id' => $typeB->id,
            'number' => '999',
            'status' => 'available',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['is_active' => true]);
        $hotelA->users()->attach($user->id, ['status' => 'active', 'is_owner' => true]);

        $role = Role::create(['hotel_id' => $hotelA->id, 'name' => 'ADMIN', 'label' => 'Admin', 'scope' => 'hotel']);
        $role->permissions()->attach(Permission::where('name', 'hotel.view')->firstOrFail());
        $user->roles()->attach($role->id, ['hotel_id' => $hotelA->id]);

        $response = $this->actingAs($user)
            ->withSession([config('hospitality.tenant_session_key') => $hotelA->id])
            ->getJson('/admin/search?q=999')
            ->assertOk();

        $results = $response->json('results');
        $this->assertCount(1, $results);
        $this->assertEquals("Room {$roomA->number}", $results[0]['title']);
        $this->assertStringContainsString("/admin/rooms/{$roomA->id}", $results[0]['url']);
    }
}
