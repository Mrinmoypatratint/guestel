<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class QrCode extends Model {
 use BelongsToTenant;
 protected $fillable=['hotel_id','public_token','qrable_type','qrable_id','label','is_active','last_scanned_at'];
 protected $hidden=['id','qrable_id'];
 protected function casts(): array{return ['is_active'=>'boolean','last_scanned_at'=>'datetime'];}
 public function qrable(): MorphTo{return $this->morphTo();}
 public function scans(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(QrScan::class); }
}
