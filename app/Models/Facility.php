<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class Facility extends Model{use BelongsToTenant;protected $fillable=['hotel_id','name','description','location','hours','is_active'];protected function casts():array{return ['is_active'=>'boolean'];}}
