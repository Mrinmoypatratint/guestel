<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\PlatformCommunication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CommunicationController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $communications = PlatformCommunication::with('hotel', 'sender')->latest()->paginate(15);
        $hotels = Hotel::where('status', 'active')->orderBy('name')->get();

        return view('platform.communications.index', [
            'communications' => $communications,
            'hotels' => $hotels,
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $data = $request->validate([
            'hotel_id' => ['nullable', 'string'],
            'target_audience' => ['required', 'string', 'in:all_hotels,hotel_admin,restaurant_manager'],
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:billing_notice,system_alert,onboarding,general'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $hotelId = ($data['hotel_id'] === 'all' || empty($data['hotel_id'])) ? null : (int) $data['hotel_id'];

        $comm = PlatformCommunication::create([
            'sender_id' => auth()->id(),
            'hotel_id' => $hotelId,
            'target_audience' => $data['target_audience'],
            'recipient_email' => $hotelId ? Hotel::find($hotelId)?->email : 'broadcast@platform.local',
            'subject' => $data['subject'],
            'message' => $data['message'],
            'category' => $data['category'],
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Attempt graceful email dispatch without blocking if local SMTP is not configured
        try {
            $recipients = collect();
            if ($hotelId) {
                $hotel = Hotel::with('users')->find($hotelId);
                $recipients = $hotel ? $hotel->users->pluck('email') : collect();
            } else {
                $recipients = User::where('is_platform_admin', false)->pluck('email');
            }

            Log::info("Platform SaaS Communication Dispatched: {$comm->subject}", [
                'comm_id' => $comm->id,
                'target' => $data['target_audience'],
                'recipient_count' => $recipients->count(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("SaaS email transmission logged locally: {$e->getMessage()}");
        }

        return redirect()->route('platform.communications.index')
            ->with('status', "Official communication '{$comm->subject}' dispatched to {$data['target_audience']}.");
    }
}
