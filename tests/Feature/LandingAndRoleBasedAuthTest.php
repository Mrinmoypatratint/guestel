<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Permission;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandingAndRoleBasedAuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $companyAdmin;
    protected User $hotelAdmin;
    protected User $housekeeper;
    protected User $chef;
    protected Hotel $hotelA;
    protected Hotel $hotelB;
    protected Restaurant $restaurantA;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed core permissions
        $permissions = [
            'hotel.view', 'hotel.update', 'rooms.view', 'rooms.create', 'rooms.update',
            'requests.view', 'requests.assign', 'requests.update',
            'orders.view', 'orders.update', 'menu.manage', 'notifications.view'
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['label' => ucwords(str_replace('.', ' ', $perm))]);
        }

        // Hotels
        $this->hotelA = Hotel::create([
            'name' => 'Grand Azure Resort',
            'slug' => 'grand-azure-resort',
            'city' => 'Goa',
            'status' => 'active',
        ]);

        $this->hotelB = Hotel::create([
            'name' => 'Royal Palms Luxury Resort',
            'slug' => 'royal-palms-resort',
            'city' => 'Kolkata',
            'status' => 'active',
        ]);

        $this->restaurantA = Restaurant::create([
            'hotel_id' => $this->hotelA->id,
            'name' => 'The Azure Grill',
            'is_active' => true,
        ]);

        // 1. Company Admin
        $this->companyAdmin = User::create([
            'name' => 'Alexander Vance (Company Admin)',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin12345!'),
            'is_platform_admin' => true,
            'is_active' => true,
        ]);

        // 2. Hotel Admin with multiple properties
        $this->hotelAdmin = User::create([
            'name' => 'Multi-Hotel General Manager',
            'email' => 'gm@hospitalitygroup.com',
            'password' => Hash::make('Admin12345!'),
            'is_platform_admin' => false,
            'is_active' => true,
        ]);
        $this->hotelA->users()->attach($this->hotelAdmin->id, ['status' => 'active', 'is_owner' => true]);
        $this->hotelB->users()->attach($this->hotelAdmin->id, ['status' => 'active', 'is_owner' => true]);

        $adminRoleA = Role::create(['hotel_id' => $this->hotelA->id, 'name' => 'HOTEL_ADMIN', 'label' => 'Hotel Admin']);
        $adminRoleA->permissions()->sync(Permission::pluck('id'));
        $this->hotelAdmin->roles()->attach($adminRoleA->id, ['hotel_id' => $this->hotelA->id]);

        $adminRoleB = Role::create(['hotel_id' => $this->hotelB->id, 'name' => 'HOTEL_ADMIN', 'label' => 'Hotel Admin']);
        $adminRoleB->permissions()->sync(Permission::pluck('id'));
        $this->hotelAdmin->roles()->attach($adminRoleB->id, ['hotel_id' => $this->hotelB->id]);

        // 3. Housekeeper
        $this->housekeeper = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@grandazure.com',
            'password' => Hash::make('Admin12345!'),
            'is_platform_admin' => false,
            'is_active' => true,
        ]);
        $this->hotelA->users()->attach($this->housekeeper->id, ['status' => 'active', 'is_owner' => false]);
        $hkRole = Role::create(['hotel_id' => $this->hotelA->id, 'name' => 'HOUSEKEEPING_LEAD', 'label' => 'Housekeeping Lead']);
        $hkRole->permissions()->sync(Permission::whereIn('name', ['rooms.view', 'rooms.update', 'requests.view', 'requests.update', 'notifications.view'])->pluck('id'));
        $this->housekeeper->roles()->attach($hkRole->id, ['hotel_id' => $this->hotelA->id]);

        // 4. Chef
        $this->chef = User::create([
            'name' => 'Chef Marcus Drake',
            'email' => 'chef.marcus@grandazure.com',
            'password' => Hash::make('Admin12345!'),
            'is_platform_admin' => false,
            'is_active' => true,
        ]);
        $this->hotelA->users()->attach($this->chef->id, ['status' => 'active', 'is_owner' => false]);
        $this->restaurantA->users()->attach($this->chef->id, ['role' => 'chef', 'status' => 'active']);
        $chefRole = Role::create(['hotel_id' => $this->hotelA->id, 'name' => 'EXECUTIVE_CHEF', 'label' => 'Executive Chef']);
        $chefRole->permissions()->sync(Permission::whereIn('name', ['orders.view', 'orders.update', 'menu.manage', 'notifications.view'])->pluck('id'));
        $this->chef->roles()->attach($chefRole->id, ['hotel_id' => $this->hotelA->id]);
    }

    public function test_public_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Hospitality,');
        $response->assertSee('beautifully connected.');
        $response->assertSee('Explore Platform');
        $response->assertSee('Sign In');
        $response->assertSee('Live Operations Stream');
    }

    public function test_tc_auth_001_company_admin_login_redirects_to_platform(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'Admin12345!',
        ]);

        $response->assertRedirect(route('platform.dashboard'));
    }

    public function test_tc_auth_002_multi_property_hotel_admin_redirects_to_property_selector(): void
    {
        $response = $this->post('/login', [
            'email' => 'gm@hospitalitygroup.com',
            'password' => 'Admin12345!',
        ]);

        // Redirects to property selection because user has 2 authorized hotels
        $response->assertRedirect(route('property.select'));

        // Follow to selector page
        $selectorResponse = $this->actingAs($this->hotelAdmin)->get(route('property.select'));
        $selectorResponse->assertOk();
        $selectorResponse->assertSee('Where are you working today?');
        $selectorResponse->assertSee('Grand Azure Resort');
        $selectorResponse->assertSee('Royal Palms Luxury Resort');

        // Select Grand Azure Resort
        $switchResponse = $this->actingAs($this->hotelAdmin)->post(route('property.switch'), [
            'property_type' => 'hotel',
            'property_id' => $this->hotelA->id,
        ]);

        $switchResponse->assertRedirect(route('admin.dashboard'));
        $this->assertEquals($this->hotelA->id, session(config('hospitality.tenant_session_key')));
    }

    public function test_tc_auth_003_housekeeping_login_redirects_to_requests(): void
    {
        $response = $this->post('/login', [
            'email' => 'maria.santos@grandazure.com',
            'password' => 'Admin12345!',
        ]);

        // User belongs to 1 hotel -> direct workspace redirect to /admin/requests
        $response->assertRedirect(route('admin.requests.index'));
    }

    public function test_tc_auth_004_chef_login_redirects_to_orders(): void
    {
        $response = $this->post('/login', [
            'email' => 'chef.marcus@grandazure.com',
            'password' => 'Admin12345!',
        ]);

        // Chef role workspace -> direct redirect to /admin/orders
        $response->assertRedirect(route('admin.orders.index'));
    }

    public function test_tc_auth_005_housekeeper_cannot_access_platform_admin(): void
    {
        $this->actingAs($this->housekeeper)
            ->withSession([config('hospitality.tenant_session_key') => $this->hotelA->id])
            ->get(route('platform.dashboard'))
            ->assertForbidden();
    }

    public function test_tc_auth_006_housekeeper_without_hotel_view_cannot_access_general_admin_dashboard(): void
    {
        // Maria has requests.view and rooms.view, but not hotel.view
        $this->actingAs($this->housekeeper)
            ->withSession([config('hospitality.tenant_session_key') => $this->hotelA->id])
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_tc_auth_007_unauthorized_property_switch_is_forbidden(): void
    {
        // Another hotel Maria doesn't belong to
        $unauthorizedHotel = Hotel::create(['name' => 'Unauthorized Hotel', 'slug' => 'unauthorized-hotel', 'status' => 'active']);

        $response = $this->actingAs($this->housekeeper)->post(route('property.switch'), [
            'property_type' => 'hotel',
            'property_id' => $unauthorizedHotel->id,
        ]);

        $response->assertForbidden();
    }
}
