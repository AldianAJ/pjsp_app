<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\GudangController;
use App\Http\Controllers\Admin\MesinController;
use App\Http\Controllers\Admin\JenisMesinController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\StokMasukController;
use App\Http\Controllers\Admin\KinerjaController;
use App\Http\Controllers\Admin\PermintaanBarangController;
use App\Http\Controllers\Admin\PengirimanBarangController;
use App\Models\Admin\JenisMesin;

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

// Rute untuk UserController
Route::controller(UserController::class)->group(function () {
    Route::get('/auth', 'index')->name('auth');
    Route::post('/auth/login', 'login')->name('auth.login');
    Route::get('/auth/logout', 'logout')->name('auth.logout');
});

// Rute yang memerlukan autentikasi
Route::middleware('user')->group(function () {
    Route::controller(BarangController::class)->group(function () {
        Route::get('/barang', 'index')->name('barang');
        Route::get('/barang/create', 'create')->name('barang.create');
        Route::get('/barang/edit/{brg_id}', 'edit')->name('barang.edit');
        Route::post('/barang/store', 'store')->name('barang.store');
        Route::post('/barang/update/{brg_id}', 'update')->name('barang.update');
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
        Route::get('/kinerja-skm', 'index')->name('kinerja-skm');
        Route::get('/kinerja-skm/create', 'create')->name('kinerja-skm.create');
        Route::get('/kinerja-skm/edit/{supplier_id}', 'edit')->name('kinerja-skm.edit');
        Route::post('/kinerja-skm/store', 'store')->name('kinerja-skm.store');
        Route::post('/kinerj-skm/update/{supplier_id}', 'update')->name('kinerja.update');
    });


    Route::controller(PermintaanBarangController::class)->group(function () {
        Route::get('/permintaan-barang', 'index')->name('permintaan-barang');
        Route::get('/permintaan-barang/create', 'create')->name('permintaan-barang.create');
        Route::post('/permintaan-barang/store', 'store')->name('permintaan-barang.store');
        Route::post('/permintaan-barang/detail', 'detail')->name('permintaan-barang.detail');
        Route::get('/permintaan-barang/detail/{slug}', 'detailByGudang')->name('permintaan-barang.detailByGudang');
        Route::get('/permintaan-barang/checkminta', 'checkMinta')->name('permintaan-barang.checkMinta');
        Route::get('/permintaan-barang/persetujuan/{slug}/{id}', 'createPersetujuan')->name('permintaan-barang.persetujuan');
        Route::post('/permintaan-barang/temp/persetujuan', 'temporaryPersetujuan')->name('permintaan-barang.tmpPersetujuan');
        Route::get('/permintaan-barang/pengiriman/store/{slug}', 'storePengiriman')->name('permintaan-barang.storePersetujuan');
        Route::get('/permintaan-barang/history', 'indexHistory')->name('permintaan-barang.history');
        Route::get('/permintaan-barang/exportPDF/{slug}', 'exportPDF')->name('permintaan-barang.exportPDF');
    });

    Route::controller(PengirimanBarangController::class)->group(function () {
        Route::get('/pengiriman-barang', 'index')->name('pengiriman-barang');
        Route::get('/pengiriman-barang/detail/{slug}', 'show')->name('pengiriman-barang.detail');
        Route::get('/pengiriman-barang/store/penerimaan/{slug}', 'storePenerimaan')->name('pengiriman-barang.penerimaan');
        Route::get('/pengiriman-barang/keep/barang', 'indexBarangDiambil')->name('pengiriman-barang.barangDiambil');
        Route::get('/pengiriman-barang/history', 'indexHistory')->name('pengiriman-barang.history');
        Route::post('/pengiriman-barang/history/detail', 'detailHistory')->name('pengiriman-barang.detailHistory');
        Route::get('/pengiriman-barang/exportPDF/{slug}', 'exportPDF')->name('pengiriman-barang.exportPDF');
        Route::get('/pengiriman_barang/update/status/{pengiriman_barang_id}/{barang_id}', 'updateStatus')->name('pengiriman-barang.updateStatus');
    });



    Route::controller(StokMasukController::class)->group(function () {
        Route::get('/stok-masuk', 'index')->name('stok-masuk');
        Route::get('/stok-masuk/create', 'create')->name('stok-masuk.create');
        Route::get('/stok-masuk/edit/{no_trm}', 'edit')->name('stok-masuk.edit');
        Route::post('/stok-masuk/store', 'store')->name('stok-masuk.store');
        Route::post('/stok-masuk/update/{no_trm}', 'update')->name('stok-masuk.update');
    });

});
