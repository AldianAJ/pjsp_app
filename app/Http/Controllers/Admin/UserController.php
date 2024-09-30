<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\User;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.auth.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && $user->password === $request->password) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        } else {
            return back()->withErrors(['login' => 'Username atau password salah !']);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth');
    }
}
