<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class NadiCsLoginController extends Controller
{
    public function showLogin()
    {
        if (
            Auth::check() &&
            in_array(Auth::user()->role ?? null, ['cs', 'admin'], true)
        ) {
            return redirect('/cs/nadi');
        }

        return view('nadi.cs.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $user = Auth::user();

        if (!in_array($user->role ?? null, ['cs', 'admin'], true)) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Akun tidak memiliki akses Customer Service.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/cs/nadi');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('nadi.cs.login');
    }
}