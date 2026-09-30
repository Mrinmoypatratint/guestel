<?php
namespace App\Http\Middleware;
use App\Models\Hotel;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class ResolveTenant {
 public function __construct(private TenantContext $context) {}
 public function handle(Request $request, Closure $next): Response {
   $user=$request->user(); abort_unless($user && $user->is_active,403);
   $key=config('hospitality.tenant_session_key','current_hotel_id');
   $hotelId=(int) $request->session()->get($key,0);
   if (!$hotelId) {
       $hotelId=(int) ($user->hotels()->wherePivot('status','active')->value('hotels.id') ?? ($user->is_platform_admin ? Hotel::where('status','active')->value('id') : 0));
       if($hotelId) $request->session()->put($key,$hotelId);
   }
   abort_unless($hotelId && ($user->is_platform_admin || $user->belongsToHotel($hotelId)),403,'No authorized hotel context.');
   $hotel=Hotel::query()->whereKey($hotelId)->where('status','active')->firstOrFail();
   $this->context->set($hotel);
   try { return $next($request); } finally { $this->context->clear(); }
 }
}
