<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
class QrScan extends Model { use BelongsToTenant; protected $fillable=['hotel_id','qr_code_id','room_id','anonymous_session_id','user_agent','referrer','ip_hash','scanned_at']; protected function casts(): array{return ['scanned_at'=>'datetime'];} }
