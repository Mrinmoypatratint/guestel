<?php
namespace Tests\Feature;
use App\Models\{Hotel,Permission,Role,Room,RoomType,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class TenantIsolationTest extends TestCase {use RefreshDatabase;
 public function test_hotel_a_admin_cannot_open_hotel_b_room():void{
  $this->seed(\Database\Seeders\PermissionSeeder::class);
  $a=Hotel::create(['name'=>'A','slug'=>'a','timezone'=>'UTC','status'=>'active']);$b=Hotel::create(['name'=>'B','slug'=>'b','timezone'=>'UTC','status'=>'active']);
  $u=User::factory()->create(['is_active'=>true]);$a->users()->attach($u->id,['status'=>'active','is_owner'=>true]);
  $role=Role::create(['hotel_id'=>$a->id,'name'=>'ADMIN','label'=>'Admin','scope'=>'hotel']);$role->permissions()->attach(Permission::where('name','rooms.view')->firstOrFail());$u->roles()->attach($role->id,['hotel_id'=>$a->id]);
  $type=RoomType::withoutGlobalScopes()->create(['hotel_id'=>$b->id,'name'=>'Standard','capacity'=>2,'base_rate'=>0,'is_active'=>true]);$room=Room::withoutGlobalScopes()->create(['hotel_id'=>$b->id,'room_type_id'=>$type->id,'number'=>'204','status'=>'available','is_active'=>true]);
  $this->actingAs($u)->withSession([config('hospitality.tenant_session_key')=>$a->id])->get('/admin/rooms/'.$room->id)->assertNotFound();
 }
}
