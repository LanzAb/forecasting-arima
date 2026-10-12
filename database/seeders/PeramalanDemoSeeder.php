<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\HasilPeramalan;
use App\Models\Peramalan;
use App\Models\Simulasi;
use App\Models\User;
use App\Services\Arima\BoxJenkinsPipeline;
use App\Services\Arima\TimeSeriesBuilder;
use App\Services\Simulasi\SimulationRunner;
use App\Services\Stok\TargetProduksiPlanner;
use Illuminate\Database\Seeder;
use RuntimeException;
use Throwable;

/**
 * Mengisi tabel hasil analisis (Modul B) untuk keperluan demonstrasi.
 *
 * PENTING — angkanya BUKAN karangan.
 *
 * Seeder ini menjalankan pipeline Box-Jenkins yang sebenarnya di atas data
 * penjualan yang sudah ada, lalu meneruskannya ke perencanaan target produksi
 * dan simulasi. Jadi seluruh isi tabel `peramalan`, `uji_stasioneritas`,
 * `korelasi_lag`, `kandidat_model`, `parameter_model`, `hasil_peramalan`,
 * `target_produksi`, `kebutuhan_bahan`, `simulasi`, dan `simulasi_detail`
 * benar-benar konsisten satu sama lain dan dapat ditelusuri ulang.
 *
 * Alasannya: hasil ARIMA yang diketik manual akan runtuh begitu penguji
 * memeriksa apakah AIC, MAPE, atau koefisiennya cocok dengan datanya. Yang
 * "dummy" di sini hanyalah data penjualan sumbernya (dari
 * PenjualanHistorisSeeder) — perhitungannya asli.
 *
 * Jalankan setelah data penjualan tersedia:
 *
 *     php artisan db:seed --class=PeramalanDemoSeeder
 *
 * Aman diulang: hasil lama untuk barang yang sama dihapus lebih dulu.
 */
class PeramalanDemoSeeder extends Seeder
{
    /** Jumlah periode yang diramalkan ke depan. */
    private const HORIZON = 6;

    /**
     * Skenario pembanding pada simulasi. `naif_bulan_lalu` dipilih karena
     * itulah kebiasaan yang paling mendekati praktik perusahaan kecil:
     * memproduksi sebanyak permintaan bulan sebelumnya.
     */
    private const METODE_PEMBANDING = 'naif_bulan_lalu';

    public function run(): void
    {
        $userId = User::where('role', User::ROLE_PIMPINAN)->value('id')
            ?? User::where('role', User::ROLE_ADMIN)->value('id');

        $daftarBarang = Barang::query()
            ->where('is_diramalkan', true)
            ->where('is_aktif', true)
            ->orderBy('kode_barang')
            ->get();

        if ($daftarBarang->isEmpty()) {
            $this->command?->warn('Tidak ada barang bertanda is_diramalkan. Tandai dulu barang jadi di Master Barang.');

            return;
        }

        foreach ($daftarBarang as $barang) {
            $this->command?->info("► {$barang->kode_barang} {$barang->nama_barang}");

            $this->bersihkanHasilLama($barang);
            $this->bangunDeret($barang);

            $peramalan = $this->jalankanForecasting($barang, $userId);

            if ($peramalan === null) {
                continue;
            }

            $this->rencanakanTarget($peramalan, $userId);
            $this->jalankanSimulasi($barang, $userId);
        }
    }

    /**
     * Hasil lama dihapus supaya seeder dapat dijalankan berulang tanpa
     * menumpuk peramalan ganda untuk barang yang sama. Tabel turunannya
     * (uji_stasioneritas, kandidat_model, hasil_peramalan, target_produksi,
     * kebutuhan_bahan, simulasi_detail) ikut terhapus lewat cascade.
     */
    private function bersihkanHasilLama(Barang $barang): void
    {
        Simulasi::where('barang_id', $barang->id)->delete();
        Peramalan::where('barang_id', $barang->id)->delete();
    }

