<?php
namespace App\Policies;
use App\Models\Room;
use App\Models\User;
use App\Support\TenantContext;
class RoomPolicy {
 public function __construct(private TenantContext $context){}
 public function viewAny(User $user):bool{return $user->hasPermission('rooms.view',$this->context->id());}
 public function view(User $user,Room $room):bool{return (int)$room->hotel_id===$this->context->id() && $user->hasPermission('rooms.view',$this->context->id());}
 public function create(User $user):bool{return $user->hasPermission('rooms.create',$this->context->id());}
 public function update(User $user,Room $room):bool{return (int)$room->hotel_id===$this->context->id() && $user->hasPermission('rooms.update',$this->context->id());}
 public function delete(User $user,Room $room):bool{return (int)$room->hotel_id===$this->context->id() && $user->hasPermission('rooms.delete',$this->context->id());}
}
