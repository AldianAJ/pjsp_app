<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Mesin;
use App\Models\Admin\JenisMesin;
use Illuminate\Support\Facades\Auth;

class MesinController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $Mesins = Mesin::all();
        return view('pages.mesin.index', compact('user', 'Mesins'));
    }

    public function create()
    {
        $user = $this->userAuth();
        $mesin_id = Mesin::generateMesinId();
        $JenisMesins = JenisMesin::select('jenis_id', 'nama')
            ->where('status', 1)
            ->get();
        return view('pages.mesin.create', compact('mesin_id', 'user', 'JenisMesins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|unique:m_mesin,mesin_id',
            'jenis_id' => 'required|exists:m_jenis_mesin,jenis_id',
            'nama' => 'required|string|max:50',
        ]);


        Mesin::create([
            'mesin_id' => $request->mesin_id,
            'jenis_id' => $request->jenis_id,
            'nama' => $request->nama,
        ]);

        return redirect()->route('mesin')->with('success', 'Data mesin berhasil ditambahkan.');
    }

    public function edit(string $mesin_id)
    {
        $data = Mesin::where('mesin_id', $mesin_id)->first();
        $JenisMesins = JenisMesin::select('jenis_id', 'nama')
            ->where('status', 1)
            ->get();
        return view('pages.mesin.edit', compact('data', 'mesin_id', 'JenisMesins'));
    }

    public function update(Request $request, string $mesin_id)
    {
        $request->validate([
            'jenis_id' => 'required|exists:m_jenis_mesin,jenis_id',
            'nama' => 'required|string|max:50',
        ]);

        $mesin_id = Mesin::where('mesin_id', $mesin_id)->first();
        $mesin_id->update([
            'jenis_id' => $request->jenis_id,
            'nama' => $request->nama,
        ]);

        return redirect()->route('mesin')->with('success', 'Data mesin berhasil diperbarui.');
    }

}