    /**
     * Agregasi penjualan bulanan menjadi deret Zt.
     *
     * Langkah ini terpisah dari forecasting — persis seperti di aplikasi, yang
     * memisahkannya menjadi menu "Data Historis" (tombol Agregasi) dan menu
     * "Proses Forecasting". BoxJenkinsPipeline hanya MEMBACA `data_time_series`,
     * jadi tanpa langkah ini deretnya kosong dan pipeline menolak jalan.
     */
    private function bangunDeret(Barang $barang): void
    {
        $deret = (new TimeSeriesBuilder())->bangun($barang);

        $this->command?->line("   Deret Zt: {$deret->count()} periode");
    }

    private function jalankanForecasting(Barang $barang, ?int $userId): ?Peramalan
    {
        try {
            $peramalan = (new BoxJenkinsPipeline())->jalankan($barang, self::HORIZON, $userId);
        } catch (Throwable $e) {
            $this->command?->error('   Forecasting gagal: '.$e->getMessage());

            return null;
        }

        $ordo = "ARIMA({$peramalan->ordo_p},{$peramalan->ordo_d},{$peramalan->ordo_q})";
        $mape = $peramalan->mape !== null
            ? number_format((float) $peramalan->mape, 2, ',', '.').'%'
            : 'tidak terhitung';

        $this->command?->line("   Model terpilih {$ordo} · MAPE {$mape} · {$peramalan->kategori_akurasi}");

        return $peramalan;
    }

    /**
     * Target produksi dihitung untuk periode forecast PERTAMA, yaitu bulan
     * tepat setelah data historis berakhir — itulah periode yang benar-benar
     * perlu direncanakan sekarang.
     */
    private function rencanakanTarget(Peramalan $peramalan, ?int $userId): void
    {
        $periode = HasilPeramalan::query()
            ->where('peramalan_id', $peramalan->id)
            ->where('tipe', 'forecast')
            ->orderBy('periode')
            ->value('periode');

        if ($periode === null) {
            $this->command?->warn('   Tidak ada periode forecast, target produksi dilewati.');

            return;
        }

        try {
            $target = (new TargetProduksiPlanner())->rencanakan($peramalan, $periode, $userId);
        } catch (RuntimeException $e) {
            $this->command?->error('   Target produksi gagal: '.$e->getMessage());

            return;
        }

        $jumlahBahan = $target->kebutuhanBahan()->count();
        $perluBeli = $target->kebutuhanBahan()->whereIn('status', ['perlu_beli', 'mendesak'])->count();

        $this->command?->line(sprintf(
            '   Target %s: %s unit · SS %s · %d bahan diledakkan, %d perlu dibeli',
            $periode,
            rtrim(rtrim(number_format((float) $target->jumlah_target_produksi, 2, ',', '.'), '0'), ','),
            rtrim(rtrim(number_format((float) $target->safety_stock, 2, ',', '.'), '0'), ','),
            $jumlahBahan,
            $perluBeli,
        ));
    }

    /**
     * Stok awal simulasi diambil dari stok barang jadi saat ini, supaya
     * titik berangkatnya sama dengan keadaan gudang yang sebenarnya.
     *
     * Biaya simpan dan biaya stockout adalah ASUMSI yang wajib dikonfirmasi
     * ke CV. Pande Sejahtera sebelum dipakai di laporan akhir:
     *   - biaya simpan   : 2% harga jual per unit per bulan
     *   - biaya stockout : 15% harga jual per unit (kehilangan margin + nama baik)
     */
    private function jalankanSimulasi(Barang $barang, ?int $userId): void
    {
        $hargaJual = (float) $barang->harga_jual;

        try {
            $simulasi = (new SimulationRunner())->jalankan(
                barang: $barang,
                metodePembanding: self::METODE_PEMBANDING,
                stokAwalSimulasi: (float) $barang->stok_tersedia,
                biayaSimpanPerUnit: round($hargaJual * 0.02),
                biayaStockoutPerUnit: round($hargaJual * 0.15),
                userId: $userId,
            );
        } catch (Throwable $e) {
            $this->command?->error('   Simulasi gagal: '.$e->getMessage());

            return;
        }

        $this->command?->line("   Simulasi {$simulasi->kode_simulasi}: {$simulasi->detail()->count()} baris bulanan");
    }
}
