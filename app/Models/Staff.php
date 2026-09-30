<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class Staff extends Model{use BelongsToTenant;protected $table='staff';protected $fillable=['hotel_id','user_id','department_id','employee_code','name','phone','status'];}
