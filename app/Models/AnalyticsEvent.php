<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class AnalyticsEvent extends Model{public $timestamps=false;protected $fillable=['hotel_id','guest_session_id','event_name','entity_type','entity_id','properties','created_at'];protected function casts():array{return ['properties'=>'array','created_at'=>'datetime'];}}
