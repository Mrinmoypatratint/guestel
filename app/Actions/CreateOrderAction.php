<?php
namespace App\Actions;
use App\Models\{AnalyticsEvent,GuestSession,MenuItem,MenuModifier,Order,OrderItem,Restaurant};
use Illuminate\Support\Facades\DB;use Illuminate\Support\Str;use Illuminate\Validation\ValidationException;use App\Services\HotelNotifier;use App\Notifications\NewOrderNotification;
class CreateOrderAction{
 public function __construct(private HotelNotifier $notifier){}
 public function execute(GuestSession $guest,array $data):Order{
  return DB::transaction(function()use($guest,$data){
   GuestSession::withoutGlobalScopes()->whereKey($guest->id)->lockForUpdate()->firstOrFail();
   $existing=Order::withoutGlobalScopes()->where('guest_session_id',$guest->id)->where('idempotency_key',$data['idempotency_key'])->first();if($existing)return $existing->load('items');
   $restaurant=Restaurant::withoutGlobalScopes()->whereKey($data['restaurant_id'])->where('hotel_id',$guest->hotel_id)->where('is_active',true)->lockForUpdate()->firstOrFail();
   $subtotal=0.0;$lines=[];
   foreach($data['items'] as $raw){$qty=(int)$raw['quantity'];if($qty<1||$qty>20)throw ValidationException::withMessages(['items'=>'Invalid quantity.']);$item=MenuItem::withoutGlobalScopes()->whereKey($raw['menu_item_id'])->where('hotel_id',$guest->hotel_id)->where('restaurant_id',$restaurant->id)->where('is_available',true)->firstOrFail();$unit=(float)$item->price;$mods=[];
    foreach(($raw['modifier_ids']??[]) as $mid){$m=MenuModifier::withoutGlobalScopes()->whereKey($mid)->where('hotel_id',$guest->hotel_id)->where('menu_item_id',$item->id)->where('is_available',true)->firstOrFail();$unit+=(float)$m->price_delta;$mods[]=['id'=>$m->id,'name'=>$m->name,'price_delta'=>(float)$m->price_delta];}
    $line=round($unit*$qty,2);$subtotal+=$line;$lines[]=['item'=>$item,'unit'=>$unit,'qty'=>$qty,'line'=>$line,'mods'=>$mods,'note'=>$raw['note']??null];}
   if($lines===[])throw ValidationException::withMessages(['items'=>'Your cart is empty.']);
   $subtotal=round($subtotal,2);$tax=round($subtotal*((float)$restaurant->tax_rate/100),2);$discount=0.0;$total=round($subtotal+$tax-$discount,2);
   $order=Order::withoutGlobalScopes()->create(['hotel_id'=>$guest->hotel_id,'room_id'=>$guest->room_id,'guest_session_id'=>$guest->id,'restaurant_id'=>$restaurant->id,'order_number'=>'ORD-'.now()->format('ymd').'-'.strtoupper(Str::random(7)),'idempotency_key'=>$data['idempotency_key'],'status'=>'PENDING','subtotal'=>$subtotal,'tax'=>$tax,'discount'=>$discount,'total'=>$total,'currency'=>$restaurant->currency,'special_instructions'=>$data['special_instructions']??null]);
   foreach($lines as $l)OrderItem::withoutGlobalScopes()->create(['hotel_id'=>$guest->hotel_id,'order_id'=>$order->id,'menu_item_id'=>$l['item']->id,'item_name'=>$l['item']->name,'unit_price'=>$l['unit'],'quantity'=>$l['qty'],'line_total'=>$l['line'],'modifier_snapshot'=>$l['mods'],'special_instructions'=>$l['note']]);
   DB::table('order_status_history')->insert(['hotel_id'=>$guest->hotel_id,'order_id'=>$order->id,'changed_by'=>null,'from_status'=>null,'to_status'=>'PENDING','note'=>'Created by guest','created_at'=>now()]);
   AnalyticsEvent::create(['hotel_id'=>$guest->hotel_id,'guest_session_id'=>$guest->id,'event_name'=>'order.created','entity_type'=>Order::class,'entity_id'=>$order->id,'properties'=>['total'=>$total,'currency'=>$restaurant->currency],'created_at'=>now()]);
   $order->load('items');$this->notifier->permission($guest->hotel_id,'orders.view',new NewOrderNotification($order));
   return $order;
  });
 }
}
