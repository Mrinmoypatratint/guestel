<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Floor extends Model { use BelongsToTenant; protected $fillable=['hotel_id','name','number','sort_order']; public function rooms(): HasMany { return $this->hasMany(Room::class); } }
