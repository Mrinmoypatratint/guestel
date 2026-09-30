<?php
namespace App\Http\Requests;
use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class CreateRoomRequest extends FormRequest {
 public function authorize():bool{return (bool)$this->user()?->hasPermission('rooms.create',app(TenantContext::class)->id());}
 public function rules():array{$h=app(TenantContext::class)->requireId();return ['number'=>['required','string','max:30',Rule::unique('rooms','number')->where(fn($q)=>$q->where('hotel_id',$h)->whereNull('deleted_at'))],'name'=>['nullable','string','max:100'],'floor_id'=>['nullable',Rule::exists('floors','id')->where(fn($q)=>$q->where('hotel_id',$h))],'room_type_id'=>['required',Rule::exists('room_types','id')->where(fn($q)=>$q->where('hotel_id',$h))],'status'=>['nullable',Rule::in(['available','occupied','maintenance','out_of_service'])],'notes'=>['nullable','string','max:2000']];}
}
