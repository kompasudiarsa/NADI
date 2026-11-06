<?php
namespace App\Http\Controllers\NadiSupport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class CsAuthController extends Controller {
 public function show(Request $r) {
  if(Auth::guard('nadi_cs')->check()) return redirect()->route('cs.nadi.dashboard');
  return view('admin.login');
 }
 public function login(Request $r) {
  $v=$r->validate(['email'=>['required','email'],'password'=>['required','string']]);
  $ok=Auth::guard('nadi_cs')->attempt(['email'=>$v['email'],'password'=>$v['password'],'is_active'=>1]);
  if(!$ok) throw ValidationException::withMessages(['email'=>'Email, kata sandi, atau status akun tidak sesuai.']);
  $user=Auth::guard('nadi_cs')->user();
  if(!in_array($user->role,['cs','admin'],true)) {
   Auth::guard('nadi_cs')->logout();
   throw ValidationException::withMessages(['email'=>'Akun tidak memiliki izin CS.']);
  }
  $r->session()->regenerate();
  return redirect()->intended(route('cs.nadi.dashboard'));
 }
 public function logout(Request $r) {
  Auth::guard('nadi_cs')->logout();
  // Jangan invalidate() session web: data session pasien NADI berada di cookie/session yang sama.
  $r->session()->regenerateToken();
  return redirect()->route('admin.login');
 }
}
