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
use App\Http\Controllers\Admin\PermintaanController;
use App\Http\Controllers\Admin\PengirimanController;

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
        Route::get('/kinerja-minggu', 'index')->name('kinerja-minggu');
        Route::get('/kinerja-minggu/create', 'create')->name('kinerja-minggu.create');
        Route::get('/kinerja-minggu/edit/{supplier_id}', 'edit')->name('kinerja-minggu.edit');
        Route::post('/kinerja-minggu/store', 'store')->name('kinerja-minggu.store');
        Route::post('/kinerj-minggu/update/{supplier_id}', 'update')->name('kinerja.update');
        Route::get('/kinerja-hari', 'indexhari')->name('kinerja-hari');
        Route::get('/kinerja-hari/create', 'createhari')->name('kinerja-hari.create');
        Route::post('/kinerja-hari/store', 'storehari')->name('kinerja-hari.store');
        Route::get('/kinerja-hari/detail/', 'detailhari')->name('kinerja-hari.detail');
        Route::get('/kinerja-shift', 'index')->name('kinerja-shift');
        Route::get('/kinerja-mesin', 'index')->name('kinerja-mesin');
    });


    Route::controller(PermintaanController::class)->group(function () {
        Route::get('/permintaan', 'index')->name('permintaan');
        Route::get('/permintaan/create', 'create')->name('permintaan.create');
        Route::get('/permintaan/edit/{no_reqskm}', 'edit')->name('permintaan.edit');
        Route::post('/permintaan/store', 'store')->name('permintaan.store');
        Route::post('/permintaan/update/{no_reqskm}', 'update')->name('supplier.update');
    });

    Route::controller(PengirimanController::class)->group(function () {
        Route::get('/pengiriman', 'index')->name('pengiriman');
        Route::get('/pengiriman/detail/{slug}', 'show')->name('pengiriman.detail');
        Route::get('/pengiriman/store/penerimaan/{slug}', 'storePenerimaan')->name('pengiriman.penerimaan');
        Route::get('/pengiriman/keep/barang', 'indexBarangDiambil')->name('pengiriman.barangDiambil');
        Route::get('/pengiriman/history', 'indexHistory')->name('pengiriman.history');
        Route::post('/pengiriman/history/detail', 'detailHistory')->name('pengiriman.detailHistory');
        Route::get('/pengiriman/exportPDF/{slug}', 'exportPDF')->name('pengiriman.exportPDF');
        Route::get('/pengiriman_barang/update/status/{pengiriman_barang_id}/{barang_id}', 'updateStatus')->name('pengiriman.updateStatus');
    });



    Route::controller(StokMasukController::class)->group(function () {
        Route::get('/stok-masuk', 'index')->name('stok-masuk');
        Route::get('/stok-masuk/create', 'create')->name('stok-masuk.create');
        Route::get('/stok-masuk/edit/{no_trm}', 'edit')->name('stok-masuk.edit');
        Route::post('/stok-masuk/store', 'store')->name('stok-masuk.store');
        Route::post('/stok-masuk/update/{no_trm}', 'update')->name('stok-masuk.update');
    });
});
