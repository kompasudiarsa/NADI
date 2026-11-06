<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NadiCsAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('nadi_cs')->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi login CS telah berakhir.'
                ], 401);
            }

            return redirect()->route('nadi.cs.login');
        }

        if (!$user->is_active) {
            abort(403, 'Akun Customer Service tidak aktif.');
        }

        if (!in_array($user->role, ['cs', 'admin'], true)) {
            abort(403, 'Anda tidak memiliki akses Customer Service.');
        }

        Auth::shouldUse('nadi_cs');

        return $next($request);
    }
}