<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class NadiCsRole {
 public function handle(Request $r,Closure $next) {
  $u=Auth::guard('nadi_cs')->user();
  if(!$u) return $r->expectsJson()?response()->json(['message'=>'Login CS diperlukan'],401):redirect()->route('nadi.cs.login');
  abort_unless((bool)$u->is_active && in_array($u->role,['cs','admin'],true),403,'Akses CS ditolak.');
  return $next($r);
 }
}
