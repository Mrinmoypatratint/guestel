<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
class RoomType extends Model { use BelongsToTenant; protected $fillable=['hotel_id','name','description','capacity','base_rate','is_active']; protected function casts(): array{return ['base_rate'=>'decimal:2','is_active'=>'boolean'];} }
