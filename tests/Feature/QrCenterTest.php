<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Permission;
use App\Models\QrCode;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_qr_center_and_print_sheet(): void
    {
        $this->seed(PermissionSeeder::class);

        $hotel = Hotel::create(['name' => 'The Palm', 'slug' => 'the-palm', 'status' => 'active']);
        $user = User::factory()->create(['is_active' => true]);
        $hotel->users()->attach($user->id, ['status' => 'active', 'is_owner' => true]);

        $role = Role::create(['hotel_id' => $hotel->id, 'name' => 'ADMIN', 'label' => 'Admin', 'scope' => 'hotel']);
        $role->permissions()->attach(Permission::where('name', 'qr.manage')->firstOrFail());
        $user->roles()->attach($role->id, ['hotel_id' => $hotel->id]);

        $qr = QrCode::withoutGlobalScopes()->create([
            'hotel_id' => $hotel->id,
            'public_token' => 'test-token-123456789012345678901234567890',
            'qrable_type' => Hotel::class,
            'qrable_id' => $hotel->id,
            'label' => 'Hotel QR',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession([config('hospitality.tenant_session_key') => $hotel->id])
            ->get('/admin/qr-center')
            ->assertOk()
            ->assertSeeText('QR Code Management & Print Center');

        $this->actingAs($user)
            ->withSession([config('hospitality.tenant_session_key') => $hotel->id])
            ->get('/admin/qr-center/print')
            ->assertOk()
            ->assertSeeText('Printable Guestroom Tent Cards');

        $this->actingAs($user)
            ->withSession([config('hospitality.tenant_session_key') => $hotel->id])
            ->post("/admin/qr-center/{$qr->id}/toggle")
            ->assertRedirect();

        $this->assertFalse($qr->fresh()->is_active);
    }
}
