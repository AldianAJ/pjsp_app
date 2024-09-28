<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\ClosingController;
use App\Http\Controllers\Admin\GudangController;
use App\Http\Controllers\Admin\MesinController;
use App\Http\Controllers\Admin\JenisMesinController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TrTrmSupController;
use App\Http\Controllers\Admin\KinerjaController;
use App\Http\Controllers\Admin\TrReqSKMController;
use App\Http\Controllers\Admin\TrKrmSKMController;
use App\Http\Controllers\Admin\TrKrmMsnController;
use App\Http\Controllers\Admin\ReturnMesinController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('auth');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/auth', 'index')->name('auth');
    Route::post('/auth/login', 'login')->name('auth.login');
    Route::get('/auth/logout', 'logout')->name('auth.logout');
});

Route::middleware('user')->group(function () {
    Route::controller(BarangController::class)->group(function () {
        Route::get('/barang', 'index')->name('barang');
        Route::get('/barang/create', 'create')->name('barang.create');
        Route::get('/barang/edit/{brg_id}', 'edit')->name('barang.edit');
        Route::post('/barang/store', 'store')->name('barang.store');
        Route::post('/barang/update/{brg_id}', 'update')->name('barang.update');
        Route::get('/', [BarangController::class, 'getSidebarData']);

        Route::get('/stok', 'indexStok')->name('stok');
    });

    Route::controller(GudangController::class)->group(function () {
        Route::get('/gudang', 'index')->name('gudang');
        Route::get('/gudang/create', 'create')->name('gudang.create');
        Route::get('/gudang/edit/{gudang_id}', 'edit')->name('gudang.edit');
        Route::post('/gudang/store', 'store')->name('gudang.store');
        Route::post('/gudang/update/{gudang_id}', 'update')->name('gudang.update');
    });

    Route::controller(MesinController::class)->group(function () {
        Route::get('/mesin', 'index')->name('mesin');
        Route::get('/mesin/create', 'create')->name('mesin.create');
        Route::get('/mesin/edit/{mesin_id}', 'edit')->name('mesin.edit');
        Route::post('/mesin/store', 'store')->name('mesin.store');
        Route::post('/mesin/update/{mesin_id}', 'update')->name('mesin.update');
    });

    Route::controller(JenisMesinController::class)->group(function () {
        Route::get('/jenis-mesin', 'index')->name('jenis-mesin');
        Route::get('/jenis-mesin/create', 'create')->name('jenis-mesin.create');
        Route::get('/jenis-mesin/edit/{jenis_id}', 'edit')->name('jenis-mesin.edit');
        Route::post('/jenis-mesin/store', 'store')->name('jenis-mesin.store');
        Route::post('/jenis-mesin/update/{jenis_id}', 'update')->name('jenis-mesin.update');
    });

    Route::controller(SupplierController::class)->group(function () {
        Route::get('/supplier', 'index')->name('supplier');
        Route::get('/supplier/create', 'create')->name('supplier.create');
        Route::get('/supplier/edit/{supplier_id}', 'edit')->name('supplier.edit');
        Route::post('/supplier/store', 'store')->name('supplier.store');
        Route::post('/supplier/update/{supplier_id}', 'update')->name('supplier.update');
    });


    Route::controller(KinerjaController::class)->group(function () {
        Route::get('/kinerja-minggu', 'index')->name('kinerja-minggu');
        Route::get('/kinerja-minggu/create', 'create')->name('kinerja-minggu.create');
        Route::get('/kinerja-minggu/edit/{supplier_id}', 'edit')->name('kinerja-minggu.edit');
        Route::post('/kinerja-minggu/store', 'store')->name('kinerja-minggu.store');
        Route::post('/kinerj-minggu/update/', 'update')->name('kinerja-minggu.update');

        Route::get('/kinerja-hari', 'indexhari')->name('kinerja-hari');
        Route::get('/kinerja-hari/create', 'createhari')->name('kinerja-hari.create');
        Route::post('/kinerja-hari/store', 'storehari')->name('kinerja-hari.store');
        Route::get('/kinerja-hari/detail/', 'detailhari')->name('kinerja-hari.detail');
        Route::post('/kinerj-hari/update/', 'updatehari')->name('kinerja-hari.update');

        Route::get('/kinerja-shift', 'indexshift')->name('kinerja-shift');
        Route::get('/kinerja-shift/create', 'createshift')->name('kinerja-shift.create');
        Route::post('/kinerja-shift/store', 'storeshift')->name('kinerja-shift.store');
        Route::get('/kinerja-shift/detail/', 'detailshift')->name('kinerja-shift.detail');
        Route::post('/kinerj-shift/update/', 'updateshift')->name('kinerja-shift.update');

        Route::get('/kinerja-mesin', 'indexmesin')->name('kinerja-mesin');
        Route::get('/kinerja-mesin/create', 'createmesin')->name('kinerja-mesin.create');
        Route::post('/kinerja-mesin/store', 'storemesin')->name('kinerja-mesin.store');
        Route::get('/kinerja-mesin/detail/', 'detailmesin')->name('kinerja-mesin.detail');
        Route::post('/kinerj-mesin/update/', 'updatemesin')->name('kinerja-mesin.update');
    });

    Route::controller(ClosingController::class)->group(function () {
        Route::get('/closing-mesin', 'index')->name('closing-mesin');
        Route::get('/closing-mesin/create', 'create')->name('closing-mesin.create');
        Route::post('/closing-mesin/store', 'store')->name('closing-mesin.store');
        Route::post('/closing-mesin/store-hlp', 'storeHlp')->name('closing-mesin.storeHlp');
        Route::get('/closing-mesin/edit/{closing_id}', 'edit')->name('closing-mesin.edit');
        Route::post('/closing-mesin/update/{closing_id}', 'update')->name('closing-mesin.update');
    });

    Route::controller(TrReqSKMController::class)->group(function () {
        Route::get('/permintaan-skm', 'index')->name('permintaan-skm');
        Route::get('/permintaan-skm/detail', 'showDetail')->name('permintaan-skm.showDetail');
        Route::get('/permintaan-skm/create', 'create')->name('permintaan-skm.create');
        Route::post('/permintaan-skm/store', 'store')->name('permintaan-skm.store');
        Route::get('/permintaan-skm/edit/{no_reqskm}', 'edit')->name('permintaan-skm.edit');
        Route::post('/permintaan-skm/update/{no_reqskm}', 'update')->name('permintaan-skm.update');
        Route::get('/permintaan-skm/history', 'indexHistory')->name('permintaan-skm.history');
        Route::get('/permintaan-skm/history/detail', 'showDetailHistory')->name('permintaan-skm.showDetailHistory');

        Route::get('/penerimaan-barang', 'indexTerima')->name('penerimaan-barang');
        Route::get('/penerimaan-barang/create/{no_krmskm}', 'createTerima')->name('penerimaan-barang.create');
        Route::post('/penerimaan-barang/store', 'storeTerima')->name('penerimaan-barang.store');
        Route::get('/penerimaan-barang/detail', 'showterimaDetail')->name('penerimaan-barang.showterimaDetail');
    });

    Route::controller(TrKrmSKMController::class)->group(function () {
        Route::get('/pengiriman-gudang-utama', 'index')->name('pengiriman-gudang-utama');
        Route::get('/pengiriman-gudang-utama/create/{no_reqskm}', 'create')->name('pengiriman-gudang-utama.create');
        Route::post('/pengiriman-gudang-utama/store', 'store')->name('pengiriman-gudang-utama.store');
        Route::get('/pengiriman-gudang-utama/detail', 'showDetail')->name('pengiriman-gudang-utama.showDetail');
        Route::get('/pengiriman-gudang-utama/edit/{no_krmskm}', 'edit')->name('pengiriman-gudang-utama.edit');
        Route::post('/pengiriman-gudang-utama/update/{no_krmskm}', 'update')->name('pengiriman-gudang-utama.update');
        Route::get('/pengiriman-gudang-utama/history', 'indexHistory')->name('pengiriman-gudang-utama.history');
        Route::get('/pengiriman-gudang-utama/history/detail', 'showDetailHistory')->name('pengiriman-gudang-utama.showDetailHistory');
    });

    Route::controller(TrKrmMsnController::class)->group(function () {
        Route::get('/pengiriman-skm', 'index')->name('pengiriman-skm');
        Route::get('/pengiriman-skm/detail', 'showDetail')->name('pengiriman-skm.showDetail');
        Route::get('/pengiriman-skm/create', 'create')->name('pengiriman-skm.create');
        Route::post('/pengiriman-skm/store', 'store')->name('pengiriman-skm.store');
        Route::get('/pengiriman-skm/edit/{no_krmmsn}', 'edit')->name('pengiriman-skm.edit');
        Route::post('/pengiriman-skm/update/{no_krmmsn}', 'update')->name('pengiriman-skm.update');
        Route::get('/pengiriman-skm/history', 'indexHistory')->name('pengiriman-skm.history');
        Route::get('/pengiriman-skm/history/detail', 'showDetailHistory')->name('pengiriman-skm.showDetailHistory');
    });

    Route::controller(ReturnMesinController::class)->group(function () {
        Route::get('/return-mesin', 'index')->name('return-mesin');
        Route::get('/return-mesin/create', 'create')->name('return-mesin.create');
        Route::post('/return-mesin/store', 'store')->name('return-mesin.store');
        Route::get('/return-mesin/edit/{no_returnmsn}', 'edit')->name('return-mesin.edit');
    });


    Route::controller(TrTrmSupController::class)->group(function () {
        Route::get('/stok-masuk', 'index')->name('stok-masuk');
        Route::get('/stok-masuk/create', 'create')->name('stok-masuk.create');
        Route::post('/stok-masuk/store', 'store')->name('stok-masuk.store');
        Route::get('/stok-masuk/edit/{no_trm}', 'edit')->name('stok-masuk.edit');
        Route::post('/stok-masuk/update/{no_trm}', 'update')->name('stok-masuk.update');
        Route::get('/stok-masuk/detail', 'showDetail')->name('stok-masuk.showDetail');
    });
});
