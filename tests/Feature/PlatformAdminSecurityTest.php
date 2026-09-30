<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformAdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_platform_admin_is_denied_from_platform_portal(): void
    {
        $hotel = Hotel::create(['name' => 'Resort A', 'slug' => 'resort-a', 'status' => 'active']);
        $user = User::factory()->create(['is_active' => true, 'is_platform_admin' => false]);
        $hotel->users()->attach($user->id, ['status' => 'active', 'is_owner' => true]);

        $this->actingAs($user)
            ->withSession([config('hospitality.tenant_session_key') => $hotel->id])
            ->get('/platform/hotels')
            ->assertForbidden();
    }

    public function test_platform_admin_can_access_platform_portal(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'is_platform_admin' => true]);

        $this->actingAs($admin)
            ->get('/platform/hotels')
            ->assertOk();
    }
}
