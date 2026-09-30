<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Room;
use App\Services\QrRenderService;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class QrCenterController extends Controller
{
    public function index(TenantContext $tenant)
    {
        $hotelId = $tenant->requireId();

        $qrCodes = QrCode::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->with(['qrable', 'scans'])
            ->withCount('scans')
            ->latest()
            ->paginate(30);

        return view('admin.qr-center.index', [
            'hotel' => $tenant->hotel(),
            'qrCodes' => $qrCodes,
            'totalQrs' => QrCode::withoutGlobalScopes()->where('hotel_id', $hotelId)->count(),
            'activeQrs' => QrCode::withoutGlobalScopes()->where('hotel_id', $hotelId)->where('is_active', true)->count(),
        ]);
    }

    public function printSheet(Request $request, TenantContext $tenant, QrRenderService $renderer)
    {
        $hotel = $tenant->hotel();
        $hotelId = $tenant->requireId();

        $roomId = $request->query('room_id');
        $query = QrCode::withoutGlobalScopes()
            ->where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->with(['qrable']);

        if ($roomId) {
            $query->where('qrable_type', Room::class)->where('qrable_id', $roomId);
        }

        $qrCodes = $query->get();

        $cards = $qrCodes->map(function ($qr) use ($renderer) {
            $url = url('/' . config('hospitality.qr_public_prefix', 'g') . '/' . $qr->public_token);
            return [
                'qr' => $qr,
                'svg' => $renderer->svg($url),
                'url' => $url,
                'label' => $qr->label ?? ($qr->qrable instanceof Room ? 'Room ' . $qr->qrable->number : 'Hotel QR'),
                'room' => $qr->qrable instanceof Room ? $qr->qrable : null,
            ];
        });

        return view('admin.qr-center.print-sheet', [
            'hotel' => $hotel,
            'cards' => $cards,
        ]);
    }

    public function toggle(QrCode $qr, TenantContext $tenant)
    {
        abort_unless((int) $qr->hotel_id === $tenant->requireId(), 403);
        $qr->update(['is_active' => !$qr->is_active]);

        return back()->with('status', 'QR code status updated to ' . ($qr->is_active ? 'Active' : 'Inactive') . '.');
    }
}
