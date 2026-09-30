<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
class PasswordResetController extends Controller {
 public function requestForm(){return view('auth.forgot-password');}
 public function send(Request $request){$request->validate(['email'=>['required','email']]);$status=Password::sendResetLink($request->only('email'));return $status===Password::ResetLinkSent?back()->with('status',__($status)):back()->withErrors(['email'=>__($status)]);}
 public function resetForm(string $token,Request $request){return view('auth.reset-password',['token'=>$token,'email'=>$request->query('email')]);}
 public function reset(Request $request){$request->validate(['token'=>'required','email'=>['required','email'],'password'=>['required','confirmed','min:12']]);$status=Password::reset($request->only('email','password','password_confirmation','token'),function(User $user,string $password){$user->forceFill(['password'=>Hash::make($password),'remember_token'=>Str::random(60)])->save();event(new PasswordReset($user));});return $status===Password::PasswordReset?redirect()->route('login')->with('status',__($status)):back()->withErrors(['email'=>__($status)]);}
}
