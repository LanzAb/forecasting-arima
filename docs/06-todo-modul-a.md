# To-Do Modul A — Operasional Pabrik

**Sistem Peramalan Penjualan Barang (Box-Jenkins / ARIMA) — CV. Pande Sejahtera**
Per 20 September 2026 · branch `modul-a-operasional`

Daftar ini melengkapi [05-roadmap-modul-a.md](05-roadmap-modul-a.md). Roadmap
menjawab "apa yang dibangun dan dengan urutan apa"; berkas ini menjawab
"apa yang masih harus dikerjakan sekarang".

---

## Keadaan Sekarang

| Tahap | Isi | Status |
|---|---|---|
| 1 | Master data mandiri (supplier, pelanggan, tahapan produksi) | Selesai |
| 2 | Barang — simpul pusat | Selesai |
| 3 | Fondasi stok (`StockMutator`, observer, 3 halaman persediaan) | Selesai |
| 4 | Pembelian, penerimaan, penjualan, import Excel penjualan | Selesai |
| 5 | BOM + perintah produksi 5 tahapan | Selesai |
| 6 | Laporan, dashboard, pengguna, tombol rekomendasi | Selesai |

Angka: 24 controller, 196 test lolos, seluruh menu Modul A di sidebar aktif.

**Modul B belum dikerjakan sama sekali** — `app/Services/Arima/`,
`app/Services/Simulasi/`, dan `app/Support/Math/` masih kosong. Itu bagian rekan
satu tim dan tidak masuk daftar ini.

---

## A. Mendesak

- [ ] **Commit seluruh pekerjaan.**
      Sejak commit fondasi (`85d2f89`) belum ada satu commit pun, padahal enam
      tahap sudah selesai. Dua kali dalam pengerjaan ada data terhapus tak
      sengaja dan tidak ada titik aman untuk kembali. Disarankan dipecah per
      tahap agar riwayatnya terbaca saat menulis bab implementasi.

- [ ] **Perbaiki MariaDB yang crash.**
      MySQL (XAMPP, MariaDB 10.4.32) beberapa kali mati sendiri. Log
      `C:\xampp\mysql\data\mysql_error.log` mencatat:

      ```
      mysqld got exception 0x80000003
      Query: select * from `sessions` where `id` = ? limit 1
      ```

      Dua jalan keluar:
      1. Ganti `SESSION_DRIVER=database` menjadi `file` di `.env` — sesi tidak
         lagi menyentuh MySQL. Paling cepat.
      2. `REPAIR TABLE sessions;` atau `TRUNCATE TABLE sessions;` lewat
         phpMyAdmin. Semua orang perlu login ulang.

---

## B. Sebelum Data Asli Dipakai di Laporan

- [ ] **Import data penjualan asli 36 bulan.**
      Data penjualan yang ada sekarang adalah bangkitan seeder. Selama masih
      dummy, hasil simulasi Modul B **belum layak masuk laporan skripsi**.
      Jalan masuknya sudah siap di menu *Import Penjualan*; berkas contohnya
      dapat diunduh dari halaman yang sama.

- [ ] **Konfirmasi angka asumsi ke CV. Pande Sejahtera.**
      Angka berikut masih asumsi dan ikut menentukan hasil perhitungan Modul B:

      | Data | Letak | Dipakai untuk |
      |---|---|---|
      | `lead_time_default` supplier | Master Supplier | Waktu tunggu pembelian |
      | `lead_time_hari` bahan baku | Master Barang | L<sub>beli</sub> |
      | `waktu_proses_hari` & `kapasitas_per_hari` | Tahapan Produksi | L<sub>produksi</sub> |
      | `jumlah_kebutuhan` & `persen_susut` | BOM / Komposisi | Ledak BOM |
      | Harga beli & harga jual | Master Barang | Nilai persediaan & laporan |

- [ ] **Masukkan saldo awal stok lewat Stok Opname.**
      Stok barang saat ini berasal dari seeder. Barang baru selalu mulai dari
      nol dan hanya dapat diisi lewat mutasi stok, jadi saldo awal yang
      sebenarnya perlu dimasukkan lewat menu *Stok Opname*.

---

## C. Pembuktian yang Belum Lengkap

