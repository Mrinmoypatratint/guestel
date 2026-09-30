<?php
namespace App\Actions;
use App\Models\QrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class GenerateQrCodeAction {
 public function execute(int $hotelId, Model $qrable, string $label): QrCode {
   return QrCode::withoutGlobalScopes()->create([
     'hotel_id'=>$hotelId,'public_token'=>Str::lower(Str::random(48)),
     'qrable_type'=>$qrable->getMorphClass(),'qrable_id'=>$qrable->getKey(),'label'=>$label,'is_active'=>true,
   ]);
 }
}
