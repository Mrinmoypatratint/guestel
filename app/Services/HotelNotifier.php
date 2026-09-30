<?php
namespace App\Services;use App\Models\User;use Illuminate\Notifications\Notification;
class HotelNotifier{public function permission(int $hotelId,string $permission,Notification $notification):void{$users=User::query()->where('is_active',true)->whereHas('hotels',fn($q)=>$q->where('hotels.id',$hotelId)->where('hotel_users.status','active'))->get();foreach($users as $user)if($user->hasPermission($permission,$hotelId))$user->notify($notification);}}
