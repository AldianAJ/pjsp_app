<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Yajra\DataTables\DataTables;
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

        // Check if user exists and verify the hashed password
        if ($user && Hash::check($request->password, $user->password)) {
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

    public function IndexUser(Request $request)
    {
        $path = 'super-admin.';
        if ($request->ajax()) {
            $users = User::where('status', 0)->get();

            return DataTables::of($users)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "edit", ["user_id" => $object->user_id]) . '" class="btn btn-success waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.super-admin.index');
    }

    public function create()
    {
        $user_id = 'U' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
        return view('pages.super-admin.create', compact('user_id'));
    }

    public function store(Request $request)
    {
        User::create([
            'user_id' => $request->user_id,
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'gudang_id' => $request->gudang_id ?? 0,
        ]);

        return redirect()->route('super-admin')->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit(string $user_id)
    {
        $data = User::where('user_id', $user_id)->where('status', 0)->first();
        return view('pages.super-admin.edit', compact('data', 'user_id'));
    }

    public function update(Request $request, string $user_id)
    {
        $user = User::where('user_id', $user_id)->first();
        $user->nama = $request->nama;
        $user->username = $request->username;
        $user->role = $request->role;

        if ($request->filled('gudang_id')) {
            $user->gudang_id = $request->gudang_id;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user')->with('success', 'Data user berhasil diperbarui.');
    }
}
