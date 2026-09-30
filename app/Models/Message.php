<?php
namespace App\Models;use App\Models\Concerns\BelongsToTenant;use Illuminate\Database\Eloquent\Model;class Message extends Model{use BelongsToTenant;protected $fillable=['hotel_id','conversation_id','user_id','guest_session_id','sender_type','body','read_at'];protected function casts():array{return ['read_at'=>'datetime'];}}
