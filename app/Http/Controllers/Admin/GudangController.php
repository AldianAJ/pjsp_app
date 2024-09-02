<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Gudang;
use App\Models\Admin\Mesin;
use Illuminate\Support\Facades\Auth;

class GudangController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $gudangs = Gudang::where('status', 1)->get();
        return view('pages.gudang.index', compact('user', 'gudangs'));
    }

    public function create()
    {
        $user = $this->userAuth();
        $mesin_id = null;
        $gudang_id = Gudang::generateGudangId();
        $Mesins = Mesin::where('status', 1)->get();

        return view('pages.gudang.create', compact('gudang_id', 'mesin_id', 'user', 'Mesins'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|integer|in:1,2',
            'gudang_id' => 'required_if:jenis,1|unique:m_gudang,gudang_id',
            'mesin_id' => 'required_if:jenis,2|exists:m_mesin,mesin_id',
            'address' => 'required',
        ]);

        $gudang_id = ($request->jenis == 1) ? Gudang::generateGudangId() : null;
        $mesin_id = ($request->jenis == 2) ? $request->mesin_id : null;
        $address = $request->address;

        if ($request->jenis == 1 || $request->jenis == 2) {
            Gudang::create([
                'gudang_id' => $gudang_id ?: $mesin_id,
                'address' => $address,
            ]);
        }


        return redirect()->route('gudang')->with('success', 'Data gudang berhasil ditambahkan.');
    }




    public function edit(string $gudang_id)
    {
        $data = Gudang::where('gudang_id', $gudang_id)->first();
        $Mesins = Mesin::select('mesin_id', 'nama')
            ->where('status', 1)
            ->get();

        return view('pages.gudang.edit', compact('data', 'gudang_id', 'Mesins'));
    }

    public function update(Request $request, string $gudang_id)
    {
        $request->validate([
            'jenis' => 'required|integer|in:1,2',
            'mesin_id' => 'required_if:jenis,2|exists:m_mesin,mesin_id',
            'address' => 'required',
        ]);

        $gudang = Gudang::where('gudang_id', $gudang_id)->first();

        if ($request->jenis == 1) {
            $mesin_id = null;
        } elseif ($request->jenis == 2) {
            $mesin_id = $request->mesin_id;
        }

        $gudang->update([
            'mesin_id' => $mesin_id,
            'address' => $request->address,
        ]);

        return redirect()->route('gudang')->with('success', 'Data gudang berhasil diperbarui.');
    }
}

