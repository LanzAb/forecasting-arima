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
        <a href="{{ route('master.kategori.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('master.kategori.*')])>Data Kategori</a>
        <a href="{{ route('master.barang.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('master.barang.*')])>Data Barang</a>
        <a href="{{ route('master.supplier.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('master.supplier.*')])>Data Supplier</a>
        <a href="{{ route('master.pelanggan.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('master.pelanggan.*')])>Data Pelanggan</a>
        <a href="{{ route('master.tahapan-produksi.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('master.tahapan-produksi.*')])>Tahapan Produksi</a>
        @if (auth()->user()?->role === 'admin')
            <a href="#" class="{{ $itemClass }}">Pengguna</a>
        @endif
    </div>
</div>

<div>
    <p class="{{ $headClass }}">Transaksi &amp; Operasional</p>
    <div class="space-y-0.5">
        <a href="{{ route('pembelian.order.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('pembelian.order.*')])>Pembelian</a>
        <a href="{{ route('pembelian.riwayat') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('pembelian.riwayat')])>Riwayat Pembelian Bahan</a>

        <p class="px-3 pt-3 pb-1 text-xs font-medium text-gray-500">Produksi</p>
        <a href="{{ route('produksi.bom.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('produksi.bom.*')])>BOM / Komposisi</a>

        {{--
            Menu tahapan dibangkitkan dari master Tahapan Produksi, bukan ditulis
            satu per satu. Dengan begitu menambah atau mengganti nama tahapan
            cukup dilakukan lewat halaman master, tanpa menyentuh berkas ini.
        --}}
        @foreach (\App\Models\TahapanProduksi::where('is_aktif', true)->orderBy('urutan')->get() as $tahapanMenu)
            <a href="{{ route('produksi.perintah.index', $tahapanMenu->kode_tahapan) }}"
               @class([
                   $itemClass,
                   'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('produksi.perintah.*')
                       && request()->route('tahapan')?->id === $tahapanMenu->id,
               ])>{{ $tahapanMenu->nama_tahapan }}</a>
        @endforeach

        <p class="px-3 pt-3 pb-1 text-xs font-medium text-gray-500">Persediaan</p>
        <a href="{{ route('persediaan.stok') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('persediaan.stok')])>Stok Saat Ini</a>
        <a href="{{ route('persediaan.mutasi') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('persediaan.mutasi')])>Mutasi Stok</a>
        <a href="{{ route('persediaan.opname.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('persediaan.opname.*')])>Stok Opname</a>

        <p class="px-3 pt-3 pb-1 text-xs font-medium text-gray-500">Penjualan</p>
        <a href="{{ route('penjualan.faktur.index') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('penjualan.faktur.*')])>Transaksi Penjualan</a>
        <a href="{{ route('penjualan.import.form') }}"
           @class([$itemClass, 'bg-gray-900 text-white hover:bg-gray-900' => request()->routeIs('penjualan.import.*')])>Import Penjualan</a>
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
