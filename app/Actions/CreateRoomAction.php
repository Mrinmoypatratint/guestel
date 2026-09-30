<?php
namespace App\Actions;
use App\Models\Room;
use App\Support\TenantContext;
use Illuminate\Support\Facades\DB;
class CreateRoomAction {
 public function __construct(private TenantContext $tenant, private GenerateQrCodeAction $generateQr){}
 public function execute(array $data): Room {
  return DB::transaction(function() use($data){
   $room=Room::create(['floor_id'=>$data['floor_id']??null,'room_type_id'=>$data['room_type_id'],'number'=>$data['number'],'name'=>$data['name']??null,'status'=>$data['status']??'available','notes'=>$data['notes']??null,'is_active'=>true]);
   $this->generateQr->execute($this->tenant->requireId(),$room,'Room '.$room->number);
   return $room->fresh(['qrCode','roomType','floor']);
  });
 }
}
