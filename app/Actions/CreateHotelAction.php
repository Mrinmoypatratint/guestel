<?php
namespace App\Actions;
use App\Models\{Department,Hotel,HotelBranding,Permission,Role,RoomType,Service,ServiceCategory,User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class CreateHotelAction {
 public function __construct(private GenerateQrCodeAction $generateQr){}
 public function execute(array $data): Hotel {
  return DB::transaction(function() use($data){
   $hotel=Hotel::create(['name'=>$data['name'],'slug'=>$data['slug']??Str::slug($data['name']).'-'.Str::lower(Str::random(6)),'email'=>$data['email']??null,'phone'=>$data['phone']??null,'timezone'=>$data['timezone']??'UTC','status'=>'active','city'=>$data['city']??null,'state'=>$data['state']??null,'country'=>$data['country']??null]);
   HotelBranding::withoutGlobalScopes()->create(['hotel_id'=>$hotel->id,'primary_color'=>'#172554','accent_color'=>'#C8A96A','welcome_message'=>'Everything you need for a comfortable stay.']);
   $admin=User::firstOrCreate(['email'=>$data['admin_email']],['name'=>$data['admin_name'],'password'=>Hash::make(Str::random(48)),'is_active'=>true]);
   $hotel->users()->syncWithoutDetaching([$admin->id=>['is_owner'=>true,'status'=>'active']]);
   $role=Role::create(['hotel_id'=>$hotel->id,'name'=>'HOTEL_ADMIN','label'=>'Hotel Admin','scope'=>'hotel']);
   $role->permissions()->sync(Permission::whereIn('name',self::hotelAdminPermissions())->pluck('id'));
   $admin->roles()->attach($role->id,['hotel_id'=>$hotel->id]);
   foreach([['Front Desk','FRONT_DESK'],['Housekeeping','HOUSEKEEPING'],['Maintenance','MAINTENANCE'],['Kitchen','KITCHEN'],['Concierge','CONCIERGE']] as [$name,$code]) Department::withoutGlobalScopes()->create(['hotel_id'=>$hotel->id,'name'=>$name,'code'=>$code,'is_active'=>true]);
   $category=ServiceCategory::withoutGlobalScopes()->create(['hotel_id'=>$hotel->id,'name'=>'Guest Services','slug'=>'guest-services','sort_order'=>1,'is_active'=>true]);
   $housekeeping=Department::withoutGlobalScopes()->where('hotel_id',$hotel->id)->where('code','HOUSEKEEPING')->firstOrFail();
   foreach(['Extra Towels','Bedsheets','Pillows','Toiletries','Water'] as $name) Service::withoutGlobalScopes()->create(['hotel_id'=>$hotel->id,'service_category_id'=>$category->id,'department_id'=>$housekeeping->id,'name'=>$name,'slug'=>Str::slug($name),'target_response_minutes'=>10,'target_completion_minutes'=>20,'is_active'=>true]);
   RoomType::withoutGlobalScopes()->create(['hotel_id'=>$hotel->id,'name'=>'Standard','capacity'=>2,'base_rate'=>0,'is_active'=>true]);
   $this->generateQr->execute($hotel->id,$hotel,'Hotel QR');
   DB::table('audit_logs')->insert(['hotel_id'=>$hotel->id,'user_id'=>auth()->id(),'action'=>'hotel.created','entity_type'=>Hotel::class,'entity_id'=>$hotel->id,'new_values'=>json_encode(['name'=>$hotel->name]),'ip_address'=>request()?->ip(),'user_agent'=>request()?->userAgent(),'created_at'=>now()]);
   return $hotel->fresh(['branding']);
  });
 }
 private static function hotelAdminPermissions():array{return ['hotel.view','hotel.update','rooms.view','rooms.create','rooms.update','rooms.delete','services.manage','requests.view','requests.assign','requests.update','orders.view','orders.update','menu.manage','staff.manage','analytics.view','settings.manage','audit.view','qr.manage','chat.manage','notifications.view'];}
}
