<?php

namespace App\Http\Controllers\Platform;

use App\Actions\CreateHotelAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateHotelRequest;
use App\Models\Hotel;
use App\Models\HotelInvoice;
use App\Models\HotelSubscription;
use App\Models\PlatformCommunication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $hotels = Hotel::with('currentSubscription')->latest()->paginate(15);
        $totalHotels = Hotel::count();
        $activeHotels = Hotel::where('status', 'active')->count();

        return view('platform.hotels.index', [
            'hotels' => $hotels,
            'totalHotels' => $totalHotels,
            'activeHotels' => $activeHotels,
        ]);
    }

    public function store(CreateHotelRequest $request, CreateHotelAction $action): RedirectResponse
    {
        $data = $request->validated();
        $hotel = $action->execute($data);

        // 1. Establish initial SaaS subscription
        $planName = $request->input('plan_name', 'Professional Cloud');
        $planFee = (float) ($request->input('plan_fee') ?: 9999.00);

        $subscription = HotelSubscription::create([
            'hotel_id' => $hotel->id,
            'plan_name' => $planName,
            'billing_cycle' => 'monthly',
            'fee' => $planFee,
            'currency' => 'INR',
            'status' => 'active',
            'starts_at' => now(),
            'renews_at' => now()->addMonth(),
        ]);

        // 2. Generate initial onboarding proforma invoice
        $tax = round($planFee * 0.18, 2);
        HotelInvoice::create([
            'invoice_number' => 'INV-' . now()->format('Ym') . '-' . strtoupper(Str::random(6)),
            'hotel_id' => $hotel->id,
            'title' => "Initial Setup & {$planName} Subscription",
            'subtotal' => $planFee,
            'tax_amount' => $tax,
            'total_amount' => $planFee + $tax,
            'currency' => 'INR',
            'status' => 'unpaid',
            'due_date' => now()->addDays(7),
            'notes' => 'Onboarding SaaS invoice. Due within 7 days of deployment.',
        ]);

        // 3. Log onboarding welcome communication
        PlatformCommunication::create([
            'sender_id' => auth()->id(),
            'hotel_id' => $hotel->id,
            'target_audience' => 'hotel_admin',
            'recipient_email' => $data['admin_email'],
            'subject' => "Welcome to Hospitality Cloud · {$hotel->name} Provisioned",
            'message' => "Congratulations! Your hotel tenant account for '{$hotel->name}' has been provisioned on the SaaS Hospitality Platform. Your administrative credentials and activation details have been routed.",
            'category' => 'onboarding',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        try {
            Password::sendResetLink(['email' => $data['admin_email']]);
        } catch (\Throwable $e) {
            Log::warning('Hotel admin onboarding reset email failed', ['hotel_id' => $hotel->id, 'exception' => $e::class]);
        }

        return redirect()->route('platform.hotels.index')
            ->with('status', "{$hotel->name} onboarded successfully with {$planName} (₹" . number_format($planFee, 2) . "/mo). Initial bill generated.");
    }

    public function toggleStatus(Hotel $hotel): RedirectResponse
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $newStatus = $hotel->status === 'active' ? 'suspended' : 'active';
        $hotel->update(['status' => $newStatus]);

        if ($hotel->currentSubscription) {
            $hotel->currentSubscription->update(['status' => $newStatus]);
        }

        return redirect()->route('platform.hotels.index')
            ->with('status', "Property {$hotel->name} status updated to " . strtoupper($newStatus) . ".");
    }
}
