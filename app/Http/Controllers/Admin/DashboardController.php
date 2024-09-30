<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\user;
use App\Models\Admin\Barang;
use App\Models\Admin\TrStok;
use App\Models\Admin\TrMutasi;
use App\Models\Admin\TrMutasiDetail;
use App\Models\Admin\Gudang;
use App\Models\Admin\TargetMesin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $this->userAuth();

        return view('pages.dashboard.index', compact('user'));
    }

    /**
     *  the form for creating a new resource.
     */



}
