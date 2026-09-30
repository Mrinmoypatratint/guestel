<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class MenuModifier extends Model{use BelongsToTenant;protected $fillable=['hotel_id','menu_item_id','name','price_delta','is_available'];protected function casts():array{return ['price_delta'=>'decimal:2','is_available'=>'boolean'];}}
