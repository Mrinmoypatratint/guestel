<?php
use App\Http\Controllers\Admin\{DashboardController,RoomController,TenantSwitchController,QrController as AdminQrController,QrCenterController,SearchController,ServiceRequestController,OrderController,RestaurantController,AnalyticsController,GalleryController,ConversationController,NotificationController};
use App\Http\Controllers\Auth\{LoginController,PasswordResetController};
use App\Http\Controllers\Guest\QrController as GuestQrController;
use App\Http\Controllers\Guest\StayController;
use App\Http\Controllers\Platform\{DashboardController as PlatformDashboardController, HotelController, BillingController, CommunicationController};
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LandingController;
use App\Http\Controllers\PropertySelectController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::middleware('guest')->group(function(){
 Route::get('/login',[LoginController::class,'create'])->name('login');
 Route::post('/login',[LoginController::class,'store'])->middleware('throttle:login')->name('login.store');
 Route::get('/forgot-password',[PasswordResetController::class,'requestForm'])->name('password.request');
 Route::post('/forgot-password',[PasswordResetController::class,'send'])->middleware('throttle:5,1')->name('password.email');
 Route::get('/reset-password/{token}',[PasswordResetController::class,'resetForm'])->name('password.reset');
 Route::post('/reset-password',[PasswordResetController::class,'reset'])->middleware('throttle:5,1')->name('password.update');
 });
Route::post('/logout',[LoginController::class,'destroy'])->middleware('auth')->name('logout');

Route::get('/'.config('hospitality.qr_public_prefix','g').'/{token}',[GuestQrController::class,'show'])->middleware('throttle:public-qr')->name('guest.qr');
Route::prefix('stay/{session}')->name('guest.')->middleware('throttle:guest-actions')->group(function(){Route::get('/',[StayController::class,'show'])->name('stay');Route::post('/service-requests',[StayController::class,'service'])->name('service');Route::get('/restaurant',[StayController::class,'restaurant'])->name('restaurant');Route::post('/orders',[StayController::class,'order'])->name('order');Route::post('/messages',[StayController::class,'message'])->name('message');Route::post('/feedback',[StayController::class,'feedback'])->name('feedback');});

Route::middleware(['auth', 'platform.admin'])->prefix('platform')->name('platform.')->group(function(){
    Route::get('/', PlatformDashboardController::class)->name('dashboard');
    
    // Hotel Onboarding & Tenant Management
    Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
    Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');
    Route::post('/hotels/{hotel}/toggle-status', [HotelController::class, 'toggleStatus'])->name('hotels.toggle-status');
    
    // SaaS Billing & Taking Payments for Platform Services
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/invoices', [BillingController::class, 'storeInvoice'])->name('billing.invoices.store');
    Route::get('/billing/invoices/{invoice}', [BillingController::class, 'show'])->name('billing.invoices.show');
    Route::post('/billing/invoices/{invoice}/payment', [BillingController::class, 'recordPayment'])->name('billing.invoices.payment');

    // Tenant Communications (Mails to Hotels & Restaurants)
    Route::get('/communications', [CommunicationController::class, 'index'])->name('communications.index');
    Route::post('/communications/send', [CommunicationController::class, 'send'])->name('communications.send');
});
Route::middleware('auth')->group(function(){
    Route::get('/select-property', [PropertySelectController::class, 'index'])->name('property.select');
    Route::post('/switch-property', [PropertySelectController::class, 'switch'])->name('property.switch');
});

Route::middleware(['auth','tenant'])->prefix('admin')->name('admin.')->group(function(){
 Route::get('/', DashboardController::class)->middleware('permission:hotel.view')->name('dashboard');
 Route::get('/search',SearchController::class)->name('search');
 Route::post('/switch-hotel/{hotel}',TenantSwitchController::class)->withoutMiddleware('tenant')->name('switch-hotel');
 Route::get('/rooms',[RoomController::class,'index'])->middleware('permission:rooms.view')->name('rooms.index');
 Route::post('/rooms',[RoomController::class,'store'])->middleware('permission:rooms.create')->name('rooms.store');
 Route::get('/rooms/{room}',[RoomController::class,'show'])->middleware('permission:rooms.view')->name('rooms.show');
 Route::patch('/rooms/{room}/status',[RoomController::class,'updateStatus'])->middleware('permission:rooms.update')->name('rooms.status');
 Route::get('/qr-center',[QrCenterController::class,'index'])->middleware('permission:qr.manage')->name('qr-center.index');
 Route::get('/qr-center/print',[QrCenterController::class,'printSheet'])->middleware('permission:qr.manage')->name('qr-center.print-sheet');
 Route::post('/qr-center/{qr}/toggle',[QrCenterController::class,'toggle'])->middleware('permission:qr.manage')->name('qr-center.toggle');
 Route::get('/qr/{qr}/svg',[AdminQrController::class,'svg'])->middleware('permission:qr.manage')->name('qr.svg');
 Route::get('/requests',[ServiceRequestController::class,'index'])->middleware('permission:requests.view')->name('requests.index');
 Route::patch('/requests/{serviceRequest}',[ServiceRequestController::class,'update'])->middleware('permission:requests.update')->name('requests.update');
 Route::get('/orders',[OrderController::class,'index'])->middleware('permission:orders.view')->name('orders.index');
 Route::patch('/orders/{order}',[OrderController::class,'update'])->middleware('permission:orders.update')->name('orders.update');
 Route::get('/restaurant',[RestaurantController::class,'index'])->middleware('permission:menu.manage')->name('restaurant.index');
 Route::post('/restaurant',[RestaurantController::class,'storeRestaurant'])->middleware('permission:menu.manage')->name('restaurant.store');
 Route::post('/restaurant/categories',[RestaurantController::class,'storeCategory'])->middleware('permission:menu.manage')->name('restaurant.categories.store');
 Route::post('/restaurant/items',[RestaurantController::class,'storeItem'])->middleware('permission:menu.manage')->name('restaurant.items.store');
 Route::post('/restaurant/items/{item}/toggle',[RestaurantController::class,'toggleItem'])->middleware('permission:menu.manage')->name('restaurant.items.toggle');
 Route::get('/analytics',AnalyticsController::class)->middleware('permission:analytics.view')->name('analytics');
 Route::get('/gallery',[GalleryController::class,'index'])->middleware('permission:settings.manage')->name('gallery.index');
 Route::post('/gallery',[GalleryController::class,'store'])->middleware('permission:settings.manage')->name('gallery.store');
 Route::delete('/gallery/{image}',[GalleryController::class,'destroy'])->middleware('permission:settings.manage')->name('gallery.destroy');
 Route::get('/chat',[ConversationController::class,'index'])->middleware('permission:chat.manage')->name('chat.index');
 Route::get('/chat/{conversation}',[ConversationController::class,'show'])->middleware('permission:chat.manage')->name('chat.show');
 Route::post('/chat/{conversation}/reply',[ConversationController::class,'reply'])->middleware('permission:chat.manage')->name('chat.reply');
 Route::get('/notifications',[NotificationController::class,'index'])->middleware('permission:notifications.view')->name('notifications.index');
 Route::post('/notifications/{notification}/read',[NotificationController::class,'read'])->middleware('permission:notifications.view')->name('notifications.read');
});
Route::get('/health',function(){try{\Illuminate\Support\Facades\DB::select('select 1');$db=true;}catch(\Throwable){$db=false;}return response()->json(['status'=>$db?'ok':'degraded','database'=>$db?'ok':'unavailable','storage'=>is_writable(storage_path())?'ok':'unavailable'], $db?200:503);})->name('health');
