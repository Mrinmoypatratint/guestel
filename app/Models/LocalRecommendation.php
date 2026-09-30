<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LocalRecommendation extends Model
{
    use BelongsToTenant;

    protected $fillable = ['hotel_id', 'category', 'name', 'description', 'address', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
