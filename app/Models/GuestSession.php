<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestSession extends Model
{
    use BelongsToTenant;

    protected $fillable = ['hotel_id', 'room_id', 'qr_code_id', 'public_id', 'browser_session_hash', 'expires_at', 'last_seen_at', 'status'];

    protected $hidden = ['id', 'browser_session_hash'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'last_seen_at' => 'datetime'];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
