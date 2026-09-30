<?php
namespace App\Providers;
use App\Models\Room;
use App\Policies\RoomPolicy;
use App\Support\TenantContext;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider {
 public function register(): void { $this->app->singleton(TenantContext::class, fn()=>new TenantContext); }
 public function boot(): void {
   Gate::policy(Room::class,RoomPolicy::class);
   RateLimiter::for('login',fn(Request $r)=>Limit::perMinute(5)->by(strtolower((string)$r->input('email')).'|'.$r->ip()));
   RateLimiter::for('public-qr',fn(Request $r)=>Limit::perMinute(60)->by($r->ip()));
   RateLimiter::for('guest-actions',fn(Request $r)=>Limit::perMinute(20)->by($r->session()->getId().'|'.$r->ip()));

   \Illuminate\Support\Facades\View::composer(['layouts.app', 'admin.*', 'platform.*'], function ($view) {
       $tenant = app(TenantContext::class);
       $hotel = $tenant->hotel();
       $user = auth()->user();

       $userHotels = collect();
       if ($user) {
           $userHotels = $user->is_platform_admin
               ? \App\Models\Hotel::where('status', 'active')->orderBy('name')->get()
               : $user->hotels()->wherePivot('status', 'active')->orderBy('name')->get();
       }

       $pendingRequests = 0;
       $pendingOrders = 0;
       if ($hotel) {
           $pendingRequests = \App\Models\ServiceRequest::withoutGlobalScopes()
               ->where('hotel_id', $hotel->id)
               ->where('status', 'PENDING')
               ->count();
           $pendingOrders = \App\Models\Order::withoutGlobalScopes()
               ->where('hotel_id', $hotel->id)
               ->where('status', 'PENDING')
               ->count();
       }

       $view->with([
           'currentHotel' => $hotel,
           'userHotels' => $userHotels,
           'globalPendingRequests' => $pendingRequests,
           'globalPendingOrders' => $pendingOrders,
       ]);
   });
 }
}
