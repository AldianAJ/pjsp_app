<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\JenisMesin;
use Illuminate\Support\Facades\Auth;

class JenisMesinController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $JenisMesins = JenisMesin::all();
        return view('pages.jenis-mesin.index', compact('user', 'JenisMesins'));
    }

    public function create()
    {
        $user = $this->userAuth();
        return view('pages.jenis-mesin.create', compact( 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_id' => 'required|unique:m_jenis_mesin,jenis_id',
            'nama' => 'required|string|max:50',
        ]);


        JenisMesin::create([
            'jenis_id' => $request->jenis_id,
            'nama' => $request->nama,
        ]);

        return redirect()->route('mesin')->with('success', 'Data jenis mesin berhasil ditambahkan.');
    }

    public function edit(string $jenis_id)
    {
        $data = JenisMesin::where('jenis_id', $jenis_id)->first();
        return view('pages.jenis-mesin.edit', compact('data', 'jenis_id'));
    }

    public function update(Request $request, string $jenis_id)
    {
        $request->validate([
            'jenis_id' => 'required|unique:m_jenis_mesin,jenis_id',
            'nama' => 'required|string|max:50',
        ]);

        $jenis_id = JenisMesin::where('jenis_id', $jenis_id)->first();
        $jenis_id->update([
            'jenis_id' => $request->jenis_id,
            'nama' => $request->nama,
        ]);

        return redirect()->route('mesin')->with('success', 'Data jenis mesin berhasil diperbarui.');
    }


}
