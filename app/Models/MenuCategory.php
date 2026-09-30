<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class MenuCategory extends Model{use BelongsToTenant;protected $fillable=['hotel_id','restaurant_id','name','sort_order','is_active'];protected function casts():array{return ['is_active'=>'boolean'];}}
