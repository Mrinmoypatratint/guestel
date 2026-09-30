<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class Announcement extends Model{use BelongsToTenant;protected $fillable=['hotel_id','title','body','priority','audience','start_at','expires_at','is_active'];protected function casts():array{return ['start_at'=>'datetime','expires_at'=>'datetime','is_active'=>'boolean'];}}
