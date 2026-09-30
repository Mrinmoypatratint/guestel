<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
class Room extends Model {
 use BelongsToTenant, SoftDeletes;
 protected $fillable=['hotel_id','floor_id','room_type_id','number','name','status','notes','is_active'];
 protected function casts(): array{return ['is_active'=>'boolean'];}
 public function floor(): BelongsTo{return $this->belongsTo(Floor::class);}
 public function roomType(): BelongsTo{return $this->belongsTo(RoomType::class);}
 public function qrCode(): MorphOne{return $this->morphOne(QrCode::class,'qrable');}
 public function serviceRequests(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(ServiceRequest::class); }
}
