{{--
    Kerangka sidebar.

    JANGAN menambah menu di file ini.

    Proyek dikerjakan dua orang, jadi menu dipecah menjadi dua partial
    terpisah supaya tidak pernah ada konflik saat menggabungkan pekerjaan:

        sidebar-operasional.blade.php  -> Modul A
        sidebar-analisis.blade.php     -> Modul B

    Tambahkan menu baru ke partial modul masing-masing.
--}}

<aside class="w-64 shrink-0 bg-white border-r border-gray-200 min-h-screen">
    <div class="px-4 py-5 border-b border-gray-200">
        <p class="text-sm font-semibold text-gray-900 leading-tight">CV. Pande Sejahtera</p>
        <p class="text-xs text-gray-500 mt-0.5">Peramalan &amp; Perencanaan Stok</p>
    </div>

    <nav class="px-3 py-4 space-y-6 text-sm">

        <a href="{{ route('dashboard') }}"
           @class([
               'flex items-center gap-2 px-3 py-2 rounded-md font-medium',
               'bg-gray-900 text-white' => request()->routeIs('dashboard'),
               'text-gray-700 hover:bg-gray-100' => ! request()->routeIs('dashboard'),
           ])>
            Dashboard
        </a>

        @include('partials.sidebar-operasional')

        @include('partials.sidebar-analisis')

    </nav>
</aside>
