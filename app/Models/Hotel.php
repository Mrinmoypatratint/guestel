<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
class Hotel extends Model {
 use HasFactory, SoftDeletes;
 protected $fillable=['name','slug','email','phone','timezone','status','address_line1','address_line2','city','state','country','postal_code'];
 public function branding(): HasOne { return $this->hasOne(HotelBranding::class); }
 public function users(): BelongsToMany { return $this->belongsToMany(User::class,'hotel_users')->withPivot(['is_owner','status'])->withTimestamps(); }
 public function floors(): HasMany { return $this->hasMany(Floor::class); }
 public function rooms(): HasMany { return $this->hasMany(Room::class); }
 public function qrCodes(): HasMany { return $this->hasMany(QrCode::class); }
 public function subscriptions(): HasMany { return $this->hasMany(HotelSubscription::class); }
 public function currentSubscription(): HasOne { return $this->hasOne(HotelSubscription::class)->latestOfMany(); }
 public function invoices(): HasMany { return $this->hasMany(HotelInvoice::class); }
 public function communications(): HasMany { return $this->hasMany(PlatformCommunication::class); }
}
