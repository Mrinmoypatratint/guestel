<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Role extends Model {
 protected $fillable=['hotel_id','name','label','scope'];
 public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class,'role_permissions')->withTimestamps(); }
 public function users(): BelongsToMany { return $this->belongsToMany(User::class,'user_roles')->withPivot('hotel_id')->withTimestamps(); }
}
