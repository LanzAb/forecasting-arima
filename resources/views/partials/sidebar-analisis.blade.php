{{--
    Menu Modul B - Analisis, Peramalan & Simulasi
    Pemilik file ini: pengerjaan Modul B. Modul A tidak menambah apa pun di sini.

    Cara mengaktifkan menu: ganti href="#" menjadi route yang sudah dibuat
    di routes/analisis.php, contoh: {{ route('peramalan.forecasting.index') }}
--}}

@php
    $itemClass = 'block px-3 py-2 rounded-md text-gray-300 hover:bg-gray-800 hover:text-white';
    $headClass = 'px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500';
@endphp

<div>
    <p class="{{ $headClass }}">Peramalan (ARIMA)</p>
    <div class="space-y-0.5">
        <a href="#" class="{{ $itemClass }}">Data Historis</a>
        <a href="#" class="{{ $itemClass }}">Proses Forecasting</a>
        <a href="#" class="{{ $itemClass }}">Waktu Tunggu</a>
        <a href="#" class="{{ $itemClass }}">Target Produksi</a>
    </div>
</div>

<div>
    <p class="{{ $headClass }}">Simulasi &amp; Pengujian</p>
    <div class="space-y-0.5">
        <a href="#" class="{{ $itemClass }}">Jalankan Simulasi</a>
        <a href="#" class="{{ $itemClass }}">Perbandingan Skenario</a>
        <a href="#" class="{{ $itemClass }}">Rincian Bulanan</a>
        <a href="#" class="{{ $itemClass }}">Laporan Hasil Simulasi</a>
    </div>
</div>
