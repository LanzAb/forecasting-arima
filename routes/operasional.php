<?php

/*
|--------------------------------------------------------------------------
| Route Modul A - Operasional Pabrik
|--------------------------------------------------------------------------
|
| Pemilik file ini: pengerjaan Modul A. Modul B tidak menambah apa pun di sini.
|
| Cakupan : master data, pembelian, produksi, persediaan, penjualan,
|           laporan operasional
| Folder  : app/Http/Controllers/{Master,Pembelian,Produksi,Persediaan,
|           Penjualan,Laporan}
|
*/

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {

    // -----------------------------------------------------------------
    // MASTER DATA
    // -----------------------------------------------------------------
    Route::prefix('master')->name('master.')->group(function () {
        // Route::resource('kategori', KategoriController::class);
        // Route::resource('barang', BarangController::class);
        // Route::resource('supplier', SupplierController::class);
        // Route::resource('pelanggan', PelangganController::class);
        // Route::resource('tahapan-produksi', TahapanProduksiController::class);
        // Route::resource('pengguna', PenggunaController::class)->middleware('role:admin');
    });

    // -----------------------------------------------------------------
    // PEMBELIAN
    // -----------------------------------------------------------------
    Route::prefix('pembelian')->name('pembelian.')->group(function () {
        // Route::resource('order', PembelianController::class);
        // Route::get('riwayat', [PembelianController::class, 'riwayat'])->name('riwayat');
        // Route::post('import', [ImportPembelianController::class, 'store'])->name('import');
    });

    // -----------------------------------------------------------------
    // PRODUKSI
    // -----------------------------------------------------------------
    Route::prefix('produksi')->name('produksi.')->group(function () {
        // Route::resource('bom', BomController::class);
        //
        // Lima menu tahapan memakai SATU controller yang sama,
        // dibedakan oleh parameter {tahapan}:
        // Route::resource('perintah/{tahapan}', ProduksiController::class);
    });

    // -----------------------------------------------------------------
    // PERSEDIAAN
    // -----------------------------------------------------------------
    Route::prefix('persediaan')->name('persediaan.')->group(function () {
        // Route::get('stok', [StokController::class, 'index'])->name('stok');
        // Route::get('mutasi', [MutasiStokController::class, 'index'])->name('mutasi');
        // Route::resource('opname', OpnameController::class);
    });

    // -----------------------------------------------------------------
    // PENJUALAN
    // -----------------------------------------------------------------
    Route::prefix('penjualan')->name('penjualan.')->group(function () {
        // Route::resource('faktur', PenjualanController::class);
        // Route::post('import', [ImportPenjualanController::class, 'store'])->name('import');
    });

    // -----------------------------------------------------------------
    // LAPORAN OPERASIONAL
    // -----------------------------------------------------------------
    Route::prefix('laporan')->name('laporan.')->group(function () {
        // Route::get('pembelian', [LaporanPembelianController::class, 'index'])->name('pembelian');
        // Route::get('produksi', [LaporanProduksiController::class, 'index'])->name('produksi');
        // Route::get('persediaan', [LaporanPersediaanController::class, 'index'])->name('persediaan');
        // Route::get('penjualan', [LaporanPenjualanController::class, 'index'])->name('penjualan');
    });

});
