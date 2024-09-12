<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\ReturnMesin;
use App\Models\Admin\DetailReturnMesin;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ReturnMesinController extends Controller
{
  /**
   * Display a listing of the resource.
   */

  public function userAuth()
  {
    $user = Auth::guard('user')->user();
    return $user;
  }
  public function index(Request $request)
  {
    $user = $this->userAuth();
    $path = 'return-mesin.';

    if ($request->ajax()) {
      $returnMesins = ReturnMesin::where('status', 0)->get();
      return DataTables::of($returnMesins)
        ->addColumn('action', function ($object) use ($path) {
          $html = '<a href="' . route($path . "edit", ["no_returnmsn" => $object->no_returnmsn]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
            . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
          return $html;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    return view('pages.return-mesin.index', compact('user'));
  }


  /**
   * Show the form for creating a new resource.
   */
  public function create(Request $request)
  {
    $user = $this->userAuth();
    $path = 'return-mesin.create.';
    if ($request->ajax()) {
      $barangs = Barang::where('status', 0)->get();
      return DataTables::of($barangs)
        ->addColumn('action', function ($object) use ($path) {
          $html = '<div class="d-flex justify-content-center"><button class="btn btn-primary waves-effect waves-light btn-add" data-bs-toggle="modal"' .
            'data-bs-target="#qtyModal"><i class="bx bx-plus-circle align-middle font-size-18"></i></button></div>';
          return $html;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('pages.return-mesin.create', compact('user'));
  }



  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $no_returnmsn = 'RBI/SKM' . '/' . date('y/m/' . str_pad(ReturnMesin::count() + 1, 3, '0', STR_PAD_LEFT));

    $returnSKM = ReturnMesin::create([
      'no_returnmsn' => $no_returnmsn,
      'tgl' => $request->tgl,
    ]);

    foreach ($request->items as $item) {
      DetailReturnMesin::create([
        'no_returnmsn' => $no_returnmsn,
        'brg_id' => $item['brg_id'],
        'qty' => $item['qty'],
        'satuan_besar' => $item['satuan_besar'],
      ]);
    }

    return redirect()->route('return-mesin')->with('success', 'Data return berhasil ditambahkan.');
  }

  /**
   * Display the specified resource.
   */
  public function show()
  {
    //
  }

  public function detailKRM()
  {
    return view('pages.pengiriman-gu.detail');
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit()
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update()
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy()
  {
    //
  }
}
