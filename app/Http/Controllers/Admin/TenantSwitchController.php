<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class TenantSwitchController extends Controller {
    public function __invoke(Request $request, int $hotel) {
        $user = $request->user();
        abort_unless($user && ($user->is_platform_admin || $user->belongsToHotel($hotel)), 403, 'Unauthorized hotel switch.');
        $request->session()->put(config('hospitality.tenant_session_key', 'current_hotel_id'), $hotel);
        return redirect($user->workspaceRoute($hotel))->with('status', 'Switched hotel context.');
    }
}
