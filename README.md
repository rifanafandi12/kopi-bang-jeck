# ☕ Kopi Bang Jeck — Aplikasi Taking Order Berbasis QR Code

Aplikasi pemesanan mandiri (*self-service taking order*) untuk kedai kopi, dibangun dengan **Laravel 13** dan **Livewire 4**. Pelanggan cukup memindai QR Code di meja, memesan menu secara langsung, dan memantau status pesanan secara *real-time*.

Selain sebagai aplikasi fungsional, proyek ini merupakan **artefak penelitian akademik** yang dirancang untuk mengukur efisiensi waktu pencatatan pesanan (dari pelanggan memindai QR hingga pesanan diterima kasir) dibandingkan dengan metode manual.

---

## Daftar Isi

1. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
2. [Prasyarat Sistem](#prasyarat-sistem)
3. [Panduan Instalasi (Local Development)](#panduan-instalasi-local-development)
4. [Cara Menjalankan Aplikasi](#cara-menjalankan-aplikasi)
5. [Fitur Lengkap Aplikasi](#fitur-lengkap-aplikasi)
6. [Struktur Database & Instrumentasi Penelitian](#struktur-database--instrumentasi-penelitian)
7. [Pengujian Otomatis (Testing)](#pengujian-otomatis-testing)
8. [Troubleshooting Umum](#troubleshooting-umum)

---

## Teknologi yang Digunakan

| Kebutuhan | Teknologi |
|---|---|
| Framework utama | Laravel 13 |
| Frontend interaktif | Livewire 4 & Alpine.js |
| Styling | Tailwind CSS 4 (kustomisasi tema warna kedai kopi) |
| Panel admin | Filament 5 |
| Real-time WebSocket | Laravel Reverb |
| Database | MySQL 8.0+ |
| Pengujian | Pest PHP |
| Ekspor data | Maatwebsite Excel |
| QR Code | Endroid QR Code |

---

## Prasyarat Sistem

Pastikan sistem Anda memenuhi persyaratan berikut sebelum menjalankan aplikasi:

- PHP 8.3 atau lebih baru (disarankan PHP 8.5)
- Composer 2.x
- Node.js 20.x atau lebih baru
- MySQL / MariaDB
- Ekstensi PHP aktif: `gd`, `pdo_mysql`, `fileinfo`, dan `sqlite3` (untuk testing)

---

## Panduan Instalasi (Local Development)

Ikuti langkah berikut untuk menjalankan aplikasi di lingkungan lokal (Laragon, XAMPP, atau Valet).

### 1. Kloning Repositori dan Instal Dependensi

```bash
git clone <url-repositori-anda> kopi-bang-jeck
cd kopi-bang-jeck
composer install
npm install
```

### 2. Konfigurasi Environment

Salin file konfigurasi dan buat application key:

```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env`, lalu sesuaikan bagian database dan WebSocket (Reverb):

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kopi_bang_jeck
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=reverb
```

> Pastikan variabel `REVERB_*` juga terisi (lihat `.env.example`).

### 3. Migrasi Database dan Seeding

Buat database baru bernama `kopi_bang_jeck` di MySQL, lalu jalankan perintah berikut untuk membuat tabel dan mengisi data awal (menu contoh, meja, serta akun admin dan kasir):

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 4. Build Aset Frontend

Kompilasi CSS dan JavaScript menggunakan Vite:

```bash
npm run build
```

---

## Cara Menjalankan Aplikasi

Aplikasi memakai WebSocket untuk fitur *real-time*, sehingga **tiga proses** perlu berjalan bersamaan. Buka tiga tab terminal di folder proyek:

| Terminal | Fungsi | Perintah |
|---|---|---|
| 1 | Server web utama | `php artisan serve` |
| 2 | Server WebSocket (Reverb) | `php artisan reverb:start` |
| 3 | Vite dev server *(hanya saat mengembangkan UI)* | `npm run dev` |

Akses aplikasi melalui browser di **http://localhost:8000**.

### Akun Demo

Password semua akun demo: `password`

| Peran | Email | Halaman Login |
|---|---|---|
| Admin / Pemilik | `admin@kopibangjeck.test` | http://localhost:8000/admin |
| Kasir | `kasir@kopibangjeck.test` | http://localhost:8000/login |

> ⚠️ Akun demo hanya untuk pengembangan. Ganti password sebelum aplikasi dipakai di server produksi.

---

## Fitur Lengkap Aplikasi

Aplikasi terbagi menjadi tiga antarmuka utama: **Pelanggan**, **Kasir**, dan **Admin**.

### 1. Antarmuka Pelanggan (Mobile-First)

Diakses melalui URL `/m/{token}` (didapat dari QR Code meja).

- **Input data diri** — Nama wajib diisi; nomor HP dan email bersifat opsional.
- **Katalog menu** — Menu tampil per kategori dengan desain menyerupai menu cetak (garis titik-titik), dilengkapi pencarian dan label "Terlaris".
- **Keranjang belanja** — *Sticky bar* di bagian bawah menampilkan total pesanan secara langsung. Pelanggan dapat menambah catatan khusus per item.
- **Halaman konfirmasi** — Pilihan metode pembayaran (Bayar di Kasir atau Online). Total harga dihitung ulang di sisi server demi keamanan.
- **Halaman status (real-time)**
  - Memantau status pesanan: Menunggu, Diproses, Siap, Selesai.
  - Diperbarui otomatis tanpa perlu *refresh*.
  - Pop-up notifikasi muncul otomatis saat pesanan siap.
- **Pesan lagi** — Membuat pesanan baru tanpa kehilangan status pesanan sebelumnya (pesanan lama tetap berjalan).
- **Desain** — Palet warna kustom (Krim, Espresso, Karamel, Mentega) dengan font Fraunces dan Instrument Sans.

### 2. Antarmuka Kasir (`/kasir`)

- **Dasbor antrean real-time** — Pesanan baru muncul otomatis di layar kasir (didukung Laravel Reverb) disertai bunyi notifikasi.
- **Manajemen status** — Status diubah dengan urutan *Diterima → Diproses → Siap → Selesai* dan validasi transisi yang ketat.
- **Pembayaran tunai** — Kasir memasukkan nominal uang yang diterima; input otomatis diformat dengan titik (contoh: `50.000`) dan kembalian dihitung otomatis.
- **Cetak struk** — Struk berukuran 80 mm menggunakan CSS Print (hanya struk yang tercetak, tanpa sidebar atau tombol).
- **Pembatalan pesanan** — Kasir dapat membatalkan pesanan bila diperlukan.

### 3. Antarmuka Admin (Filament 5, `/admin`)

- **Manajemen kategori** — Tambah, ubah, dan atur urutan kategori.
- **Manajemen menu**
  - Kelola nama, harga, deskripsi, dan ketersediaan menu.
  - Unggah gambar dengan *drag & drop*; otomatis dikonversi ke format WebP agar ringan.
  - Ambil gambar langsung dari URL (misalnya Google Images).
- **Manajemen meja**
  - Tambah dan ubah nomor meja.
  - Token QR dibuat acak (ULID) sehingga tidak bisa ditebak.
  - Cetak QR Code untuk ditempel di meja, dengan tampilan print yang bersih.
- **Data pengukuran (instrumentasi penelitian)**
  - Menampilkan durasi (dalam detik) antar tahapan pesanan: Waktu Input, Waktu Kasir, dan Waktu Proses.
  - Filter berdasarkan rentang tanggal.
  - Ekspor ke Excel (`.xlsx`) dengan header berwarna untuk dianalisis di SPSS/Excel.

---

## Struktur Database & Instrumentasi Penelitian

Sesuai kebutuhan metodologi penelitian, aplikasi merekam setiap pergerakan pesanan dengan presisi milidetik (tipe data `datetime(3)`).

### Tabel Utama

| Tabel | Keterangan |
|---|---|
| `users` | Data admin dan kasir (role menggunakan Spatie) |
| `cafe_tables` | Nomor meja dan token ULID unik |
| `categories`, `menu_items` | Data menu dan kategorinya |
| `orders`, `order_items` | Data transaksi. Harga dan nama menu disimpan sebagai *snapshot* agar tidak berubah bila admin mengedit menu di kemudian hari |
| `order_events` | **Tabel krusial penelitian** — log aktivitas pesanan (lihat di bawah) |

### Event pada `order_events`

| Event | Dicatat saat |
|---|---|
| `qr_scanned` | Pelanggan mulai mengisi data diri |
| `first_item_added` | Pelanggan pertama kali memasukkan menu ke keranjang |
| `order_confirmed` | Pelanggan menekan tombol konfirmasi pesanan |
| `accepted_by_cashier` | Kasir menerima pesanan |
| `preparing`, `ready`, `completed`, `cancelled` | Perubahan status pesanan |
| `payment_paid`, `payment_failed` | Hasil pembayaran |

### Rumus Durasi (dapat disesuaikan)

| Metrik | Rumus |
|---|---|
| Waktu Input | `order_confirmed` − `qr_scanned` |
| Waktu Kasir | `accepted_by_cashier` − `order_confirmed` |
| Waktu Proses | `ready` − `accepted_by_cashier` |

Semua perhitungan memakai waktu server (UTC) dan ditampilkan dalam zona waktu **Asia/Jakarta**.

---

## Pengujian Otomatis (Testing)

Aplikasi dilengkapi pengujian dasar dengan Pest untuk memastikan alur pesanan dan pencatatan waktu berjalan benar.

```bash
php artisan test
```

> File tes: `tests/Feature/OrderFlowTest.php`

---

## Troubleshooting Umum

### 1. Notifikasi WebSocket (real-time) tidak muncul

- Pastikan Terminal 2 (`php artisan reverb:start`) berjalan tanpa error.
- Pastikan `npm run dev` atau `npm run build` sudah dijalankan.
- Cek `.env`: `BROADCAST_CONNECTION=reverb` dan semua `REVERB_*` terisi.

### 2. Gambar menu error 403 (Forbidden)

- Jalankan `php artisan storage:link`.
- Pastikan ekstensi `gd` aktif di `php.ini` (hapus tanda titik koma pada `;extension=gd`).
- Gambar otomatis menjadi `.webp` setelah diunggah.

### 3. Font Fraunces / Instrument Sans tidak muncul

- Pastikan file `.ttf` ada di folder `resources/fonts/`.
- Jalankan `npm run build`, lalu `php artisan optimize:clear`.

### 4. Cetak struk / QR Code terpotong

- Pada dialog print browser (`Ctrl+P`), pilih ukuran kertas **80 mm** untuk struk atau **A4** untuk QR.
- Centang opsi **Background Graphics**.