- [ ] **Telusuri Tahap 6 lewat browser.**
      Dashboard, Pengguna, keempat laporan, dan Rekomendasi sudah terbukti lewat
      196 test, tetapi belum pernah ditelusuri lewat HTTP sungguhan seperti
      tahap 1–5, karena MariaDB crash saat pengujian. Yang perlu dicek langsung:
      unduhan PDF & Excel, tampilan grafik dashboard, dan penjagaan role admin.

- [ ] **Uji tombol Rekomendasi dengan data Modul B yang sebenarnya.**
      Tombol "Buat Order dari Rekomendasi" sudah jadi dan teruji, tetapi
      memakai baris `kebutuhan_bahan` buatan sendiri yang menirukan keluaran
      Modul B. Perlu dicek ulang begitu rekan Anda benar-benar mengisi tabel itu
      lewat perhitungan target produksi.

---

## D. Keputusan Teknis yang Menunggu Jawaban

Empat hal berikut sudah berjalan, tetapi mengandung pilihan yang sebaiknya
Anda sadari dan setujui — atau ubah.

- [ ] **`barang.stok_tersedia` bertipe integer, `mutasi_stok` decimal(15,4).**
      Akibatnya nyata: perintah produksi 10 unit Kepala Sekop memakai 0,875
      lembar Plat Besi, tetapi stok tercatat turun 1 lembar penuh
      (120 → 119,125 → dibulatkan 119). Rantai mutasi tetap menyimpan angka
      tepat. Untuk produksi harian berjumlah besar tidak berarti; bila
      perusahaan sering menjalankan perintah kecil, perlu migration baru yang
      mengubah kolom itu menjadi decimal.

- [ ] **`urutan` tahapan produksi unik hanya lewat validasi.**
      Migration tidak memberi constraint `unique`, jadi penulisan langsung ke
      database (seeder, import) masih bisa menembusnya. Perlu migration baru
      bila ingin penjagaan sungguhan.

- [ ] **Supplier wajib diisi untuk bahan baku.**
      Aturan ini saya simpulkan dari roadmap ("supplier boleh kosong untuk
      setengah jadi & barang jadi"). Bila kenyataannya ada bahan baku yang
      belum ditentukan pemasoknya, aturan di `BarangRequest` perlu dilonggarkan.

- [ ] **Import Excel penjualan sengaja tidak mengubah stok.**
      Alasannya: import dipakai memasukkan penjualan masa lalu sebagai bahan
      ARIMA, dan barangnya sudah keluar gudang bertahun lalu. Bila ternyata
      import juga akan dipakai mencatat penjualan berjalan, perilakunya harus
      diubah.

### Catatan jebakan pada model beku

`Produksi::tahapan()` **tidak dapat dipanggil statis** — model punya relasi
`tahapan()` dan scope `scopeTahapan()` dengan nama yang bentrok, sehingga PHP
mengarahkan panggilan statis ke relasinya. Pakai `Produksi::query()->tahapan(...)`.
Model tidak diubah karena sudah dibekukan sejak fase 0.

---

## E. Opsional / Ditunda

- [ ] **Import Excel Pembelian** — roadmap menandainya "(opsional, bisa
      belakangan)". Polanya tinggal menyalin `PenjualanImport`.

---

## F. Penutup Proyek

- [ ] **Deploy ke hosting.** Revisi sidang mewajibkan web sudah dihosting.
      Langkahnya sudah tertulis di
      [01-alur-kerja-sistem.md bagian 12](01-alur-kerja-sistem.md).
      Yang perlu diingat: `APP_ENV=production`, `APP_DEBUG=false`, document root
      diarahkan ke `public/`, dan jalankan `config:cache` + `route:cache` +
      `view:cache`.

- [ ] **Tulis bab implementasi Modul A.** Bahan yang sudah tersedia:
      struktur folder per menu, tabel keputusan rancangan pada roadmap tiap
      tahap, dan 196 test sebagai bukti pengujian.

---

## Perubahan Terakhir

**20 September 2026 — tampilan dibuat memenuhi layar.** Seluruh pembungkus
halaman (`max-w-3xl` sampai `max-w-7xl`) diganti `w-full` di 44 berkas view,
sehingga tabel dan laporan memakai seluruh lebar layar. Halaman profil bawaan
Breeze sengaja dibiarkan memakai `max-w-xl`.

Bila form tambah/ubah terasa terlalu melebar pada layar besar, batas lebarnya
dapat dikembalikan khusus untuk halaman form tanpa mengubah halaman tabel.
