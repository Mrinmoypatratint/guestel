<?php
namespace App\Models;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
class HotelBranding extends Model { use BelongsToTenant; protected $fillable=['hotel_id','logo_path','cover_image_path','primary_color','accent_color','welcome_message','font_family']; }
