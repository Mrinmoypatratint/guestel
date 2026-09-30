<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class HotelPolicy extends Model
{
    use BelongsToTenant;

    protected $fillable = ['hotel_id', 'title', 'content', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
