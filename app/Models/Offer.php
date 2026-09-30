<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class Offer extends Model{use BelongsToTenant;protected $fillable=['hotel_id','title','description','price','start_at','expires_at','is_active'];protected function casts():array{return ['price'=>'decimal:2','start_at'=>'datetime','expires_at'=>'datetime','is_active'=>'boolean'];}}
