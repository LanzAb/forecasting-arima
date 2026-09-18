{{--
    Menu Modul A - Operasional Pabrik
    Pemilik file ini: pengerjaan Modul A. Modul B tidak menambah apa pun di sini.

    Cara mengaktifkan menu: ganti href="#" menjadi route yang sudah dibuat
    di routes/operasional.php, contoh: {{ route('master.barang.index') }}
--}}

@php
    // Sementara semua href diisi '#'. Ganti satu per satu saat halamannya jadi.
    $itemClass = 'block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100';
    $headClass = 'px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400';
@endphp

<div>
    <p class="{{ $headClass }}">Master Data</p>
    <div class="space-y-0.5">
        <a href="#" class="{{ $itemClass }}">Data Kategori</a>
        <a href="#" class="{{ $itemClass }}">Data Barang</a>
        <a href="#" class="{{ $itemClass }}">Data Supplier</a>
        <a href="#" class="{{ $itemClass }}">Data Pelanggan</a>
        <a href="#" class="{{ $itemClass }}">Tahapan Produksi</a>
        @if (auth()->user()?->role === 'admin')
            <a href="#" class="{{ $itemClass }}">Pengguna</a>
        @endif
    </div>
</div>

<div>
    <p class="{{ $headClass }}">Transaksi &amp; Operasional</p>
    <div class="space-y-0.5">
        <a href="#" class="{{ $itemClass }}">Pembelian</a>
        <a href="#" class="{{ $itemClass }}">Riwayat Pembelian Bahan</a>

        <p class="px-3 pt-3 pb-1 text-xs font-medium text-gray-500">Produksi</p>
        <a href="#" class="{{ $itemClass }}">BOM / Komposisi</a>
        <a href="#" class="{{ $itemClass }}">Produksi Kepala</a>
        <a href="#" class="{{ $itemClass }}">Produksi Handle</a>
        <a href="#" class="{{ $itemClass }}">Proses Coating</a>
        <a href="#" class="{{ $itemClass }}">Perakitan Sekop</a>
        <a href="#" class="{{ $itemClass }}">Proses Pengemasan</a>

        <p class="px-3 pt-3 pb-1 text-xs font-medium text-gray-500">Persediaan</p>
        <a href="#" class="{{ $itemClass }}">Stok Saat Ini</a>
        <a href="#" class="{{ $itemClass }}">Mutasi Stok</a>

        <p class="px-3 pt-3 pb-1 text-xs font-medium text-gray-500">Penjualan</p>
        <a href="#" class="{{ $itemClass }}">Transaksi Penjualan</a>
    </div>
</div>

<div>
    <p class="{{ $headClass }}">Laporan</p>
    <div class="space-y-0.5">
        <a href="#" class="{{ $itemClass }}">Laporan Pembelian</a>
        <a href="#" class="{{ $itemClass }}">Laporan Produksi</a>
        <a href="#" class="{{ $itemClass }}">Laporan Persediaan</a>
        <a href="#" class="{{ $itemClass }}">Laporan Penjualan</a>
    </div>
</div>
