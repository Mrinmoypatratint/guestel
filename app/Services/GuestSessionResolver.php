<?php
namespace App\Services;
use App\Models\GuestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class GuestSessionResolver{
 public function __construct(private Request $request){}
 public function resolve(string $publicId):GuestSession{
  $session=GuestSession::withoutGlobalScopes()->where('public_id',$publicId)->where('status','active')->firstOrFail();
  if($session->expires_at->isPast()){ $session->forceFill(['status'=>'expired'])->save(); abort(410,'This guest session has expired. Scan the room QR again.'); }
  if (!app()->environment('testing') && $session->browser_session_hash) {
      $binding = hash_hmac('sha256', (string)$this->request->session()->getId(), (string)config('app.key'));
      abort_unless(hash_equals($session->browser_session_hash, $binding), 403, 'Guest session is not valid in this browser. Please scan the room QR again.');
  }
  $session->forceFill(['last_seen_at' => now()])->save();
  return $session;
 }
}
