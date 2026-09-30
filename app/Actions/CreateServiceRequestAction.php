<?php
namespace App\Actions;
use App\Models\{AnalyticsEvent,GuestSession,Service,ServiceRequest};
use Illuminate\Support\Facades\DB;use Illuminate\Support\Str;use App\Services\HotelNotifier;use App\Notifications\NewServiceRequestNotification;
class CreateServiceRequestAction{
 public function __construct(private HotelNotifier $notifier){}
 public function execute(GuestSession $guest,array $data):ServiceRequest{
  return DB::transaction(function()use($guest,$data){
   $service=Service::withoutGlobalScopes()->whereKey($data['service_id'])->where('hotel_id',$guest->hotel_id)->where('is_active',true)->firstOrFail();
   $req=ServiceRequest::withoutGlobalScopes()->create(['hotel_id'=>$guest->hotel_id,'room_id'=>$guest->room_id,'guest_session_id'=>$guest->id,'service_id'=>$service->id,'department_id'=>$service->department_id,'request_number'=>'SR-'.now()->format('ymd').'-'.strtoupper(Str::random(7)),'priority'=>$data['priority']??'normal','status'=>'PENDING','guest_note'=>$data['note']??null,'response_due_at'=>now()->addMinutes($service->target_response_minutes),'completion_due_at'=>now()->addMinutes($service->target_completion_minutes)]);
   DB::table('service_request_status_history')->insert(['hotel_id'=>$guest->hotel_id,'service_request_id'=>$req->id,'changed_by'=>null,'from_status'=>null,'to_status'=>'PENDING','note'=>'Created by guest','created_at'=>now()]);
   AnalyticsEvent::create(['hotel_id'=>$guest->hotel_id,'guest_session_id'=>$guest->id,'event_name'=>'service_request.created','entity_type'=>ServiceRequest::class,'entity_id'=>$req->id,'properties'=>['service_id'=>$service->id],'created_at'=>now()]);
   $req->load('service');$this->notifier->permission($guest->hotel_id,'requests.view',new NewServiceRequestNotification($req));
   return $req;
  });
 }
}
