<?php
namespace App\Http\Controllers\Guest;
use App\Http\Controllers\Controller;
use App\Models\{GuestSession,QrCode,QrScan,Room};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class QrController extends Controller {
 public function show(Request $request,string $token){
   $qr=QrCode::withoutGlobalScopes()->where('public_token',$token)->where('is_active',true)->firstOrFail();
   $room=$qr->qrable_type===Room::class?Room::withoutGlobalScopes()->whereKey($qr->qrable_id)->where('hotel_id',$qr->hotel_id)->where('is_active',true)->firstOrFail():null;
   $anonymous=$request->session()->get('anonymous_guest_id')??(string)Str::uuid();$request->session()->put('anonymous_guest_id',$anonymous);
   $session=GuestSession::withoutGlobalScopes()->create(['hotel_id'=>$qr->hotel_id,'room_id'=>$room?->id,'qr_code_id'=>$qr->id,'public_id'=>Str::lower(Str::random(48)),'browser_session_hash'=>hash_hmac('sha256',$request->session()->getId(),config('app.key')),'expires_at'=>now()->addHours(config('hospitality.guest_session_hours',24)),'last_seen_at'=>now(),'status'=>'active']);
   QrScan::withoutGlobalScopes()->create(['hotel_id'=>$qr->hotel_id,'qr_code_id'=>$qr->id,'room_id'=>$room?->id,'anonymous_session_id'=>$anonymous,'user_agent'=>Str::limit((string)$request->userAgent(),500,''),'referrer'=>Str::limit((string)$request->headers->get('referer'),500,''),'ip_hash'=>hash_hmac('sha256',(string)$request->ip(),config('app.key')),'scanned_at'=>now()]);
   $qr->forceFill(['last_scanned_at'=>now()])->save();
   $hotel=\App\Models\Hotel::with('branding')->findOrFail($qr->hotel_id);
   return redirect()->route('guest.stay',['session'=>$session->public_id]);
 }
}
