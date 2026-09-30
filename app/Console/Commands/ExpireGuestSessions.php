<?php
namespace App\Console\Commands;use App\Models\GuestSession;use Illuminate\Console\Command;
class ExpireGuestSessions extends Command{protected $signature='hospitality:expire-guest-sessions';protected $description='Expire guest sessions whose TTL has elapsed.';public function handle():int{$count=GuestSession::withoutGlobalScopes()->where('status','active')->where('expires_at','<=',now())->update(['status'=>'expired','updated_at'=>now()]);$this->info("Expired {$count} guest sessions.");return self::SUCCESS;}}
