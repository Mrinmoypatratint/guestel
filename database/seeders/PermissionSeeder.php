<?php
namespace Database\Seeders;
use App\Models\Permission;use Illuminate\Database\Seeder;
class PermissionSeeder extends Seeder{public function run():void{$items=['hotel.view','hotel.update','rooms.view','rooms.create','rooms.update','rooms.delete','services.manage','requests.view','requests.assign','requests.update','orders.view','orders.update','menu.manage','staff.manage','analytics.view','settings.manage','audit.view','qr.manage','chat.manage','notifications.view'];foreach($items as $name)Permission::updateOrCreate(['name'=>$name],['label'=>ucwords(str_replace(['.','_'],' ',$name))]);}}
