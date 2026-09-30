<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelSubscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'fee' => 'decimal:2',
        'starts_at' => 'datetime',
        'renews_at' => 'datetime',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
