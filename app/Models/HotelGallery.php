<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class HotelGallery extends Model{use BelongsToTenant;protected $fillable=['hotel_id','category','path','alt_text','sort_order','is_cover'];protected function casts():array{return ['is_cover'=>'boolean'];}}
