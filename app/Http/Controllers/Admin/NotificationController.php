<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use Illuminate\Http\Request;
class NotificationController extends Controller{public function index(Request $request){return view('admin.notifications',['notifications'=>$request->user()->notifications()->latest()->paginate(40)]);}public function read(Request $request,string $notification){$n=$request->user()->notifications()->whereKey($notification)->firstOrFail();$n->markAsRead();return back();}}
