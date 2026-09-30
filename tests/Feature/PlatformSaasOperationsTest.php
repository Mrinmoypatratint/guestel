<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\HotelInvoice;
use App\Models\HotelSubscription;
use App\Models\PlatformCommunication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformSaasOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $platformAdmin;
    protected User $hotelStaff;
    protected Hotel $hotel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->platformAdmin = User::factory()->create([
            'name' => 'Alexander Vance (Company Admin)',
            'email' => 'admin@example.com',
            'is_platform_admin' => true,
            'is_active' => true,
        ]);

        $this->hotel = Hotel::create([
            'name' => 'Grand Azure Resort',
            'slug' => 'grand-azure-resort',
            'status' => 'active',
            'email' => 'contact@grandazure.com',
            'phone' => '+91 98765 43210',
            'city' => 'Goa',
            'country' => 'India',
        ]);

        $this->hotelStaff = User::factory()->create([
            'name' => 'Staff Member',
            'is_platform_admin' => false,
            'is_active' => true,
        ]);
        $this->hotel->users()->attach($this->hotelStaff->id, ['status' => 'active', 'is_owner' => false]);
    }

    public function test_platform_admin_can_access_saas_dashboard_with_inr_kpis(): void
    {
        // Add paid invoice
        HotelInvoice::create([
            'invoice_number' => 'INV-202609-TEST01',
            'hotel_id' => $this->hotel->id,
            'title' => 'Monthly Cloud License',
            'subtotal' => 10000.00,
            'tax_amount' => 1800.00,
            'total_amount' => 11800.00,
            'currency' => 'INR',
            'status' => 'paid',
            'due_date' => now()->addDays(10),
            'paid_at' => now(),
            'payment_method' => 'UPI',
        ]);

        $response = $this->actingAs($this->platformAdmin)->get(route('platform.dashboard'));

        $response->assertOk();
        $response->assertSee('Platform Company Administration');
        $response->assertSee('₹11,800.00');
    }

    public function test_platform_admin_can_onboard_new_hotel_with_subscription(): void
    {
        $payload = [
            'name' => 'Royal Heritage Palace',
            'slug' => 'royal-heritage-palace',
            'city' => 'Jaipur',
            'state' => 'RJ',
            'country' => 'India',
            'email' => 'gm@royalheritage.com',
            'phone' => '+91 99887 76655',
            'admin_name' => 'Vikram Rathore',
            'admin_email' => 'vikram@royalheritage.com',
            'plan_name' => 'Enterprise Cloud',
            'plan_fee' => 19999.00,
        ];

        $response = $this->actingAs($this->platformAdmin)->post(route('platform.hotels.store'), $payload);

        $response->assertRedirect(route('platform.hotels.index'));
        $this->assertDatabaseHas('hotels', ['slug' => 'royal-heritage-palace']);
        $newHotel = Hotel::where('slug', 'royal-heritage-palace')->first();

        // Verify initial subscription was created
        $this->assertDatabaseHas('hotel_subscriptions', [
            'hotel_id' => $newHotel->id,
            'plan_name' => 'Enterprise Cloud',
            'fee' => 19999.00,
            'currency' => 'INR',
        ]);

        // Verify proforma onboarding invoice was created with 18% GST
        $this->assertDatabaseHas('hotel_invoices', [
            'hotel_id' => $newHotel->id,
            'subtotal' => 19999.00,
            'tax_amount' => 3599.82,
            'total_amount' => 23598.82,
            'currency' => 'INR',
            'status' => 'unpaid',
        ]);
    }

    public function test_platform_admin_can_generate_service_bill_and_record_payment(): void
    {
        // 1. Generate service bill
        $billResponse = $this->actingAs($this->platformAdmin)->post(route('platform.billing.invoices.store'), [
            'hotel_id' => $this->hotel->id,
            'title' => 'Quarterly Support & Cloud Infrastructure Fee',
            'subtotal' => 15000.00,
            'tax_percent' => 18,
            'due_date' => now()->addDays(15)->format('Y-m-d'),
            'notes' => 'Quarterly advance service fee.',
        ]);

        $billResponse->assertRedirect(route('platform.billing.index'));
        
        $invoice = HotelInvoice::where('hotel_id', $this->hotel->id)->latest()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(15000.00, (float)$invoice->subtotal);
        $this->assertEquals(2700.00, (float)$invoice->tax_amount);
        $this->assertEquals(17700.00, (float)$invoice->total_amount);
        $this->assertEquals('unpaid', $invoice->status);

        // 2. View official tax invoice
        $viewResponse = $this->actingAs($this->platformAdmin)->get(route('platform.billing.invoices.show', $invoice));
        $viewResponse->assertOk();
        $viewResponse->assertSee($invoice->invoice_number);
        $viewResponse->assertSee('₹17,700.00');

        // 3. Record payment for the service bill
        $payResponse = $this->actingAs($this->platformAdmin)->post(route('platform.billing.invoices.payment', $invoice), [
            'payment_method' => 'NEFT / RTGS / Bank Transfer',
            'payment_reference' => 'UTR-HDFC-9021849182',
            'notes' => 'Received via corporate banking transfer.',
        ]);

        $payResponse->assertRedirect(route('platform.billing.index'));
        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals('NEFT / RTGS / Bank Transfer', $invoice->payment_method);
        $this->assertEquals('UTR-HDFC-9021849182', $invoice->payment_reference);
        $this->assertNotNull($invoice->paid_at);
    }

    public function test_platform_admin_can_dispatch_communication_to_restaurant_and_hotel(): void
    {
        $response = $this->actingAs($this->platformAdmin)->post(route('platform.communications.send'), [
            'hotel_id' => (string) $this->hotel->id,
            'target_audience' => 'restaurant_manager',
            'category' => 'system_alert',
            'subject' => 'Kitchen Display System Software Upgrade Notice',
            'message' => 'Please note the F&B kitchen tablet app will undergo a minor maintenance upgrade tonight at 11:30 PM.',
        ]);

        $response->assertRedirect(route('platform.communications.index'));
        $this->assertDatabaseHas('platform_communications', [
            'hotel_id' => $this->hotel->id,
            'target_audience' => 'restaurant_manager',
            'category' => 'system_alert',
            'subject' => 'Kitchen Display System Software Upgrade Notice',
            'status' => 'sent',
        ]);
    }

    public function test_hotel_staff_cannot_access_platform_billing_or_onboarding(): void
    {
        $this->actingAs($this->hotelStaff)
            ->withSession([config('hospitality.tenant_session_key') => $this->hotel->id])
            ->get(route('platform.billing.index'))
            ->assertForbidden();

        $this->actingAs($this->hotelStaff)
            ->withSession([config('hospitality.tenant_session_key') => $this->hotel->id])
            ->get(route('platform.communications.index'))
            ->assertForbidden();

        $this->actingAs($this->hotelStaff)
            ->withSession([config('hospitality.tenant_session_key') => $this->hotel->id])
            ->get(route('platform.dashboard'))
            ->assertForbidden();
    }
}
