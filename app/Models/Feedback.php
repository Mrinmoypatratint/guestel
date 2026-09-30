<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class Feedback extends Model{use BelongsToTenant;protected $table='feedback';protected $fillable=['hotel_id','room_id','guest_session_id','subject_type','subject_id','rating','comment'];}
